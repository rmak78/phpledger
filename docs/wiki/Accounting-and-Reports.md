# From a transaction to an explainable balance

Every posting uses one service that checks access, company/book scope, dates, currency, account ownership and balanced debit/credit totals. Financial decisions use exact decimals.

Open **Reports → Open account ledger** to choose any account, or follow the ledger links from Transactions and Journals. Mobile statements show debit, credit and running balance together for each movement.

## What 0.1.4-preview does

| Workflow | Present scope |
|---|---|
| Account statements | Any authorized account: opening balance, period debits/credits, running balances and closing balance, with journal/source links and pagination. |
| Chart management | Authorized owners/accountants create accounts and edit names or active status with an audit trail and stale-edit protection. Code, type and purpose remain fixed. |
| General journals | Save and reopen drafts, edit lines, review totals and explicitly post. Drafts may be unbalanced; posting must balance. |
| Receipts and expenses | Save recoverable drafts and explicitly post them through the same accounting services. |
| Corrections | Preserve the posted entry and create a linked reversal with a reason. |
| Trial balance | Read posted balances within the selected business/book and investigate the underlying accounts. |
| Profit and loss / balance sheet | Basic posted income/expense and position summaries; complete professional statement structure remains under review. |
| Cash scenario | Project entered weekly cash in/out from posted opening cash; no forecast entries are posted. |

A statement's **opening balance** is the balance before its selected date range. This calculation alone does not establish a reconciled cutover. Use the separate opening trial-balance/CSV preview and confirmation workflow, including unpaid-document reconciliation where needed.

Posting checks the current saved draft; a changed draft must be reviewed again. Identical retries do not create duplicate journals, and closed periods reject new posting. Linked reversals preserve both sides of the correction. Technical checks support these controls but do not replace qualified accounting review.

The public demo keeps accounts read-only. Visitors can save/edit general drafts, post balanced entries and use linked reversals within their own temporary books and capacity limits.

## A complete statement needs more than a layout

Applicable accounting guidance governs recognition, measurement, classification, adjustments, closing and disclosures as well as presentation. Each regional profile uses its relevant statutory framework and professional guidance. Pakistan is one intended direction within a country-neutral core; it does not define the entire audience or a mandatory global rollout sequence. ICAP, ICMAP and ACCA references inform the research; they are not a product certification.

The next reporting work needs reviewed classifications, current/non-current distinctions, appropriate COGS/subledger data, comparatives, equity movements and the notes or cash-flow components required by the selected profile. Issued results must retain their profile/version. Missing mappings or records must stay visible.

Current starter accounts and summaries do not implement a complete country framework. Tax regimes are separately researched and remain disabled; see [[Tax research|Tax-Research]].

## Next core work: opening, periods and reconciliation

Reviewed opening/cutover, period-close/reopen administration, bank-statement matching and reconciliation are planned. Imports must preview mappings, errors and totals before explicit confirmation.

Core-only cutover must explain retained AR/AP controls using reconciled external unpaid-document schedules until the optional AR/AP modules exist. Their later activation must match those balances without reposting them. Customer/vendor open-item statements and aging require those modules; an ordinary account statement does not supply them.

The complete core must let a supported business reconcile and complete its accounting period with optional modules disabled. [[Module roadmap|Module-Roadmap]] sets out the sequence and acceptance gates.

[[Countries and currencies|Countries-and-Currencies]] · [[Architecture]] · [[First package|First-Package]]
