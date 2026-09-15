# ERPNext review: what PHP Ledger should adopt, adapt and avoid

Commissioned 15 September 2026. Four independent deep-dives into ERPNext's source (`frappe/erpnext` `develop`, commit `9be19e6`, cross-checked against `version-15`), its documentation, and years of forum/GitHub history — covering the accounting core and settings, AR/AP and bank reconciliation, inventory/stock, and the interface/onboarding/reporting/permissions/platform layer. This document is the synthesis: what the pattern is, why ERPNext built it that way, where it bit them, and what that means for PHP Ledger specifically — a PHP/MySQL, single-VPS, AGPL-core product aimed at Pakistani SMEs, sole traders and AOPs, with AR then AP then the FBR digital-invoicing client next on the roadmap.

The full per-area reports (accounting core & settings; AR/AP & bank reconciliation; inventory; interface/platform) run to roughly 250 "adopt/adapt/avoid" findings between them, each tied to a file path, doc URL or forum/issue link. This document does not reproduce all of them — it pulls out the load-bearing ones, organised around the decisions PHP Ledger actually has in front of it, with a full-detail appendix at the end for anything you want to trace back to source.

**How to read this**: ADOPT = copy the idea, it is right. ADAPT = the idea is right but ERPNext's specific implementation caused itself pain; do it differently. AVOID = ERPNext itself would build this differently today, or already regrets it; don't copy the mechanism even where you copy the goal.

---

## 0. The one idea worth taking whole

Everything else in ERPNext's accounting core hangs off one decision: **every posted document reaches the general ledger through exactly one funnel** (`make_gl_entries()`), and nothing else is allowed to write a ledger row. `GL Entry` has `in_create: 1` and no create/write/delete permission for any role — only that function inserts rows. That single rule is what lets ERPNext bolt budgets, period locks, accounting dimensions, and a nightly "ledger health" sanity check onto ~30 different document types without touching any of them individually, because every one of those document types already goes through the same door.

This is the single highest-leverage architectural decision available to PHP Ledger right now, before AR and AP are built. If PHP Ledger's posting service already works this way (per the earlier architecture notes: "posting, period control and reversal always execute on the server" was already decided for the offline/queued-entry model), this review confirms that instinct was right and gives four concrete engineering practices that make the pattern actually hold under real use rather than degrading into ERPNext's current state, where a "Repost Accounting Ledger" back door lets people edit accounts on submitted documents and a scheduled job exists specifically to catch when the ledger and its own derived copies disagree.

The four practices, all copyable cheaply:
1. **Debit/credit as separate, unsigned, pre-normalised columns.** Sign bugs get caught once, at the boundary, not scattered across every report.
2. **A rate/currency snapshot on every row**, not just a reference to "today's rate" — revaluation and audits need the rate *used*.
3. **A round-off row with a tiny, visible tolerance and a dedicated account**, rather than silent adjustment. Multi-line, tax-inclusive, multi-currency documents never sum exactly; make the residual a real, auditable line.
4. **A nightly assertion job**: every voucher balances, every sub-ledger total equals its control account. Cheap to write, and it is the difference between finding drift in a support ticket six months later versus a log entry the next morning.

---

## 1. Accounting core: the ledger, chart of accounts, periods

### What to adopt outright

