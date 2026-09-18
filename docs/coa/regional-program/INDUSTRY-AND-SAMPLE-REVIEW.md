# Industry and sample-company accounting review

**Research proposal — unreviewed — not runtime-importable.** Prepared 16 September 2026. This local follow-on work does not alter the released 0.4 application, its five demo choices, its four historical packs, or any company. No accountant has approved these proposed industry compositions, sample postings, or country combinations.

The deliverables are this review plan and [exact accounting review cases](ACCOUNTING-REVIEW-CASES.json). The JSON is a set of accounting oracles for discussion and later acceptance tests, **not a third installable fixture format**. Installation belongs to the separately reviewed [catalog contract](CATALOG-AND-INSTALLATION-CONTRACT.md). Country applicability belongs to [country provenance](COUNTRY-PROVENANCE.md) and [its source register](SOURCES.json).

## 1. Baseline and source reconciliation

The current architecture has one posting funnel, shared AR/AP, optional Purchasing and Inventory, manual tax configuration, immutable corrections, and explicit opening conversion. Industry modules must use those services. Routes, delivery, membership schedules, jobs, recipes, batches and manufacturing operations must not create competing receivable, payable or stock ledgers.

The existing [candidate accounts](../ACCOUNT_CANDIDATES.md), [template proposal](../TEMPLATE_MODEL.md), [research overview](../README.md) and [discovery gaps](../DISCOVERY_GAPS.md) are research inputs. Parts describe an earlier application. In particular, the old template proposal's absent-template boundary and opening-document-generated GL example are **historical proposals**, not instructions to replace the current installed snapshot or to post unpaid detail twice. This plan uses the current opening model: confirm the reconciled opening TB once, then link verified unpaid and stock detail to its existing control balances.

### Two existing source families

| Source family | Observed contents | Keep | Reconcile before a new version |
|---|---|---|---|
| [Seven candidate industry files](../../../resources/sample-data/) | `schema_version=1.0.0`, `authored_candidate_not_runtime_seed`; seven September 2026 examples, 77 expected events, 28 documents and 16 items; semantic account keys, party-like contacts, expected journals, opening detail and future industry scenarios | Sample identities, explicit opening detail, original expectations, nonposting scenario boundaries and source IDs | These are not runtime-certified documents. Some purchases combine stock and AP directly; current Purchasing needs receipt/GRNI/bill stages. Candidate `service` items map to reviewed nonstock products. Contact roles map to one party with customer/vendor flags. Candidate lot/location data cannot be silently loaded into single-location Inventory. |
| [Four pinned historical packs](../../../resources/demo-packs/README.md) | Each has 74 source records, 72 journals, three drafts, 71 event objects, 25 monthly-support rows and 36 checkpoints; 2024–2025 history and open 2026 practice | Existing bytes, versions, catalog digests, original names, all historical journals and support schedules | These post generic GL history. Manual AR/AP/stock/payroll/depreciation support is not an operational subledger. Do not activate details by fabricating invoices or issue stock a second time. |
| Released Accounting starter playground | Zero opening balance; current open practice year; prepared parties, product/account mappings and sample manual tax | Thin end-to-end experimentation with released modules | It remains a distinct zero-history choice. It is not evidence that the eleven proposed companies or regional templates are shipped. |

The candidate count above is counted directly from the seven JSON `documents` arrays (four each). Do not substitute a count of opening documents or a previous narrative total. The four pack event counts differ from their source/journal totals because receipts, reversals and drafts have different posting effects.

### Identity and mapping rules

1. Keep each existing source family immutable. A future approved successor receives a new version and provenance linking its old inputs. Never change an existing catalog digest under the same version.
2. Select one neutral core, one primary industry and zero or one reviewed country package. Optional accounts are explicit additions; a second business activity does not merge an entire second industry chart.
3. Use the existing semantic account keys where meanings match. Resolve them to company/book account IDs with the existing root types and service roles. A proposed semantic key does not add a new runtime account-role enum.
4. Preserve fictional party source IDs; merge customer/vendor roles only after an explicit identity match. Never create one GL account per party. Never infer tax registration, jurisdiction or legal form from currency, name or language.
5. Replay new operational history through the shared services only. An expected journal is an assertion, not another posting command. Stock sales require an AR invoice and its inventory adapter; the current cash POS showcase does not deduct stock.
6. Choose **complete operational history in an empty company** or **a reviewed opening cutover**. Never combine a replay of old sales/purchases with opening controls containing the same balances.
7. For cutover, the source TB is the authority; unpaid documents and stock quantities/value explain already-posted controls. Original dates, due dates, prior payments, party, currency and rate basis survive. Missing detail blocks readiness; it is not manufactured from a summary balance.

