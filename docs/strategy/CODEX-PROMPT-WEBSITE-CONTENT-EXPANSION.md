# Codex prompt: build the phpledger.com content library

Companion to `docs/strategy/CODEX-PROMPT-WEBSITE-SEO-AEO-GEO.md`. That prompt fixes the nine existing pages. This one builds the site out from 9 pages to roughly 45.
Paste everything below the line into Codex at the repository root.

---

You are working in the PHP Ledger repository (`C:\phpledger`). This task adds pages to the marketing website under `www/website/`. **Do not touch the accounting application** (`www/phpledger/`), and do not change any existing published URL.

**Run the SEO/AEO/GEO prompt first.** It establishes the build-system changes every page here depends on: the `webpage`, `person`, `howto` and `itemlist` JSON-LD graph parts, required `lastmod`, the generated table of contents, the generated `llms.txt` and AI-discovery files, the footer byline and `<time>` element, and the `<dl>` / `<pre>` / summary-block CSS. If those are not in place, stop and do that task first.

Read before starting: `www/website/README.md`, `www/website/build.mjs`, `www/website/check.mjs` (its header comment is the rule list), `AGENTS.md`, `CONTRIBUTING.md`.

Build and validate with `node www/website/build.mjs --check`. Commit source and generated output together. Do not push. Do not deploy.

---

## 1. The problem, stated plainly

The site is nine pages and roughly 6,000 words. Every one of them is a product page. There is no page on phpledger.com that answers a question someone asks *before* they know the product exists, which is why the site ranks and gets cited for nothing except its own name.

Meanwhile the repository already contains **about 56,000 words of original, sourced research** that has never been published:

| Source document | Words | What it is |
|---|---|---|
| `docs/strategy/ERPNEXT-REVIEW.md` | 5,116 | A technical teardown of ERPNext's accounting model with specific findings |
| `docs/strategy/MULTI-CURRENCY.md` | 3,791 | Multi-currency accounting design research |
| `docs/coa/ACCOUNT_CANDIDATES.md` | 2,789 | Chart-of-accounts research across seven business profiles |
| `docs/strategy/PAKISTAN-MARKET.md` | 2,604 | Market research (internal — extract facts only, see §7) |
| `docs/accounting/REPORTING_GAP_ANALYSIS.md` | 2,482 | Financial-statement reporting analysis |
| `docs/accounting/PAKISTAN_REPORTING_RESEARCH.md` | 2,401 | Pakistan reporting framework research |
| `docs/strategy/research/pakistan-sme-bookkeeping-reality.md` | — | **Original primary research** on what Pakistani SMEs actually use: khata apps, download and MAU figures, which players died and when |
| `docs/strategy/research/pakistan-fbr-einvoicing-regime.md` | — | The FBR digital-invoicing regime |
| `docs/strategy/research/pakistan-vendor-landscape.md` | — | Pakistan vendor landscape |
| `docs/strategy/HOSTING-PHP-COMPATIBILITY.md` | 680 | **Original research** on PHP version availability across cPanel, Plesk, Hostinger, SiteGround, each with a primary-source link and a check date |
| `docs/strategy/COMPETITIVE-LANDSCAPE.md` | 859 | Five tiers of competitors, with licences, stacks and sourced notes |
| `docs/tax/PAKISTAN.md`, `UK-UAE.md`, `ASIA.md` | 4,583 | Tax regime research |
| `docs/accounting/CORE_RULE_REGISTER.md` | 1,277 | The accounting rules the system enforces |
| `docs/accounting/examples/retail-statements.md` | — | A two-year sample statement fixture, arithmetic-checked |
| `docs/wiki/*.md` | ~4,900 | Product and architecture documentation |

Some of that is commercially sensitive and stays internal (§7). Most of it is **exactly the material that generative engines cite**: original figures, primary sources, dated verification, honest comparison, and specificity nobody else in this category has bothered to produce.

The job is not to write filler. It is to turn research the project already did into pages that answer real questions.

---

## 2. Hard constraints

