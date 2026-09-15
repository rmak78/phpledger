<p align="center">
  <a href="https://phpledger.com/">
    <img src="docs/repository/assets/phpledger-logo.webp" width="520" alt="PHP Ledger">
  </a>
</p>

<h1 align="center">Open-source, self-hosted accounting and cash POS for small businesses</h1>

<p align="center">
  Built on PHP 8.5 and MySQL 8.4. New code is MIT licensed. Development preview.
</p>

<p align="center">
  <img src="docs/repository/assets/development-preview.svg" width="215" height="26" alt="Status: development preview">
</p>

<p align="center">
  <a href="https://github.com/rmak78/phpledger/releases/tag/v0.1.0-preview"><strong>Download 0.1.0-preview</strong></a> &nbsp; · &nbsp;
  <a href="https://phpledger.com/demo/">Try the demo</a> &nbsp; · &nbsp;
  <a href="https://github.com/rmak78/phpledger/wiki">Read the Wiki</a> &nbsp; · &nbsp;
  <a href="https://github.com/rmak78/phpledger/wiki/Roadmap">Roadmap</a> &nbsp; · &nbsp;
  <a href="https://github.com/rmak78/phpledger/discussions">Discussions</a>
</p>

---

## What is PHP Ledger

PHP Ledger is open-source, self-hosted double-entry accounting software with a simple cash point of sale for small businesses, built on PHP 8.5 and MySQL 8.4 and currently in development preview.

It records receipts and expenses as balanced double-entry journals, keeps posted entries immutable with linked reversals, and shows a trial balance, profit and loss, balance sheet and an entered cash scenario. A small cash point of sale posts sales through the same service and prints a receipt. Modern source lives in `www/phpledger`; the 2015 application is preserved, unmaintained, under `legacy/` with its own terms.

**Requirements:** PHP 8.5.x with the BCMath, PDO, PDO MySQL, mbstring and session extensions, MySQL 8.4 with InnoDB, HTTPS and terminal access. Serve only `www/phpledger/public`.

**Not included yet:** receivables and payables, inventory and cost of sales, tax, historical imports, bank reconciliation, multi-book, foreign-currency posting, translations, offline use, card payments, document scanning.

## What the working preview shows

[![PHP Ledger owner overview: synthetic cash, income, expenses and profit with linked reports.](docs/repository/assets/owner-overview-preview.webp)](docs/repository/assets/owner-overview-preview.webp)

*Actual development capture with fictional books. The sample records 1,000 in receipts and 125 in expenses, leaving 875 in the bank. Reports and POS are being refined for the first supported pilot package.*