## 2. Eleven proposed companies

Names below are fictional training identities, not registered-business or trademark assertions. All eleven remain proposals. Existing names are retained where practical; overlap is resolved explicitly rather than counting the same retailer/distributor twice.

| Stable proposed industry ID | Fictional company | Source relationship | Default teaching mode |
|---|---|---|---|
| `professional-services` | Cedar Studio | Historical `service-agency` | Earned services, shared AR/AP; Inventory hidden |
| `seasonal-services` | Sunrise Garden Services | Historical `seasonal-business` | Earned services with fixed seasonal volumes; Inventory hidden |
| `retail-shop` | Willow Corner Shop | Historical retailer plus candidate Corner Stationery scenarios; retain Willow name | Purchased finished goods, one location and one base unit |
| `wholesale-trader` | Harbour Trade | Candidate `trader`; retain distinction from distributor | Purchased goods and credit terms |
| `distributor` | Harbor Supply Company | Historical distributor plus candidate North Loop Distribution scenarios; retain Harbor name | Company-wide stock and shared AR; route exercise is explanatory only |
| `restaurant-cafe` | Cedar Table | Candidate `restaurant` | Bought-in ready portions/drinks; no recipe conversion |
| `membership-club` | Riverside Community Club | Candidate `membership-club` | Earned monthly dues and hall bills; no charitable status assumed |
| `pharmacy` | Meadow Training Pharmacy | Candidate `pharmacy` | **Nonmedicinal empty cartons/pouches only**; no patients or dispensing |
| `jewelry` | Lantern Finch Jewelry Studio | New fictional proposal | Standardized owned training trinkets bought for resale; no precious-metal pricing |
| `light-manufacturing` | Maple Bench Works | New fictional proposal | Bought-in finished training units; manufacturing accounting is a separate review exercise |
| `service-workshop` | Wheel & Spoke Workshop | Candidate `service-workshop` | Company-owned spare parts plus earned labour; customer bicycles remain custody items |

### Shared required accounts and controls

All profiles need the research meanings represented by `core.bank.operating`, `core.cash.on_hand` when needed, `core.receivables.trade`, `core.payables.trade`, legal-form-reviewed equity, earned income and relevant expenses. The published foundation still requires its installed `core.cash_bank` binding. The proposed bank/cash meanings must map explicitly alongside that retained binding through an approved alias/compatibility plan; they do not replace it by name. AR/AP remain available in the starter even when navigation is hidden for a simple business. Use one authoritative open-item ledger and per-control reconciliation.

Stock profiles also resolve their inventory, COGS, and receipt-clearing accounts. `wholesale.liability.received_unbilled` already appears in the research; a generic receipt-clearing semantic alias must be agreed in the installation contract before any catalog release. Normal debit balances apply to bank, AR, owned stock and expenses; AP/clearing/equity/income normally carry credits. Returns, allowances and accumulated depreciation require deliberate contra presentation. These descriptions do not prohibit valid opposite-side entries.

Tax input/output, recoverability and withholding mappings are separate from industry. The review cases use an explicitly sample rate. They do not recommend a national rate or decide eligibility.

### Profile review sheets

Each sheet lists optional accounts as proposals. Accounts whose operational workflow is absent stay hidden or review-only; their presence must not imply that the workflow ships.

#### 2.1 Professional services / agency