Everything in §2 of the SEO/AEO/GEO prompt still applies — CSP, no external resources, no client-side storage or network, byte budgets, banned words, no `aggregateRating` or `review` in JSON-LD, meta description 50-160 characters, one `<h1>`, unique titles, canonical equals `baseUrl + path`. Plus:

1. **Nothing here ships a feature.** PHP Ledger has no AR/AP, no inventory, no cost of sales, no tax engine and no statutory reporting. Pages about Pakistan tax, charts of accounts or multi-currency are **research and education**, never a claim of support. Every such page carries an explicit scope line saying so, near the top, above the fold.
2. **Every external claim needs a primary source and a check date.** The research documents already work this way — carry the URLs and the "checked on" dates through to the page. A claim you cannot source does not get published.
3. **Carry `UNVERIFIED` markers through.** `pakistan-sme-bookkeeping-reality.md` marks uncertain facts. If the source says UNVERIFIED, the page says "not verified" in plain words or omits the claim. Do not launder an uncertain figure into a confident one.
4. **Comparisons must lose rows.** See §4.2.
5. **Accounting explainers need a review gate.** General double-entry bookkeeping is textbook material and safe to publish. Anything touching a jurisdiction, a statutory framework, a tax rate or a filing obligation is unreviewed research and must be labelled as such. Keep "technical checks passed" distinct from "reviewed by an accountant" exactly as the repository does.
6. **Do not copy source documents wholesale.** The internal docs are written for the owner. Public pages are written for a stranger with a question. Restructure, rewrite, cut the internal reasoning, keep the evidence.
7. **No new URL collides with or replaces an existing one.** Everything here is additive.

---

## 3. Information architecture

Five new clusters, each with a hub page and children. Hub pages are real content, not link lists — 600-900 words that frame the cluster and answer the category question.

```
/glossary/                          one page, the definitional spine of the site
/learn/                             hub + 10  — bookkeeping education
/compare/                           hub + 6   — competitive and category intent
/self-hosting/                      hub + 6   — the deployment-floor wedge
/research/                          hub + 4   — original data, the citation moat
/guides/                            existing hub + 3, extend to 8
/for/                               3 audience pages
```

### Navigation

`src/site.json` `nav` currently holds five entries. Add **one**: `["learn", "Learn", "/learn/"]`, placed after "Point of sale".

`/compare/`, `/research/`, `/for/` and `/glossary/` go in the **footer** columns, not the main nav. Restructure `src/partials/footer.html` into five columns: Product, Learn, Compare, Project, Contact. Keep every existing footer link — `check.mjs` errors on missing navigation destinations and broken anchors.

**OWNER DECISION:** a larger nav restructure (dropping Roadmap into the footer, adding a Compare top-level item) is worth considering once the clusters exist. Do not do it; propose it in the final summary with the before/after nav.

**`nav` front-matter key.** `build.mjs` sets `aria-current="page"` when a page's `nav` key matches a `site.nav` entry, and `check.mjs` errors if a non-nav page carries `aria-current` at all. So: every page under `/learn/` uses `"nav": "learn"` and correctly highlights the Learn tab. Pages under `/compare/`, `/research/`, `/for/` use `"nav": "compare"` / `"research"` / `"for"` — keys that are deliberately *not* in `site.nav`, exactly as `/guides/` already does.

### Front matter for every new page

```json
{"path": "/learn/trial-balance/", "nav": "learn",
 "title": "…", "description": "… (140-158 chars)",
 "ogImage": "/assets/og/home.png", "ogType": "article",
 "bodyClass": "page-learn",
 "jsonld": ["organization", "webpage", "person", "breadcrumb", "faq"],
 "breadcrumb": "Trial balance",
 "faq": [{"q": "…", "a": "…"}],
 "lastmod": "2026-09-…"}
```

Omit `budgetEager` / `budgetTotal` on text-only pages (they are optional — `about.html` omits them). Declare them on any page carrying images, following the values on comparable existing pages.

