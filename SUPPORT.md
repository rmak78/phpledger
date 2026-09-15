# Support

PHP Ledger is open-source, self-hosted double-entry accounting software with a simple cash point of sale for small businesses, built on PHP 8.5 and MySQL 8.4 and currently in development preview. This page says where to ask what, what to include, and what the project can and cannot offer at this stage.

## Where to ask

| You want to | Go to |
|---|---|
| Ask how something works, or get help installing the 0.1.0-preview package | [Discussions: Q&A](https://github.com/rmak78/phpledger/discussions/categories/q-a) |
| Report a reproducible bug | [Issues](https://github.com/rmak78/phpledger/issues), using the bug report template and synthetic data |
| Propose a feature | [Issues](https://github.com/rmak78/phpledger/issues), using the feature request template |
| Report a security problem | Privately, as described in [SECURITY.md](SECURITY.md); never in a public issue or discussion |
| Discuss a pilot, installation assistance, training or paid support | Email [rmak78@gmail.com](mailto:rmak78@gmail.com) |
| Read the support offer on the website | [phpledger.com/support](https://phpledger.com/support/) |

Before asking, read [Getting Started](https://github.com/rmak78/phpledger/wiki/Getting-Started) on the Wiki; it covers requirements, the download, demo guidance and known limits. `INSTALL.md` and `UPGRADE.md` inside the release ZIP cover installation and upgrades.

## What to include in a report

- The version: the release tag (for example `0.1.0-preview`) or the commit you built from, and whether you installed from the package or from source.
- Your PHP and MySQL versions, web server and operating system, and the browser if the problem is in the interface.
- Exact steps to reproduce with synthetic data: the screen or route, the role you used and the values you entered.
- What you expected and what you observed, including any error message and whether the record was a draft, saved, posted or reversed.
- Sanitized logs or screenshots.

Never include passwords, tokens, session cookies, real customer or supplier records, or database backups. Remove real names, amounts and account details before posting; issues and discussions are public.

## Boundaries

- PHP Ledger is a development preview, not a stable release. Evaluate it with synthetic data. There is no support for using it for live bookkeeping.
- Questions and issues are answered as time allows. There is no response-time guarantee, on-call cover or service-level agreement.
- Not included yet: receivables and payables, inventory and cost of sales, tax, historical imports, bank reconciliation, multi-book, foreign-currency posting, translations, offline use, card payments, document scanning. Requests for these belong in a feature request; the [Roadmap](https://github.com/rmak78/phpledger/wiki/Roadmap) shows where they sit.
- Paid installation assistance, training, troubleshooting and support on customer-owned hosting are discussed by email. Scope, supported environments and response expectations are agreed before any commitment; nothing on this page is a quote.
- `legacy/` is unmaintained and out of scope for support.

## Demo notes

The public demo at https://phpledger.com/demo/ gives each visitor a private synthetic company. It resets every hour, so anything you enter disappears; do not enter real records, credentials or documents. Destructive user actions are disabled and capacity is limited, so you may see a busy or refresh message; wait a moment and try again. Demo problems can be reported in Discussions, but the demo is for evaluation, not for load or destructive testing.