- **Roles:** `service.income.projects`, `service.income.recurring`, `service.cost.contractors`, `service.cost.delivery_tools`; shared AR/AP and operating bank.
- **Optional:** `service.asset.unbilled`, `service.liability.retainer`, `service.asset.client_disbursement`, equipment/depreciation and doubtful-debt allowance.
- **Assumptions:** invoices represent earned work in the thin sample. Contractors create AP. No owned resale stock. Project, client and campaign detail belongs outside the chart.
- **Credits, deposits and loss:** credit an identified invoice for accepted scope reduction; keep receipts separately allocated. Retainers/deposits require a liability and an approved recognition schedule, not automatic income. Doubtful debt needs a reviewed allowance/write-off process; generic credit notes must not disguise bad debt.
- **Teaching:** partial receipt, contractor bill/payment, source-linked fee credit, wrong-amount correction under the same identity, and optional unearned-retainer paper case.
- **Questions:** When is work earned? Is advertising spend principal revenue or client disbursement? Is an unbilled amount an unconditional receivable or another asset? What evidence authorizes a fee reduction or impairment?
- **Unsupported operations:** time sheets, milestones, automatic revenue schedules, retainers/unapplied payments, project profitability and quote-to-invoice automation. Quotes remain a separate plugin.

#### 2.2 Seasonal services

- **Roles:** reuse service income/contractor roles and core rent, utilities, payroll, equipment and depreciation; season is a reporting context, not a separate GL per month.
- **Optional:** prepaid insurance, deposits paid, customer advances and short-term funding.
- **Assumptions:** completed visits are earned services; no customer garden or tools owned by the customer enter company assets. Company tools are distinct from consumables.
- **Credits, deposits and loss:** cancelled completed-work charges need linked credit evidence; advance bookings stay a separate recognition question. Review damaged tools and prepaid contract recoverability separately from stock write-downs.
- **Teaching:** identical service workflow with the fixed monthly seasonal factors below, low-season cash pressure, prior-month settlements, December correction and equipment/prepayment paper schedules.
- **Questions:** Which costs are consumed immediately versus prepaid? Who owns installed materials? What cancellation terms create a refund obligation? Which equipment policy and useful lives apply?
- **Unsupported operations:** field scheduling, dispatch, subscriptions, payroll, asset/depreciation automation and deposit refunds.

#### 2.3 Retail shop

- **Roles:** `retail.inventory.goods`, `retail.income.goods`, `retail.cost.goods`, shared AR/AP and receipt clearing.
- **Optional:** `retail.cost.shrinkage`, `retail.cost.packaging`, `retail.clearing.till`, card settlement and `retail.liability.gift_credit`.
- **Assumptions:** purchased goods, one unit/product and one location; moving weighted average. A cash sale is an AR invoice with full allocation if it must affect stock.
- **Credits, deposits and loss:** customer credit and physical return are separately identified but atomic where supported. Return at original issue cost; damaged returns must not silently become saleable stock. Gift credit and deposits remain liabilities pending policy. A reviewed count/value adjustment explains shrinkage/write-down.
- **Teaching:** partial receipt/bill, cash and credit settlement, returned unit, stock count, residual costing and till-to-bank transfer without a second sale.
- **Questions:** Who approves damaged stock? How are card fees, refunds and chargebacks evidenced? Are goods owned or on consignment? Which inventory/tax amounts are recoverable?
- **Unsupported operations:** barcode/register sessions, integrated stock POS, loyalty, gift-card redemption, payment providers and multi-location transfers.

#### 2.4 Wholesale / trader

- **Roles:** `wholesale.inventory.goods`, `wholesale.income.goods`, `wholesale.cost.goods`, receipt clearing and shared AR/AP.
- **Optional:** `wholesale.inventory.transit`, supplier advances, inbound cost review, rebates and deposits for returnable packaging.
- **Assumptions:** one carton is one base unit; no carton/piece conversion. A purchase order is nonposting; receipt and bill are separately matched.
- **Credits, deposits and loss:** distinguish an unbilled receipt return from a billed supplier credit/physical return. Changed bill price needs explicit variance; do not overwrite receipt cost. Supplier deposits and consignment are review-only. Damaged/slow stock requires an evidenced adjustment.
- **Teaching:** two receipts against one order, partial billing, supplier credit, payment, credit sale and aging.
- **Questions:** When does ownership pass? Do rebates reduce inventory cost? How are freight, customs and nonrecoverable taxes included? What establishes return acceptance?
- **Unsupported operations:** tier pricing, units conversion, landed cost, imports/LC, consignment and automatic purchasing forecasts.

#### 2.5 Distributor

