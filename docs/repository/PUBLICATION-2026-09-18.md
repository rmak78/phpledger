# PHP Ledger 0.6.0-preview publication receipt

**Published and live on 18 September 2026.**

- GitHub prerelease: https://github.com/rmak78/phpledger/releases/tag/v0.6.0-preview
- Live demo: https://phpledger.com/demo/
- Downloads: https://phpledger.com/download/
- Wiki: https://github.com/rmak78/phpledger/wiki/Release-0.6.0-preview
- Media kit: https://github.com/rmak78/phpledger/releases/download/v0.6.0-preview/phpledger-0.6.0-preview-media-kit.zip

The owner instructed publication of the current preview after the remaining interface/acceptance gaps were described. Those gaps are disclosed in the release notes, package README, media kit and public documentation. No stable-release, accounting sign-off or complete visual/accessibility acceptance claim is made.

## Source and artifacts

PR #66 merged at 08:06:10 UTC after the owner personally signed the CLA and all PHP 8.2/8.3/8.4 CI checks passed. Merge commit: `36c4cee35c0258c230ff86a73377e40d80ec62cf`. Package and release tag source: `b117b5cef7b4d1d3cc0a4328751820a000c580f6`. Later website/receipt commits do not change the application ZIP.

GitHub published the prerelease at 08:07:44 UTC. Both archives and their separate checksum files were downloaded anonymously from their public URLs and compared byte-for-byte with the verified local artifacts.

| Artifact | Bytes | SHA-256 |
|---|---:|---|
| phpledger-0.6.0-preview.zip | 3,113,762 | `627dd5219a8de66ad46c55fe671ea0f4140280ea178270d3728ea1ac512ef8d1` |
| phpledger-0.6.0-preview-media-kit.zip | 446,433 | `6e5a774978729fe97ff25b65c401da4adef9bd6ab4414b1b20f5adc0be222458` |

The media kit includes announcement/press copy, social/email drafts, a guided demo, FAQs, and four visually inspected screenshots captured from the versioned runtime using isolated synthetic data, with captions and alt text. No campaigns were sent.

## Verification

- Source check: 282 tests, zero failures; PHP lint 209 files with zero failures; PHPStan and sample validation passed. GitHub CI passed on PHP 8.2.33, 8.3.33 and 8.4.25.
- Exact ZIP: all 1,569 manifest file sizes/hashes checked. Compiled CSS included; Node dependencies, Tailwind source, prototype files and DataTables remnants excluded.
- Published 0.5 baseline ZIP independently downloaded; SHA-256 matched `0d25e41bd724d90b8b803e10b7b81d3da751a3e7abedf245237febcb04375e0b`.
- Extracted ZIP only: fresh and upgrade scenarios passed on PHP 8.2.33, 8.3.33 and 8.4.25. Prior financial rows and migration receipts preserved; upgrade applies only 029-031 and replay is empty. New AR/AP, tax, stock and purchasing scenarios reconcile with exact retries. See PREVIEW-0.6.0-PACKAGE-PROOF.json.
- Local backup restoration verified 80 table definitions/data checksums, 735,764 rows, two scoped views with 42,026 effective row digests, 106 guard triggers, all 32 migration receipts, source links and balanced journals. Only the random restore database was removed.
- Local browser sweep: 33 states / 132 captures with checked HTTP, overflow, console-error and primary-action assertions passed. Valid OAuth consent/cancel passed with JavaScript on/off at both folds. This is not all 38 routes/75 prototype states or complete accessibility acceptance.
- Live demo smoke: fresh synthetic sample creation with JavaScript on/off; Home, accounts, invoice editor and P&L at 1366x768 and 1024x768; runtime version, page overflow, page errors and cross-visitor account denial passed. Health and public entry returned 200; live compiled CSS matched the package hash. The version assertion was corrected to read DOM text because the tablet rail hides its visible label; no runtime correction was needed.

## Demo deployment and recovery

Candidate: `core-0.6.0-preview-b117b5cef7b4`. Cutover completed at **2026-09-18T08:08:38Z**. The running PHP image was reused only after executable runtime inputs and dependency lock were verified unchanged. Application source comes from the verified ZIP.

Before mutation, the existing web/scheduler were stopped and the database, sessions, private configuration and vhost were backed up. The actual frozen hosted database backup was restored into an isolated schema, migrated and checked before live migration. Original fields, records and receipts were preserved. Migrations 029-031 applied live; 80 base tables, two scoped views, 106 guards and 32 receipts verified. The restricted web user could read its scoped views; scheduler/reset identity, synthetic isolation, secure-cookie configuration and disabled external dispatch remained in force. Database container identity was preserved.

Backup directory: `/var/www/phpledger/data/backups/core-0.6.0-preview-b117b5cef7b4`.
Database dump: `273,556` bytes; SHA-256 `96171ffcf2bf30db85f330d5177cd756e44e2f1a5c6e51ef6847ac39240cad6f`. Private configuration and session backups are retained in that restricted directory; none are published. The hosted frozen-backup restoration rehearsal passed. Reset scheduler is running; this receipt does not claim observation of a later scheduled reset.

Existing drift was found and corrected: the private release setting named 0.4 while the running source was 0.5. The deployment pins the observed baseline and explicitly selects source-matched release images for forward and rollback operations. Final read-only inspection verified the configured and running 0.6 source, file hashes and current website root.

## Website, Wiki and metadata

- Website: `website-redesign-20260918-081348`, published **2026-09-18T08:14:50.183554+00:00**. All 167 public files matched reviewed archive/host hashes. Download version, ZIP/checksum, media link and current screenshot updated. Canonical/directory redirects, private-path rejection, security headers and demo noindex behavior verified. Demo container identities unchanged by website publication.
- Website backup: `/var/www/phpledger/data/backups/website-redesign-20260918-081348`. The first attempt safely rolled back when public bytes did not match: the old helper addressed an inactive configuration copy. Active nginx configuration was identified using read-only inspection. The successful attempt changed only the active site's root, passed nginx validation/reload and all public checks.
- Wiki: nine pages updated and anonymously fetched to verify exact published bytes; commit `4bc2f7c`.
- About: description updated to current invoices/bills/payments/journals/reports and optional inventory/purchasing/POS scope. Website URL reviewed and retained as `https://phpledger.com/`; existing repository topics reviewed and retained.
- README, release/operator notes, design/development/architecture/roadmap documents, app version, website download/share metadata and media-kit links updated or reviewed. Historical product/release records remain historical.

## Preview limits and change boundaries

Full prototype-state visual/accessibility acceptance, consistent field-level validation recovery and the complete nested report/source/action return journey remain follow-up work. Mobile refinement and dark mode remain deferred. No financial certification, country compliance, WCAG certification or stable production recommendation is implied.

No new migration files were added during publication preparation; existing release migrations 029-031 were applied to the hosted demo. Hosted schema, demo source and website changed as authorized. GitHub branch/PR/release/assets, Wiki and About were published. No raw secrets or real customer data were exposed, and no campaign, email, payment or webhook was sent. No Google Drive reference was used.