**Breadcrumbs need parents.** `build.mjs`'s `breadcrumb()` resolves the parent page by path and fails the build if it is missing. Create every hub page **before** its children.

---

## 4. The pages

Word counts are floors, not targets to pad to. Each page follows the content patterns from §4 of the SEO/AEO/GEO prompt: Key takeaways block, a 40-200 character answer after every heading, question-shaped H3s covering what/why/how/when/who/which, 120-180 word self-contained sections, `<dl>`, tables, both list types, `<figure>`/`<figcaption>`, FAQ `<details>`, a "when this is not the right choice" section, and a closing recommendation.

### 4.1 `/glossary/` — build this first

**900-1,400 words.** One page, 30-40 terms, marked up as a single `<dl>` with `id` on each `<dt>` so every term is directly linkable (`/glossary/#trial-balance`).

Terms from `docs/accounting/CORE_RULE_REGISTER.md`, `docs/wiki/Accounting-and-Reports.md` and `docs/coa/TEMPLATE_MODEL.md`: account, accrual, balance sheet, base currency, bank reconciliation, chart of accounts, closing balance, control account, credit, cutover, debit, double-entry, drawings, equity, fiscal period, general journal, journal entry, ledger, liability, opening balance, period close, posting, posting role, profit and loss, receipt, reversal, running balance, subledger, tender, trial balance, unposted draft.

Each definition: 25-60 words, opening "X is …", plus a one-line note on how PHP Ledger treats it where that differs from the textbook (drafts are unposted; corrections are linked reversals, never edits).

**Why this page first:** every other new page links into it, which builds the internal link graph the whole cluster depends on, and it is the single highest-value AEO asset on the site — a `<dl>` of 35 "X is …" definitions is precisely the structure `geo_definitions`, `aeo_definition_lists` and `geo_featured_snippet` reward.

### 4.2 `/compare/` — hub + 6

Highest commercial intent. `docs/strategy/DISTRIBUTION-PLAN.md` already names these as an owner priority. Source: `docs/strategy/COMPETITIVE-LANDSCAPE.md` and `docs/strategy/ERPNEXT-REVIEW.md`.

| Page | Words | Core of it |
|---|---|---|
| `/compare/` | 900 | The category: what self-hosted accounting actually means, the five tiers from the landscape doc, how to choose, and the comparison table |
| `/compare/akaunting-alternative/` | 1,200 | Akaunting moved to BSL and is no longer OSI open source; core features sit behind a paid App Store. State it factually with sources, never sneeringly |
| `/compare/frontaccounting-alternative/` | 1,000 | Genuine double-entry, runs anywhere, ageing out — docs still recommend PHP 5.6/7.x. This is the seat PHP Ledger is taking |
| `/compare/erpnext-alternative/` | 1,200 | Full-featured but needs ~4GB RAM and real ops skill. Draw on the ERPNext review's specific findings |
| `/compare/dolibarr-alternative/` | 900 | The nearest philosophical competitor: PHP, cheap hosting, no Docker. Be fair — it is alive and it installs on shared hosting |
| `/compare/bigcapital-alternative/` | 900 | Modern, real double-entry, actively developed, AGPL — and Node/Docker, not a cheap PHP VPS |
| `/compare/open-source-accounting-software/` | 1,500 | Category page covering all five tiers including the personal-ledger and invoicing-tool adjacents |

**The rule that makes these worth publishing.** Every comparison page carries a **"Where PHP Ledger loses"** section, named that plainly, listing what the competitor does that PHP Ledger does not: Akaunting has invoicing and receivables today; ERPNext has inventory, manufacturing and a mature ecosystem; Dolibarr has a decade of modules; Bigcapital has a working AR/AP. PHP Ledger's honest claims are the deployment floor, the licence position, and accountant-grade correctness on the primitives it does implement — nothing else, yet.

A comparison page that says the product wins everything gets cited by nobody and believed by nobody. One that says "use Akaunting today if you need to invoice a customer this week" is the page an AI assistant quotes.