- **Roles:** wholesale stock/revenue/COGS/clearing plus shared AR/AP; optional `wholesale.cost.delivery`, `wholesale.asset.route_cash` and `wholesale.liability.returnable`.
- **Assumptions:** released stock remains company-wide at one location. A route document must refer to the shared invoice/receipt; it cannot create a second receivable. The historical van scenario stays a paper exercise.
- **Credits, deposits and loss:** route return must identify the original sale and physical condition. Container deposits are separate from goods revenue. Vehicle damage is not stock shrinkage; undelivered or missing goods require counted evidence.
- **Teaching:** sale on credit, collection reducing AR once, later banking as cash transfer, accepted return, and no-profit/no-company-value effect from a hypothetical location transfer.
- **Questions:** Is the driver holding cash as company cash or an accountable advance? Who accepts returns? Are containers sold, lent or deposit-backed? When does customer control pass?
- **Unsupported operations:** routes, van locations, delivery proof, offline collections and container tracking. A future vertical reuses core AR and Inventory.

#### 2.6 Restaurant / cafe

- **Roles:** `restaurant.income.food`, `restaurant.income.beverages`, `restaurant.cost.consumption`; purchased ready-goods stock maps to an explicitly reviewed stock role. `restaurant.inventory.ingredients` must not falsely describe manufactured portions.
- **Optional:** ingredients, `restaurant.cost.waste`, `restaurant.cost.delivery_commission`, `restaurant.asset.delivery_settlement`, `restaurant.liability.tips`.
- **Assumptions:** the thin sample buys completed training portions; it does not turn ingredients into meals. Catering uses shared AR, suppliers use AP.
- **Credits, deposits and loss:** do not restock a consumed/unsafe returned meal simply because a credit was granted; use a financial-only nonstock credit or an approved disposal scenario. Catering deposits, tips and service charges need distinct recognition review. Record spoilage through reviewed counts/value evidence.
- **Teaching:** bought-in goods cost, catering invoice/partial receipt, supplier bill, financial service credit and a separate waste paper case.
- **Questions:** Are tips held for staff? Is the platform principal or agent and are settlements gross/net? What are recipe yields and normal waste? Which portion actually remains saleable on return?
- **Unsupported operations:** KOT, tables, recipes, kitchen production, split bills, delivery platform feeds, tips distribution and batch safety controls.

#### 2.7 Membership club

- **Roles:** retain candidate `club.income.membership`, `club.income.events`, `club.liability.prepaid_dues`; shared AR/AP and hall/utilities expenses.
- **Optional:** refundable deposits, restricted funding or member equity only after legal-form review; equipment and prepayments.
- **Assumptions:** monthly dues in the runnable proposal are already earned. A club name does not imply nonprofit/charity status or authorize donation accounting. Members remain parties; membership status is a future operational concern.
- **Credits, deposits and loss:** credit unprovided services only with an approved basis; annual prepaid dues remain a separate liability/schedule. Bad dues need recoverability review, not silent deletion. Refunds/unapplied receipts are outside current AR/AP flows.
- **Teaching:** earned monthly dues, overdue member balance, hall bill/payment and the independent prepaid-dues recognition oracle.
- **Questions:** What obligations are promised and over what period? Are joining fees refundable or separately earned? Which funds are restricted? Does nonpayment cancel access, create a credit, or leave a receivable?
- **Unsupported operations:** recurring billing, access control, attendance, household grouping, renewal messages and automatic deferral recognition.

#### 2.8 Pharmacy training

- **Roles:** use `pharmacy.inventory.other` for nonmedicinal cartons/pouches, with reviewed neutral training-sales income and `pharmacy.cost.goods`. Do not carry forward the candidate's medicines labels for these items without a visible mapping correction.
- **Optional:** `pharmacy.cost.expiry`, supplier claims and medicines/dispensing accounts only in a separately reviewed future composition.
- **Assumptions:** no real medicines, patient data, prescription or dosing information. Existing candidate lot/expiry fields are scenario metadata and cannot imply implemented FEFO, batches or quarantine.
- **Credits, deposits and loss:** source-linked returns for intact training goods; supplier claims require evidence of acceptance. Loss/write-down practice uses nonmedicinal items. No regulated-goods resale decision is encoded.
- **Teaching:** purchase/bill, stock sale/credit, count variance and a paper expiry-hold scenario with zero unsupported runtime movements.
- **Questions:** Which licenses and tax classes would apply to a real business? What makes a supplier claim enforceable? What return/quarantine evidence is required? How would financial cost differ from physical lot selection?
- **Unsupported operations:** medicines, dispensing, patient/insurance claims, regulated pricing, batches, expiry, recalls and quarantine enforcement.

