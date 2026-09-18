# Sample retail statements: two-year acceptance fixture

**Original documentation fixture only — not installed sample data, a working report export, a real business, a tax calculation, or a compliance claim.** The values below were specified for the report redesign and checked independently for arithmetic consistency. They are not loaded into the current application or its sample company.

All amounts are **PKR '000** (thousands of Pakistani rupees), with columns for the years ended **30 June 2026** and **30 June 2025**, or those dates where the statement is a position. Thus `3,000` means PKR 3,000,000. Accounting dates remain calendar dates, not timezone-shifted timestamps. Display scale does not alter underlying exact money.

The user requires accounting guidelines to govern the system, not merely its report appearance. This fixture supplies a common set of outcomes for reviewing future recognition/classification, postings, period-end adjustment, closing/equity, reconciliation, and presentation behavior. The applicable accounting framework, entity profile, country requirements, and policy choices still need review. **Tax expenses of 126 and 98 are fixture inputs; they are not Pakistan tax rates or outputs of a tax engine.**

## Statement of profit or loss

| Line | 2026 | 2025 |
|---|---:|---:|
| Revenue | 3,000 | 2,400 |
| Cost of goods sold | (1,800) | (1,440) |
| **Gross profit** | **1,200** | **960** |
| Selling expenses | (120) | (96) |
| Administrative expenses | (420) | (350) |
| **Operating profit** | **660** | **514** |
| Finance costs | (30) | (24) |
| **Profit before tax** | **630** | **490** |
| Tax expense — supplied fixture amount | (126) | (98) |
| **Profit for the year** | **504** | **392** |

Administrative expenses include depreciation of **60** in 2026 and **50** in 2025. These depreciation charges are already included in operating profit; add them back only when constructing the indirect cash-flow reconciliation. The COGS figures are supplied accepted outcomes for this fixture. The current sample POS does not calculate COGS, and selling prices cannot be used to invent them.

## Statement of financial position

| Line | 30 June 2026 | 30 June 2025 |
|---|---:|---:|
| **Non-current assets** | | |
| Property, plant and equipment, net | 540 | 500 |
| **Total non-current assets** | **540** | **500** |
| **Current assets** | | |
| Inventory | 400 | 300 |
| Trade receivables | 350 | 250 |
| Cash and bank | 650 | 500 |
| **Total current assets** | **1,400** | **1,050** |
| **Total assets** | **1,940** | **1,550** |
| **Equity** | | |
| Capital | 800 | 800 |
| Retained earnings | 704 | 300 |
| **Total equity** | **1,504** | **1,100** |
| **Non-current liabilities** | | |
| Non-current portion of loan | 140 | 200 |
| **Total non-current liabilities** | **140** | **200** |
| **Current liabilities** | | |
| Current portion of loan | 60 | 60 |
| Trade payables | 180 | 140 |
| Tax payable | 56 | 50 |
| **Total current liabilities** | **296** | **250** |
| **Total liabilities** | **436** | **450** |
| **Total equity and liabilities** | **1,940** | **1,550** |

Empty amount cells in this document occur **only on nonnumeric section headings**. They do not mean zero, missing information, or an unsupported calculation. Every numeric line and subtotal has an explicit value. A future renderer must carry a heading/amount/quality state rather than inferring data meaning from a blank cell. Current/non-current loan classifications are supplied fixture assumptions, not a maturity policy implemented by the present schema.

## Retained-earnings movement and equity

| Line | 2026 | 2025 |
|---|---:|---:|
| Retained earnings at start of year | 300 | 8 |
| Profit for the year | 504 | 392 |
| Dividends | (100) | (100) |
| **Retained earnings at end of year** | **704** | **300** |
| Capital at end of year | 800 | 800 |
| **Total equity at end of year** | **1,504** | **1,100** |

Capital is unchanged in this fixture. There are no other equity movements or other comprehensive income included in the supplied example. Dividends are an equity distribution here, not a P&L expense. The future closing policy must explain how the year's profit reaches retained earnings without double counting it again as unclosed income/expense balances. The current “earned profit to date” summary is not an implemented retained-earnings rollforward.

## Indirect cash-flow statement

| Line | 2026 | 2025 |
|---|---:|---:|
| Profit before tax | 630 | 490 |
| Add back depreciation | 60 | 50 |
| Add back finance costs | 30 | 24 |
| **Operating result before working-capital movements** | **720** | **564** |
| Increase in inventory | (100) | (80) |
| Increase in trade receivables | (100) | (60) |
| Increase in trade payables | 40 | 40 |
| **Cash generated before interest and tax paid** | **560** | **464** |
| Interest paid | (30) | (24) |
| Tax paid | (120) | (88) |
| **Net cash from operating activities** | **410** | **352** |
| Purchase of property, plant and equipment | (100) | (150) |
| **Net cash used in investing activities** | **(100)** | **(150)** |
| Loan repayments | (60) | (40) |
| Dividends paid | (100) | (100) |
| **Net cash used in financing activities** | **(160)** | **(140)** |
| **Net increase in cash and bank** | **150** | **62** |
| Cash and bank at start of year | 500 | 438 |
| **Cash and bank at end of year** | **650** | **500** |