**Sourcing.** Every competitor claim links to that project's own documentation or repository, with "verified on ⟨date⟩". Where `COMPETITIVE-LANDSCAPE.md` says to verify star counts and licences before quoting externally — **do that**, and if you cannot verify from a primary source at build time, write "not verified" rather than publishing the internal figure.

Comparison tables use `<table>` with `<caption>`, `<thead>` and row headers. Axes: licence and whether it is OSI-approved, hosting model, stack, minimum server, data location, phone-home or licence-key behaviour, double-entry, AR/AP, inventory, tax/e-invoicing, current maturity.

### 4.3 `/learn/` — hub + 10

Top-of-funnel education, and the strongest AEO structure available. Source: `docs/accounting/CORE_RULE_REGISTER.md`, `docs/wiki/Accounting-and-Reports.md`, `docs/accounting/REPORTING_GAP_ANALYSIS.md`, `docs/coa/`.

| Page | Words | Question it answers |
|---|---|---|
| `/learn/` | 900 | What bookkeeping a small business actually needs, and the order to learn it in |
| `/learn/double-entry-bookkeeping/` | 1,400 | What double-entry is, why every entry balances, and what single-entry costs you |
| `/learn/debits-and-credits/` | 1,200 | The rule that confuses everyone, with T-account diagrams |
| `/learn/journal-entry/` | 1,000 | What a journal entry contains and what makes one valid |
| `/learn/chart-of-accounts/` | 1,400 | How to structure one; posting role vs number vs label; the seven business profiles from `docs/coa/` |
| `/learn/trial-balance/` | 1,000 | What it proves, what it does not, and what to do when it does not balance |
| `/learn/profit-and-loss-vs-balance-sheet/` | 1,200 | The two statements, what each answers, how they connect |
| `/learn/cash-vs-accrual/` | 1,000 | The distinction, and which a small trader should start with |
| `/learn/bank-reconciliation/` | 1,100 | What it is, why it is the control that catches everything else |
| `/learn/correcting-mistakes-reversal-vs-edit/` | 1,000 | Why an audit trail forbids editing a posted entry. **This is the project's philosophical wedge — give it the best writing on the site** |
| `/learn/opening-balances-and-cutover/` | 1,100 | Moving from whatever you use now onto a ledger without losing history |

Each page: define the term, explain it with the sample-company figures already on the site (1,000.00 in, 125.00 out, 875.00 balance), show it as a table or T-account, say what goes wrong in practice, then link to the relevant `/guides/` walkthrough and `/product/` section. Link every technical term to its `/glossary/` anchor on first use.

**Diagrams.** T-accounts, the transaction lifecycle (draft → post → report → reversal) and the two-statement relationship deserve pictures. Author them as **inline `<svg>`** in the page HTML or as local `.svg` files under `/assets/diagrams/`. Either is CSP-safe (`img-src 'self'`), but **`check.mjs` errors on any `style=` attribute anywhere**, so SVG must use presentation attributes (`fill="currentColor"`, `stroke-width="2"`) or CSS classes defined in a new `src/css/22-diagram.css`. Never a `style=` attribute, never an inline `<style>` block inside the SVG. Give every diagram `role="img"` and a `<title>`, and wrap it in `<figure>` with a `<figcaption>` that explains what it shows. Local `.svg` files used via `<img>` still need `alt`, `width` and `height`.

**Review gate.** These pages make accounting claims. Add a line to `docs/accounting/README.md` recording that the `/learn/` set is published as general bookkeeping education, is not jurisdiction-specific, and has not been reviewed by a qualified accountant. Put a short, non-alarming version of that on each page near the foot, linked to `/about/`.

### 4.4 `/self-hosting/` — hub + 6

The deployment floor is the project's defensible position and there is currently no page about it. Source: `docs/strategy/HOSTING-PHP-COMPATIBILITY.md`, `docs/wiki/PHP-Hosting.md`, `docs/wiki/Getting-Started.md`, `docs/wiki/First-Package.md`, `resources/release/INSTALL.md`, `Dockerfile`, `compose.yaml`.

