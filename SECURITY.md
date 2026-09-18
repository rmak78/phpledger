# Security policy

PHP Ledger is open-source, self-hosted double-entry accounting software with a simple cash point of sale for small businesses, built on PHP 8.5 and MySQL 8.4 and currently in development preview. If you find a security problem, please report it privately rather than in a public issue, discussion or pull request.

## Scope

In scope:

- The modern application in `www/phpledger`.
- The release package from the [v0.1.0-preview release](https://github.com/phpledger/phpledger/releases/tag/v0.1.0-preview): `phpledger-0.1.0-preview.zip` and its `.sha256` file.
- The website at https://phpledger.com/ and the public demo at https://phpledger.com/demo/.

Out of scope:

- `legacy/`, which preserves the 2015 application. It is unmaintained, is not loaded by the modern runtime and keeps its own terms. Do not deploy it, and do not report problems in it here.
- Third-party dependencies. Report those to their own projects; a report that PHP Ledger pins a vulnerable version is welcome.
- Hosting you control (web server, TLS, operating system, database server), unless the project's installation documentation is what is wrong.

## How to report

- Preferred: [open a private vulnerability report on GitHub](https://github.com/phpledger/phpledger/security/advisories/new).
- Alternatively, email [rmak78@gmail.com](mailto:rmak78@gmail.com) with the subject "PHP Ledger security".

Please do not post exploit details, screenshots of real data or credentials anywhere public.

## What to include

- The affected component and the version, tag or commit (for example `0.1.0-preview`).
- Steps to reproduce with synthetic data only.
- The impact you observed or expect, for example data exposure, unauthorized posting or reversal, privilege escalation, or bypass of company and book permissions.
- Your environment: PHP and MySQL versions, web server, and browser if the problem is in the interface.
- Sanitized logs or screenshots. Never send passwords, tokens, session cookies, real business records or database backups.
- Whether you want to be credited, and under which name.

## What to expect

We aim to acknowledge reports within five working days. This is a best-effort statement from a small project in development preview, not a guarantee. There is no bug bounty and no payment for reports.

## Coordinated disclosure

Please give the project a reasonable period to investigate and prepare a fix or mitigation before publishing details, and tell us if you intend to publish. The intention is to keep you informed of progress, agree a disclosure date with you once a fix is available, and credit you if you wish.

## Supported versions

| Version | Status |
|---|---|
| `0.1.0-preview` | Development preview. Fixes on a best-effort basis; no security-support guarantee. |

There is no stable release yet. Evaluate the preview with synthetic data on your own installation.

## Public demo

The demo at https://phpledger.com/demo/ gives each visitor a private synthetic company, resets every hour and is capacity-limited. Do not test destructive actions, load or automated scanning against it, and never enter real records or credentials there. Use your own installation of the release package for security testing.
