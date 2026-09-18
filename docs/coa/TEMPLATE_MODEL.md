# Proposed template and wizard model

Status: research design for review, **not a runtime interface or implemented schema**. Read [the research overview](README.md) and [open decisions](DISCOVERY_GAPS.md) before implementation.

## Current boundary

The inspected foundation creates six generic accounts in `pl_create_company()`. `pl_accounts` currently has a company/book scope, code/name fields, five root types, and an active flag; it does not contain a template catalog, semantic keys, control roles, normal balances, or localization provenance. Those additions require an explicit subsequent migration and tests. This research changes none of that code.

## Composition and identity

Compose **one versioned neutral core + one primary industry profile + zero or one country package + explicitly reviewed optional capabilities**. A company choosing several business activities can add reviewed capability subsets later; do not union whole industry charts blindly. A preview resolves the selected composition before saving.

| Concept | Proposed meaning |
|---|---|
| Template identity | Stable package ID and immutable version; layer, status, compatibility range, source IDs, reviewer/date, and provenance |
| Semantic account key | Stable purpose such as `core.receivables.trade` or `in.gst.cgst.input`; independent of account number, translated name, and database ID |
| Root type | Existing `asset`, `liability`, `equity`, `income`, `expense`; keep this enum compatible |
| Reporting group | Current/non-current, contra classification, direct costs, overheads, etc.; do not encode these into the root enum |
| Normal balance | Debit/credit metadata, including contra accounts; not a prohibition on valid opposite-side transactions |
| Functional role | Receivables control, payables control, bank, stock, tax control, migration clearing, etc.; controls allowed workflows |
| Account code/name | User-visible defaults that can be edited before installation; unique code per book; no claim of mandated national codes |
| Tax mapping | Separate reviewed jurisdiction/registration/tax-code mapping to account roles, with effective dates; no tax rates or return logic inside chart labels |
| Company instance | Company/book-scoped account IDs plus an installed template snapshot and role mappings; user changes do not rewrite the source catalog |

Role cardinality is role-specific: many named banks or stock accounts can share a role, while a workflow needs one explicitly resolved default in its relevant company/registration scope. Customer/supplier detail belongs in subledgers, not one new GL account per contact. The source-system chart may legitimately have several AR/AP controls; migration must map and reconcile each supported control rather than collapse them by name.

The composer deduplicates the same semantic key only when type, role, and meaning agree. Different keys with the same number require a visible renumbering choice. Conflicting roles/types, cycles, missing dependencies, overlapping country packages, and silently reclassifying a core account are errors. Country labels may override display text only through an explicit package rule. A PK package cannot be combined with an IN package; cross-border registrations need a future reviewed model, not stacked national defaults.

## Wizard journey

1. **About the business:** business name, country/region (or neutral setup), base currency, legal form, accounting start/fiscal year; no assumed accounting framework from currency or browser location.
2. **What the business does:** choose the primary business profile. Ask only capability questions that affect accounts: stock held, invoices on credit, supplier bills, advance payments, manufacturing, payroll. Label operational capabilities unavailable until their modules exist.
3. **Regional needs:** when a reviewed package exists, choose the relevant registration/jurisdiction and reporting profile. Users who do not know their registration status can save progress; do not treat uncertainty as unregistered or enable tax posting.
4. **Recommended accounts:** plain-language grouped preview, account count, why each account exists, optional-account toggles, and expandable accountant details. Offer neutral setup and bring-your-own-chart. Show package version and review status in details.
5. **Start fresh or bring past data:** choose cutover date and upload chart/contacts/balances/unpaid documents through preview and matching. Show unresolved mappings and reconciliations; preserve progress without posting.
6. **Confirm setup:** display the exact company/book, installed package versions, required role assignments, and remaining migration work. Explicit confirmation saves the chart atomically; it does not confirm tax filings or unpaid-document imports.

Only authorized company owners/accountants may apply or change mappings. The server revalidates the exact preview against a version/hash and the current company state; stale previews require regeneration. Browser actions use the existing authentication, membership, CSRF, and shared database helpers. The installer reads bundled approved data, with **no provider calls on page load**.

