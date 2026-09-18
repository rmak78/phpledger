# Reporting gap analysis

**Status: code review and proposed design, 14 September 2026. No implementation or schema changes. Publication is on hold for the report/POS review and redesign requested by the user.**

The current services provide traceable, exact-decimal summaries of the posted ledger. They do not yet model the classifications, comparisons, disclosure structure, or reproducibility needed for a professionally reviewed statement package. Changing their typography alone cannot supply those missing accounting inputs. This document identifies the technical gaps in the actual checkout; country/framework requirements and ACCA/ACMA-related reference research are separate workstreams, not a claim that one universal statutory format exists.

**Explicit user requirement: the selected accounting guidelines must govern the system, not only the report format.** Once the applicable framework and entity/country profile are reviewed, their supported requirements must guide account meaning, recognition and measurement decisions, transaction/journal validation, inventory/cost treatment, period-end adjustments, closing and retained earnings, control-account reconciliation, and disclosures. A renderer cannot repair missing accounting rules or source records. Record the chosen policies and effective versions, expose unsupported requirements, and validate the end-to-end behavior with an accounting reviewer. ACCA/ACMA reference material informs this work; a professional-body label alone does not select an entity's applicable accounting framework or prove compliance.

## Evidence inspected

- [Foundation schema](../../www/phpledger/install/migrations/001_foundation.php): company/book scope, five account root types, accounting periods, exact journal lines, posted identities, and immutable journal/line guards.
- [Product-slice schema](../../www/phpledger/install/migrations/002_product_slice.php): nullable semantic keys/functional roles, one installed chart snapshot per company, and receipt/expense source documents.
- [Starter chart](../../resources/coa/core-starter-1.0.0.json): six preliminary accounts, including trade receivable/payable controls but no active subledgers or cost/asset detail.
- [Report services](../../www/phpledger/includes/functions/report_functions.php), [trial balance](../../www/phpledger/includes/functions/ledger_functions.php), [account activity](../../www/phpledger/includes/functions/document_functions.php), and [setup services](../../www/phpledger/includes/functions/setup_functions.php).
- [Current routes](../../www/phpledger/public/index.php), [shared layout](../../www/phpledger/templates/layout.php), and the [P&L](../../www/phpledger/templates/views/profit-loss.php), [balance sheet](../../www/phpledger/templates/views/balance-sheet.php), [trial balance](../../www/phpledger/templates/views/trial-balance.php), [account activity](../../www/phpledger/templates/views/account.php), and [reports hub](../../www/phpledger/templates/views/reports.php) templates.
- [Report tests](../../tests/report_test.php), [POS boundary](../POS.md), and the earlier [proposed chart-template model](../coa/TEMPLATE_MODEL.md). The latter is a historical research proposal: its old “current boundary” predates migration 002, which has now added semantic keys, roles, and installation snapshots.

The code references establish current behavior and missing structures. No production data, external accounting API, Google Drive document, or hosted application was inspected for this review.

## What is already dependable within the tested scope

Report queries enforce the same company/book access boundary as the ledger, use exact decimal arithmetic, exclude unposted source documents, and include reversals according to their posting dates. Account drilldown retains the selected P&L date range and links to journal/source detail. Inactive accounts with historical postings remain included. The trial balance compares debit and credit totals, and the balance sheet adds accumulated unclosed income less expenses only once alongside recorded equity.

Those properties are useful foundations. A balanced equation proves arithmetic agreement of recorded entries; it does not prove that opening positions, adjustments, account classification, or supporting subledgers are complete. Current tests confirm the bounded examples; they do not establish a full reporting-framework implementation.

## Current behavior and missing capability

