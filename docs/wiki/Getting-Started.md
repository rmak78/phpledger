## Current package: 1.0.0

**1.0.0**, published 18 September 2026, is the first stable release. [Download 1.0.0](https://github.com/rmak78/phpledger/releases/tag/v1.0.0) and its [matching media kit](https://github.com/rmak78/phpledger/releases/download/v1.0.0/phpledger-1.0.0-media-kit.zip). It consolidates the interface rebuild, the guided browser installer and publisher-signed automatic updates with automatic matched backup/recovery. Compiled CSS is included; no Node is needed on the host. The PHP/MeekroDB architecture and central immutable posting model remain.

Automated test suites, fault-injection update/recovery tests, exact-artifact install/upgrade/recovery checks and developer-operated browser checks back this release. Independent accounting review, independent security review, supervised pilots with a real month-end close, unfamiliar-operator installation observation and restricted shared-host recovery certification have **not** happened; these continue as post-release commitments. See [[Release 1.0.0|Release-1.0.0]], [INSTALL.md](https://github.com/rmak78/phpledger/blob/master/resources/release/INSTALL.md), [UPGRADE.md](https://github.com/rmak78/phpledger/blob/master/resources/release/UPGRADE.md) and [RELEASE-SIGNING.md](https://github.com/rmak78/phpledger/blob/master/docs/RELEASE-SIGNING.md).

# Start with the package

Start at [the PHP Ledger website](https://phpledger.com/) or [open the demonstration](https://phpledger.com/demo/).

**[Download the 1.0.0 package](https://github.com/rmak78/phpledger/releases/tag/v1.0.0).** Choose `phpledger-1.0.0.zip` and its SHA-256 file from the release assets; automatic source archives do not include installed dependencies. Follow the package's `INSTALL.md`, or `UPGRADE.md` when upgrading an existing installation. Verify the package signature against the publisher fingerprint in `RELEASE-SIGNING.md` before installing. Use synthetic data for evaluation.

This release adds universal account statements, chart management, saved general-journal draft/review/post/reverse workflows, the accounting starter (AR/AP, optional Purchasing/Inventory, core tax), browser installation and signed automatic updates. It retains receipts, expenses, owner reports and the sample cash POS. It is the first stable release, with the remaining independent-review and pilot gates described in [[First package|First-Package]].

Modern source is in `www/phpledger`; historical code remains only in Git history. Developers can use the [development guide](https://github.com/rmak78/phpledger/blob/master/docs/DEVELOPMENT.md). New project-owned code and documentation are [AGPL-3.0-or-later licensed](https://github.com/rmak78/phpledger/blob/master/LICENSE); [licence scope](https://github.com/rmak78/phpledger/blob/master/LICENSE-SCOPE.md) preserves separate historical, dependency and asset terms.

## Install in the browser

Unpack the release ZIP on your host, point an HTTPS hostname at `www/phpledger/public`, and visit `/install`. The guided installer checks host/database prerequisites, applies the existing migration chain, creates the first account and continues into business onboarding — no terminal, Composer or Node is required. CLI installation with `preflight.php`, `migrate.php` and `create-admin.php` remains available for operators who prefer it. See `INSTALL.md` in the release package for full steps.

## Keep it updated

Signed automatic updates are available from the independent `/maintenance.php` operator interface: verify the publisher signature, take a matched code/configuration/key/database backup, apply the release and run migrations, with automatic restoration of the matched backup if anything fails. This requires the PHP zip extension. See `UPGRADE.md` and [[Release 1.0.0|Release-1.0.0]] for the manual backup/restore procedure and current qualification boundaries.

## Try the demonstration

Each visitor receives a separate fictional business. Synthetic records reset hourly, ending the old sample session. Capacity limits apply to temporary writes. Do not enter real customer records, credentials or business documents.

1. Open **Reports → Open account ledger**, choose an account and inspect opening, debit, credit, running and closing balances. Transactions and Journals also link directly to the ledger; mobile entries keep the running balance visible.
2. Create a general-journal draft with synthetic amounts and save it.
3. Reopen it, review the lines and balance debits against credits before posting.
4. Follow its source and journal into the account statement and trial balance.
5. Try a linked reversal with a reason, or explore the [[sample shop sale|POS-Showcase]].

Accounts are read-only in the public demo; account creation and changes are reserved for authorized owners/accountants in a self-hosted installation. General-journal draft editing, posting and linked reversals are available within the visitor's books. Reset removes temporary work.

## Planning a self-hosted installation

| Requirement | 1.0.0 environment |
|---|---|
| PHP | PHP 8.2+ (8.3 recommended) |
| PHP extensions | BCMath, PDO, PDO MySQL, mbstring, cURL, OpenSSL, fileinfo and sessions; the zip extension is additionally required to use automatic in-browser updates |
| Database | MySQL 8.4 LTS with InnoDB |
| Dependencies | Composer with a pinned lockfile; production dependencies included in the package |
| Development environment | Docker Compose |
| Web root | Only the new application's `www/phpledger/public` directory |
| Installation | Browser installation at `/install`; no terminal access required for setup. CLI installation/recovery remains available for operators who prefer it |
| Operations | HTTPS, private configuration, backups and tested restoration |

Never serve the repository root, and never use historical installation SQL dumps as the new application's migration path. Follow the instructions for the exact package and retain a reconciled backup before an upgrade.

## Installation and business setup are separate

An administrator prepares the server and initial administrator account. An owner or accountant then creates a business and reviews its accounts and opening-position requirements.

Reviewed opening trial-balance/CSV cutover, period administration, bank CSV reconciliation and core CSV exports are included. Company owners can review and enable the optional POS showcase in Modules; ordinary companies default off and explicit new samples enable it. Existing-business setup must not be treated as complete merely because a name and currency were entered. AR/AP, tax, inventory and production POS are planned optional modules; the eight-country tax research catalog does not activate tax rules.

[[Package scope and remaining gates|First-Package]] · [[Module roadmap|Module-Roadmap]] · [[Support enquiries|Contributing-and-Support]]

## Connected reporting and richer samples

This combined preview retains company/book permissions, chart management, receipt/expense/general-journal drafts, balanced posting, linked reversals, opening cutover with a reconciled unpaid-document register, period controls, bank CSV reconciliation, reports, running account balances and CSV exports.

Scoped API/MCP reads and existing-user OAuth/Connections join four fictional businesses: service agency, retail shop, seasonal business and distributor. Each includes 74 sources, 2024–2025 history, an open 2026 practice period and three editable drafts. Follow [[Reporting walkthroughs|Reporting-Guides]] and [[Read integrations|Integrations]].
