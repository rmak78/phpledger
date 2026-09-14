# Product brief

Supporting research: the [Chart of Accounts catalog proposal](coa/README.md) covers business and country variations for the installation wizard; [original business sample packs](sample-data/README.md) provide reviewable scenarios for restaurants, membership clubs, pharmacies, traders, distributors, retail shops, and workshops. These are research and demo artifacts, not installed modules or country-compliance claims.

## Purpose and audience

Help SME owners, accountants, and bookkeepers start useful accounting work quickly, with readable daily workflows backed by traceable double-entry records. Begin with a country-neutral core and one base currency per company. Regional and industry workflows follow demonstrated accounting reliability and user demand.

The product will be **multilingual, with English as its primary language**. Users should read event times in the local timezone of the terminal they are using while the backend keeps authoritative timestamps in UTC. Business accounting dates, including posting, fiscal, and cutover dates, keep the same calendar date everywhere. Translation readiness and local display support do not imply completed translations or country accounting coverage.

Users should be able to choose readable local date and money formats: currency symbol or code and its placement, Western or South Asian digit grouping, decimal/group separators, and permitted display precision. Locale defaults can be adjusted for a user/terminal; official exported documents will later use company defaults. These choices only affect presentation. They do not change stored amounts, currencies, or accounting dates. Localized entry must use an explicit format and explain ambiguous values rather than guessing what a user meant.

The software and all modules are intended to remain open source. Paid services cover defined installation assistance, training, troubleshooting, and support on customer-owned hosting. A supported hosted service, lifetime support, and paid-only product features are not current commitments. Licensing must be resolved before public contribution/funding launch.

## First useful journey

Installation and business onboarding are different journeys. The administrator checks the hosting environment, installs the application, and creates an administrator. An owner or accountant then chooses an isolated sample company or creates a company, enters its name/base currency/accounting start/fiscal year, accepts an account template, and starts fresh or imports records.

The first complete slice is company setup → account template → receipt or expense → balanced journal → trial balance → linked reversal. People should be able to understand why each balance changed and return to the source transaction. Advanced settings are available when needed rather than required up front.

The user has additionally made **POS a showcase product** in Sprint 02. The bounded general-shop journey uses six fictional products, searchable categories, editable cart quantities, exact cash/change calculation, a recorded receipt, and a linked journal. It records the sale through the same accounting services. These illustrative prices and cash declarations are not payment collection, inventory/COGS accounting, tax calculations, credit sales, or restaurant functionality. See [POS showcase](POS.md) for the exact boundary and checks; broader retail workflows remain a future release.

[Sprint 02](SPRINT-02.md) implements this core browser journey with saved receipt/expense drafts and the approved Review Console/Inter design. The broad journey above describes the product destination: historical uploads, full industry/country templates, and opening-document reconciliation are deferred from this sprint. The current release must clearly distinguish new-business setup from an existing business whose opening positions are still unresolved. A disabled or explanatory import path is not a delivered importer.

The current reports hub adds posted profit and loss, balance sheet, cash balance, trial balance, and account/source drilldown. Its **cash forecast** is an explicit scenario using entered weekly cash in/out over 1–52 weeks and today's posted opening cash balance, with a visible negative-balance point. It does not predict unpaid invoices, stock demand, tax, or future sales, and creates no accounting entries. Receivables, payables, and stock reports are important future capabilities tied to their still-unimplemented subledgers and inventory modules. Publication is on hold while the reports/POS undergo the user's requested review and redesign; the [reporting gap analysis](accounting/REPORTING_GAP_ANALYSIS.md) separates today's summaries from the accounting-guideline-based system still needed.

Regional groundwork uses one bounded backend country lookup per browser session, caches success/failure, and leaves manual currency selection available. Private/local addresses skip the lookup. The supported base-currency choices are USD, EUR, GBP, PKR, INR, MYR, BDT, LKR, NPR and SGD; a country hint does not select a tax regime, translate the UI, or convert money. The application remains English with terminal-local event time display and unchanged business dates; full translation and formatting settings remain future work.

For an existing business, opening balances and cutover decisions must be resolved before onboarding says bookkeeping is ready. The user has authorized a public `/demo` for Sprint 02 with separate synthetic storage, a private sample company for each visitor, hourly reset, and server-enforced restrictions on destructive user operations. Local backend isolation/reset checks have passed; browser and deployment evidence are recorded separately. It must remain isolated from customer records and be clearly marked as resettable demonstration data; publication remains pending its actual receipt.

## Historical data import

