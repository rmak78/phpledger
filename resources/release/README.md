# PHP Ledger {{VERSION}}

Open-source bookkeeping for small businesses, built with PHP and MySQL.

**Development preview.** Evaluate this package with synthetic data before arranging an accountant-reviewed pilot. It is an early foundation, with known workflow and reporting gaps; it is not a completed ERP or a country-certified accounting product.

Source revision: `{{SOURCE_COMMIT}}`

## Start here

1. Read [INSTALL.md](INSTALL.md) for hosting requirements, private configuration and the three setup commands.
2. Sign in, create an isolated sample company or a new business, then record a receipt or expense.
3. Follow the posted transaction into its journal and reports. Corrections use a linked reversal.
4. Review the chart and an account statement. Use a general-journal draft for a balanced adjustment, review it, then post it in the sample company.

The package needs PHP **8.5.x**, MySQL **8.4**, HTTPS and command-line access. Production Composer dependencies are included. Keep the supplied directory layout: only `www/phpledger/public` is the web document root. Do not serve the package root.

## Included in this preview

- Sign-in, company setup, a preliminary account template and a clearly identified synthetic sample.
- Receipt/expense drafts, balanced posting, durable source references, duplicate protection, period controls and linked reversals.
- Chart management with stable account IDs and audited name/status changes; general-journal drafts, review, posting and dated reversals.
- Account statements for all five account classes, with opening, period, running and closing balances.
- An owner overview, trial balance, balance sheet, profit and loss, and a cash scenario using editable assumptions.
- An illustrative cash-sale POS with click-to-add products, cart controls, separate review/cash confirmation, receipt and accounting entry.
- Opening trial-balance/CSV cutover with reconciled unpaid-document evidence, period administration, and bank statement CSV import/matching/reconciliation.
- Core CSV exports with exact amounts and scoped report metadata; account exports include all movements up to the documented 10,000-row limit.
- Bundled core/POS module manifests and owner-controlled enable/disable/upgrade decisions in **Modules**, with immutable history. Ordinary companies start with POS disabled; explicitly created samples enable the showcase. Existing receipts remain readable after disablement.
- English screens, a choice of supported base currencies, regional formatting and terminal timezone display. Each book uses one currency.
- Disabled, unreviewed tax research JSON for eight countries and seven industries, with a read-only CLI structural validator. These files do not enable tax calculations or set company tax profiles.

Detailed historical journals, XLSX, invoice/bill settlement workflows, receivables/payables aging, stock control, country tax adapters, foreign-exchange accounting, additional accounting books and document scanning are future work. POS currently has no stock/COGS, tax, credit-sale or payment-provider integration. See [RELEASE-NOTES.md](RELEASE-NOTES.md) for the boundaries.

## Package and operations

[INSTALL.md](INSTALL.md) covers a fresh database. [UPGRADE.md](UPGRADE.md) covers maintenance, backups and recovery. No automatic upgrade from the historical PHP Ledger application is provided.

`PACKAGE-MANIFEST.json` identifies the packaged files and source. Preserve the package, its published checksum, configuration backup and database backup together. Project terms are in [LICENSE](LICENSE); dependency and asset notices are in [THIRD-PARTY-NOTICES.md](THIRD-PARTY-NOTICES.md).

The archive includes eleven migrations and 35 guard triggers, including both distinct `006_*` identities and `010_module_lifecycle`. Follow the upgrade guide before changing an existing database. Account IDs and posted history are retained, and existing-business opening reconciliation remains required. Module installation alone never enables existing companies. Public API/MCP access remains future work.

The archive excludes the old application, marketing website, development Docker setup, development/test suite, private configuration and customer data. The standalone `tools/validate-tax-catalog.php` is an optional structural check; it does not activate or approve tax research. There is no requirement to run Composer development scripts on the customer server.

## Learn and contribute

[Project website](https://phpledger.com/) · [Hosted evaluation demo](https://phpledger.com/demo/) · [Documentation](https://github.com/rmak78/phpledger/wiki) · [Issue tracker](https://github.com/rmak78/phpledger/issues)

The hosted demo uses temporary synthetic data and resets hourly; never enter customer information there. Report reproducible problems with the package version and a synthetic example, without passwords, database dumps or private records. Setup and pilot enquiries: [rmak78@gmail.com](mailto:rmak78@gmail.com).
