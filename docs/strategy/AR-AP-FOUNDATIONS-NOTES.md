# AR/AP foundations implementation notes

## Agreed boundary — 16 September 2026

The owner approved the foundation plan after parallel read-only review. The starting commit is `531541c` on `master`; the former `codex/integration-delivery` branch was deliberately removed during cleanup. Implementation uses `codex/ar-ap-foundations`. The original implementation authorization was local-only. The owner subsequently requested live release, website takeover/publication and repository cleanup. That later instruction authorizes this release and supersedes the original no-push restriction; no customer messages, payments or connector activation were requested.

References read in order: Decision Register B7 and Product Direction Clarification (15 September), the full Multi-Currency and AR/AP Parties and Vetting documents, and ERPNext Review sections 0–2. README, Architecture, Roadmap, development instructions and current migration/service/test code provide implementation conventions. No Google Drive reference is required.

## Resolved design conflicts

- Rate corrections: the originally specified unique key conflicts with append-only corrections. The owner selected versioned rows with revision and supersedes reference, preserving source provenance.
- Realised settlement: the owner selected a minimal internal authoritative open-item/allocation ledger. A calculation-only helper does not satisfy this scope. No invoice or bill documents, screens, imports, aging or operational AR/AP workflows are included.
- Existing aggregate AR/AP balances: the owner selected activation on unused control accounts only. Existing opening registers and balances are preserved, without guessing party identities or introducing a second mutable outstanding balance. Reviewed conversion belongs to the later AR/AP cutover milestone.
- Corrections: the owner selected existing receipt, expense and general-journal support as well as future sources. Preserve original source snapshots and journal links; append posting revisions and resolve effective source reads. POS and opening sources retain their existing specialized behavior.
- Phone duplicates: the owner selected explicit acknowledgement with a reason; no automatic merge. Scoped tax/national-identity duplicates are blocked.
- Tags: the owner selected schema-only storage until accounting dimensions are defined.
- Populated upgrades: the owner selected supported, tested migration with backup and maintenance. Preserve old monetary columns, source identity and hashes while adding domestic currency snapshots. Restrictive migration guards must remain in place while blanket immutability guards are exchanged; interrupted migrations remain blocked.
- Money: reuse BCMath and DECIMAL(20,4); rates use DECIMAL(28,12). Unsigned FC/base magnitude follows the existing debit/credit side. Round conversions explicitly; do not plug unbalanced journals.
- Legacy account monetary classification: use known roles; retain unknown classification for unclassified accounts. Group identifiers are nullable reservations, not working consolidation.
- Reversal date: default to UTC cancellation date; only an owner with a reason may use the original posting date while its period remains open. Existing bank reconciliation barriers and guarded opening restart remain enforced.

## Deferred capabilities

Invoice/bill documents and UI; provider rate imports/fetch; period revaluation; group consolidation; commodities; vetting transitions/enforcement; vendor bank approval workflow; uploads; tag assignment; A75 connector; regional tax calculation; public write API/MCP; automatic cron installation and external delivery.

## Coordination and validation

Three agents own currency, party/outbox and correction work. The lead owns open-item integration, shared bootstrap/test registration, validation and the three logical local commits. Database tests run serially in disposable test databases. Preserve unrelated files, including the new untracked website SEO prompt observed at implementation start.

At implementation start Docker Desktop was stopped and PHP was absent from PATH. The local Docker test runtime was restored; migrations were exercised only in disposable test databases. The complete suite passed 179 tests, linted 128 PHP files and passed PHPStan on both PHP 8.3.33 and 8.2.33. Fresh installation, four populated upgrade baselines, interrupted currency-upgrade recovery, backup/restore of 56 tables and two effective views, and 27 browser route/viewport checks passed. See [the validation receipt](../VALIDATION.md#arap-prerequisites--local-16-september-2026) for exact commands, runtime evidence and remaining acceptance boundaries.

Independent review identified and fixed stale-snapshot activation/account-property checks and recognition-reversal retry ordering. Tests cover all three. Outgoing foreign-bank settlement remains explicitly rejected until its carrying-value allocation exists; otherwise a payment could leave a base-currency residual after the foreign balance closes. Activity dates cannot precede the latest open-item event. The actual settlement quote is retained in the immutable command receipt even when the bank line is domestic.

Concurrent website/content work occupies different files and remains outside the foundation commits. No unresolved overlap required stopping this implementation. Any later conflict must be recorded before changing the affected scope.

## Authorized publication — 16 September 2026

The 0.3.0-preview package is built from `2d8f4417229f8472b2112e941f9c5f439937eed4`. Its hosted sample demo upgrade rehearsed the actual frozen backup, preserved original fields/receipts and verified permanent-reset-user view definers with restricted web reads. The real 21:00 UTC reset recreated all 17 receipts and 62 guards. The observer's transient-database timing limit is retained in the publication record rather than described as a completed bearer-token replay test.

The separate website work was taken over with explicit owner authorization, completed by three parallel content workstreams, reviewed and published through the existing static lane. A separately active `codex/owner-questions-ar-ap` worktree is preserved during cleanup; it is not folded into the 0.3.0 package.

See [publication evidence](../repository/sprint-05/PREVIEW-0.3.0-PUBLICATION.json) and [website content review](../design/website/CONTENT-REGISTER.md). Remaining operator AR/AP, accounting acceptance and regional gates are unchanged.