| Area | Actual code evidence | Gap and practical effect |
|---|---|---|
| Report classification | `pl_accounts.type` is limited to asset, liability, equity, income, expense. Migration 002 adds semantic key and operational role. | No statement-line mapping, report hierarchy, normal/contra presentation metadata, or reviewed country/framework package. Root types are too broad to express professional line items. |
| Financial position | `pl_balance_sheet()` produces flat assets/liabilities/equity arrays ordered by account code. | No current/non-current split, property/plant/equipment, accumulated depreciation, financing maturity, tax categories, or other detailed classifications. A name or account number cannot safely determine those meanings. |
| Performance | `pl_profit_loss()` groups every income account and every expense account, then subtracts total expenses from income. | No separately mapped revenue, cost of sales, gross profit, distribution/admin expenses, other income, finance costs, or tax lines. Their relevance and definitions need the chosen reporting profile and accountant review. |
| Inventory and cost of sales | The starter chart has one general-expense account; POS posts only cash versus sales and explicitly excludes inventory/COGS. | A sample shop sale cannot produce a reliable gross margin, stock value, or cost-of-sales figure. Do not fill these missing report lines with zero or derive costs from selling prices. |
| Receivables/payables | Starter controls exist, but `pl_documents` stores only receipts/expenses with free-text counterparties. | There are no customer/vendor balances, unpaid invoices/bills, due dates, allocations, aging, or subledger reconciliation. A zero control-account balance is merely the posted ledger result; it is not proof that the business has no debtors/creditors. |
| Comparatives | P&L accepts one `from/to`; balance sheet accepts one `as_of`. Templates render one amount column. | No prior-period selection, comparable-period mapping, prior-year columns, variance, restated comparator, or disclosed difference in period length. |
| Fiscal periods | Company fiscal-year end and period boundaries are stored. P&L defaults to company start date and today; the hub uses company start/today. | Default reports do not resolve a chosen fiscal year or a comparable prior fiscal period. A company older than one year sees inception-to-date results until it changes dates. |
| Equity and closing | `earned_profit` is the negative sum of all unclosed income/expense balances through `as_of`, added to recorded equity. | No opening retained earnings, current-year result, distributions/drawings, closing-transfer workflow, statement of changes in equity, or reviewed rollforward. Current test fixtures prove capital/liability/profit inclusion, not year-end closing or comparative retained-earnings behavior. |
| Cash flow versus scenario | `pl_cash_balance()` sums asset accounts with `role=cash_bank`; `pl_cash_forecast()` repeats entered weekly inflow/outflow assumptions. | No operating/investing/financing classification, noncash adjustments, or cash-flow statement. The browser scenario currently starts from **today's UTC-date balance**; there is no selectable opening-date control. It must stay clearly labeled as a scenario. |
| Completeness | The shared layout shows a readiness banner for all company views, including P&L/BS, and trial balance adds its own opening-review warning. Report services still allow authorized reads and return no structured completeness result. | The general warning is present, but there is no per-report mapping/opening/subledger coverage status or issued-report gate. A balanced draft result must remain distinct from a complete reviewed statement package. |
| Cash mapping | `pl_cash_balance()` filters by `role=cash_bank`; old accounts remain unmapped until explicit setup review. | A nonzero cash account without that role is omitted from the cash total, which can return numeric zero without a mapping warning. Reporting must not equate missing role coverage with known zero cash. |
| Empty versus zero | Aggregate SQL uses `COALESCE(..., 0)`. Reports include account rows but no coverage/posted-row-count metadata. A pre-start zero balance is accepted in current tests. | The renderer cannot reliably distinguish a known zero, no activity in the selected range, missing opening data, unmapped categories, or unsupported source modules. Blank layout space must not imply any of these states. |
| Notes and entity information | Current header includes company name, currency, and dates. Company records do not store a reporting framework/entity classification or statement-disclosure package. | No note references, accounting-policy text, approval/signature roles, reporting basis, or reviewed entity/jurisdiction-specific disclosures. These cannot be inferred from IP country hints. |
| Exports and issue history | The router exposes HTML reports but no report PDF/CSV/XLSX export or persisted report-run entity. | No controlled print pagination, comparative export, statement package, report version, approval status, or stored “as issued” result. Browser printing alone is not a verified statement export. |
| Coherent report run | The hub obtains P&L, balance sheet and cash through separate service calls; no explicit shared reporting snapshot wraps them. | Outside the serialized demo, concurrent postings could change the ledger between those calls. Future statement packages need a consistent read and an explicit issued-result record. Current tests do not prove concurrent multi-report coherence. |

## Proposed classification and version model

This is a design proposal for review, not authorization to edit existing migrations or create a second accounting engine. Retain the central ledger, the five root types, exact money, existing company/book permissions, and stable account IDs. Add reporting metadata separately from workflow roles and accounting postings.

| Concept | Proposed contract |
|---|---|
| Reporting profile | Explicit entity/reporting-basis/jurisdiction choices confirmed by an authorized accountant. Keep language and currency display separate from the accounting profile. Country detection may suggest a region, never select the reporting rules. |
| Versioned report definition | Stable package ID/version/digest, report type, effective applicability, language keys, line hierarchy/order, safe subtotal definitions, sign/presentation rules, required disclosures, provenance, review status and reviewer. Released versions are immutable. |
| Scoped account mapping | Book/account IDs mapped to stable report-line keys, with mapping version, actor/reason and confirmation time. Seed suggestions may use verified semantic keys; account names or numeric ranges must not silently make final decisions. |
| Mapping completeness | Every relevant account is assigned to one additive leaf for a given report, or explicitly identified as excluded/incomplete with a reason. Subtotals reference leaves; repeated visual references never add the same amount twice. Validate allowed root types, mapping conflicts and hierarchy/formula cycles. |
| Contra and sign handling | Specify presentation separately from root type and debit/credit storage. Preserve legitimate opposite-side balances; do not rewrite a ledger account's type or erase a negative amount just to fit the display. |
| Effective versions | New mappings can create a reviewed new report version, but cannot silently alter a previously issued result. Show whether comparisons use the original mapping or an explicitly restated mapping, with a reconciliation of changes. |
| Report run | Record company/book, dates, currencies/scale, definition/mapping versions, filters, completeness findings, consistent source snapshot, generated timestamp and status. Keep an issued result's line amounts and source references reproducible even after later backdated postings. |
| Notes and approval | Link reviewed notes/disclosures to the relevant report version and reporting period. Draft notes, unsupported sections, required approvals and omitted data must remain visible; technical balance alone must not mark the package approved. |

