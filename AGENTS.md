# PHP Ledger repository instructions

## Read first

Read `README.md`, `docs/ARCHITECTURE.md`, and `docs/ROADMAP.md`, then the relevant product/design documentation and current code. `docs/LEGACY.md` preserves historical context, not current installation guidance. The user-approved roadmap permits new foundation schemas and versioned migrations; do not add unrelated schema changes.

## Working boundaries

- Work locally unless the user explicitly authorizes a live action. Do not push, deploy, publish, send messages, or collect payments merely because a design or implementation was approved.
- Preserve unrelated work and historical files. Never run `legacy/install/*.sql` dumps against the new database. Never serve the repository root.
- The new application is `www/phpledger`; its only web document root is `www/phpledger/public`. Use the new shared bootstrap and existing helper interfaces. Do not load legacy root code into the new application.
- Retain BixiSoft's modular PHP/MeekroDB architecture. Do not add an alternative framework, ORM, authentication stack, router, or second database connection layer without an explicit design decision.
- Keep secrets and real customer data out of files, logs, fixtures, screenshots, commits, and responses. Use synthetic fixtures and local-only services for development.

## Accounting and security

- Use one central posting interface, fixed-precision money, transactions, duplicate prevention, and a durable source reference. Never write posted journal rows directly from a screen.
- Enforce company/book membership and action permissions on the server. Include CSRF protection on browser POST actions, contextual output escaping, and bound database parameters.
- Keep posted entries immutable. Corrections use traceable linked reversals; closed periods reject new postings. Reports and imports must use the same company/book scope as writes.
- Keep financial rules in small typed functions independent of request globals, HTML, and sessions.
- Historical imports must preview and validate before confirmation and must reconcile opening AR/AP with unpaid documents at the cutover date. Do not silently guess missing accounting data.
- Multiple accounting books require an approved definition and reconciliation rules before implementation; a company/book identifier in the foundation is not a completed multi-book feature.

## Design, documentation, and validation

- Use `docs/DESIGN.md` and the selected direction once approved. Before selection, identify screens as candidate directions or foundation previews.
- Update the existing docs when behavior, commands, routes, permissions, workflows, or deployment expectations change. Keep implemented facts distinct from targets and pending gates.
- Lint changed PHP and applicable JavaScript. Run relevant financial/access tests and fresh-install checks. Browser-check changed screens at desktop, tablet, and mobile widths where practical.
- State exactly what ran and what was skipped. Never equate a build, screenshot, or passing technical test with accounting review or observed usability success.
- Report changed files, tested workflows, references read, migration/schema status, secret exposure, external/live calls, production impact, test results, and remaining risks.

## Reference boundaries

BixiSoft conventions were consulted from local Agency75 architecture documentation. That project's CRM workflows, production settings, credentials, providers, and deployment arrangements are not PHP Ledger requirements. Required external references should be read if available; reference access does not authorize editing them.
