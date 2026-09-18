# Core accounts and journals — 0.1.2-preview

Execution date: **15 September 2026** (Asia/Karachi). The isolated `codex/account-statements` worktree preserves unrelated root-checkout changes. This receipt extends, rather than replaces, the earlier [account-statement checkpoint](ACCOUNT-STATEMENTS-VALIDATION.md) and [0.1.1 publication](../sprint-03/PREVIEW-0.1.1-VALIDATION.md).

## Implemented scope

- All authorized ledger accounts have opening, period, running and closing balances. Running amounts are calculated before pagination; inactive-account history remains available. Receipt/expense and general-journal sources, including reversals, can be followed from the statement.
- Chart management creates accounts with stable codes/types/purposes and permits audited name/status changes with a reason and revision conflict detection. It does not reclassify posted history or silently alter template mappings. Public-demo administration remains blocked.
- General journals support exact-decimal, multi-line saved drafts, editing, review, explicit posting, dated linked reversal and history. Drafts may be unbalanced; posting may not. Posting uses saved values and a stable source key in one transaction with the journal link and audit record. Posted financial fields and audit rows are immutable.
- Migration **006_core_accounts_journals** adds account revisions/creation identities and two core tables, with four additional guards. Applied migrations 001–005 are preserved. General drafts share the demo's bounded document capacity and hourly reset lifecycle.
- Tax research covers eight countries, seven industries, 81 candidate regimes and 72 cited sources. All candidates are disabled/unreviewed. The structural validator rejects unsafe activation, invalid references and malformed dates/amounts. No runtime tax engine, business API or MCP server is activated.

## Local checks completed

| Check | Observed result |
|---|---|
| PHP 8.5.10 / MySQL 8.4 integration | **81 tests, zero failures**. Account statements plus account/journal permissions, readiness, duplicate/concurrent writes, stale revisions, period rejection, reversal, immutable history and late-failure rollback. |
| Snapshot concurrency regression | A separate connection changes an account/draft after an older repeatable-read snapshot exists. Current locking reads reject stale saves/posting and preserve the current exact amount. |
| Existing test DB 005 → 006 | Only 006 applies; replay applies nothing. Prior account fields, journals/lines, documents and template-installation data hashes remain unchanged. |
| Original 001 → current | Upgrade verifier passes with six original accounts and journal/lines preserved, setup review/mapping enforced, reconciled report and idempotent replay. |
| Backup restoration | **17 table definitions/data checksums, 7,011 synthetic rows, 13 triggers and six migration receipts** restored and compared; scoped source links and journal balance checks pass. Random isolated restore DB removed afterward. |
| Restricted demo smoke/reset | General draft/post/reverse succeeds within limits; account administration, posted edits/deletes and cross-visitor reads rejected. Two local guarded resets change generation correctly. |
| Browser core journey | **20 assertions pass**: account creation/audit reason, CSRF denial, exact live totals, add/remove lines, unbalanced draft save/correction, explicit posting, statement reconciliation and source link. Layout captures at 1440, 768, 390 and 320 CSS pixels have no page overflow. |
| HTTP core boundaries | **63 checks pass** for account audit/revisions, CSRF, retained stale input/revision, saved-only posting despite forged values, replay, dated reversal and real cross-company rejection. The optional browser-viewer credential case was skipped; service-level viewer tests pass in the integration suite. |
| Additional layout/keyboard/site checks | **18 assertions pass** for the journal editor and website at four widths, keyboard navigation, primary media loading and retained contact details. Reduced-motion preference enabled. The interactive editor starts with two lines; HTML retains ten spare rows when JavaScript is unavailable. |
| Tax catalog | Eight countries, 81 regimes, 72 sources and 56 industry profiles pass structure checks; **29 invalid in-memory candidates rejected**. This does not validate legal accuracy or business eligibility. |
| PHP/JS lint | **67 PHP files, zero lint failures**; `node --check` passes for the new journal editor. |
| Static/package tooling | Full PHPStan passes after correcting two validator variable-initialization warnings. Five package-builder fixture tests pass. Python HTTP helper syntax, package allowlist paths and 81 Wiki links pass. |

