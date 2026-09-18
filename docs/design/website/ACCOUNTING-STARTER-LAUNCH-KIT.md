# Accounting starter launch kit

> **RELEASE VERIFIED - 16 September 2026.** The accounting starter application and media kit are published as [0.4.0-preview](https://github.com/rmak78/phpledger/releases/tag/v0.4.0-preview). The release lead downloaded both public archives and confirmed their bytes and checksums. The hosted starter passed its live synthetic workflow checks. The static release article and guided experiment are also live; [the website receipt](qa/release040-live-publication.json) records the 165-file publication. No social post, email, press submission or scheduled campaign has been sent by preparing or publishing these artifacts.

This kit follows the existing [campaign execution convention](SEO-CAMPAIGN-EXECUTION.md) and the [release marketing procedure](../../strategy/OWNER-QUESTIONS-DASHBOARD-AND-RELEASE-MARKETING.md). It supersedes their older campaign versions, optional-AR/AP descriptions and PHP runtime wording **for this starter campaign only**. Source facts, exact scope, FAQ, a guided experiment and asset briefs are in [the companion fact and demo sheet](ACCOUNTING-STARTER-FACTS-AND-DEMO.md).

**Released version:** `0.4.0-preview`. The release article is `/news/0-4-0-preview/`; it is live, while the separate SEO article remains reusable editorial copy.

## 1. Handoff and verified release destinations

**Audience:** technical self-hosters, PHP developers, bookkeepers who can review synthetic accounting examples, and small-business owners evaluating software with technical help. The first action is to experiment with a complete fictional workflow, then send reproducible feedback.

**Message:** PHP Ledger extends its accounting foundation into an accounting starter spanning core AR/AP, optional Purchasing and Inventory, and manually configured tax. Start with invoices and bills; enable stock workflows when needed.

**Primary CTA:** download the specific verified starter preview, install it separately and follow the synthetic experiment. **Secondary CTA:** read the source and report a reproducible issue. The hosted demo now offers the Accounting starter playground alongside four unchanged multi-year teaching histories. These are five choices, not five deep historical datasets.

| Release field | Destination or status |
|---|---|
| Version | `0.4.0-preview`; published GitHub prerelease. |
| Release date | 16 September 2026 (Asia/Karachi). |
| Application release | [Exact release](https://github.com/rmak78/phpledger/releases/tag/v0.4.0-preview). Archive: 3,179,444 bytes; SHA-256 `b777f9b831db7a6e774e53f510c17ea001514f278ef9a585245ac013dc3aecf6`. |
| Media ZIP | [Press and media kit](https://github.com/rmak78/phpledger/releases/download/v0.4.0-preview/phpledger-0.4.0-preview-media-kit.zip). Archive: 715,562 bytes; SHA-256 `b71f59da80e0b1e0cda753a53d34f0e2e63023cb3996f1ae2637e4bbe05a19a6`. |
| Release article | [Accounting starter release](https://phpledger.com/news/0-4-0-preview/); publication verified. |
| Guided experiment | [Fictional service-business and stock exercises](https://phpledger.com/news/0-4-0-preview/#try-a-small-service-business-experiment); part of the verified live release article. |
| Public project contact | [GitHub Discussions](https://github.com/rmak78/phpledger/discussions). |
| Hosted demo | [Synthetic playground](https://phpledger.com/demo/); starter flows verified live, private sample per visit and hourly resets. |


Public destinations already identified by the repository are [PHP Ledger](https://phpledger.com/), the [source repository](https://github.com/rmak78/phpledger), [release listing](https://github.com/rmak78/phpledger/releases), [issues](https://github.com/rmak78/phpledger/issues) and [discussions](https://github.com/rmak78/phpledger/discussions). The exact release assets above were verified independently of these generic destinations. The static article and experiment links have their own verified publication receipt. Distribution through individual channels remains a separate action.

**Copy boundary:** the sections below are ready for editorial use with resolved release links. Application, media, article and experiment links are verified; distributing posts or email remains a separate action. No customer testimonial, attributed quote, adoption figure, benchmark, revenue figure or certification claim is supplied.

## 2. Press release — launch-day draft

**Headline:** PHP Ledger introduces an open-source accounting starter with invoicing, purchasing and inventory

**Subheading:** The PHP and MySQL development preview connects customer and supplier accounting with optional stock workflows, inviting self-hosters and bookkeepers to test complete examples using synthetic data.

**Release date:** 16 September 2026
**Media contact:** https://github.com/rmak78/phpledger/discussions

PHP Ledger introduces 0.4.0-preview, a development preview that extends its self-hosted accounting foundation into a connected accounting starter. The release brings together customer invoices, supplier bills, partial payments, credit notes, ageing and reconciliation, with optional Purchasing and Inventory modules for businesses that handle stock. It is intended for practical evaluation with fictional records and feedback from developers, bookkeepers and technically supported small businesses.

The starter builds on PHP Ledger's existing double-entry journals, company and book permissions, opening balances, period controls, bank CSV reconciliation and financial reports. Its purpose is to let an evaluator follow everyday business activity from a document to an outstanding balance and then into the accounting records. A customer invoice, a partial receipt and the remaining amount due belong to the same accounting workflow. A supplier bill and its payment use the corresponding shared payable records.

Accounts receivable and accounts payable are included in the required accounting core, with separate internal module ownership. Businesses can hide the navigation they do not need without disabling the underlying accounting services or removing balances from reports. Purchasing and Inventory use the existing optional-module controls, so a service business can begin with invoices and bills while a stock business enables the additional workflows it needs.

Purchasing supports purchase orders, partial goods receipts and supplier bills recorded after receipt. A business can order ten units, receive six, receive the remaining four later and match the supplier bill to the quantities actually received. Goods waiting for a bill remain visible in a received-but-unbilled reconciliation. Supplier debt belongs to accounts payable throughout; the Purchasing module does not create a separate supplier balance that an operator must keep in step manually.

Inventory provides a shared product catalogue, one stock location per company, immutable movement history and moving weighted-average costing. Stock invoices issue goods and record their cost through the shared accounting services. The release also supports counts, reviewed value adjustments and source-linked returns. Quantities, carrying value and selling prices remain separate measures, helping an evaluator check what is held and what its recorded cost means.

The core tax engine is manually configured and country-neutral. An owner can enter tax codes, dated percentage rates and input/output account mappings, then choose whether document prices are entered inclusive or exclusive of tax. Documents display net, tax and total separately and preserve the reviewed tax snapshot. Credits use their original document's tax basis. No country rates, registration rules, tax filings or statutory compliance are inferred from a business's currency or location.

Existing-business evaluation also includes reviewed conversion of opening debts and stock. Party and product mappings must reconcile to the original opening journal amounts. Conversion links the operational records to that existing basis rather than posting the same balances again. Ambiguous or already-used opening amounts are rejected for further review. Posted corrections preserve source identity and traceable reversing entries; the history of what was posted remains available.

The release deliberately limits the first stock workflow to one location. Multiple warehouses, batches, serial numbers, expiry tracking, manufacturing, landed cost and letter-of-credit workflows remain outside this starter. Quotes are excluded and preserved separately for a future plugin. Advances, unapplied credits, refunds, bank feeds and new public financial write APIs are also deferred. The existing illustrative cash POS continues to use its own sample catalogue and does not yet deduct shared Inventory stock.

PHP Ledger runs on PHP 8.2 or newer, with PHP 8.3 recommended, and MySQL 8.4. Project-owned code is licensed under AGPL-3.0-or-later, with a separate commercial licence available. Self-hosting gives an operator responsibility for hosting, backups, access controls and upgrades; it does not establish that a particular installation has passed accounting, security or tax review. The development-preview label remains part of this release.

Evaluators are invited to install 0.4.0-preview in a separate environment and try a fictional service or stock business. A useful first test is to post an invoice, collect part of it, apply a credit and collect the remainder, then check ageing and the ledger. Stock evaluators can follow an order through partial receipts, a matched bill and a stock sale. Reports should include the version, steps, expected result and a synthetic example, without customer records or credentials.

The release archive, installation instructions and recorded limitations are available at https://github.com/rmak78/phpledger/releases/tag/v0.4.0-preview. The source repository is [github.com/rmak78/phpledger](https://github.com/rmak78/phpledger), and project information is at [phpledger.com](https://phpledger.com/). The guided experiment is at https://phpledger.com/news/0-4-0-preview/#try-a-small-service-business-experiment.

**About PHP Ledger**

PHP Ledger is open-source, self-hosted double-entry accounting software built with PHP and MySQL. Its current development direction is a country-neutral accounting core that optional business and industry modules can reuse. Regional connectors and more advanced operational workflows require their own implementation and review.

## 3. SEO launch article — launch-day draft

### Editorial metadata

- **SEO title:** PHP Ledger accounting starter: invoices, bills and stock
- **H1:** A self-hosted accounting starter for invoices, bills and stock
- **Meta description:** Explore PHP Ledger's accounting starter: invoices, bills, partial payments, purchasing and inventory. Try a synthetic business on your own PHP hosting.
- **Proposed slug:** `/news/accounting-starter-ar-ap-purchasing-inventory/`
- **Canonical after publication:** `https://phpledger.com/news/accounting-starter-ar-ap-purchasing-inventory/`
- **Search intent:** evaluate self-hosted accounting with invoicing and optional inventory.
- **Natural topic phrases:** self-hosted accounting software; open-source invoicing; PHP accounting; supplier bills; purchase orders and inventory; accounts receivable and payable.
- **Internal links:** product overview, installation/download, accounting guides, the guided starter experiment and release notes. Resolve exact destinations through the existing website content register.
- **Editorial rule:** describe one useful workflow per section. Do not promise rankings, indexing, rich results or inclusion in AI answers. Use the actual publication date and screenshots from the exact candidate.

### Article body

PHP Ledger 0.4.0-preview brings its accounting foundation into a more complete set of everyday workflows. You can create customer invoices and supplier bills, record partial payments, issue linked credits, inspect ageing and reconcile the results to the general ledger. Businesses that hold stock can also enable Purchasing and Inventory and follow goods from an order through receipt, supplier billing and sale.

This is a development preview for experimentation. The useful starting point is a fictional business in a separate installation: make a few transactions, follow their accounting effects and check whether the workflow is understandable. Developers can inspect the source; bookkeepers can challenge the examples and balances. The aim is concrete feedback on a usable accounting starter, with its remaining limits visible.

### Start with the money customers owe and the bills you owe suppliers

Accounts receivable records customer debt. Accounts payable records supplier debt. Both are included in the required accounting core, so a business does not have to add a separate receivables engine before using another module.

For example, create a customer invoice for 1,000, collect 400 and inspect the remaining 600. Apply a linked credit of 100 and the balance becomes 500. Collect that final amount and check that the document closes. Repeat the corresponding supplier-bill flow. These are accounting examples using fictional amounts, not instructions about a particular business's tax treatment.

The document, its payments and credits connect to the shared open-item ledger. Ageing shows what remains outstanding at a selected date, with drilldown to the source. Control reconciliation compares those open items with the general ledger. You can see whether the detailed amounts customers and suppliers owe agree with the account balances.

### Keep the interface relevant to a simple business

A small consultancy may need customer invoices and a few supplier bills. It may never receive physical goods. Purchasing and Inventory can remain disabled, and the owner can hide AR or AP navigation that is not useful to the people entering records.

Hiding navigation does not remove financial balances or turn off the accounting services. That distinction matters when the business grows or another module uses them. A future distributor workflow can own route planning and delivery while passing customer debt and collections through shared AR. Those distributor operations are a future extension; this release supplies the accounting services they can reuse.

### Receive goods before the supplier bill arrives

Stock businesses often receive an order in parts. The starter keeps the purchase order, physical receipt and supplier bill as distinct linked records.

Try ordering ten units at a net cost of ten each. Receive six units first and four later. Each receipt increases recorded stock and the received-but-unbilled balance. When the supplier's bill arrives, match it to the quantities actually received. The liability to the supplier is then held in AP, while the receipt matching explains what happened to the clearing balance.

Price or exchange-rate differences require an explicit reviewed variance account. They do not silently rewrite the recorded receipt cost. This gives an evaluator a clear place to inspect a difference before accepting the bill. Partial billing and supplier returns are part of the same source-linked workflow.

### Understand stock quantity and recorded cost

Inventory uses one stock location per company and moving weighted-average costing. When receipts arrive at different costs, the remaining quantity and carrying value determine the average cost used for subsequent issues. The final issue consumes the remaining recorded value so that zero quantity does not quietly retain an unexplained balance.

Posting a stock invoice also records the goods leaving stock and their cost of goods sold. Returns link to original movements and their historical cost. Counts and reviewed value adjustments provide explicit ways to address differences, with checks against stale quantities and values.

The valuation report distinguishes quantity and carrying value from selling price. Ten items priced for sale at twenty each do not automatically have an inventory cost of two hundred. The source movements and their accounting entries explain the recorded cost. Multiple warehouses, batches, serial numbers and manufacturing are outside this first stock workflow.

### Choose inclusive or exclusive tax entry explicitly

The tax engine starts with manual configuration. Choose tax codes, dated rates and the accounts for input and output tax, then select inclusive or exclusive price entry. Each document shows net, tax and total separately.

With a fictional ten-percent tax rate, a tax-exclusive net amount of 100 produces tax of 10 and a total of 110. Entering 110 as tax-inclusive produces the same split. These are demonstration figures only. The software does not decide whether that rate applies to an actual product, business or country.

Posted documents preserve their reviewed price mode and tax snapshot. A later rate change does not rewrite earlier records, and a linked credit uses its original document's tax basis. Country tax packs, withholding, filing and more complex recovery rules remain outside the core calculation engine delivered here.

### Bring opening balances into the workflow without duplicating them

An existing business may already have an opening trial balance and a list of unpaid invoices or bills. Posting those debts again as new documents would duplicate their accounting effect.

The starter instead provides a reviewed conversion. Map each opening debt to its party and reconcile the total to the existing control account. Opening stock has a separate product-and-quantity review against its original value. Confirmation links the operational detail to the existing journal basis. Ambiguous amounts or unsupported later activity stop the conversion for review rather than being guessed.

### What to try, and what to leave for later

Begin with the service example: an invoice, partial receipt, credit and final receipt. Then inspect historical ageing before and after those actions. If you need stock, follow the ten-unit purchase example, pay part of the supplier bill, sell a few units and check inventory valuation and the corresponding journal entries. Existing bank CSV reconciliation can match the resulting payment lines.

Quotes are excluded from this starter and preserved for a separate plugin. Bank feeds, advances, unapplied credits, refunds, public financial write APIs and advanced stock operations are also deferred. The existing cash POS showcase has its own illustrative catalogue and does not deduct shared inventory.

Download the verified development preview from https://github.com/rmak78/phpledger/releases/tag/v0.4.0-preview and follow https://phpledger.com/news/0-4-0-preview/#try-a-small-service-business-experiment with synthetic data. PHP 8.2 or newer, MySQL 8.4, HTTPS and command-line access are required; PHP 8.3 is the recommended deployment version. Project-owned code uses AGPL-3.0-or-later, with commercial licensing available separately. Report a reproducible result in the [issue tracker](https://github.com/rmak78/phpledger/issues) or discuss a workflow in [GitHub Discussions](https://github.com/rmak78/phpledger/discussions).

## 4. LinkedIn — three variants

### Company page: the complete starter

PHP Ledger 0.4.0-preview introduces a self-hosted accounting starter for people who want to inspect the records behind their business numbers.

Core AR/AP now connects customer invoices, supplier bills, partial payments, linked credits, ageing and control reconciliation. Optional Purchasing and Inventory add purchase orders, partial goods receipts, later supplier bills, stock movements and moving weighted-average valuation.

A simple business can keep optional modules off and hide unused AR/AP navigation. A stock business can enable the shared services without creating another supplier or customer ledger.

The core tax engine supports manually configured rates and inclusive or exclusive price entry. Country tax rules and filing are outside this preview. Quotes are reserved for a separate plugin.

We are inviting developers, bookkeepers and technically supported business owners to experiment with fictional records. Try an invoice for 1,000, collect 400, credit 100 and collect the final 500. Then check the source, ageing and journal.

Development preview. Start in a separate installation with synthetic data.

Release: https://github.com/rmak78/phpledger/releases/tag/v0.4.0-preview
Walkthrough: https://phpledger.com/news/0-4-0-preview/#try-a-small-service-business-experiment

#OpenSource #AccountingSoftware #SelfHosted #PHP

### Founder/maintainer voice: why this milestone exists

I wanted the next PHP Ledger milestone to answer a practical question: can someone follow a small business transaction from the document through payment and back to the accounting records?

That is the focus of 0.4.0-preview. Invoices, bills, partial payments, credits, ageing and reconciliation now form the accounting starter. AR/AP is part of the base; Purchasing and Inventory are optional.

The useful test is simple. Order ten items. Receive six, then four. Match the supplier bill. Sell a few items and collect part of the customer invoice. Check what is owed, what remains in stock and what reached the ledger.

There are deliberate limits: one stock location, moving weighted-average cost and manually configured tax. Quotes, advanced stock features and country filing remain separate work.

I am looking for concrete feedback from self-hosters and bookkeepers: where did the next step feel unclear, and can you reproduce a result that does not reconcile?

Please use synthetic data and include the version and steps. This is a development preview, not a claim of accounting or tax certification.

https://github.com/rmak78/phpledger/releases/tag/v0.4.0-preview

*Internal note: use this first-person version only from the actual owner/maintainer. It is a suggested post, not an attributed statement already made.*

### Bookkeeper/developer collaboration

A useful software review can pair two kinds of questions:

- Can I install it, inspect its source and reproduce a workflow?
- Can I explain the document, remaining balance and ledger effect?

The PHP Ledger accounting starter gives that review a concrete sequence: invoice → partial receipt → credit → final receipt. For stock businesses: purchase order → partial receipts → matched bill → stock sale.

AR/AP is bundled with the core. Purchasing and Inventory are optional and use the same accounting records. Tax codes and dated rates are configured manually, with inclusive/exclusive document entry.

If you are a PHP self-hoster or a bookkeeper willing to review fictional examples, we would value a reproducible experiment. Tell us what you entered, what you expected and what happened.

Download 0.4.0-preview: https://github.com/rmak78/phpledger/releases/tag/v0.4.0-preview
Guided example: https://phpledger.com/news/0-4-0-preview/#try-a-small-service-business-experiment

Development preview; use a separate installation and synthetic data.

## 5. X — thread and standalone posts

### Eight-post thread

1. PHP Ledger 0.4.0-preview: core invoices, bills, partial payments, credits and ageing. Optional Purchasing and Inventory. A self-hosted development preview for fictional experiments. https://github.com/rmak78/phpledger/releases/tag/v0.4.0-preview

2. AR and AP are included in the core. Hide navigation you do not need without removing balances or disabling the services other modules use.

3. Try this: invoice 1,000 → receive 400 → credit 100 → receive 500. Check the remaining balance, historical ageing and journal after each step.

4. For stock: order ten units → receive six → receive four → match the supplier bill. Received-but-unbilled reconciliation shows goods still waiting for billing.

5. Inventory uses one location and moving weighted-average cost. Stock invoices issue goods and post their cost. Selling price and recorded stock cost remain separate measures.

6. Configure tax codes and dated rates manually. Choose inclusive or exclusive document prices and see net, tax and total. Country tax rules and filing are outside this preview.

7. Quotes are excluded for a future plugin. Multiple warehouses, serials/batches, landed cost, bank feeds and public financial write APIs are also deferred. The illustrative POS does not deduct shared stock.

8. Try a fictional workflow; report the version, steps and result. Guide: https://phpledger.com/news/0-4-0-preview/#try-a-small-service-business-experiment Source: https://github.com/rmak78/phpledger

### Standalone: accounting experiment

Invoice 1,000. Receive 400. Credit 100. Receive 500. Trace the balance in PHP Ledger: core AR/AP, optional Purchasing and Inventory. Development preview; use fictional data. https://github.com/rmak78/phpledger/releases/tag/v0.4.0-preview

### Standalone: stock experiment

Order, receive in parts, then match the supplier bill. PHP Ledger connects Purchasing, shared Inventory and AP. Reconcile goods awaiting billing. Development preview; use fictional data. https://github.com/rmak78/phpledger/releases/tag/v0.4.0-preview

### Standalone: source-first invitation

PHP self-hosters and bookkeepers: help review a complete accounting example in PHP Ledger 0.4.0-preview. Invoices, bills, partial payments, credits and ageing, plus optional stock workflows. Source: https://github.com/rmak78/phpledger

*Channel check: validate final character counts after replacing variables and shortening links through the platform. Do not remove the development-preview qualification to fit.*

## 6. Facebook and community copy

### Facebook page

The PHP Ledger accounting starter gives you a complete small example to explore: create an invoice, collect part of it, apply a credit and check what remains due.

AR/AP is included in the core. If the business handles stock, optional Purchasing and Inventory let you order goods, receive them in parts, match the supplier bill and inspect stock valuation. Tax codes and rates are configured manually, with a choice of inclusive or exclusive prices.

We are inviting technical self-hosters, bookkeepers and business owners with technical support to try fictional records and share clear feedback. You do not need to load real customer information to test the workflow.

This is a development preview. Quotes, advanced stock features and country tax filing remain outside the starter. The existing sample POS does not deduct shared Inventory stock.

Download and installation notes: https://github.com/rmak78/phpledger/releases/tag/v0.4.0-preview
Step-by-step fictional example: https://phpledger.com/news/0-4-0-preview/#try-a-small-service-business-experiment

### Community forum / self-hosting group

**Suggested title:** PHP Ledger accounting starter: looking for feedback on a synthetic invoice-to-payment flow

Disclosure: I am involved with PHP Ledger.

We have prepared 0.4.0-preview, a self-hosted PHP/MySQL accounting preview with core invoices, bills, partial payments, credits, ageing and reconciliation. Optional Purchasing and Inventory add partial goods receipts, later bill matching and one-location moving weighted-average stock valuation.

I would appreciate feedback from people who can install it separately and run a fictional example. The guided sequence has expected balances, so a useful report can point to the exact step where your result differs or the interface becomes unclear.

Project-owned code is AGPL-3.0-or-later. It requires PHP 8.2+ and MySQL 8.4. Tax configuration is manual; country filing, bank feeds and public financial write APIs are outside this preview. Quotes are a separate future plugin.

Release: https://github.com/rmak78/phpledger/releases/tag/v0.4.0-preview
Source: https://github.com/rmak78/phpledger
Experiment: https://phpledger.com/news/0-4-0-preview/#try-a-small-service-business-experiment

Please keep feedback synthetic. I can discuss the implemented accounting flow and reproducible bugs, but I am not presenting it as certified for a jurisdiction or production-ready for every business.

### Reddit-style post

**Suggested title:** I am working on PHP Ledger, a self-hosted accounting starter; feedback wanted on invoices, payments and stock

Disclosure up front: I am part of this project.

The new 0.4.0-preview preview extends PHP Ledger's existing journals and reports into AR/AP workflows: invoices, bills, partial payments, linked credits, ageing and reconciliation. Purchasing and Inventory are optional. They share the accounting records; a goods receipt and its later supplier bill stay separate but linked.

The first stock scope is intentionally small: one location, moving weighted-average cost, stock invoices, returns and reviewed adjustments. Tax codes/rates are manual, with inclusive or exclusive price entry. Quotes, multiple warehouses, batches, manufacturing and country filing are deferred. The existing sample POS does not deduct shared stock.

I would like feedback on a specific test rather than a general feature wish list: invoice 1,000, receive 400, credit 100 and receive 500. Do the balance, history and next action make sense at each step? There is also a ten-unit purchase/receipt/bill example.

Source: https://github.com/rmak78/phpledger
Release: https://github.com/rmak78/phpledger/releases/tag/v0.4.0-preview
Guide: https://phpledger.com/news/0-4-0-preview/#try-a-small-service-business-experiment

It is a development preview, uses AGPL-3.0-or-later for project-owned code and needs PHP 8.2+/MySQL 8.4. Please use synthetic records. I will be available to discuss reproducible issues when this is posted.

*Internal note: the actual poster must be affiliated and available to answer. Check the chosen community's current self-promotion and submission rules before posting; this draft is not a claim that a particular subreddit permits it.*

### Hacker News / Show HN

**Suggested title:** Show HN: PHP Ledger, a self-hosted accounting starter in PHP and MySQL

**Submission URL after release:** https://phpledger.com/news/0-4-0-preview/ or the verified repository release, according to the destination chosen by the posting maintainer.

**First comment draft:**

I am involved with PHP Ledger. This preview builds on its double-entry accounting foundation with invoices, bills, partial payments, credits, ageing and reconciliation. AR/AP is part of the base; Purchasing and Inventory are optional and use the same posting services.

The practical experiment is small: invoice 1,000, collect 400, credit 100 and collect 500. For stock, order ten units, receive six and four, then match the bill. Inventory uses one location and moving weighted-average cost. Tax codes and rates are manual, including inclusive/exclusive price entry.

The code is PHP/MeekroDB on MySQL, with BCMath for financial arithmetic. Requirements are PHP 8.2+ and MySQL 8.4. Project-owned code is AGPL-3.0-or-later.

I would particularly value feedback on workflow clarity, reproducible accounting differences and the developer experience of self-hosting it. It is a development preview. Quotes, advanced stock operations, country filing and public financial write APIs are outside the release. Please test with fictional records.

Guide: https://phpledger.com/news/0-4-0-preview/#try-a-small-service-business-experiment. Known boundaries and validation are linked from https://github.com/rmak78/phpledger/releases/tag/v0.4.0-preview.

*Internal note: this is candidate submission copy only. Verify current community rules and the exact public artifact before choosing a submission type. Do not claim a hosted starter demo exists unless it has been released.*

## 7. Email announcement

**Subject A:** Try the PHP Ledger accounting starter with a fictional business
**Subject B:** Invoices, bills and stock: the next PHP Ledger preview
**Preheader:** Core AR/AP, optional Purchasing and Inventory, and a guided synthetic experiment.

Hello,

PHP Ledger 0.4.0-preview extends the self-hosted accounting foundation into a connected starter: customer invoices, supplier bills, partial payments, linked credits, ageing and reconciliation.

AR and AP are included in the accounting core. Purchasing and Inventory are optional, so a service business can start with documents and payments while a stock business adds purchase orders, partial receipts, later bill matching and one-location moving weighted-average valuation.

The core tax engine supports manually configured codes and dated rates. You can enter prices inclusive or exclusive of tax and review net, tax and total separately. Country tax rules and filing remain outside this preview.

**A useful first experiment**

Create a fictional invoice for 1,000. Receive 400, apply a credit of 100 and receive the remaining 500. Check the outstanding balance, ageing and journal at each step. The guide also includes an order for ten stock units received in two deliveries.

**Download the development preview:** https://github.com/rmak78/phpledger/releases/tag/v0.4.0-preview
**Follow the guided experiment:** https://phpledger.com/news/0-4-0-preview/#try-a-small-service-business-experiment

Please install it separately and use synthetic records. Quotes, advances/unapplied credits/refunds, advanced inventory features and bank feeds remain deferred. The release notes explain the current boundaries and validation.

If a result differs from the guide or an action is unclear, share the version, steps, expected result and a fictional example in the [issue tracker](https://github.com/rmak78/phpledger/issues). Please leave customer data, credentials and private backups out of reports.

Thank you,
PHP Ledger project

*Internal distribution note: use the owner's existing authorised mailing process and audience. This kit creates no mailing list, subscription consent, sender identity or send authorization. Add the normal verified sender/contact and applicable subscription-management footer before sending.*

## 8. Editorial sequence and evidence to retain

1. The release lead resolves version, archive, checksum, notes and installation evidence. The marketing editor checks the fact sheet against that exact scope and removes anything that did not make the package.
2. Publish the canonical launch article and guided experiment through the existing website process when publication is authorised. Use verified release links; record article URLs and dates separately from package publication.
3. Prepare channel-specific posts with the actual current screenshot, clear affiliation and the development-preview qualification. Use the company, founder or community variant appropriate to the real account owner.
4. Send or post only through explicitly authorised channels. Retain the final copy, destination, actual public URL and timestamp for each action. A draft or submission is not a publication receipt.
5. Collect reproducible feedback by workflow and version. Keep bug reports separate from feature requests. Track useful experiments and issues using the data actually available; set no invented adoption or search-ranking target.

For simple attribution, the team may consistently use `utm_campaign=accounting_starter` with truthful channel/source names on website links. Keep the article canonical free of campaign parameters. This is an editorial convention, not a claim that analytics, consent configuration or a particular platform account is already connected.

## Actual screenshot media

[Download the six original PNG assets and read their captions](accounting-starter-media/README.md). The [manifest](accounting-starter-media/manifest.json) records alt text, hashes, synthetic baselines and local capture context. These are faithful application captures, not generated product mockups or live-publication proof. The final release media ZIP also separates press, SEO, social and email copy for the marketing team.
