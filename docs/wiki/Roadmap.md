# The complete future path

PHP Ledger develops through eight stages. Working preview code and research already support parts of the early stages; that does not mean their user, accounting, funding or release gates are complete. The immediate deliverable is [[the first installable package|First-Package]].

| Stage | What it delivers | Completion gate |
|---|---|---|
| **1. Discovery and restart** | Preserved history, product scope, accounting examples, architecture, licence review and priorities | Owner/accountant decisions and actual research support the scope. |
| **2. Experience and stack proof** | Selected UI direction, onboarding/entry/report prototypes, authentication and database transactions | Runtime checks and observed core journeys support the design. |
| **3. Complete product slice** | Business setup → starter accounts → receipt/expense → journal → trial balance → reversal | Results reconcile and representative users complete the journey without coaching. |
| **4. Website and early validation** | Product website, isolated demonstration, documentation, contributor paths and pilot interest | Accurate claims, working public journeys, accounting reviewers and prospective pilots. |
| **5. Milestone funding** | A costed, measurable next release and verified receiving route | Licence, funding eligibility, budget and delivery capacity are established. |
| **6. Accounting MVP and pilots** | Journals/fiscal controls, AR/AP, cash/bank, starter imports, reconciliation, statements/exports, installation and upgrades | Supported pilots complete an accounting period; reports and recovery reconcile. |
| **7. Multi-book and localization** | An approved book model, traceable differences, reviewed regional templates and tax adapters | Each book balances; differences are explainable; each supported profile is reviewed. |
| **8. ERP expansion** | Inventory/purchasing, production retail POS, van distribution and later specialist releases | Demand, usable operations, funded scope and accounting reconciliation support each release. |

## Required accounting progression

Historical data matters. CSV/XLSX starter imports should cover accounts, contacts, opening balances and unpaid documents through preview, correction, reconciliation and confirmation. Detailed transaction history and source-system adapters form a later migration milestone. AR/AP, bank reconciliation and stock reports need their underlying records, not just report screens.

Country accounting proceeds **Pakistan → UK → UAE**, with explicit entity/period profiles and separately reviewed tax behavior. Multi-book meaning must be approved before implementing cross-book copying or reporting adjustments. Every book and difference must remain traceable.

The current cash forecast is an entered scenario, not a prediction engine. Any future forecast based on invoices, bills, inventory or other operational data must disclose its inputs and assumptions and remain distinct from posted accounting.

## Regional and business expansion

- **Language and formatting:** English fallback, reviewed translations/RTL, terminal-local event display, flexible dates and money presentation without changing stored values.
- **Multicurrency:** fixed/fetched/manual rates, immutable rate snapshots, stale-data visibility and reviewed rounding/revaluation policies.
- **Inventory and purchasing:** products, warehouses, stock movements and a reviewed valuation method tied to COGS and journals.
- **Production retail:** returns, payment reconciliation, taxes, tills/hardware and end-of-day controls beyond the current sample cash sale.
- **Van distribution:** van warehouses, route deliveries, collections and reconciled evening settlement.
- **Specialist releases:** restaurant tables/kitchen tickets; pharmacy batches/expiry; jewelry material/weight/labour pricing; club memberships; workshop jobs/parts/labour. Each needs its own validation.

Original demonstration scenarios cover restaurants, clubs, pharmacies, traders, distributors, shops and workshops. They support research and future onboarding; they do not mean those operational modules are available.

## Later investigations

One **Scan document** action is planned for receipts, invoices and cheques. AI would identify the document and suggest editable fields for human review before the normal save/posting process. It is deferred, with no promise for the next sprint and no current extraction capability.

Offline synchronization, durable asynchronous operational events and native wrappers follow demonstrated need. They must preserve duplicate protection, authoritative posting and visible reconciliation across interruptions.

Each release is scoped and funded separately. A roadmap entry is not a delivery date, funding commitment or claim of regulatory support.

[[First package|First-Package]] · [[Accounting and reports|Accounting-and-Reports]] · [[Contributing and support|Contributing-and-Support]]