#### 2.9 Jewelry

- **Roles:** `jewelry.inventory.finished`, `jewelry.income.goods`, `jewelry.cost.goods`; shared AR/AP and clearing.
- **Optional:** `jewelry.inventory.metal`, `.stones`, `.work`, `jewelry.income.workmanship`, `jewelry.cost.loss` and customer advances.
- **Assumptions:** standardized owned training trinkets are purchased for resale. They do not establish suitability of weighted average for individually significant jewelry. Metal weight/purity is not currency and does not become a financial FX account.
- **Credits, deposits and loss:** return the linked owned training item at original cost. Customer metal and repair custody stay outside owned stock. Deposits and old-for-new exchanges need separate valuation/recognition approval; no market-price mark-up of inventory.
- **Teaching:** standard goods lifecycle, loss/write-down oracle, and custody/exchange questions with no fabricated posting.
- **Questions:** Which goods require specific identification? Who owns entrusted metal? What is paid for material versus workmanship? How are refining losses, exchange consideration and consignment evidenced?
- **Unsupported operations:** precious-metal pricing, purity/weight conversion, commodity ledgers, serial costing, metal loans, refining and consignment.

#### 2.10 Light manufacturing

- **Roles:** research retains `manufacturing.inventory.raw`, `.work`, `.finished`, `manufacturing.income.goods`, `manufacturing.cost.goods`, `.overhead`, `.abnormal` and receipt clearing.
- **Optional:** plant/equipment, depreciation, labour and WIP schedules subject to recognition review.
- **Assumptions:** the thin runtime proposal sells **bought-in finished training units**. An independent manual accounting case may discuss conversion; it must not claim a bill of materials, production order or manufactured stock movement exists.
- **Credits, deposits and loss:** supplier returns/credits follow shared Purchasing. Rejects and abnormal waste need separate expense/value evidence; customer advances and WIP contracts require approved policies. Do not expense all material purchases and also expense stock consumption.
- **Teaching:** purchased finished-goods cycle plus a review-only raw/WIP/finished conversion bridge and normal-versus-abnormal-cost questions.
- **Questions:** Which conversion costs qualify? How is normal capacity estimated? How are WIP completeness, scrap, subcontracting and customer-owned material evidenced? Which costs remain period expenses?
- **Unsupported operations:** BOM, routing, production, capacity allocation, WIP valuation, subcontract inventory and automatic overhead absorption.

#### 2.11 Service workshop

- **Roles:** retain `workshop.inventory.parts`, `workshop.income.parts`, `workshop.income.labour`, `workshop.cost.parts`; shared AR/AP and clearing.
- **Optional:** company tools/equipment, warranty/rework cost and customer advances, with reviewed role mappings.
- **Assumptions:** company-owned parts issue through Inventory; labour is nonstock. A customer's bicycle is **customer-owned property**, absent from company inventory, fixed assets and COGS. Custody evidence is operational, not an asset journal.
- **Credits, deposits and loss:** distinguish labour credit, returned unused part, used part disposal, and warranty rework. Deposits await an approved liability/application workflow. Count/write-down relates only to company-owned parts.
- **Teaching:** mixed parts/labour invoice, original-cost part return, supplier bill, customer-property zero-posting case and correction history.
- **Questions:** When is a repair accepted/earned? Who owns removed/replacement parts? What makes a part reusable? Is a warranty obligation material? What authorizes release of customer property?
- **Unsupported operations:** job cards, custody tracking, scheduling, warranty automation, deposits and quotes; the future quote plugin is nonposting until an authorized shared invoice is created.

## 3. Fixed two-year design, followed by practice

### Calendar and deterministic identity