**Elected fixture policy:** interest and tax paid are classified as operating, asset purchases as investing, and loan repayments/dividends as financing. Finance costs are first added back to profit before tax and actual interest paid is then deducted; they are not counted twice. These classifications and their applicability require review against the selected framework/version before implementation. This is not a universal policy choice or a current system feature.

The fixture assumes the supplied tax expense flows through the tax-payable movement, interest paid equals the supplied finance cost, dividends are paid in the stated year, and there are no additional cash-flow/asset-disposal/FX movements. It does not model deferred tax or establish a tax treatment. These simplifying assumptions must remain visible; missing real-world transactions cannot be silently set to zero.

## Cross-statement and opening bridges

| Reconciliation | 2026 | 2025 |
|---|---|---|
| Net PPE | 500 opening + 100 purchases − 60 depreciation = 540 | 400 derived opening + 150 purchases − 50 depreciation = 500 |
| Inventory | 300 opening + 100 increase = 400 | 220 derived opening + 80 increase = 300 |
| Receivables | 250 opening + 100 increase = 350 | 190 derived opening + 60 increase = 250 |
| Payables | 140 opening + 40 increase = 180 | 100 derived opening + 40 increase = 140 |
| Tax payable | 50 opening + 126 expense − 120 paid = 56 | 40 derived opening + 98 expense − 88 paid = 50 |
| Total loan | 260 opening − 60 repayments = 200 (140 non-current + 60 current) | 300 derived opening − 40 repayments = 260 (200 non-current + 60 current) |
| Cash | 500 opening + 150 net increase = 650 | 438 opening + 62 net increase = 500 |

The 2025 opening amounts above are **derived bridges within this sample fixture**, not imported or verified records. They also balance as an opening position: assets `400 + 220 + 190 + 438 = 1,248`; capital/retained earnings/liabilities `800 + 8 + 300 + 100 + 40 = 1,248`. The actual opening transaction set, individual invoices, inventory layers and asset register have not been created.

## Future supporting-fixture requirements

The following are requirements for later original acceptance datasets, not working modules or invented transaction detail:

- **Receivables aging:** provide sample invoices, due dates, credit notes, receipt allocations, cutover positions and dated reversals. A selected-date aging must reconcile to receivables **350/250**, with an explicit aging basis and bucket definitions; no balancing plug or guessed invoice can fill a gap.
- **Payables aging:** supply bills, due dates, supplier credits and payment allocations that reconcile to **180/140**. Distinguish a genuine zero from an unavailable subledger or unresolved opening balance.
- **Stock valuation and COGS:** supply item quantities, opening valuation, purchases, sales/issues, returns, stock adjustments and an explicitly reviewed costing policy that reconcile inventory **400/300** and COGS **1,800/1,440**. A catalog selling price or total-only journal does not satisfy this requirement.
- **Asset register and loans:** provide the asset additions/depreciation and loan maturity/payment facts needed to reconcile PPE **540/500**, depreciation **60/50**, and current/non-current loan balances. Do not classify maturity from an account label alone.
- **Receipts, payments and cash:** create original source documents and exact journal links that reconcile operating/investing/financing movements and closing cash **650/500**. Preserve allocations, reversal links, duplicate protection, posting periods and company/book scope.
- **Tax and equity:** provide the fixture expense/payment/payable bridge and reviewed closing/dividend entries. Do not calculate real tax obligations from these fixed sample amounts or count the year's profit twice in equity.
- **Issued report and export:** carry mapping/definition versions, dates, scale, assumptions, completeness status and source references through screen, printable/exported output and later reproduction. Reconcile displayed totals without changing ledger values.

For these future datasets, unresolved or unsupported information remains visibly incomplete. It must not be represented as a zero amount, a blank line, or a working country-compliance feature. See the [reporting gap analysis](../REPORTING_GAP_ANALYSIS.md) for the proposed mapping, coverage, and issued-result model.

## Arithmetic check receipt

On 14 September 2026, a separate Python check parsed the actual UTF-8 Markdown tables and passed **64 exact-Decimal arithmetic assertions, 0 failures**. It checked both years' line subtotals, profit, asset/equity/liability totals, retained earnings, indirect cash flow, closing cash, cross-statement links, and the disclosed opening bridges. An initial check's final document read failed because Windows defaulted to a different text encoding; the completed check explicitly used UTF-8.

This validates **this document fixture's arithmetic only**. No PHP Ledger report service, posting engine, tax calculation, inventory dataset, database migration, or application test was run by the fixture check. Country/framework review, supporting transaction datasets, policy validation and implemented-system acceptance remain outstanding.
