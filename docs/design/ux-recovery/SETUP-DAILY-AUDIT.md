# Setup, daily bookkeeping and sample exploration audit

Status: current-run evidence; design recommendations pending acceptance. Captured locally on 16 September 2026 (Asia/Karachi), using the released 0.4.0-preview source, isolated synthetic companies and user-approved isolated Playwright. Screenshots are under `evidence/` after review; capture originals remain under ignored `output/playwright/ux-audit/`.

Screens 01–28 have desktop (1440), tablet (768) and mobile (390) captures at a 1000px viewport height. Full-page capture heights vary. Screenshot inspection does not establish WCAG conformance. The additional keyboard check is recorded separately.

## 01. Login — generally clear

Task: sign in to an existing installation. Context: anonymous, no company selected. Evidence: `01-login-{1440,768,390}.png`, inspected at all three widths.

- Strength: explicit email/password labels, one primary action, and a concrete explanation of administrator-provided access. Brand and form fit all widths.
- Friction: the decorative introduction consumes roughly 210px before the form on mobile/tablet. A returning user must pass the same marketing heading each visit.
- Accessibility risk: the smaller muted help/footer text needs measured contrast/zoom checks; screenshots cannot confirm input autocomplete or screen-reader error announcements.
- Responsive behavior: form remains readable with full-width fields; no visible clipping. The oversized introduction is a priority issue, not a broken layout.
- Direction: shorten the introduction on small screens and keep the form above it in reading order. Preserve the clear single-action pattern.
- Evidence limit: this capture is the initial form, not authentication success or validation recovery.

## 02. Company selection — clear identity, excessive space

Task: open the correct company. Evidence: `02-companies-{1440,768,390}.png`, all inspected. Sample/ordinary labels, currency, ownership and opening-readiness warning are clear. Each card repeats a large amount of padding; three companies require 1654px on mobile, and the staggered desktop cards reduce scan speed. Keep the identity labels, use compact aligned rows, add a separate Explore samples entry and expose last-used company without implying account readiness. No clipping or page overflow observed. Screen-reader grouping and large-company-list navigation remain untested.
## 03. Onboarding — needs workflow redesign

Task: define a business before creation. Evidence: `03-onboarding-{1440,768,390}.png`, all inspected. Preview-before-create, explicit zero-balance confirmation and a visible template version are useful safeguards. However, the regional detection panel precedes business details, the six-account neutral template is the sole automatic path, and sample creation competes with ordinary setup in the same radio group. Mobile page height is 3025px. Currency labels combine countries with currencies, and the statement that currency conversion is not included is stale relative to current posting foundations. Plain help is useful but repeated limitations overwhelm the next action. Replace this with the proposed six-step progressive flow and separate sample chooser; do not infer compliance from currency. This requires catalog/backend contracts, not CSS alone. No page overflow or JS exception was measured.
## 04. Setup validation — useful recovery, distant correction

Evidence: `04-setup-validation-{1440,768,390}.png`, all inspected. Submitting a named business without the required zero-balance confirmation returned HTTP422, focused the alert, and retained the entered name. These are observed strengths. The summary explains the fix but offers no direct link to the unchecked field far below the regional panel; mobile height grows to3218px. Add a linked field error and keep conditional guidance beside the relevant choice. Screen-reader announcement was not independently tested.
## 05. Setup preview — clear confirmation, incomplete future contract

Evidence: `05-setup-preview-{1440,768,390}.png`, all inspected. Correcting the checkbox reaches a separate review screen without creating a company. It clearly shows business, currency, dates, six accounts and separate Back/Create actions. The long table repeats step1, while required-role mappings, country/industry eligibility and import work are absent. Those are future contract features, not a claim of present malfunction. A compact purpose-grouped chart plus expandable accountant detail would shorten review. Mobile remains readable; creation and duplicate-confirmation were not executed in this visual audit.
**05 responsive defect:** although page scroll width equals viewport width, the primary Create button extends beyond the left edge at390px; its leading text is clipped. Whole-page overflow checks missed this. Acceptance must measure actionable element bounds and inspect screenshots, not only document width.

## 06. Existing setup review — focused warning, noisy shell

