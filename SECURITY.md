# Security policy

Maintenance of PHP Ledger has stopped for lack of funds, so no further updates are planned. To help restart it, see https://phpledger.com/funding/.

PHP Ledger is open-source, self-hosted double-entry accounting with a cash point of sale, for PHP 8.2 or newer with MySQL 8.0.19 or newer or MariaDB 10.4 or newer. While maintenance is stopped there is no support, and no fixes or security updates are planned for any version.

## Reporting a security problem

Email [rmak78@gmail.com](mailto:rmak78@gmail.com) with the subject "PHP Ledger security". Report privately; do not post exploit details, screenshots of real data or credentials anywhere public.

Reports are read, but while maintenance is stopped no fix, advisory, response time or disclosure date is promised. There is no bug bounty and no payment for reports.

## What to include

- The affected component and its exact version.
- Steps to reproduce with sample data only.
- The impact you observed or expect.
- Your environment: PHP and database versions, web server, and browser if the problem is in the interface.
- Never send passwords, tokens, session cookies, real business records or database backups.

## Supported versions

| Version | Status |
|---|---|
| 1.4.7, the latest release | Not maintained: no fixes or security updates are planned. |
| Earlier releases and previews | Not maintained. |

If you run PHP Ledger, keep your server, PHP and database patched, restrict who can reach the installation, and keep verified backups. Published versions do not imply independent security certification.

## Public demo

The public demo at https://phpledger.com/demo/ is a shared evaluation installation with fictional data. Do not enter real records or credentials, and do not run load tests, destructive tests or automated scanning against it; use your own installation for security testing.
