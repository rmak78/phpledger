# Pakistan accounting and reporting research

**Research date: 14 September 2026. Status: source-backed design input, not an implemented or professionally approved reporting package.** Pakistan is the user-approved first country, followed by the UK and UAE. Publication remains on hold for the report/POS redesign. The existing [reporting gap analysis](REPORTING_GAP_ANALYSIS.md) records the checkout's technical limitations.

## Approved requirement and authority

The user's requirement applies to the **accounting system itself**, including account meaning, recognition, measurement, posting, period-end adjustments, closing, statements and disclosures. A visual report template alone does not meet it. Each supported country/entity/period must have an explicitly selected, versioned accounting and reporting profile, reviewed against its authoritative requirements. A country-IP suggestion, currency or interface language must never choose the framework.

For Pakistan, interpret the user's “ICA” as **ICAP**, and “ICMA” as **ICMAP/ICMA Pakistan**. ICAP's Accounting Standards Board recommends international standards for SECP notification. SECP and the relevant legislation determine company reporting obligations. [ICAP standards process](https://www.icap.net.pk/standards)

**ACCA is a professional accountancy body; ACMA is a membership designation, not a financial-statement standard.** Use applicable guidance and qualified reviewers from the requested bodies, without advertising an invented universal “ACCA/ACMA format” or certification. [ACCA](https://www.accaglobal.com/gb/en/about-us.html), [ICMAP membership](https://www.icmap.com.pk/membership.aspx)

ICMAP's official *Model Financial Statements for SMEs* explains framework selection, comparative information, policy consistency, complete statements and disclosure. It is useful professional teaching material; its older company-size thresholds are superseded by the newer SECP schedule below. ACCA's technical articles help explain statement terminology and cash-flow reconciliation, but an examination example does not determine Pakistan statutory applicability. [ICMAP presentation](https://www.icmap.com.pk/downloads/Presentations/wrk_model_financial_statements_SMEs.pdf), [ACCA presentation guidance](https://www.accaglobal.com/middle-east/en/student/exam-support-resources/fundamentals-exams-study-resources/f7/technical-articles/rd1.html), [ACCA cash-flow explanation](https://www.accaglobal.com/uk/en/student/exam-support-resources/fundamentals-exams-study-resources/f3/technical-articles/cashflow-statements.html)

## Entity classification before template selection

The following size thresholds come from SECP's **Third Schedule updated 29 December 2025**. Amounts are PKR millions. Apply special/public-interest categories before ordinary size tests; this is a review matrix, not an implemented eligibility calculator.

| Ordinary company class | Size criteria | Framework and additional schedule |
|---|---|---|
| Small private company | All: paid-up capital ≤10; turnover ≤150; employees ≤250 | Revised AFRS for SSEs; Fifth Schedule |
| Medium private company | After excluding public-interest/large cases: capital >10 and <200, or turnover >150 and ≤800, or employees >250 and <750 | IFRS for SMEs; Fifth Schedule |
| Medium non-listed public company | All: capital <200; turnover ≤800; employees <750 | IFRS for SMEs; Fifth Schedule |
| Large non-listed company | Any: capital ≥200; turnover >800; employees ≥750 | Notified IFRS; Fifth Schedule |

Classification uses the previous year's audited financial statements; change of class generally requires falling outside the criteria for two consecutive years. Employee count uses a monthly average. First-year cases and category precedence require a documented reviewer decision. [SECP Third Schedule](https://www.secp.gov.pk/document/updated-3rd-schedule-to-the-companies-act-2017-29-12-2025/)

The separate category rules also matter:

| Category | Consequence for the profile |
|---|---|
| Listed/public-interest company | Full IFRS path; listed companies use Fourth Schedule, ordinary non-listed public-interest companies Fifth. Sector rules may add requirements. |
| Foreign company | Turnover ≥PKR 1 billion selects large; below that selects medium. |
| Section 42/45 nonprofit company | Revenue including other income ≥PKR 200 million selects IFRS plus NPO standard; below selects IFRS for SMEs plus NPO standard; Fifth Schedule also applies. |
| Permitted higher-framework election | Medium companies may elect notified IFRS; small companies may elect IFRS for SMEs or notified IFRS. Record the election and consequences. |

The IFRS Foundation's Pakistan profile corroborates these reporting paths. Do not assume a membership club is an ordinary trading company, or that all shops are incorporated. [IFRS Foundation Pakistan profile](https://www.ifrs.org/use-around-the-world/use-of-ifrs-standards-by-jurisdiction/view-jurisdiction/pakistan/)

**Non-company businesses need their own decision.** ICAP TR-5 (Revised 2026), effective for periods beginning on/after 1 July 2026, directs its members to applicable statutes first. For other entities without a prescribed framework, it recommends a suitable framework with at least Revised AFRS for SSEs. This is professional guidance, not proof that Companies Act company thresholds legally govern every sole proprietor, partnership or club. Record legal form and applicable statute before recommending a basis. [ICAP TR-5 Revised 2026](https://www.icap.net.pk/wp-content/uploads/2026/06/Circular-2-of-2026-TR-5-Statement-on-applicability-of-IFRS-Accounting-Standards-and-pronouncements-issued-by-the-IASB-Revised-2026.pdf)

## Editions and effective periods

ICAP's retrieved adoption-status table is labelled **as on 1 January 2026**. It records IFRS 18 as adopted for annual periods beginning on/after **1 January 2027**, replacing IAS 1, and records the third IFRS for SMEs edition as under SECP consideration while the second edition is adopted. This snapshot does not establish that no subsequent notification occurred before this research date. Verify later notifications before releasing a profile. [ICAP adoption status](https://www.icap.net.pk/wp-content/uploads/2026/04/IFRS-Accounting-and-Sustainability-Disclosure-Standards-Adoption-Status.pdf)

The IASB issued the third IFRS for SMEs edition in February 2025, internationally effective for periods beginning on/after 1 January 2027 with early application permitted. International issuance and local adoption are separate decisions. [IFRS for SMEs editions](https://www.ifrs.org/issued-standards/ifrs-for-smes/)

Implementation consequence: resolve applicability using the **period start and approved framework edition**, not the date the user opens a report. Store the notification/source version and review date with the profile. Do not silently apply IFRS 18 to an earlier period or replace an issued report when a package is updated.

## Statement package and usable presentation

### Small-entity path

The Revised AFRS for SSEs sets accrual accounting, consistent annual presentation and preceding-period comparatives. Its minimum set is a statement of financial position, income statement, and accounting policies/explanatory notes. Current/non-current classification and material asset, liability, equity, revenue and expense distinctions are required; the current five broad account types are insufficient. See section 1, especially paragraphs 1.2, 1.7–1.12 and 1.13–1.25. [Revised AFRS for SSEs, 2015](https://icap.net.pk/wp-content/uploads/2013/12/SSE-Standard-Final-for-notification.pdf)

ICAP's **2018 Companies Act FAQ**, question 5, printed pages 16–17, explains that the applicable framework sets the minimum components: the SSE path has those three mandatory components and may include extra statements. It also explains the Companies Act statement titles. This avoids incorrectly imposing every full-IFRS statement on every small entity; current legislation and subsequent amendments must still be checked for the release. [ICAP Companies Act FAQ](https://www.icap.net.pk/wp-content/uploads/2018/07/Financial-Reporting-under-the-Companies-Act-2017-Your-questions-answered.pdf)

The 2015 SSE text alone is not the complete amendment record. ICAP Circular 7/2018 changes revaluation-surplus treatment/presentation for company periods ended on/after 30 June 2018. Include applicable amendments in the profile's source register. [ICAP amendment circular](https://www.icap.net.pk/wp-content/uploads/2018/07/Circular-No.7-of-2018-Amendments-to-Revised-AFRS-for-SSEs.pdf)

### Medium-company reference

ICAP's official *Illustrative Financial Statements for Medium Sized Companies* uses IFRS for SMEs **2015** and the Companies Act Fifth Schedule. The guidance portal lists it on **12 August 2020**; that listing date is not a new standards edition. Its actual statements were inspected: PDF pages 19 and 22 show a vertical statement with **Note / current year / prior year** columns, currency units, section headings and clear subtotals.

The reference includes position, profit/loss and comprehensive income, equity movements, cash flows and supporting notes. Its position statement separates current/non-current items and equity components; profit/loss distinguishes revenue, cost of sales, gross profit, operating expenses, finance costs and tax. It is an illustration requiring entity-specific judgement, not a ready compliance certificate. Use the layout principles in an original PHP Ledger design, not a copied full publication. [ICAP illustration](https://www.icap.net.pk/files/pdf/acguidancetools/IllustrativeFinancialStatementsforMediumSizedCompanies.pdf), [official guidance listing](https://www.icap.net.pk/accounting-guidancetools)

### Proposed PHP Ledger report structure

This is an original implementation recommendation to be reconciled with the selected profile, not a universal prescribed form:

| Report | Proposed semantic sections and required inputs |
|---|---|
| Statement of financial position | Non-current assets, current assets, equity, non-current liabilities, current liabilities; mapped accounts, contra balances, maturity allocations and equity reconciliation. |
| Statement of profit or loss | Revenue, cost of sales, gross profit, operating expenses, other income/expense, finance costs, tax and profit; use the profile's permitted expense presentation and effective-period rules. |
| Comprehensive income, when applicable | Profit plus individually identified OCI movements and their tax effect; do not route every reserve movement through ordinary profit. |
| Changes in equity, when applicable or supplementary | Opening components, profit/OCI, capital changes, distributions and reviewed restatements, closing components; owner drawings and company dividends must retain their distinct legal/accounting meanings. |
| Historical statement of cash flows | Actual operating, investing and financing flows, non-cash adjustments/disclosures, opening-to-closing cash reconciliation; transaction classification and supporting movements are necessary. |
| Notes | Entity/basis/policies, cross-referenced supporting schedules, required judgements and disclosures, relevant commitments/contingencies and approval information. Missing content is visible, not boilerplate filled automatically. |

Keep simple owner labels such as “Profit and loss” in navigation; use the selected formal title in issued statements. Headers identify entity, individual/group scope, period, currency and rounding unit. Show genuine comparatives; do not repeat the current year or fill unavailable history with zeros. Add note links and source drilldown without turning the formal statement into dashboard cards. Restated labels, signatures, authorization dates and audit opinions appear only when supported by actual reviewed records.

**Cash forecast remains separate.** IAS 7 concerns historical cash/cash-equivalent movement, classified as operating, investing and financing, with reconciliation and separate treatment of non-cash transactions. A future weekly inflow/outflow scenario does not satisfy it. For an SME profile apply that framework's cash-flow section rather than assuming all full-IFRS choices match. [IAS 7 overview](https://www.ifrs.org/issued-standards/list-of-standards/ias-7-statement-of-cash-flows/)

## Accounting behaviour that must support the reports

The following is the proposed engineering/reviewer work package; exact policies and paragraph references must be signed off before implementation:

| Area | Required decision and evidence |
|---|---|
| Revenue and receivables/payables | Recognition event and measurement, returns/discounts, credit documents and settlement; distinguish revenue from collection and expense from payment. Reconcile subledgers to controls. |
| Inventory and margin | Approved valuation/cost formula, cost components, write-downs and sale/return cost recognition. A cash sale alone cannot establish gross margin. |
| Assets and adjustments | Capitalization, useful life/depreciation, impairment, prepayments/accruals, provisions and other relevant estimates; approved dated adjustment sources. |
| Equity and close | Legal-form-specific capital/distribution accounts, period result versus accumulated retained earnings, reviewed adjustments and repeat-safe close/reopen controls. Prevent earnings appearing twice. |
| Tax and other local matters | Separate financial-reporting measurement/disclosure from tax-return, withholding and indirect-tax obligations. Review the actual entity's applicable rules; do not infer tax from PKR. |
| Policy changes and errors | Effective versions, reason and authorization, original versus restated comparatives, linked adjustments and preserved issued history. No rewriting immutable journals to restyle a statement. |

Use existing exact-decimal, scoped posting services for all financial writes. Add tested accounting rules and versioned reporting metadata around that boundary, not a second posting engine. A supported business must either handle a relevant requirement or show an explicit unsupported/review-needed state; a blank line is not proof of zero.

## What the current checkout cannot yet deliver

The installed [core starter chart](../../resources/coa/core-starter-1.0.0.json) contains only cash/bank, receivables, payables, owner equity, sales/service income and general expenses. It does not distinguish inventory, cost of sales, fixed/contra assets, borrowing maturities, tax or separate retained-earnings components.

The [report services](../../www/phpledger/includes/functions/report_functions.php) total income/expense movements and broad position groups, adding accumulated unclosed profit once. They have no country/framework profile, comparative package, disclosure workflow, issued version or historical cash-flow statement. The [POS showcase](../POS.md) records cash versus sales without stock/COGS/tax; its sale cannot supply a defensible retail margin. Technical balance and the earlier test suite do not establish accounting-standard compliance. See [detailed gaps](REPORTING_GAP_ANALYSIS.md).

## Bounded delivery sequence and acceptance gates

1. **Approve one Pakistan profile.** Start with an ordinary trading/service entity; exclude regulated financial entities, public-interest/SOE, nonprofit and consolidated groups from the first package. Confirm legal form, classification, period, framework/election, edition, notifications and required components with a Pakistan accountant. Keep non-company and company profiles distinct.
2. **Write the accounting rule register.** For every supported requirement record authoritative source/paragraph, interpretation, policy choice, effective dates, affected account/service, test fixture and reviewer decision. Record unsupported areas explicitly. Professional review must cover recognition and measurement as well as report appearance.
3. **Agree a complete worked example before expanding code.** Build on the original [two-year retail example](examples/retail-statements.md), adding reviewed source transactions and adjustments. Include opening balances, credit sales/collections, purchases/payments, closing stock/COGS, fixed assets/depreciation, borrowing/current portions, interest/tax, capital/distributions and prior-period comparatives. This file is arithmetic research, not an installed Pakistan template.
4. **Implement mappings and statement runs.** Preserve account IDs/history. Version account-to-line mappings, disclosures and policy/profile releases. Use a consistent read across a package and preserve the issued values/provenance. Current/non-current allocations and cash-flow classification may need supporting data, not just account labels. Keep missing, unmapped, not applicable and known zero distinct.
5. **Pass end-to-end accounting fixtures.** Post the example through the central services, reconcile all statements and notes to the trial balance/subledgers, reconcile equity and cash movements, and prove no duplicated earnings. Test corrections, closed periods, previous-year opening/import history, restatement/mapping revisions and later backdated entries. Test display rounding without altering ledger amounts, comparative drilldown and printable/exported identity.
6. **Review with qualified practitioners and users.** Obtain recorded Pakistan accounting review by appropriately qualified/licensed practitioners, including ICAP/ICMAP expertise as appropriate, and ACCA-informed financial-reporting review. Verify legal eligibility for any audit/signature role separately. Then test owner/accountant comprehension. Only the explicitly reviewed scope may be described as supported.
7. **Research UK, then UAE independently.** Reuse the profile/mapping infrastructure, not Pakistan's thresholds or disclosures. Confirm each jurisdiction's legal forms, adopted frameworks and effective periods before implementing or marketing support. India remains an earlier seed idea, not ahead of the user's new priority order.

## Remaining verification and reference rights

Before releasing a Pakistan profile, reconcile the current consolidated Companies Act, Fourth/Fifth Schedules and applicable SECP/sector notifications. The retrieved 2025 Third Schedule is current to its stated update; the attempted consolidated Act download available through the portal was an older edition and could not establish a complete September 2026 legal set. Recheck adoption changes after ICAP's January 2026 snapshot, and obtain the full applicable SSE amendment text where the circular only announces it.

ICAP's revised 2025 compliance guidance illustrates framework-specific wording and statutory precedence. It reinforces why PHP Ledger must not automatically insert an unreserved compliance statement based only on a selected template. [ICAP compliance guidance, revised 2025](https://www.icap.net.pk/wp-content/uploads/2025/11/Statement-of-Compliance-for-the-Financial-Statements-prepared-under-the-Companies-Act-2017-Revised-2025.pdf)

Public availability does not establish redistribution rights. Link to the official publications and write original specifications/examples; do not bundle full IFRS/ICAP/ICMAP publications, reproduce their disclosure checklists wholesale, or imply endorsement. Source PDFs were read as references, including visual inspection of the ICAP illustration; no permission to republish their contents or branding was verified.

This task changes documentation only. The three changed documents passed 31 local file/anchor checks, 23 external URL-shape checks and a trailing-whitespace check; `git diff --check` reported no errors (the documents remain untracked in the current restart checkout). URL-shape checks are not a claim that every remote publication is always available. External read-only official web/PDF calls: yes. Google Drive documents: none. Migrations/schema changes: no. Raw secrets exposed: no. Live/production changes: no. Application tests were not rerun for this documentation task; the future accounting fixtures and professional approval above remain open gates.
