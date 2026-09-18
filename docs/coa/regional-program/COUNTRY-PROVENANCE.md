# Country provenance and review boundaries

**Status: Research. Review: unreviewed. `runtime_importable: false`.**

This bounded pass covers **Pakistan (PK), India (IN), United Arab Emirates (AE), United Kingdom (GB) and United States (US)** in the same evidence format. Pakistan and India received the first and deepest pass. Recorded **16 September 2026, Asia/Karachi**; public-source access occurred **15 September 2026 UTC**. The interrupted release checkpoint has been resumed. These local research documents are separate from the published accounting starter.

The pack contains **38 source records, 21 original candidate account groups and six observed source-byte hashes**. Counts include historical discovery pointers and two upstream comparisons, not 38 fully reviewed legal instruments. No country package, accountant approval, statutory chart or compliance conclusion is established.

## Evidence and product boundaries

The existing [research index](../../../resources/coa/research-index.json), [COA overview](../README.md), [source register](../SOURCES.md), [template model](../TEMPLATE_MODEL.md), [candidate accounts](../ACCOUNT_CANDIDATES.md) and [discovery gaps](../DISCOVERY_GAPS.md) were inspected first. Existing runtime packages were not rewritten.

[SOURCES.json](SOURCES.json) records stable source IDs, exact URLs, publishers, editions, access dates, evidence locations, access failures, rights limits, reviewers and the five profiles. Search excerpts, retrieved text, downloaded bytes and historical repository pointers are different evidence levels. Access date is not publication or amendment date.

- **Neutral structure:** shared assets, liabilities, equity, income and expenses; shared AR/AP and posting controls. Account numbers and labels do not determine accounting behavior.
- **Country suggestions:** original, conditional group proposals. Account types are proposals; semantic roles, normal balances and report mappings remain unapproved. Reporting formats are not universal charts of accounts.
- **Tax engine:** no rates, withholding logic, recoverability decisions, eligibility tests or payroll calculations are installed by this research. Existing manually configured tax features do not become country compliance features through labels.
- **Filing:** statutory accounts, returns, e-filing, payroll submissions and regulator integrations remain outside this output.
- **Composition:** zero or one country package, with explicit legal form, jurisdiction, reporting period and framework. Multiple registrations within a country still need reviewed treatment. Countries must not combine accidentally.
- **Lifecycle:** Research, Preview-only, Accountant reviewed, Validated, Released, Deprecated. All records remain Research; reviewers are `null`. The [catalogue contract](CATALOG-AND-INSTALLATION-CONTRACT.md) governs later promotion and installation.

Five PDFs and one HTML response were downloaded into memory and hashed; raw publications are not bundled. Hashes identify observed bytes, not legal completeness or redistribution rights. Every unhashed source has an explicit `null` reason. Dynamic HTML and mutable URLs may return different bytes later.

## Pakistan - PK

### Evidence, legal form and framework

ICAP distinguishes company-class frameworks and presentation requirements and describes its circular as facilitative guidance. The dated SECP schedule must be reconciled with the intended entity and period. Company guidance cannot automatically govern proprietors, partnerships or special entities.

**Legal forms to distinguish:** Sole proprietor; Partnership or other non-company entity; Company with statutory classification; NPO/section 42 or 45 company; Regulated or state-owned entity.

**Framework boundaries:**
- Company-class-specific notified IFRS, IFRS for SMEs or small-entity framework: eligibility unapproved.
- Fourth/Fifth Schedule and special-entity presentation: mapping unapproved.
- No automatic company-framework choice for non-company businesses.

**Chart/example availability:** Presentation/recordkeeping examples and original group proposals; no complete statutory chart established. No copied account list or statutory account-number requirement was established.

### Primary evidence

The table selects the main sources; the JSON register retains additional supporting and historical pointers. Access limitations are part of the evidence.

