# From a transaction to an explainable balance

Every financial write in the new foundation goes through one posting service. It checks access, company/book scope, dates, currency, account ownership and balanced debit/credit totals before committing the result. Financial decisions use exact decimals.

## What the local preview does

| Workflow | Present scope |
|---|---|
| Receipt and expense entry | Save recoverable drafts, then explicitly post them. |
| Journal | Keep a durable source reference and balanced lines. |
| Corrections | Preserve posted entries and create linked reversals. |
| Trial balance and account activity | Read posted records within the selected business/book and link back to sources. |
| Profit and loss / balance sheet | Basic posted income/expense and position summaries; professional statement structure is under refinement. |
| Cash scenario | Project entered weekly cash in/out from the posted opening cash balance; no forecast entries are posted. |

An identical retry is protected against duplicate posting. Closed periods reject new postings. Automated checks cover these boundaries, including concurrency and rollback, but technical passes do not substitute for qualified accounting review.

## A complete statement needs more than a layout

Applicable accounting guidance must govern recognition, measurement, classification, adjustments, closing and disclosures as well as presentation. The regional work starts with **Pakistan, then the UK and UAE**, informed by the relevant statutory framework and professional guidance. No professional-body endorsement is implied.

The next reporting model needs reviewed classifications, current/non-current distinctions, appropriate COGS and subledger data, genuine comparatives, equity movements and the notes or cash-flow components required by the selected profile. It must preserve the profile/version behind an issued result. Missing mappings or incomplete records must be explicit; a blank or zero must not conceal missing data.

The current starter accounts and summaries do not implement a complete Pakistan, UK or UAE framework. [[The first package|First-Package]] defines the next acceptance gates.

## Past data and reconciliation

Historical import is planned as **upload → match columns → preview → correct → reconcile totals → confirm**. The first accounting release is intended to cover account/contact lists, opening balances and unpaid documents through CSV/XLSX templates. Detailed historical journals and document/payment relationships follow as a separate migration milestone.

A chosen cutover date must keep opening AR/AP balances consistent with unpaid invoices and bills without counting them twice. Bank matching and reconciliation will have their own review and confirmation workflow. These import, AR/AP and bank-reconciliation capabilities are not available in the current preview.

[[Countries and currencies|Countries-and-Currencies]] · [[Architecture]] · [[Roadmap]]