The catalog's release state is proposed as research → accountant reviewed → validated → released, with deprecated versions retained for provenance. A preview can expose research to reviewers; a real company's installer can load only released compatible packages. Selection of a country is never a badge of statutory compliance.

## Installation and upgrades

Pin the resolved template versions and digest to an immutable installer snapshot containing selected options and role-to-account mappings. Applying the same request twice returns the same setup result; changed content under the same request identity is a conflict. A failed apply must leave no partial accounts or role bindings.

Application updates do not replace charts in existing companies, rename their accounts, change account types, alter posted journals, or repoint tax controls. A future template upgrade is a separate reviewed diff: additions, deprecated suggestions, mapping changes, and reasons. Existing history keeps original account IDs and source versions. Posted-account reclassification needs accounting review; deactivation must first replace dependent defaults and preserve reporting.

Before the first posting, changing the selected template still requires a fresh preview and conflict check. After posting, disallow wholesale template switching; use explicit reviewed account additions/mapping changes. Preserve the ability to export the installed chart and mappings.

## Opening balances and unpaid documents

Proposed first-import method: **unpaid documents supply AR/AP opening balances; the remaining opening trial balance excludes those same control lines**. The input trial balance is retained as the comparison authority, with explicit per-control reconciliation. Cutover date, currency, source account codes, source document IDs, original dates/due dates, outstanding amounts, and counterparties remain traceable. This is an opening transfer, not re-posting old sales, purchases, or tax.

Worked synthetic example, with debit-positive/credit-negative source balances:

| Source cutover trial balance | Debit | Credit |
|---|---:|---:|
| Bank | 500.00 | 0.00 |
| Customer receivables | 1,000.00 | 0.00 |
| Supplier payables | 0.00 | 600.00 |
| Opening equity/retained balance | 0.00 | 900.00 |
| Total | 1,500.00 | 1,500.00 |

- Import customer outstanding documents of 600.00 and 400.00: debit AR 1,000.00; credit migration clearing 1,000.00.
- Import supplier outstanding document of 600.00: debit migration clearing 600.00; credit AP 600.00.
- Import remaining trial-balance lines: debit bank 500.00 and migration clearing 400.00; credit opening equity 900.00.
- Result: bank 500.00, AR 1,000.00, AP 600.00 credit, equity 900.00 credit, **clearing zero**. The AR/AP lines from the source trial balance are compared, not posted a second time.

All amounts go through the central posting interface as opening-transfer sources with duplicate protection and preserved origin references. Staging may save uploaded data, but confirmation must publish an atomic reconciled opening set, or use a designed recoverable batch mechanism before any larger-volume implementation. A clearing balance is not a default plug for an unexplained difference.

Partial payments before cutover are reflected in each document's remaining amount. Customer advances, supplier advances, credit notes, withholding, disputed/negative balances, and multiple controls require explicit mapping and separate tests. Do not recreate original revenue/tax or import both gross invoice value and pre-cutover payments unless using a later complete-history method. If only summary AR/AP exists without party detail, do not invent unpaid invoices or mark customer/vendor balances ready.

Amounts are never silently guessed; ambiguous labels, duplicate source codes/documents, incompatible types, unsupported currency/precision, or missing required roles block confirmation. A deliberate new-account action must specify its classification and role and be included in a fresh preview. Existing-account matches do not change account types to make an import fit.

## Required implementation acceptance cases

- Seven profiles compose deterministically with the neutral core; duplicate keys/codes and incompatible country selections fail visibly.
- Only released versions can populate a real company; template source edits cannot change an installed snapshot.
- Renamed/renumbered accounts preserve roles and report drill-down; group accounts cannot receive postings if hierarchy is implemented.
- Company isolation, unauthorized apply attempts, stale preview, repeated apply, concurrency, and mid-transaction failure behave correctly.
- The worked opening example matches source totals; repeated imports do not duplicate balances; mismatches/advances/credit notes require explicit resolution.
- A software upgrade leaves existing companies, posted entries, user labels, and mappings unchanged.
- Usability sessions test simple setup, accountant review, keeping an existing chart, and correcting an import mismatch; technical tests cannot substitute for those sessions.
