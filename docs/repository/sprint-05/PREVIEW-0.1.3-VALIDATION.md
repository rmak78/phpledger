# Account ledgers and module foundation — 0.1.3-preview

Date: 15 September 2026. The owner requested all completed work live first for review, then API/MCP reads, controlled commands and optional AR/AP. SEO discovery and marketing preparation run in parallel at high priority. This receipt extends the [core checkpoint](CORE-COMPLETION.md) and [module foundation](MODULE-FOUNDATION.md); it does not mark subledgers or reviewed financial reporting complete.

## Running-balance correction

The prior account service already calculated opening, debit/credit movement, running and closing balances across pages. The browser audit found the running column outside the initial mobile viewport and no direct account-ledger chooser from the Reports hub. The account-statement table is now presented as complete entries on mobile, with debit, credit and running balance visible together. `/reports/account` accepts a missing ID to show the scoped account chooser; existing account/date/page links retain their service checks. Reports, Transactions and Journals provide direct ledger entry points. A journal's account links open its statement. Financial calculations and posted data are unchanged.

The same browser run found a 3px overflow in the owner report's three-column mobile metrics. Those metrics now stack with readable amounts. This is presentation repair, not a new cash-flow or subledger report.

## Local validation before release

| Check | Result |
|---|---|
| Full `composer check` | 121 tests, zero failures; 90 PHP lint checks; PHPStan zero errors; sample validator passed. |
| Core HTTP | 75 assertions passed, zero skipped viewer checks; includes account chooser, malformed dates, chart/general-journal workflows, scope, permissions, posting/reversal and statement reconciliation. |
| Browser via Playwright | 14 assertions and 15 route/width captures at 1440/768/390px. Chooser navigation, account persistence, date-range opening carry and exact 1,000 → 1,125 → 1,100 running balances passed. No document overflow, broken images, page errors or external requests. |
| Website build/check | 12 HTML documents, zero errors/warnings; new release article, sitemap and RSS entries prepared. Final artifact metadata is set after package construction. |
| Earlier consolidated core/module acceptance | See linked receipts for two-period reconciliation, 26 module HTTP assertions, default-off/enable/disable/history, concurrency and rollback, fresh/upgrade/restore and actual ZIP proofs. |

Ignored local browser evidence: `output/playwright/running-balances/before/` and `after/`. Initial findings are retained; successful checks use the after receipt. Technical and visual checks do not establish independent accounting/security review or observed usability success.

## Release preparation

The existing public demo is a separate synthetic database and runtime. A fresh read-only SSH preflight verified live 0.1.2 at 17 tables, 13 guard triggers and six unchanged migration receipts. The additive upgrade installs the complete eleven-file chain, including both distinct 006 identities, without renumbering or changing applied checksums; expected totals are 27 tables and 35 guards.

Deployment preparation preserves the database container/volume and exact compatible PHP image, stops web/scheduler writes for backup and migration, verifies preserved data, then uses the established isolated demo reset so new visitor samples receive their enabled POS module. Temporary demo sessions are invalidated by that normal sample refresh. Private backups and recovery helpers stay outside published source. The website is staged as a separate static release.

Actual package identity, GitHub/Wiki publication, hosted cutover and fresh live checks will be recorded below after execution. Preparation is not publication.

## Scope and references

Changed files: account controller/view; Reports, Transactions and journal views; responsive CSS/layout; core HTTP tests; release README/Wiki/roadmap/development docs; website product/download/roadmap/news/static sources and generated output; campaign execution kit and read-only marketing audit tool. No new migration or accounting rule is added by the running-balance fix. This release includes previously validated migrations through 010, which require hosted schema changes.

References read: repository instructions, README, ARCHITECTURE, ROADMAP, MODULE-ROADMAP, DESIGN, accounting/reporting gap register, release/package/demo runbooks, Claude website/marketing plan and existing hosting helpers. Google Drive documents: none. Raw secrets exposed: no. External work before deployment consists of read-only GitHub/public SEO/official reference checks and authenticated hosting preflight. The publication section records subsequent authorized live changes separately.

Outstanding: customer/vendor subledgers, allocations/aging, reviewed report definitions/mappings/comparatives/disclosures and immutable issued snapshots; qualified accounting/security review; supported-host/load and representative-user acceptance; XLSX/detailed historical imports; production inventory/tax/POS and foreign-exchange accounting. API/MCP read access is the next implementation milestone.

## Publication and report-layout follow-up

The authorized 0.1.3 release was published on 15 September 2026. Runtime/package source is `39a68cc5aad68bdd1d690ea1805ba7b16b7e5e72`; GitHub prerelease `v0.1.3-preview` was published at 12:13 UTC, Wiki commit `53be1ca`, website `website-redesign-20260915-121347` at 12:14:46 UTC. The 1,326,659-byte ZIP SHA-256 is `0d9d4afa72ab8b9e9478258c0cc7d4f61f0feed1bfcffa18b6980d2bb5f06d13`; a fresh public download and independent rebuild matched. This exact package passed fresh installation, three upgrade baselines and module enable/sale/export/disable/history/reversal proofs.

