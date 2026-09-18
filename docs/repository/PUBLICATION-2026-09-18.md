# PHP Ledger 0.6.0-preview publication receipt

Status: prepared and uploaded as a GitHub **draft**, not publicly released.

The owner instructed publication of the current preview after the remaining interface/acceptance gaps were described. Those gaps are disclosed in the release notes, package README, media kit and prepared public documentation. No stable-release, accounting sign-off or complete visual/accessibility acceptance claim is made.

## Source and artifacts

- Application source/package revision: `b117b5cef7b4d1d3cc0a4328751820a000c580f6`.
- Release branch: `codex/ui-redesign-0.6`.
- PR: https://github.com/rmak78/phpledger/pull/66 (not merged).
- Draft release target: `v0.6.0-preview`; marked prerelease.
- Application ZIP: `phpledger-0.6.0-preview.zip`, 3,113,762 bytes.
- Application SHA-256: `627dd5219a8de66ad46c55fe671ea0f4140280ea178270d3728ea1ac512ef8d1`.
- Media kit: `phpledger-0.6.0-preview-media-kit.zip`, 446,433 bytes.
- Media-kit SHA-256: `6e5a774978729fe97ff25b65c401da4adef9bd6ab4414b1b20f5adc0be222458`.
- Both ZIPs and their separate checksum files are attached to the draft. Public download checks remain pending publication.
- Expected media-kit download: https://github.com/rmak78/phpledger/releases/download/v0.6.0-preview/phpledger-0.6.0-preview-media-kit.zip (not yet public).

## Verification completed

- Latest complete source check: 282 tests, zero failures; PHP lint 209 files with zero failures; PHPStan and sample validation passed.
- Current-version source fresh installation: 32 migrations, user/company, central posting, balanced report and migration replay passed in an isolated database.
- Current-version source upgrade from the 0.5 schema through 028: posted journal/accounts/setup, existing full-read connection, balanced report and replay preserved.
- Exact application ZIP: 1,569 manifest file sizes/hashes checked; no Node dependencies, Tailwind sources, prototype files or DataTables remnants; compiled CSS included.
- Published 0.5 baseline ZIP independently downloaded and its checksum matched `0d25e41bd724d90b8b803e10b7b81d3da751a3e7abedf245237febcb04375e0b`.
- Extracted ZIP only: fresh and upgrade scenarios passed on PHP 8.2.33, 8.3.33 and 8.4.25. Prior financial rows and migration receipts preserved; upgrade applies only 029-031; replay is empty. New AR/AP, tax, stock and purchasing scenarios reconcile with exact retries. See PREVIEW-0.6.0-PACKAGE-PROOF.json.
- Backup restoration: 80 table definitions/data checksums, 735,764 rows, two scoped views with 42,026 effective row digests, 106 guard triggers, 32 migration receipts, source links and balanced journals verified. Only the random restore database was removed.
- Refreshed browser sweep: 33 states, 132 captures; checked HTTP, page overflow, console errors and marked primary controls passed. Valid OAuth consent/cancel passed with JavaScript on/off at both fold sizes. Full 38-route/75-state and accessibility acceptance is not claimed.
- Four media screenshots captured from the versioned runtime with isolated synthetic data; captions/alt text supplied and images visually inspected. No campaigns sent.
- Website build: 66 pages, zero errors and zero warnings. Current screenshot, download metadata/checksum and media-kit link generated from source.

## Hosted staging and remaining publication actions

- Read-only hosted preflight verified the published 0.5 package source, PHP 8.3.33, restricted web database grants, scheduler/reset identity, source mounts and synthetic-only deployment boundaries.
- Found existing configuration drift: private configured release was 0.4 while running web/scheduler mounts and package were 0.5. Staged helper pins the observed old source and explicitly selects source-matched release images, preserving rollback behavior.
- Candidate staged at the hosted release directory with verified source hashes and the unchanged executable image inputs. Private configuration/vhost backups created by staging. The active web/scheduler, database schema and website remain unchanged.
- Final cutover still requires its frozen database backup/restoration rehearsal and migration check. No active hosted migrations have run.
- GitHub Wiki candidate prepared locally; not pushed. About description/website/topics inspected; final update/review receipt pending publication. Website source/output committed; hosted website not switched.
- GitHub CLA Assistant failed for the sole committer account `rmak78`; the signatures registry is empty. The owner must personally review the CLA and sign on PR #66 if they agree. No signature was submitted by the agent and no check was bypassed.
- GitHub PHP CI must finish successfully before merge. After the CLA and CI gates clear: merge, publish the draft, verify public ZIP/checksum/media downloads, deploy with backup/rollback, publish website/Wiki/About updates and complete this receipt.

No raw secrets or real customer data were exposed. External actions were GitHub read/push/PR/draft-upload operations and authorized hosted read/staging operations. No campaign, payment, email or webhook was sent. No new migration files were added by publication preparation; 029-031 were already on the release branch.
