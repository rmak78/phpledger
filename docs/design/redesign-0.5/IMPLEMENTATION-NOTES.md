# 0.6.0-preview implementation record

Owner approval: 17 September 2026. The conversation and
`docs/strategy/CODEX-PROMPT-UI-REDESIGN-0.6.md` supersede this prototype's older
"proposed" labels. All fourteen improvements, DataTables removal, and publication
after verification are approved. Mobile refinement is deferred; overflow safety is not.

## Plan and acceptance gates

1. Commit the approved prototype sources, excluding generated output, dependencies,
   and prototype fonts. Port tokens, compiled Tailwind, shell and shared UI helpers.
2. Implement the server-side list contract, retain `/tables`, and rebuild access/setup,
   daily work/POS, sales/purchasing/inventory, then reporting/administration.
3. Complete each approved improvement with targeted tests. Preserve the central
   posting service, immutable history, period locks, scoped permissions and CSP.
   Multi-item settlements reject an unallocated remainder explicitly; no on-account
   balance capability is introduced. Continue migrations at 029 if needed.
4. Require composer checks, real fresh/0.5-upgrade database verification, route/state
   captures at both desktop folds, representative portrait checks, accessibility
   evidence, audit closure mapping, list performance and package-content verification.
5. Only after all gates pass: version, package/checksum/media kit, branch/PR,
   GitHub release, backed-up isolated hosted demo and website update, live receipts.

## Baseline

- Branch `codex/ui-redesign-0.6` created from `master` at `33b53dc`, after fetching
  and fast-forwarding the stale local master reference to origin/master.
- Previous branch `codex/regional-catalog-ux-review` is preserved at `238574c`.
  The unrelated untracked Claude handoff file is preserved.
- An empty stale Git index lock (17 Sep 01:17 local; no running git process) was
  preserved as `.git/index.lock.stale-20260917-1717` before switching branches.
- Prototype Home visually inspected in an isolated Playwright session, 1366x768.
  Prototype evidence does not establish PHP implementation acceptance.
- Existing 0.5 settlement code already permits omitted FX accounts when there is
  no realised difference. Verify and retain that behavior rather than replacing it.

## Status

Implementation in progress. No release, production change, migration, or completed
PHP screen rebuild is claimed by this record. Verification receipts will be added
as checks actually complete. Publication is blocked until all required gates pass.

## Foundation checkpoint, 17 September 2026

- Tailwind 4.3.3 development build and committed compiled CSS; existing styles
  temporarily folded into a development source compatibility layer. The final
  screen rebuild must remove that dependency before interface acceptance.
- Shared sidebar/company switcher, 48px topbar, quick create, real-navigation
  command palette, tablet rail, small-screen drawer and focused login ported.
  Companies uses compact rows with the existing CSRF-protected selection POST.
  Home and the complete screen inventory remain outstanding.
- DataTables browser dependency removed. Ordinary GET controls added for
  transactions, journals, account statements and statement rows. `/tables` retained.
  Journal status filtering added to the existing scoped source-list service.
- Existing reversal test corrected to use its original posting date: the old
  16 September cancellation date became an invalid backdate on 17 September.
  The production reversal policy was not changed.
- `composer check`: 176 PHP files linted; PHPStan no errors; sample validation and
  eight invalid-pack checks passed; 241 tests, zero failures. Targeted foundations:
  76 tests passed. Targeted lists: 19 tests passed.
- Isolated synthetic browser fixture on localhost:18219. Shell captures at
  1366x768, 1024x768, 768x1024 and 390x844: no page-width overflow or console errors.
  Command palette filtering and Escape; drawer open/Escape/focus restoration checked.
  This is a foundation smoke check, not the required complete route/state evidence.
- Prototype builder now resolves Windows paths and can use the packaged Inter
  font; screenshot script selects installed Chrome (or an explicit executable)
  and fails its process when fold/overflow checks fail.

Still open: full prototype-to-PHP rebuild, remaining approved improvements,
all-route/state captures, keyboard/contrast review by pattern, 5,000-row indexed
query-plan evidence, fresh install and 0.5 upgrade, package content acceptance,
release documentation/media, publication and live receipts. No migration or live
system change has been made.

