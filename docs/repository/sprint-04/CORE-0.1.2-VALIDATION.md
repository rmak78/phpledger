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
