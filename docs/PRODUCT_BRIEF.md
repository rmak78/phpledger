# Product brief

Supporting research: the [Chart of Accounts catalog proposal](coa/README.md) covers business and country variations for the installation wizard; [original business sample packs](sample-data/README.md) provide reviewable scenarios for restaurants, membership clubs, pharmacies, traders, distributors, retail shops, and workshops. These are research and demo artifacts, not installed modules or country-compliance claims.

## Purpose and audience

Owner clarification, 15 September 2026: PHP Ledger serves **small businesses, owners, bookkeepers, accountants and multi-company operators across countries**. The reusable accounting core is country-neutral. Pakistan is one intended regional direction, not the main audience or ultimate product direction. Sales-tax-registered SMEs, sole traders, AOPs and Pvt Ltd companies are examples within that regional research; they do not define global eligibility or exclude businesses elsewhere. Daily entry should work well on phones, with clear desktop review and explicit per-client company/book permissions. Country-specific accounting and tax capabilities require their own supported profiles and connectors.

The headline reporting direction is **real-time owner's equity**. For AOPs, the planned scope includes partners' capital accounts, approved profit-sharing ratios and drawings with traceable movements. Current reports show recorded equity and accumulated earnings; this decision does not implement partner accounts, ratios, allocations or a reviewed equity statement. Classification and allocation policies require worked cases and qualified accounting review.

The first delivery layer includes **Urdu first, then Arabic, with RTL layout support and English as the fallback catalog**. These are planned capabilities; the current application remains English. Store event instants in UTC and display them in the terminal's timezone. Business accounting dates keep the same calendar date everywhere. Locale selection never activates a tax or reporting profile.

Offline means **queued entry only, never offline posting**. Planned browser clients retain visibly unposted drafts in browser storage and submit when connected. Planned native desktop and Android clients use periodic sync over low-bandwidth connections. The server alone validates and posts, controls periods and performs reversals; a pending client entry is not a receipt or accepted ledger record. See the architecture's queued-entry contract.

Regional research and connector candidates include Pakistan, the UK, UAE, Saudi Arabia, Oman, Singapore, Malaysia, Sri Lanka and Bangladesh. Each requires current research, supported entity scope and accounting/tax review. Pakistan FBR is one planned connector within this architecture. Regional delivery follows validated demand and connector readiness; a Pakistan-first launch is not a dependency of the country-neutral core. See the [owner clarification](strategy/PRODUCT-DIRECTION-CLARIFICATION-2026-09-15.md).

Readable local date and money formats remain planned: currency code/symbol placement, Western or South Asian digit grouping, separators and supported precision. These settings affect presentation, not stored exact amounts, currencies or business dates. Localised entry must use an explicit format and explain ambiguity rather than guess.

The accounting core remains open source under AGPL-3.0-or-later; a commercial licence and optional paid services are available. Published 0.1.0 through 0.1.5 releases retain MIT. Current and future module terms follow [Licensing policy](LICENSING-POLICY.md), including advance declaration of any future commercial module. Native desktop/Android clients are planned paid add-ons, not shipped products. Self-hosting one's own business stays free, without licence keys or calls to a licensing server.

## First useful journey

Installation and business onboarding are different journeys. The administrator checks the hosting environment, installs the application, and creates an administrator. An owner or accountant then chooses an isolated sample company or creates a company, enters its name/base currency/accounting start/fiscal year, accepts an account template, and starts fresh or imports records.

The first complete slice is company setup → account template → receipt or expense → balanced journal → trial balance → linked reversal. People should be able to understand why each balance changed and return to the source transaction. Advanced settings are available when needed rather than required up front.

The user has additionally made **POS a showcase product** in Sprint 02. The bounded general-shop journey uses six fictional products, searchable categories, editable cart quantities, exact cash/change calculation, a recorded receipt, and a linked journal. It records the sale through the same accounting services. These illustrative prices and cash declarations are not payment collection, inventory/COGS accounting, tax calculations, credit sales, or restaurant functionality. See [POS showcase](POS.md) for the exact boundary and checks; broader retail workflows remain a future release.

[Sprint 02](SPRINT-02.md) implements this core browser journey with saved receipt/expense drafts and the approved Review Console/Inter design. The broad journey above describes the product destination: historical uploads, full industry/country templates, and opening-document reconciliation are deferred from this sprint. The current release must clearly distinguish new-business setup from an existing business whose opening positions are still unresolved. A disabled or explanatory import path is not a delivered importer.

