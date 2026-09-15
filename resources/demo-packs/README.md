# Original multi-year sample companies

Version 1.0.0 provides service agency, retail shop, seasonal business and distributor companies. All people, entities, amounts and events are synthetic. These fixtures are country-neutral examples; selecting a currency does not activate tax or foreign-exchange accounting.

Each pack contains 74 source records, three editable drafts, 2024–2025 history and an open 2026 practice period. There are 36 month-end checkpoints per company. The default demo limit of 100 source records leaves room for 26 new records; provisioning refuses limits below the history plus 20 practice records.

`catalog.json` pins each file and SHA-256 digest. `python tools/build-demo-packs.py --check` verifies authored fixtures without changing them. Every sample is provisioned through existing account, document, journal, posting, reversal and period services; exact monthly trial balance, P&L and Balance Sheet checkpoints must reconcile before the company is assigned to a visitor. Existing books cannot be replaced.

The 2024 annual period and twelve 2025 monthly periods are closed after reconciliation. The 2026 annual period stays open; January has posted sample activity and February has three editable drafts. The private `/sample-guide` connects daily bank movements, December adjustments, cross-year settlement and separate quarterly/yearly comparisons to scoped sources.

Manual staff, asset, depreciation, prepayment, loan, unpaid-document and stock schedules explain the general-ledger entries. They do not implement payroll, inventory or AR/AP subledgers. POS practice does not deduct stock or calculate cost of sales. Sample closure prevents backdated posting; it does not certify statutory statements or transfer profit automatically to retained earnings.

Public visitors receive isolated sample companies. Their sessions and machine grants expire at the next hourly reset; reused numeric IDs never restore old access. Run migration `012_demo_history_periods` with web and scheduler stopped because its trigger DDL is not transactional. Existing assigned visitors remain prohibited from administering periods.