| Source | Edition or date | Access and limitation |
|---|---|---|
| `REG-PK-ICAP-COMPLIANCE` - [Statement of Compliance for the Financial Statements prepared under the Companies Act, 2017 (Revised 2025)](https://www.icap.net.pk/wp-content/uploads/2025/11/Statement-of-Compliance-for-the-Financial-Statements-prepared-under-the-Companies-Act-2017-Revised-2025.pdf) | Circular No. 4/2025; supersedes Circular No. 4/2018 | pdf text retrieved and raw bytes hashed. Facilitative professional guidance, not an authoritative legal pronouncement. Reconcile the 2025 circular to later legislation; company rules cannot be generalized to all business legal forms. |
| `REG-PK-SECP-3RD` - [Updated 3rd Schedule to the Companies Act 2017 - 29-12-2025](https://www.secp.gov.pk/document/updated-3rd-schedule-to-the-companies-act-2017-29-12-2025/) | Schedule updated 29 December 2025; landing page created/updated 1 January 2026 | primary search index retrieved latest direct access failed. The earlier checkpoint located a four-page PDF; this pass could not retrieve it. Classification thresholds, applicable notifications and the complete current schedule remain unreviewed. |
| `REG-PK-FBR-ITO-2026` - [Income Tax Ordinance, 2001 (amended up to 20 February 2026)](https://download1.fbr.gov.pk/Docs/2026226162211364IncomeTaxOrdinance2001-Amended-20.02.2026.pdf) | Amended up to 20 February 2026 | pdf text retrieved and raw bytes hashed. 821-page edition selectively inspected, not a legal consolidation audit. Predates later 2026 finance-law activity; rates, exemptions, final/minimum/adjustable treatment and creditability are not approved. |
| `REG-PK-FBR-SALES` - [Sales Tax Basics](https://www.fbr.gov.pk/sales-tax/51148/101149) | Undated guidance; no edition established | primary page text retrieved and raw html hashed. Undated explanatory page. No current rates, exemption/recovery eligibility, provincial treatment or consolidated law established. HTML bytes can vary between requests. |
| `REG-PK-SRB` - [Acts, Rules & Amendments](https://www.srb.gos.pk/srb/acts-rules-amendments/) | Index labels principal services-tax and withholding materials amended up to 1 July 2026 | search index only direct open failed. Index labels are not confirmation that every underlying consolidated instrument is complete or applicable. No underlying 2026 instrument read. |
| `REG-PK-EOBI` - [Employees' Old-Age Benefits Act, 1976](https://pakistancode.gov.pk/pdffiles/administratorf3be1ec8d21dfebb9c5d1733f43cffe6.pdf) | Act XIV of 1976; consolidation cutoff not established | pdf retrieved and raw bytes hashed limited text review. No contribution allocation, coverage, pension eligibility or current formula established. A later text request timed out. The official index Under Review label and unknown consolidation cutoff prevent a current-law claim. |

### Original candidate groups

These are accounting-design inferences for review, not rules asserted by the cited authorities. Source IDs resolve to exact URLs in the register.

| Candidate | Proposed group and condition | Evidence |
|---|---|---|
| `PK-TAX-SUFFERED` | **Business tax suffered and advance tax.** Only balances eligible for credit/recovery under reviewed provisions; final/minimum/adjustable treatment unresolved. | `REG-PK-FBR-ITO-2026`, `REG-PK-FBR-WHT` |
| `PK-WITHHOLDING` | **Salary and supplier withholding for remittance.** Separate tax withheld for others from tax suffered by the business; provisions and payment types reviewed. | `REG-PK-FBR-ITO-2026` |
| `PK-SALES-TAX` | **Federal and jurisdiction-specific service-tax controls.** Separate reviewed registrations; recoverability and presentation unapproved. No coverage inferred for unresearched provinces. | `REG-PK-FBR-SALES`, `REG-PK-PRA`, `REG-PK-SRB` |
| `PK-PAYROLL-BENEFITS` | **Employee-benefit and statutory payroll clearing.** Old-age benefit, welfare and participation categories are conditional research; coverage/recognition require current instruments. | `REG-PK-EOBI`, `REG-PK-SRB` |
| `PK-EQUITY` | **Legal-form-specific capital, reserves and accumulated results.** Company guidance does not determine proprietor/partner capital, special-entity reserves or distributions. | `REG-PK-ICAP-COMPLIANCE`, `REG-PK-SECP-3RD` |

### Tax, payroll, rights and next review

Final/minimum/adjustable tax treatment is not resolved by naming an asset. Employee deductions, employer costs and remittances require separate recognition review. The July 2025 ordinance pointer is historical only; the downloaded February 2026 consolidation does not establish complete September 2026 law.

No licence to reproduce ICAP/SECP/FBR material or vendor charts is assumed. A Pakistan accountant must approve classification, current instruments and transaction mappings; legal/labour specialists resolve applicability beyond accounting review.

**Open evidence gaps:**

- Read dated SECP schedule and later notifications.
- February 2026 income-tax consolidation predates later finance-law activity; obtain applicable amendments.
- EOBI current consolidation/coverage and provincial payroll/social-security instruments open.
- Punjab/Sindh access limited; KP, Balochistan and other relevant territorial regimes not researched.

## India - IN

### Evidence, legal form and framework

ICAI offers distinct non-corporate and LLP guidance and illustrative formats. The March 2026 announcement changes the applicability context by reporting period. Corporate Schedule III examples distinguish Divisions I, II and III; an NBFC example cannot become the ordinary small-business default.

**Legal forms to distinguish:** Company under AS or Ind AS as applicable; NBFC under a distinct applicable regime; LLP; Other non-corporate entity.

**Framework boundaries:**
- Schedule III Divisions I/II/III distinct; corporate eligibility unapproved.
- ICAI non-corporate/LLP guidance and 31 March 2026 phased applicability.
- Income-tax Act/year context is independent of financial-reporting framework.

**Chart/example availability:** Presentation/recordkeeping examples and original group proposals; no complete statutory chart established. No copied account list or statutory account-number requirement was established.

### Primary evidence

The table selects the main sources; the JSON register retains additional supporting and historical pointers. Access limitations are part of the evidence.

| Source | Edition or date | Access and limitation |
|---|---|---|
| `REG-IN-ICAI-INDEX` - [Guidance Notes](https://www.icai.org/post/guidance-notes) | Undated guidance; no edition established | page retrieved. Publication availability is not adoption of their formats, current effective-date confirmation, or a mandatory national chart of accounts. |
| `REG-IN-ICAI-APPLICABILITY-2026` - [Announcement regarding applicability of non-corporate and LLP financial-statement guidance for annual reporting periods 2025-26 onwards](https://www.icai.org/post/23950) | 451st Council meeting held 30-31 March 2026 | page retrieved. Not a complete financial-statement standard or filing rule. Do not encode criteria without reviewing entity scope, later amendments and legal applicability. |
| `REG-IN-ICAI-CORPORATE` - [Publications: Corporate Laws and Corporate Governance Committee](https://www.icai.org/post/icai-publications-corporate-laws-corporate-governance-committee) | Index includes Schedule III guidance revised January 2022; later amendments not closed | primary search index retrieved. Underlying guidance and current MCA rules not fully read. Division selection, eligibility, formats and filing remain accountant/legal review tasks. |
| `REG-IN-GST-LEDGER` - [Electronic Credit Ledger](https://tutorial.gst.gov.in/userguide/ledgers/Electronic_Credit_Ledger.htm) | Undated guidance; no edition established | page retrieved. Portal guide is not a chart or complete law. Registration, credit eligibility, blocked amounts, set-off sequence, cash-deposit reconciliation and state/UT scope require review; no utilisation rules implemented. |
| `REG-IN-ITD-TRANSITION-2026` - [Latest News: Income-tax Act, 2025 transition and forms](https://www.incometax.gov.in/iec/foportal/latest-news?link=2&mobile-app=1&page=%2C4) | 1 April 2026 entries referring to notification 22/2026 dated 20 March 2026 | page retrieved. Paginated news is a version boundary, not complete Act or Rules. Review operative legislation/forms for the period; do not rename old TDS/TCS forms mechanically. |
| `REG-IN-EPF-ACT` - [Employees' Provident Funds and Miscellaneous Provisions Act, 1952](https://www.indiacode.nic.in/handle/123456789/2152?col=123456789%2F1362) | Act 19 of 1952; consolidated amendment cutoff not established | primary search index retrieved. No rates, coverage, labour-code transition or current obligation determined from the index. Review operative schemes/notifications; ESI and professional tax outside this pass. |

### Original candidate groups

These are accounting-design inferences for review, not rules asserted by the cited authorities. Source IDs resolve to exact URLs in the register.

| Candidate | Proposed group and condition | Evidence |
|---|---|---|
| `IN-GST` | **GST component credit, output liability and cash-deposit controls.** CGST, SGST/UTGST, IGST and cess only for reviewed registrations/treatment; no automatic set-off or credit eligibility. | `REG-IN-GST-LEDGER` |
| `IN-TDS` | **Tax suffered/advances and tax deducted or collected for remittance.** Separate salary/non-salary and business/remitter roles; choose applicable Act/year before mapping forms. | `REG-IN-ITD-TDS`, `REG-IN-ITD-TRANSITION-2026` |
| `IN-PAYROLL` | **Provident-fund, pension and insurance clearing.** Employee deductions and employer expense distinct; operative schemes, ESI and professional tax require review. | `REG-IN-EPFO`, `REG-IN-EPF-ACT` |
| `IN-EQUITY` | **Owner/partner contributions or company capital and reserves.** Match entity form and presentation basis; no NBFC/company default for ordinary non-corporate entities. | `REG-IN-ICAI-INDEX`, `REG-IN-ICAI-CORPORATE`, `REG-IN-ICAI-APPLICABILITY-2026` |

### Tax, payroll, rights and next review

The department distinguishes tax year 2026-2027 onward under the 2025 Act from AY 2026-2027 and earlier under the 1961 Act. Old form references cannot be renamed mechanically. GST registration/recovery and payroll coverage need separate decisions.

ICAI formats, spreadsheets and standards are references with no adaptation licence assumed. An Indian accountant must approve legal form, period, framework, component mappings and reconciliation. Applicable legal and rights decisions remain separate gates.

**Open evidence gaps:**

- Illustrative ICAI formats could not be freshly downloaded; no copied chart approved.
- Review current MCA rules and subsequent ICAI announcements for the period.
- Tax year 2026-2027 under Act 2025 differs from AY 2026-2027 and earlier under Act 1961.
- State/UT registration, GST recovery, labour-law effects, ESI and professional tax unresolved.

## United Arab Emirates - AE

### Evidence, legal form and framework

CTGACS1 distinguishes accounting standards from corporate-tax adjustments and describes conditional alternatives. Mainland/free-zone jurisdiction, legal form and tax status need explicit review. A tax guide does not determine every entity's financial-reporting obligations.

**Legal forms to distinguish:** Natural-person business where applicable; Mainland company with identified legal form; Free-zone entity with identified jurisdiction; Other regulated or exempt entity.

**Framework boundaries:**
- FTA CTGACS1 distinguishes IFRS and conditional alternatives for corporate-tax purposes.
- Commercial Companies Law applicability and free-zone exceptions unresolved.
- No automatic cash-basis, tax-group or consolidation feature.

**Chart/example availability:** Presentation/recordkeeping examples and original group proposals; no complete statutory chart established. No copied account list or statutory account-number requirement was established.

### Primary evidence

The table selects the main sources; the JSON register retains additional supporting and historical pointers. Access limitations are part of the evidence.

| Source | Edition or date | Access and limitation |
|---|---|---|
| `REG-AE-FTA-ACCOUNTING` - [Accounting Standards and Interaction with Corporate Tax - CTGACS1](https://tax.gov.ae/en/content/accounting.standards.and.interaction.with.corporate.tax.ctgacs1.aspx) | CTGACS1, November 2023; landing page updated 16 November 2023 | landing page and pdf text retrieved raw bytes hashed. Selective review of a 2023 guide. No automatic framework eligibility, current threshold, free-zone status, group treatment, cash-basis feature or tax adjustment approved. |
| `REG-AE-FTA-VAT` - [Taxable Person Guide for Value Added Tax (VATG001)](https://tax.gov.ae/DownloadOpenTextFile?fileUrl=en%2FVAT_VAT_Guides%2FTaxable_Person_Guide_Value_Added_Tax%2FTaxable_Person_Guide_June_2018_EN.pdf) | Issue 2, June 2018 | pdf text retrieved. Historical guide, non-binding and subject to change. No current rate, taxability, recovery policy, threshold, return mapping or legal completeness claimed. |
| `REG-AE-COMPANIES-2021` - [Federal Decree-Law No. 32 of 2021 on Commercial Companies](https://www.uaelegislation.gov.ae/en/legislations/1542/download) | Federal Decree-Law 32 of 2021; English download consolidation cutoff not established | primary search index retrieved direct download forbidden. Full download returned 403. Mainland/free-zone exclusions, entity coverage, translation status and later amendments unresolved. No reserve percentage or universal equity requirement adopted. |
| `REG-AE-GOV-EOS` - [End of service benefits for workers in the private sector](https://u.ae/en/information-and-services/jobs/employment-in-the-private-sector/end-of-service-benefits-for-employees-in-the-private-sector) | Earlier indexed page update 6 October 2025; not reverified from full body | primary search index retrieved latest page body unavailable. Latest full-body access returned empty content or 404. Search evidence distinguishes private-sector worker arrangements and alternative savings; no recognition formula, coverage, nationality, pension or free-zone conclusion established. |

### Original candidate groups

These are accounting-design inferences for review, not rules asserted by the cited authorities. Source IDs resolve to exact URLs in the register.

| Candidate | Proposed group and condition | Evidence |
|---|---|---|
| `AE-VAT` | **VAT recoverable, output and settlement controls.** Recovery, apportionment and registration require current review; historical guide supports category research only. | `REG-AE-FTA-VAT` |
| `AE-CORPORATE-TAX` | **Current business income-tax expense, payable and advances.** Accounting profit and tax adjustments distinct; legal/tax status and period reviewed. | `REG-AE-FTA-ACCOUNTING` |
| `AE-BENEFITS` | **End-of-service accrual or funded-benefit clearing.** Worker category, pension, scheme election and free-zone rules determine treatment; no formula approved. | `REG-AE-GOV-EOS` |
| `AE-EQUITY` | **Entity capital, accumulated results and conditional legal reserves.** Exact entity form and current instrument required; no percentage or universal reserve default. | `REG-AE-COMPANIES-2021` |

### Tax, payroll, rights and next review

Obtain operative VAT, corporate-tax, company and worker-benefit instruments for the chosen period. Pension, nationality, savings scheme and free-zone treatment remain unresolved. No conclusion is made about withholding rates or absence of obligations.

Government hosting is not redistribution approval. References and original proposals only are retained. A UAE accountant and appropriate legal/labour specialist must approve applicability, recognition and postings.

**Open evidence gaps:**

- 2023 corporate-tax and 2018 VAT guides require later-law review.
- Companies Law download failed; current consolidation and translation status unresolved.
- End-of-service full page unavailable; obtain operative labour/pension/savings instruments.
- No withholding, free-zone treatment or absence-of-obligation conclusion.

## United Kingdom - GB

### Evidence, legal form and framework

Company guidance and FRC scope require legal-form and eligibility decisions. FRS 105 is tied to an optional eligible micro-entities regime. The FRC says the September 2024 editions and subsequent amendments together form the latest edition; a single PDF hash is insufficient.

**Legal forms to distinguish:** Sole trader; Partnership; LLP; Company with size/eligibility reviewed; Charity or other specialist entity.

**Framework boundaries:**
- FRS 102 versus eligible optional FRS 105 or another applicable framework.
- September 2024 editions plus subsequent amendments and effective dates.
- Sector SORPs/non-company reporting need separate research; no Ireland rules imported.

**Chart/example availability:** Presentation/recordkeeping examples and original group proposals; no complete statutory chart established. No copied account list or statutory account-number requirement was established.

### Primary evidence

The table selects the main sources; the JSON register retains additional supporting and historical pointers. Access limitations are part of the evidence.

| Source | Edition or date | Access and limitation |
|---|---|---|
| `REG-GB-FRC-102` - [FRS 102: The Financial Reporting Standard applicable in the UK and Republic of Ireland](https://www.frc.org.uk/library/standards-codes-policy/accounting-and-reporting/uk-accounting-standards/frs-102/) | September 2024 edition plus subsequent amendments; page lists 2025 and 2026 amendments | page retrieved. Standard text not adopted. Eligibility, early adoption, special entities and sector SORPs require review; UK profile must not automatically apply Irish rules. |
| `REG-GB-FRC-105` - [FRS 105: The Financial Reporting Standard applicable to the Micro-entities Regime](https://www.frc.org.uk/library/standards-codes-policy/accounting-and-reporting/uk-accounting-standards/frs-105/) | September 2024 redacted edition plus subsequent amendments; published 10 September 2024 | page retrieved and linked pdf raw bytes hashed. Raw PDF downloaded for identity; full standard not interpreted. Check legal eligibility, period and subsequent amendments. Hash of the 2024 PDF is not a complete current-standard snapshot. |
| `REG-GB-COMPANIES-ACCOUNTS` - [Annual accounts](https://www.gov.uk/annual-accounts) | Live guidance; no page edition established | page retrieved. General guidance does not establish entity-specific framework, full filing format, assurance or eligibility. LLP, charity and other sector rules need separate review. |
| `REG-GB-HMRC-VAT-BASELINE` - [Keeping VAT records](https://www.gov.uk/charge-reclaim-record-vat/keeping-vat-records) | Undated guidance; no edition established | page retrieved. Registration/scheme and Great Britain/Northern Ireland differences require review. No rates, recovery rules, digital filing or statutory-return output supplied. |
| `REG-GB-HMRC-PAYROLL` - [Running payroll: Deductions](https://www.gov.uk/running-payroll/deductions) | Live guidance; no page edition established | page retrieved. No calculator, rates, thresholds, student-loan rules, pension eligibility, employment-law determination or reporting submission implemented. |

### Original candidate groups

These are accounting-design inferences for review, not rules asserted by the cited authorities. Source IDs resolve to exact URLs in the register.

| Candidate | Proposed group and condition | Evidence |
|---|---|---|
| `GB-VAT` | **VAT input, output and settlement controls.** Registration/scheme and Great Britain/Northern Ireland scope reviewed; no MTD or return mapping. | `REG-GB-HMRC-VAT-BASELINE` |
| `GB-PAYROLL` | **PAYE, National Insurance and pension clearing.** Employee deductions and employer costs separately reviewed; other deductions only where applicable. | `REG-GB-HMRC-PAYROLL` |
| `GB-EQUITY` | **Owner/partner capital or company capital and accumulated results.** Legal form and regime determine presentation; no automatic micro-entity eligibility. | `REG-GB-COMPANIES-ACCOUNTS`, `REG-GB-FRC-102`, `REG-GB-FRC-105` |
| `GB-TAX-BOUNDARY` | **Entity tax balances distinct from owner drawings.** Boundary proposal requiring separate entity-specific income/corporation-tax research; cited source does not establish a tax obligation. | `REG-GB-COMPANIES-ACCOUNTS` |

### Tax, payroll, rights and next review

Income/corporation-tax instruments, LLP/charity/SORP treatment, CIS and special industries remain gaps. VAT schemes and Great Britain/Northern Ireland scope require review. No statutory accounts, Companies House filing or MTD output is provided.

GOV.UK pages display Open Government Licence v3.0 with exceptions; exact-content rights still need checking. This does not license FRC text. A UK accountant must approve period-specific amendments, eligibility, equity/tax distinctions and example reporting.

**Open evidence gaps:**

- FRS 102/105 amendments/effective dates must accompany any approved version.
- LLP/charity/SORP, income/corporation-tax, CIS and special sectors not fully researched.
- FRC copyright and government-content reuse are separate rights questions.
- No statutory accounts, Companies House filing or MTD output provided.

## United States - US

### Evidence, legal form and framework

An LLC legal-form label does not fix federal tax classification. Reporting basis is a separate decision: FASB identifies nongovernmental US GAAP authority, while AICPA describes FRF for SMEs as non-GAAP. The latter does not automatically establish lender or regulator acceptance.

**Legal forms to distinguish:** Sole proprietorship; Partnership; Corporation; S corporation tax status where elected/eligible; LLC with separate federal/state tax classification; Nonprofit/regulated entity: separate research.

**Framework boundaries:**
- Nongovernmental US GAAP distinct from tax accounting.
- AICPA FRF for SMEs is a separate non-GAAP alternative where appropriate.
- Federal entity/tax classification and state/local jurisdictions independently scoped.

**Chart/example availability:** Presentation/recordkeeping examples and original group proposals; no complete statutory chart established. No copied account list or statutory account-number requirement was established.

### Primary evidence

The table selects the main sources; the JSON register retains additional supporting and historical pointers. Access limitations are part of the evidence.

| Source | Edition or date | Access and limitation |
|---|---|---|
| `REG-US-IRS-STRUCTURES` - [Business structures](https://www.irs.gov/businesses/small-businesses-self-employed/business-structures) | Live guidance; no page edition established | page retrieved. LLC legal-form label alone does not establish federal tax classification. Confirm elections, ownership and state law rather than deriving treatment from a name. |
| `REG-US-IRS-RECORDS-BASELINE` - [What kind of records should I keep?](https://www.irs.gov/businesses/small-businesses-self-employed/what-kind-of-records-should-i-keep) | Undated guidance; no edition established | page retrieved. Federal guidance does not supply a mandatory national chart, US GAAP policy, state/local tax treatment or statutory financial statements. |
| `REG-US-IRS-PAYROLL-2026` - [Publication 15 (2026), (Circular E), Employer's Tax Guide](https://www.irs.gov/publications/p15) | 2026 edition | page retrieved. Federal scope only. No rate, wage base, calendar, worker classification, state/local obligation or payroll-filing implementation adopted. |
| `REG-US-FASB-CODIFICATION` - [Standards: FASB Accounting Standards Codification](https://fasb.org/Page/PageContent?PageId=%2Fstaticpages%2Fstandards.html) | Live standards page; no Codification topic/version accessed | primary search index retrieved direct page shell only. No Codification topic read or copied. Eligibility, application, sectors and amendments require qualified review; no US GAAP conformity claim. |
| `REG-US-AICPA-FRF-SME` - [Financial Reporting Framework for Small- and Medium-Sized Entities](https://www.aicpa-cima.com/resources/article/financial-reporting-framework-for-small-and-medium-sized-entities) | Live overview; no complete framework edition accessed | primary search index retrieved direct page shell only. Do not select automatically or imply GAAP equivalence, lender/regulator acceptance or applicability to all SMEs. Full framework and contractual requirements remain unreviewed. |
| `REG-US-CA-RECORDS` - [Sales and Use Tax Regulation 1698: Records](https://www.cdtfa.ca.gov/lawguides/vol1/sutr/1698.html) | History last lists amendment adopted 29 March 2016, effective 1 October 2016 | page retrieved. California only, not nationwide. No nexus, sourcing, taxability, reimbursement policy, rates or 50-state coverage inferred; later-law completeness not certified. |

### Original candidate groups

These are accounting-design inferences for review, not rules asserted by the cited authorities. Source IDs resolve to exact URLs in the register.

| Candidate | Proposed group and condition | Evidence |
|---|---|---|
| `US-EQUITY` | **Owner/member/partner capital, distributions or corporate equity.** Confirm legal form/elections; do not map every LLC to one federal tax class. | `REG-US-IRS-STRUCTURES` |
| `US-PAYROLL` | **Federal withholding, employment-tax and unemployment controls.** Separate employee deductions/employer costs; state/local categories need their own instruments. | `REG-US-IRS-PAYROLL-2026` |
| `US-SALES-USE` | **Jurisdiction-specific sales/use-tax controls.** California is one example; legal incidence, reimbursement and business use-tax treatment require review. No VAT input-credit default. | `REG-US-CA-RECORDS` |
| `US-REPORTING` | **Business income/expense and reporting-basis mappings.** Neutral categories need reviewed GAAP or other-basis report mappings; no GAAP or tax-return claim. | `REG-US-IRS-RECORDS-BASELINE`, `REG-US-FASB-CODIFICATION`, `REG-US-AICPA-FRF-SME` |

### Tax, payroll, rights and next review

No 50-state/local coverage, nexus, sourcing, reimbursement policy, worker classification or federal/state filing is established. California supplies one bounded records example only. Nonprofit, government and regulated-sector frameworks remain separate work.

Government and standards-body material need source-specific rights review. A US accountant must confirm legal/tax classification, accepted reporting basis and jurisdiction-limited mappings; relevant counsel resolves legal applicability. A US label cannot authorize multi-state rules.

**Open evidence gaps:**

- FASB/AICPA substantive evidence limited to primary search excerpts; full frameworks not interpreted.
- No 50-state/local sales/use-tax, nexus, sourcing, unemployment or labour coverage.
- Lender/regulator acceptance, nonprofit/government and special sectors require separate review.
- No federal/state filing, payroll engine, rates or tax calculations provided.

## Vendor comparison and promotion gates

`REG-UPSTREAM-ERP-BASELINE` and `REG-UPSTREAM-ODOO-BASELINE` preserve earlier repository comparison pins and then-observed licence declarations. They were not re-fetched. No vendor account list, old source hash or licence observation becomes an approved package by being added to this register.

Before promotion, reviewers must record:

1. **Scope and freshness:** entity, legal form, registration/jurisdiction, reporting period, framework, exact source versions and applicable later amendments.
2. **Accounting review:** semantic roles, account types and normal balances, gross/net treatment, recoverability boundaries, reporting mappings and opening reconciliation. Review affected invoices, bills, partial settlements, credits and reversals.
3. **Legal and rights decisions:** specialist review where needed; explicit rights before reproducing/adapting standards, formats or charts. Keeping a reference is not a redistribution-permission finding.
4. **Validation evidence:** coherent composition, no conflicting control roles, repeatable posting/report examples, immutable version/review records and the approval gates in the catalogue contract.

No interviews, accountant/counsel approvals, external writes, application changes, migrations, schema changes, staging, commits or publication occurred in this research pass. Public government/professional websites were read. Unavailable or partial sources are recorded as limitations, not treated as fully reviewed content.
