# Start with the preview

**The new hosted preview is awaiting its final launch check.** The intended entry points are [the PHP Ledger website](https://phpledger.com/) and [the demonstration](https://phpledger.com/demo/). Their availability must be confirmed before this becomes a published launch announcement.

The first installable package for the new application is still being prepared. The public repository's default branch currently contains the legacy application. Cloning it is not a quickstart for the new preview, and this page deliberately provides no installation command that would imply otherwise.

## When the demonstration is available

The prepared demo gives each visitor a separate fictional business, uses synthetic records and resets hourly. Treat entries as temporary: an hourly refresh ends the old sample session. Destructive administrative operations are restricted on the server. Do not enter real customer records, credentials or business documents.

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

Hosting support will be documented against tested configurations. The repository root and historical SQL dumps are not the new application's deployment or migration path. A customer installation also needs HTTPS, private configuration, reliable backups and a tested restoration procedure.

## Installation and business setup are separate

An administrator prepares the server and creates the initial administrator account. An owner or accountant then creates a business, reviews its accounts and resolves any opening-position requirements. Importing past records is a separate guided accounting process, not a shortcut around opening reconciliation.

The [[first package plan|First-Package]] includes a versioned archive, verified installation instructions, an upgrade path and recovery checks. A tested quickstart will be linked here when that package exists; no release tag or download is available yet for the new foundation.

[[Current status|Home]] · [[Countries and currencies|Countries-and-Currencies]] · [[Support enquiries|Contributing-and-Support]]
