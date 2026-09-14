# Architecture

## Runtime and application structure

Target PHP 8.5 on its current stable patch and MySQL 8.4 LTS/InnoDB. Pin third-party PHP dependencies through Composer and commit the lockfile. Use Docker Compose for reproducible development; validate hosting compatibility before claiming a supported installation target.

Keep BixiSoft's lightweight modular architecture: one application, one shared bootstrap, explicit routes, PHP-rendered templates, reusable typed functions, and a maintained MeekroDB dependency. Do not import Agency75 CRM business modules or settings. The reference project demonstrates conventions; it does not prove PHP 8.5 compatibility.

PHP Ledger is a standalone PHP application. It does not run inside WordPress or depend on WordPress plugins, themes, hooks, or its database model. Its maintainability depends on explicit service boundaries, small testable functions, versioned migrations and documented extension contracts; adding ERP modules must preserve those boundaries.

New code belongs under `www/phpledger`. Only its `public` directory is web-accessible. Configuration, Composer dependencies, migrations, command-line tools, tests, and private storage remain outside that document root. Historical code is preserved in `legacy/` for research and never included by the new bootstrap.

Use modern CSS and small JavaScript modules for progressive enhancement. Financial decisions stay on the server. The approved Review Console direction and Inter typography produce one reusable component system. Replace the legacy UI in the new application without editing historical assets for cosmetic consistency.

## Data and posting boundaries

Financial amounts use exact fixed precision; never use floating-point arithmetic for ledger decisions. Validate date, currency, account ownership, amount precision, period availability, actor permissions, and balanced totals before writing. Database changes use versioned migrations against a canonical new schema, not historical dumps.

Every financial module submits through one posting interface carrying company/book identity, source-document reference, posting date, currency, duplicate-prevention key, and debit/credit lines. Return a durable journal reference or an explicit validation/access failure. This is an internal application interface in the foundation, not a promised public network API.

Post synchronously and atomically: all header, line, source, and audit records succeed or roll back together. Repeated submissions return the original journal only when their payloads agree; reuse of a key with different financial content is a conflict. Tests must cover concurrent submissions, not just sequential duplicates.

Posted entries are immutable. Corrections use linked reversals with period rules and a visible original/reversal relationship. Closing a period must serialize with posting so concurrent requests cannot bypass the lock. Reports read the same company/book boundaries and reconcile to posted lines.

Begin with one complete book per company. A book key anticipates scoped posting and permissions; it does not settle alternative-book policy. Do not add tax/reporting flags, automatic cross-book copying, or consolidation until discovery defines the intended behavior.

## Approved accounting frameworks and reporting profiles

The user requires the accounting system to follow the applicable accounting guidance, including recognition, measurement, account classification, transaction rules, period-end adjustments, closing, statements and disclosures. This is a lasting product requirement; a cosmetic report redesign does not satisfy it. **Pakistan is first, followed by the UK and UAE.** For Pakistan, use the applicable statutory/SECP framework with ICAP and ICMAP guidance and appropriately qualified accounting review; ACCA guidance supports professional practice but does not define a separate universal reporting standard. See the [Pakistan research](accounting/PAKISTAN_REPORTING_RESEARCH.md) and [current reporting gaps](accounting/REPORTING_GAP_ANALYSIS.md).

Define versioned accounting/reporting profiles by jurisdiction, legal form, entity classification, reporting period, framework edition and applicable notifications. Record policy choices, source/paragraph provenance and reviewer approval. Frameworks must be explicitly confirmed for the entity and period, never inferred from IP location, display locale or currency. Effective-period changes create reviewed profile versions without rewriting posted entries or previously issued statements.

Preserve the central posting interface and stable account identities. Add reviewed recognition/measurement rules, supporting subledger and adjustment data, and separate report mappings rather than treating labels or account-number ranges as accounting authority. A statement package needs current/prior periods, applicable components and disclosures, coherent source reads, and reproducible issued results. Unmapped or missing data must remain visible; do not substitute zero or assert compliance merely because the equation balances.

