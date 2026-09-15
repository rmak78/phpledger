<p><img src="https://raw.githubusercontent.com/rmak78/phpledger/master/docs/repository/assets/phpledger-logo.webp" alt="PHP Ledger" width="320"></p>

# PHP Ledger

**PHP Ledger is open-source, self-hosted double-entry accounting software with a simple cash point of sale for small businesses, built on PHP 8.2+ (8.3 recommended) and MySQL 8.4 and currently in development preview.**

PHP Ledger is being rebuilt for small-business owners, accountants and bookkeepers who want a useful workspace on hosting they control. The direction is a complete accounting core, followed by optional business modules.

Explore [phpledger.com](https://phpledger.com/) and [your temporary sample company](https://phpledger.com/demo/). The [0.1.6-preview package](https://github.com/rmak78/phpledger/releases/tag/v0.1.6-preview) adds universal account statements, chart management and saved general journals. It remains a development preview for evaluation with synthetic data.

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

Opening cutover, period administration, bank CSV reconciliation and the bundled core/POS lifecycle are included. Scoped API/MCP reads come next, followed by controlled commands, the browser installer and optional AR/AP. Richer multi-year demo packs have separate reconciliation and reset gates.

Accounting framework work uses explicit regional entity/period profiles over a country-neutral core. Pakistan is one intended direction; connector readiness and validated demand determine delivery. The separate tax candidate catalog covers eight countries and seven industries, remains disabled and unreviewed, and supplies no active tax calculations.

New project-owned code and documentation use the [AGPL-3.0-or-later](https://github.com/rmak78/phpledger/blob/master/LICENSE). [Licence scope](https://github.com/rmak78/phpledger/blob/master/LICENSE-SCOPE.md) preserves separate historical, dependency and asset terms. No stable release, professional-body endorsement or regulatory certification is claimed.

[[Package scope|First-Package]] · [[Module roadmap|Module-Roadmap]] · [[Full roadmap|Roadmap]]

## 0.1.6 hosting, licensing and direction update

PHP 8.2 is the minimum and PHP 8.3 is recommended for deployment. [[Hosting compatibility|PHP-Hosting]] explains the tested runtime and the remaining shared-hosting requirements.

The core is country-neutral and serves businesses, owners, bookkeepers and accountants across countries. Pakistan is one intended regional direction; FBR is one planned connector. Regional accounting/tax capabilities remain separate reviewed integrations.

The current release uses AGPL-3.0-or-later with a commercial licence available. Previously published 0.1.0 through 0.1.5 previews retain MIT. Self-hosting stays free without licence keys or licensing-server calls. See [[Licensing and contributions|Licensing]].

## 0.1.5 accessibility update

[Nagulanvelu contributed PR #65](https://github.com/rmak78/phpledger/pull/65), giving each fallback POS quantity its product name. A maintainer correction keeps the fields visible with JavaScript disabled. The package passed fresh installation, upgrade from 0.1.4, 29 synthetic checkout checks and desktop/tablet/mobile browser checks. See the [release](https://github.com/rmak78/phpledger/releases/tag/v0.1.5-preview), [privacy notice](https://phpledger.com/privacy/) and [demo-use terms](https://phpledger.com/terms/).
