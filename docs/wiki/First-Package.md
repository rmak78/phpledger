## Current package: 1.0.0

**1.0.0**, published 18 September 2026, is the first stable release. [Download 1.0.0](https://github.com/rmak78/phpledger/releases/tag/v1.0.0) and its [matching media kit](https://github.com/rmak78/phpledger/releases/download/v1.0.0/phpledger-1.0.0-media-kit.zip). It consolidates the interface rebuild, the guided browser installer and publisher-signed automatic updates with automatic matched backup/recovery. Compiled CSS is included; no Node is needed on the host. The PHP/MeekroDB architecture and central immutable posting model remain.

Automated test suites, fault-injection update/recovery tests, exact-artifact install/upgrade/recovery checks and developer-operated browser checks back this release. Independent accounting review, independent security review, supervised pilots with a real month-end close, unfamiliar-operator installation observation and restricted shared-host recovery certification have **not** happened; these continue as post-release commitments. See [[Release 1.0.0|Release-1.0.0]].

# The first stable package

The [1.0.0 package](https://github.com/rmak78/phpledger/releases/tag/v1.0.0) is PHP Ledger's first stable release, consolidating the accounting core, the accounting starter, browser installation and signed automatic updates. It is an installable release for production evaluation with synthetic-then-real data; independent review and supported-pilot acceptance remain open post-release commitments, not completed gates.

Modern source is under `www/phpledger`, with historical code retained only in Git history. New project-owned code and documentation use [AGPL-3.0-or-later](https://github.com/rmak78/phpledger/blob/master/LICENSE); [dependency, asset and historical terms](https://github.com/rmak78/phpledger/blob/master/LICENSE-SCOPE.md) remain separate.

## Package scope

| Area | Included scope |
|---|---|
| Account statements | Opening, period debits/credits, running and closing balances for any authorized account, with source drilldown. |
| Chart management | Create accounts; audit name and active-status changes; keep account code, type and purpose fixed. |
| General journals | Save/edit drafts, review, post balanced entries and make linked reversals. Stale changes and duplicate submissions are checked. |
| Existing workflows | Company setup, receipt/expense drafts, exact posting, trial balance, basic owner reports, cash scenario and sample cash POS. |
| Public demo | Temporary visitor books; read-only accounts; general-journal draft/save/post and linked reversal within capacity limits; hourly reset. |
| Tax research | Disabled, unreviewed candidate catalog for eight countries and seven industries, with sources and unresolved questions. |
| Browser installation | Guarded `/install` wizard: host/database checks, the existing migration chain, first-account creation and business onboarding, without Composer, Node or a terminal. |
| Signed automatic updates | Publisher-signed release packages applied through the independent `/maintenance.php` interface, with automatic matched code/configuration/key/database backup and automatic recovery on a failed update. |

See [[Getting started|Getting-Started]] for installation and upgrade instructions. Use the release's own validation record and checksums for its exact artifact; older preview test totals are not evidence for a new package.

## Current scope and remaining acceptance

This preview includes opening entry/CSV review and cutover, reasoned period close/reopen, bank CSV matching/reconciliation, core CSV exports and audited module enablement. Core-only tests reconcile a complete two-period business. The account ledger is directly reachable from Reports, Transactions and Journals, with debit, credit and running balances visible on mobile. These technical checks do not complete qualified accounting review or observed pilot acceptance.

Opening import needs mapping, preview, row errors, duplicate protection, reconciliation and explicit confirmation. Until AR/AP modules exist, any retained control balances need reconciled external unpaid-document schedules. Later module activation must not double count them.

**Acceptance:** a documented entity/framework scope; reviewed account mappings and accounting policies; a reconciled two-period fixture; source drilldowns and explicit missing-data states; qualified accounting review; and observed completion by representative users.

## Installation and release gates

Each package needs its own exact source revision, checksums, dependency notices and validation receipt. A supported release needs fresh installation, upgrade from supported prior versions, backup restoration and reconciled records from the actual archive.

Security and accounting review, representative owner/bookkeeper use and supported hosting remain distinct gates. A technical pass, preview deployment or screenshot does not establish those outcomes.

The sample POS remains useful for testing the shared ledger. Production checkout still needs its declared operational capabilities and cashier review; it does not determine the next core milestone.

## Later capabilities

The bundled core/POS lifecycle is implemented. The next [[module roadmap|Module-Roadmap]] milestones after this read API/MCP preview are AR, AP, distribution/updater tooling, regional connectors, inventory, shop POS, e-commerce and then controlled commands. Customer/vendor subledgers and reviewed statement packages remain unfinished. SEO discovery and campaign preparation run in parallel at high priority. Applicable tax rules must be reviewed before affected production transactions. The catalog's research status does not satisfy that gate.

Broad country compliance, alternative books, offline operation and AI document scanning remain future work. There is no promised release date for these capabilities.

[[Current status|Home]] · [[Accounting and reports|Accounting-and-Reports]] · [[Full roadmap|Roadmap]]

## Connected reporting and richer samples

This combined preview retains company/book permissions, chart management, receipt/expense/general-journal drafts, balanced posting, linked reversals, opening cutover with a reconciled unpaid-document register, period controls, bank CSV reconciliation, reports, running account balances and CSV exports.

Scoped API/MCP reads and existing-user OAuth/Connections join four fictional businesses: service agency, retail shop, seasonal business and distributor. Each includes 74 sources, 2024–2025 history, an open 2026 practice period and three editable drafts. Follow [[Reporting walkthroughs|Reporting-Guides]] and [[Read integrations|Integrations]].