Release gates require a traceable accounting rule register and complete synthetic financial-statement fixtures posted through the real services. Reconcile profit, position, cash flows where applicable, equity movements, notes and subledger controls; test period closing, reversals, prior balances, mapping changes and restatements. Obtain recorded qualified accounting review for the supported entity/framework scope before making compliance claims. The present six-account foundation and reports remain an accounting preview, not an implemented Pakistan/UK/UAE reporting framework.

## Language, time, and future currency model

The user has decided that PHP Ledger will be multilingual with **English as the primary language**, keep backend time in **UTC**, display event times in each terminal's local timezone, and later support fixed exchange rates, periodic online rate updates, and manual overrides. These decisions guide the foundation; they do not mean translations or foreign-currency posting are already implemented.

### Language and terminal display

Externalize user-facing text into translation catalogs with an English fallback. Keep text, storage, and responses UTF-8, and separate language/locale formatting from accounting values and stable internal identifiers. Prepare layouts and component direction for future right-to-left languages; a language is supported only after its translations, font coverage, dates/numbers, and actual screens are reviewed. Inter remains the English interface font; another script may need a separately reviewed companion font.

Persist event instants and audit timestamps in UTC and keep backend timekeeping consistent with that convention. Format those instants for the terminal using its browser-reported IANA timezone and locale, with a visible timezone when context could be ambiguous. A terminal's timezone is a presentation preference, not authority to change server timestamps or accounting-period rules. Define and test the display fallback when a valid terminal timezone is unavailable; do not silently reinterpret stored timestamps.

Accounting dates are a separate type of value: posting dates, fiscal boundaries, due dates, and cutover dates represent a business calendar **DATE**, not midnight UTC. Preserve their date value across terminals and daylight-saving changes. Never convert an accounting DATE through timezone arithmetic or let a timestamp formatter shift it into the preceding/following day. Localized display may change the written date format, not the date used for posting, period checks, or reconciliation.

### Locale and formatting preferences

The user requires flexible date formats, currency symbol/code and placement, digit grouping, decimal/group separators, and display precision within the supported currency/accounting rules. Support Western grouping such as `123,456,789` and South Asian grouping such as `12,34,56,789`. Start from locale defaults and allow explicit user/terminal presentation overrides; later official exported documents need company-controlled defaults so an operator's terminal preferences do not silently restyle them.

Formatting is a presentation layer. It must not change stored exact amounts, the transaction/base currency, posting precision rules, or business DATE values. Conversion of entered localized numbers/dates is a separate, explicit validation step using the selected input format. Reject ambiguous or invalid input with a clear example instead of guessing separators, decimal scale, or day/month order. Normalize accepted amounts to exact decimal strings and dates to canonical DATE values before the accounting services receive them; never introduce binary floating-point financial calculations.

Future display preferences must cover the field, table, totals, report, and exported-document contexts deliberately. A changed currency symbol is not currency conversion, and a changed decimal display must not bypass supported precision or reconciliation requirements. Record the actually implemented locale preferences and parsing behavior separately from these approved future requirements.

### Multicurrency after the current slice

Retain a company/base-book currency and introduce a distinct transaction currency when the multicurrency milestone is designed. Future conversion must use exact decimal amounts and a rate snapshot linked to each posting: the chosen rate, rate source/type (fixed, fetched, or manual), effective time, and enough provenance to explain the converted base amount. Changing or refreshing a rate later must never rewrite posted amounts or historical rate snapshots.

Periodic rate retrieval belongs in a scheduled backend process, never a live provider request made by a browser page load. Keep the last known good rate and make its age, refresh failure, and stale status visible. Manual overrides require explicit server-side permission, a recorded actor/reason and audit trail, and visible reconciliation effects; they must not bypass the central posting interface or posted-entry immutability.

