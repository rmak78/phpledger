# 0.1.0-preview foundation package validation

Date: 14 September 2026. This receipt covers an evaluation package, not completion of Sprint 03 or approval for live bookkeeping.

## Exact artifact

- Source: `049515bb7392742b68bc18f93a6b22395fb63e68` on the public default branch.
- Archive: `phpledger-0.1.0-preview.zip`, 1,209,919 bytes.
- SHA-256: `7d463fb79b3de3a791ae98559bb64c205b9fcbc34b4fd0a7c69a6908460eb4ca`.
- 106 manifest entries plus `PACKAGE-MANIFEST.json`; all archived bytes, sizes and hashes verified. Two independent builds produced identical ZIP bytes.
- MIT approved for new project-owned code/docs. Nine required notice/licence files are included; third-party terms and legacy provenance remain separate.
- Production vendor generated afresh from the committed lockfile using Composer 2.10.1 on PHP 8.5.10; only MeekroDB v3.1.5, reference `98a400845cffd6cafbbec38eae1a61b0e695a446`.
- Production platform checks and advisory audit passed. The archive excludes local configuration, secrets, database state, legacy code, PHPStan and upstream development SQL fixtures.

## Executed checks

| Check | Result |
|---|---|
| Package builder guard/reproducibility tests | 5 passed; rejects dirty source, missing licence, development dependencies, private/escaping paths and overwrite; upstream test SQL excluded. |
| Local PHP lint and static analysis | 58 PHP files passed; PHPStan zero errors. |
| GitHub CI on artifact source | [Run 34880220133](https://github.com/phpledger/phpledger/actions/runs/34880220133) passed build, manifest/audit, lint/static, sample checks, full integration and restoration. |
| Actual unpacked artifact installation | Empty isolated MySQL 8.4 database: read-only preflight reports five pending migrations; all five applied; preflight reports current schema. |
| Actual artifact integration | 62 tests passed, zero failures, using its production vendor and a separate nonshipping harness. |
| Actual artifact upgrade | Baseline 001 to current 005 preserves six account identities and posted headers/lines, enforces setup review and mappings, reconciles reports and passes replay/UTC checks. |
| Actual artifact restoration | 15 tables, 952 rows, nine triggers and five migration receipts restored; definitions, checksums and scoped journal reconciliation passed. |
| Actual artifact HTTP journey | 26 checks passed: initial user/sign-in, onboarding, drafts, posting, source/report drill-down, reversal and closed-period rollback. |
| Post-test package integrity | All 106 manifest file hashes still match, with no added private state in the unpacked tree. |

Testing used the separate `phpledger-package-acceptance` Compose project and disposable MySQL storage. Its app mounts the unpacked archive; test code is mounted separately and is not shipped. Existing development, public demo and customer databases were not used for package acceptance.

GitHub reports 20 open dependency alerts, all in four archived `legacy/assets/plugins/*/package.json` manifests. They do not occur in the distributed runtime. The legacy directory remains historical reference, is never served, and is excluded from the runtime Docker context and customer archive. These alerts have not been hidden or dismissed.

## Publication and boundaries

The default branch now contains the modern application, website source, migrations, tests, resources, release builder, contributor files and MIT scope. Ten old root items were relocated under `legacy/` with Git history preserved. No history rewrite was performed; original dirty development work was preserved in its checkout.

Package publication and website download-link verification are recorded below once complete. The hosted demo remains its previously verified sprint-02 runtime; CLI installer changes are distributed in the new package and have not been redeployed to the demo.

Remaining work: qualified Pakistan accounting/report review, UK/UAE profiles, refined POS checkout, representative usability sessions, full accessibility/performance certification and a supported pilot release. AR/AP, stock/COGS, tax, history imports, opening-balance cutover, offline operation, translated UI, exchange-rate posting and AI scanning are not included. Only the internal schema 001 upgrade has been tested; there is no legacy-data or previous-package migration guarantee. Host-specific HTTPS, PHP identities/session permissions and least-privilege grants require operator verification.

Files changed include `LICENSE`, `LICENSE-SCOPE.md`, Composer metadata, installer/runtime helpers and tests, `tools/build-package.py`, `tools/package-files.json`, package templates/notices, documentation and the published modern source tree. Five existing migrations were packaged unchanged; new migrations in this release-preparation step: **no**. Live schema changed: **no**. Raw secrets exposed: **no**. External calls: **yes**, GitHub and public dependency sources. Live changes: repository/Wiki publication, with static download-link publication recorded below; no live accounting data or application changes during package preparation. Google Drive documents read in this release-preparation step: **none**. References: existing repository plans, architecture, package contract, licence/provenance audit, installer source and the official GCC mirror of the GPLv3 licence text.

Real-browser artifact checkpoint: initial-user sign-in, empty company list, onboarding preview and an isolated SGD sample succeeded. Posting a further SGD125 expense produced SGD750 cash/profit, SGD1,000 income and SGD250 expenses in Reports. Images loaded, no console errors, and no horizontal overflow at the measured 1686px CSS viewport. This is functional browser acceptance; it does not replace the prior device checks or future observed usability/accessibility work.

## Published result

[v0.1.0-preview](https://github.com/phpledger/phpledger/releases/tag/v0.1.0-preview) is a public prerelease targeting the exact source above, with the ZIP and its checksum. Both assets were downloaded again from GitHub and matched the recorded SHA-256. GitHub reports the expected ZIP digest and size.

Static website release `website-20260914-193424` was verified at 19:34:33 UTC on 14 September (00:34:33 Pakistan time on 15 September). The current homepage links the published package and states MIT terms. A fresh website/vhost backup was retained; Nginx validation and reload succeeded; exact public homepage hash matched. The full contact address and LinkedIn remain present, with no public phone. The demo runtime, proxy and database were unchanged.
