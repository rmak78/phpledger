# Product workflow research for directions 4–6

Research date: 2026-09-14. Trigger: the user rejected concepts 1–3 as looking AI-generated and asked to examine QuickBooks, Odoo, and Salesforce before generating options 4–6. This is public primary-source research, not a signed-in product audit or user test. No accounts were created, records changed, proprietary code copied, or messages sent.

## Evidence from official sources

| Product/source | Documented behavior | Public visual reference and limits |
|---|---|---|
| [QuickBooks setup guide](https://quickbooks.intuit.com/learn-support/en-us/quickbooks-online/get-started-quickbooks-set-up/) | Setup starts with business details, opening records, customer/vendor lists, and open invoices/bills. Account configuration and transaction review connect to useful work. | Official tutorial media; the page warns that some videos can differ from the updated application. Do not use an old video frame as proof of the current shell. |
| [QuickBooks chart of accounts](https://quickbooks.intuit.com/learn-support/en-us/help-article/chart-accounts/learn-chart-accounts-quickbooks-online/L2yc6KBob_US_en_US) | The account list exposes names/types/balances and controls for displayed columns and batch changes. Accounts lead to transaction history. | Official help content and tutorial media; current article reviewed 2026-09-14. |
| [QuickBooks transaction matching](https://quickbooks.intuit.com/learn-support/en-us/help-article/bank-feeds/match-online-bank-transactions-quickbooks-online/L6qyw0PvP_US_en_US) | Review a bank row, expand its information, inspect an existing-record match, and confirm. Matching and creating a new categorized record are distinct actions to avoid duplicates. | Official US help updated 2026-08-14. It uses both Pending and For review terminology; labels vary across editions/rollouts. |
| [Odoo 19 bank reconciliation](https://www.odoo.com/documentation/19.0/applications/finance/accounting/bank/reconciliation.html) | Bank matching lists date, label, partner, actions, and amount. A completed match replaces the suggested action with the linked counterpart; rows expand for detailed work. | Official documentation includes dashboard and bank-matching screenshots. Search-index content was readable; direct web fetch timed out. Screenshot inspection is recorded separately by the main task. |
| [Odoo 18 accounting setup](https://www.odoo.com/documentation/18.0/applications/finance/accounting/get_started.html) | A dismissible onboarding banner leads to accounting periods, bank setup, taxes, and accounts/opening balances; settings stay accessible later. | Official but explicitly older-version setup reference. Odoo 19 setup direct fetch timed out, so this is not evidence of exact current setup styling. |
| [Odoo 19 search/filter/group](https://www.odoo.com/documentation/19.0/fr/applications/essentials/search.html) | View-level search, Filters, Group By, and Favorites refine the displayed records; grouping changes organization without removing records. | Official French documentation, with search/group screenshots; indexed text read. Use the pattern, not an assumed English pixel layout. |
| [Salesforce Lightning list views](https://trailhead.salesforce.com/content/learn/modules/lightning-experience-for-salesforce-classic-users/work-with-list-views) | Saved/pinned views, configurable columns, an adjacent filter pane, row actions, editable-versus-locked fields, and task list/detail split view support repeated work. | Public official annotated screenshots of list controls, filters, rows, and editing. Full article read. Split-view description here concerns tasks; do not claim every object has the same behavior. |
| [Salesforce records and global search](https://trailhead.salesforce.com/content/learn/modules/lex_implementation_basics/lex_implementation_basics_explore) | Record highlights and related information retain context; a search box at the top of pages provides recent items and typed record results. | Public official record, workspace, and search screenshots. Full article read. **Salesforce is a CRM workflow comparator, not an accounting model.** |

These sources establish workflow patterns, not proof that every product is equally usable or that copying its appearance will meet PHP Ledger's targets. Public product screenshots can illustrate structure; they do not establish authenticated behavior, timings, accessibility conformance, or availability in every plan.

## Pakistan-market playlist pass

On 2026-09-16 the supplied YouTube playlist was fetched directly enough to
confirm the title **Setting up HysabOne** and its HysabOne association. The
playlist page itself was intermittently throttled while loading its complete
video list, so this record does not claim a video-by-video review or infer
screens that could not be inspected. The accessible HysabOne product material
was used only to cross-check the observable workflow themes: guided business
setup, chart/opening-balance preparation, daily activity, accounting and
inventory modules, party balances, ageing, and a product-led support/onboarding
path. See the [HysabOne product overview](https://hysabone.com/),
[features](https://hysabone.com/features/) and [Pakistan-market ERP overview](https://hysabone.com/erp-software-pakistan/).

The design inference for PHP Ledger is deliberately narrower: make setup a
short decision sequence with a visible opening-data consequence; give a daily
work surface priority over a generic dashboard; let accounting, purchasing,
inventory and reports remain discoverable through one scalable workspace; and
keep receivable/payable status close to the source document. HysabOne's claims,
pricing, compliance statements and feature list are not treated as PHP Ledger
requirements or independent accounting evidence.

## Visual references inspected on 2026-09-14

The main task subsequently opened the official pages in the browser and inspected these actual product visuals. Local screenshots are research inputs in ignored `.cache/design-references/`, not distributable application assets.

| Captured reference | Source and inspection | Applied pattern |
|---|---|---|
| `quickbooks-expense-demo.png` | [QuickBooks public expense walkthrough](https://quickbooks.intuit.com/product-brochure/expense/), reached through its [expense product page](https://quickbooks.intuit.com/accounting/track-expenses/). Advanced the public guided demonstration to step 3 of 6; no signed-in account or business record was used. The image includes the tutorial overlay. | Payee/payment account/date/reference fields, line categories and amounts, total, attachment area, and persistent save controls. PHP Ledger keeps its own explicit draft/posting semantics. |
| `odoo-bank-matching.png` | The Odoo 19 documentation loaded in the browser despite the earlier web-fetch timeout. Inspected its published [bank matching screenshot](https://www.odoo.com/documentation/19.0/_images/user-interface.png). | Narrow toolbar, filters in context, compact dated rows, aligned amounts, actions beside unresolved records. |
| `salesforce-list-filter.png` | Inspected the official Trailhead list-view article's image labeled “Filter panel open next to the All Open Leads list view.” | Saved list selector, small search and view controls, table rows, adjacent contextual panel. The tutorial screenshot contains older sample dates; it is a documented pattern, not a verified current production shell. |

The QuickBooks transaction-matching article was also read directly in the browser, confirming the distinction between matching an existing record and creating a new categorized record. Public guided demos are illustrations, not end-to-end product tests. The three screenshots are supplied to ImageGen as references for the original PHP Ledger concepts; competitor logos, records, and proprietary UI assets are not implementation inputs.

## PHP Ledger recommendations — design inference

The following are design recommendations inferred from the evidence and the user's goals, not measured competitor specifications:

- **Make the worklist the main surface.** Use roughly 70–80% of the workspace for rows, a form, or the selected transaction. Show date range, count, filters, amount columns, and specific actions before adding charts. Keep company/book context visible.
- **Use a repeatable record grammar.** Header identifies the document/reference and status; fields capture business facts; line items and totals sit together; source journal and history are reachable. Draft editing, posting, and reversal remain distinct. Posted financial rows must never inherit CRM-style inline editing.
- **Make review states actionable.** A pending count opens the pending list; a warning names the affected field/row; a duplicate shows its existing source; an accepted match becomes a linked record. Suggested categorization/matching remains unposted until authorized confirmation in PHP Ledger.
- **Separate list search from global search.** A local filter narrows the current table. A global search identifies record types and company scope. On narrow screens, record detail becomes a deliberate destination with a clear return to the filtered list.
- **Provide setup assistance in context.** A compact progress strip/checklist links to the missing requirement, including cutover/opening balances. Do not make completed users pass through a welcome dashboard repeatedly. Keep skipped required setup visible until resolved.
- **Prefer restrained practical styling.** Start around 14px body text, 36–42px desktop table rows, 2–4px control radii, light separators, aligned monetary values, and a single restrained accent; validate these values with actual use. Touch layouts need larger targets. Avoid gradient heroes, pill-heavy status decoration, oversized greetings, meaningless metric cards, and sparkle/assistant imagery.

## Briefs for the next three concepts

| Number | Working structure | Comparative purpose |
|---|---|---|
| 4 | Transaction workbench: compact left navigation, account/date/filter toolbar, pending/posted tabs, dense rows, one expanded transaction and explicit review action | Test whether owners understand and complete daily bookkeeping with minimal navigation; QuickBooks workflow influence |
| 5 | Accounting records: restrained application bar, predictable list controls, selected document form, clear draft/posted state, line items and linked journal | Test coherence between efficient record entry and traceable accounting; Odoo workflow influence |
| 6 | Review console: company context and global search, saved worklist, selected-record details alongside, related entries/history | Test whether accountants can review multiple records without losing their place; Salesforce navigation influence |

Use the same sample business, dates, amounts, and task across the three options. Label sample data, keep totals internally consistent, and retain option numbers 4/5/6. Use PHP Ledger's own identity without competitor logos, proprietary visual assets in the implementation, or implied integrations. Exact names and generation prompts are maintained with the final image set.

After selection, build the task in PHP/HTML/CSS and test keyboard entry, validation recovery, responsive behavior, and timed unassisted completion. Image plausibility is only an intermediate design check; a generated screenshot cannot prove a workflow works.
