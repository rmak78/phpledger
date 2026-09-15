# A small, explicit foundation

PHP Ledger retains BixiSoft's lightweight modular PHP approach: one shared bootstrap, explicit routes, reusable typed functions, PHP templates and small JavaScript modules. It is a standalone application, with no WordPress runtime or CRM-specific dependency.

| Layer | Direction |
|---|---|
| Runtime | PHP 8.5 |
| Database | MySQL 8.4 LTS / InnoDB |
| Database access | Maintained MeekroDB dependency, pinned through Composer |
| Interface | Server-rendered pages, modern CSS and progressive enhancement |
| Development | Docker Compose, versioned migrations, automated checks and static analysis |

The application lives under `www/phpledger`, with only its `public` directory served by the web server. Configuration, dependencies, migrations, tools and private storage stay outside the document root. Historical code remains separate.

## Accounting rules belong outside the screen

Typed services handle exact amounts, posting, account maintenance, saved general journals, source links and corrections independently of HTML, request variables and sessions. Every document uses the central posting interface.

Account code, type and purpose remain fixed after creation. Name and active-status changes are permission checked, audited and protected against stale edits. Draft journals may be unbalanced; posting validates the current saved revision and requires balanced lines. Posted entries are immutable, with corrections recorded as linked reversals.

Financial writes commit atomically or roll back. Company/book permissions apply on the server; durable source identities protect retries. Universal account statements and other reports use the same authorized scope as their sources. A book identifier does not itself implement alternative reporting books or consolidation.

## Core first, optional modules next

The required core owns accounts, money, journals, periods, opening/cutover, cash/bank reconciliation and core reports. Opening import, usable period-close administration and bank reconciliation are still planned. Account statements are core reports; customer/vendor aging and stock valuation depend on future subledgers.

AR, AP, purchasing/inventory, tax, checkout and industry workflows will own their documents and submit to the same core services. Shared contacts and products will be introduced for their first consuming module. The current sample POS is wired into the application; a supported install/enable/upgrade/disable lifecycle is not implemented.

A versioned business API and MCP access are planned over the same services, permissions and company capabilities, with reads before controlled commands. Neither interface is currently available. See [[Module roadmap|Module-Roadmap]] and the [detailed architecture](https://github.com/rmak78/phpledger/blob/master/docs/ARCHITECTURE.md).

## Security and operational boundaries

Browser writes require CSRF protection; rendered content uses contextual escaping. The demo uses separate synthetic storage, visitor-scoped companies, capacity limits and hourly reset/session expiry. Account administration stays blocked there; saved general journals, posting and linked reversals operate within the visitor's books.

Technical checks cover access failures, duplicate conflicts, concurrency, reversals, upgrades and restoration. Independent security review, qualified accounting review, supported hosting and observed user acceptance remain release gates. Test results do not establish professional certification or a terminal-capacity claim.

Tax research is disabled candidate data outside the runtime. Reviewed tax adapters must define transaction scope, effective rules and retained calculation snapshots before applicable production use. [[Tax research|Tax-Research]] distinguishes tax authorities from financial-reporting guidance.

Offline checkout and native wrappers are later investigations. Interrupted connections require an authoritative server result; future offline work needs recovery and reconciliation.

[[Accounting and reports|Accounting-and-Reports]] · [[Module roadmap|Module-Roadmap]] · [[First package|First-Package]]
