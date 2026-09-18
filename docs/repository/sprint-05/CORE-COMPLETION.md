# Consolidated core completion, local checkpoint

Date: 15 September 2026. The owner requested completion/cleanup of all worktrees, removal of legacy source, then core completion and the next roadmap sprint. Checkpoints `e746e13` and `b433db3` retain the previously uncommitted website/opening/period/bank work and merge the released 0.1.2 accounts/general-journals work. Both original worktree branches were clean and shared `b433db3` before this continuation. The legacy application and duplicate root copies were removed from the repository tree; original history was not rewritten. A local backup exists outside the repository.

## Supported core implementation

The bounded country-neutral core includes company/book access and readiness; stable chart identities and audited edits; receipt/expense and general-journal drafts; explicit atomic posting and linked reversal; opening trial-balance/CSV review with reconciled unpaid-document evidence; period create/close/reopen; bank CSV review, exact manual matching, cancellation and completed reconciliation; trial balance, account statements, management profit/loss and balance sheet; and CSV exports.

This checkpoint adds `export_functions.php`, `/reports/export` and links on four report screens. Exports include scope, dates, setup status, exact amounts and all account-statement pages, capped at 10,000 movements with explicit rejection. Spreadsheet-formula text is escaped. Report transactions hold the existing shared book scope across pages. The mobile profit/loss date/export controls use two rows to retain readable date inputs.

Consolidation also extends cutover-restart protection to saved general journals. The restore verifier now preserves UTF-8 through Windows PowerShell native pipelines. Upgrade verification accepts fresh, foundation, released-core and local-opening baselines; both distinct `006_*` migration filenames/checksums are preserved.

## Executed validation

| Check | Result |
|---|---|
| Full target-runtime `composer check` | 116 tests, 0 failures; 85 PHP files linted; PHPStan 0 errors; sample validator passed. |
| Two-period core fixture | Opening 1,000; first-year income 100/expense 25; second-year income 50/expense 5; final cash/assets 1,120, recorded equity 1,000 and carried unclosed profit 120. Both statements reconciled and both fiscal periods closed; all account closings agree with trial balance. No automatic year-end closing entry is claimed. |
| CSV tests | All four reports reconcile; 51 account movements cross the browser page boundary; exact 0.1001 amounts sum to 5.1051; spreadsheet formula text, viewer reads, denied company/account scope and invalid dates tested. |
| Core HTTP | 72 assertions, 0 failures, no skipped viewer checks; chart/general draft/post/reverse, stale/conflict/CSRF/scope/permission behavior. Sample company 19 retained. |
| Opening/period/bank HTTP | 71 assertions, 0 failures; sample company 15, cancelled statement 7 and completed statement 8 retained. |
| Combined browser | 37 functional assertions plus 69 state/width captures at 1440/768/390px, no page overflow, broken images, page errors or external requests. Includes all four CSV attachments. Screenshots are ignored local QA artifacts under `output/playwright/core-completion/`. |
| Installation and upgrades | Fresh 10-migration install plus user/company/post/replay; foundation, 0.1.2 core and opening-local upgrades passed in separate random disposable databases. Existing account/journal/line values preserved. |
| Backup/restore | 25 table definitions/data checksums, 5,743 rows, 33 triggers, 10 migration receipts, scoped document/general-source links and balanced journals. First run detected Unicode corruption; the successful run includes the UTF-8 fix. |
| Package checks | Six package-builder tests passed; export helper is explicitly included. No public package was published. |
| Website | Eleven HTML files passed build/static validation; 27 local route/width checks, redirects/crawler/404 checks and 14 interactions passed. No messages were sent. |

Local development was privately backed up before applying the missing `006_core_accounts_journals` migration. Fresh/upgrade/restore verification only created and removed its own random databases inside `db_test`; customer and public demo data were not touched.

## Open acceptance gates and next sprint

Technical checks complete the bounded implementation checklist, not accounting certification or pilot acceptance. Qualified accounting/security review, observed core-only user sessions, supported-host acceptance, issued statements, jurisdiction-selected reporting/closing policies, true statutory comparatives/disclosures, XLSX and broader historical imports remain open. The retained opening AR/AP schedule is not an operational subledger. Tax/FX/inventory/production POS remain separate modules and review gates.

Next is module-roadmap milestone 3: manifests/lifecycle, company capability checks and POS isolation. API/MCP reads follow; commands and optional AR/AP follow their own gates. The SEO/campaign status remains in the website design handoff: on-site baseline exists; search-console, submissions and campaign execution are pending.

Files changed: export service/bootstrap/front controller; four report templates; shared responsive CSS/layout; core completion tests/test runner; package allowlist; upgrade/restore tools; existing architecture/development/roadmap/website-design docs and this receipt. References read: AGENTS, README, ARCHITECTURE, ROADMAP, MODULE-ROADMAP, PLAN, DESIGN, DEVELOPMENT, VALIDATION, product/accounting context, release/package docs, website/Claude design and campaign handoffs, current code/schema/tests. Google Drive documents: none. Migrations added by this core follow-up: no; existing migration applied locally: yes. Schema changed locally: yes (previously committed core migration); raw secrets exposed: no. External calls: read-only official MCP/OpenAPI documentation for the following adapter planning; no application/provider calls. Live/production changed: no. No Git push, deployment, campaign/account write, message or payment occurred.
