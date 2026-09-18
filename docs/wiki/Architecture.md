## Current package: 1.0.0

**1.0.0**, published 18 September 2026, is the first stable release. [Download 1.0.0](https://github.com/rmak78/phpledger/releases/tag/v1.0.0) and its [matching media kit](https://github.com/rmak78/phpledger/releases/download/v1.0.0/phpledger-1.0.0-media-kit.zip). Independent accounting review, independent security review, supervised pilots with a real month-end close and unfamiliar-operator installation observation have **not** happened; they continue as post-release commitments. See [[Release 1.0.0|Release-1.0.0]].

# A small, explicit foundation

PHP Ledger retains BixiSoft's lightweight modular PHP approach: one shared bootstrap, explicit routes, reusable typed functions, PHP templates and small JavaScript modules. It is a standalone application, with no WordPress runtime or CRM-specific dependency.

| Layer | Direction |
|---|---|
| Runtime | PHP 8.2+ (8.3 recommended) |
| Database | MySQL 8.4 LTS / InnoDB |
| Database access | Maintained MeekroDB dependency, pinned through Composer |
| Interface | Server-rendered pages, modern CSS and progressive enhancement |
| Installation | Browser installer at `/install`, or CLI (`preflight.php`, `migrate.php`, `create-admin.php`) |
| Updates | Publisher-signed packages applied through `/maintenance.php`, with automatic matched backup and recovery |
| Development | Docker Compose, versioned migrations, automated checks and static analysis |

The application lives under `www/phpledger`, with only its `public` directory served by the web server. Configuration, dependencies, migrations, tools and private storage stay outside the document root. Historical code remains separate.

## Accounting rules belong outside the screen

Typed services handle exact amounts, posting, account maintenance, saved general journals, source links and corrections independently of HTML, request variables and sessions. Every document uses the central posting interface.

Account code, type and purpose remain fixed after creation. Name and active-status changes are permission checked, audited and protected against stale edits. Draft journals may be unbalanced; posting validates the current saved revision and requires balanced lines. Posted entries are immutable, with corrections recorded as linked reversals.

Financial writes commit atomically or roll back. Company/book permissions apply on the server; durable source identities protect retries. Universal account statements and other reports use the same authorized scope as their sources. A book identifier does not itself implement alternative reporting books or consolidation.

## Required core plus optional modules

The required core owns accounts, money, journals, periods, opening conversion, bank reconciliation and core reports, plus required AR/AP with manually configured tax. Purchasing and shared Inventory are optional, bundled modules that submit to the same core services. Account statements are core reports; customer/vendor ageing comes from AR/AP, and stock valuation from Inventory.

A scoped, revocable read API and MCP connection interface is available over the same services, permissions and company capabilities, with a published client compatibility matrix defining tested clients. Financial write access over the API/MCP is not yet available. The bundled cash POS is wired into the application as an illustrative demonstration; a supported install/enable/upgrade/disable module lifecycle is not implemented. See [[Module roadmap|Module-Roadmap]] and the [detailed architecture](https://github.com/rmak78/phpledger/blob/master/docs/ARCHITECTURE.md).

## Installation and updates

Browser installation at `/install` checks host and database prerequisites, applies the existing migration chain, provisions OAuth keys, creates the first account and continues into business onboarding, without Composer, Node or a terminal. CLI installation remains available. `/maintenance.php`, a separate operator entry point, applies publisher-signed release packages, taking an automatic matched code/configuration/key/database backup first and restoring it automatically if a migration or mutation fails. 1.0.0 ships without signed update metadata because the publisher signing key has not yet been generated; verify the release by its SHA-256 checksum. See [[Getting started|Getting-Started]] and the [installer plan](https://github.com/rmak78/phpledger/blob/master/docs/INSTALLER.md).

## Security and operational boundaries

Browser writes require CSRF protection; rendered content uses contextual escaping. The demo uses separate synthetic storage, visitor-scoped companies, capacity limits and hourly reset/session expiry. Account administration stays blocked there; saved general journals, posting and linked reversals operate within the visitor's books.

Automated tests, fault-injection tests and exact-artifact install/upgrade/recovery checks back 1.0.0. Independent security review, qualified accounting review, supported hosting and observed user acceptance have not happened; they remain post-release commitments, not completed gates. Test results do not establish professional certification.

Tax research is disabled candidate data outside the runtime. Reviewed tax adapters must define transaction scope, effective rules and retained calculation snapshots before applicable production use. [[Tax research|Tax-Research]] distinguishes tax authorities from financial-reporting guidance.

Offline checkout and native wrappers are later investigations. Interrupted connections require an authoritative server result; future offline work needs recovery and reconciliation.

[[Accounting and reports|Accounting-and-Reports]] · [[Module roadmap|Module-Roadmap]] · [[First package|First-Package]]
