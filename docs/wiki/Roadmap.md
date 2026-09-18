## Current package: 1.0.0

**1.0.0**, published 18 September 2026, is the first stable release, consolidating the 0.6.1 workflow-recovery closure, the 0.7 browser installer and the 0.8 signed update/automatic-backup/recovery work. [Download 1.0.0](https://github.com/rmak78/phpledger/releases/tag/v1.0.0) and its [matching media kit](https://github.com/rmak78/phpledger/releases/download/v1.0.0/phpledger-1.0.0-media-kit.zip). Independent accounting review, independent security review, supervised pilots with a real month-end close and unfamiliar-operator installation observation have **not** happened; they continue as post-release commitments. See [[Release 1.0.0|Release-1.0.0]].

# The path after 1.0.0

With 1.0.0 published, next is **1.0.x** production fixes and compatibility improvements, **1.1** reviewed Urdu/RTL plus installer distribution channels (Softaculous/Installatron, published containers/packages), and **1.2** reviewed Arabic/RTL and demand-led reporting refinements. Independent accounting/security review and supervised pilots run in parallel as post-release commitments rather than as pre-publication gates. Later capability releases add reviewed regional connectors, stock/tax-integrated shop POS, e-commerce, controlled API/MCP writes and restaurant/distribution/specialist modules, each with its own independent acceptance. See [[Module roadmap|Module-Roadmap]] for current gates; older milestone snapshots below retain their historical scope.

The direction confirmed on 15 September 2026 is **complete accounting core first, optional business modules next**, with a business API and MCP access over the same services. The current preview supplies working slices; it does not complete the accounting, user or release gates below.

The 0.1.4-preview release completes the bounded core implementation and bundled module lifecycle technical checks: 121 tests pass, with a two-period reconciliation, exports and owner-controlled POS enablement. API/MCP reads are the next implementation milestone, followed by commands and optional AR/AP. Qualified reviews and observed pilot use remain separate gates.

The [[module roadmap|Module-Roadmap]] gives the detailed delivery order. [[First package|First-Package]] describes the 0.1.4-preview scope and remaining acceptance work.

| Stage | What it delivers | Completion gate |
|---|---|---|
| **1. Discovery and restart** | Preserved history, product scope, accounting examples, architecture, licence review and priorities | Owner/accountant decisions and source research support the scope. |
| **2. Experience and stack proof** | Selected design, onboarding/entry/report prototypes, authentication and transactions | Runtime checks and observed journeys support the design. |
| **3. Complete product slice** | Business setup, accounts, receipt/expense, journal, trial balance and reversal | Results reconcile and representative users complete the journey. |
| **4. Website and validation** | Website, isolated demo, documentation, contributor paths and pilot interest | Accurate claims and working public journeys, with reviewers and prospective pilots. |
| **5. Milestone funding** | A costed next release and verified receiving route | Licence, eligibility, budget and delivery capacity are established. |
| **6. Complete accounting core and pilots** | Statements, chart, general journals, opening/cutover, periods, cash/bank reconciliation, reports/exports and recovery | A supported core-only business completes a period; accounting review and observed usability pass. |
| **7. Extension and integration foundation** | Module/dependency contracts, company capability gates, shared master data, business API and MCP | Scoped clients, consistent results, retained history and idempotent audited commands. |
| **8. Optional business modules** | AR, AP, purchasing/inventory, tax, shop and restaurant POS, distribution and specialists | Each module passes its own accounting and operational gates. |
| **9. Further regional and multi-book support** | Reviewed profiles/adapters, approved alternative-book model, translations and multicurrency | Defined entity/period support; every book and difference reconciles. |

## Required accounting progression

Account statements, chart management and general journals are the current core slice. Reviewed opening entries, period-close/reopen administration and bank reconciliation come next. Historical import must preview mappings, errors and totals before confirmation, with a cutover that does not double count existing balances.

AR/AP open-item records and stock valuation belong to their future modules. An account statement alone cannot provide aging, outstanding invoices/bills or stock reports. Unexplained opening controls must keep readiness unresolved.

Financial reporting uses explicit, reviewed regional entity/period profiles over a country-neutral core. Pakistan FBR is one planned connector; the product is not defined by one country. Tax research runs alongside it, covers eight countries and seven industries, and remains disabled and unreviewed. [[Tax research|Tax-Research]] explains that separate boundary.

## Optional business expansion

- **AR and AP:** invoices/bills, credit notes, receipts/payments, allocations, aging and reconciled unpaid-document imports.
- **Purchasing and inventory:** products, receiving/returns, warehouses, quantities, reviewed valuation and COGS.
- **Tax:** reviewed jurisdiction adapters, effective rules and immutable calculation snapshots; required before applicable production use.
- **Shop and restaurant POS:** shared checkout, shop entry or table/order/kitchen operations, returns and settlement controls.
- **Distribution and specialists:** route/van stock and collections; pharmacy batch/expiry; jewelry pricing; membership dues; workshop jobs/parts/labour.

The bundled core/POS lifecycle is implemented. API/MCP access remains the next implementation milestone. Optional software does not make legal obligations optional.

## Later investigations

Reviewed translations/RTL and number/date preferences must preserve stored amounts and business dates. Multicurrency needs immutable rate snapshots and reviewed rounding/revaluation. Alternative books require an approved meaning and reconciliation rules.

One **Scan document** action is planned to suggest editable fields from receipts, invoices and cheques for review before the normal save/post flow. It has no current extraction capability or next-sprint promise.

Offline synchronization, durable background events and native wrappers follow demonstrated need, with duplicate protection, recovery and authoritative posting.

Each release is scoped separately. Roadmap entries are not delivery dates, funding commitments or regulatory support claims.

[[Module roadmap|Module-Roadmap]] · [[First package|First-Package]] · [[Contributing and support|Contributing-and-Support]]

## Combined 0.2.1 preview

The owner approved combining read API/MCP, OAuth/Connections and server-side tables with four reconciled businesses, 2024–2025 history, open 2026 practice and reporting guides. The prior staged sequence is superseded. Existing accounting and POS capabilities remain included.

Each named client's compatibility requires its own executed connection and report checks. The verified-client preview can ship with unavailable clients explicitly pending. Protocol support or an OpenAPI fallback does not close those gates. The product remains country-neutral; Pakistan FBR is one planned regional connector. Next: AR, AP, distribution/updater tooling, reviewed regional connectors, inventory, shop POS, e-commerce and controlled commands.
