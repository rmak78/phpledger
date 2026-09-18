# Original business scenarios and expected outcomes

Every pack represents **1–30 September 2026**, in configurable demonstration currency units (stored as USD). The same small receipt/expense correction appears in the six stock businesses so a reviewer can compare identical posting behavior across industries. Values are teaching data; sparse activity deliberately produces a small loss in two packs and should not be disguised as business success.

## Reconciled month-end totals

Amounts below are rendered to two decimals for reading; JSON and validation retain four decimals. Net result includes the example expenses. Opening balances never contribute to current-month income.

| Pack | Events | Income | Net result | Receivables | Payables | Stock value | Till cash | Bank |
|---|---:|---:|---:|---:|---:|---:|---:|---:|
| Restaurant | 11 | 680.00 | 299.00 | 550.00 | 500.00 | 1,264.00 | 530.00 | 2,855.00 |
| Membership club | 9 | 330.00 | -10.00 | 80.00 | 400.00 | 0.00 | 250.00 | 5,200.00 |
| Pharmacy | 11 | 370.00 | 83.00 | 340.00 | 400.00 | 1,258.00 | 430.00 | 2,855.00 |
| Trader | 11 | 900.00 | 235.00 | 720.00 | 500.00 | 1,480.00 | 580.00 | 2,855.00 |
| Distributor | 13 | 1,056.00 | 265.00 | 796.00 | 440.00 | 1,694.00 | 660.00 | 2,855.00 |
| Retail shop | 11 | 288.00 | -17.00 | 264.00 | 500.00 | 1,340.00 | 424.00 | 2,855.00 |
| Service workshop | 11 | 410.00 | 195.00 | 355.00 | 500.00 | 1,430.00 | 455.00 | 2,855.00 |

## Common stock-business journey

The restaurant, pharmacy, trader, distributor, retail shop and workshop start with cash 500, bank 3,000, receivables 300, payables 400, owned equipment 2,000 and the pack's opening stock. Their opening customer invoice was originally 500 with 200 paid before cutover; the opening supplier bill was originally 500 with 100 paid before cutover. These are explanatory details of the opening controls, never new September sales or purchases.

During September, each receives a stock purchase on credit, records a cash sale and a separate customer invoice, receives 200 allocated as 120 against the opening invoice plus 80 against the new one, and pays 400 allocated equally between the opening and new supplier bills. It pays rent 100, credits a returned item to the unpaid current invoice, and banks 200 of till cash. Finally, a utility entry of 60 is reversed with an explicit link and reposted correctly as 45. The net utility expense is 45, and the original audit trail remains visible.

An early accounting UI can reuse the receipt, expense, reversal and report examples once account mapping is implemented. The related stock, document and industry objects are future module fixtures, not evidence that those modules already work.

## Restaurant: Cedar Table

A table of ten orders ten prepared meals and ten bottled drinks, yielding a cash sale of 230. A separate catering customer invoice totals 460. Returning two bottled drinks reduces the catering invoice by 10 and restores their cost of 4. Food and beverage revenue use distinct proposed account keys.

The menu's prepared meal kit is a deliberately simplified counted stock unit; it is not a recipe engine. A future kitchen ticket links to the table sale but does not post another sale. Recipe conversion, waste, tips, split bills, delivery commissions and reservations require separate validated examples before implementation.

## Membership club: Riverside Community Club

The opening position includes 120 overdue dues, 300 unpaid hall hire and 1,100 prepaid membership income remaining from an earlier 1,200 contract. During September, two new annual memberships collect 240 into deferred income, three monthly memberships are invoiced for 60, and event tickets earn 150 cash. Receipts settle 80 of opening dues and 20 of current dues. Hall hire adds a 300 unpaid bill; a 200 payment reduces the older bill. Utilities cost 40 and 100 till cash moves to bank.

At month end, recognize 100 from the older annual contract and 20 from the new annual contracts. September income is 330; remaining prepaid income is **1,220**, not current income. The fixture models even monthly service over stated annual terms solely for demonstration; real membership agreements and recognition policy need accounting review. Member groups are synthetic aggregate contacts, not real members. Recurring billing, attendance, access permissions and renewal messages remain future workflows.