> [!NOTE]
> **The 0.1.0-preview package is available for evaluation.** [Download 0.1.0-preview](https://github.com/rmak78/phpledger/releases/tag/v0.1.0-preview), check the ZIP against its SHA-256 file and follow the `INSTALL.md` inside it. Evaluate it with synthetic data; a stable release is not yet available. Modern source lives in `www/phpledger`; historical code is preserved in `legacy/`. Regional accounting review, POS refinement and pilot usability gates remain open.

## Explore the working preview

| Your task | What you can explore |
|---|---|
| **Start a business** | Company setup, a preliminary account template and an isolated sample company. |
| **Record the day** | Receipt and expense drafts, clear posting, balanced journals and linked reversals. |
| **Understand the numbers** | Profit and loss, balance sheet, cash balance, account activity and trial balance, with source drill-down. |
| **Look ahead** | A cash scenario using the inflows and outflows you enter; assumptions remain visible. |
| **Try the counter** | A small illustrative catalog, cash tender and change, a printable receipt and the linked journal. |

<details>
<summary><strong>See the transaction and its accounting entry</strong></summary>

[![A posted sample expense beside its source record and balanced debit and credit entry.](docs/repository/assets/expense-to-journal-preview.webp)](docs/repository/assets/expense-to-journal-preview.webp)

A saved draft has no effect on the books. Posting creates the balanced entry; a correction retains history through a linked reversal. This screenshot uses synthetic data from the working preview.

</details>

<details>
<summary><strong>See the cash POS preview</strong></summary>

[![Early PHP Ledger cash POS with illustrative products, basket, amount due, tender and change.](docs/repository/assets/cash-pos-preview.webp)](docs/repository/assets/cash-pos-preview.webp)

An early working cash-sale flow, with an illustrative unposted basket. A more compact register and clearer payment journey are planned. Stock deduction, COGS, tax, card processing and credit sales are not implemented by this showcase.

</details>

[**Open your sample company →**](https://phpledger.com/demo/)

No registration is needed. Each visitor gets separate synthetic books. Demo records reset hourly; destructive user actions are disabled. The [demo guide](https://github.com/rmak78/phpledger/wiki/Getting-Started) explains what to try and what is still in development.

## How it is built

The modern foundation uses **PHP 8.5, MySQL 8.4/InnoDB and MeekroDB** in BixiSoft's lightweight modular PHP structure. Server-rendered screens and small JavaScript modules keep the application approachable to maintain.

Every financial write follows the same posting path: exact decimal amounts, company/book permissions, atomic transactions, duplicate protection, period controls and immutable posted history. Local checks cover these behaviors; they do not replace independent security, accounting or usability review. [Explore the architecture →](https://github.com/rmak78/phpledger/wiki/Architecture)

Developers can work with the modern source using the [local development guide](docs/DEVELOPMENT.md). Serve only `www/phpledger/public`; the repository root and `legacy/` are not web document roots. Source availability is separate from a tested installable release.

### Currencies and regions

The current preview is English and uses one base currency per book. Choose **USD, EUR, GBP, PKR, INR, MYR, BDT, LKR, NPR or SGD**. Event times are stored in UTC and shown in the terminal's timezone; accounting dates keep their meaning.

Pakistan is first for accounting-framework research, followed by the UK and UAE. Currency selection does not activate country accounting or tax rules. Reviewed translations, flexible date/number formats and fixed, fetched or manually overridden exchange rates are part of the future path. [Countries and currencies →](https://github.com/rmak78/phpledger/wiki/Countries-and-Currencies)

## What comes next

| Next | Outcome |
|---|---|
| **First supported pilot package** | Refined reports and POS, qualified accounting review, observed usability and explicit supported scope. The foundation preview is available now. |
| **Accounting MVP and pilots** | Receivables, payables, opening balances, historical imports, reconciliation and reviewed period-end reporting. |
| **Regional accounting and ERP** | Explainable multi-book differences, reviewed country adapters, inventory/purchasing, production POS and distribution. |

Restaurant, pharmacy, club, trader, distributor, shop and workshop scenarios inform the longer-term product. **Scan document** and AI extraction come later, with human review before saving or posting.

[**Full roadmap**](https://github.com/rmak78/phpledger/wiki/Roadmap) · [**First-package scope**](https://github.com/rmak78/phpledger/wiki/First-Package) · [**Sprint 03 progress**](https://github.com/rmak78/phpledger/milestone/4)

## How to get involved

We welcome thoughtful feedback from business owners, bookkeepers, accountants, designers and developers. Describe the task you need to finish, show a synthetic example, and tell us where the flow gets in your way.

- **Ask a question:** [Discussions Q&A](https://github.com/rmak78/phpledger/discussions/categories/q-a) for usage and installation help.
- **Report a bug:** [open an issue](https://github.com/rmak78/phpledger/issues) with synthetic data and sanitized logs; [SUPPORT.md](SUPPORT.md) explains what to include.
- **Review accounting or contribute:** start with the [contributor guide](https://github.com/rmak78/phpledger/wiki/Contributing-and-Support) or a [good first issue](https://github.com/rmak78/phpledger/issues?q=is%3Aopen+label%3A%22good+first+issue%22).
- **Report a security problem privately:** see [SECURITY.md](SECURITY.md).
- **Discuss a pilot or setup support:** [rmak78@gmail.com](mailto:rmak78@gmail.com).
- **Connect on LinkedIn:** [Rana Mansoor Akbar Khan](https://pk.linkedin.com/in/rmak78).

**Location:** Innovista Chenab, Arcade Plaza, Sector C, DHA Multan, Punjab 60000, Pakistan.

**Supporting the initiative:** BixiTech · BixiSoft · BrownBag · Agency75. These are project supporters, not customers.

The new project-owned code and documentation use the [MIT License](LICENSE), with paid setup, training and support on customer-owned hosting. [Licence scope](LICENSE-SCOPE.md) preserves separate terms for historical code, dependencies, fonts, datasets and company marks; the legacy application's provenance is not resolved by this grant, and MeekroDB keeps its LGPLv3 terms. No stable-release, jurisdiction-compliance or support-response guarantee is implied by the preview.

---

<p align="center">
  <a href="https://phpledger.com/">PHP Ledger</a> &nbsp; · &nbsp;
  <a href="https://github.com/rmak78/phpledger/wiki">Documentation</a> &nbsp; · &nbsp;
  <a href="https://github.com/rmak78/phpledger/wiki/Roadmap">What's next</a>
</p>