## Access/setup checkpoint, 17 September 2026

- Ported the six-step onboarding frame, sample chooser, setup-review account
  mappings, OAuth consent, Help task cards, and error-page presentation. Opening
  balances now uses a bounded account table and sticky preview action; opening
  conversion uses mapping and control-reconciliation tables. These are partial
  lane changes, not acceptance of every access/setup state.
- Fixed native select text clipping caused by inherited legacy block padding.
  Isolated shell-template variables from page data after browser testing exposed
  a navigation loop overwriting the opening-conversion item list. A rendering
  regression test protects the boundary.
- Full composer check after helper integration: 176 PHP files, PHPStan clean,
  sample validation clean, 241 tests passed. Subsequent error/rendering changes:
  178 PHP files lint clean and all four shell tests passed. The next full check
  must include the added regression test and error-page adapter.
- Local synthetic browser checks at both desktop folds, with JavaScript on and
  off: setup review, opening balances and zero preview, opening-document mappings
  and reconciled preview, Help, 404 and 405. No horizontal overflow; marked actions
  within the viewport. Login, businesses, chooser and onboarding through preview
  also captured at both folds with no console errors. Confirmation was not
  submitted in these browser passes; service tests retain the accounting checks.
- `tests/redesign_browser_fixture.php` provides isolated synthetic setup states
  and refuses any database except the dedicated test database. Browser evidence
  remains local under output/playwright; no real customer data was used.
- Outstanding in this lane: complete demo/sample-guide port, OAuth browser
  consent evidence, complete field-error mapping, confirmed setup/conversion
  states and portrait coverage. Existing compatibility CSS is still temporary.

Follow-up verification: full composer check passed with 242 tests, zero failures,
including the shell-variable regression and error adapter. Subsequent sample-guide,
demo, and icon changes passed PHP lint (178 files). Local demo welcome and starter
guide were captured at 1366, 1024, 768 and 390 pixels with no page-width overflow
or JavaScript errors; a new isolated fictional starter visitor was provisioned.
The demo and sample-guide layouts now have an initial port. Historical guide and
OAuth consent state coverage remain pending. Additional icons are unchanged MIT
Tabler 3.46.0 assets from the prototype lockfile and explicitly included in packaging.

## Home checkpoint

`/home` now composes existing cash, receipt/expense draft, journal, AR/AP and
open-item reports. A selected-company `/` and company selection lead to Home.
Attention includes setup, drafts, due/overdue open items and unmatched rows in
draft bank statements. Open-item/control discrepancies remain visible instead
of being presented as reconciled. New bank attention counting is tested against
the existing reconciliation summary and excludes cancelled statements.

Targeted Home suite: 43 tests passed. Browser smoke captures at 1366x768,
1024x768, 768x1024 and 390x844: no page overflow or JavaScript errors; palette
and drawer remain usable. Full verification and populated-state fold review
remain release gates. The current AR/AP source-list service loads all documents;
Home's use of that service needs review alongside list-performance work.

## Journal editor checkpoint

- New journal starts with one line. Add/remove works through POST/redirect with
  JavaScript disabled and preserves incomplete fields; JavaScript enhances the
  same controls. Four-decimal display totals retain the existing BigInt logic.
- Editor posting saves and posts in one outer transaction through the existing
  draft and central posting services. Invalid posting rolls back the editor save;
  original saved values/revisions survive. Repeated new-entry submissions retain
  the existing source identity and cannot post a duplicate.
- Journal and POS behavior are folded into app.js; separate scripts and package
  entries removed. CSP unchanged. POS cart/exact-cash/checkout smoke passed in an
  isolated synthetic company after consolidation.
- 32 targeted editor/core tests passed; full composer check passed with 247 tests,
  zero failures. Browser JS/no-JS flows both added a second line, preserved entered
  values and posted a synthetic balanced 12.3401 journal. Both desktop folds were
  captured; table actions and totals are visible after compacting metadata.
- The remaining daily-work record/list/editor states are not yet accepted.