## Pharmacy: Meadow Training Pharmacy

The catalog contains an empty training carton and a storage pouch, explicitly **not medicines**. Both carry fictional lots. An opening lot expires on 15 October 2026, which is within the 30-day alert window at the fixed 30 September reference date. A later received lot expires in June 2027. Sales and returns identify their original lot; ending stock value is 1,258.

A future task may preview a hold on a nominated lot and confirm that held stock cannot be sold. The scenario does not actually quarantine stock, prescribe treatment, identify patients or certify pharmacy compliance. FEFO selection and accounting valuation must remain distinct.

## Trader: Harbour Trade

Boxed fasteners and cleaning cloth cartons are sold in carton units. The illustrative fastener price drops from 55 retail to 50 for at least four cartons; both sample sales meet that threshold. The current business invoice is 700, reduced by an 80 receipt and an 80 credit for two returned cloth cartons, leaving 540. Adding 180 still due on the opening invoice gives receivables of 720.

Future scenarios should add genuine unit conversions, price-list permissions, landed costs, quotation acceptance and credit-limit behavior only after those rules are approved. The current carton price is explicit, not an inferred market price.

## Distributor: North Loop Distribution

The warehouse loads Van 01 with 40 snack cases and 50 water cases. Two stops consume 30 of each; the credit customer returns two water cases. The van returns its remaining ten snack cases and 22 water cases to the warehouse, ending at zero. Both location transfers conserve every SKU and have no journal entry because ownership and the company/book are unchanged.

The route's cash sale is 360. Of that, 200 is banked and 160 is retained; with the opening till cash of 500, total till cash is 660. Separate bank-transfer receipts of 200 are not counted as route cash. The 720 credit invoice, less an 80 receipt and 24 return, leaves 616; together with the older 180, receivables are 796. Delivery routes, stock handoffs, driver roles, route cash control, offline sync and evening settlement UI are future requirements. This small fixture uses the existing cash role with an explicit settlement explanation; a dedicated reviewed route-cash account can be introduced in a later version.

## Retail shop: Corner Stationery

The counter sale contains 12 notebooks and eight pen sets for 124. A school/business customer invoice totals 180. The return of two pen sets creates a 16 credit against that unpaid invoice; it is not a cash refund. Ending stock holds 268 notebooks and 134 pen sets. A till count should explain the starting 500, sale 124 and bank deposit 200, leaving 424.

Use this pack to check quick item entry, a meaningful empty/error state, a return linked to its invoice and a report-to-source journey once those screens exist. Barcode handling and register sessions remain future implementation.

## Service workshop: Wheel & Spoke Workshop

A customer-owned sample bicycle is held for repair. Its embedded asset record explicitly excludes it from owned stock, fixed assets and financial postings. The sample estimate of 290 covers three cables, two chains and four labour hours. After the proposed job approval/completion flow, the corresponding invoice records parts and labour separately. Returning one chain credits 35 and restores its cost of 15; completed labour is not refunded.

The embedded estimate is nonposting. Intake, quotes, mechanic assignments, job progress and customer custody must not recognize revenue merely because their status changes. Deposits, work in progress, unclaimed property, warranty costs and actual customer approvals need their own reviewed future scenarios. This pack does not supply a working job-card module.

## Review tasks before a public sample wizard

- Accountant: approve each account mapping, opening import bridge, recognition policy and expected report result.
- Designer: make sample choice and sample status unmistakable; test a complete task with owners and accountants without coaching.
- Engineer: create and reset isolated demo companies, map stable IDs, import atomically with duplicate protection, reconcile every expected result, and block external sends.
- Industry reviewers: validate the original operational examples against restaurant, club, pharmacy, trade, distribution, retail and workshop practice. Source listings alone are insufficient evidence.

The full future product path remains in [ROADMAP.md](../ROADMAP.md). These samples preserve useful future situations without representing those features as implemented.