- Preserve **2024-01-01 to 2024-12-31** and **2025-01-01 to 2025-12-31** as two complete historical years. Close them only after monthly, quarterly and annual controls reconcile. Preserve the existing demonstrated arrangement of one closed 2024 annual period plus twelve closed 2025 monthly periods unless the contract explicitly chooses another supported arrangement.
- **2026-01-01 to 2026-12-31 remains open.** Post the January pattern, leave February's invoice/order-or-bill/payment exercises as drafts, and show later months as empty practice periods. A viewer's clock must not move the authored dates or sample totals.
- Proposed stable event key: `company-id/version/YYYY-MM/event-code`. Sort by explicit date and sequence. No randomness, wall-clock values, live exchange rates or provider calls affect authored amounts.
- Fixed opening at 2024-01-01: bank debit `10000.0000`, capital credit `10000.0000`; no unpaid documents, stock, hidden equity plug or tax. This is a **new version's proposed opening**, not a replacement of the old packs' opening positions.
- Every profile has all 24 historical months plus January 2026; month-specific factors below are fixed and repeat by calendar month in each year. Currency is a pre-creation teaching choice; choosing another currency label does not translate these amounts or change country applicability.
- Historical patterns S/G intentionally omit tax modelling; this does not mean zero-rated, exempt or unregistered. Sample tax is a separate review case. Each new invoice/bill is due on the last calendar day of its month; the next month's day5/day6 settlements clear its carried balance.

### Service monthly pattern S

Amounts below multiply by the profile's exact decimal factor `f`. Customer and supplier carried balances are the prior month's amounts, not the current month's factor. January 2024 has no carried settlements; January 2025 and January 2026 settle the preceding December balances.

| Day | Fixed event | Exact amount before factor |
|---|---|---:|
| 03 | Earned customer invoice | `1100.0000` |
| 04 | Source-linked customer credit for agreed scope reduction | `100.0000` |
| 05 | Settle prior month's remaining customer invoice | prior `400.0000 × f` |
| 06 | Pay prior month's remaining supplier bill | prior `80.0000 × f` |
| 08 | Contractor/hall supplier bill | `220.0000` |
| 09 | Source-linked supplier credit | `20.0000` |
| 12 | Partial current customer receipt | `600.0000` |
| 14 | Partial current supplier payment | `120.0000` |
| 20 | Paid operating expense | `100.0000` |

Monthly net income is `1000 × f`, expenses `300 × f`, profit `700 × f`; month-end AR `400 × f`, AP `80 × f`. Supplier credit is an accepted reduction of the expense bill, not a stock return. December substitutes an erroneous expense `150 × f`, a dated reversal of it, then a corrected `100 × f` under the same document identity. Its net monthly totals remain unchanged. The correction's old journal and reversal remain visible.

### Stock monthly pattern G

Each profile fixes unit cost `c`, sale price `p` and overhead `h` below. All amounts are four-place decimal strings; quantity is in the one base unit. The returned customer unit is deliberately intact and saleable in this teaching pattern. Cedar Table uses an unopened purchased drink for this cycle, not a consumed meal.

| Day | Fixed event | Exact quantity/value rule |
|---|---|---|
| 02 | Nonposting purchase order | 11 units at `c` |
| 03, 04 | Two receipts | 6 units then 5 units, each at `c`; Dr stock/Cr clearing |
| 05 | Settle prior month AR | prior `2.8 × p` (omit only January 2024) |
| 06 | Pay prior month AP | prior `4 × c` (omit only January 2024) |
| 07 | Unbilled supplier return | 1 unit at original `c`; reduce stock and clearing |
| 08 | Bill the ten retained receipt units | `10 × c`; clear receipt liability into AP |
| 10 | Customer invoice | 8 units at `p`; issue cost `8 × c` |
| 11 | Customer credit and intact return | 1 unit at original sale `p` and original issue cost `c` |
| 12 | Partial customer receipt | `4.2 × p`, leaving `2.8 × p` |
| 14 | Partial supplier payment | `6 × c`, leaving `4 × c` |
| 20 | Paid operating expense | `h` |

Monthly net revenue is `7p`, COGS `7c`, overhead `h`, profit `7p − 7c − h`; quantity increases exactly three units and carrying value increases `3c`. December uses the same correction exercise on overhead: enter `h + 10`, reverse, repost `h`. Final totals stay `h`. Pattern G keeps constant receipt cost to make the 25-month schedules independently checkable; the separate exact-cost oracle deliberately uses different costs to exercise weighted averaging.

