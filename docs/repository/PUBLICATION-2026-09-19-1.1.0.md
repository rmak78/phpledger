# Publication receipt: PHP Ledger 1.1.0

Published 19 September 2026 on the owner's request ("time to go ahead with the new release, new docs and new demo and website"). Owner decisions: version **1.1.0**; generate the publisher signing key first so the release ships signed update metadata. Machine-readable copy: [STABLE-1.1.0-PUBLICATION.json](STABLE-1.1.0-PUBLICATION.json).

## What was released

WordPress-style installation from any web folder, MariaDB 10.4+, a chosen username, an optional logo, the first signed update metadata, the sample-start gate and template encoding fixes, the release protocol and tooling, and a demo capacity fix. See [RELEASE-NOTES.md](../../resources/release/RELEASE-NOTES.md).

Integrated on branch `release-1.1.0` from master `75f85f7`:
- Codex's live SEO website patch, committed unchanged (all 197 source and 295 public files matched `.cache/seo-20260919/source-manifest.json` after line-ending normalisation).
- `wordpress-style-install` (9b7375d), `fix-template-encoding` (cfef7d9), `fix-sample-start-gate` (0005d2b), `distribution-protocol` (608f40e). No merge conflicts.

## Artifacts

| Item | Value |
|---|---|
| Tag | `v1.1.0` → `af0f4895f85a9c9c8b2107b5b919428c9919d551` (package source) |
| Release | https://github.com/phpledger/phpledger/releases/tag/v1.1.0, published 2026-09-19 15:19:49 UTC |
| `phpledger-1.1.0.zip` | 2,927,089 bytes, 1,500 files, SHA-256 `ba8de6cfca58505dda7b4d5b00e377512a9f134d06e007f97ba0ecfdd022135a` |
| `phpledger-1.1.0.update.json` | 312,063 bytes, SHA-256 `10085caf22cbc7df42c2795eaf2bfc16c0608f06d4bedf81d028f100a7be764c` |
| `phpledger-1.1.0-media-kit.zip` | 459,918 bytes, SHA-256 `e281008d6bc193f05453e6c9e8ec6ee7b28bf3158fe85d91c37fbd9df844571d` |
| Publisher key | RSA-4096, SPKI DER SHA-256 `4e58a5f46b0538c9b37aaadbfced9a2d8ad2f7d413bc168a67b1b94feaa78e78`, [publisher-public.pem](../../resources/release/publisher-public.pem) |

All three assets were downloaded anonymously after publication and matched these hashes.

## Evidence

| Check | Result |
|---|---|
| Full check on the merged branch (MySQL 8.4) | lint and PHPStan clean, samples valid, **295 tests, 0 failures** |
| Installer | browser installer 81 checks; keyless installer 103 checks |
| Update and recovery | update-recovery, database recovery, full-schema recovery (81 tables, 2 views, 106 triggers), maintenance HTTP: all passed |
| Signing, channels, packaging, feed | 25, 38, package builder OK, 30 |
| Reproducible build | two local builds of `af0f489` byte-identical |
| Exact archive in Apache (PHP 8.3 image) | 30 of 30 web adapter checks |
| Signature | signed on the owner's machine; verified with `pl_update_verify_metadata()` as an upgrade from 1.0.0, 1,500 files in the signed inventory |
| CI on the tag | Foundation checks (PHP 8.2/8.3/8.4) and Release build both succeeded |
| MariaDB 10.4/10.6/10.11/11.4 | from the installer branch validation (see [VALIDATION.md](../VALIDATION.md)); not re-run on the merged branch |

**Cross-platform build difference.** GitHub's release workflow built the tag twice with byte-identical results, but its archive (2,922,184 bytes) differs from the local Windows build. All 1,499 manifest files and the package manifest are identical; only the ZIP compression bytes differ between zlib builds. The tested local archive is the published one. Follow-up: make `tools/build-package.py` produce identical compressed bytes across platforms, or compare member contents in `tools/build-release.py --compare`.

## Demo

| Item | Value |
|---|---|
| Release | `core-1.1.0-af0f4895f85a`, cut over 2026-09-19 15:20:41 UTC from `core-1.0.0-sample-20260919-r2` |
| Contents | the published package plus 11 runtime inputs from the same commit (1,511 files, every file hash verified, every PHP file linted in the runtime image, 11 pack digests) |
| Database | guarded sample-only reset rebuilt `phpledger_demo` with 34 migrations; the first 32 receipts unchanged; database container preserved |
| Runtime image | reused (Dockerfile, `docker/` and locked packages unchanged since 1.0.0) |
| Backup | `/var/www/phpledger/data/backups/core-1.1.0-af0f4895f85a` (frozen dump SHA-256 `ef5ea048317cb764edc66d59c7a9951973b33bf1b4d8bc3c73dfb0ae1b58aa22`, sessions, environment) |
| Public check | `/demo/health` 200; a visitor started the service-workshop pack (105 records on arrival), saved and posted a receipt; version shown 1.1.0 |

**Demo capacity fix.** The public demo's per-company record ceiling counted each pack's own history (85 to 105 records) against a default of 100, so in 6 of the 11 packs a visitor could not record anything. The default is now 250 and `tests/demo_smoke.php` asserts at least 100 practice records per pack.

## Website

| Item | Value |
|---|---|
| Release | `website-1-1-0-20260919-152351`, published 2026-09-19 15:25:25 UTC, from `release-1.1.0` at d466d54 |
| Files | 298 (295 before, plus `/news/1-1-0/`, `/releases/index.json`, `/releases/1.1.0.update.json`); every archive, host and public hash verified |
| Nginx | only the document root changed; `/support` redirects, demo proxy, OAuth, TLS and headers preserved (the demo `X-Robots-Tag` header already existed and was kept; the helper's field name says "added") |
| Backup | `/var/www/phpledger/data/backups/website-1-1-0-20260919-152351` |
| Content | 22 pages corrected for the new install flow and MariaDB, 1.1.0 news page, share cards, fingerprint on `/download/`; 71 pages, 0 build errors or warnings; no horizontal overflow at 375 px on `/download/` |
| Release feed | `https://phpledger.com/releases/index.json` served as JSON; parsed by the application's `pl_release_feed_parse()`: a 1.0.0 installation is offered 1.1.0, a 1.1.0 installation nothing |

## Other surfaces

| Surface | Status |
|---|---|
| README, roadmap (1.1 language plan moved to 1.2, Arabic to 1.3), installer, validation, demo docs, signing guide | updated |
| GitHub Wiki | Home, Getting Started, PHP Hosting, Roadmap, sidebar updated; new Release-1.1.0 page (f53b986) |
| Repository About | description updated for 1.1.0 and MariaDB |
| Media kit | seven captures from the exact archive, fictional sample data; SOCIAL and EMAIL are drafts that need the owner's approval before posting |

## Open items

- **Key custody:** the key's generation receipt records no offline backup yet; the owner should copy `C:\phpledger-signing-key` to encrypted offline media and store the passphrase in a password manager.
- The sample guide's "leaving 26 new records" sentence is fixed on master (c9743d4) and ships with the next patch release; the demo still shows the old sentence.
- The demo landing page still calls itself a "development preview".
- Cross-platform reproducible compression (above).
- The main checkout's working tree still holds the SEO patch as uncommitted edits identical to the committed files; update it from `origin/master` with care.
- Not yet observed: a real shared host, an unfamiliar operator, independent accounting and security review, supervised pilots.
