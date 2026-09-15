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
