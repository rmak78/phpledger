# Account statements: local core slice

Validated on 15 September 2026 (Asia/Karachi), in branch `codex/account-statements` based on `bd415d4d95e9bd6999a8825ea0dbf9e0db31a830`. This work is local and is not included in the published v0.1.1-preview package, website or demo. The existing isolated worktree preserves unrelated root-checkout work.

## Implemented behavior

`GET /reports/account` now shows every authorized ledger account's opening balance, period debit/credit totals, running balance and closing balance. Opening means posted history strictly before the optional From date; closing includes postings through the inclusive Through date. Leaving From blank starts with zero before recorded history. Initial business opening-balance entry/import remains a separate, unfinished cutover workflow.

Rows use accounting date, journal ID and line number order. Exact decimal window totals are calculated before pagination; the second page carries the first page's ending balance. Summary totals cover the entire period. Credit balances display an absolute amount with Cr, debit balances with Dr, and zero has neither suffix. Inactive accounts retain their history, drafts are excluded and dated reversals affect their actual accounting date. Unresolved setup displays an opening-balance review notice.

The service reads within a shared-book transaction so concurrent posting cannot split its opening, totals and rows into different states. Company/book/account membership checks remain on the server. The existing internal `balance` result retains its period-movement meaning; explicit opening/closing and page balances are additional fields. No posting behavior or stored journal is changed by viewing a statement.

## Executed checks

| Check | Result |
|---|---|
| PHP 8.5.10 / MySQL 8.4 integration suite | **73 tests, 0 failures**; includes six new statement regressions and extended reversal assertions. |
| Statement financial cases | Inclusive boundaries; exclusion of later postings/drafts; empty periods with nonzero opening; inactive credit account; four-decimal sign changes; backdated journals and tied lines; 52-line pagination; all five account types reconciling to trial balance; viewer and denied cross-scope reads. |
| Existing integration coverage | Authentication, CSRF, posting/reversal, concurrent duplicate writes, rollback, periods, documents, POS and installer/migration replay remained passing. This run used the existing isolated test database. |
| PHP syntax | **59 files, 0 failures** using `tools/lint.php`. |
| PHPStan | **No errors**, repository configuration, level 5. |
| Local browser | **19 checks passed** at `127.0.0.1:18205`: exact statement totals, journal/source drill-down, pagination with retained dates, page carry-forward, empty-period and credit statements, Inter amounts, keyboard focus/link reachability, reduced motion and four viewport overflow checks. |
| Viewports and visual review | Screenshots at 1440×1000, 768×1024, 390×844 and 320×740; no page-level horizontal overflow. Desktop and mobile screenshots visually inspected; the narrow account table scrolls inside its labelled, focusable region. |
| Repository whitespace | `git diff --check` passed. No runtime JavaScript changed. |

The browser fixture is a separate synthetic Willow Studio company: opening 1,000, receipt 200 and expense 125 give closing 1,075. A later 51-line synthetic receipt proves page carry-forward and closing 1,126. No customer data was used.

Repeatable commands from the isolated worktree:

```powershell
docker compose -f .cache/pos-runtime/compose.yaml run --rm test php tests/run.php
docker run --rm --entrypoint php -v C:/phpledger/.cache/sprint03-pos:/work:ro -w /work phpledger-test tools/lint.php
docker run --rm --entrypoint php -v C:/phpledger/.cache/sprint03-pos:/work -v C:/phpledger/vendor:/work/vendor:ro -w /work phpledger-test vendor/bin/phpstan analyse --no-progress --memory-limit=512M
npx --yes --package @playwright/cli playwright-cli -s=phpledger-pos run-code --filename=.cache/pos-runtime/account-statement-browser.js
```

Local-only evidence is retained in `.cache/pos-runtime/account-statement-tests.log`, `account-statement-browser.log`, and `output/playwright/account-statement-{desktop,tablet,mobile,narrow,page-two}.png`. These ignored artifacts and the browser's synthetic IDs are not portable release fixtures; the committed PHP regression cases create their own fixtures.

## Files and decisions

Changed application files: `www/phpledger/includes/functions/document_functions.php`, `www/phpledger/public/index.php`, `www/phpledger/templates/views/account.php`, and `www/phpledger/public/assets/app.css`. Financial tests are in `tests/report_test.php`.

Changed documentation: `README.md`, `docs/ARCHITECTURE.md`, `docs/ROADMAP.md`, new `docs/MODULE-ROADMAP.md`, and this receipt. The user clarified core-first delivery, optional POS/AR/AP/tax modules, industry-specific POS interfaces, and future API/MCP access. The module roadmap records the build order and dependencies; it does not implement a module registry, API, MCP server or invoicing.

References read: repository instructions, README, architecture, roadmap, design guidance, relevant source/tests and prior release receipts. Module planning used the official [OpenAPI specification](https://spec.openapis.org/oas/v3.2.1.html), [MCP authorization](https://modelcontextprotocol.io/specification/2025-11-25/basic/authorization) and [MCP tools](https://modelcontextprotocol.io/specification/2025-11-25/server/tools). No Google Drive documents were read for this change.

## Boundaries and remaining validation

No migrations; no schema changes; no raw secrets exposed. External calls: read-only official documentation/tool retrieval only, plus loopback browser and local Docker checks. No live application, website, GitHub publication or production changes; no messages or payments sent.

Full fresh-install/upgrade/backup-restoration acceptance was not repeated for this read-only reporting slice; prior package evidence remains in its original receipt. No sustained large-ledger benchmark, independent accounting review, observed user session, screen-reader audit or native 200% browser-zoom verification was performed. The responsive checks are not a full accessibility certification. Per-page reads are coherent; separate page requests may reflect intervening backdated postings, so an issued immutable statement/export still needs the roadmap's reproducibility work.

Opening/cutover entry, chart management, general-journal screens, full period close, bank reconciliation, API/MCP and optional-module lifecycle remain next work. No country-framework support is claimed by this statement improvement.
