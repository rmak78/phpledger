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
