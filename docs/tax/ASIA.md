# Asia tax research catalog

Research checked on **15 September 2026**. These are **disabled, unreviewed research candidates**, not supported country adapters, legal advice for a particular entity, or an instruction to charge tax. They prepare classification questions for the [module roadmap](../MODULE-ROADMAP.md); no runtime consumes these files.

The catalog covers [Malaysia](../../resources/tax/malaysia.json), [Bangladesh](../../resources/tax/bangladesh.json), [Sri Lanka](../../resources/tax/sri-lanka.json), [Nepal](../../resources/tax/nepal.json) and [Singapore](../../resources/tax/singapore.json). Every country includes the same seven industry profiles. A profile's regime IDs are possibilities to investigate, not instructions to apply all listed taxes.

## How to interpret a candidate

- `rate_percent` is an exact decimal string only when the cited authority supports that broad rate. It does not establish that a customer's transaction qualifies.
- `null` means unresolved or not representable as a general percentage. In particular, an exempt supply is not represented by the same code as a taxable zero-rated supply.
- `effective_from` is populated only for the dated rule identified in the source. `effective_to: null` means no verified end date was recorded; it is not proof that the rule remains valid forever.
- `checked_on` and `research_date` record this research pass. They are not legal commencement dates or a claim that every intervening amendment was reviewed.
- Every country has `enabled: false`, `review_required: true` and `status: research_only`; every regime remains `unreviewed`. Country, currency and industry selection must not activate a tax policy.

## Country findings and gaps

| Country | Candidate coverage | Principal review gap |
|---|---|---|
| Malaysia | Manufacturer/import sales tax; prescribed-service tax; F&B; exemption/relief; income tax | Tariff/service group, registration threshold, current exemption and tax base |
| Bangladesh | Standard VAT; zero-rate; exemption; turnover tax; supplementary duty; income tax | Current enacted amendments, service/product codes and registration rules |
| Sri Lanka | Standard VAT; exports; exemption; SSCL; income tax | 2026 VAT amendment, product exemptions and activity-specific liable turnover |
| Nepal | Standard VAT; zero-rate; exemption; income tax | Current fiscal-year law, thresholds, schedules and effective dates |
| Singapore | Standard GST; exports/international services; exemption; income tax | Registration date, supply classification, input restrictions and special schemes |

### Malaysia

