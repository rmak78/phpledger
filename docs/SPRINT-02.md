# Sprint 02: first working browser journey

Execution scope authorized on **14 September 2026**. The user approved **direction 6, Review Console, with Inter** and requested parallel work on repository details, the website, and the next product phase. Work is implemented and checked locally first. The user subsequently explicitly authorized **GitHub repository information updates and marketing website publication at the end of the sprint, after validation**, then selected **website composition 1, Field Notes**, and added a **public `/demo` with hourly reset and no destructive user operations**. The website/demo and repository/Wiki publication were completed after renewed authorization; see the appended publication receipt. The original [approved plan snapshot](PLAN.md) remains unchanged and the [roadmap](ROADMAP.md) retains the full future path.

## Outcome and scope

Build a real browser journey from login and essential company onboarding through a saved receipt/expense draft, balanced posting, a trial balance linked to its source, and a traceable reversal. The user later added a **working general-shop POS showcase**: sample catalog → cart → cash checkout → printable receipt → source journal. Reuse the existing PHP 8.5/MySQL 8.4, BixiSoft, MeekroDB, authentication, permission, and posting foundation. A screenshot or simulated save is not completion of either journey.

The expanded current reports scope includes a reports hub, posted profit and loss, balance sheet, cash balance, trial balance/account drilldown, and a cash scenario driven by explicit weekly inflow/outflow assumptions. The scenario writes no accounting entries and does not predict unpaid invoices or stock movements. Receivables, payables, and stock reports are planned with the still-future AR/AP and inventory modules, including on the marketing website.

The initial accounting scope stays country-neutral with one primary book and a base currency per company. Starter accounts must be explicit; researched country/industry charts and synthetic operational packs do not become approved runtime templates merely by appearing in a setup screen. Real-company setup must never silently load sample records.

**Historical imports are deferred from this sprint.** Keep their future upload/mapping/preview/validation/confirmation and cutover-reconciliation requirements in the roadmap. Do not present an existing business as ready for live bookkeeping while opening positions or unpaid-document reconciliation remain unresolved. Multi-book behavior, AR/AP, inventory and production retail workflows, tax adapters, bank reconciliation, customer branding uploads, and funding collection remain later milestones. The current [POS showcase](POS.md) records a bounded cash sale using six illustrative products; it does not implement stock, COGS, tax, discounts, credit sales, actual payments, or restaurant operations. The unified **Scan document** flow (AI type detection and editable suggestions for receipts/invoices/cheques) is deferred further down the roadmap, **not promised for the next sprint**.

### Additional product decisions recorded during the sprint

- **Multilingual, English primary:** externalized interface catalogs with English fallback, UTF-8 throughout, and a path to reviewed translations and future right-to-left layouts. The current interface is English; complete catalogs and additional languages await reviewed translations/fonts/screens.
- **UTC backend, local terminal display:** event instants and audit timestamps are stored in UTC. The current JavaScript presents journal instants in the browser terminal's IANA timezone with English labels. Accounting posting/fiscal/cutover DATE values are never timezone-shifted. Full terminal/locale preference settings remain future work.
- **Flexible date and money presentation:** locale defaults with user/terminal overrides for date format, currency symbol/code and placement, Western or South Asian grouping, decimal/group separators, and permitted display precision. Company defaults will later control official exported documents. Formatting cannot alter stored exact amounts, currencies, or business DATE values. Localized entry needs explicit parsing and must reject ambiguous dates/numbers rather than guessing. Current English date labels, Western grouping, and canonical decimal entry are implemented; customizable formatting/localized entry remain future work.
- **Future multicurrency:** support fixed rates, periodic online updates, and manual overrides. Retain company/base currency and a separate transaction currency, with immutable per-posting rate/source/effective-time snapshots. Fetch online rates through scheduled backend work, display last-known-good/stale status, and audit permission-controlled overrides. Provider, cadence, cross-rate/rounding policy, and gains/revaluation rules are future design decisions. Sprint 02 retains one base currency per book and does not claim foreign-currency posting.

These are durable user decisions, not test results or an instruction to implement every future capability during this sprint. The [architecture](ARCHITECTURE.md) and [roadmap](ROADMAP.md) retain the full behavior and accounting boundaries; historical import delivery follows the current slice.

**Current implementation checkpoint:** the interface remains English; journal event times use the terminal timezone and unchanged accounting dates use date-only presentation. A once-per-session backend country lookup caches success/failure, skips local/private addresses, and suggests only a supported base currency with manual choice. Full English translation catalogs, additional languages, flexible presentation settings, localized entry, and multicurrency posting remain future work. Country detection is not a compliance or language-support claim.

## Parallel ownership

