# 0.1.6-preview: PHP compatibility and country-neutral direction

Published 15 September 2026 under the owner's explicit instruction to fix the PHP requirement, correct the product direction and make the validated changes live. The [machine-readable receipt](PREVIEW-0.1.6-PUBLICATION.json) records the artifact, runtime, migration receipts, browser results and public checks.

## Result and scope

- **Minimum PHP 8.2; recommended/default deployment PHP 8.3.33.** Composer resolves against 8.2; installer checks enforce the same floor. CI tests 8.2.33, 8.3.33 and 8.4.25. A later version is additional compatibility coverage, not a requirement.
- The public demo runs the tested **PHP 8.3.33** image. The maintenance package retains the published accounting core and eleven migrations. API/MCP/OAuth/server-side tables and four multi-year histories retain their separate release gates.
- The product serves small businesses, owners, bookkeepers, accountants and multi-company operators across countries. **Pakistan is one intended regional direction; FBR is one planned connector.** Current product, architecture, roadmap, installer, website and Wiki guidance follow the [owner clarification](../../strategy/PRODUCT-DIRECTION-CLARIFICATION-2026-09-15.md).
- Project-owned core/current modules use AGPL-3.0-or-later with a commercial licence available. Published pre-adoption 0.1.0 through 0.1.5 previews retain their original grants; third-party notices remain separate. The app links to its corresponding source. The CLA workflow and separate signature branch are published; no signature was fabricated and an actual hosted signature/recheck remains unverified.
- The owner's POS label replacement remains in place. PR #65 and its contributor attribution remain in Git history; the owner later reverted its patch and supplied the replacement. The fallback quantities have product-specific accessible names and remain usable without JavaScript.

## Published identity