## Receipt, expense and POS checkpoint

- Receipt/expense editor preview normalizes and validates through the same service
  payload as posting, without creating drafts or journals. Editing a displayed
  preview hides it and asks for an updated server preview.
- Transactions use the split layout with keyboard-operated Details/Journal/History
  tabs. Without JavaScript all sections remain available. Validated list filters
  survive preview, editing, saving, posting and the back link.
- Full composer check: PHP lint (181 files), static analysis, sample validation,
  and 249 tests passed before the POS layout changes. POS follow-up lint and static
  analysis passed; 45 targeted POS/dependency tests passed.
- Browser receipt/expense preview/save/post passed with JavaScript on and off at
  1366x768 and 1024x768. Filter persistence and tab keyboard operation were checked.
- POS now uses its standalone header and records no new financial identity:
  existing sale.created_by and the journal's document source link identify the
  creator. Receipts resolve that creator's display name rather than the viewer's.
  No till/shift management. Historical display names are not snapshotted.
- POS cart/review/checkout/receipt passed with JavaScript on and off; captures at
  1366, 1024, 768 and 390 pixels wide had no horizontal page overflow or JS errors.
  This does not yet close the entire daily-work lane or release verification.

## Connection access and confirmations checkpoint

- Migration 029 adds connection access_mode. Existing credentials default to full
  read, preserving their grants. New personal-token and OAuth browser forms default
  to report-only; the owner can explicitly select full read.
- Report-only allows company discovery/capabilities, trial balance, profit and loss,
  and balance sheet. It excludes accounts, account statements, transactions,
  journals and source detail. OAuth ledger.read remains the transport scope;
  access_mode is an additional server-side grant restriction, reloaded for every
  shared API/MCP read. MCP discovery and capabilities reflect the restriction.
- Module disable and connection revoke use a shared confirmation. Native details
  retains an explicit consequence/review step without JavaScript. With JavaScript,
  a native modal traps focus, starts on Cancel and restores the trigger on close.
- Browser module disable/re-enable and revoke cancel/confirm passed locally.
  Personal token report-only creation and revoke passed with JS on and off. Token
  values were neither captured nor logged. No external client was contacted.
- Full composer check passed: 182 PHP files linted, static analysis and sample
  validation passed, 250 tests with zero failures. The 33-test connection suite
  separately verifies personal and OAuth API/MCP restrictions.
- Disposable fresh install passed (30 migration files). New preview-0.5.0 upgrade
  verifier built schema through 028, preserved a posted journal, accounts, ready
  setup and full-read connection, and verified balanced totals and migration replay.
  Full AR/AP/inventory/currency upgrade scenarios remain part of the release gate.

## Ageing report checkpoint

- Added /reports/ageing as a first-class report using pl_ar_ap_open_items without
  duplicating its financial calculations. Includes receivable/payable selection,
  as-of date, five overdue buckets, document/carrying amounts and per-account
  control reconciliation. Source-document return links preserve direction/date.
- Browser fixture option --ageing creates five exact synthetic balances per
  direction. JS/no-JS browser checks passed for both populated directions and an
  empty historical state, both desktop folds, portrait tablet and phone overflow.
  Update action remained visible; source-return state was checked.
- PHP lint: 183 files, zero failures; static analysis passed. Browser testing
  caught and corrected a missing template allow-list entry before acceptance.
- No additional migration. This uses the existing ageing service; its all-item
  read and document drill-down performance still need the release scale checks.

## Cost of sales and account guidance checkpoint

- Migration 030 adds nullable report_classification to accounts. Every existing
  account stays unclassified. Account edits can explicitly assign expense accounts
  to cost of sales; the existing account audit records the change. The form explains
  that grouping changes all report periods without changing postings or net profit.
- New operational sample cost_of_goods_sold accounts default to this section;
  existing sample-account retries retain their prior classification. Generic new
  starter charts keep their general expense account unclassified.
- Profit and loss returns Income, Cost of sales, Gross profit, Expenses and Net
  profit. CSV and API/MCP pagination include the new section. Sample checkpoint
  verification reconciles combined expense and cost totals to the original fixture.