The user has chosen all three rate mechanisms. The provider, refresh cadence, currency-pair/cross-rate rules, precision/rounding policy, and realized/unrealized gains and revaluation treatment are still design and accounting-review decisions. Specify rate selection and override behavior before implementation; do not infer those accounting policies from a rate feed. The current sprint still posts only in its selected base currency with the existing exact-money rules and makes no foreign-exchange accounting claim.

## Access and installation

Use real password authentication, secure session settings, server-side membership/action authorization, browser POST CSRF checks, bound query values, and contextual output escaping. Deny unknown routes and cross-company access. Keep database errors and credentials out of browser output and logs.

Installation checks runtime extensions, database connectivity, writable private storage, and schema status before creating the first administrator through a controlled installation command/flow. Installer re-entry must not become an unauthenticated administrator-creation path. Synthetic fixtures are explicit development tools and must never overwrite customer records.

Keep development services bound to loopback and the database internal by default. Installation documentation must distinguish local HTTP from production HTTPS/session requirements. Backup/restore and upgrade verification are release gates. Do not add hosted-account billing, telemetry collection, provider calls on page load, or external sending to the foundation.

The user subsequently authorized a public `/demo` at the marketing website, with a separate synthetic database, hourly controlled reset, and server-enforced restrictions on destructive user operations. This is an explicit demo deployment scope, not permission to expose customer books. Keep demo identity, allowed actions, storage/reset targets, and session/base-path behavior separate from ordinary installations. Use the shared URL helper for routes, forms, redirects, and assets so the application operates correctly below `/demo`. Reset is backend-controlled, not a public destructive action. Verify customer-database isolation, denied operations, active-session behavior during reset, and recovery before claiming the hosted demo is ready. Deployment and reset evidence remain pending.

## Extension path and quality gates

Receipt/expense screens, future AR/AP, and later operational modules must use the same posting interface. If asynchronous operational delivery is later needed, introduce a durable outbox, duplicate protection, bounded retries, visible failure status, and reconciliation. An operational save must never silently lose its financial entry.

Keep accounting rules typed and independent of HTML, sessions, and request globals so automated tests can exercise them directly. Cover precision, balance, reversal, period locks, transaction rollback, permissions, duplicates/concurrency, and report reconciliation. Run syntax checks, static analysis, dependency review, and installation tests on the target runtime.

No compatibility is claimed merely because code parses on an older local PHP. Verify PHP 8.5 and MySQL 8.4 together before calling the foundation proof complete. Test UI at desktop/tablet/mobile sizes and with keyboard interaction; timed participant research remains separate.

### Capacity, security and operational release gates