| Surface | Verified identity |
|---|---|
| Release and tag | [v0.1.6-preview](https://github.com/phpledger/phpledger/releases/tag/v0.1.6-preview) |
| Package/application source | `f2c228c08185dc7803c763dc286c2ba23712ddf2` |
| Downloaded ZIP | `phpledger-0.1.6-preview.zip`, **1,346,895 bytes** |
| Download SHA-256 | `ab912843f045a7fb904f6b158e2c18d1d6d7792cd91ea4567ee35eb539253eb8` |
| Demo release | `core-0.1.6-preview-f2c228c08185` |
| Tested/deployed image | `sha256:51d53499660261da6e2146d912d59ed1da0011136fb18cfdee2a4bcccf418cb8` |
| Static website | `website-redesign-20260915-173352`; published 17:34:51 UTC; all **109 files** match reviewed build/archive/host/public bytes |
| Wiki | `73cdf7c00b3568c8f492f08c030a7bf9369298c8` |

The package and checksum were downloaded again from the public release; their hashes agree with the exact tested artifact. The GitHub repository description, current README, Wiki, download page, release news, RSS and sitemap were updated. Post-package documentation/receipt commits do not change the tagged package or deployed application source.

## Validation performed

| Check | Result |
|---|---|
| Maintenance CI on PHP 8.2.33 / 8.3.33 / 8.4.25 | [All three jobs passed](https://github.com/phpledger/phpledger/actions/runs/35000843488): **121 tests, zero failures and 90 PHP files linted per runtime**; PHPStan and sample validation passed |
| Local maintenance checks | Full `composer check` passed on PHP 8.2.33 and 8.3.33; package-builder tests 6/6 passed |
| Actual platform/dependencies | `composer check-platform-reqs` and manifest validation passed; deliberate exact-pin warnings retained; dependency audit reported no known advisories |
| Final ZIP fresh install | Passed independently on PHP 8.2.33 and 8.3.33; eleven receipts; trial-balance debit/credit both `1000.0000` |
| Final ZIP upgrades | Actual 0.1.4 and 0.1.5 packages upgraded to this ZIP on both runtimes; **four upgrade cases**, zero new migrations; exact trial balance and all prior receipts preserved |
| Restoration | CI restored **27 tables, 35 guards and 11 receipts** with matching definitions/data checksums; 2,352 sample rows on 8.2/8.3 and 2,349 on 8.4 |
| Restricted demo test | Sample isolation, concurrent reset lock, CSRF, posting/reversal, capacity, generation expiry and denied administrative/destructive actions passed on the separate local maintenance database |
| Website build | **18 pages, zero errors, zero warnings** in the clean maintenance worktree |
| Live website browser | Eight routes at **1440, 768 and 390px**: 24 layouts, HTTP 200, no horizontal overflow or JavaScript errors; current PHP/version/licence/direction copy asserted |
| Live demo without JavaScript | Six named POS quantity inputs at all three widths; 9.00 sample sale, 10.00 illustrative cash and 1.00 change; posted journal drilldown; bank 875.00 + sale 9.00 = **884.00** |
| Existing live visitor | Pre-switch browser session remained authorized; bank balance stayed **875.00**; new corresponding-source link visible; trial balance, P&L and Balance Sheet routes returned 200 |
| Public web checks | Exact static bytes, canonical and directory redirects, private-path 404s, HTTPS headers, health and demo noindex passed |

The integration development branch separately resolved `symfony/uid` from 8.1.5 to 7.4.17, keeping MCP SDK 0.8.1 and OAuth server 9.4.1. Its [three-version CI also passed](https://github.com/phpledger/phpledger/actions/runs/35000839904). These libraries are absent from the smaller 0.1.6 maintenance package. The full local integration tree's 142-test runs include preserved multi-year demo work and do not establish Release B/C or native-client acceptance.

## Live change and recovery evidence

The guarded cutover stopped/recreated only the demo web and scheduler containers. Before switching, it created a private consistent database dump, original configuration, session archive and source/image recovery artifacts under `/var/www/phpledger/data/backups/core-0.1.6-preview-f2c228c08185`. It verified all frozen table checksums/definitions, 35 guards and eleven migration receipts before opening the new web service. The database container/volume, visitor generation and restored session files were preserved. No migration or forced demo reset ran during this cutover. The previous image and source remain recoverable.

The static publication separately backed up the previous website and vhost under `/var/www/phpledger/data/backups/website-redesign-20260915-173352`. Its reviewed Nginx diff changed only the static release root; syntax validation, reload and HTTPS read-back passed. Existing CSP, host-only HSTS, Permissions Policy and demo noindex were preserved. No unrelated site/container was changed.

The pre-existing hourly reset boundary was retained. A scheduled reset after this runtime switch was not part of the completed maintenance observation; the later multi-year demo release still requires its own observed reset evidence.

## Discovery and remaining gates

IndexNow accepted the eight changed canonical marketing URLs with **HTTP 200**. The existing public verification key was checked first. Acceptance is not proof of indexing. Owned Google/Bing property access, console sitemap submission and observed indexing remain open; no account access was invented. No social post, message, advertising spend or real payment was sent.

PHP availability was checked against official Plesk, cPanel, Hostinger, SiteGround and PHP documentation in the [hosting record](../../strategy/HOSTING-PHP-COMPATIBILITY.md). This is a representative availability check, not proof of what a numerical majority of all shared hosts runs. **MySQL 8.4/InnoDB remains required**; MariaDB, earlier MySQL and complete shared-host installation profiles are unverified. Qualified accounting review and observed pilot usability remain separate gates. No FBR or other regional connector is shipped by this release.

## Changed files and reference boundary

Runtime/configuration changes cover `composer.json`, `composer.lock`, `Dockerfile`, both Compose files, `.github/workflows/foundation.yml`, the shared runtime helper, installer preflight, installer tests and the application source footer. Licensing/contribution changes cover LICENSE, LICENSE-SCOPE, CLA, CONTRIBUTING, funding/licensing policy/review, package licence inclusion and the pinned CLA workflow. Product changes cover README, PRODUCT_BRIEF, ARCHITECTURE, MODULE-ROADMAP, ROADMAP, INSTALLER, DEVELOPMENT, controlling strategy clarification/hosting evidence, current accounting-reference guidance and Wiki mirrors. Website changes use the existing builder's site metadata, common footer, current pages, 0.1.6 news and generated public files. Existing historical release receipts and owner-supplied strategy documents remain preserved.

References read: repository instructions, README/architecture/roadmap, the owner-supplied 15 September strategy prompt/register/market/competitive material, the subsequent direct owner clarification, existing accounting/runtime/release code and the official hosting sources linked above. **No Google Drive document was required or read.** Browser verification followed the local Playwright skill using its CLI workflow.

- Migrations added/applied on live: **no**.
- Schema changed: **no**.
- Raw secrets exposed: **no**.
- External/live calls made: **yes**; public documentation, GitHub release/repository/Wiki, authorized host operations, public browser checks and IndexNow.
- Live/production changed: **yes**; PHP Ledger demo runtime, static website and publication surfaces only.
