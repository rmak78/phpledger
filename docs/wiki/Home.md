<p><img src="https://raw.githubusercontent.com/rmak78/phpledger/master/docs/repository/assets/phpledger-logo.webp" alt="PHP Ledger" width="320"></p>

# PHP Ledger

**PHP Ledger is open-source, self-hosted double-entry accounting software with a simple cash point of sale for small businesses, built on PHP 8.5 and MySQL 8.4 and currently in development preview.**

PHP Ledger is being rebuilt for small-business owners, accountants and bookkeepers who want a useful workspace on hosting they control. The direction is a complete accounting core, followed by optional business modules.

Explore [phpledger.com](https://phpledger.com/) and [your temporary sample company](https://phpledger.com/demo/). The [0.1.2-preview package](https://github.com/rmak78/phpledger/releases/tag/v0.1.2-preview) adds universal account statements, chart management and saved general journals. It remains a development preview for evaluation with synthetic data.

Modern application source is in `www/phpledger`; the historical application remains only in Git history. See [[Getting started|Getting-Started]] for the package, installation requirements and demonstration limits.

## Find your starting point

| I want to… | Start here |
|---|---|
| Understand the product and who it serves | [[Product overview|Product-Overview]] |
| Try the preview or plan an installation | [[Getting started|Getting-Started]] |
| Follow accounts, journals and report balances | [[Accounting and reports|Accounting-and-Reports]] |
| See how a shop sale reaches the ledger | [[POS showcase|POS-Showcase]] |
| Understand core and optional module delivery | [[Module roadmap|Module-Roadmap]] |
| Review country and industry tax research | [[Tax research|Tax-Research]] |
| Check currencies and regional plans | [[Countries and currencies|Countries-and-Currencies]] |
| Check the package scope and remaining gates | [[First package|First-Package]] |
| Explore the broader future path | [[Roadmap]] |
| Help build or review PHP Ledger | [[Contributing and support|Contributing-and-Support]] |

## What the preview demonstrates

Follow any authorized account from its opening balance through period debits, credits and running balances to its closing balance. Return from a statement line to the journal and source behind it. Authorized owners and accountants can create accounts and audit changes to names or active status; account code, type and purpose stay fixed.

Save a general-journal draft, return to edit it, review its lines and post when debits equal credits. Corrections use linked reversals that preserve the original entry. Receipts, expenses, owner reports and the sample cash POS use the same accounting services.

The public demo provides temporary visitor books: accounts are read-only, while general-journal drafts, posting and linked reversals are available within capacity limits. Its synthetic data resets hourly. The shop showcase remains a six-product cash example, without stock, tax or payment processing.

## The direction

Reviewed opening entries and cutover, period completion and bank reconciliation come next in the core. Module lifecycle, a business API and MCP access follow before optional AR, AP, inventory, tax and industry POS. These are planned capabilities; the current POS is still part of the application rather than an installable add-on.

Accounting framework work starts with **Pakistan, then the UK and UAE**. The separate tax candidate catalog covers eight countries and seven industries, remains disabled and unreviewed, and supplies no active tax calculations.

New project-owned code and documentation use the [MIT License](https://github.com/rmak78/phpledger/blob/master/LICENSE). [Licence scope](https://github.com/rmak78/phpledger/blob/master/LICENSE-SCOPE.md) preserves separate historical, dependency and asset terms. No stable release, professional-body endorsement or regulatory certification is claimed.

[[Package scope|First-Package]] · [[Module roadmap|Module-Roadmap]] · [[Full roadmap|Roadmap]]
