# Local revival development

These instructions apply to the modern source containing `compose.yaml`, `composer.json` and `www/phpledger`. The current source tree contains the modern application; historical code remains only in Git history. Use the public [Wiki](https://github.com/rmak78/phpledger/wiki) for visitor documentation and package availability. The [foundation evaluation package](https://github.com/rmak78/phpledger/releases/tag/v0.1.0-preview) includes production dependencies; this page covers development from source.

## Start the verified environment

Use Docker Compose for PHP 8.2+ and MySQL 8.4. Preserve any existing `.env`. For a fresh checkout, copy `.env.example` to `.env` and privately set independent random development database passwords.

```powershell
docker compose up -d --build
docker compose exec -T web php www/phpledger/install/migrate.php
```

Open the local sign-in screen at `http://127.0.0.1:18200/login`. Serve only `www/phpledger/public`, never the repository root. Historical installation dumps are not part of the revival; never run them against the modern database. Use synthetic data and a separate development database.

Create the first administrator through the controlled command. Supply its password through standard input or a private `PL_ADMIN_PASSWORD` variable; never put the password in command arguments or committed files.

```powershell
docker compose exec -T -e PL_ADMIN_PASSWORD web php www/phpledger/install/create-admin.php --email=owner@example.test --name=Owner
```

Clear the private shell variable after use. Sign in, create a business or isolated sample, and follow setup through the first receipt or expense. Existing businesses require reviewed opening balances before posting. Installation, business onboarding and historical-data cutover are separate workflows.

## Verify changes

```powershell
docker compose --profile test run --rm test composer check
docker compose --profile test run --rm test composer validate --no-interaction
docker compose --profile test run --rm test composer audit --no-interaction
./tools/verify-backup-restore.ps1
```

The test profile uses `phpledger_test` in its separate `db_test` service. The restore check creates and removes only its own isolated test database. See [validation receipts](VALIDATION.md) for exact executed checks and remaining limits.

Optional browser-facing acceptance scripts are [core HTTP](../tests/http-smoke.py), [POS HTTP](../tests/pos-http-smoke.py) and [demo HTTP](../tests/demo-http-smoke.py). They use local synthetic records; read each script's scope and required private inputs before running it. Do not broaden their targets to production or real customer books.

## Opening cutover, periods and bank reconciliation

The local `website-redesign` branch extends the development application. Back up existing development data, then apply the complete versioned migration chain with `docker compose exec -T web php www/phpledger/install/migrate.php`. The public 0.1.0-preview ZIP/demo do not acquire these features from a local source change.

| Route | Workflow |
|---|---|
| `/opening-balances` | Existing-business setup → balances/manual or CSV → saved validated preview → explicit confirmation → ready after cutover. Source evidence and correction history remain visible. |
| `/periods` | List periods/history, create a nonoverlapping range, close with reason, owner-only reopen. A fresh revision prevents stale changes; retries have durable receipts. |
| `/bank-reconciliation` | Preview/import statement, choose each match, inspect outstanding entries, then explicitly complete with zero adjusted difference. Consecutive statements carry the prior closing balance. |

An existing company's accounting start date is its cutover **closing date**. Bring reviewed balances through that day and date ordinary transactions afterward. This also lets a first bank statement begin the next day with the confirmed cash opening. Full AR/AP operations are not present: the unpaid register is the cutover evidence, not an invoicing, collection or bill-settlement module.

CSV is UTF-8 with comma separators, a header in the exact order below, ISO `YYYY-MM-DD` dates, and decimal amounts without grouping separators/exponents. Limits are 500 data rows and 512 KiB per CSV. Zero-side amounts must be `0`; each nonzero balance/transaction uses only one side. Imports reject malformed quoting, unknown/duplicate account codes, missing data, wrong precision, repeated document/transaction identities and unbalanced totals. XLSX and automatic column/bank-format guessing are not implemented.

```csv
account_code,debit,credit
1000,1000,0
1100,300,0
2000,0,200
3000,0,1100
```

```csv
kind,account_code,party,reference,document_date,due_date,outstanding
receivable,1100,Synthetic customer,INV-01,2026-08-15,2026-09-15,300
payable,2000,Synthetic supplier,BILL-01,2026-08-20,2026-09-20,200
```

The two examples reconcile an opening cutover at 2026-09-01: cash 1,000 + receivables 300 = payables 200 + equity 1,100. Unpaid amounts are source evidence for those control balances and are never posted a second time. Positive unpaid invoices/bills are supported; credit notes, advances and stock valuation need a separately reviewed workflow. Use the actual scoped account codes, which may differ from the example.

```csv
date,reference,description,money_in,money_out
2026-09-02,BANK-001,Synthetic receipt,125,0
2026-09-03,BANK-002,Synthetic expense,0,25
```

For that bank example, enter opening 1,000 and closing 1,100 and explicitly confirm the first cleared baseline. The corresponding receipt/expense must already be posted through the normal services before matching; import/reconciliation creates no financial entries. Review bank-only fees or other missing transactions and record them through the normal accounting workflow before matching. A first baseline with unresolved earlier outstanding items is rejected; resolve or reconcile the earlier history first. Completion protects history from backdated postings even if a period is reopened.

An incorrectly imported draft can be cancelled with a reason after explicitly removing its matches. The original statement, rows and match/unmatch history remain; a corrected import may reuse its bank references. Completed statements remain immutable. Split or aggregate matches are not supported: each bank row matches one posted line with the same signed amount.

```powershell
docker compose --profile test run --rm test composer check
python tests/accounting-http-smoke.py
docker compose --profile test run --rm -e PL_DB_USER=root -e PL_DB_PASSWORD=local-test-root-only test php tools/verify-upgrade.php
./tools/verify-backup-restore.ps1
node www/website/build.mjs
node www/website/check.mjs
```

`accounting-http-smoke.py` is hard-limited to `http://127.0.0.1:18200`, creates isolated synthetic owner/viewer companies and keeps random credentials in memory/stdin. It retains its synthetic data for inspection. Unit/financial tests use only `db_test`; upgrade/restore scripts create, validate and remove their own randomly named databases in that disposable test service. The literal root password shown is solely the documented disposable test credential.

## Company modules

Open `/modules` in an installation to inspect the optional cash POS showcase and recent changes. An owner enters a reason to enable, disable or apply a reviewed version. Accountants/viewers may inspect status/history; the public demo cannot administer modules. Ordinary companies default to POS disabled, including after upgrade. Explicit new sample-company provisioning enables its showcase through the same audited service. Core accounts/journals/reports remain available with every add-on off.

Run the existing preflight/migrations before using the new source: `010_module_lifecycle` adds state/audit tables and never activates existing companies. Retain both `006_*` migrations unchanged; the next migration number is `011`. A changed manifest requires owner review, and missing or mismatched migration receipts block POS operation. Disabling preserves receipt/source/journal history; new POS review/checkout/retry requests fail on the server. See [contracts, compatibility and recovery](repository/sprint-05/MODULE-FOUNDATION.md).

Run `python tests/module-http-smoke.py` for 26 local-only HTTP assertions with synthetic owner/viewer books. Run `composer check` through the existing test container for service, concurrency and rollback tests; `./tools/verify-demo.ps1` verifies isolated sample provisioning/reset in `db_test`. No actual API/MCP endpoint or machine credential is added by this sprint.

## Repository working boundaries

### Core CSV exports

**Account ledger entry point:** `/reports/account` without an account ID now opens the company-scoped account chooser. Reports, Transactions and Journals link directly to it. Choose an account and optional date range; the existing statement service supplies opening, debit/credit movement, running and closing balances across all pages. On phones, each table row lays out its date/source, debit, credit and running balance without horizontal scrolling. Draft document totals are not account balances; statement calculations continue to include only posted journal lines. Existing `id`, `as_of`, `from` and `page` links remain supported and authorized on the server.

`GET /reports/export?report=trial-balance|account|profit-loss|balance-sheet&to=YYYY-MM-DD` downloads a CSV through the existing signed-in company/book scope. Account statements accept `account_id` and optional `from`; profit and loss requires `from`. Each report screen links the current date selection to its export. Viewers may export their authorized books. All statement pages are included, with a 10,000-movement limit that rejects oversized exports before sending any CSV. A shared book transaction keeps pages and totals coherent. CSV preserves four-place decimal strings, business dates, scope, readiness and source references; potentially executable spreadsheet text receives an apostrophe prefix. The CSV is a management preview, not an issued statutory statement.

### Consolidated installation checks

`tools/verify-upgrade.php` accepts `fresh`, `foundation`, `core-0.1.2` or `opening-local`. Run it with the existing disposable-test root invocation above. Each mode creates/removes its own random database and preserves all existing test/development data. The two `006_*` files have distinct full migration identities from separate branches; preserve both names and original checksums. The migration runner uses full filenames, not just numeric prefixes. Module lifecycle occupies `010`; future migrations continue from `011`.

The PowerShell restoration check explicitly uses UTF-8 for native process input/output so non-ASCII descriptions survive dump/import. Run restore checks after the test suite completes, without concurrent database writes.

Read [architecture](ARCHITECTURE.md), [contribution guidance](../CONTRIBUTING.md) and [repository instructions](../AGENTS.md). Reuse the bootstrap, MeekroDB helpers, explicit routes and central posting service. Preserve historical files and migration receipts. Configuration, dependencies and storage remain outside the public document root.

The [demo runbook](DEMO.md) covers separate synthetic storage, restricted runtime permissions, hourly UTC reset and deployment. The [roadmap](ROADMAP.md) preserves future language/formatting/FX, imports, regional accounting, inventory, production POS and industry modules. Development checks are not accounting sign-off, observed usability evidence or a stable-release claim.
