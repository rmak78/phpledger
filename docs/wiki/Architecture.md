# A small, explicit foundation

PHP Ledger retains BixiSoft's lightweight modular PHP approach: one shared bootstrap, explicit routes, reusable typed functions, PHP templates and small JavaScript modules. It is a standalone application, with no WordPress runtime or CRM-specific dependency.

| Layer | Direction |
|---|---|
| Runtime | PHP 8.5 |
| Database | MySQL 8.4 LTS / InnoDB |
| Database access | Maintained MeekroDB dependency, pinned through Composer |
| Interface | Server-rendered pages, modern CSS and progressive enhancement |
| Development | Docker Compose, versioned migrations, automated checks and static analysis |

The application lives under `www/phpledger`, with only its `public` directory served by the web server. Configuration, dependencies, migrations, tools and private storage stay outside the document root. Historical root code remains separate from the new runtime.

## Accounting rules belong outside the screen

Small typed services handle exact amounts, balanced posting, source links and corrections independently of HTML, request variables and sessions. Modules use the same central posting interface. A sale, receipt or future inventory document must not bypass it to write journal rows directly.

Financial writes commit atomically or roll back. Company/book permissions are enforced on the server, duplicate keys protect retries, and posted records remain immutable. The initial company/book identifiers provide scope; they do not yet implement multiple reporting books or consolidation.

## Security and operational boundaries

Browser writes require CSRF protection and contextual output escaping. Each report uses the same business scope as its source records. The prepared public demo adds separate synthetic storage, per-visitor companies, reset/session expiry and restricted administrative operations.

Local checks include posting/access failures, duplicate conflicts, concurrent writes, reversals, upgrades and backup restoration. Supported hosting, sustained checkout/report load, independent security review and qualified accounting review remain release gates. There is no published terminal-capacity or compliance claim.

## Growth follows measured need

Inventory and specialist modules should extend the existing services rather than introduce parallel authentication or posting systems. Background processing, report summaries and asynchronous events need durable receipts, retries and visible reconciliation when they become necessary.

Offline checkout and native wrappers are later investigations. A browser losing its server connection cannot currently finish an authoritative sale. Any future offline mode needs conflict handling, recovery and reconciliation before it can be described as supported.

[[First package|First-Package]] · [[Roadmap]] · [[Contributing and support|Contributing-and-Support]]