Evidence: `06-setup-review-{1440,768,390}.png`, all inspected. The opening-required company is explicitly identified and the single reconcile-opening action is clear. Multiple global navigation, company context and module-link rows precede that action; mobile hides much of the navigation behind horizontal scroll. The actual opening task is short. Collapse navigation into one consistent menu and keep company identity with the heading. Warning text is understandable; keyboard order and account permissions need separate verification.
## 07. Opening balances — sound boundary, expert input

Evidence: `07-opening-balances-{1440,768,390}.png`, all inspected. The page names one balanced starting point and separates preview from confirmation. CSV text areas expose exact account/date/document requirements, but demand accounting-format knowledge from an owner and show multiple lengthy instructions before entry. Mobile requires substantial scrolling; the page fits without whole-page overflow, but textarea horizontal content and table usability need keyboard testing. Future flow should offer upload/mapping/row errors and a clear AR/AP reconciliation difference, while retaining the existing no-double-posting contract. No cutover was confirmed.
**07 visual detail:** the initial screen uses account/debit/credit inputs; CSV and unpaid-document entry are collapsed disclosures. On mobile the numeric inputs become very narrow. The initial capture establishes discovery of those disclosures, not their expanded contents.

## 08. Opening conversion, before cutover — valid empty state, unclear prerequisite

Evidence: `08-opening-conversion-{1440,768,390}.png`, all inspected. The screen says no opening debts have been converted, inside a plain empty-state panel. It repeats the global opening warning and adds another starter-navigation strip. An owner has to infer that Opening balances comes before this page; the advice that new businesses can start ordinary flows is unhelpful for this opening-required company. Replace that generic advice with the actual prerequisite and direct action, then show mappings/reconciliation only when data exists. Mobile text wraps without page overflow. No conversion was executed. Populated conversion remains an interaction-test gap in this run.
## 09. Transactions — useful review console, competing navigation

Evidence: `09-transactions-{1440,768,390}.png`, all inspected. Desktop draft list and selected expense show status, amount and posting preview, with edit/post actions. Four stacked areas (global navigation, company, sample guide, administration links) precede the task. Tablet/mobile hide the adjacent detail; row links become the detail entry. Both a server filter and a table Search appear, followed by repeated counts and totals; the search icon overlaps its placeholder. The mobile date/reference column wraps one identifier across several lines and the status column is offscreen. Preserve list/detail with a compact shell, one filter model and explicit mobile Back to list. Keyboard row selection and retained-filter behavior require interaction checks.

## 10. New receipt/expense — clear draft action, oversized guidance

Evidence: `10-expense-new-{1440,768,390}.png`, all inspected. Money in/out, date, amount, bank and category are plainly labeled; Save draft accurately describes the next action. A large guidance panel repeats the three-step flow and squeezes tablet fields, truncating select labels. On mobile that panel follows the form, extending the page to2387px. Put optional reference/memo and one-line posting guidance behind progressive disclosure; keep the primary form full-width on tablet. The statement about tax and FX being planned must be explicitly scoped to this simple editor, since other documents already support them. No save was executed on this blank form.
## 11. Existing expense edit — potential accounting-choice loss

Evidence: `11-expense-edit-{1440,768,390}.png`, all inspected. Amount, counterparty, reference and memo are retained and draft identity remains visible. However, detail09 shows Petty cash as the saved paying account, while editor11 offers only Cash and bank. Saving may substitute the saved choice; this is a high-priority source/fixture investigation, not a cosmetic issue. No save was performed. The layout repeats10, including the oversized guidance panel and narrow tablet fields. Preserve every valid saved account in edit choices and block an unavailable mapping visibly instead of selecting a different default.
## 12. Posted transaction detail — traceable, misleading list context

Evidence: `12-transaction-detail-{1440,768,390}.png`, all inspected. Posted status, amount, balanced journal and report/source links are explicit. Tablet/mobile correctly present a standalone detail with Back to list. Desktop still shows the Drafts list next to the posted receipt reached by a direct link; no matching row is visible. Journal/History tabs coexist with an always-visible journal and a second History disclosure. Keep one detail hierarchy and reconcile list filter/selection context. Reversal is deliberately secondary, but two reversal affordances duplicate the same task. This historical entry was not reversed.
## 13. Posted journal — useful traceability, narrow amount review

