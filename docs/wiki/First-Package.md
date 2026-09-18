# The foundation preview package

The [0.1.6-preview package](https://github.com/rmak78/phpledger/releases/tag/v0.1.6-preview) develops the accounting core. It is an installable evaluation checkpoint with synthetic data; supported-pilot acceptance remains open.

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

The bundled core/POS lifecycle is implemented. The next [[module roadmap|Module-Roadmap]] milestone is API/MCP reads, followed by AR, AP, distribution/updater tooling, regional connectors, inventory, shop POS, e-commerce and then controlled commands. Customer/vendor subledgers and reviewed statement packages remain unfinished. SEO discovery and campaign preparation run in parallel at high priority. Applicable tax rules must be reviewed before affected production transactions. The catalog's research status does not satisfy that gate.

Broad country compliance, alternative books, offline operation and AI document scanning remain future work. There is no promised release date for these capabilities.

[[Current status|Home]] · [[Accounting and reports|Accounting-and-Reports]] · [[Full roadmap|Roadmap]]
