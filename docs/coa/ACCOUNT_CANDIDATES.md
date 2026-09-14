# Original candidate accounts for review

These are **original PHP Ledger proposals**, not a copied national chart, a complete prescribed format, or installable seed data. No account numbers are mandated here. Every row needs accounting review. A normal balance describes the usual sign; a valid transaction can move the other way. `income` is the existing foundation's revenue root type.

Sources establish broad account/function and valuation concepts, not approval of every proposed row: [ERPNext account roles](https://docs.frappe.io/erpnext/chart-of-accounts) (SRC-ERP-DOC), [IFRS inventory overview](https://www.ifrs.org/issued-standards/list-of-standards/ias-2-inventories/) (SRC-IAS2), and the [country source inventory](SOURCES.md). Company/legal form and adopted accounting policy determine which optional accounts apply.

## Neutral core

Suggested minimal setup reveals a small subset first; the table is a reusable catalog, not a requirement to create every account. Separate each real bank account for reconciliation.

| Semantic key | Suggested label | Type / normal side | Intended posting role |
|---|---|---|---|
| core.cash.on_hand | Cash on hand | asset / debit | Cash receipts/payments |
| core.bank.operating | Operating bank | asset / debit | A named bank's receipts/payments and reconciliation |
| core.settlement.card | Card settlements due | asset / debit | Amounts due from payment acquirer; separate fees |
| core.receivables.trade | Customer balances | asset / debit | AR control; customer-level subledger |
| core.receivables.allowance | Allowance for doubtful balances | asset / credit | Contra receivables; reviewed impairment policy |
| core.prepayments | Prepaid operating costs | asset / debit | Costs paid before the benefit period |
| core.deposits.paid | Refundable deposits paid | asset / debit | Deposits recoverable under contract |
| core.advances.supplier | Advances to suppliers | asset / debit | Unapplied supplier advances |
| core.assets.equipment | Equipment at cost | asset / debit | Capitalized equipment; asset register later |
| core.assets.accumulated_depreciation | Accumulated depreciation | asset / credit | Contra fixed assets |
| core.payables.trade | Supplier balances | liability / credit | AP control; supplier-level subledger |
| core.accruals.operating | Unbilled operating costs | liability / credit | Incurred costs awaiting invoice |
| core.advances.customer | Customer advances | liability / credit | Money received before earning revenue |
| core.payroll.due | Payroll due | liability / credit | Net wages due; payroll engine not implied |
| core.loans.current | Borrowings due within a year | liability / credit | Current loan portion, with lender detail |
| core.loans.noncurrent | Longer-term borrowings | liability / credit | Remaining loan principal |
| core.equity.capital | Contributed capital | equity / credit | Owner/member/shareholder capital; legal-form wording |
| core.equity.drawings | Owner drawings | equity / debit | Sole proprietor/partnership withdrawals only; not company salary |
| core.equity.retained | Retained results | equity / credit | Prior closed-period results; losses can be debit |
| core.migration.clearing | Opening transfer clearing | equity / either | Restricted technical opening transfer; zero on completed migration |
| core.income.general | Business revenue | income / credit | Neutral fallback; resolve daily default to industry revenue |
| core.income.returns | Sales returns and allowances | income / debit | Contra revenue; linked credit/reversal source |
| core.expense.direct | Direct costs | expense / debit | Service/non-stock direct costs; not duplicate stock consumption |
| core.expense.payroll | Staff costs | expense / debit | Employer cost by approved policy |
| core.expense.rent | Premises rent | expense / debit | Period operating rent; lease accounting separate |
| core.expense.utilities | Utilities and connectivity | expense / debit | Operating usage |
| core.expense.professional | Professional fees | expense / debit | Accounting/legal and other professional services |
| core.expense.software | Software subscriptions | expense / debit | Period services; asset/prepayment decision separate |
| core.expense.bank | Banking and processing charges | expense / debit | Fees separated from settlement proceeds |
| core.expense.depreciation | Depreciation cost | expense / debit | Approved depreciation charge |
| core.expense.finance | Finance costs | expense / debit | Reviewed finance expense; never loan principal |

Tax accounts are optional country/registration roles, not enabled by this core. Current/non-current splits, legal-form equity naming, and policy-specific assets are reporting/setup decisions to review. Migration clearing has no ordinary normal balance and must not be offered as an everyday expense category.

## Professional services / agency

| Semantic key | Suggested label | Type / normal side | Intended role |
|---|---|---|---|
| service.income.projects | Project fees | income / credit | Earned project/service work |
| service.income.recurring | Recurring service fees | income / credit | Earned recurring service |
| service.cost.contractors | Delivery contractor costs | expense / debit | Direct outsourced work |
| service.cost.delivery_tools | Project delivery tools | expense / debit | Non-capitalized tools attributable to service delivery |
| service.asset.unbilled | Earned work not yet billed | asset / debit | Only with a reviewed revenue-recognition policy |
| service.liability.retainer | Unearned service retainers | liability / credit | Cash before the service is earned; may alias customer advances |
| service.asset.client_disbursement | Recoverable client disbursements | asset / debit | Only when contract and principal/agent treatment support it |

Avoid a GL account per project or client. Record project dimensions and customer subledgers later. Agency advertising pass-through amounts need principal-versus-agent review; do not automatically book all client-funded spend as revenue.

## Retail

| Semantic key | Suggested label | Type / normal side | Intended role |
|---|---|---|---|
| retail.inventory.goods | Merchandise inventory | asset / debit | Cost of goods owned for resale |
| retail.income.goods | Merchandise sales | income / credit | Retail goods revenue |
| retail.cost.goods | Merchandise cost sold | expense / debit | Cost released from inventory on sale |
| retail.cost.shrinkage | Stock losses and write-downs | expense / debit | Reviewed shrinkage/NRV adjustments |
| retail.liability.gift_credit | Unredeemed store credit | liability / credit | Customer value owed; policy and legal review needed |
| retail.cost.packaging | Retail packaging | expense / debit | Selling/consumable packaging unless valuation policy differs |
| retail.clearing.till | Till count differences | expense / either | Investigated till variance; reporting presentation reviewed |

Use the core bank/card/returns roles. A store/warehouse dimension should handle locations; do not create a new chart for every till. Inventory valuation and POS settlement remain separate implementation gates.

## Wholesale / van distribution

| Semantic key | Suggested label | Type / normal side | Intended role |
|---|---|---|---|
| wholesale.inventory.goods | Distribution stock | asset / debit | Owned stock; warehouse/van detail belongs in stock ledger |
| wholesale.inventory.transit | Owned goods in transit | asset / debit | Ownership established, not merely goods ordered |
| wholesale.liability.received_unbilled | Goods received awaiting bill | liability / credit | Receipt/invoice timing control |
| wholesale.income.goods | Wholesale sales | income / credit | Goods supplied to customers |
| wholesale.cost.goods | Wholesale cost sold | expense / debit | Cost of stock sold |
| wholesale.cost.delivery | Delivery and route costs | expense / debit | Outbound transport; separate capitalizable inbound costs |
| wholesale.asset.route_cash | Route collections awaiting deposit | asset / debit | Controlled cash held by route/collector until settlement |
| wholesale.liability.returnable | Customer deposits for returnables | liability / credit | Refundable containers/crates deposit liability |

Van transfers within the same company are stock movements, not sales. Route collection should reduce AR once; depositing the same money must not book another receipt against AR. Inbound freight, rebates, goods in transit, and consignment ownership need policy review.

## Restaurant / cafe

| Semantic key | Suggested label | Type / normal side | Intended role |
|---|---|---|---|
| restaurant.inventory.ingredients | Ingredient inventory | asset / debit | Owned ingredients at approved value |
| restaurant.income.food | Food sales | income / credit | Earned food revenue |
| restaurant.income.beverages | Beverage sales | income / credit | Earned beverage revenue; tax tags separate |
| restaurant.cost.consumption | Ingredients consumed | expense / debit | Cost released from ingredients inventory |
| restaurant.cost.waste | Food waste and spoilage | expense / debit | Separately reviewed inventory losses |
| restaurant.asset.delivery_settlement | Delivery platform settlements due | asset / debit | Platform receivable; gross/fee/tax components preserved |
| restaurant.cost.delivery_commission | Delivery platform fees | expense / debit | Commission separate from receipts |
| restaurant.liability.tips | Tips owed to staff | liability / credit | Only when held for staff; service-charge policy separate |

Table/kitchen operations do not belong in GL accounts. Tips, compulsory service charges, discounts, platform settlement and recipe/stock consumption require jurisdiction and operating-model review.

## Pharmacy

| Semantic key | Suggested label | Type / normal side | Intended role |
|---|---|---|---|
| pharmacy.inventory.medicines | Medicine inventory | asset / debit | Owned medicines; batches/expiry in inventory subledger |
| pharmacy.inventory.other | Other health retail inventory | asset / debit | Other resale products where separate reporting is useful |
| pharmacy.income.medicines | Medicine sales | income / credit | Product revenue; not an automatic tax-exemption claim |
| pharmacy.income.dispensing | Dispensing/service fees | income / credit | Only if lawful and applicable in the operating model |
| pharmacy.cost.goods | Pharmacy cost sold | expense / debit | Cost of medicines/products supplied |
| pharmacy.cost.expiry | Expiry and stock write-downs | expense / debit | Approved loss/NRV adjustment |
| pharmacy.asset.claims | Reimbursement claims due | asset / debit | Insurer/institution receivable if the business uses claims |

No medicine-level tax rates, prescription compliance, insurance adjudication, batch tracing, or drug controls are provided by this chart. Returns awaiting supplier acceptance must not become a receivable without evidence.

## Jewelry

| Semantic key | Suggested label | Type / normal side | Intended role |
|---|---|---|---|
| jewelry.inventory.metal | Owned precious metal inventory | asset / debit | Financial cost; grams/purity handled separately |
| jewelry.inventory.stones | Owned stones inventory | asset / debit | Cost with item identification where needed |
| jewelry.inventory.work | Jewelry in production | asset / debit | Reviewed accumulated production cost |
| jewelry.inventory.finished | Finished jewelry inventory | asset / debit | Owned finished items at cost |
| jewelry.income.goods | Jewelry sales | income / credit | Earned finished-goods revenue |
| jewelry.income.workmanship | Workmanship/service revenue | income / credit | Distinct service only when contract supports separation |
| jewelry.cost.goods | Jewelry cost sold | expense / debit | Cost of owned inventory delivered |
| jewelry.cost.loss | Abnormal metal/stone losses | expense / debit | Reviewed abnormal loss; normal yield allocation separate |

Customer-owned gold/repair items require custody records and must not automatically be recognized as the jeweler's asset. Metal loans, exchanges, refining recovery, consignment, price feeds, and weight-versus-money reconciliations require separate design. A market price change is not an automatic inventory revaluation.

## Light manufacturing

| Semantic key | Suggested label | Type / normal side | Intended role |
|---|---|---|---|
| manufacturing.inventory.raw | Raw material inventory | asset / debit | Owned production inputs |
| manufacturing.inventory.work | Production in progress | asset / debit | Reviewed cost accumulated before completion |
| manufacturing.inventory.finished | Manufactured goods inventory | asset / debit | Completed goods at approved cost |
| manufacturing.liability.received_unbilled | Materials received awaiting bill | liability / credit | Goods receipt/invoice timing |
| manufacturing.income.goods | Manufactured product sales | income / credit | Earned product revenue |
| manufacturing.cost.goods | Manufactured cost sold | expense / debit | Release finished-goods cost on sale |
| manufacturing.cost.overhead | Production overhead incurred | expense / debit | Reviewed absorption policy; avoid duplicated expense |
| manufacturing.cost.abnormal | Abnormal production losses | expense / debit | Separately reviewed abnormal waste/loss |

The applicable reporting framework must settle direct-labor capitalization, overhead allocation, normal capacity, scrap, WIP, and valuation method. Do not offer periodic purchase-expense and perpetual stock-consumption defaults together. IAS 2's overview distinguishes purchase/conversion costs, valuation, and losses; it is a concept reference, not a declaration that every company applies full IFRS. [IFRS IAS 2](https://www.ifrs.org/issued-standards/list-of-standards/ias-2-inventories/) (SRC-IAS2).

## Country account-role candidates

All country rows are conditional on verified entity/registration needs. No rates, thresholds, tax-code tables, forms, or statutory account numbers are supplied.

| Country / candidate key | Label / scope | Type / normal side | Review purpose |
|---|---|---|---|
| PK / pk.federal.sales_tax.input | Federal recoverable input tax | asset / debit | Eligible federal credit; reconcile to records |
| PK / pk.federal.sales_tax.output | Federal output tax due | liability / credit | Federal goods tax scope; ICT services needs explicit authority tagging |
| PK / pk.services.input | Recoverable services input tax, per authority | asset / debit | Eligibility and offsets reviewed for each registration |
| PK / pk.services.output | Services tax due, per authority | liability / credit | Separate Sindh/Punjab/other applicable authority controls |
| PK / pk.withholding.credit | Income tax withheld by customers | asset / debit | Recoverability/final/minimum-tax treatment must be reviewed |
| PK / pk.withholding.due | Tax withheld from payees | liability / credit | Separate tax/authority/category mappings |
| IN / in.gst.cgst.input | CGST input credit | asset / debit | Component-specific eligible credit |
| IN / in.gst.sgst.input | SGST input credit | asset / debit | State/registration-specific eligible credit |
| IN / in.gst.utgst.input | UTGST input credit | asset / debit | Applicable union-territory alternative, not automatic SGST duplicate |
| IN / in.gst.igst.input | IGST input credit | asset / debit | Distinct interstate/import component |
| IN / in.gst.cgst.output | CGST due | liability / credit | Component-specific output liability |
| IN / in.gst.sgst.output | SGST due | liability / credit | State/registration-specific liability |
| IN / in.gst.utgst.output | UTGST due | liability / credit | Applicable territory-specific liability |
| IN / in.gst.igst.output | IGST due | liability / credit | Distinct output liability |
| IN / in.withholding.credit | TDS deducted by customers | asset / debit | Supported credit and certificate reconciliation |
| IN / in.withholding.due | TDS withheld from payees | liability / credit | Applicable section/rate rules outside chart |
| IN / in.collection.due | TCS collected for remittance | liability / credit | Applicable collection obligations outside chart |
| AE / ae.vat.input | Recoverable VAT | asset / debit | Eligible input; reverse-charge/import roles separately reviewed |
| AE / ae.vat.output | VAT due | liability / credit | Output/control reconciliation |
| GB / gb.vat.input | Recoverable VAT | asset / debit | Scheme-specific eligibility |
| GB / gb.vat.output | VAT due | liability / credit | VAT control; MTD connection is separate work |
| US / us.sales_tax.due | Sales tax collected, per authority | liability / credit | State/local obligations require later research |
| US / us.use_tax.due | Use tax due, per authority | liability / credit | Applicability and corresponding cost treatment need review |

Pakistan sources support separating tax authorities; India sources support separating component ledgers and credit usage. UAE/UK/US rows are a research starting point, not finished localizations. See [Sources](SOURCES.md) for FBR/PRA/SRB, GST, FTA, HMRC, IRS, and pinned catalog evidence. Reverse charge, nonrecoverable taxes, cess where applicable, payment/refund settlement, payroll, income/corporation tax, and statutory disclosures remain explicit research gaps.

## Additional sample-pack extension mappings

The separately authored business sample packs also cover membership clubs and service workshops. These small original extensions align their scenarios with this research naming model; they are **not additional reviewed templates**. Trader/distributor samples can use wholesale roles, and retail-shop samples can use retail roles. Each pack must declare its own concrete account selection.

| Semantic key | Suggested label | Type / normal side | Intended role |
|---|---|---|---|
| club.income.membership | Earned membership fees | income / credit | Fees for the service period already delivered |
| club.income.events | Earned event income | income / credit | Delivered club events; advance tickets remain deferred until earned |
| club.liability.prepaid_dues | Membership fees received ahead | liability / credit | Unearned fees released as service is provided |
| workshop.income.labor | Workshop labor revenue | income / credit | Earned repair/service work |
| workshop.income.parts | Workshop parts revenue | income / credit | Parts sold/supplied under reviewed recognition policy |
| workshop.inventory.parts | Owned workshop parts | asset / debit | Owned resale/repair inputs, excluding customer property |
| workshop.cost.parts | Workshop parts consumed/sold | expense / debit | Cost released from owned parts inventory |

Club legal form, nonprofit status, joining fees, cancellation/refund policy, and refundable deposits need review; the word club implies no exemption. Workshops also need custody handling for customer equipment and a decision on unfinished jobs. Source references here support general roles and inventory concepts only; membership and repair policies require practitioner evidence.