| Page | Words | Core of it |
|---|---|---|
| `/self-hosting/` | 900 | What self-hosting accounting means, what it costs, what you take on |
| `/self-hosting/requirements/` | 900 | PHP 8.2+ (8.3 recommended), MySQL 8.4 InnoDB, HTTPS, terminal access, extensions, as a table with minimum and recommended columns |
| `/self-hosting/shared-hosting-cpanel/` | 1,200 | The cPanel/Plesk path. The largest realistic audience and the hardest thing for competitors to match |
| `/self-hosting/vps-install/` | 1,300 | A 1GB VPS, start to finish, numbered steps with real commands |
| `/self-hosting/docker/` | 1,000 | The Compose path, from the repository's actual `compose.yaml` |
| `/self-hosting/php-version-compatibility/` | 1,200 | **Publish the hosting research directly.** The cPanel/Plesk/Hostinger/SiteGround availability table with its four primary-source links and the 15 September 2026 check date; PHP 8.2 security support ending 31 December 2026 and 8.3 ending 31 December 2027, sourced to php.net; the CI matrix (8.2.33, 8.3.33, 8.4.25) and the test results actually recorded |
| `/self-hosting/backup-and-restore/` | 1,000 | Database and file backup, restore rehearsal, what to test before trusting it |

These pages are dense with `<pre><code>` and numbered `<ol>` steps — which closes `aeo_code_blocks` and `geo_structured_answers` across the cluster. Every command must match `resources/release/INSTALL.md` and the actual Docker files. **If a command on a page and a command in the installer disagree, fix the page and report the discrepancy — never quietly change the installer in this task.**

Add `howto` JSON-LD (the graph part added in the other prompt) to `/self-hosting/vps-install/`, `/self-hosting/shared-hosting-cpanel/` and `/self-hosting/docker/`.

### 4.5 `/research/` — hub + 4

This is the citation moat. Nobody else in this category publishes primary research, and these are the pages an AI assistant will cite for questions that have nothing to do with buying accounting software.

| Page | Words | Source |
|---|---|---|
| `/research/` | 700 | What the project researches and why it publishes it; method and verification policy |
| `/research/pakistan-sme-bookkeeping/` | 2,000 | `strategy/research/pakistan-sme-bookkeeping-reality.md`. The khata-app table with downloads, ratings, last-update dates and pricing; the Data Darbar finding that ~17.8M installs produced ~740K monthly actives, roughly 4%; which B2B players died and when; that DigiKhata Pro is the one hard test of paid subscription and it failed. **Carry every UNVERIFIED marker through.** |
| `/research/fbr-digital-invoicing/` | 1,800 | `strategy/research/pakistan-fbr-einvoicing-regime.md` and `tax/PAKISTAN.md`. High search intent in Pakistan and almost nothing authoritative in English. **Must open with a scope line: this is research; PHP Ledger has no tax engine and no FBR client today; the FBR client is planned, not shipped.** |
| `/research/php-hosting-availability-2026/` | 1,000 | The host-availability study, if not already fully covered by `/self-hosting/php-version-compatibility/` — otherwise fold it in and drop this page rather than duplicating |
| `/research/open-source-accounting-landscape/` | 1,800 | `COMPETITIVE-LANDSCAPE.md` as a public survey: five tiers, what each is for, where the category consistently fails (e-invoicing mandates, deployment floor, accountant-grade correctness) |

Each research page carries a visible method block: research date, what was checked, what was not, what is unverified, and the primary sources as a real list. Give each an `article` JSON-LD node with `datePublished`, `dateModified` and the `Person` author.

**Publishing dated, sourced, honestly-hedged research with a named author is the single strongest E-E-A-T and GEO signal this project can produce**, and it costs nothing but the transcription because the research is already done.

### 4.6 `/guides/` — extend from 3 to 8

The existing three walkthroughs are the best content on the site and `/guides/` still scores worst (AEO 56, GEO 55) because the index is 256 words. The other prompt fixes the index; this adds depth.

New walkthroughs, from `docs/accounting/examples/retail-statements.md` and the four sample businesses in 0.2.1:

- `/guides/retail-two-year-statements/` (1,400) — the two-year fixture: P&L, balance sheet, the depreciation add-back. Label it exactly as the source does: an original documentation fixture, arithmetic-checked, not loaded sample data, not a real business, not a tax calculation.
- `/guides/first-week-on-the-books/` (1,200) — company setup to first trial balance.
- `/guides/opening-balances-cutover/` (1,200) — moving onto the ledger mid-year.
- `/guides/bank-reconciliation-walkthrough/` (1,200) — CSV matching to a reconciled statement.
- `/guides/correcting-a-posted-entry/` (900) — a linked reversal, start to finish.

Every guide keeps the existing sample-data label (`check.mjs` errors without it on any page showing `/assets/screens/`), keeps `srcset` and `sizes` on every capture, and wraps each in `<figure>` with a `<figcaption>`.

### 4.7 `/for/` — 3 audience pages

`docs/strategy/DECISION-REGISTER.md` (decision A2) establishes that the installer is usually not the end user. Write to the installer.

- `/for/accountants/` (1,200) — audit trail, reversal discipline, period locking, trial balance, what is not there yet, what a practitioner should wait for.
- `/for/software-houses/` (1,000) — self-hosting for clients, the licence position, the API/MCP read layer, what deploying it involves.
- `/for/small-businesses/` (1,000) — plain language, the demo, honest "you may not be ready for this yet".

Each one ends with an honest redirect: who should use something else today, and which of the `/compare/` pages to read.

---

## 5. Internal linking

The cluster is worth more than the pages. Rules:

1. **Every technical term links to its `/glossary/` anchor on first use per page.**
2. **Every hub links to all its children; every child links back to its hub** and to two siblings.
3. **Every `/learn/` page links to at least one `/guides/` walkthrough** that demonstrates the concept, and one `/product/` section that implements it.
4. **Every `/compare/` page links to `/download/` and to `/self-hosting/requirements/`** — comparison traffic is evaluating, and the requirements page is where the honest disqualification happens.
5. **Every `/research/` page links to the `/compare/` page on the same subject.**
6. **No orphans.** Every new page is reachable from the footer or from a hub that is in the footer or nav.
7. Link with descriptive anchor text, never "click here" or "read more".

Add a `docs/design/website/CONTENT-REGISTER.md` recording, per page: URL, source document, word count, publication date, review status (published / unreviewed research / awaiting accountant review), and its primary sources. This is how the library stays honest as it grows, and it is what the next person will need.

---

## 6. Phasing

Do not build 38 pages in one commit. Three phases, each independently shippable, each ending green on `build.mjs --check` and the `aeo-geo-audit.mjs` auditor from the other prompt.

**Phase 1 — the spine (11 pages, ~12,000 words).** `/glossary/`, `/learn/` hub + double-entry + debits-and-credits + trial-balance + chart-of-accounts + correcting-mistakes, `/compare/` hub + akaunting + frontaccounting + open-source-accounting-software. This establishes the glossary link target, the nav change, and the comparison template.

**Phase 2 — the wedge (13 pages, ~14,000 words).** The whole `/self-hosting/` cluster, the remaining `/learn/` pages, `/compare/erpnext-alternative/`.

**Phase 3 — the moat (14 pages, ~18,000 words).** The `/research/` cluster, the five new guides, the three `/for/` pages, the remaining comparisons.

