# Support

PHP Ledger is open-source, self-hosted double-entry accounting software with a simple cash point of sale for small businesses, for PHP 8.2 or later and MySQL 8.4. Version 1.0.0 is the first stable release. This page says where to ask what, what to include, and what the project can and cannot offer.

## Where to ask

| You want to | Go to |
|---|---|
| Ask how something works, or get help installing a release | [Discussions: Q&A](https://github.com/phpledger/phpledger/discussions/categories/q-a) |
| Report a reproducible bug | [Issues](https://github.com/phpledger/phpledger/issues), using the bug report template and sample data |
| Propose a feature | [Issues](https://github.com/phpledger/phpledger/issues), using the feature request template |
| Report a security problem | Privately, as described in [SECURITY.md](SECURITY.md); never in a public issue or discussion |
| Discuss a pilot, installation assistance, training or paid support | Email [rmak78@gmail.com](mailto:rmak78@gmail.com) |
| Read the support offer on the website | [phpledger.com/pricing](https://phpledger.com/pricing/) |

Before asking, read [Getting Started](https://github.com/phpledger/phpledger/wiki/Getting-Started) on the Wiki; it covers requirements, the download, demo guidance and known limits. `INSTALL.md` and `UPGRADE.md` inside the release ZIP cover installation and upgrades, and the [release protocol](docs/RELEASE-PROTOCOL.md) lists the upgrade path for each distribution channel.

## What to include in a report

- The version: the release tag (for example `v1.0.0`) or the commit you built from, and how you installed it (release ZIP, source, or another channel).
- Your PHP and database versions, web server and operating system, and the browser if the problem is in the interface.
- Exact steps to reproduce with sample data: the screen or route, the role you used and the values you entered.
- What you expected and what you observed, including any error message and whether the record was a draft, saved, posted or reversed.
- Sanitized logs or screenshots.

Never include passwords, tokens, session cookies, real customer or supplier records, or database backups. Remove real names, amounts and account details before posting; issues and discussions are public.

## Boundaries

- 1.0.0 was published before its independent accounting review, independent security review and supervised pilots; those continue after release (see the [roadmap](docs/ROADMAP.md)). Keep your own backups and have your accountant review your books.
- Questions and issues are answered as time allows. There is no response-time guarantee, on-call cover or service-level agreement.
- The stable scope is described in the [roadmap](docs/ROADMAP.md). Regional tax certification and e-invoicing, production shop POS, advanced stock, e-commerce, and offline or native clients are outside it; requests for these belong in a feature request.
- Paid installation assistance, training, troubleshooting and support on customer-owned hosting are discussed by email. Scope, supported environments and response expectations are agreed before any commitment; nothing on this page is a quote.
- `legacy/` is unmaintained and out of scope for support.

## Demo notes

The public demo at https://phpledger.com/demo/ gives each visitor a private sample company. It resets every hour, so anything you enter disappears; do not enter real records, credentials or documents. Destructive user actions are disabled and capacity is limited, so you may see a busy or refresh message; wait a moment and try again. Demo problems can be reported in Discussions, but the demo is for evaluation, not for load or destructive testing.
