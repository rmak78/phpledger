# PHP and hosting compatibility

**Minimum PHP 8.2; PHP 8.3 recommended for deployment.** Use a current security patch and the same version/extensions for web requests and command-line installation. The current Docker default is 8.3.33; CI covers 8.2/8.3/8.4 without raising the minimum.

Plesk and cPanel document 8.2/8.3 availability. Hostinger names 8.3 as its new-site default; its 8.2 availability wording is inconsistent and should be checked in the actual plan. SiteGround's published 2024 rollout provides evidence of 8.2 availability. These checks are not a market-share survey or completed provider installations. See the [dated research, sources and runtime evidence](https://github.com/rmak78/phpledger/blob/master/docs/strategy/HOSTING-PHP-COMPATIBILITY.md).

MySQL 8.4/InnoDB, the required extensions, HTTPS, front-controller routing, private sessions/configuration and CLI access remain requirements. MariaDB and earlier MySQL releases are not yet validated. A PHP selector in a hosting panel does not establish that the complete package will install there.

The downloadable ZIP includes compatible production dependencies. Follow its INSTALL.md and UPGRADE.md and run preflight. Composer development resolution targets PHP 8.2 so a newer development runtime cannot silently raise the package floor.
