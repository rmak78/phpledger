<p align="center">
  <a href="https://phpledger.com/">
    <img src="docs/repository/assets/phpledger-logo.webp" width="520" alt="PHP Ledger">
  </a>
</p>

<h1 align="center">Clear books. Confident decisions.</h1>

<p align="center">
  Accounting for the people running the business.<br>
  Built for owners, accountants and bookkeepers. Designed to live on your own hosting.
</p>

<p align="center">
  <img src="docs/repository/assets/development-preview.svg" width="215" height="26" alt="Status: development preview">
</p>

<p align="center">
  <a href="https://phpledger.com/demo/"><strong>Try the demo</strong></a> &nbsp; · &nbsp;
  <a href="https://github.com/rmak78/phpledger/wiki">Read the Wiki</a> &nbsp; · &nbsp;
  <a href="https://github.com/rmak78/phpledger/wiki/Roadmap">Explore the roadmap</a> &nbsp; · &nbsp;
  <a href="https://github.com/rmak78/phpledger/issues">Share feedback</a>
</p>

---

## From the day's work to a clearer picture

Record an expense. Follow its balanced entry. See what changed in the business.

PHP Ledger is being rebuilt around that connected journey: useful daily tasks for an owner, traceable records for an accountant, and a practical cash register for a small shop. The goal is a welcoming first five minutes and dependable books over the years that follow.

[![PHP Ledger owner overview: synthetic cash, income, expenses and profit with linked reports.](docs/repository/assets/owner-overview-preview.webp)](docs/repository/assets/owner-overview-preview.webp)

*Actual development capture with fictional books. The sample records 1,000 in receipts and 125 in expenses, leaving 875 in the bank. Reports and POS remain development previews.*

