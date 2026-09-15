# PHP Ledger {{VERSION}} — release notes

Source revision: `{{SOURCE_COMMIT}}`.

This package is a development preview of the restarted PHP Ledger application. It preserves the lightweight BixiSoft PHP/MeekroDB structure while separating accounting functions, server permissions, templates and the public front controller. It is intended for evaluation and supported pilot preparation.

## 0.1.6-preview changes

- PHP 8.2 minimum, PHP 8.3 recommended/default deployment; compatible dependency resolution, accurate runtime/preflight checks and an 8.2/8.3/8.4 CI matrix.
- AGPL-3.0-or-later core licensing, commercial licence available, individual/entity CLA and a pinned CLA Assistant workflow. The pre-adoption 0.1.0 through 0.1.5 releases retain MIT; third-party terms remain unchanged.
- Country-neutral product positioning: Pakistan is one intended regional direction and FBR is one planned connector, not the definition of the core or a global launch requirement.
- Owner-authored accessible POS labels preserve the contributor/revert history. The eleven migrations and accounting schema are unchanged. No API/MCP transport, new connector or richer demo pack is included in this maintenance package.

## Earlier 0.1.5-preview changes

- Product-specific accessible names for the no-JavaScript POS quantity inputs, contributed by [Nagulanvelu in PR #65](https://github.com/rmak78/phpledger/pull/65).
- A maintainer follow-up preserves the final newline and prevents product cards from clipping the fallback quantity fields at desktop, tablet and mobile widths.
- The accounting services, migration chain and stored amounts retain the 0.1.4-preview behavior. The website adds About, Privacy, demo-use terms and product FAQs; website content is deployed separately and is not part of this ZIP.
- API/MCP read access and richer multi-year samples are subsequent releases with separate acceptance gates.

## Earlier 0.1.3-preview changes

- Consolidates the released accounts/general-journals slice with reviewed opening/CSV cutover, reasoned period administration and bank reconciliation/cancellation.
- Adds CSV exports for trial balance, account statements, profit and loss and balance sheet. Amounts remain exact, scope/dates/readiness are explicit, spreadsheet-formula text is escaped, and account exports reject more than 10,000 movements.
- Adds the bundled core/POS manifest contract and owner-only **Modules** screen. Optional POS defaults off for ordinary new/upgraded companies. Explicit synthetic sample provisioning enables it. Service checks block new review/checkout/retry after disablement; old sources and receipts remain available.
- `010_module_lifecycle` adds two tables and two immutable-audit triggers. The complete supplied chain has eleven migration identities and 35 guard triggers. Both original `006_*` files retain their original checksums; numeric prefixes alone are not migration identities.
- Repairs UTF-8 handling in the repository's Windows restore verifier. Technical completion does not establish accounting/pilot certification.

## Earlier 0.1.2-preview changes

- Account statements show opening balance, period debits/credits, running balance and closing balance for all five account classes.
- The chart supports new accounts and audited name/status changes with stable account IDs. Existing account code, type and role remain fixed; changing classification needs a reviewed mapping/correction process that this preview does not supply.
- General journals have saved drafts, review before posting, exact balanced posting and linked dated reversals. Source records and audit history retain the connection to posted entries; posted entries remain immutable.
- Migration `006` adds two tables, account revision/creation metadata and four triggers. The full installation has six migrations and thirteen triggers. Existing migration files and production dependencies are retained.
- Eight country tax research catalogs are packaged as disabled, unreviewed candidates, with an optional standalone structural validator.

The 0.1.1-preview cash POS interactions remain included: click-to-add products, retained validation inputs and separate cart review/cash confirmation.

## Working scope

- PHP 8.2+ (8.3 recommended), MySQL 8.4, pinned production dependencies and a versioned migration runner.
- Command-line prerequisite checks, schema installation and initial-user creation; private database configuration and HTTPS sessions.
- Company setup with an account template, new-business readiness rules and opt-in synthetic sample data.
- Company/book-scoped receipts and expenses, drafts, atomic balanced posting, fixed-precision amounts, duplicate protection, linked sources, period locking and reversals.
- Company/book-scoped chart management, general-journal drafts and audit history, plus statements for asset, liability, equity, income and expense accounts.
- Owner overview, trial balance, balance sheet, profit and loss, and an editable cash scenario. A scenario reflects entered assumptions; it is not a prediction or a statement of cash flows.
- An illustrative cash POS with click-to-add product tiles, cart plus/minus controls, server-priced review, separate cash confirmation, exact amount/change, receipt and linked accounting entry.
- Opening balance entry/CSV preview and confirmation, an unpaid-document register reconciled to AR/AP control balances, and owner correction before business activity.
- Period creation, reasoned close/reopen actions and immutable audit receipts; bank CSV import, explicit matching, outstanding entries and protected completed reconciliations.
- English screens, supported base-currency choices, regional date/number formatting and local terminal timezone display with UTC event storage.

## Known limits

The reports are country-neutral management views. Accounting/reporting research does not amount to ICAP, ICMAP or ACCA approval, statutory presentation compliance, tax certification or filing support. Obtain appropriate accounting review before relying on this preview for a business period.

The POS is a cash-sale demonstration of the posting foundation. Stock quantities, cost of goods sold, tax calculations, credit accounts, payment-provider capture, hardware integrations and complete retail/restaurant workflows are not implemented. The sample catalog is illustrative.

Each book has one base currency. Currency choices and formatting do not implement foreign exchange, multi-currency transactions, consolidation or a completed multi-book model. English remains the application language; country metadata does not provide translated screens or localized accounting rules.

Detailed historical journal imports, XLSX, customer invoicing and invoice collection, bill settlement, aging, inventory/stock reports, country tax adapters, offline operation and receipt/document scanning remain future work. Opening AR/AP is a reconciled cutover snapshot, not an operational subledger. Credit notes, advance balances and unresolved prior bank outstanding items require a separately reviewed workflow. Existing-business setup remains blocked until opening balances are explicitly confirmed; new transactions must be dated after cutover.

The `resources/tax/` catalogs cover Pakistan, the UK, UAE, Malaysia, Bangladesh, Sri Lanka, Nepal and Singapore, with classification questions for restaurants, membership clubs, pharmacies, traders, distributors, retail shops and workshops. Every candidate remains `research_only`, `enabled: false` and `unreviewed`. Product/service, registration, jurisdiction, effective-period and recovery conditions require qualified review; an industry name never selects a universal rate. Null rates mean unresolved or non-flat treatment, not zero tax. No catalog is imported into company settings or used by POS, and no tax activation, filing, public business API or MCP interface is included. The separate bundled core/POS module lifecycle is included.

Optional `php tools/validate-tax-catalog.php --self-test` validates local schema, disabled states and references without database access. It neither checks legal accuracy nor approves the sources, rates or dates. Approved tax profiles, effective-period snapshots and their separate accounting mappings remain future module work.

The selected UI is a working preview. The click-to-add POS direction is owner-approved; representative cashier sessions and country-specific report review remain pending. Passing technical checks does not establish observed usability success, independent accounting acceptance or a completed security review.

## Installation and compatibility

Use [INSTALL.md](INSTALL.md) for the current CLI installation. Only `www/phpledger/public` may be served. Dependencies and the complete versioned migration chain are included; the historical root application, development tools, marketing website and hosted-demo scheduler are excluded.

The package has no automatic upgrade from the historical PHP Ledger database. A recognized prior modern installation retains its applied migration identities/checksums and applies only the remaining supplied chain, including both distinct `006_*` files as needed. Use the documented backup, preflight and controlled replacement procedure. MySQL schema changes are not rolled back as one application transaction. See [UPGRADE.md](UPGRADE.md) for maintenance and restoration requirements. Keep release test results and deployment-specific validation records separate from these feature notes; this document does not claim that your host or data has passed acceptance.

## Feedback and next steps

Use the [issue tracker](https://github.com/rmak78/phpledger/issues) for reproducible synthetic examples and the [public documentation](https://github.com/rmak78/phpledger/wiki) for current scope and roadmap. The [hosted demo](https://phpledger.com/demo/) is temporary and resets hourly. Do not submit real business data, credentials or private database exports with feedback.