After each phase: regenerate `sitemap.xml`, `llms.txt` and the AI-discovery JSON (all automatic if the other prompt's generators are in place — verify, do not assume), re-run the auditor, and record the phase in `www/website/design-qa.md`.

---

## 7. Do not publish

These are internal. Extract *facts* from them where a public page needs evidence; never publish the documents, their reasoning, or their commercial content.

| Document | Why |
|---|---|
| `docs/strategy/DECISION-REGISTER.md` | Internal commercial decisions, unannounced positioning |
| `docs/strategy/DISTRIBUTION-PLAN.md` | Channel strategy, partner terms, revenue-share arrangements, sequencing gates |
| `docs/strategy/PAKISTAN-MARKET.md` | Commercial positioning and targeting. The underlying *research* in `strategy/research/` is publishable; the market strategy is not |
| `docs/FUNDING.md` | Budget lines |
| `docs/strategy/AR-AP-PARTIES-AND-VETTING.md` §5 | The A75 CRM linkage describes another company's internal system |
| `docs/strategy/PHP-8.2-AUDIT.md` | Internal blocker record. The *resolution* in `HOSTING-PHP-COMPATIBILITY.md` is publishable |
| `docs/LICENSING-POLICY.md` candidate module list | Candidate commercial modules are explicitly not declared. Never name them publicly |
| Anything with a price | No pricing is declared. Do not imply one, including "free forever" beyond the existing licence statement |

Also do not publish: any figure marked UNVERIFIED as if it were verified; any competitor claim without a primary source; any statement that PHP Ledger supports tax, e-invoicing, AR/AP or inventory.

---

## 8. Quality bar — what gets rejected

Before committing any page, check it against these. A page that fails one of them does not ship.

1. **Does it answer a question a stranger would actually type?** If the only person who would search for this page's subject already knows the product exists, it belongs on `/product/`, not in a cluster.
2. **Would this page be worth reading if PHP Ledger did not exist?** The `/learn/` and `/research/` pages must pass this outright. Product mentions are the last third of the page, not the first.
3. **Is every number sourced?** Name the source in the text and link it.
4. **Does it say what it does not know?** Every substantive page has a limits section.
5. **Does it say when PHP Ledger is the wrong answer?** Every page. No exceptions.
6. **Could a competitor read it and find nothing unfair?** If not, rewrite it.
7. **Is it free of filler?** No "in today's fast-paced business environment". No restating the heading as the first sentence. No paragraph that survives being deleted. Check the banned-word list in `check.mjs` (`empower`, `seamless`, `streamlin*`, `robust`, `effortless`, `unlock`, `elevat*`) — but the real test is stricter than the regex.
8. **Does it read like the existing site?** The current copy is spare, concrete and unembarrassed about limitations. Match it. The pronoun and transition-word changes from the other prompt loosen the register; they do not turn it into marketing.

---

## 9. Verification

1. `node www/website/build.mjs --check` — zero errors. Report the warning count per phase; it must not increase.
2. `www/website/tools/aeo-geo-audit.mjs` (from the other prompt) passes on every new page. New pages are held to the same thresholds as existing ones — a new page that scores worse than `/guides/` did makes the site worse, not better.
3. Every breadcrumb resolves to a real parent page.
4. Every `/glossary/#term` anchor referenced from any page exists (`check.mjs` errors on unresolved cross-page anchors — this will catch glossary drift, which is the most likely failure mode in Phase 1).
5. No duplicate titles, no duplicate ids, every description 50-160 characters.
6. `sitemap.xml` lists every new indexable page; `llms.txt` and `/ai/summary.json` include the new clusters.
7. Every new page appears in `docs/design/website/CONTENT-REGISTER.md`.
8. Spot-check five new pages in the local Compose service at `http://127.0.0.1:18201/` — headings, TOC anchors, `<details>` keyboard operation, diagram rendering in both light and dark rendering contexts if the site supports them.

---

## 10. Commits

One commit per cluster, in phase order. Within a phase: hub page first (breadcrumbs depend on it), then children, then the linking pass, then the content register.

For each commit: a short summary, every file changed, the word count added, and the source documents drawn on.

## 11. Final summary to produce

- Page count and word count before and after, per cluster.
- The auditor's per-page metrics for every new page.
- Every claim you could not source, and what you did instead.
- Every `UNVERIFIED` item carried through, and where it appears publicly.
- Any discrepancy found between a source document and the shipped application or installer — these are real defects and the owner needs them listed, not silently reconciled.
- The nav restructure proposal (§3) for the owner to rule on.
- Which `/learn/` pages you judge to need accountant review before they should stay published, and why.