| Company | Pattern and pinned parameters | Expected ordinary monthly net result |
|---|---|---:|
| Cedar Studio | S, `f=1.0000` | `700.0000` |
| Sunrise Garden Services | S; Jan–Dec `f=[0.5000,0.5000,1.0000,1.5000,2.0000,2.0000,2.0000,1.5000,1.0000,1.0000,0.5000,0.5000]` | `700 × f` |
| Willow Corner Shop | G, `c=10.0000`, `p=20.0000`, `h=20.0000` | `50.0000` |
| Harbour Trade | G, `c=30.0000`, `p=50.0000`, `h=30.0000` | `110.0000` |
| Harbor Supply Company | G, `c=15.0000`, `p=24.0000`, `h=25.0000` | `38.0000` |
| Cedar Table | G, bought-in unopened drink, `c=2.0000`, `p=5.0000`, `h=10.0000` | `11.0000` |
| Riverside Community Club | S, `f=0.2500` | `175.0000` |
| Meadow Training Pharmacy | G, nonmedicinal carton, `c=4.0000`, `p=10.0000`, `h=15.0000` | `27.0000` |
| Lantern Finch Jewelry Studio | G, owned training trinket, `c=60.0000`, `p=100.0000`, `h=30.0000` | `250.0000` |
| Maple Bench Works | G, bought-in finished training unit, `c=12.0000`, `p=24.0000`, `h=20.0000` | `64.0000` |
| Wheel & Spoke Workshop | G, part `c=8.0000`, `p=20.0000`, `h=20.0000`; day16 earned nonstock labour invoice `100.0000`, fully settled day17 | `164.0000` |

The deliberately simple monthly patterns teach shared controls. Profile-specific deposits, manufacturing, custody, delivery and expiry questions are separate review worksheets and **must not be represented by fabricated operational documents**. Do not claim that repeating a pattern validates a real business's seasonal demand, materiality or accounting policy.

### Checkpoints and admission gate

The future authoring tool must emit exact month-end TB, period and cumulative P&L, and BS checkpoints for all 36 months, plus quarterly and annual comparisons. Every month must independently compare AR open items to AR control, AP open items to AP control, stock movement valuation to inventory GL, and unmatched receipt basis to GRNI. A bank schedule explains each cash movement. Prior-month due dates make the end-month remainder current and any intentionally unpaid older document visible in the correct aging bucket; no unexplained overdue balances are inserted.

Examples before December's net-neutral correction: Willow after 24 months has bank `10464.0000`, AR `56.0000`, stock `720.0000`, AP `40.0000`, cumulative profit `1200.0000`; assets `11240.0000` equal liabilities `40.0000` plus capital `10000.0000` plus result `1200.0000`. Cedar Studio has bank `26480.0000`, AR `400.0000`, AP `80.0000`, cumulative result `16800.0000`; assets `26880.0000` equal liabilities `80.0000` plus capital and result `26800.0000`. Closing periods does not itself transfer result to retained earnings.

**Public-demo capacity remains a design blocker.** Two years of monthly operational AR/AP/stock commands exceed the released 100-record allowance. Existing 74-source GL packs fit; these proposed operational histories do not. Before implementation, approve a bounded resource/admission policy that preserves visitor isolation, exact retries, atomic compound posting and useful practice headroom. Do not raise the cap, bypass guards, exempt arbitrary sources, grant visitors provisioning rights, or silently collapse operational history into manual GL to make this proposal fit. A private test fixture is not approval for a public sample release.

## 4. Exact accounting review cases

[ACCOUNTING-REVIEW-CASES.json](ACCOUNTING-REVIEW-CASES.json) supplies independent, sample scenarios with explicit starting balances, dated entries, expected final TB/P&L/BS and AR/AP/stock controls. A case's journals are expectations for the existing posting funnel, not instructions to insert rows. Money and quantities use decimal strings. Domestic currency/rate snapshots are base currency and rate `1`; no tax case claims jurisdictional correctness.

The 19 cases contain 39 expected journals. They cover receipt-to-bill clearing, stock sale/COGS, partial AR/AP settlement, customer and supplier credits/returns, dated same-identity correction, opening conversion without duplicate GL, varying-cost inventory and value write-down, and sample inclusive/exclusive tax. Separate **review-only** cases cover prepaid dues/deposits, customer-owned custody, and manufacturing conversion. They are accounting questions/oracles, not claims that missing workflows are implemented. Exact arithmetic is necessary but does not establish recognition policy, rights, tax eligibility, document design or accountant acceptance.

