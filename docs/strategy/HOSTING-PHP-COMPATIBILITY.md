# Hosting and PHP compatibility — 15 September 2026

## Browser installation and recovery candidate — 18 September 2026

The local 0.7/0.8 implementation removes shell/Composer/Node from the browser installation journey after hosting-panel preparation. The older CLI requirement below describes the published baseline. Updates additionally require PHP ZIP, private storage, writable release paths and enough disk space for a complete matched backup. Current checks run on local Linux containers with PHP 8.3.33/MySQL 8.4; they do not qualify Plesk, cPanel or arbitrary shared hosts.

The current updater requires the configured database identity to have schema-scoped installation/recovery privileges and own the existing view/trigger definers. A separate restricted runtime identity cannot yet supply temporary update credentials. Insufficient privileges fail before mutation. Representative restricted hosting, PHP-FPM timeout/interruption behavior, unfamiliar-operator installation, sustained workload and PHP 8.2/8.4 reruns of the new paths remain qualification work. See the current [validation receipt](../VALIDATION.md#stable-path-local-implementation--18-september-2026) and [upgrade contract](../../resources/release/UPGRADE.md).

## Decision and implementation

Minimum **PHP 8.2**, recommended/default deployment **PHP 8.3**. The owner authorized dependency/runtime fixes and publication after the earlier audit stopped. No PHP 8.4/8.5 requirement is imposed.

Composer now uses `php >=8.2` and `config.platform.php = 8.2.0`. This makes dependency resolution respect the minimum even on a newer developer machine. Actual installations still run `composer check-platform-reqs` and preflight; the platform setting is not a substitute for checking real PHP/extensions.

Only one production dependency changed in the integration candidate: `symfony/uid v8.1.5` (requires PHP >=8.4.1) became **v7.4.17** (requires >=8.2). The existing `mcp/sdk 0.8.1` and `league/oauth2-server 9.4.1` pins remain. Composer resolved the compatible version with a targeted minimal update; no vendor files or platform checks were patched around the requirement.

Docker and Compose default to **8.3.33**. `PL_PHP_VERSION` selects the pinned test runtime; CI covers **8.2.33, 8.3.33 and 8.4.25** and checks real platform requirements. The shared runtime guard accepts >=8.2 and rejects older versions; preflight reports the actual running version. PHP 8.4 is additional compatibility coverage, not a deployment prerequisite.

## Host availability research

Checked official documentation on 15 September 2026. This is a representative availability check, not a market-share survey or a completed installation on each provider.

| Platform/provider | Published offering relevant to this decision | Implication |
|---|---|---|
| Plesk | Its installer lists PHP 8.2, 8.3, 8.4 and 8.5 for supported Linux and Windows systems. | Both proposed versions are available in the platform; the operator must install/enable the handler. [Plesk version list](https://support.plesk.com/hc/en-us/articles/12377285397911-Which-PHP-versions-are-available-for-installation-via-Plesk-Installer) |
| cPanel/WHM | EasyApache supports PHP 8.1–8.5; the documented default profile includes 8.2. MultiPHP selection depends on installed packages. | An application should not require 8.5 merely because a current panel can install it. [cPanel PHP documentation](https://docs.cpanel.net/ea4/php/about-php/) |
| Hostinger | The new-site instructions name 8.3 as default and selectable 8.2–8.5. Another paragraph inconsistently labels 8.2 unavailable/deprecated. | 8.3 is the clear documented default; verify 8.2 in the actual hosting plan before relying on it. [Hostinger PHP configuration](https://www.hostinger.com/support/1575755-how-to-change-the-php-version-of-your-hostinger-hosting-plan/) |
| SiteGround | Its published rollout made 8.2 the default across its servers in 2024. | Evidence of broad 8.2 availability, not proof that the 2024 default remains unchanged today. [SiteGround announcement](https://www.siteground.com/blog/php-8-2-becomes-default-version) |

The sample supports an 8.2 minimum and an 8.3 default; it does not establish what a numerical majority of all shared hosts currently runs. PHP's upstream security support ends **31 December 2026 for 8.2** and **31 December 2027 for 8.3**, which favors 8.3 for new deployments within the owner's ceiling. Use current security patches. [PHP supported versions](https://www.php.net/supported-versions.php)

PHP availability alone does not prove PHP Ledger hosting compatibility. The current database profile remains **MySQL 8.4/InnoDB**; MariaDB and earlier MySQL versions have not been certified. Required extensions, HTTPS, front-controller routing, private sessions/configuration and CLI access must pass package preflight. No Plesk/cPanel customer account or provider setting was changed during this research.

## Executed local checks

- PHP **8.2.33**: `composer check` passed — **109 PHP files linted, PHPStan zero errors, sample validation passed, 142 tests and zero failures**.
- PHP **8.3.33**: the same full suite passed — **109 PHP files linted, PHPStan zero errors, sample validation passed, 142 tests and zero failures**.
- PHP **8.4.25**: the same full suite passed — **109 PHP files linted, PHPStan zero errors, sample validation passed, 142 tests and zero failures**. This is additional compatibility coverage; deployment remains on 8.3.
- Actual installed platform requirements passed on both runtimes. Composer manifest validation passed with the expected warnings for deliberate exact dependency pins.
- These runs include the preserved local multi-year demo work and integration candidate; they do not mark either release or the named-client matrix complete.
- The exact 0.1.6 maintenance package passed installation and upgrades from 0.1.4/0.1.5 on PHP 8.2/8.3. Its smaller published scope passed 121 tests on each of the three CI runtimes. The public demo now runs PHP 8.3.33. See the [maintenance publication record](../repository/sprint-05/PREVIEW-0.1.6-VALIDATION.md) for package, restoration, browser and hosted proof; the integration and multi-year fixtures above remain separately gated.

The earlier [PHP 8.2 audit](PHP-8.2-AUDIT.md) remains unchanged as historical evidence of the blocker. This document records its authorized resolution.