| Workstream | Owner | Deliverable and boundary |
|---|---|---|
| Product implementation and integration | Technical lead and product foundation agent | Browser authentication, company onboarding, draft receipts/expenses, posting, trial balance, reversal, and relevant migration/access/accounting tests; reuse the current application and internal interfaces |
| General-shop POS showcase | POS implementation agent, integrated by the lead | Six-product searchable catalog, exact cart/cash checkout, immutable line/price receipt snapshot, source/journal links, printable receipt, and financial/tamper/isolation/concurrency/rollback tests |
| Website | Website agent, reviewed by the lead | Three visual compositions prepared; user-selected composition 1, Field Notes, proceeds into a responsive website with real product walkthroughs and international SME imagery, accurate capabilities, roadmap, installation/support/contribution paths, and authorized contact information |
| Public demo | Product foundation agent and technical lead | Separate synthetic demo database, server-enforced restricted operations, hourly controlled reset, base-path-safe `/demo` routes/assets, and deployment/isolation/reset evidence |
| Repository and documentation | Repository documentation agent | Current README and sprint/roadmap status, contributor guidance, issue/PR templates, architecture/product/funding/design updates, and a dated evidence receipt |
| Final verification and authorized publication | Technical lead | Review all work together, run checks warranted by the changes, verify browser journeys, then own reviewed GitHub updates, website backup/deployment, and release verification; distinguish technical results from pending observed usability and accounting sign-off |

The documentation agent owns the central documents listed above. Implementation agents supply their final route, command, migration, permission, and test details before documentation claims those behaviors are verified. Avoid overlapping edits and preserve existing uncommitted work.

## Website and identity direction

Use the approved book/P identity, navy `#0C2052`, charcoal `#424242`, and Inter. Public pages should explain the business value with a genuine product walkthrough and compelling, properly sourced photographs of varied businesses. Record asset sources and permissions; do not imply photographed people are customers or invent testimonials, adoption counts, certifications, or funding results. The three [website compositions](design/website/README.md) were prepared before code and the user selected **1, Field Notes**. The [photography record](design/website/SOURCES.md) identifies the real source images and their licensing/provenance limits.

The authorized contact details are `rmak78@gmail.com`, `[public phone removed]`, and BixiSoft, Office M5, First Floor, Innovista Chenab Arcade Plaza, Sector C, DHA Multan, Punjab 60000, Pakistan. The supporting partners named for the website are **BixiTech, BixiSoft, and Agency75**. Their placement must not imply unverified customer endorsements or certification. Contact information does not authorize sending a message.

Build and validate locally first. Marketing website publication and the later requested `/demo` are explicitly authorized for the sprint's end; both remain previews until deployment and live verification are recorded. The demo must show its synthetic/resettable nature, use a separate database, block destructive user operations on the server, and reset automatically each hour through a controlled backend process. A reset must never target a customer's or the main development database. Scope all internal routes, links, forms, and assets correctly beneath `/demo`. The [demo operations runbook](DEMO.md) records the current Compose configuration, private environment boundary, proxy contract, hourly UTC schedule, and deployment/rollback pattern.

Do not advertise a working hosted demo until it is deployed and checked, or claim completed import/ERP features, a settled software license, or funding collection before those capabilities and gates exist. Forms must not claim delivery or enrollment without implemented and verified behavior. Public demo authorization does not authorize exposing real/customer books or an unrelated application domain.

## Acceptance and current evidence

| Check | Required evidence | Current status |
|---|---|---|
| Application journey | Browser login → company setup → saved draft → balanced posting → linked report → linked reversal | Implemented; local HTTP acceptance passed 26 checks in an isolated synthetic company; browser integration review remains pending |
| POS showcase | Search/filter → cart → explicit cash checkout → receipt/source/journal; exact prices, duplicate/rollback/isolation checks, keyboard/mobile/print | Eight backend cases passed in the 54-test suite; latest POS HTTP passed 21 checks, including explicit-intent denial; user-requested redesign and browser/print review remain pending |
| Reports and cash scenario | Scoped posted P&L/balance sheet/cash/trial balance, linked records, reversals, explicit assumptions and no forecast writes | Implemented; backend cases passed in the combined suite; final browser presentation review pending |
| Security and accounting | Server-side membership/action checks, CSRF, escaped output, exact amounts, duplicate prevention, rollback, closed-period rejection, and posted immutability | Combined target-runtime suite passed 54 tests; latest core/POS HTTP checkpoints passed 26 and 21 checks; dependency/static/lint results recorded separately |
| UI behavior | Desktop/tablet/mobile, keyboard/focus, empty/error/saved/posted states, retained form values, intentional list/detail navigation | Working-browser checks pending |
| Language/time/formatting groundwork | English fallback, UTF-8, terminal-zone timestamp formatting, unchanged accounting DATE values, and any implemented preference/parser checks | English UI/local-time groundwork and bounded regional hints implemented; regional behavior tested with mock transport; translations/full settings/parsing remain future |
| Installation and migration | Fresh installation/replay and any new migration checks; no legacy SQL or customer data | Original 001 → 002–005 preservation/replay/UTC proof passed; restore passed 15 tables, 6,069 synthetic rows, 9 triggers, and all five receipts |
| Website | Selected composition 1 after three visual candidates; responsive navigation, truthful product/contact/roadmap paths, verified asset provenance | Composition 1 published; responsive browser and hosted asset checks passed; observed usability remains pending |
| Public demo | Separate synthetic database, private company per visitor, `/demo` base-path behavior, server rejection of destructive user actions, hourly reset/recovery and customer-database isolation | Local restricted-user/isolation/real-reset smoke passed; public browser, schedule and deployment receipts pending |
| Repository | Linked contributor guide, valid issue/PR templates, accurate current status, unchanged historical plan and validation receipt | Documentation/templates prepared and locally checked; final integration status awaits implementation evidence |
| Authorized publication | Reviewed GitHub information, marketing website and restricted `/demo` after sprint checks, backup/recovery preparation, and live verification | Published as a development preview after renewed owner authorization; first-package report/POS refinement remains in Sprint 03 |
| Observed usability and accounting sign-off | Representative users and accounting reviewers evaluate the working journey | Pending; not replaced by automated tests |

