# PHP Ledger {{VERSION}} — release notes

Source revision: `{{SOURCE_COMMIT}}`.

This package is a development preview of the restarted PHP Ledger application. It preserves the lightweight BixiSoft PHP/MeekroDB structure while separating accounting functions, server permissions, templates and the public front controller. It is intended for evaluation and supported pilot preparation.

## Working scope

- PHP 8.5.x, MySQL 8.4, pinned production dependencies and a versioned migration runner.
- Command-line prerequisite checks, schema installation and initial-user creation; private database configuration and HTTPS sessions.
- Company setup with an account template, new-business readiness rules and opt-in synthetic sample data.
- Company/book-scoped receipts and expenses, drafts, atomic balanced posting, fixed-precision amounts, duplicate protection, linked sources, period locking and reversals.
- Owner overview, trial balance, balance sheet, profit and loss, and an editable cash scenario. A scenario reflects entered assumptions; it is not a prediction or a statement of cash flows.
- An illustrative cash POS with catalog search, quantity entry, exact cash/change, an explicit checkout action, receipt and linked accounting entry.
- English screens, supported base-currency choices, regional date/number formatting and local terminal timezone display with UTC event storage.

## Known limits

The reports are country-neutral management views. Pakistan, UK and UAE accounting/reporting research does not amount to ICAP, ICMAP or ACCA approval, statutory presentation compliance, tax certification or filing support. Obtain appropriate accounting review before relying on this preview for a business period.

The POS is a cash-sale demonstration of the posting foundation. Stock quantities, cost of goods sold, tax calculations, credit accounts, payment-provider capture, hardware integrations and complete retail/restaurant workflows are not implemented. The sample catalog is illustrative.

Each book has one base currency. Currency choices and formatting do not implement foreign exchange, multi-currency transactions, consolidation or a completed multi-book model. English remains the application language; country metadata does not provide translated screens or localized accounting rules.

Historical CSV imports and cutover tools, opening AR/AP reconciliation, customer invoices, vendor bills, aging reports, bank reconciliation, inventory/stock reports, country tax adapters, offline operation and receipt/document scanning remain future work. Existing-business setup must not be treated as ready for posting until its opening obligations are resolved.

The selected UI is a working preview. Country-specific report layouts and the next POS design are still under review. Passing technical checks does not establish observed usability success, independent accounting acceptance or a completed security review.

## Installation and compatibility

Use [INSTALL.md](INSTALL.md) for the current CLI installation. Only `www/phpledger/public` may be served. Dependencies and all five migration files are included; the historical root application, development tools, marketing website and hosted-demo scheduler are excluded.

The package has no automatic upgrade from the old PHP Ledger database and no established earlier customer-package compatibility range. See [UPGRADE.md](UPGRADE.md) for maintenance and restoration requirements. Keep deployment-specific validation records separate from these feature notes; this document does not claim that your host or data has passed acceptance.

## Feedback and next steps

Use the [issue tracker](https://github.com/rmak78/phpledger/issues) for reproducible synthetic examples and the [public documentation](https://github.com/rmak78/phpledger/wiki) for current scope and roadmap. The [hosted demo](https://phpledger.com/demo/) is temporary and resets hourly. Do not submit real business data, credentials or private database exports with feedback.