Each eventual adapter must also prove: same-key retry causes no extra posting; changed payload under the same key fails; cross-company/book access fails; posted history is immutable; period lock prevents changes; stock cannot go negative or backdate before its latest movement; direct financial reversal cannot bypass stock effects; stale reviewed snapshots fail; whole compound operations roll back on failure; original-rate/cost credits remain tied to the source. The current financial tests are useful evidence but do not replace accountant review of the new sample versions.

## 5. First review wave and acceptance

**Proposal, not approval:** review professional-services and retail-shop for **Pakistan and India** first (four country/industry combinations). Each review must state legal form, applicable reporting framework and edition, financial year, registrations/jurisdictions, selected industry assumptions, optional accounts and named practitioner. Selecting these countries does not import tax rates, declare compliance or authorize real-company installation.

Reviewers first examine neutral service and stock cases, then propose only necessary country mappings. Pakistan questions include entity/framework applicability, federal versus relevant provincial/territorial service/sales-tax controls and withholding. India questions include company/LLP/noncorporate basis, state/UT registration, input recoverability, GST component mapping and withholding. Resolve these from the country evidence register; this industry document does not answer them.

For the remaining nine industries in either country, and **all eleven combinations in UAE/UK/US or another country**, retain the profile questions above plus country applicability questions. No approval propagates from a services review to medicines, manufacturing, precious metals, clubs or another jurisdiction. One reviewer's approval of a worked example is not approval of every tax/currency/legal-form variation.

Acceptance record required before progression:

1. Contract and UX review accepts stable IDs, semantic mapping, version/provenance, sample isolation, three-year navigation, hidden optional modules and the resource policy.
2. A practitioner records decisions on ownership, earning/cutoff, returns, impairment, deposits, equity/legal form, tax recoverability and opening reconciliation. Unsupported cases remain visibly unavailable.
3. Exact case results and each authored monthly checkpoint are independently recomputed; service-level replay in an empty isolated company matches them, including AR/AP/stock/GRNI controls and failure cases.
4. Users can find a source from a report, explain partial payment and credit versus cash refund, identify closed/open years, and distinguish a paper scenario from an available workflow. Capture observed UX evidence rather than inferring success from screenshots.
5. Only then may a version progress through **Research → Preview-only → Accountant reviewed → Validated → Released**. Rejected/deprecated versions keep their provenance. New release and installation authorization remains separate from this research.

## 6. Primary accounting references and limits

- **IND-IFRS-IAS2:** IFRS Foundation, [IAS 2 overview](https://www.ifrs.org/issued-standards/list-of-standards/ias-2-inventories/), read 16 September 2026. Where that standard applies, its overview distinguishes cost from net realisable value, purchase/conversion costs, specific identification versus interchangeable-stock formulas, and expense recognition on sale or loss. These support the questions about ownership, cost method, conversion and write-downs; they do not approve this product's cost method for every entity or jewelry item.
- **IND-IFRS-15:** IFRS Foundation, [IFRS 15 overview](https://www.ifrs.org/issued-standards/list-of-standards/ifrs-15-revenue-from-contracts-with-customers/), read 16 September 2026. Where applicable, revenue follows the identified obligations and transfer of promised goods/services. This is why the proposal asks about earned services, member dues and advance receipts rather than assuming all cash is revenue. Framework applicability and detailed deposit/refund policy still need local review.

Only the public overview pages were read; no complete standard, paid publication or national legal instrument was copied. No source redistribution licence is assumed. Source pages were not archived as reproducible raw bytes, so no content hash is asserted. Industry workflows and fictional numerical examples above are original review proposals informed by repository evidence, not extracted charts or legal advice. No Google Drive references were required or accessed for this work.

## 7. Local validation of these research artifacts

An independent Python `Decimal` pass parsed the JSON, rejected binary floating-point values, recomputed all 19 case trial balances and 39 journal balances, checked P&L/BS and AR/AP/stock/GRNI totals, and recomputed the 264 historical company-month positions (eleven profiles × 24 months). The manual policy cases intentionally do not assert a runtime inventory reconciliation. All checks passed. Relative links and eleven-profile coverage were also checked.

`python tools/build-demo-packs.py --check` passed for all four unchanged original packs, each with 74 source records and 36 checkpoints. This work did not replay the new cases through application services, generate new company fixtures, run practitioner interviews or establish accountant approval. No PHP, application, schema, migration, provider configuration or live company changed.
