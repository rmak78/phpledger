# PHP and hosting compatibility

**Minimum PHP 8.2; PHP 8.3 recommended for deployment.** Use a current security patch and the same version/extensions for web requests, the browser installer and command-line installation. The current Docker default is 8.3.33; CI covers 8.2/8.3/8.4 without raising the minimum.

1.0.0 installs from a browser at `/install`, guarded by a private per-installation setup key; CLI installation (`preflight.php`, `migrate.php`, `create-admin.php`) remains available for operators who prefer it. The PHP **zip** extension is additionally required to use signed automatic updates through `/maintenance.php`, which verifies a pinned publisher public key against a private `operator.key` before applying a release.

Plesk and cPanel document 8.2/8.3 availability. Hostinger names 8.3 as its new-site default; its 8.2 availability wording is inconsistent and should be checked in the actual plan. SiteGround's published 2024 rollout provides evidence of 8.2 availability. These checks are not a market-share survey or completed provider installations. See the [dated research, sources and runtime evidence](https://github.com/phpledger/phpledger/blob/master/docs/strategy/HOSTING-PHP-COMPATIBILITY.md).

MySQL 8.4/InnoDB, the required extensions, HTTPS, front-controller routing, private sessions/configuration and CLI access remain requirements. MariaDB and earlier MySQL releases are not yet validated. A PHP selector in a hosting panel does not establish that the complete package will install there. The updater currently requires a schema-owning database identity with DDL privileges and matching view/trigger definers; a restricted shared-hosting account with only an application-level database user cannot yet use automatic updates or recovery, and this has not been certified on a representative restricted shared host.

The downloadable ZIP includes compatible production dependencies. Follow its INSTALL.md and UPGRADE.md and run preflight. Composer development resolution targets PHP 8.2 so a newer development runtime cannot silently raise the package floor.