Record commands, exact results, changed routes, migration/schema effects, and known gaps in the appended Sprint 02 section of [Validation](VALIDATION.md). This scope document is not a passing test receipt.

## Authorized publication and current status

**Published development preview, 14 September 2026:** the user explicitly renewed the instruction to replace the existing website, update the demo, publish the README/logo and use GitHub Wiki. That instruction superseded the earlier publication hold. The website and restricted demo are now live; the README/assets and ten Wiki pages are published. Reports/POS refinement and independent accounting/usability review remain requirements for the first installable package, tracked in [Sprint 03](https://github.com/rmak78/phpledger/milestone/4). See the appended publication receipt for the checks and limits.

The inspected local branch is `revival/foundation`, based on legacy commit `fe528eb52a8be277f3b2806022d23a817c01bda8`. The foundation, research, and design work were still uncommitted at sprint start. Preserve that work and include untracked files in review. The technical lead owns the reviewed commits/remote actions necessary for the authorized GitHub information update and website publication; the documentation agent does not commit, push, deploy, or change the default branch independently.

The user's request, "I want github repo update with info and website made live at end of sprint please", superseded the earlier local-preview-only publication boundary for **GitHub repository information and the marketing website**. A subsequent user decision additionally authorized the **restricted synthetic `/demo` with hourly reset**. Complete sprint validation and backup/recovery preparation, perform those authorized actions, and record their actual outcomes. No additional publication permission is pending for this scope. Exposing real/customer books, unrelated domains, funding collection, provider changes, and external messages remain outside it.

Historical preparation snapshot: GitHub metadata was initially read on 14 September 2026 with default branch `master`, description "Accounting System General Ledger in PHP MySQL", homepage `http://www.phpledger.com/`, no topics, and no detected project license. The proposal below was not applied at that point; the later metadata receipt records the actual published values:

- Proposed description: **Self-hosted accounting and a sample cash POS for small businesses. PHP 8.5, MySQL 8.4, and a modern web interface. Revival in development.**
- Proposed topics: `php`, `accounting`, `bookkeeping`, `general-ledger`, `point-of-sale`, `mysql`, `self-hosted`, `small-business`.
- Homepage: confirm the real HTTPS destination during an authorized relaunch before changing it.

The [license/provenance review](LICENSE_REVIEW.md) remains open. The authorized repository/website information must state that status accurately; do not invent a project license, SPDX declaration, CLA/DCO, stable-release badge, support commitment, or funding eligibility. Website and GitHub publication authorization does not resolve software distribution rights or authorize outreach and payments.

The local publication-candidate review found two Python bytecode cache files; the lead added ignore coverage before staging. Private environment files, dependencies, app storage, and output directories are excluded. A bounded scan of changed/untracked text files found no private-key, common provider/GitHub token, or credential-URL patterns; literal password assignments inspected in tests were synthetic fixtures. This scan does not audit every historical commit or establish a general secret-free certification. The design/photo assets are intentional reference artifacts with recorded provenance, not generated runtime caches. Code staging/commit/push and website/demo publication remain pending the current redesign work; the later About-metadata update below is complete.

### GitHub About metadata applied — 14 September 2026

The user subsequently asked why the repository metadata was unchanged. The lead applied the authorized metadata update and immediately read it back from GitHub for `rmak78/phpledger`:

- Description: **PHP Ledger revival: self-hosted accounting and POS for small businesses, targeting PHP 8.5 and MySQL 8.4. In development.**
- Homepage: **https://phpledger.com/**. The existing HTTPS site returned HTTP 200 before the link was changed; this is not evidence that the new website was deployed.
- Topics: `php`, `accounting`, `bookkeeping`, `general-ledger`, `point-of-sale`, `mysql`, `self-hosted`, `small-business`.

The verified default branch is still `master`. No branch, license, repository file or README was published by this metadata operation; the local revival remains uncommitted. This live GitHub About change supersedes the earlier un-applied metadata draft above. Website/demo code publication is a separate outstanding outcome.