The current reports hub adds posted profit and loss, balance sheet, cash balance, trial balance, and account/source drilldown. Its **cash forecast** is an explicit scenario using entered weekly cash in/out over 1–52 weeks and today's posted opening cash balance, with a visible negative-balance point. It does not predict unpaid invoices, stock demand, tax, or future sales, and creates no accounting entries. Receivables, payables, and stock reports are important future capabilities tied to their still-unimplemented subledgers and inventory modules. The public 0.1.5-preview has its own release evidence; later integrations and reporting work retain their acceptance gates; the [reporting gap analysis](accounting/REPORTING_GAP_ANALYSIS.md) separates today's summaries from the accounting-guideline-based system still needed.

Regional groundwork uses one bounded backend country lookup per browser session, caches success/failure, and leaves manual currency selection available. Private/local addresses skip the lookup. The supported base-currency choices are USD, EUR, GBP, PKR, INR, MYR, BDT, LKR, NPR and SGD; a country hint does not select a tax regime, translate the UI, or convert money. The application remains English with terminal-local event time display and unchanged business dates; full translation and formatting settings remain future work.

For an existing business, opening balances and cutover decisions must be resolved before onboarding says bookkeeping is ready. The user has authorized a public `/demo` for Sprint 02 with separate synthetic storage, a private sample company for each visitor, hourly reset, and server-enforced restrictions on destructive user operations. Local backend isolation/reset checks have passed; browser and deployment evidence are recorded separately. It must remain isolated from customer records and be clearly marked as resettable demonstration data; publication remains pending its actual receipt.

## Historical data import

The planned import destination includes CSV and XLSX templates for accounts, customers/vendors, opening balances and unpaid invoices/bills. The current core has scoped CSV exports, opening trial-balance/CSV cutover and a reconciled unpaid-document register; XLSX and full customer/vendor subledgers remain unimplemented. Detailed historical journals, original invoices/bills/payments, and their relationships follow as a separate migration milestone.

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

Tax filing, reviewed country-specific calculations, foreign-exchange accounting, inventory, production retail POS and later distribution retain separate implementation gates. Native desktop/Android clients remain planned paid add-ons over the API/MCP layer. Offline entry/sync is planned, but posting, period control and reversal always execute on the server. The bounded cash-sale showcase above does not complete those operational modules. The seed idea remains vision input; the approved accounting-first, synchronous, self-hosted direction governs the current milestone.

The user chose one future **Scan document** action, with AI identifying the document type and proposing editable fields for review before normal save/posting. Receipts, invoices, and cheques share that intended entry point; BixiSoft AI is the intended initial extraction service. This work was moved further down the roadmap so the current sprint can finish. It is not promised for the next sprint, and no working scan/upload/AI extraction is claimed.

The approved operational order is API/MCP reads, AR, AP, distribution/updater tooling, the regional tax/e-invoicing connector framework (including an optional Pakistan FBR connector), purchasing/inventory, shop POS, e-commerce/storefront, controlled API/MCP commands, restaurant, distribution/van operations and specialist modules. POS and e-commerce are expected commercial drivers, with no proprietary module declared by this document. The “installer builds the customer's website” idea is parked; an e-commerce/storefront module is a separate planned product. Restaurant, distribution and specialist verticals remain later modules, rather than being removed from the roadmap. [Module roadmap](MODULE-ROADMAP.md) defines dependency and acceptance gates.

One planned connector, for Pakistan, includes an FBR digital-invoicing client, sandbox scenarios SN001–SN028, protected token storage, HS/UoM reference sync, QR/logo invoice output, the applicable 72-hour edit-window workflow, debit/credit notes and Annex-A/C/I reconciliation from ledger and DI responses. Shipping an open-source client is separate from acting as an integrator of record; the product plan uses PRAL's free integration route and does not depend on BixiSoft first obtaining an integrator licence. Live transmission still requires configuration through a valid licensed integrator, including PRAL's stated free route. [FBR DI FAQs 6 and 12](https://www.fbr.gov.pk/faqs/173967/173969), checked 15 September 2026. Current API/rule verification and qualified review precede production acceptance.

Source provenance: the user's original "Seed Idea Brief: Multi-Book Regional ERP Ecosystem" supplied the regional and modular vision. The user's attached [approved plan snapshot](PLAN.md), originally `PLAN (7).md`, establishes the revised stack, accounting-first stages, UI/UX gates, self-hosted delivery, and funding boundaries. The subsequent historical-import request extends that plan. The snapshot is preserved unchanged; its old review-status paragraph is not current execution evidence.