Browser captures are ignored local evidence under `output/playwright/core-*`. Desktop journal and mobile account screens were visually inspected. Existing account-statement evidence includes 19 earlier browser assertions. This is bounded technical/visual QA, not observed usability research or WCAG conformance. Actual browser 200% zoom, assistive-technology testing and physical printer testing remain unverified.

## Release synchronization

The release includes updated **DOCS, Wiki source, README, website copy and demo-compatible application code**. Package, GitHub/Wiki publication and hosted migration results will be appended after executing and checking them. Prepared version links are not evidence of publication.

## Remaining core work and review

Next: reviewed opening/cutover, fiscal-period administration/close policy, bank reconciliation and supported statements/exports. Existing-business readiness remains blocked until the supported opening workflow is completed. An account statement's brought-forward balance is not an opening-import workflow.

AR, AP, inventory, tax and industry POS remain optional-module work after the core and extension contracts. The preserved cash POS is a showcase. See [ordered roadmap](../../MODULE-ROADMAP.md) and [core accounting rule register](../../accounting/CORE_RULE_REGISTER.md). ICAP, ICMAP and ACCA references inform controls; no professional-body approval, certification or jurisdiction-compliance claim is made. Qualified accounting/tax review, security assessment, observed usability and supported-hosting/load gates remain open.

## Change and reference record

Files: core/document/demo/web helpers, bootstrap/router/layout; account and journal views/assets; migration 006; financial/concurrency/installer/demo tests; package allowlist/release installation guides; upgrade/restore and tax-validation tools; README, architecture/roadmaps, Wiki/website copy, accounting rule register, tax research and eight candidate JSON catalogs. Git records the exact inventory.

References read: repository instructions, current architecture/design/roadmaps and service/test code; official ICAP, ICMAP, ACCA, tax-authority and framework references cited in the research documents; prior release/runbooks and read-only hosted state. **Google Drive documents: none** in this core release work.

Migrations: **yes — 006**. Schema changed: **yes, locally at this checkpoint**. Raw secrets exposed: **no**. External calls: **yes**, official research, GitHub/SSH read-only preflight and development dependencies. Live/production changed at this checkpoint: **no**; publication is separately recorded below. No messages, payments or funding collection were sent.

## Published release — 15 September 2026

All five release surfaces are updated: repository **DOCS and README**, the **GitHub Wiki**, the **website** and the isolated **demo**. The package and runtime source is **`da5ff133e6454d305729f837707cf89eb6e1cbc5`** (implementation commit `30de11d`, followed by file-ending normalization). It was pushed to the default `master` branch without rewriting history. This later documentation receipt does not change the packaged source.

