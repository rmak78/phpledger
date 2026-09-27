# PHP Ledger 1.4.7 local candidate gates — 27 September 2026

This is a **local candidate receipt**, not a publication, deployment or independent accounting/security assurance receipt. It identifies the tested source and archive rather than the mutable branch tip. The published 1.4.6 archive is the upgrade baseline (SHA-256 `dadc2758d9c50fff109e79f8448c4cb8010d4a9d51a458ce190d4512cf7765f1`, as recorded in the 1.4.6 publication receipt).

## What the candidate changes

1.4.7 says that maintenance of PHP Ledger has stopped for lack of funds, so no further updates are planned, and points to https://phpledger.com/funding/ to help restart it. The Updates and privacy screen says so in plain text, and so do the package's `README.txt` and the INSTALL and UPGRADE guides (decision register B101). Nothing else in the application changes: the installation notice, the in-app updater, the release feed address, the sample directory and every feature are as in 1.4.6, and the packaged application has no link to GitHub issues or Discussions. Against the published 1.4.6 archive exactly four members differ: `PACKAGE-MANIFEST.json`, `README.txt`, `www/phpledger/VERSION` and `www/phpledger/templates/views/updates.php`. No migration: every migration file is byte-identical to the published 1.4.6 set (62 files).

## Full application checks

| Engine | Tests | Failures | PHP lint | PHPStan | Sample validation | Log |
|---|---|---|---|---|---|---|
| MySQL 8.4 | 714 | 0 | 422 files, 0 failures | same analysis (one run) | — | `.cache/release-1.4.7/logs/mysql-*.log` |
| MariaDB 10.11 | 714 | 0 | 422 files, 0 failures | no errors (tracked `phpstan.neon`, 3 GB cap) | valid | `.cache/release-1.4.7/logs/maria-*.log` |

Each engine ran in its own Compose project and subnet (`pl147` on 10.214.47.0/24, `pl147m` on 10.214.48.0/24) with `php tests/run.php`, one after the other; both projects were removed afterwards. The website source is unchanged by this candidate.

## Exact-package gates

`tools/verify-patch-release.py` ran the exact candidate archive against the published 1.4.6 archive on disposable `pl147-mysql` (MySQL 8.4) and `pl147-maria` (MariaDB 10.11) services. On each engine: a fresh install applied 62 migrations and made a first balanced posting; a populated 1.4.6 seed was upgraded with the packaged `install/upgrade.php`, which applied no migration and was a no-op the second time; every migration receipt, row hash and the balanced totals were unchanged. Steps recorded: MySQL `fresh_62_migrations_first_post, populated_1_4_6_seed, packaged_upgrade_1_no_op, packaged_upgrade_2_no_op, historical_rows_and_balances_unchanged`; MariaDB `fresh_62_migrations_first_post, populated_1_4_6_seed, packaged_upgrade_1_no_op, packaged_upgrade_2_no_op, historical_rows_and_balances_unchanged`.

| Item | Evidence |
|---|---|
| Candidate archive | `phpledger-1.4.7.zip`, 3,643,594 bytes, SHA-256 `d52e3dc7966774d228410db6af5c47eb264a53ec467193beb3745bb5620cc236`, 1729 members, source commit `29b7d62243dd23d7a98ce3b10e46c6c7b4d36391` |
| Published 1.4.6 baseline | SHA-256 `dadc2758d9c50fff109e79f8448c4cb8010d4a9d51a458ce190d4512cf7765f1` |
| Reproduction | `tools/build-release.py` built the archive twice from the same commit: `bytes` (byte-identical) |
| Migration files | 62 (identical to 1.4.6; none added) |

## Production-mode walk

The exact archive, extracted with its own `vendor/` (no repository fallback), was served with `PL_ENV=production` against a disposable MySQL 8.4 and installed through the container channel's CLI path (`install/migrate.php`, `install/create-admin.php`, `install/complete.php`); a business was then created through the five setup stages in the browser. phpledger.com and GitHub resolved to 127.0.0.1 inside the preview, so it could not contact any project service; the installation notice therefore records `unavailable`. At 1366×768: the setup Start stage, the Updates and privacy screen with "Installed version: 1.4.7" and the maintenance notice, and its unchanged privacy text. The browser made no off-origin request. Screenshots in `.cache/release-1.4.7/evidence/`: 1-setup-start-fold.png, 1b-setup-start-gallery-fold.png, 1b-setup-start-gallery-full.png, 2-updates-full.png, 2-updates-notice-fold.png, 3-updates-privacy-text.png.

No production, public provider or credential state was changed by these gates. Nothing was pushed, tagged, signed or published.