- **Typed accounts that drive behaviour, not just labels.** Receivable/Payable types force a party on every row and feed the sub-ledger; Bank/Cash types drive payment-method pickers; a Temporary type is the only thing openings are allowed to hit; Round-Off accounts absorb residuals. This is what lets the UI filter correctly and the ledger validate without a second configuration layer. Keep the list short — ERPNext has 31 account types; ~10 is enough for an SME product (Receivable, Payable, Bank, Cash, Tax, Income, Expense/COGS, Fixed Asset, Equity, Temporary).
- **Root type → report type derivation, and "P&L rows need a cost centre."** This is what guarantees a Balance Sheet and P&L can always be produced from the ledger alone with no separate "what goes where" mapping table to maintain.
- **Layered period locks, not one blunt "closed" flag.** ERPNext runs three independent gates: a per-document-type "Accounting Period" lock with an exempt role, a company-wide frozen-till date with an exempt role (Administrator explicitly *not* exempt — a deliberate choice worth copying), and a period-closing voucher that blocks posting on/before its own end date. A Pakistani sole trader's accountant needs to lock last month cheaply and reopen it just as cheaply for one correction; a single flag is too blunt for that, three independent gates is enough without being ERPNext's full machinery.
- **A proper reversal document, distinct from cancellation** (`reversal_of`). Accountants want a dated reversing entry as a normal operation; cancellation should be the exception, not the everyday correction mechanism.
- **Closing-balance snapshots.** ERPNext's Period Closing Voucher writes an `Account Closing Balance` table that Trial Balance and Balance Sheet read as their opening balance, instead of summing the entire ledger from day one on every report. On a single VPS with MySQL, this is not optional — it's the difference between a report that runs in milliseconds and one that scans years of history every time someone opens the Balance Sheet.

### What to adapt

- **Immutability, designed in from day one — not as a settings toggle.** This is the single most consequential recommendation in the whole review, and it interacts directly with a decision PHP Ledger has already made. ERPNext's default cancellation model flags original rows `is_cancelled=1` (they stay in the table, just hidden from every report) and inserts a mirror-image reversal row — so every cancellation doubles the table. There is a separate "Immutable Ledger" switch that changes the *semantics* of what a reversal means (dated today vs. dated on the original transaction), and ERPNext shipped it as an opt-in after a multi-year forum fight, meaning the product has two different cancellation behaviours depending on a setting nobody explains well. **PHP Ledger should skip the fight**: store every row forever, cancellation always inserts a reversing entry dated on the cancellation date by default (with a permissioned exception to reverse on the original date while the period is still open), and there should be exactly one `reversal_of_entry_id` column, never an `is_cancelled` flag with duplicate rows underneath it.
- **Amendment that preserves the document number.** ERPNext's "amend" creates a new document named `<original>-1` and leaves the original cancelled. This is the single most repeated complaint in ERPNext's own forum history (a thread open since 2017 asking for "amend without cancelling"), and it is a genuine legal problem in jurisdictions with gapless invoice numbering — which **is exactly the FBR digital-invoicing situation PHP Ledger is building toward next**. A `-1` renumbering scheme is incompatible with FBR's sequential invoice numbering and 72-hour edit window. Model corrections as a new posting that supersedes the prior one by reference (`supersedes_invoice_id`), or as a credit note + fresh invoice — never as a renamed copy of the same document.
- **Settings: far fewer, and never "legacy."** ERPNext's Accounts Settings alone carries 66 fields across 8 tabs, plus 38 default-account links on the Company record, plus more scattered across Selling/Buying/Stock Settings — and several exist only because a past behaviour change needed a compatibility switch (`use_legacy_controller_for_pcv`, `use_legacy_budget_controller`, `ignore_is_opening_check_for_reporting`). Cap PHP Ledger's accounting settings at roughly 15, and default accounts per company at roughly 10. When behaviour changes, migrate data — don't ship a permanent toggle.
- **Chart-of-accounts import that survives go-live.** ERPNext refuses to import a chart once *any* transaction exists against the company, which forces manual tree surgery for any business that restructures (and SMEs restructure their chart constantly as they grow). Support add/rename/merge of chart nodes after go-live, and block only structural changes on accounts that already have postings.
- **Fill a real gap**: ERPNext ships 76 verified country charts of accounts and has none for Pakistan. A Pakistani company installing ERPNext today gets the generic "Standard" chart. Shipping a proper Pakistan chart (sales-tax input/output, WHT payable/receivable per section, FED, provincial sales-tax heads by province) is both a straightforward local win and a real product differentiator against the incumbent everyone will compare PHP Ledger to.

### What to avoid