| Surface | Publication and verification |
|---|---|
| GitHub source and README | Default branch updated; repository description now reflects the core-first scope. [CI run 34911987945](https://github.com/phpledger/phpledger/actions/runs/34911987945) completed successfully for the exact package source. |
| Download | [v0.1.2-preview](https://github.com/phpledger/phpledger/releases/tag/v0.1.2-preview) published as a prerelease with ZIP and checksum. **1,276,132 bytes; 123 manifest files plus the manifest**. Repeated builds are byte-identical; a fresh download from GitHub matches the local ZIP. |
| Actual package acceptance | Fresh installation and actual **0.1.1 → 0.1.2** upgrade pass on separate local databases. Twelve accounts, six documents, three journals/six lines, two companies, unresolved opening status, all prior column values and 125.0000 balanced report totals are preserved. Six migration receipts/thirteen triggers; replay applies zero. Packaged tax validator rejects all 29 invalid fixtures. |
| Packaged HTTP | **64 checks, zero failures** on the actual ZIP at local port 18207. Includes fresh-company setup plus the earlier 63 core HTTP assertions. Optional viewer HTTP login checks remain skipped; service-level viewer tests pass. No source overlay supplied the application code. |
| Wiki | [Wiki](https://github.com/phpledger/phpledger/wiki) commit **`b14b9a691cf162ac79e484c297d3a58f92738a48`**. Thirteen files changed/added; all fourteen Markdown files match the reviewed source. All twelve content pages and Wiki home returned 200 with current scope and navigation. |
| Website | Static release **`website-20260915-001433`**, publicly verified at **00:14:40 UTC**. All **32 committed static files** and the canonical home page match local hashes. Current download/core copy, partner identities, full postal locality and LinkedIn remain; no phone is published. Nginx syntax/reload passed after changing only the static root. |
| Demo | **`core-0.1.2-preview-da5ff133e645`**, cutover verified at **00:14:04 UTC**. Only migration 006 applied. **17 tables, six matching migration receipts and thirteen guards**. Database container/volume, generation and session archive preserved; web grants remain restricted. |
| Public workflows | **41 new core HTTP checks, zero failures**, final pass at **00:17:57 UTC**: exact 37.1250 posting, duplicate prevention, dated reversal, report/source links, CSRF, unchanged chart after rejected administration and cross-visitor rejection. A separate existing-demo smoke passed **18 checks**. |
| Live browser | **10 checks pass**: sample entry, read-only account administration, 875.00 statement, two-line interactive journal editor, desktop/mobile/320px layout and current website release/copy. Live screenshots retained locally. |

ZIP SHA-256: `8bce5df6be15ad261e13719be2bc199b49011748ec073843d76b087b47b2e495`.

Website home SHA-256: `1b835988ad2281f6a802187b2c40c063387818c0940eb5c4ab8e36fcfcfda6d7`. Demo source archive SHA-256: `a35567eabd03895f46eb15c7c25445c348ed8981c081e22555f9ff4c9b0dcec4`.

### Deployment and remaining operational limits

The demo web/scheduler paused for a protected database/session snapshot and additive migration; only those two containers were recreated using the unchanged PHP image. Fourteen old tables were hash-compared before/after; only the demo-state marker was populated at cutover, following the existing normal hourly reset. No visitor data was invented to claim a populated live migration test. The populated upgrade proof comes from the actual local packages above. Private backup directory/file modes are 0700/0600. Website/vhost backups were preserved separately; the demo database container and volume were unchanged.

The scheduler resumed with the same generation and next reset **01:00 UTC**. The pre-deployment normal 00:00 reset was observed; the first normal hourly reset after this deployment was **not waited for**. Local reset tests include the new six-migration schema. No forced public reset or database restoration occurred. A transient connection reset during web startup recovered within the health retry. An earlier parallel public QA attempt received a 503; the final 41-check run had no 503 or retry. The existing demo's shared maintenance lock can produce busy responses, so this is not a high-concurrency or availability guarantee.

The first package-harness attempt installed the fresh schema before its fixture referenced a derived status as a database column; only the ignored harness was corrected, preserving that test database. Docker network allocation/publishing issues were also fixed only in the isolated harness. Final artifact results above passed without changing package bytes. Source UI QA corrected test expectations for collapsed history, native date-field tab stops and the actual demo landing route; the final assertions passed. The interactive editor was refined to two initial rows while preserving submitted values and the ten-row no-JavaScript fallback.

Production Composer audit found **no advisories** in the locked shipped dependency set. GitHub still reports **20 repository alerts (six high, fourteen moderate)** associated with the preserved historical tree; those are not a clean-repository-security claim and that tree is excluded from the package. Independent security/accounting review and the other release gates remain open.

Final boundaries: files changed are recorded above and in Git; routes/workflows checked include `/accounts`, `/general-journals/*`, `/reports/account`, source journals, onboarding/auth and the restricted `/demo` equivalents. Google Drive documents read: **none**. Migrations: **yes, 006**. Schema changed: **yes, local test environments and only the isolated public demo**. Raw secrets exposed: **no**. External/live calls: **yes**, research, GitHub publication, authorized SSH/SFTP, dependency audit and HTTPS checks. Live changes: **yes**, GitHub source/metadata/release/Wiki and PHP Ledger website/demo. No customer database, unrelated domain, message, payment or funding collection was changed or sent.
