# PHP Ledger {{VERSION}}

Open-source bookkeeping for small businesses, built with PHP and MySQL.

**Development preview.** Evaluate this package with synthetic data before arranging an accountant-reviewed pilot. It is an early foundation, with known workflow and reporting gaps; it is not a completed ERP or a country-certified accounting product.

Source revision: `{{SOURCE_COMMIT}}`

## Start here

1. Read [INSTALL.md](INSTALL.md) for hosting requirements, private configuration and the three setup commands.
2. Sign in, create an isolated sample company or a new business, then record a receipt or expense.
3. Follow the posted transaction into its journal and reports. Corrections use a linked reversal.

The package needs PHP **8.5.x**, MySQL **8.4**, HTTPS and command-line access. Production Composer dependencies are included. Keep the supplied directory layout: only `www/phpledger/public` is the web document root. Do not serve the package root.

## Included in this preview

- Sign-in, company setup, a preliminary account template and a clearly identified synthetic sample.
- Receipt/expense drafts, balanced posting, durable source references, duplicate protection, period controls and linked reversals.
- An owner overview, trial balance, balance sheet, profit and loss, and a cash scenario using editable assumptions.
- An illustrative cash-sale POS with a sample catalog, receipt and accounting entry.
- Opening trial-balance/CSV cutover with reconciled unpaid-document evidence, period administration, and bank statement CSV import/matching/reconciliation.
- English screens, a choice of supported base currencies, regional formatting and terminal timezone display. Each book uses one currency.

Detailed historical journals, XLSX, invoice/bill settlement workflows, receivables/payables aging, stock control, country tax adapters, foreign-exchange accounting, additional accounting books and document scanning are future work. POS currently has no stock/COGS, tax, credit-sale or payment-provider integration. See [RELEASE-NOTES.md](RELEASE-NOTES.md) for the boundaries.

## Package and operations

[INSTALL.md](INSTALL.md) covers a fresh database. [UPGRADE.md](UPGRADE.md) covers maintenance, backups and recovery. No automatic upgrade from the historical PHP Ledger application is provided.

`PACKAGE-MANIFEST.json` identifies the packaged files and source. Preserve the package, its published checksum, configuration backup and database backup together. Project terms are in [LICENSE](LICENSE); dependency and asset notices are in [THIRD-PARTY-NOTICES.md](THIRD-PARTY-NOTICES.md).

The archive excludes the old application, marketing website, development Docker setup, test tools, private configuration and customer data. There is no requirement to run Composer development scripts on the customer server.

## Learn and contribute

[Project website](https://phpledger.com/) · [Hosted evaluation demo](https://phpledger.com/demo/) · [Documentation](https://github.com/rmak78/phpledger/wiki) · [Issue tracker](https://github.com/rmak78/phpledger/issues)

The hosted demo uses temporary synthetic data and resets hourly; never enter customer information there. Report reproducible problems with the package version and a synthetic example, without passwords, database dumps or private records. Setup and pilot enquiries: [rmak78@gmail.com](mailto:rmak78@gmail.com).