- Calendar presets work through GET without JS and populate date fields with JS.
  Account statement return links preserve the report's exact date range.
- Account form explains statement consequences and warns before deactivation of
  cash/bank, AR/AP or tax-linked accounts. This is guidance, not a new posting lock.
- Full composer check: 184 PHP files linted, static analysis/sample validation
  passed, 252 tests with zero failures. Final template edits were linted again.
  JS/no-JS browser classification, P&L, period presets and statement return passed;
  P&L captured at both folds, portrait tablet and phone without page overflow.
- Fresh install (31 migration files) and the synthetic 0.5 upgrade/replay passed.
  The upgrade verifier confirms old accounts remain unclassified and posted data
  unchanged. Complete historical starter/currency scenarios still remain pending.

## Multi-item settlement checkpoint

- One receipt/payment allocates across 1–30 unique open items for the same party,
  currency and direction. Allocations must equal the payment exactly; any remainder
  is rejected explicitly in both the service and the screen.
- Posting remains one central journal: historic carrying lines per item, one bank
  line and only the applicable net FX line. No new settlement migration. Whole
  payment reversal restores every allocation and is named as such in the UI.
- Preview performs no writes. Confirmation binds the normalized payment, current
  remaining balances and rate provenance under the book lock; stale reviews are
  rejected. Selecting the applicable FX account after preview is allowed. Durable
  command identity makes retries return the same journal, including after reversal.
- Local synthetic browser checks passed with JavaScript and without it: domestic
  AR/AP allocations, preview and posting, both folds, portrait tablet and phone
  overflow. Foreign zero-net preview has no FX selector; a gain exposes only its
  gain account and posts after selection. Screenshots are local in output/playwright.
- An expanded retry test exposed MySQL JSON object ordering in nested receipts;
  settlement results now use the existing canonical result helper. Final full
  composer check passed: 187 PHP files linted, static analysis and sample validation
  passed, 259 tests with zero failures. Composer needed a 900-second process limit
  for the expanded suite in the accumulated synthetic database; the prior run
  reached the default 300-second limit and was not counted as a pass.
- Payment layout still needs the final prototype-fidelity sweep, along with the
  rest of the sales/purchases/inventory lane. Full route, scale, accessibility,
  historical upgrade and publication gates remain open.

## Stock-count preview checkpoint

- The inventory writer and read-only preview share one movement plan and exact
  count-effect calculation. Preview includes recorded/counted quantities,
  difference, carrying-value effect and the same journal lines used by posting.
- Confirmation binds the reviewed values under the book lock and rejects a changed
  quantity or carrying value. Exact retries still return the original movement.
- The existing inventory route gains a focused count state with sticky document
  actions. Browser increases and decreases passed with JS and without it, including
  stale-review rejection, both folds, portrait tablet and phone overflow checks.
- Targeted accounting suite: 60 tests, zero failures; PHP lint (190 files) and
  static analysis passed. JS syntax and compiled CSS build passed. No migration.
- Whole inventory list/product layouts and goods-receipt previews remain pending;
  this checkpoint does not close the entire inventory lane or release gates.

## Goods-receipt preview checkpoint

- Receipt quantities, remaining-order checks, currency conversion and totals now
  come from one shared read-only plan used by the receipt writer. Preview uses
  the inventory movement plan for the actual stock and GRNI journal effects.
- A focused receive state on /purchasing shows ordered/received/remaining quantities,
  per-line value and the journal effect before physical-receipt confirmation.
  Confirmation rejects changed order or stock balances under the book lock.
- The original purchasing command remains the durable retry identity; inventory
  still posts its own source-linked movements through the central ledger funnel.
- Targeted suite: 62 tests, zero failures. PHP lint (191 files), static analysis,
  JavaScript syntax and CSS build passed. JS/no-JS browser receipt posting and
  stale-review rejection passed; both folds, portrait and phone had no overflow.
- No migration. Purchase-order editor/list fidelity and complete route acceptance
  remain pending. No external or hosted system changed for this checkpoint.
