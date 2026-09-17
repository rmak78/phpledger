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
