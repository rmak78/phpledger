# UK and UAE tax research seeds

Research date: **15 September 2026**. These are early, disabled research candidates for the optional tax module. They do not activate tax, registration, currency, return submission or payment support. Accounting core completion remains first; jurisdiction adapters follow the gates in [Module roadmap](../MODULE-ROADMAP.md). [Existing accounting research](../accounting/UK_UAE_REPORTING_RESEARCH.md) owns reporting-framework selection.

Catalogs: [United Kingdom](../../resources/tax/united-kingdom.json), [United Arab Emirates](../../resources/tax/united-arab-emirates.json). Both use `schema_version: 1`, `status: research_only`, `enabled: false` and `review_required: true`; every regime is `unreviewed`. Country and industry selection produce classification questions, never a universal rate. Sources record their check date separately from a rule's legal dates. Null rate means a non-rate decision or unresolved rule, not zero; null commencement means unverified. Stated end dates are inclusive.

## United Kingdom

| Tax family/candidate | Scope and timing |
|---|---|
| VAT standard: 20% | Most ordinary taxable supplies. The standard rate began 4 January 2011; an item's legal classification can override it. [HMRC rates](https://www.gov.uk/vat-rates). |
| VAT reduced: 5% | Only specified goods/circumstances. Eligibility for each product must be established; it is not a restaurant or pharmacy default. [HMRC goods/service classifications](https://www.gov.uk/guidance/vat-rates-on-different-goods-and-services). |
| VAT zero: 0% | Eligible food, qualifying prescriptions and evidenced exports have distinct conditions. Zero-rated supplies remain taxable; an exemption is a different classification. [Charging VAT](https://www.gov.uk/charge-reclaim-record-vat/charging-vat). |
| VAT exempt | Qualifying healthcare, eligible sports and certain membership supplies need their own legal tests and recovery treatment. No numeric rate represents exemption. [Healthcare](https://www.gov.uk/guidance/health-professionals-pharmaceutical-products-and-vat-notice-70157), [sports](https://www.gov.uk/guidance/sport-supplies-that-are-vat-exempt-notice-70145). |
| Corporation Tax: 19% / 25% | From 1 April 2023, ordinary eligible small/main profit regimes use GBP 50,000/250,000 limits before relevant adjustments. Associated companies, period length, eligibility and marginal relief matter. These are profit-tax computations. [Rates](https://www.gov.uk/corporation-tax-rates), [2023 commencement](https://www.gov.uk/government/publications/corporation-tax-charge-and-rates-from-1-april-2022-and-small-profits-rate-and-marginal-relief-from-1-april-2023). |
| Personal Income Tax / withholding | Owner/partner tax depends on taxpayer and year, including Scottish differences. Specified interest/royalty deductions have separate payer/payee and treaty rules. Rates remain unset here. [Income Tax](https://www.gov.uk/income-tax-rates), [HMRC withholding manual](https://www.gov.uk/hmrc-internal-manuals/company-taxation-manual/ctm35050). |

Registration ordinarily becomes compulsory for UK-established businesses when taxable turnover exceeds GBP 90,000 under the rolling 12-month or next-30-day tests. Non-established businesses have separate rules; voluntary registration is possible. Record the actual effective registration date. [HMRC registration](https://www.gov.uk/vat-registration/when-to-register).

Recovery requires valid evidence and qualifying use. Mixed taxable/exempt activities require partial-exemption analysis; personal/non-business and blocked amounts cannot be assumed recoverable. [Reclaiming VAT](https://www.gov.uk/charge-reclaim-record-vat/reclaim-vat-business-expenses), [partial exemption](https://www.gov.uk/guidance/vat-exemption-and-partial-exemption).

| Industry | Required classification before a reviewed rule can apply |
|---|---|
| Restaurant | Distinguish on-premises catering, hot takeaway tests, eligible cold takeaway and excluded food/drink categories. Capture bundles, tax point and service-charge facts. [Notice 709/1](https://www.gov.uk/guidance/catering-takeaway-food-and-vat-notice-7091). |
| Membership club | Separate dues/benefits, eligible sporting services, admissions, catering, goods and genuine donations; a social-club label grants no exemption. [Notice 701/5](https://www.gov.uk/guidance/clubs-and-associations-vat-responsibilities-notice-7015). |
| Pharmacy | Separate prescribed/dispensed goods, OTC products, specified reduced-rate products and medical care; capture prescriber, dispenser, patient and treatment facts. [Notice 701/57](https://www.gov.uk/guidance/health-professionals-pharmaceutical-products-and-vat-notice-70157). |
| Trader | Classify goods, principal/agent role, imports/exports and new/used status. A margin scheme needs eligibility and records, not a flat rate on sale value. [Margin-scheme eligibility](https://www.gov.uk/vat-margin-schemes/eligibility). |
| Distributor | Capture product classification, GB/Northern Ireland/EU goods route, destination and dispatch evidence, rebates and returns. [Export distinctions](https://www.gov.uk/charge-reclaim-record-vat/charging-vat). |
| Retail shop | Apply reviewed classifications per basket line; assess food consumption location, mixed bundles and special schemes. [Goods/service list](https://www.gov.uk/guidance/vat-rates-on-different-goods-and-services). |
| Workshop | Separate parts/labour, used-item sales, warranty, statutory testing and disbursements. Repairs and accessories are excluded from the margin calculation itself. [Margin-scheme eligibility](https://www.gov.uk/vat-margin-schemes/eligibility). |

**Historical rule:** HMRC's summer children's-meal relief was 5% from **25 June through 1 September 2026 inclusive**. It is expired at the research date. The catalog retains a dated historical candidate restricted to qualifying on-premises children's meals; takeaway, alcohol, packages, add-ons and prepayments need the brief's tests. [HMRC Brief 5 (2026)](https://www.gov.uk/government/publications/revenue-and-customs-brief-5-2026-temporary-reduced-rate-of-vat-for-childrens-meals-tickets-and-family-attractions/temporary-reduced-rate-of-vat-for-childrens-meals-tickets-and-family-attractions).

## United Arab Emirates

| Tax family/candidate | Scope and timing |
|---|---|
| VAT standard: 5% | Introduced 1 January 2018. Ordinary domestic taxable goods/services need a specific exception to depart from this treatment. No general positive reduced VAT rate was established for these industries. [MoF VAT](https://mof.gov.ae/en/public-finance/tax/value-added-tax-vat/). |
| VAT zero: 0% | Qualifying exports, human healthcare and specified medical goods follow different conditions. [VAT Law, Article 45](https://tax.gov.ae//Datafolder/Files/Legislation/2025/Federal%20Decree-Law%20No.%208%20of%202017%20and%20amendments%20-%20publishing%2028%2011%202025.pdf). |
| VAT exempt | Article 46 identifies specified finance, residential property, bare-land and local-passenger-transport supplies, subject to their conditions. Neither zero-rating nor exemption follows merely from industry. [VAT Law, Article 46](https://tax.gov.ae//Datafolder/Files/Legislation/2025/Federal%20Decree-Law%20No.%208%20of%202017%20and%20amendments%20-%20publishing%2028%2011%202025.pdf). |
| Ordinary Corporate Tax: 0% / 9% | 0% on taxable income through AED 375,000; 9% on the excess, for ordinary persons within scope. Applies to financial years beginning on/after 1 June 2023, not every transaction after that day. [MoF rates](https://mof.gov.ae/en/news/ministry-of-finance-issues-explanatory-guide-for-corporate-tax-purposes/), [scope/start](https://mof.gov.ae/en/public-finance/tax/corporate-tax-in-the-uae/). |
| Qualifying Free Zone Person | 0% only on Qualifying Income; relevant non-qualifying taxable income is 9% without the ordinary AED 375,000 band. Qualification, activity and breach consequences require review. [FTA General Guide, section 9.2.2](https://tax.gov.ae/Datafolder/Files/Guides/CT/CT%20General%20Guide%20-%20EN%20-%2010%2009%202023.pdf). |
| Natural-person business / withholding | Business-turnover scope is separate from wages and private investment. Specified non-resident UAE-sourced income has a 0% withholding candidate; it is not an exemption from all taxes. [FTA natural persons](https://www.tax.gov.ae/en/taxes/corporate.tax/corporate.tax.topics/basis.of.taxation.natural.person.aspx), [MoF withholding](https://mof.gov.ae/en/public-finance/tax/corporate-tax-in-the-uae/). |
| Small Business Relief / top-up tax | Relief is an election, not a rate; multinational top-up tax is a separate group assessment. These remain review markers. [FTA relief conditions](https://tax.gov.ae/en/taxes/corporate.tax/corporate.tax.topics.aspx), [MoF top-up scope](https://mof.gov.ae/en/public-finance/tax/top-up-tax/). |

VAT registration normally uses AED 375,000 taxable supplies/imports for resident persons under the previous-12-month/next-30-day tests; the voluntary threshold is AED 187,500 with relevant expense eligibility. Non-resident rules differ. The equal-sized Corporate Tax band measures **taxable income**, not VAT turnover. [FTA registration](https://www.tax.gov.ae/en/services/vat.registration.aspx).

**Mainland/free zones:** VAT Designated Zone treatment concerns specified transactions and conditions. It is not the Corporate Tax free-zone concession, and not every free zone is a VAT Designated Zone. Services follow ordinary UAE VAT place-of-supply rules; goods movement, consumption and evidence require review. [FTA Designated Zones guide](https://tax.gov.ae/-/media/Files/FTA/links/vat-designated-zone/Designated-Zones-VAT-Guide.pdf).

| Industry | Required classification before a reviewed rule can apply |
|---|---|
| Restaurant | Meals, delivery, service charges, gratuities and accommodation/packages; identify any emirate/local levy separately. |
| Membership club | What dues purchase, separate food/events/goods, and any genuine donation; confirm any claimed entity exemption under the correct tax family. |
| Pharmacy | Separate licensed human care, approved medicines/equipment and ordinary retail/cosmetic goods; retain product approval evidence. |
| Trader | Goods origin/destination, principal/agent role, importer, evidence and any special scheme or reverse charge. |
| Distributor | Goods movement and resale facts; assess corporate qualifying-distribution eligibility independently of VAT Designated Zone treatment. |
| Retail shop | Classify basket lines and bundles; medical products, excise goods, exports and tourist refunds may need additional rules. |
| Workshop | Labour, parts, scrap, imports, used goods and warranty/composite repairs; check applicable reverse-charge/margin rules. |

The UAE industry rows are **product discovery questions**, not assertions of industry exemptions. The standard-rate starting point comes from [MoF VAT](https://mof.gov.ae/en/public-finance/tax/value-added-tax-vat/). Healthcare licensing/purpose tests are in [Executive Regulation Article 41](https://tax.gov.ae//Datafolder/Files/Legislation/Executive-Regulation-of-Federal-Decree-Law-No-08-of-2017-Publish-18-09-2025.pdf). Specified medicine/equipment zero-rating requires the decision's product definitions and registration/import approval, effective 1 January 2018. [Cabinet Decision 56/2017](https://tax.gov.ae/-/media/Files/FTA/links/Legislation/VAT/04-Cabinet-Decision-No-56-of-2017-on-Medications-and-Medical-Equipment.pdf).

Recovery must follow qualifying use, evidence and payment conditions; blocked entertainment/vehicle costs, mixed use and anti-evasion requirements need review. [VAT Law Articles 54–55](https://tax.gov.ae//Datafolder/Files/Legislation/2025/Federal%20Decree-Law%20No.%208%20of%202017%20and%20amendments%20-%20publishing%2028%2011%202025.pdf), [Executive Regulation Articles 53–55](https://tax.gov.ae//Datafolder/Files/Legislation/Executive-Regulation-of-Federal-Decree-Law-No-08-of-2017-Publish-18-09-2025.pdf).

## Date traps and evidence limits

- The FTA's September 2026 consolidation includes Cabinet Decision 149/2026, generally effective **1 October 2026**, including changed medical-product wording. Selected Article 55 changes apply from the first tax year commencing after **1 October 2027**. The future marker is separate from the current candidates; publication must not activate it. [Consolidated text and effective-date footnotes](https://tax.gov.ae//Datafolder/Files/Legislation/2026/Law-No-8-of-2017-and-its-amendments--09-2026.pdf).
- MoF's **7 August 2026** announcement extends Small Business Relief to qualifying tax periods ending on/before **31 December 2029**. Older FTA guides retain 2026. The full amending Ministerial Decision 131/2026 and its legal commencement were not obtained, so commencement remains null. [Official extension](https://mof.gov.ae/en/news/ministry-of-finance-announces-extension-of-small-business-relief-for-corporate-tax-purposes-until-31-december-2029/).
- FTA VATP016 on B2B healthcare was located but the PDF could not be retrieved. Contractual-recipient interpretation remains open. The UAE legislation portal returned an access error; the FTA-hosted 2025 and 2026 consolidated PDFs were readable. Those English PDFs identify themselves as unofficial translations; Arabic/current implementing decisions require qualified review.
- Current free-zone qualifying activities, medicine regulatory succession/product lists, e-invoicing, local hospitality levies, customs/excise, reverse charge, margin schemes and filing procedures have not been fully established. No automatic classification or compliance claim follows from this research.

## Accounting and implementation boundary

Collected sales tax is a liability to the authority rather than revenue. Recoverable purchase tax belongs in a tax-control balance; non-recoverable tax follows the underlying asset/expense policy. ACCA's teaching material supports these accounting distinctions and does not set country tax rates. [ACCA sales-tax example](https://specimen.accaglobal.com/PDF/FA%20and%20FFA%20Full%20Specimen%20exam%20answers.pdf), [ACCA revenue explanation](https://www.accaglobal.com/crsh/en/student/exam-support-resources/fundamentals-exams-study-resources/f3/technical-articles/trade-receivables-and-revenue.html).

Proposed posting examples use synthetic reviewed facts: a GBP 100 taxable sale plus GBP 20 VAT creates cash/receivable 120, revenue 100 and VAT payable 20; an AED 100 sale plus AED 5 VAT creates 105, 100 and 5 respectively. Input-tax recovery, profit tax, withholding, settlement and later corrections are separate events. Exact money, calculation snapshots, company/book permissions and the existing central posting service remain mandatory. These examples are not executable tax calculations.

ICAP/ICMAP guidance informs the Pakistan-first accounting work documented [there](../accounting/PAKISTAN_REPORTING_RESEARCH.md); no UK/UAE tax authority or endorsement is attributed to those bodies. Qualified country review must approve entity scope, effective versions, tax bases, rounding, recoverability, corrections and reconciled synthetic fixtures before an adapter is enabled.

This slice changes three local research files only. Validation checks JSON parsing, required fields, unique/source/regime IDs, reciprocal industry coverage, decimal/date formats and disabled/unreviewed state. No application route or financial test is changed; no migration or database schema change occurs. No Google Drive reference was supplied/read. External activity consists of read-only official web research; no secrets, provider submissions, account writes or production changes occur.