InnoDB is a suitable transactional foundation for accounting and checkout. Its ACID behavior supplies database transaction and recovery mechanisms; durability also depends on database configuration, storage, power and backup arrangements. Balanced accounting, valid account scope and duplicate prevention remain application responsibilities. Preserve durability settings and verify recovery on each supported hosting profile. See [MySQL 8.4: InnoDB and ACID](https://dev.mysql.com/doc/refman/8.4/en/mysql-acid.html).

The current `pl_ledger_book(..., true)` locking read serializes writes to the selected book. `pl_ledger_transaction()` commits related database work together, rolls back failures, and permits at most three retries after a detected deadlock when it owns the complete transaction. An outer caller's transaction is not independently replayed. POS source, journal and receipt snapshot share one transaction; stable request identities distinguish harmless retries from changed-content conflicts. Keep these transactions short, with no user interaction or provider calls while locks are held. InnoDB still permits deadlocks, including through index locks; consistent lock order, suitable indexes and complete-transaction retry are deliberate controls. See [locking](https://dev.mysql.com/doc/refman/8.4/en/innodb-locking.html) and [deadlock handling](https://dev.mysql.com/doc/refman/8.4/en/innodb-deadlocks-handling.html) in the MySQL 8.4 manual.

Existing separate-process tests prove selected duplicate, rollback and concurrency outcomes; they do not establish a till count, transactions per second, latency target or sustained capacity. Before production retail, run simultaneous checkout and report workloads using realistic history volumes, several terminals in the same book and several independent businesses. Agree workload and response-time/error targets before testing, then record latency percentiles, lock waits/deadlocks, resource use, report reconciliation and a sustained soak run. Test lost acknowledgements and retry recovery. The public demo's additional request-wide maintenance lock intentionally serializes demo requests and is not a normal-installation throughput benchmark.

Current reports aggregate posted ledger data synchronously. Profile queries and indexes before introducing summary tables, caching, report workers or background exports. Add those only for measured need; any derived balance must remain rebuildable, scoped, dated and reconciled to authoritative journals. A background job needs durable identity, retries and visible progress/failure. Never make checkout wait for a large report or move financial acceptance into an untracked background task to mask contention.

Current controls include password hashing and login throttling, session rotation, company/book permissions, CSRF, bound query values, output escaping, browser security headers, immutable posted records and tested rollback. These do not constitute an independent security audit or compliance certificate. Before real-customer pilots, commission a risk-based independent application/deployment review using a recorded [OWASP ASVS](https://owasp.org/www-project-application-security-verification-standard/) version and scope. Include privilege separation, account recovery, session/access revocation, backups, dependencies and abuse controls; add upload/export review when those features exist. Separately review privacy/data flows and retention, including the authorized country-IP lookup, and obtain accounting/local tax review for each supported jurisdiction. No current country selector establishes tax compliance.

### Connectivity and a later offline POS

The present POS requires access to its application server for checkout. There is no service worker, durable browser transaction queue or offline synchronization. A customer-owned server on the shop's LAN is a possible hosting profile to validate: terminals could continue reaching that server during an internet outage while the LAN, server and power remain available. This requires installation/support documentation, TLS, backup/restore and outage tests; it is not an implemented disconnected-browser mode or a universal hosting guarantee.

If pilots justify offline POS, design it as a separate delivery milestone. Give each locally queued operation a stable device/request identity and durable storage, with visible pending, acknowledged and rejected states. Only a server acknowledgement containing the durable receipt/journal reference means the operation posted. Reconnect through the existing permission, period, price, duplicate and posting checks; keep rejected work recoverable and explain conflicts. Define stale prices, stock reservation/overselling, revoked users, clock differences, repeated/lost acknowledgements, device loss, browser storage clearing and queue-version upgrades before release. Never silently overwrite a posted sale or describe a pending sale as synchronized.

Service workers can support cached resources and network-aware behavior, but they do not themselves provide durable accounting acceptance or conflict resolution; they require a secure context, normally HTTPS. See the [MDN Service Worker API](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API). Validate restart/reconnect behavior and local-data protection on each supported browser/device. Offline card authorization is not promised: payment capabilities depend on a separately reviewed provider/terminal integration. The current showcase only records illustrative cash tender and does not collect payments.

## Verified foundation boundary and current browser work

The following describes the verified foundation snapshot. [Sprint 02](SPRINT-02.md) adds the first browser journey using these same interfaces; its routes, draft persistence, schema changes, and checks must be documented from the completed implementation. Until that evidence is recorded, the planned journey is not a verified capability.

The foundation checkpoint was a fresh application under `www/phpledger`, with PHP 8.5.10 and MySQL 8.4 in Docker. The shared bootstrap loads the pinned Composer MeekroDB dependency and typed functions from `includes/functions/`. At that checkpoint the only HTTP endpoint was `GET /health`; login, onboarding, transaction entry, imports, and reports had no browser routes. The newer browser implementation and its separate evidence are recorded below.

Financial inputs are decimal strings, normalized through BCMath to four decimal places; persisted amounts use `DECIMAL(20,4)`. The proof supports one primary book per company and the base currencies USD, EUR, GBP, PKR, INR, MYR, BDT, LKR, NPR and SGD. A currency option does not imply tax, localization, or foreign-exchange support. Company creation currently adds six generic starter accounts; the researched industry/country catalog is not activated.

| Internal function | Current behavior |
|---|---|
| `pl_create_user()` | Creates a password-hashed account through controlled CLI setup; no public registration route |
| `pl_authenticate()` | Verifies password/active state and durable account/client attempt limits; returns a user identity or generic denial |
| `pl_require_company_access()` | Checks current active membership; owners/accountants can write, viewers can read |
| `pl_create_company()` | Atomically creates company, owner membership, one book, initial open period, and six generic accounts |
| `pl_post_journal()` | Validates scope, dates, base currency, active accounts, precision, balanced lines, open period, and duplicate-prevention key before atomic posting |
| `pl_get_journal()` | Retrieves a scoped durable journal reference and its account lines |
| `pl_reverse_journal()` | Posts one exact linked reversal on or after the original date in an open period |
| `pl_trial_balance()` | Returns scoped net account balances, debit/credit movements, and reconciled totals, optionally to a date |

Posted headers and lines reject updates/deletes through database triggers. The posting interface is the authorized application write path; database administrators still require tightly controlled access. The proof validates an existing period's state at posting time; period-management screens and a complete fiscal-calendar workflow are future work.

Posting serializes on the selected book, uses current locking reads for permissions and idempotency, and can replay a complete internally owned database transaction after a detected InnoDB deadlock, with at most three retries. Caller-owned transactions are never independently replayed. Callbacks must contain database work only; email, provider, or other external side effects cannot be placed inside a retryable transaction.

Migrations are CLI-only and keep checksums and application receipts. An interrupted DDL migration is a recovery case, not an instruction to blindly replay partial SQL. Fresh install and replay evidence, dependency checks, concurrent-write regressions, and isolated backup restoration are recorded in [Validation](VALIDATION.md). Design selection is recorded separately; the technical proof does not establish a finished interface, production readiness, general hosting compatibility, or accountant approval.

### Sprint 02 implementation contract

Add browser login and logout around the existing authentication/session helpers, and essential company onboarding around the existing company/book service. Save receipt/expense drafts as editable business documents; posting submits their authorized, validated financial content to the central journal interface. A durable link must connect each posted source document to its journal and report drilldown. Show Save draft only during editing, preserve user input on validation failure, and prevent stale or repeated actions from silently overwriting or duplicating financial work.

Reports and linked reversals retain the same company/book permission boundaries. The Review Console uses a persistent list/detail layout on desktop and an intentional list-to-detail path on small screens. The initial sprint does not add a second routing/authentication/database stack, historical import execution, alternative books, or country tax behavior. Record actual route and schema details once implemented; do not treat this contract as a completed API inventory.

### Browser implementation checkpoint

The current router implements the following server-rendered interfaces. These are internal browser routes, not a promised public JSON API. HTML GET screens and POST actions share the existing bootstrap, authentication, CSRF, company/book permissions, and posting services. `/health` remains a separate JSON endpoint.

| Browser interface | Routes and behavior |
|---|---|
| Session and business context | `GET/POST /login`, `POST /logout`, `GET /companies`, `POST /company/select`; root redirects according to authentication state |
| Setup | `GET/POST /onboarding` previews and confirms the starter chart; `GET/POST /setup/review` explicitly maps prior-foundation accounts and confirms review without replacing their history |
| Drafts and review | `GET /transactions`, `/transactions/detail`, `/transactions/new`, `/transactions/edit`; `POST /transactions/save` keeps draft revisions and recoverable errors |
| Accounting writes | `POST /transactions/post` uses the central posting interface and returns the same durable journal on duplicate confirmation; `POST /transactions/reverse` posts a linked reversal |
| Reports and help | `GET /reports`, `/reports/trial-balance`, `/reports/account`, `/reports/profit-loss`, `/reports/balance-sheet`, `/journals/detail`, and `/help`; reports read posted records and drill down to account/journal/source detail |
| Cash scenario | `GET/POST /reports/cash-forecast`; scoped posted opening cash plus explicit weekly inflow/outflow assumptions over 1–52 weeks; no ledger writes or automatic prediction |
| General-shop POS | `GET /pos`, `POST /pos/checkout`, `GET /pos/receipt?id=...`; server-priced sample cart → atomic cash receipt/journal/item snapshot → printable receipt |
| Restricted demo entry | `GET /login` shows the public sample entry in demo mode; `POST /start` creates/reuses the visitor's private synthetic company; the normal business-setup and company-switch routes are blocked |

Migration `002_product_slice` adds business setup/sample state, stable semantic account roles, a versioned template installation snapshot, and receipt/expense documents. A document carries its company/book, exact amount, date, source fields, creation identity, revision, and durable journal link. Posted source documents reject updates/deletes through database triggers. Companies from the original foundation enter `review_required`; an existing business chosen during new onboarding enters `opening_required` and cannot bypass missing opening data through the prior-foundation review route.

The active preliminary template is `core-starter` version `1.0.0`. It is a small neutral chart, not reviewed country/industry accounting coverage. The isolated core sample and its version/digest are recorded with setup; the broader industry research packs remain separate. The current schema has migrations `001_foundation` through `005_demo_period_guard`, with **15 tables and 9 accounting/demo/POS guard triggers**. Applied migration files and their checksums remain immutable; the demo period guard is additive migration 005, not a rewrite of 003.

A local HTTP acceptance checkpoint passed **26 checks** using its own synthetic company: session/CSRF boundaries, setup preview/confirmation, retained and escaped invalid inputs, draft report isolation, repeated posting identity, report/source links, invalid/repeated-state recovery, linked reversal, and closed-period rejection preserving the draft. The period check only closed/reopened that synthetic company's period. This is HTTP integration evidence; browser interaction, responsive/keyboard usability, final demo checks, and publication require their own receipts in [Validation](VALIDATION.md).

### Reports and cash scenario

`pl_profit_loss()` reads posted income/expense movements for an inclusive business-date range. `pl_balance_sheet()` classifies posted balances as assets, liabilities, and recorded equity, with accumulated unclosed earnings included once; the result reconciles assets against liabilities plus equity. `pl_cash_balance()` reads scoped asset accounts carrying the cash/bank role. Reversals remain posted records and naturally change their report effect; drafts do not appear.

`pl_cash_forecast()` is an exact-decimal scenario function. The browser takes the selected date's authorized posted cash balance and explicitly entered weekly cash in/out over 1–52 weeks, then shows closing balances and the first negative point. It does not infer future revenue, outstanding invoices, bills, stock, or tax. It neither changes the ledger nor represents a cash-flow statement. Full receivables/payables/stock reports await their respective modules and cannot be derived honestly from this small receipt/expense ledger alone.

### POS posting contract

The [POS showcase](POS.md) uses `pl_pos_catalog()`, `pl_checkout_pos()`, and `pl_get_pos_receipt()` with the existing database/authentication/transaction interfaces. Only catalog SKU/quantity, date, cash tender, catalog digest, and stable checkout identity reach the checkout service. The route verifies CSRF and company/book identity before removing those transport fields; unexpected price/total fields still fail validation.

Checkout locks the book, checks posting permission/readiness, resolves prices on the server, and calls `pl_save_document()` and `pl_post_document()` inside the same outer transaction as the immutable `pl_pos_sales` snapshot. The cash journal amount is the sale total; cash received/change remain receipt facts. Same-key/same-content retries return the existing receipt; conflicting content is rejected. A closed period or late snapshot-insert failure rolls back the source, journal, lines, and snapshot together. Migration **004_pos_showcase** adds the scoped snapshot table, uniqueness/check constraints, and update/delete guards. The six fictional products are not stock records, and their prices are not FX conversions.

The independent local POS HTTP checkpoint passed **17 checks**, including a USD 12.75 sale, 20.00 cash/7.25 change, source/journal identity, repeat checkout, retained invalid inputs, and CSRF/scope/tamper rejection. The combined target-runtime suite passed **54 tests** including eight POS cases and separate-process duplicates. These tests do not establish receipt-printer compatibility or production retail suitability.

### Restricted public demonstration

Demo mode requires the literal isolated demo database, a separate restricted web database user, and a valid demo state. Migration **003_demo_isolation** adds generation/visitor records; migration **005_demo_period_guard** blocks fiscal-period changes by the demo web path. Each visitor receives a synthetic account and private sample company, with server-enforced membership, generation/expiry checks, and bounded visitor/document capacity. Business setup, administration, destructive database privileges, posted edits/deletes, and fiscal changes are unavailable to visitors. Normal authorized draft/post/reversal/POS services retain their accounting checks.

The CLI-only `tools/demo-reset.php` validates the exact demo target and synthetic markers, holds a maintenance lock shared with requests, and rebuilds only that isolated database. Reset credentials never enter the web runtime. An expired generation rejects work until refresh; sessions from a prior generation are invalid. An hourly backend schedule and correctly prefixed `/demo` links/forms/assets are required at deployment. Local reset/restricted-user/isolation proof passed; the actual live schedule, uptime, and public browser behavior require release evidence. Never use this reset against the development, test, or customer database.

### Current regional and presentation groundwork

The English UI formats journal UTC event instants using the terminal's IANA timezone and uses the local day only for untouched new-date defaults. Stored accounting dates are presented as date values, without timestamp conversion. Money display uses exact decimal strings, Western grouping, and two decimals unless the stored value needs four; explicit localized parsing, user formatting settings, English translation catalogs, additional languages, and FX posting remain future work.

`pl_regional_suggestion()` performs at most one bounded backend country lookup per browser session, including caching failure or local-network skip. For a public client address it sends that address to the fixed country.is HTTPS endpoint; it requests no precise location and does not log the raw address/provider URL/body. Private/local addresses never call the provider. Forwarded addresses are accepted only behind explicitly configured exact trusted proxies, using the nearest untrusted hop rather than an arbitrary leftmost value. A timeout/failure leaves manual choices available without per-page retries.

The bundled [CLDR country defaults](../resources/locale/README.md) supply country/locale/currency metadata. Only supported base currencies (USD, EUR, GBP, PKR, INR, MYR, BDT, LKR, NPR, SGD) can be suggested; unknown, ambiguous, or unsupported currencies require manual choice. The shared `pl_base_currency_options()` list controls both selectors and server validation. Malaysia, Bangladesh, Sri Lanka, Nepal and Singapore reuse their existing CLDR entries, and cached hints refresh their local support metadata without another API call. The suggestion never changes an existing company's currency, chooses a tax regime, translates the interface, or fetches exchange rates. Provider behavior is tested using a mock transport, separately from any live deployment lookup.

## Researched template and sample-data integration

The [Chart of Accounts research](coa/README.md) proposes stable semantic account keys, a neutral core, an industry layer, and reviewed country packages. The [business sample packs](sample-data/README.md) use original synthetic data and account roles to describe linked business scenarios. Both remain separate from runtime installation until their format, provenance, accounting behavior, and company isolation are reviewed and implemented.

The future wizard should preview the resolved chart, explain required accounts, allow import mappings, and pin the confirmed template version to the company. Application upgrades must not replace a customer's chart. A sample-company choice must create an isolated demonstration context; real-company setup must never silently load sample customers, products, balances, or transactions. Opening documents and balances must reconcile without posting AR/AP twice. See the research model for the worked cutover example and the roadmap for delivery gates.
