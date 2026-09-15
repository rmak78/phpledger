# Start with the preview

Start at [the PHP Ledger website](https://phpledger.com/) or [open the demonstration](https://phpledger.com/demo/).

**[Download the 0.1.2-preview package](https://github.com/rmak78/phpledger/releases/tag/v0.1.2-preview).** Choose `phpledger-0.1.2-preview.zip` and its SHA-256 file from the release assets; automatic source archives do not include installed dependencies. Follow the package's `INSTALL.md`, or `UPGRADE.md` when upgrading an existing installation. Use synthetic data for evaluation.

This release adds universal account statements, chart management and saved general-journal draft/review/post/reverse workflows. It retains receipts, expenses, owner reports and the sample cash POS. It is a development preview, with the remaining gates described in [[First package|First-Package]].

Modern source is in `www/phpledger`; historical code stays in `legacy/`. Developers can use the [development guide](https://github.com/rmak78/phpledger/blob/master/docs/DEVELOPMENT.md). New project-owned code and documentation are [MIT licensed](https://github.com/rmak78/phpledger/blob/master/LICENSE); [licence scope](https://github.com/rmak78/phpledger/blob/master/LICENSE-SCOPE.md) preserves separate historical, dependency and asset terms.

## Try the demonstration

Each visitor receives a separate fictional business. Synthetic records reset hourly, ending the old sample session. Capacity limits apply to temporary writes. Do not enter real customer records, credentials or business documents.

1. Open an account and inspect its statement: opening balance, period activity and closing balance.
2. Create a general-journal draft with synthetic amounts and save it.
3. Reopen it, review the lines and balance debits against credits before posting.
4. Follow its source and journal into the account statement and trial balance.
5. Try a linked reversal with a reason, or explore the [[sample shop sale|POS-Showcase]].

Accounts are read-only in the public demo; account creation and changes are reserved for authorized owners/accountants in a self-hosted installation. General-journal draft editing, posting and linked reversals are available within the visitor's books. Reset removes temporary work.

## Planning a self-hosted installation

| Requirement | Preview environment |
|---|---|
| PHP | PHP 8.5.x |
| PHP extensions | BCMath, PDO, PDO MySQL and mbstring |
| Database | MySQL 8.4 LTS with InnoDB |
| Dependencies | Composer with a pinned lockfile; production dependencies included in the package |
| Development environment | Docker Compose |
| Web root | Only the new application's `www/phpledger/public` directory |
| Operations | HTTPS, terminal access, private configuration, backups and tested restoration |

Never serve the repository root or `legacy/`, and never use historical `legacy/install/` SQL dumps as the new application's migration path. Follow the instructions for the exact package and retain a reconciled backup before an upgrade.

## Installation and business setup are separate

An administrator prepares the server and initial administrator account. An owner or accountant then creates a business and reviews its accounts and opening-position requirements.

Guided opening imports, period completion and bank reconciliation remain future core work. Existing-business setup must not be treated as complete merely because a name and currency were entered. AR/AP, tax, inventory and production POS are planned optional modules; the eight-country tax research catalog does not activate tax rules.

[[Package scope and remaining gates|First-Package]] · [[Module roadmap|Module-Roadmap]] · [[Support enquiries|Contributing-and-Support]]