Evidence: `13-journal-detail-{1440,768,390}.png`, all inspected. Source transaction and account links make the accounting chain discoverable. Business date and posted-at time are distinct. The mobile table squeezes account/description into tall wrapped columns while credit remains partially offscreen. Preserve description in expandable line detail and keep account plus debit/credit readable together; provide labeled horizontal scrolling where unavoidable. Source reference document:563 is technical context suitable for an accountant expansion. This screen highlights Reports globally despite being a journal, a navigation consistency issue.
## 14. General journals — scannable desktop, mobile values hidden

Evidence: `14-general-journals-{1440,768,390}.png`, all inspected. Desktop shows date, description, status, debit, credit and balance, and the reversed example is labeled. Twenty-five long rows make a tall page; tablet loses the final balance column and mobile initially shows only identifier/description. A mobile row summary must include amount and state before opening detail. Date/status filters would be more useful than requiring free-text search across mixed history. Keep dense desktop review, pagination and source identity; verify keyboard scrolling and table announcements separately.
## 15. New general journal — strong responsive entry pattern

Evidence: `15-journal-new-{1440,768,390}.png`, all inspected. Desktop lines become labeled cards on tablet/mobile; debit and credit remain visible together. Add/remove controls and draft-only saving are clear. This is a useful existing pattern for other line editors. The shell and long instructions still delay entry. Repeated labels are appropriate on mobile, while optional descriptions can be collapsed. Removing a line must preserve focus and totals; this is not proven by screenshots. The save/review boundary is explicit and correctly separate from opening import.
## 16. Saved journal review — accurate success, mobile amount visibility fails

Evidence: `16-journal-draft-success-{1440,768,390}.png`, all inspected. A synthetic10.00 transfer draft was saved through the UI; the notice states that review/posting remains. Desktop/tablet show exact equal totals and revision1. Mobile review initially hides BOTH amount columns behind a horizontal table, while Post journal remains prominent. Entry cards in15 are stronger than this review table. Require both totals and line amounts visible before the action at narrow widths. This draft was not posted. Success has a role=status notice in the observed DOM; actual screen-reader announcement remains untested.
## 17. General journal edit — values retained

Evidence: `17-journal-edit-{1440,768,390}.png`, all inspected. Reopening draft706 retains both cash accounts and10.0000 line values, with equal10.00 totals and draft identity. Mobile cards keep amounts readable, unlike review16. Layout suggestions match15. Four-place editing versus two-place display needs a deliberate precision policy for non-cent values, not casual rounding. No second save or posting occurred.
## 18. Reversed journal — history preserved and discoverable

Evidence: `18-reversed-journal-{1440,768,390}.png`, all inspected. The original25.00 entry remains with Reversed status and a linked reversing journal. Posting/edit controls are absent. Mobile shares16's hidden amount columns. The description explains the teaching correction, but a concise dated correction timeline would make original/reversal/replacement easier to follow. The existing pack uses its historical fixture structure; this capture does not prove a new same-document correction workflow. No new reversal was submitted.
## 19. POS cart — clear task, context consumes first screen

Evidence: `19-pos-{1440,768,390}.png`, all inspected. Product buttons, prices, category search, empty-cart guidance and disabled Review action establish a clear sequence. Desktop/tablet keep cart adjacent; mobile places it below products with a fixed View cart shortcut. That shortcut overlaps content in the viewport and needs focus/scroll-obscuring tests. Much of the first screen is shell/progress/context. The catalog is explicitly a sample; its showcase stock/tax limitations must stay visible without implying shared inventory integration. Preserve this task-focused pattern with a shorter shell.
## 20. POS cash review — clear amounts and boundary

Evidence: `20-pos-review-{1440,768,390}.png`, all inspected. Adding one notebook and choosing Review produces4.50 due, a separate cash field and an explicit no-provider-payment statement. Record remains disabled until sufficient cash. Mobile stacks items then cash and keeps all values readable, though the repeated total and whitespace lengthen the task. Preserve the explicit amount-due/cash/change sequence, support a compact summary and test focus after Edit cart. This is a synthetic local showcase sale.
## 21. POS receipt — successful local flow, clipped print action

Evidence: `21-pos-receipt-{1440,768,390}.png`, all inspected. Entering5.00 showed0.50 change; recording saved synthetic receipt591 with a balanced journal. Receipt totals, source/journal links and stock/tax limitations are explicit. On mobile the Print receipt button is partly outside the LEFT edge, another negative-overflow defect that document-width checks miss. Source/journal links need wrapping as a vertical action group. Printing itself was not invoked; no real payment was taken. The local fixture now includes this4.50 practice sale.
## 22. Historical sample guide — strong teaching links, too much at once

