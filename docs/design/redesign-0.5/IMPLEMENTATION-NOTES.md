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
