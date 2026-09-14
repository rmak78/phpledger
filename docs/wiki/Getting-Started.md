# Start with the preview

**The hosted preview is live.** Start at [the PHP Ledger website](https://phpledger.com/) or [open the demonstration](https://phpledger.com/demo/). The updated website and demo were published on 14 September 2026.

**[Download the 0.1.0-preview foundation package](https://github.com/rmak78/phpledger/releases/tag/v0.1.0-preview).** Choose `phpledger-0.1.0-preview.zip` and its SHA-256 file from the release assets; the automatic source archives do not include installed dependencies. Read `INSTALL.md` inside the ZIP. The package requires PHP 8.5.x, MySQL 8.4, HTTPS and terminal access. Evaluate it with synthetic data; it is not a stable or country-certified release. Modern source is in `www/phpledger`, with historical code in `legacy/`. Developers can use the [development guide](https://github.com/rmak78/phpledger/blob/master/docs/DEVELOPMENT.md).

**Next package:** 0.1.1-preview is being prepared with click-to-add products, cart controls and a separate sale-review/cash-confirmation screen. It is not yet the published download. Package publication and deployment of the hosted demo are checked separately.

The new project-owned code and documentation are [MIT licensed](https://github.com/rmak78/phpledger/blob/master/LICENSE). Read [licence scope](https://github.com/rmak78/phpledger/blob/master/LICENSE-SCOPE.md) for the separate historical, dependency and asset terms.

## Try the demonstration

The demo gives each visitor a separate fictional business, uses synthetic records and resets hourly. Treat entries as temporary: an hourly refresh ends the old sample session. Destructive administrative operations are restricted on the server. Do not enter real customer records, credentials or business documents.

Start by recording a small expense, following its journal into a report, and trying the [[sample shop sale|POS-Showcase]]. Opening balances, posted totals and report limitations should remain visible. The preview is for evaluation rather than live bookkeeping.

## Planning a self-hosted installation

The new foundation has been exercised locally with:

| Requirement | Current foundation direction |
|---|---|
| PHP | PHP 8.5, using a maintained stable patch |
| PHP extensions | BCMath, PDO, PDO MySQL and mbstring |
| Database | MySQL 8.4 LTS with InnoDB |
| Dependencies | Composer with a pinned lockfile |
| Reproducible development | Docker Compose |
| Web root | Only the new application's `www/phpledger/public` directory |

Hosting support will be documented against tested configurations. Never serve the repository root or `legacy/`, and never use historical `legacy/install/` SQL dumps as the new application's migration path. A customer installation also needs HTTPS, private configuration, reliable backups and a tested restoration procedure.

## Installation and business setup are separate

An administrator prepares the server and creates the initial administrator account. An owner or accountant then creates a business, reviews its accounts and resolves any opening-position requirements. Importing past records is a separate guided accounting process, not a shortcut around opening reconciliation.

The [[package plan and remaining gates|First-Package]] covers installation, upgrades, recovery, accounting review and usability. The published foundation archive linked above contains its tested `INSTALL.md` quickstart and `UPGRADE.md`; follow those documents for that exact package. The preview does not yet satisfy the supported-pilot gates.

[[Current status|Home]] · [[Countries and currencies|Countries-and-Currencies]] · [[Support enquiries|Contributing-and-Support]]