> [!NOTE]
> **Evaluate the accounting core.** [Download 0.1.2-preview](https://github.com/rmak78/phpledger/releases/tag/v0.1.2-preview) with production dependencies and installation instructions. Follow opening, running and closing account balances; manage accounts; save, review, post and reverse general journals. Modern source lives in `www/phpledger`; historical code is preserved in `legacy/`. Regional accounting review and pilot usability gates remain open.

## Explore the working preview

| Your task | What you can explore |
|---|---|
| **Start a business** | Company setup, a preliminary account template and an isolated sample company. |
| **Record the day** | Receipt and expense drafts, clear posting, balanced journals and linked reversals. |
| **Work on the books** | Account creation, audited name/status changes and general-journal drafts with a separate posting review. Account administration is available in an installation; the public demo keeps it read-only. |
| **Understand the numbers** | Profit and loss, balance sheet, cash balance, trial balance and account statements with opening, running and closing balances, linked to their sources. |
| **Look ahead** | A cash scenario using the inflows and outflows you enter; assumptions remain visible. |
| **Try the counter** | Click-to-add sample products, quick cart controls, separate review/cash confirmation, a printable receipt and linked journal. |

<details>
<summary><strong>See the transaction and its accounting entry</strong></summary>

[![A posted sample expense beside its source record and balanced debit and credit entry.](docs/repository/assets/expense-to-journal-preview.webp)](docs/repository/assets/expense-to-journal-preview.webp)

A saved draft has no effect on the books. Posting creates the balanced entry; a correction retains history through a linked reversal. This screenshot uses synthetic data from the working preview.

</details>

<details>
<summary><strong>See the cash POS preview</strong></summary>

[![PHP Ledger click-to-add POS with selected products, cart quantity controls and a separate review action.](docs/repository/assets/cash-pos-click-preview.png)](docs/repository/assets/cash-pos-click-preview.png)

The owner-approved cash-sale layout: click a product to add one, adjust quantities in the cart, then review the sale before confirming cash. This actual capture contains an unposted synthetic cart. Stock deduction, COGS, tax, card processing and credit sales are not implemented by this showcase.

</details>

[**Open your sample company →**](https://phpledger.com/demo/)

No registration is needed. Each visitor gets separate synthetic books. Demo records reset hourly; destructive user actions are disabled. The [demo guide](https://github.com/rmak78/phpledger/wiki/Getting-Started) explains what to try and what is still in development.

## Built with care, kept understandable

The modern foundation uses **PHP 8.5, MySQL 8.4/InnoDB and MeekroDB** in BixiSoft's lightweight modular PHP structure. Server-rendered screens and small JavaScript modules keep the application approachable to maintain.

Every financial write follows the same posting path: exact decimal amounts, company/book permissions, atomic transactions, duplicate protection, period controls and immutable posted history. Local checks cover these behaviors; they do not replace independent security, accounting or usability review. [Explore the architecture →](https://github.com/rmak78/phpledger/wiki/Architecture)

Developers can work with the modern source using the [local development guide](docs/DEVELOPMENT.md). Serve only `www/phpledger/public`; the repository root and `legacy/` are not web document roots. Source availability is separate from a tested installable release.

### A regional product, one clear foundation

The current preview is English and uses one base currency per book. Choose **USD, EUR, GBP, PKR, INR, MYR, BDT, LKR, NPR or SGD**. Event times are stored in UTC and shown in the terminal's timezone; accounting dates keep their meaning.

Pakistan is first for accounting-framework research, followed by the UK and UAE. Currency selection does not activate country accounting or tax rules. Reviewed translations, flexible date/number formats and fixed, fetched or manually overridden exchange rates are part of the future path. [Countries and currencies →](https://github.com/rmak78/phpledger/wiki/Countries-and-Currencies)

Early [tax research](docs/tax/README.md) covers eight countries and seven business types. Its 81 candidate regimes are **disabled and unreviewed**; they do not calculate taxes or establish eligibility. The [accounting rule register](docs/accounting/CORE_RULE_REGISTER.md) connects the core's controls with ICAP, ICMAP and ACCA guidance and records the remaining review gates.

## Where we go from here

| Next | Outcome |
|---|---|
| **Complete the accounting core** | Statements, chart management and general journals are in this preview. Next: reviewed opening balances/imports, fiscal-period administration, bank reconciliation and supported reports. |
| **Extension and integration foundation** | Optional-module contracts and lifecycle, then a versioned business API and MCP access using the same accounting services and permissions. These interfaces are planned. |
| **Optional business modules** | AR → AP → purchasing/inventory → reviewed Pakistan tax → shop POS → restaurant POS → distribution and specialist modules. Required tax support precedes affected production use. |

The core must work independently of add-ons. Shop and restaurant interfaces will share checkout and accounting services while providing their own operational workflows. Qualified accounting review, observed usability and an explicit supported scope remain release gates. [Module build order and completion gates →](docs/MODULE-ROADMAP.md)

Restaurant, pharmacy, club, trader, distributor, shop and workshop scenarios inform the longer-term product. **Scan document** and AI extraction come later, with human review before saving or posting.

[**Full roadmap**](https://github.com/rmak78/phpledger/wiki/Roadmap) · [**Package scope**](https://github.com/rmak78/phpledger/wiki/First-Package) · [**Core release validation**](docs/repository/sprint-04/CORE-0.1.2-VALIDATION.md)

## Help shape PHP Ledger

We welcome thoughtful feedback from business owners, bookkeepers, accountants, designers and developers. Describe the task you need to finish, show a synthetic example, and tell us where the flow gets in your way.

- **Explore and report:** [open an issue](https://github.com/rmak78/phpledger/issues).
- **Review accounting or contribute:** start with the [contributor guide](https://github.com/rmak78/phpledger/wiki/Contributing-and-Support).
- **Discuss a pilot or setup support:** [rmak78@gmail.com](mailto:rmak78@gmail.com).
- **Connect on LinkedIn:** [Rana Mansoor Akbar Khan](https://pk.linkedin.com/in/rmak78).

**Location:** Innovista Chenab, Arcade Plaza, Sector C, DHA Multan, Punjab 60000, Pakistan.

**Companies that support our open-source initiative:** [BixiTech](https://www.bixitech.com/) · [BixiSoft](https://bixisoft.com/) · [BrownBag](https://brownbag.pk/) · [Agency75](https://agency75.com/).

The new project-owned code and documentation use the [MIT License](LICENSE), with paid setup, training and support on customer-owned hosting. [Licence scope](LICENSE-SCOPE.md) preserves separate terms for historical code, dependencies, fonts, datasets and company marks; the legacy application's provenance is not resolved by this grant. No stable-release, jurisdiction-compliance or support-response guarantee is implied by the preview.

---

<p align="center">
  <a href="https://phpledger.com/">PHP Ledger</a> &nbsp; · &nbsp;
  <a href="https://github.com/rmak78/phpledger/wiki">Documentation</a> &nbsp; · &nbsp;
  <a href="https://github.com/rmak78/phpledger/wiki/Roadmap">What's next</a>
</p>