The first accounting release includes CSV and XLSX templates for accounts, customers/vendors, opening balances, and unpaid invoices/bills. Detailed historical journals, original invoices/bills/payments, and their relationships follow as a separate migration milestone.

The guided flow is **upload → match columns → preview → correct errors → verify totals → confirm**. Show row-specific errors, duplicate candidates, and accounting totals before applying anything. Retain users' column mappings and recoverable edits during the workflow. Do not execute spreadsheet formulas or treat guessed values as verified data.

An explicit cutover date separates opening positions from new transactions. Imported unpaid documents must reconcile to the AR/AP opening control balances without adding those amounts twice. Document totals, trial-balance totals, and accepted/rejected row counts must be visible. Only an authorized confirmation can apply the validated batch; repeated confirmation must not post the same batch again. Batch receipts and source references support later investigation and safe, traceable correction.

Migration history is not live bookkeeping history until posted, and successful file parsing is not accounting approval. Bank-statement imports have a separate preview/matching/confirmation workflow; suggested matches never silently mark transactions reconciled.

## Multicurrency direction

The user has approved a future multicurrency product with **fixed rates, periodic online updates, and permission-controlled manual overrides**. A company retains its base currency while future documents can carry a transaction currency. Each posting must retain the exact rate and source/effective-time snapshot used, so a subsequent rate change cannot alter posted history.

Users must be able to see the rate applied, whether it was fixed/fetched/manual, and when an online rate is stale or a refresh failed. Online refresh runs in the backend; opening a page must not contact a rate provider. Overrides need an actor, reason, audit trail, and traceable effect on reconciliation.

This is a future capability, not an expansion of Sprint 02's base-currency posting. Provider choice, refresh frequency, cross-rate rules, conversion precision/rounding, and gains/revaluation treatment remain unselected and require design/accounting review. See the [architecture](ARCHITECTURE.md) for the distinction between UTC instants, business dates, and future rate snapshots.

## Success measures

| Outcome | Initial target | Evidence required |
|---|---|---|
| Understand the product | Sample experience accessible without registration or installation | Public demo checks after release authorization |
| Set up a simple new company | Within five minutes after first login | Timed observed session, excluding server installation/data migration |
| First useful outcome | Record a transaction and find its report effect within ten minutes | Uncoached task observation |
| Core-task usability | Four of five participants in each initial owner/accountant group succeed | Recorded task outcomes and issues; no assumed results |
| Recover from mistakes | Explain errors and edit drafts without lost work | Error-path browser checks and participant observation |

WCAG 2.2 AA is the accessibility target. Technical checks and observation both contribute; neither alone establishes conformance.

## Deferred capabilities and constraints

Multi-book meaning must be settled in discovery: distinguish separate businesses, branches, and legitimate alternative reporting bases, then specify ownership and reconciliation. Do not implement ambiguous line-level flags from the seed brief. All posted records and book differences must remain explainable and traceable.

Tax filing, country-specific tax calculations, foreign-exchange accounting, asynchronous financial posting, native wrappers, offline sync, inventory, production retail POS, and distribution are later scoped releases. The bounded cash-sale showcase above does not complete those operational modules. The seed idea remains vision input; the approved accounting-first, synchronous, self-hosted direction governs the current milestone.

The user chose one future **Scan document** action, with AI identifying the document type and proposing editable fields for review before normal save/posting. Receipts, invoices, and cheques share that intended entry point; BixiSoft AI is the intended initial extraction service. This work was moved further down the roadmap so the current sprint can finish. It is not promised for the next sprint, and no working scan/upload/AI extraction is claimed.

The later regional path includes country/industry account templates and isolated tax plugins, beginning with separately validated demand; Pakistan and India are candidates from the seed rather than current supported tax regimes. The operational sequence is inventory/purchasing with an explicitly chosen FIFO or weighted-average valuation method, general retail POS, then van warehouses/delivery/collections. Jewelry pricing by weights/materials/labor, pharmacy batches/expiry/prescription needs, and restaurant tableside ordering/kitchen order tickets are distinct later products. Offline and native wrappers require operational evidence, safe synchronization, and accounting reconciliation before release. The [roadmap](ROADMAP.md) retains the detailed future path.

Source provenance: the user's original "Seed Idea Brief: Multi-Book Regional ERP Ecosystem" supplied the regional and modular vision. The user's attached [approved plan snapshot](PLAN.md), originally `PLAN (7).md`, establishes the revised stack, accounting-first stages, UI/UX gates, self-hosted delivery, and funding boundaries. The subsequent historical-import request extends that plan. The snapshot is preserved unchanged; its old review-status paragraph is not current execution evidence.