The hosted upgrade verified a real backup restore, preserved pre-existing protected rows across five additive migrations, and then used the explicitly selected isolated-demo refresh. It retained the same PHP image and MySQL container/volume. The live schema has 27 tables, 35 guards and 11 unchanged/full-filename receipts. The website's 103 published static files and canonical/private-path behavior passed byte/status checks. The follow-up public SEO audit passed ten sitemap pages. IndexNow accepted those URLs with HTTP 202: key validation remains pending, indexing is not proved. Private credentials and backups were not published.

A subsequent read-only live browser pass checked 39 route/width combinations. Running balances were visible on desktop and mobile, and the account CSV returned 200 with running and closing 875.0000 values. It found the running column outside the initial 768px viewport. The owner also reported horizontal scrollbars inside Profit & Loss and Balance Sheet sections. The cause was shared `.statement-table` styling: the account ledger's 850px minimum width also affected the financial-report tables.

### 0.1.4-preview correction, prepared locally

The account ledger now uses its own `.account-ledger-table` selector, preserving the financial reports' existing two-column layout. Its complete-entry layout extends through 980px so running balances remain visible on tablets. Account filters wrap within their panel at intermediate widths, with an explicit browser check for left/right clipping. The shared layout updates the CSS cache key. Accounting calculations, routes, permissions, schemas and migration files are unchanged.

Local validation: both changed PHP templates passed `php -l`; `git diff --check` passed. The targeted browser run passed 19 assertions across 42 route/width captures (1440,1024,980,768,390,320px), including Profit & Loss, Balance Sheet, account statement/chooser, Reports, Transactions and Journals. All financial-report sections and account ledgers fit without internal horizontal scrolling; running balances, filter carry, account selection and the exact 1000/1125/1100 sequence passed. No document overflow, broken images, page errors or external requests. Evidence: `output/playwright/report-fit/after/`. Full 121-test accounting acceptance above is the unchanged service baseline, not a newly rerun suite for this presentation patch.

The 0.1.4 package/source-only hosted hotfix will be recorded after verification. This hotfix requires no migration, schema change or demo-data reset. The fuller four-company histories remain a plan: compact transaction variety across multiple years, including closing examples, assets, fictional employees/salaries, cash/petty cash and multiple bank accounts. API/MCP reads remain next, followed by controlled commands and optional AR/AP.


### 0.1.4 publication verified

Published package/source commit: `563524b0d29b2a4a26f1625d04963687130314da`, GitHub tag `v0.1.4-preview`. The actual ZIP is 1,326,687 bytes, SHA-256 `c29f67772b2b09c2f2100e9014cfc71090c85321326ebe81ba113c3de553fbb7`; a fresh public download matches. Fresh installation of that package passed all 11 migrations, user/company creation, central posting, balanced reporting and duplicate replay. No migration/runtime dependency changed from 0.1.3.

The source-only hosted cutover is `core-0.1.4-preview-563524b0d29b`. It changed only app.css, account.php and layout.php, preserving the exact PHP image, MySQL container/volume, all 27 tables/35 guards/11 receipts, frozen table checksums, visitor generation and session bytes/ownership/permissions. Private SQL/session backups are retained. It invoked no migration or forced demo reset. The ordinary hourly reset remains scheduled and was not separately observed in this hotfix check.

Fresh live browser validation passed all 42 route/width checks for P&L, Balance Sheet, account statement/chooser, Reports, Transactions and Journals at 1440/1024/980/768/390/320px: no report/ledger horizontal scrolling, clipped account filters, hidden running balances, document overflow or page errors. The pre-cutover browser session still accessed its same sample; closing 875.00 and CSV 875.0000 were unchanged. Nine screenshots and the receipt are retained under `output/playwright/release-0.1.4-live/`. No live sale/payment was made by this hotfix check.

Website `website-redesign-20260915-123255` published at 12:33:56 UTC on 15 September: 104 static files verified against the archive and live bytes, canonical/private-path checks passed, demo headers/containers preserved. The download page, release article, news/RSS/sitemap and corrected llms.txt identify 0.1.4. Local static checks passed 13 HTML documents with zero errors/warnings. IndexNow received six changed canonical URLs with HTTP 200; this confirms receipt, not indexing. No ads, social posts or private traffic access occurred.

Current release docs and Wiki are synchronized in the follow-up publication commit. Remaining product work is recorded in the updated demo and module plans; no new demo-history fixture, DataTables adapter or API/MCP endpoint is included in 0.1.4. The earlier 0.1.3 full-suite totals remain the unchanged accounting-service baseline.