- **A "repost accounting ledger" back door.** ERPNext lets certain fields (account, cost centre, project) be edited on a submitted, posted document, then silently regenerates the ledger rows behind it. It exists because users mis-pick an account and cancel/amend is painful — but it directly undermines the immutability story, needs its own permission gates and period-lock interactions, and is exactly the kind of thing an auditor (or FBR) would flag. If amendment properly preserves the document number (above), this back door becomes unnecessary.
- **Free-text counter-account fields on ledger rows.** ERPNext's `against` field is denormalised prose (a customer name, or a comma-joined list of income accounts) — useful for a report column, useless for a join, and it bloats the single largest table in the system. Store the id, render the prose in the report layer.
- **Company abbreviation baked into the account's primary key** (`"Cash - ABC"`), with rename operations that cascade across every historical row. Use a surrogate id and a short `code` unique per company instead.
- **Tolerating 0.5-currency-unit rounding differences on ordinary invoices.** ERPNext needs this tolerance because of float accumulation in its own rounding; with `DECIMAL` arithmetic and rounding only at document totals (never mid-calculation), PHP Ledger shouldn't need more than one minor unit of slack, and a real mismatch should fail loudly rather than get silently swept into a round-off account.

---

## 2. AR/AP: the part of the roadmap this review is most directly useful for

This is the most actionable section of the whole review, because AR and AP are literally next after the current API/MCP-reads milestone.

### The core lesson: pick one open-item ledger and make it the only source of truth

ERPNext runs **three** parallel ledgers for receivables/payables — GL Entry, a derived "Payment Ledger Entry" (added in v14 specifically because computing outstanding by scanning GL rows was too slow), and a newer "Advance Payment Ledger Entry" for order-level advances — plus a denormalised `outstanding_amount` and a 13-value `status` string cached on every invoice. Because these are separate tables kept in sync by application code rather than by construction, they drift: there's a scheduled "Ledger Health Monitor" whose entire job is detecting when the payment ledger disagrees with the GL, a "Repost Payment Ledger" tool to fix it, and a string of GitHub issues along the lines of "invoice outstanding amount is higher than the invoice amount."

**PHP Ledger should adopt the underlying idea — an open-item ledger keyed by `against_voucher`, where an invoice is posted "against itself" and every settlement (payment, credit note, journal) is posted "against" the invoice it settles, so outstanding becomes a cheap sum over that invoice's own rows — but implement it as the single source of truth, not a third copy.** Concretely: make the open-item table the only place outstanding is computed from (derive invoice status on read, or via one trigger), and treat the GL control-account balance as something you *reconcile against* periodically, never as a second calculator for the same number. This gets ERPNext's real performance win (outstanding is O(rows for this invoice), not O(all history)) without its real cost (three tables that can disagree).

### Adopt outright

- **Party mandatory on every control-account line**, with no exceptions. This is what lets sub-ledger and control account reconcile at all; ERPNext's own AR-vs-trial-balance mismatches trace back to lines that escaped this rule (mostly manual journal entries).
- **Party-account resolution chain**: party → party group → company default, with the account fixed per party per company per currency. Covers the overwhelming majority of SME cases cheaply and avoids the "invoice posted against Debtors A, payment posted against Debtors B" dead end ERPNext's own docs warn about.
- **A payment-references table with an explicit `allocated_amount` per open item and an explicit `unallocated_amount` remainder.** One payment against many invoices, and vice versa, is universal in SME bookkeeping.
- **A first-class "unreconcile" operation** that reverses an allocation without cancelling the underlying payment. Reconciliation mistakes are normal; forcing a full cancel/amend of a bank-cleared payment just to fix a wrong allocation is what generates a lot of ERPNext's clearance-date bugs.
- **Ageing report driven by the open-item ledger**, with a configurable bucket string ("30, 60, 90, 120"), a toggle for due-date vs. posting-date ageing, and a not-yet-due bucket. Cheap once the open-item ledger exists, and it's exactly what an accountant asks for first.
- **Bank Transaction as its own immutable imported record**, separate from the accounting voucher it eventually matches or creates, with an explicit `allocated`/`unallocated` split and a `reconciliation_type` flag (matched an existing voucher vs. created a new one). Separating "what the bank said" from "what we posted" and recording the link explicitly is the right shape.
- **Rule-based auto-classification of recurring bank lines** (description contains/starts-with/regex + amount range → account/party). For a small trader, most bank lines are rent, fees, and a handful of recurring counterparties — rules remove most of the manual matching.
- **Supplier hold with a type (all / invoices only / payments only) and a release date.** Simple, cheap, and a real control SMEs actually want.

