# Contributing to PHP Ledger

PHP Ledger is being rebuilt as a self-hosted accounting application for SME owners, accountants, and bookkeepers. The current work is a local development milestone. Read [the README](README.md), [repository instructions](AGENTS.md), [architecture](docs/ARCHITECTURE.md), and [current sprint](docs/SPRINT-02.md) before changing code.

## Get the development environment running

Follow the canonical [local development instructions](README.md#local-development). Use Docker Compose for the PHP 8.5/MySQL 8.4 environment, apply the new versioned migrations, and use the controlled command-line account installer when an account is needed. The account installer accepts a password through standard input or a private shell environment variable; never put a password in a command argument or report.

Preserve an existing `.env`. Use distinct random local database passwords and synthetic records. Serve only `www/phpledger/public`; the repository root and its legacy SQL dumps are historical reference material. Never point the new runtime at a customer's or production database.

The isolated test profile uses the disposable `db_test` service and `phpledger_test` database. Its reset behavior is for synthetic tests only. The backup verification command creates and removes its own isolated restore database. Do not run destructive reset commands against the main development database.

## Work within the existing application

- Reuse the shared bootstrap, explicit route handling, MeekroDB connection, permissions, CSRF helpers, and contextual escaping. Do not add another framework, ORM, router, or authentication system.
- Keep financial rules in typed functions independent of request variables, HTML, and sessions. All posted financial writes go through the central posting interface; use exact decimal strings, balanced lines, durable sources, and duplicate prevention.
- Scope reads and writes to the authorized company and book. Recheck write permissions on the server, reject closed-period postings, and correct posted entries with linked reversals.
- Make necessary schema changes in a new versioned migration. Do not edit an already applied migration or execute the historical root installation scripts.
- Use the approved Review Console direction and Inter typography from [Design](docs/DESIGN.md) and [Brand](docs/BRAND.md). Keep owners' first tasks clear and accountants' repeated work efficient. Preserve entered values on errors and support keyboard and narrow-screen use.
- Keep scope focused and preserve unrelated changes. Use synthetic fixtures and original or appropriately licensed assets. Do not copy proprietary application code, datasets, or screenshots into the product.

## Checks before review

Run the README's Docker test, manifest validation, dependency audit, and relevant backup checks. The standard test command includes PHP lint, static analysis, sample-pack validation, and accounting/access tests. Lint changed JavaScript when applicable. Add tests when they cover meaningful financial, permission, state, concurrency, or recovery behavior.

For UI work, inspect the changed screens at desktop, tablet, and mobile widths and with a keyboard. Check empty, loading, error, saved, and posted states; retain form values and provide a useful focus path after validation failures. Screenshots and automated checks do not replace observed usability sessions or accounting review.

Update the existing documentation for changed routes, commands, permissions, states, installation behavior, and user-visible features. Report exactly what passed, failed, or could not be checked. Preserve dated validation receipts; add new evidence instead of rewriting historical results. Hosted CI is not verified merely because the workflow exists locally.

## Issues and pull requests

For a bug, include reproduction steps, expected and actual behavior, the affected application version or commit, and relevant runtime/browser information. Share only sanitized logs and synthetic examples. For a feature, describe the user's task, current difficulty, desired outcome, and relationship to the roadmap.

Use the pull request template to record implementation, validation, documentation, migration/schema effects, and remaining limits. Do not claim a feature is shipped, compliant, or approved by accountants without evidence. A contribution or design approval does not authorize publishing, deployment, external messages, provider calls, or payments.

## Licensing and release status

The project intends to keep its software and modules open source, but the project license and legacy provenance review are unresolved. Read [License review](docs/LICENSE_REVIEW.md) before proposing code or asset reuse. Preserve existing third-party notices; do not infer a license from public repository visibility or add an SPDX identifier, CLA, or DCO policy without an explicit decision.

Local technical review can continue while those decisions are pending. A public contribution/funding launch and distributable release require the documented rights and license review. No support response time, production hosting coverage, or regulatory approval is implied by this guide.