RMCD distinguishes sales tax at manufacture/import from service tax on prescribed services. Its overview gives the standard service-tax candidate as **8% from 1 March 2024**, with qualifying F&B at **6%**. Registration depends on the relevant activity and threshold; retail resale is not automatically a new VAT charge. [RMCD background](https://mysst.customs.gov.my/background/)

The Ministry of Finance's 2025 announcement identifies **5%/10% sales-tax categories**, but does not classify an individual SKU; the sales-tax seed therefore leaves its rate null. [2025 revision](https://www.mof.gov.my/portal/en/news/press-release/targeted-revision-of-sales-tax-rate-and-expansion-of-service-tax-scope-effective-1-july-2025)

Do not apply a VAT input-credit model to SST. Review the specific relief instrument and supporting approval instead; exemption and export relief are not interchangeable with VAT zero-rating. RMCD publishes distinct exemption schedules and applications. [Exemption guidance](https://mysst.customs.gov.my/about-exemption/)

The tax point also needs its own rule: RMCD describes sales-tax accrual accounting and service-tax payment accounting with an unpaid-invoice provision. [Accounting for SST](https://mysst.customs.gov.my/accounting-sst/) The income-tax source displays 2023–2024 company tiers; these are not extrapolated into a 2026 percentage seed. [IRBM company rates](https://www.hasil.gov.my/syarikat/kadar-cukai-syarikat/)

### Bangladesh

NBR's FAQ supports **15% standard VAT**, export zero-rating and distinct exemptions/input-credit concepts. It also retains 2015–2017 material and obsolete-looking thresholds, so its small-business figures are deliberately excluded. [NBR VAT FAQ](https://nbr.gov.bd/faq/vat-faq/eng)

The official register contains June 2026 SRO changes, including service definitions, stage exemptions and withholding rules. Those texts need classification-specific review; turnover, industry and supplementary-duty rates remain null. [VAT SRO register](https://nbr.gov.bd/regulations/sros/vat-sros/eng) The English 2012 Act is explicitly listed as a draft, so it must not displace authoritative enacted Bangla law. [VAT Acts](https://nbr.gov.bd/regulations/acts/vat-acts/eng)

Corporate/business income tax and advance/withholding income tax require their own entity/year computation and reconciliation. [Income Tax Acts](https://nbr.gov.bd/regulations/acts/income-tax-acts/eng)

### Sri Lanka

IRD publishes **18% VAT from 1 January 2024**, ordinary thresholds of **LKR 15 million per quarter / LKR 60 million over 12 months**, and separate compulsory registration for commercial importers/exporters. Its historic tourism/restaurant zero-rate ended in 2022 and must not be reused. [VAT overview](https://www.ird.gov.lk/en/type%20of%20taxes/sitepages/value%20added%20tax%20(vat).aspx)

Section 7 zero-rating, section 22 input recovery and First Schedule exemptions need transaction-level review. The 2025 consolidation includes a medicine category with therapeutic/prophylactic conditions; a pharmacy's cosmetics cannot inherit it. This is explicitly a non-statutory consolidation. The overview lists VAT Amendment Act No.14 of 2026, but that linked text could not be fetched in this pass. [IRD consolidation](https://www.ird.gov.lk/en/publications/Value%20Added%20Tax_Acts/VAT_Act_No_14%5BE%5D_2002_(Consolidation_2025).pdf)

**SSCL is a separate 2.5% levy on defined liable turnover**, with different activity bases, including distinct distributor and ordinary wholesale/retail categories. It must not become a universal checkout addition or VAT input credit. [SSCL overview](https://www.ird.gov.lk/en/type%20of%20taxes/sitepages/social%20security%20contribution%20levy%20(sscl).aspx?menuid=1207) Annual gains/profits taxation is separate. [Income tax](https://www.ird.gov.lk/en/Type%20of%20Taxes/SitePages/Income%20Tax.aspx?menuid=1201)

### Nepal

The readable IRD FAQ expressly states **13% VAT** and distinguishes taxable zero-rated supplies, related input recovery, exempt supplies and mixed-supply allocation. Its examples span historical years; thresholds and dates are therefore unseeded. [IRD FAQ](https://ird.gov.np/faq/)

The current register lists the VAT Act amended by Finance Act 2082 and announces Finance Act 2083 materials. Linked current legislation and the older English PDF could not be retrieved. Confirm enacted FY2083/84 law, registration categories, schedules and Bikram Sambat/Gregorian dates before approval. [VAT Act register](https://ird.gov.np/category/valueaddedtaxact/), [English publication page](https://ird.gov.np/content/7798/thevalueaddedtaxact19962052/)

### Singapore

IRAS supports **9% GST from 1 January 2024**, with transitional tax-point rules. [When to charge](https://www.iras.gov.sg/taxes/goods-services-tax-%28gst%29/charging-gst-%28output-tax%29/when-to-charge-goods-and-services-tax-%28gst%29), [rate transition](https://www.iras.gov.sg/taxes/goods-services-tax-%28gst%29/gst-rate-change/gst-rate-change-for-business/transitional-rules-for-gst-rate-change)

Compulsory registration considers calendar-year or expected next-12-month taxable turnover over **SGD 1 million**. The prospective registration effective-date rule changed for liabilities arising from 1 July 2025; a threshold alone does not establish when a business starts charging. [Registration](https://www.iras.gov.sg/taxes/goods-services-tax-%28gst%29/gst-registration-deregistration/do-i-need-to-register-for-gst)

Zero-rated supplies remain taxable, while exempt supplies generally restrict related recovery. [Exempt supplies](https://www.iras.gov.sg/taxes/goods-services-tax-%28gst%29/charging-gst-%28output-tax%29/when-is-gst-not-charged/supplies-exempt-from-gst) Exports require evidence. [Exporting goods](https://www.iras.gov.sg/taxes/goods-services-tax-%28gst%29/charging-gst-%28output-tax%29/when-to-charge-0-gst-%28zero-rate%29/exporting-of-goods) Sports/recreation subscription input restrictions concern the buyer's claim and do not establish the club's output-tax exemption. [Input-tax conditions](https://www.iras.gov.sg/taxes/goods-services-tax-%28gst%29/claiming-gst-%28input-tax%29/conditions-for-claiming-input-tax)

The company headline income-tax rate is 17%, but the seed leaves the entity-specific rate unresolved because legal form, assessment year and reliefs matter. [Corporate income-tax guide](https://www.iras.gov.sg/taxes/corporate-income-tax/basics-of-corporate-income-tax/basic-guide-to-corporate-income-tax-for-companies)

## Industry classification work

These are proposed research questions, not concluded tax classifications.

| Industry code | Minimum classification evidence |
|---|---|
| `restaurant` | Meals, takeaway, catering, alcohol, delivery, tips/service charges, vouchers and outlet registration |
| `membership_club` | Legal form; subscriptions/joining fees, donations, facilities, admissions, dining and member/non-member activity |
| `pharmacy` | SKU-level medicine/device/supplement/cosmetic code, clinical services and import/manufacturing status |
| `trader` | Principal/agent role, supply stage, tariff/service code and import/export evidence |
| `distributor` | Manufacturer appointment, own-account/commission activity, rebates, returns and consignment |
| `retail_shop` | Mixed SKU basket, used goods, bundles, price display, registration and vouchers |
| `workshop` | Parts/labor/consumables, bundled contracts, subcontracting, warranties and repair/manufacturing distinction |

## Accounting and activation gate

Applicable local law and the entity's adopted accounting standards govern recognition, measurement, tax controls, adjustments and disclosures. Professional guidance helps interpret that framework; it does not create a universal ACCA/IFRS tax rate or replace jurisdiction review. These files do not select a reporting framework.

Before implementing an adapter, a qualified local reviewer must approve a defined entity, fiscal period and transaction scope with controlling legal sources, versions and commencement dates. Separate output tax, recoverable input tax, irrecoverable tax/cost, levies and income-tax liabilities according to those approved rules. Income tax is never automatically added at checkout.

The later adapter must retain registration/classification evidence and immutable calculation snapshots, enforce exact money and rounding, and test taxable/exempt/zero-rated mixed baskets, purchases, imports, returns, credit notes and period changes through the central posting service. No input-credit, filing, fiscal-device or provider-submission functionality is delivered by this catalog.

Validation for this research slice: parse all five JSON files; check required keys, decimal/date types, unique IDs, source references, industry mappings and disabled/unreviewed state. No PHP/JavaScript, route, database, migration or production behavior is changed. Official public pages were read; no Drive documents or authenticated tax accounts were used. Current legal completeness and independent accounting/tax review remain open.