### Adapt

- **Credit notes: pick one linkage model and never let the system override it.** ERPNext flip-flopped between "a return reduces the original invoice" and "a standalone open item," landed on a setting (`update_outstanding_for_self`) that the system now silently forces back on in certain cases, and the result is a forum thread and a GitHub issue about a paid debit note flipping the *original* invoice's status to overdue. Recommended: a credit note is **always** a standalone open item with a negative balance; provide a one-click "apply this credit to invoice(s)" action that writes allocation rows without generating a hidden journal entry underneath. Consistent, and it never rewrites the original invoice's history.
- **Two-sided party identity (a customer who is also a supplier).** ERPNext models Customer and Supplier as entirely separate doctypes, so the same real business needs a "Party Link" plus an auto-generated journal entry to net balances between them — and ERPNext's own maintainers have an open issue listing the problems (orphaned journals on cancel, no combined ledger view, only works for invoices). The Pakistan SME research already on file for this project notes this exact pattern is common among small traders. Recommended: one party record can carry both a receivable and a payable role with two control-account balances, plus a first-class "net settle" action that allocates between the two open-item ledgers directly — no shadow journal entry required.
- **Advances against a separate liability account by default, tracked in the same open-item table.** ERPNext bolted this on after the fact and needed a third ledger and configurable "when does this take effect" dates to make it work; designing it in from the start (a customer deposit is a liability, full stop) avoids that.
- **FX gain/loss computed and posted at allocation time**, as part of the allocation itself — not as a separate, settings-driven, auto-generated journal entry that then needs its own cancel-and-recreate logic on unreconcile. Fewer moving documents, simpler audit trail.
- **One bank-clearance workflow, not two.** ERPNext still has both an older manual "Bank Clearance" tool and the newer Bank Transaction-based reconciliation, both able to write the same `clearance_date` field, which produces exactly the confusion you'd expect. Pick one.

### Avoid

- **Thirteen-variant denormalised status strings** ("Unpaid and Discounted," "Overdue and Discounted," etc.) maintained by a daily background job. Compute paid/partly-paid/overdue/discount-available at query time from the open-item ledger and today's date; there is nothing to go stale.
- **Silent unlinking of payments on invoice cancellation.** ERPNext's default behaviour detaches an allocated payment back into an unallocated advance the moment its invoice is cancelled, without asking. Silent changes to money allocation are exactly the kind of thing a bookkeeper needs to be asked about explicitly.
- **A separate "advance" semantics bolted onto manual journal entries.** One payment document type with allocations is enough; a manual journal should never double as a settlement instrument.

---

## 3. Inventory: relevant once AR/AP land and purchasing/inventory comes up next in sequence

The inventory report is the most technically dense of the four, and its headline finding is the same shape as the AR/AP one: **ERPNext keeps a full stock ledger (Stock Ledger Entry) and a derived balance cache (Bin) that is known to drift**, with the documented repair being "run a full recalculation." The GL side compounds it — every stock movement's value is *copied* into a GL row, so there are effectively two ledgers (stock value and GL value) kept in sync by application code, and ERPNext ships a dedicated "Stock and Account Value Comparison" report and a weekly auto-repair job specifically because they disagree in practice.

### Minimal viable model worth adopting directly

The inventory report proposes a concrete minimal schema for a small trader/shop that is worth adopting close to as-is when PHP Ledger gets to purchasing/inventory:

- An append-only stock ledger keyed by (item, location) with a **signed quantity delta and a value delta that the GL copies verbatim** — one number, produced once, posted twice (into the stock ledger and the GL), never independently recomputed twice.
- A balance cache that is **rebuilt from the ledger on every write**, not patched by incremental delta — cheap at SME transaction volumes and structurally cannot drift, unlike ERPNext's delta-patch approach.
- **Business timestamp + insertion order** as a stable, composite-indexed sort key, so back-dated entries have a well-defined total order under concurrent writes.
- **Moving-average valuation as the default**, not FIFO. No queue to persist as JSON, no queue to repost, and it's the valuation method an SME bookkeeper actually understands. Offer FIFO per item as an option, not the default.
- **Return-at-original-rate**: a sales return comes back valued at the original delivery's cost; a purchase return is valued out of the exact layer it came from. Keeps COGS symmetric without requiring user judgement.
- **Negative stock allowed per item, but never inventing a cost.** When an issue exceeds on-hand, post it at the last known rate and flag the row "provisional"; when the next receipt arrives, re-cost only the provisional rows (a small, bounded repost — not ERPNext's full-history replay).
- **A GRN/accrual step (goods received but not yet invoiced) that is optional and off by default.** ERPNext forces every stock item through a "Stock Received But Not Billed" accrual account even for the common small-shop case of receiving and invoicing in the same document — cash-basis traders should be able to post `Dr Inventory / Cr Payables` directly and turn on the accrual step only if they actually operate a receiving dock separately from billing.
- **Batches/lots as rows in the ledger itself** (a small child table: ledger row, lot id, quantity, unit cost), not as a text blob (ERPNext's pre-v15 approach, which had real data-integrity problems) and not as a separate heavyweight linked document (ERPNext's v15 "Serial and Batch Bundle" rewrite, which fixed the integrity problem but is now the most-complained-about UX regression in the whole product — forum quotes include "it took a complete hour to create an invoice with 14 items with serials" and a maintainer's own admission "I know this is a major change... we are still improving the UX").

### Avoid specifically

- **A settings surface north of 50 switches** (ERPNext's Stock Settings). Most exist to undo an earlier feature. Cap inventory settings at well under ten: default valuation method, allow-negative-stock, a frozen-up-to date, over-delivery tolerance, GRN-accrual on/off, expiry-blocking on/off.
- **Company-wide "perpetual inventory on/off" as a switch that can be flipped after data exists.** Fix the accounting mode at company setup, or make switching it a real migration that posts a catch-up journal — not a checkbox.
- **Item code or warehouse name as the actual primary key.** ERPNext's rename cascades (touching stock ledger, balance cache and GL) exist purely because it used business keys as primary keys. Use surrogate ids from day one.

---

## 4. Interface, settings, localisation and platform — this is where the Urdu/RTL plan gets real evidence

This section validates and sharpens decisions already on the table (Urdu-first, then Arabic; RTL; queued-entry-only offline; a single "simple VPS" deployment story) with hard evidence from ERPNext's own record.

### The Urdu/Arabic finding, directly relevant to a decision already made

PHP Ledger's decision register already commits to Urdu first, then Arabic, with RTL support. The review found that **ERPNext's own Urdu localisation is in worse shape than you'd assume from a 15-year-old, India-headquartered, widely-deployed product**: there is no Urdu translation file at all in the current `develop` branch (a 404, confirmed by direct fetch) or in the latest released version line; the older v15 Urdu file that does exist is machine-translation quality with visible artefacts (literal `&quot;` HTML entities left untranslated, "Import of Capital Goods" mistranslated using the word for capital *city* rather than the financial term); and — most strikingly — **v15's own code did not even classify Urdu as a right-to-left language**, so a v15 site could select Urdu and still get left-to-right layout. Arabic fares a little better (roughly 75-80% translated in current files) but a community member's own account is that it was "40-50%... a poor one, machine-generated" until someone manually re-translated it with an LLM in batches as recently as late 2025.

Three concrete, low-cost takeaways for PHP Ledger:
1. **Treat "RTL" as a per-language database flag, checked explicitly, not inferred.** Do not assume a language is RTL from its presence in a hardcoded list the way ERPNext's older code did — that is exactly how Urdu ended up LTR for years.
2. **Budget human review, not just machine translation, for the roughly 1,000-2,000 strings an accountant actually sees** (invoice terminology, account types, tax labels, error messages) even if the long tail of rarer strings stays machine-translated initially. The strings that matter most for trust — tax and financial terminology — are exactly where ERPNext's machine translation embarrassed itself.
3. **Since PHP Ledger's product surface is far smaller than ERPNext's** (no manufacturing, no HR, no CRM), a full human-reviewed Urdu translation is a genuinely achievable, differentiating milestone rather than the open-ended task it would be for a 15-module ERP — this is a real opportunity, not just a risk to avoid.

### The "settings sprawl" and "generic ERP shell" findings, relevant to positioning against ERPNext directly

Frappe's own 2026 conference keynote admits ERPNext has "accumulated" features producing "unintuitive workflows," and — this is worth knowing when someone asks "why not just use ERPNext" — **Frappe itself built a separate, simpler product ("Frappe Books") specifically because ERPNext is "overwhelming for small business owners who are just starting out with accounting."** That is a strong, citable validation of PHP Ledger's whole premise, straight from the incumbent's own maker. Concretely measured: ERPNext's Accounts Settings has 67 fields across 8 tabs; the Sales Invoice form itself has 235 fields; the main accounting workspace in the current version has 45 links across 9 cards. A Sales Invoice in ERPNext is one of roughly 15 modules that all look identical because everything is generated from the same generic meta-driven UI — which is a real strength for a platform vendor and a real weakness for an SME accountant who wants software that looks and feels like accounting software.

**Adopt**: the *idea* of a meta-driven document model (one definition generates list, form, print and API) is worth having at a smaller scale — but the product surface built on top of it should look purpose-built for accounting, not like a database explorer. Comments, attachments, and change history on every document by default is cheap once built at the base level and is the single most-praised feature in ERPNext's own user reviews.

**Adapt — the Submit/Cancel/Amend friction, again.** This surfaces again independently in the interface report: it is the single most-discussed UX complaint across forum and GitHub history (an issue asking for "amend without cancelling" has been open since 2017), and ERPNext's own 2022 attempt to soften it (letting accounts/cost-centres be edited after submit via a "repost" action) introduced duplicate-ledger-entry bugs during review. This independently confirms the §1 recommendation: solve this properly with a correction/supersede model from day one rather than retrofitting it later the way ERPNext had to.

**Avoid — the deployment stack.** This is the most directly relevant platform finding to "runs on a simple VPS," PHP Ledger's own positioning. ERPNext's stack is Python + Node + MariaDB + three separate Redis instances + a background-worker fleet + a Node SocketIO server + nginx + supervisor, orchestrated by a purpose-built tool (`bench`, itself now being replaced). Official guidance recommends 8GB RAM as a *starting point*, and Hacker News commentary independently calls the deployment "very peculiar" and, from a 2021 account, "a nightmare... half a day trying to get it up." This is precisely the gap PHP Ledger's PHP-FPM + MySQL-on-a-cheap-VPS story is positioned to fill, and this review is direct evidence that the gap is real and that ERPNext's own users feel it, not just a hypothesis.

**Avoid — major-version upgrades that break the extension API.** Every ERPNext major version (v13→14→15→16) has changed runtime requirements (Python and Node versions) and removed APIs that partner apps depended on, producing a well-documented multi-hour "field-tested" upgrade process and predictable breakage. Commit to a stable extension/plugin API and migrations that never force a PHP major-version jump inside a product major release.

**Adopt — a few specific, cheap platform features**: webhooks with HMAC signatures and simple JSON-array filter syntax for the REST API (this is one of ERPNext's most-praised integration features and is cheap to replicate); typed onboarding steps ("go to page," "create entry," "view report") tied to real first-use tasks ("send your first invoice," "reconcile your first bank statement") rather than ERPNext's current six generic, content-free onboarding steps; drill-down financial statements with monthly/quarterly/yearly comparative columns and click-through from a statement line straight to the underlying ledger — these are the exact filters accountants use daily, and the spec is now validated by this review.

---

## 5. What this means for the roadmap, concretely

Mapped against the sequence already agreed (API/MCP reads → AR → AP → Pakistan tax adapter/FBR client → purchasing/inventory → shop POS → …):

1. **Before AR is built**: confirm the posting funnel is singular and enforced (§0), and settle the cancellation/amendment model now (§1, §2) — reversing entries dated on cancellation by default, never a renamed `-1` copy. This is cheap to decide before any AR document type exists and expensive to retrofit after invoices are in the wild, and it is a hard legal requirement once the FBR client (which needs gapless, non-renumbered invoice sequences) is built on top of it. This is the single most time-sensitive recommendation in this whole review.
2. **When AR/AP are built**: adopt the open-item-ledger-as-single-source-of-truth pattern from §2 directly — it is the part of this review most directly usable right now, and doing it as one ledger rather than ERPNext's three avoids inheriting their single biggest AR/AP support-ticket category (things that don't match).
3. **When the Pakistan tax adapter is built**: note that ERPNext has no Pakistan chart of accounts at all — an open gap in the incumbent product PHP Ledger can close on day one.
4. **When purchasing/inventory is built**: adopt the minimal viable model in §3 rather than ERPNext's full stock-ledger-plus-Bin-plus-serial/batch-bundle stack; moving average as default valuation, not FIFO.
5. **Ongoing, in parallel**: the Urdu/RTL findings in §4 are actionable now, independent of any other roadmap item — treat RTL as an explicit per-language flag and budget human review for the accounting-critical string set.
6. **Marketing/positioning, no engineering required**: Frappe's own admission that ERPNext is "overwhelming for small business" and their own decision to build a simpler alternative is a strong, citable data point for PHP Ledger's positioning against the incumbent — worth having in the pitch deck / landing-page copy.

---

## 6. Open questions for the owner

These are places the research surfaced a real design fork, not a clear "ERPNext got it wrong" — worth a decision, not a default:

- **Cancellation reversal date**: reverse-on-cancellation-date always, or allow reversal-on-original-date while the period is still open, gated by a permission? ERPNext ships both behind a switch because SMEs and accountants genuinely disagree about backdated corrections at audit time. Recommendation in this document assumes the permissioned exception; confirm.
- **Two-sided party (customer who is also a supplier)**: build the single-party/two-role model from §2 now, before AR/AP schemas are finalised, or treat it as a fast-follow after basic AR/AP ship? The Pakistan SME research already on file suggests this is common enough to matter for launch, not just a nice-to-have.
- **GRN/goods-received accrual**: default off (cash-basis, receive-and-invoice-in-one-step) with accrual as an opt-in upgrade, as recommended in §3 — confirm this matches how the target trader actually operates, versus consultants/accountants who may expect the accrual step by default.

---

## Appendix: source reports

This synthesis draws on four full technical reports produced the same day, each independently sourced against ERPNext's code, documentation, and forum/issue history, with every claim tied to a file path or URL:

- **Accounting core & Accounts Settings** — GL model, chart of accounts, fiscal year/period closing, accounting dimensions, journal entry types, multi-currency, taxes, audit/immutability. ~360 sourced claims, 10 ADOPT + 9 ADAPT + 6 AVOID items.
- **AR/AP & bank reconciliation** — party model, invoice lifecycle, payment entry, reconciliation, ageing/statements, dunning, bank reconciliation. ~180 sourced claims, 12 ADOPT + 10 ADAPT + 5 AVOID items.
- **Inventory/stock** — item master, warehouses, stock ledger/valuation engine (FIFO/LIFO/moving average), perpetual-inventory GL integration, batches/serials, a proposed minimal schema. ~200 sourced claims, 9 ADOPT + 8 ADAPT + 7 AVOID items.
- **Interface, onboarding, reporting, permissions, extensibility, platform** — setup wizard, Desk interaction model, reporting/dashboards, permissions, extensibility (customisation, scripting, API, hooks), localisation (measured Urdu/Arabic coverage and RTL support), platform/deployment stack. ~150 sourced claims, 11 ADOPT + 9 ADAPT + 7 AVOID items.

Each report's full source list (raw GitHub file paths, docs.frappe.io pages, GitHub issues/PRs, discuss.frappe.io threads, and — for the interface report — Hacker News discussion and live probes of ERPNext's own demo infrastructure) is preserved in the project's working files and can be pulled in full if a specific claim needs to be traced back further than this synthesis does.

---

*Prepared 15 September 2026 as part of the PHP Ledger strategy review. See also: [Decision register](DECISION-REGISTER.md), [Competitive landscape](COMPETITIVE-LANDSCAPE.md) (ERPNext's market position), [Distribution plan](DISTRIBUTION-PLAN.md).*