Evidence: `22-sample-guide-{1440,768,390}.png`, all inspected. The guide names closed2024/2025 and open2026, gives specific amounts to trace, and links source records, monthly statements, reversals and practice drafts. It clearly distinguishes manual support schedules from implemented operational modules. Three teaching tasks plus lengthy schedules produce4511px on mobile. Turn this into a short Choose an exercise list with expandable evidence and progress retained in the sample session. Keep pack/version and synthetic identity, and explain that original checkpoints differ after practice entries. Public hourly-expiry behavior is not present on this ordinary local sample route.
## 23. Help — clear instructions, stale feature inventory

Evidence: `23-help-{1440,768,390}.png`, all inspected. Numbered receipt/expense instructions and explicit permissions are useful. However, What this preview includes still calls invoice collection, bill settlement and inventory future work, conflicting with0.4.0. The correction text also needs current service-specific boundaries. Help drops the active company shell, forcing a return through businesses rather than the originating task. The3993px mobile page needs a task index and contextual return link. Update claims from release evidence, with separate simple-editor/POS limitations. No help links to external services were followed.
## 24. Missing route — clear basic recovery

Evidence: `24-not-found-{1440,768,390}.png`, all inspected. HTTP404 shows a short title and Return link, without stack traces. It fits all widths. It drops company context and offers no way back to the exact prior task; a safe contextual return would help. Keyboard focus on initial error navigation and screen-reader page-title usefulness remain untested.
## 25. Wrong-method error — safe but technical

Evidence: `25-wrong-method-{1440,768,390}.png`, all inspected. Visiting the POST-only save route with GET returns405 and a simple recovery link; no write occurs. Text fits each width but 'different request' is implementation language. Explain that the action must be started from the form, with a safe return to it where possible. This deliberately invalid navigation is an error-state check, not an ordinary workflow route.
## 26. Expired local public-demo generation — honest block, wrong recovery advice

Evidence: `26-public-demo-{1440,768,390}.png`, all inspected. After the local generation expired at00:00UTC, HTTP503 correctly prevented further work and focused the refresh-due notice. The generic company-permissions paragraph is irrelevant to a timed refresh, and mobile clips Return to your businesses off the left edge. Replace this with refresh status, a safe retry and explicit draft/session consequences. This was the isolated local demo, not evidence of a production outage. The local harness has no scheduler, so the approved guarded local reset is needed to continue capture.
## 27. Public sample chooser — clear isolation, delayed entry

Evidence: `27-demo-chooser-{1440,768,390}.png`, all inspected after the guarded local-only reset. Countdown, per-visitor isolation, no-registration and fictional-data notices are explicit. The five choices include the new starter and four historical packs. The marketing headline and country-detection panel push Start below the first mobile screen; country/currency labels still merge two concepts. Put company choice and currency first, show concrete exercise previews, and keep the expiry notice. No regional chart/compliance is implied by the help text. Only the isolated local schema was refreshed; production scheduling was untouched.

## 28. Accounting starter guide — useful linked exercises

Evidence: `28-starter-guide-{1440,768,390}.png`, all inspected. The guide names the zero-balance starting point, manual synthetic tax example, expected partial-payment balances and three-step purchase/sale/reconciliation exercise. The purchase action and source links fit every width. It correctly distinguishes this empty starter from the four historical companies. The mobile page remains long and repeats the sample-guide link above its own guide; replace the introductory stack with compact persistent identity and one exercise at a time. This local sample was created through Start my sample. These captures do not prove completion of the exercises; the release has a separate end-to-end receipt.

## 29. Keyboard spot check — skip link works

Evidence: `29-keyboard-skip-390.png`, inspected. Starting from the fresh sample-guide page, the first Tab visibly focused Skip to content with a strong outline. Enter moved actual focus to `MAIN#main` and the URL fragment became `#main`. This is a successful narrow keyboard check, not evidence that all controls, dialogs or financial workflows are keyboard accessible. Screen-reader use, browser zoom, contrast measurement and reduced-motion checks remain separate gates.