The existing `pl_template_installations` record pins a chart and account mapping once at setup. It is a useful precedent, but it is **not** report-version history or a report-run snapshot. Do not overwrite it to simulate issued-report versioning. Upgrades must preserve customer account IDs, codes, names, balances, and previously issued reports.

Use typed, constrained subtotal operations rather than evaluating arbitrary expressions from a report file. Validate exact totals before presentation rounding. A future display/export rounding policy must explain any difference between displayed lines and totals without altering posted money. Country/framework classification and year-end treatment require the separate accounting review before implementation.

For one report run, use a consistent database read across its component statements. For a saved issued result, persist the evaluated line values and the source/version information needed for drilldown and reproduction. A timestamp or maximum journal ID by itself is not sufficient proof of an immutable report result under concurrent commits and later backdated entries. Keep report-read snapshot behavior distinct from write idempotency.

## Never confuse missing information with zero

The next renderer needs explicit data state alongside every relevant line and the report as a whole:

| State | Meaning and presentation |
|---|---|
| Recorded amount, including known zero | Successfully evaluated mapped inputs in a complete selected scope. Display the exact amount; zero is valid when the coverage checks support it. |
| No posted activity in range | The selected accounts exist and are mapped, but this period has no posted movements. Say so explicitly and retain date/source context; do not imply that a full set of opening books has been reviewed. |
| Unmapped | Relevant accounts exist but lack a confirmed statement assignment. Show account count and exact unresolved amount, with a review action. Never drop the amount from a professional total silently. |
| Missing opening or supporting data | Required cutover/opening review or supporting subledger is incomplete. Show “Incomplete” with the cause. Totals may remain available as a draft preview but cannot be presented as complete issued statements. |
| Unsupported module | The application does not yet record the underlying information, such as stock valuation or invoice aging. Show “Not available in this preview,” not `0.00`. |
| Not applicable | An authorized review determined that a section does not apply to this entity/period. Keep that decision and reason; do not infer it from an empty query. |
| Failed validation | A scope, reconciliation, classification, or calculation error occurred. Show an error and withhold the affected total instead of substituting zero or an empty cell. |

For a formal report-definition preview, nonzero unmapped values must block complete/issued status. Zero-valued unmapped accounts still need a visible count and mapping decision because later postings would otherwise disappear. Include a dedicated reconciliation from the mapped statement to its scoped trial balance, listing omissions and duplicate assignments explicitly. Inactive accounts with historical balances remain in this check.

This is a reporting-quality gate, not a requirement to block every operational draft entry. Posting readiness remains controlled by the existing accounting services; report issuance adds its own explicit completeness requirements. Known zero figures may be formatted as a dash only after their underlying state is established and the chosen statement presentation explains that convention.

## Bounded next implementation sequence

The original [two-year retail statement fixture](examples/retail-statements.md) provides a non-installed arithmetic acceptance reference for the proposed system. It is sample, expressed in PKR thousands, and is not a Pakistan compliance template or evidence that the current application implements these statements.

1. Combine the country/framework research with a small representative chart and worked examples. Choose the first accounting/reporting profile and confirm system policies, recognition/measurement boundaries, posting/closing controls, classifications, fiscal comparisons, earnings treatment, and note/approval scope. Do not claim generic ACCA/ACMA certification or implement only a cosmetic statement format.
2. Prove a country-neutral **management statement** definition and account-mapping preview against the existing ledger before adding any country-specific release. Clearly label scope; do not imply filing readiness.
3. Add reviewed versioned mapping and completeness results, retaining customer account identity and the single central posting service. Include no-data, unmapped, opening-incomplete, and unsupported-module cases before polishing final statement layouts.
4. Build one readable statement layout with conventional line hierarchy, current/comparative columns where supported, subtotals, scope/currency/period context, account drilldown, and visible quality status. Keep the owner overview distinct from the statement document.
5. Validate totals, mappings and period/closing examples with the accounting reviewer; then implement reproducible export/print and issued-result history. Expand to country-specific formats only after their own definitions and evidence are reviewed.

Required future tests should include detailed/contra accounts, current/non-current mappings, known-zero versus unmapped/missing data, new and inactive accounts, signed balances, genuine prior-period comparatives, opening balances, closing transfers and retained-earnings rollforwards, later backdated postings, concurrent report reads, mapping-version changes, exact rounded presentation reconciliation, and export/drilldown identity. None is marked passed merely because the earlier 54-test technical suite passed.

## Review receipt

This review changed documentation only. It read the files listed above and traced queries, return fields, route defaults, templates, and existing tests; no new application/financial tests or browser sessions were run. No migrations, schema edits, production changes, external/live calls, or raw-secret exposure occurred. External reporting standards and legal applicability remain with the separate primary-source research workstreams.
