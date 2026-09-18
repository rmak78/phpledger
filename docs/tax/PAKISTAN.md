# Pakistan tax research and seed candidates

Research date: **15 September 2026**. These are disabled, unreviewed research candidates for the optional tax module, not a rate calculator, installed configuration, tax advice or a compliance claim. [Seed data](../../resources/tax/pakistan.json) deliberately uses `null` for unresolved rates and dates. `checked_on` records a source check; it is never a legal commencement date. A null end date does not establish continuing validity.

The [core/module roadmap](../MODULE-ROADMAP.md) remains authoritative: complete core accounting first and retain one posting service. Optional software does not make an entity's tax obligations optional; affected operational workflows need reviewed tax capability before production acceptance. An industry profile lists possible alternatives and purchase-side obligations, not taxes to add together.

## Authority and evidence

FBR administers federal goods sales tax, income tax and ICT services tax. Punjab, Sindh, Khyber Pakhtunkhwa and Balochistan have distinct services regimes. Select the legal entity, registration, establishment and place-of-provision facts; PKR, customer address or an industry label alone cannot choose a rule. The current source register is also embedded in the JSON.

| Source | Evidence used and limitation |
|---|---|
| [FBR Sales Tax Act, consolidated 30 June 2026](https://download1.fbr.gov.pk/Docs/20267171373418951SalesTaxAct1990updatedupto30.06.2026.pdf) | Sections 3–4, 7–8B, 13–14 and schedules establish goods-tax classification, rates, recovery and registration research. Later SROs still need reconciliation. |
| [FBR Income Tax Ordinance, consolidated 30 June 2026](https://download1.fbr.gov.pk/Docs/2026724177725705IncomeTaxOrdinanace2001.pdf) | Current company-rate table, minimum-tax structure and trade advance-tax provisions were read. A complete section-by-section eligibility/rate implementation is pending. |
| [FBR withholding rate-card register](https://urdu.fbr.gov.pk/withholding-taxes-rate-card/174298/174301) | Lists Tax Year 2027, updated through Finance Act 2026. Its individual rows are not seeded; the legal section and taxpayer status must be resolved first. |
| [ICT Ordinance, consolidated 30 June 2025](https://download1.fbr.gov.pk/Docs/2025711172945966ICTOUpdatedupto30.06.2025.pdf) | Official indexed text confirms the separate ICT services schedule and registration machinery. Full current 2026 reconciliation was unavailable; rates remain null. |
| [PRA Act portal](https://reg.pra.punjab.gov.pk/ptms/SalesTaxAct2012.aspx) | Retrieved chapter and schedule links, but the available links include older material. Current enacted Punjab schedule/notifications were not established; no Punjab numeric rate is seeded. |
| [SRB consolidated-law register](https://www.srb.gos.pk/srb/acts-rules-amendments/) and [notifications](https://www.srb.gos.pk/srb/notification/) | Indexed official lists identify July 2026 consolidated texts and amended withholding/integration rules. Direct retrieval was intermittent; current complete rate schedule remains a review gap. |
| [SRB restaurant permission example](https://www.srb.gos.pk/srb/wp-content/uploads/2026/06/19-06-2026-2.pdf) | A named-entity approval confirms that payment method, authority permission and active integrated outlets matter. That permission is not transferable and is not installed as a customer entitlement. |
| [KPRA legislation portal](https://kpra.gov.pk/) and [2026 consolidated Act](https://kpra.gov.pk/wp-content/uploads/2026/09/KPSTSA-2022-Updated-Finance-act-2026.pdf) | Current edition was located; repeated content fetches timed out or returned 403. The [2025 Act](https://kpra.gov.pk/wp-content/uploads/2025/07/kp-finance-act-2025.pdf) supports researching tender, registration and invoice-integration conditions, but does not prove 2026 rates. |
| [BRA 2026 consolidated Act](https://www.ebra.com.pk/storage/uploads/downloads/1785387363.pdf) and [enacted Finance Act 2026](https://excise.balochistan.gov.pk/wp-content/uploads/2026/06/The-Balochistan-Finance-Act2026Final-Assemebly-Notified-Copy.pdf) | Section 10 was read; the current Third Schedule restaurant rows/proviso on printed pages 110–111 were also visually checked. The consolidation prints historical schedules: those must not be mistaken for current entries. |

Tax legislation determines liability and collection. The selected statutory reporting framework determines financial recognition and presentation. ICAP, ICMAP/ICMA Pakistan and ACCA provide professional guidance; none is a substitute tax authority or a universal certificate. Framework/entity selection remains governed by [Pakistan reporting research](../accounting/PAKISTAN_REPORTING_RESEARCH.md).

## Regime coverage

| Area | Candidate treatment and required classification |
|---|---|
| Federal goods | Standard candidate **18%** only within section 3's ordinary scope. First test product/PCT classification, exemption, reduced/specific or retail-price treatment, additional tax and special procedures. |
| Exports and zero rating | **0%** is a positive statutory classification under section 4/Fifth Schedule, requiring export/supply evidence and all conditions. It is not a fallback for missing data. Exemption under section 13/Sixth Schedule is a separate null-rate classification. |
| Input recovery | Establish invoice/import evidence, taxable business use, registration and restrictions before recognizing a recoverable amount. Apportion mixed activities; blocked, reduced-rate and cross-authority credits need individual rules. Zero-rated output does not itself prove a refund entitlement. |
| Retail and distribution | Confirm current Tier-1, retailer, wholesaler, manufacturer and importer definitions. Retail-price/special collection rules and digital/POS obligations require separate eligibility; an ordinary shop is not automatically an 18%-of-margin business. |
| Medicines | Classify each medicine, active ingredient, device, cosmetic and other item by its actual entry, stage and conditions. A pharmacy label never exempts its whole basket or proves input recoverability. |
| Provincial/ICT services | Keep each authority separate, with place-of-provision, imported-service/recipient responsibility, exclusions, registrations and cross-border rules. Generic service rates remain null where current complete legal text was not established. |
| Balochistan services | Section 10 general candidate **15%**, subject to schedules. Restaurant Third Schedule candidate **8%** without input credit; candidate **4% from 1 July 2026** requires BRA-linked POS and exclusively electronic invoicing, excludes the specified hotel/club-premises and franchise cases, and also denies input credit. Authority permission to use the general rate is a different reviewed branch. |
| Income/corporate tax | Candidate **29%** for the current ordinary-company category; **20%** only for the Ordinance's defined small company. These are taxable-income rates, not total effective tax or invoice charges. Sole-proprietor/AOP slabs, super tax, minimum/alternative tax, concessions and credits require separate review. |
| Turnover/minimum/final tax | Research sections 113/113C and stream-specific provisions independently of profit-based tax. The base, thresholds, exclusions, carry-forward and final/adjustable/minimum character cannot be inferred from an industry or a withheld percentage. |
| Withholding | Payment withholding, salary/rent obligations, import/export collection and sales-chain advances are separate families. Sections 236G/236H concern trade advances; services withholding may arise under a different authority. Determine payer duty, recipient status/ATL evidence, section, base, exemptions/certificates and remittance period. |

The 18%, 29%, 20%, 15% and 8% candidates have null commencement dates because this pass did not establish their complete commencement history. Only the explicitly traced Balochistan 4% amendment receives `2026-07-01`. Every candidate still needs review of subsequent legal changes before use.

## Industry discovery matrix

These questions are the original PHP Ledger research checklist; they do not assert that every listed activity is taxable.

| Industry code | Questions to answer before selecting candidates |
|---|---|
| `restaurant` | Which authority and service location? Standalone, franchise, chain, hotel/club premises or catering? Cash/card/wallet/QR and split tenders? Approved integrated POS, special permission, service charge, tips, delivery platform, bundles and refunds? |
| `membership_club` | What legal form and actual exemption/approval? Distinguish dues, entrance fees, refundable deposits, donations, dining, events, sport and guests; membership/nonprofit status alone is insufficient. |
| `pharmacy` | Product/PCT/registration identity, medicine versus device/cosmetic, manufacturer/importer/retailer stage, batch returns and expiry write-offs? Are healthcare services separately supplied? |
| `trader` | Own-account goods trade or commission/agency service? Import/export, PCT code, registration, customer status, retail-price goods and trade advance collection? |
| `distributor` | Own stock or agency, manufacturer/importer relationships, channel customer classification, free goods/rebates, returns, freight/services and section 236G/236H responsibility? |
| `retail_shop` | Current retailer/Tier-1 status, mixed basket, branch registration/integration, online sales/couriers, tender and return handling? Which acquisition taxes are recoverable? |
| `workshop` | Separate parts, consumables and labor or a legally composite supply? Repair/location rules, warranty work, customer-supplied parts, subcontracting and withholding-agent customers? |

## Recognition and posting requirements

The following are proposed accounting-module requirements, subject to the entity's adopted framework and qualified review, not enabled posting rules.

- **Collections liability:** tax collected for an authority is kept separate from revenue. A taxable invoice posts gross cash/AR against net revenue and the appropriate tax control. [ACCA's revenue explanation](https://www.accaglobal.com/gb/en/student/exam-support-resources/fundamentals-exams-study-resources/f3/technical-articles/trade-receivables-and-revenue.html) explains the third-party collection distinction.
- **Recoverable versus nonrecoverable purchases:** recognize supported recoverable tax separately; an irrecoverable purchase tax follows the related expense/asset/inventory cost policy. Do not expense capital/inventory tax indiscriminately. [IAS 2 paragraph 11](https://www.ifrs.org/content/dam/ifrs/publications/pdf-standards/english/2021/issued/part-a/ias-2-inventories.pdf) excludes recoverable taxes from inventory purchase cost. Apply the selected SME/SSE equivalent where appropriate.
- **Withholding settlement:** retain gross document value; split settlement between cash and the supported tax credit/prepayment or withholding payable. Link certificates, remittance receipts and period allocations; reassess recoverability. A deduction is not automatically a sales discount, bad debt or second expense. This is an engineering consequence of preserving the original obligation and the relevant tax-law credit/collection duties.
- **Minimum/final taxes:** [ICAP's 2024 application guidance](https://www.icap.net.pk/wp-content/uploads/2024/05/IAS-12-Application-Guidance-on-Accounting-for-Minimum-Taxes-and-Final-Taxes.pdf), sections B–D, distinguishes hybrid minimum taxes and levies from IAS 12 income taxes and requires a consistent supported policy. Its preamble **excludes entities applying AFRS for SSEs**. Do not impose this treatment on SSEs or copy its historical statutory assumptions into a 2026 calculator. Obtain the applicable framework's current treatment and deferred-tax/recoverability assessment.
- **Professional review:** use ICAP/ICMAP expertise and ACCA-informed accounting review for the selected scope. [ICMAP's official publications portal](https://www.icmap.com.pk/qab_publications.aspx) lists its SME model-statements workshop; the full workshop PDF was not successfully re-read in this pass. It is a professional reference, not a rate source or a product endorsement.

Snapshot authority, legal-source version/entry, tax point, supply and posting dates, classification, seller/buyer registration/status, tender, base, precise rate, rounding, recoverability and reviewer decision with each later transaction. Calculation and ledger posting must be atomic, scoped and idempotent through the existing core. Credit notes and linked reversals retain the original rule snapshot; a newly researched rate never rewrites posted history.

## Review and acceptance gates

1. Reconcile the complete current law, enacted finance amendments and later notifications for each supported jurisdiction. Close the Punjab, Sindh, KP and ICT source gaps; do not promote a budget bill, search snippet or named-party permission into a general rule.
2. Choose one entity/industry/transaction scope with a qualified Pakistan reviewer. Confirm registrations, legal form, fiscal year, product/service coding, place/time of supply, exemption evidence and accounting framework. AJK/Gilgit-Baltistan, special areas and regulated sectors require separate discovery.
3. Specify calculation bases, inclusive/exclusive pricing, exact rounding, mixed tenders, partial exemption/input restrictions, debit/credit notes, discounts, refunds, imports/exports, withholding settlement and tax-period adjustments. Keep registration thresholds as unresolved classification rules until their exact current law is traced.
4. Post synthetic document/return/payment/correction fixtures through the core, reconciling AR/AP, inventory cost, cash and tax controls. Test missing classifications, overlapping candidates, backdating across effective dates, permission failures and duplicate retries. Financial and tax period boundaries remain distinct.
5. Record reviewer identity, scope, date and decision before enabling anything. Filing, authority integrations, payments and credentials remain separately authorized work.

This pass adds only this document and the disabled JSON. No runtime routes, migrations, schema, production configuration or external submissions are changed. Public official web/PDF reads occurred; no Google Drive references or customer data were used. JSON structure, reference integrity, date/rate shape, all seven industry mappings and whitespace checks are local validation; tax calculations, accounting review and source-complete legal verification remain pending.
