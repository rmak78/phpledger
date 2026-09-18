# Accounting starter: facts, FAQ and guided experiment

> **RELEASE VERIFIED - 16 September 2026.** The application and media kit are published as [0.4.0-preview](https://github.com/phpledger/phpledger/releases/tag/v0.4.0-preview), with public byte/checksum verification and live starter workflow evidence reported by the release lead. The static release article and guided experiment are also live, with [publication evidence](qa/release040-live-publication.json). The subsequently requested country-chart research, deeper example companies and UX review remain separate work and are not capabilities to announce as shipped.

This sheet supports the [launch copy kit](ACCOUNTING-STARTER-LAUNCH-KIT.md). It provides a factual review boundary and a reproducible fictional experiment. Do not send or publish either artifact solely because the copy is complete.

## 1. Fact sheet

| Item | Approved factual wording | Source |
|---|---|---|
| Product | PHP Ledger is open-source, self-hosted double-entry accounting software built with PHP and MySQL. | [Repository README](../../../README.md) |
| This milestone | An accounting starter spanning core AR/AP, optional Purchasing and Inventory, and a manually configured core tax engine. | [Starter implementation record](../../repository/sprint-06/ACCOUNTING-STARTER.md) |
| Availability | 0.4.0-preview application/media assets published and downloaded byte-identically; hosted starter workflows verified. The static article publication is also verified in its separate receipt. | [Starter status](../../repository/sprint-06/ACCOUNTING-STARTER.md) |
| Runtime | PHP 8.2+; PHP 8.3 recommended. MySQL 8.4, required PHP extensions, HTTPS and command-line access. | [Package installation guide](../../../resources/release/INSTALL.md) |
| Licence | Project-owned current code uses AGPL-3.0-or-later; a separate commercial licence is available. Third-party and historical terms remain distinct. | [Licensing policy](../../LICENSING-POLICY.md), [licence scope](../../../LICENSE-SCOPE.md) |
| AR/AP | Invoices, bills, partial/final allocated payments, linked credits, historical ageing and control reconciliation. Required core modules with optional navigation hiding. | [AR/AP service](../../../www/phpledger/includes/functions/ar_ap_functions.php), [module ownership](../../MODULE-ROADMAP.md) |
| Purchasing | Orders, partial receipts, later supplier bills, receipt matching, reviewed variances, returns and received-but-unbilled reconciliation. Supplier debt belongs to AP. | [Purchasing service](../../../www/phpledger/includes/functions/purchasing_functions.php) |
| Inventory | Shared products, one stock location, immutable movements, moving weighted-average cost, counts, reviewed adjustments, stock invoice issues and source-linked returns. | [Inventory service](../../../www/phpledger/includes/functions/inventory_functions.php) |
| Tax | Manually configured codes and dated rates, input/output accounts, exclusive/inclusive entry and frozen document snapshots. Country applicability and filing are not inferred. | [Core tax service](../../../www/phpledger/includes/functions/tax_functions.php), [starter tax limits](../../repository/sprint-06/ACCOUNTING-STARTER.md#core-tax-calculation) |
| Opening balances | Reviewed party/product mapping links operational detail to existing opening journals without posting balances twice. Ambiguous or later-used bases can be rejected. | [Opening debt conversion](../../../www/phpledger/includes/functions/opening_conversion_functions.php), [Inventory opening services](../../../www/phpledger/includes/functions/inventory_functions.php) |
| Reconciliation | Open items versus GL controls, stock value versus inventory accounts, received-but-unbilled versus clearing, and the existing exact bank CSV matching workflow. | [Development contracts](../../DEVELOPMENT.md), [starter record](../../repository/sprint-06/ACCOUNTING-STARTER.md) |
| Corrections | Traceable reversals/reposts retain document identity; dependencies, periods and bank-reconciliation restrictions remain enforced. | [Architecture](../../ARCHITECTURE.md), [release notes](../../../resources/release/RELEASE-NOTES.md) |
| Integration | Existing scoped read API/MCP remains. The starter exposes internal PHP services; it adds no public financial write API or autonomous external delivery. | [Integrations](../../INTEGRATIONS.md), [starter record](../../repository/sprint-06/ACCOUNTING-STARTER.md) |
| Preview boundary | Technical checks and sample journeys are evidence for the tested scope, not professional accounting/tax certification or every host's production readiness. | [Validation record](../../VALIDATION.md) |

### Claims that must not enter launch copy

- No guaranteed savings, speed, installation time, ranking, adoption, uptime or performance numbers.
- No fabricated customer, accountant endorsement, testimonial, press quote or usage statistic.
- No country-certified accounting, automatic tax compliance, e-filing, bank feeds or automatic payment processing.
- No completed quotes plugin, distributor routes, delivery planning, manufacturing, batches, serials, expiry, multiple warehouses, LC or landed-cost claims.
- No claim that the existing sample POS uses shared Inventory or the new document tax engine.
- No claim that the forthcoming country-chart catalogs, expanded sample-company program or UX prototypes are implemented or reviewed.
- Describe the verified hosted Accounting starter playground separately from the four existing multi-year teaching histories; do not imply eleven new examples are shipped.

Use **first accounting starter spanning core AR/AP and optional stock/purchasing** when describing the milestone. Earlier previews delivered real foundations and workflows; avoid describing them as unusable or erasing their release history.

## 2. FAQ for the launch page and team responses

### What can I experiment with?

Create an invoice or supplier bill, review and post it, record a partial payment, apply a linked credit and inspect the remaining balance. For stock, enable Purchasing and Inventory and follow an order through partial receipts, later billing and a stock invoice. The examples below give expected amounts.

### Is AR/AP an extra paid module?

AR/AP is included in the required accounting core with separate internal module ownership. The current project-owned code uses AGPL-3.0-or-later. Commercial licensing is separate; this release does not introduce a licence-key requirement or claim a price for future plugins.

### Can a service business ignore stock?

Yes. Purchasing and Inventory are optional. Owners can also hide unused AR/AP navigation. Hiding changes presentation, not permissions, financial balances or authorised service access.

### Does Purchasing keep a second supplier ledger?

No. Purchasing owns orders, receipts and matching; AP owns supplier debt and payment allocation. This also defines the intended boundary for later industry modules.

### Can I receive an order in parts?

Yes. Receive the quantities actually delivered and match later supplier bills to received, unbilled quantities. Price/rate differences require explicit variance review. The order itself does not post a journal.

### How is inventory cost calculated?

The first stock workflow uses moving weighted-average cost in one location per company. Source movements retain quantity/value history. Returns use the source's recorded basis. Selling price is separate from stock carrying value; no alternate costing-method claim is made.

### Does the cash POS deduct inventory?

The existing illustrative POS uses its own sample catalogue and does not deduct shared Inventory. Stock invoices use the shared Inventory issue workflow. Extending POS integration is separate work.

### Are taxes automatic for my country?

No. The core engine calculates from manually configured codes, dated rates and account mappings. It does not determine a business's registration, jurisdiction, product treatment or filing obligations. Packaged country catalogs remain disabled research references.

### Can prices include tax?

Yes. The owner can choose the default entry mode, and each document retains its reviewed exclusive/inclusive mode. The screen separates net, tax and total. A linked credit inherits its original document's tax basis rather than taking a new rate silently.

### Can I import existing debts without counting them twice?

The starter includes reviewed conversion of the existing opening unpaid-document register. Explicit party mapping and exact reconciliation link operational open items to the original opening journal. Opening stock has a separate product/quantity review. Unsupported or ambiguous bases stop for review; this is not a general-purpose historical importer.

### What does reconciliation cover?

You can compare receivables/payables to their GL control accounts, stock valuation to inventory accounts and unbilled receipts to the clearing account. Existing bank CSV reconciliation matches one statement row to one posted bank line with the same signed amount. Bank feeds and split/aggregate bank matching remain deferred.

### Can I correct a posted document?

Corrections retain document identity and append traceable reversals and replacement postings. Dependent allocations must be handled explicitly. Closed periods, completed bank reconciliations, matched purchases and stock/opening dependencies can prevent an unsafe standalone reversal. A commercial credit is a different action from correcting a source document.

### Does it support multiple currencies?

The existing foundations store transaction/base amounts and frozen manual rate snapshots, and settlement can use an actual-rate override. This is a bounded transaction/settlement capability. Outgoing foreign-currency-bank payments remain restricted; revaluation, consolidation and presentation-currency reporting are separate work.

### Where are quotes and distributor delivery routes?

Quotes are excluded and preserved for a future separate plugin. Distributor route, delivery and collection operations are also future vertical work. Their financial records should reuse shared AR/AP rather than create another receivables ledger.

### Is this certified or suitable for every production business?

No such claim is made. It is a development preview for sample experimentation and reviewed pilot preparation. Read the exact release's test evidence and limitations, arrange appropriate accounting/tax review and verify the intended installation and backup process.

### Where should I download it or report feedback?

Use the exact verified https://github.com/phpledger/phpledger/releases/tag/v0.4.0-preview from the [release listing](https://github.com/phpledger/phpledger/releases). Read the source in the [repository](https://github.com/phpledger/phpledger). Use [issues](https://github.com/phpledger/phpledger/issues) for reproducible bugs and [discussions](https://github.com/phpledger/phpledger/discussions) for workflow questions. Include sample examples only.

## 3. Guided experiment A — services and supplier costs

**Preparation:** install the exact starter preview separately. Create a fictional new business, complete setup, choose one functional currency and leave tax codes unselected for this first arithmetic example. Choose dates within an open accounting period, after any opening cutover. Create a fictional customer and supplier in Parties. Use the ordinary bank, revenue, expense and AR/AP control accounts from your reviewed chart. If the owner is prompted to activate an unused control, review that choice; do not bypass existing opening-balance conversion.

The amounts below use the same currency, domestic rate one and no tax. They are expected sample accounting results, not observed customer results.

### Customer sequence

1. Open Receivables and save an invoice for **1,000**. Review the party, date, due date, line and account. Post it explicitly.
2. Record a receipt of **400** on a later date. Outstanding should be **600**.
3. Create a customer credit linked to that invoice for **100**. Review and post it. Outstanding should become **500**.
4. Record the final receipt of **500**. Outstanding should be zero. Check that the original identity and its activity remain visible.

| After action | Customer outstanding | Cumulative bank receipts | Net sales after credits |
|---|---:|---:|---:|
| Invoice | 1,000 | 0 | 1,000 |
| Partial receipt | 600 | 400 | 1,000 |
| Customer credit | 500 | 400 | 900 |
| Final receipt | 0 | 900 | 900 |

Choose historical ageing dates before and after each payment/credit. The earlier dates should retain the corresponding outstanding amount. Check that the AR control reconciliation difference is zero at each date, and follow a document into its journal.

### Supplier sequence

5. Save, review and post a supplier bill for **300** to an expense account.
6. Pay **120**. Outstanding should be **180**.
7. Post a supplier credit of **30** linked to the bill. Outstanding should be **150**.
8. Pay **150**. Outstanding should be zero.

| After action | Supplier outstanding | Cumulative supplier payments | Net expense after credits |
|---|---:|---:|---:|
| Bill | 300 | 0 | 300 |
| Partial payment | 180 | 120 | 300 |
| Supplier credit | 150 | 120 | 270 |
| Final payment | 0 | 270 | 270 |

If this fictional company started with zero bank balance and contains only these transactions, the ending bank balance is **630**: receipts of 900 less payments of 270. Net sales are 900, net expenses 270 and the resulting profit is 630. Neither the invoice nor its credit is a bank transaction. Both AR and AP outstanding balances are zero.

For a bank-reconciliation extension, create a sample statement containing the two customer receipts and two supplier payments on their actual dates. Opening balance is zero and closing balance is 630. Match the four bank rows to their exact posted bank lines, review the baseline/outstanding items and complete only when the adjusted difference is zero. Do not create extra journals merely to make the statement match.

## 4. Guided experiment B — purchase, receive, bill and sell stock

Use a **separate fictional company** so the expected totals are easy to inspect. Complete setup with zero opening balances and no selected tax code. Enable Inventory, then Purchasing. Create appropriate stock asset, cost-of-goods-sold, sales and received-but-unbilled liability accounts; the clearing account is separate from AP. Create a stock product with base unit “each”, selling price **20** and the corresponding account mappings.

1. Create and confirm an order for **10 units at net cost 10**. There should be no stock or journal from the order itself.
2. Receive **6 units**. Stock quantity/value should be **6 / 60**; received-but-unbilled value should be **60**.
3. Receive the remaining **4 units** at the same cost. Stock becomes **10 / 100** and unbilled value **100**.
4. Preview a supplier bill matching both receipt lines for **100**, then post it. Stock remains **10 / 100**; unbilled value becomes zero and AP becomes **100**.
5. Post a stock invoice for **5 units at 20**, total **100**. AR becomes **100**. Stock becomes **5 / 50** and cost of goods sold is **50**.
6. Receive **50** from the customer and pay **40** to the supplier. AR should be **50**, AP **60** and bank **10**.

| Check after the complete example | Expected amount |
|---|---:|
| Stock quantity | 5 units |
| Inventory carrying value | 50 |
| Customer outstanding | 50 |
| Supplier outstanding | 60 |
| Bank balance | 10 |
| Sales | 100 |
| Cost of goods sold | 50 |
| Profit | 50 |
| Received-but-unbilled balance | 0 |

With no other transactions or opening balances, assets are bank 10 + AR 50 + stock 50 = **110**. Liabilities are AP 60 and retained current profit is 50. Check AR/AP control differences, Inventory-to-GL and received-but-unbilled-to-GL differences are zero. This checks a connected example; it is not a substitute for reviewing more complex returns, variances or an actual business's accounting policy.

### Tax experiment in another draft

Create a deliberately fictional ten-percent code with explicit input/output accounts and an effective date. A tax-exclusive line with net 100 should show tax 10 and total 110. A tax-inclusive entered amount of 110 should show net 100, tax 10 and total 110. Label the rate **sample**. Do not present ten percent as a rate applicable to any country or real customer transaction.

## 5. Reproducible feedback template

```text
Package version and source revision:
PHP / MySQL versions:
Workflow and selected modules:
Price-entry mode and sample tax configuration, if relevant:
Steps to reproduce:
Expected amounts or behavior:
Actual amounts or behavior:
First step where the difference appears:
Relevant sample document / journal references:
Screenshot with no credentials or real customer details:
```

Ask whether a report is an arithmetic discrepancy, an unclear next action, a permission problem, an installation issue or a feature request. Never request a production database dump in a public issue. No feedback outcome, number of testers or resolution time is assumed by this kit.

## 6. Short demo/video script

**Format:** screen recording from the exact release candidate, fictional records only. Suggested structure is six scenes; record the real duration rather than promising a completion time.

1. **Title and scope.** “This is PHP Ledger's accounting starter: core invoices and bills, with optional Purchasing and Inventory. We are using fictional data in a development preview.” Show the exact version.
2. **Invoice to balance.** Save/review/post the 1,000 invoice, record 400 and show 600 outstanding. “The payment reduces the same open item you can trace back to the journal.”
3. **Credit and final receipt.** Post the linked 100 credit, then the 500 receipt. Show zero outstanding and the preserved activity. Display historical ageing at an earlier date.
4. **Stock receipt and bill.** Switch to the separate stock company. Show the ten-unit order, the six/four receipts and the later matched bill. Explain why an order, a receipt and a bill are distinct.
5. **Valuation and tax.** Show five units at carrying value 50 after the stock sale. Briefly compare the fictional inclusive/exclusive tax drafts and their net/tax/total split.
6. **Closing invitation.** “Install the verified preview separately, follow the fictional example and tell us the steps behind any discrepancy. Quotes, advanced stock and country filing remain outside this release.” Show the exact release and guide URLs.

## 7. Screenshot and card brief

These are **capture briefs**, not claims that artwork or approved screenshots have already been produced. Use actual candidate screens after they have passed the release's browser checks. Keep any display of the version, sample label and as-of date legible.

| Asset | Evidence to capture | Suggested caption / alt text |
|---|---|---|
| 1. Invoice activity | Fictional 1,000 invoice with the 400 receipt and 600 remaining | “A partial receipt reduces the balance on the same customer invoice.” Alt: Invoice activity showing a 1,000 total, 400 paid and 600 outstanding. |
| 2. Historical ageing | Date before final settlement, document drilldown and reconciled control | “Check what was outstanding at the selected date.” Alt: Receivables ageing with an as-of date and linked fictional documents. |
| 3. Purchase matching | Ten-unit order, six/four receipt quantities and later bill preview | “Receive goods in parts; match the supplier bill to the receipts.” Alt: Supplier bill preview matching two goods receipts. |
| 4. Inventory cost | Five units, carrying value 50 and a zero GL difference | “Quantity, recorded cost and selling price are different measures.” Alt: Inventory valuation showing five units with carrying value 50. |
| 5. Tax split | Fictional inclusive 110 split into net 100 and tax 10 | “Choose inclusive or exclusive entry and review the split.” Alt: Sample tax example with net 100, tax 10 and total 110. |

For article artwork, use a real desktop capture or a clearly labelled arrangement of these captures. Social crops must preserve the balance/date labels rather than crop them into an ambiguous claim. Do not use fake customer logos, certification badges, star ratings or fabricated interface states. A social card with a number from the experiment must identify it as a sample example.

## 8. Final release/editor checklist

- Confirm the package version, source, downloadable archive, checksum, installation guidance and publication receipt.
- Reconcile launch claims with final shipped scope and validation. If a workflow is removed from the package, remove it from the copy before use.
- Retain the verified 0.4.0 hosted-starter scope: one focused playground and four unchanged multi-year teaching histories.
- Keep future catalog research, expanded sample programs and UX work out of shipped-feature lists.
- Resolve release/article/guide/contact variables; remove the unused demo variable. Verify destinations without substituting a generic “latest” link for the intended version.
- Capture and review actual sample screenshots. Prepare captions/alt text and record their candidate version.
- Use the owner's actual authorised channels and verified account identity. Copy preparation does not send posts, email or press outreach.
- Record what was published or sent, where and when. Preserve draft, submission, publication and indexing as separate states.

## Actual screenshot media

[Download the six original PNG assets and read their captions](accounting-starter-media/README.md). The [manifest](accounting-starter-media/manifest.json) records alt text, hashes, sample baselines and local capture context. These are faithful application captures, not generated product mockups or live-publication proof. The final release media ZIP also separates press, SEO, social and email copy for the marketing team.
