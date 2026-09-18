# Questions and release gates for account templates

All proposed templates remain unreviewed. No accountant or business user was contacted during this research. These are targeted questions for the next discovery sessions, not facts to fill with automated guesses.

## Shared decisions before the catalog is implemented

| Decision / evidence | Reviewer | Why it matters |
|---|---|---|
| Minimal chart for the first service and retail pilots, including three actual reporting questions | Accountant + owner | Prevent hundreds of unused setup accounts |
| Legal forms and applicable reporting frameworks for first pilots | Country accountant | Capital/drawings, disclosures, current/non-current and recognition policies vary |
| Which roles require a default, multiple instances, or party-level controls? | Technical lead + accountant | Keep code/labels independent from posting behavior |
| Existing source charts, trial balances, unpaid documents and anonymized sample exports | Pilot accountant | Prove mappings rather than infer formats |
| Exact cutover date semantics and treatment of pre-cutover credits/advances/withholding | Accountant | Prevent duplicated AR/AP, revenue or tax |
| Inventory policy and first supported valuation method | Accountant + inventory lead | Periodic versus perpetual posting cannot be mixed silently |
| Whether hierarchy is needed initially or reporting groups suffice | Product + accountant | Avoid importing upstream tree complexity without user benefit |
| Package reuse and contribution licenses | Owner + license reviewer | Odoo/ERPNext license declarations do not settle PHP Ledger's project license |

## Country-specific evidence

- **Pakistan:** identify first legal forms and size/reporting classifications using current SECP/ICAP material. Validate federal goods versus ICT/provincial services registrations, withholding receivable/payable treatment, recoverability, payroll, and required financial disclosures. Punjab and Sindh source inventories were checked; KP, Balochistan, AJK and Gilgit-Baltistan coverage and other applicable authorities need separate research. Do not infer national coverage from two provincial sources.
- **India:** confirm AS/Ind AS/non-corporate/LLP reporting applicability with the accountant. The ICAI source list includes current guidance and a relaxation announcement; no applicability threshold or effective-date rule has been encoded. Establish GSTIN/state scopes, SGST versus UTGST, reverse charge, nonrecoverable credit, tax settlement, cess if applicable, TDS/TCS, advances, exports, and invoice/reporting obligations. Multiple registrations within one company need explicit design and testing.
- **UAE:** later research must validate mainland/free-zone/entity profile, VAT eligibility and settlement, reverse charge/import treatment, corporate-tax/accounting differences, payroll/end-of-service obligations where applicable, and required records. The 2022 FTA guide is evidence about record categories, not confirmation of 2026 rules.
- **UK:** later research must validate legal form, accounting/reporting framework, VAT scheme, Northern Ireland distinctions where relevant, payroll/PAYE/NIC, corporation/owner tax treatment, and MTD obligations. No electronic filing connection or recognition by HMRC is claimed.
- **US:** later research must select real states/localities and entity forms. The IRS recordkeeping page supports business records, not state/local sales/use-tax implementation, payroll coverage, tax-return mappings, or a universally required account chart.

## Industry questions

| Business | Questions to answer with real workflows |
|---|---|
| Services/agency | Are retainers refundable? When is revenue earned? Is client-funded advertising principal or agent activity? Do unbilled work balances exist? |
| Retail | How do returns/store credit and acquirer settlement work? Which losses need separate reporting? Is stock valuation periodic or perpetual? |
| Wholesale/van distribution | When does ownership pass? How are route cash, returnables, rebates, damaged goods, and van/warehouse transfers reconciled? |
| Restaurant/cafe | How are tips/service charges treated? Does a platform pay net or gross? How are waste, staff meals, recipes, and stock counts handled? |
| Pharmacy | Are sales retail, institutional or insurance claims? How are expiry, returns, rebates and controlled stock handled? Which products/services have distinct tax treatment? |
| Jewelry | Whose gold/stones are held? How are customer metal, consignment, repair, exchanges, yield loss, metal loans, and item-specific cost tracked? |
| Light manufacturing | How are WIP, labor, overhead, subcontracting, scrap and abnormal losses valued? Which costing method and capacity policy apply? |

## Source limitations found

- Odoo Pakistan includes a description referencing imports outside the EU; India includes classifications such as a reserve/surplus label under a liability type and some payroll/advance labels under liability types. These may be contextual implementation choices or issues, but they require review before reuse. The raw source is evidence, not approval.
- `addons/l10n_us/data/template` did not exist at the inspected Odoo 19 revision. The actual US chart was located and checked in `addons/l10n_us_account/data/template`; no chart was inferred from the missing path.
- ERPNext's inspected `verified` chart directory contains India and UAE files among the selected countries. No PK/GB/UK/US filename was found there in that revision. This narrow finding is not a claim that ERPNext lacks support through other packages or defaults.
- Odoo 19 country documentation pages failed in the browsing tool. The raw pinned source, manifests, license, and general fiscal-localization documentation were accessible.
- SECP's acts index appeared in search with an updated third-schedule entry dated 29 December 2025, but direct browser retrieval failed. Full text of the current schedule and financial-reporting applicability were not verified. The older ICAP handbook search result references historical law and was not treated as current authority.
- Government/professional materials are linked and summarized; no redistribution permission was assumed. Upstream application charts were inspected but not copied into a bundled installer.

## Promotion checklist

A template can move from research into an installer release only when its provenance/license review, accountant approval, sample posting/reversal/report cases, company isolation, import reconciliation, and package-upgrade tests are complete. Record reviewer, date, scope, source revision, unresolved limitations and evidence. A business profile can be reviewed independently from its country tax adapter; a chart review cannot approve tax calculation, filing, payroll or inventory behavior that has not been implemented.
