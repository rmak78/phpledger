# PHP Ledger 0.5 interface redesign: prototype

**Status: prototype for owner review, 17 Sep 2026.** Nothing here is in the running app yet. This folder replaces the pending "Ledger Desk / Today First / Books & Workspaces" shell selection (release board P0.4). The owner chose a Frappe/ERPNext-style workspace with Akaunting-style document forms, in a calm "Claude-like" tone.

## Owner decisions (17 Sep 2026)

- Redesign every screen before any further feature work.
- Prototype first, then rebuild the PHP templates.
- Tone: Claude-calm. Warm off-white canvas, navy `#0C2052` primary, blue `#4656E8` focus/links, Inter.
- Light theme only for now; colours are CSS-variable tokens so a dark theme is a variable swap later.
- Desktop and tablet only. Fold targets **1366×768** and **1024×768**; 768×1024 spot-checked. Mobile deferred.
- Tailwind CSS approved. It is compiled at development time and the CSS file is committed, so the VPS needs no Node and the strict CSP stays unchanged.
- Built by Claude Sonnet agents: one foundation, four parallel screen lanes, one consistency QA pass.

## Open it

- `dist/index.html` opens in any browser, no server needed. Start at the Screen index; press Ctrl K to jump to any screen.
- Coverage: **75 screen states** covering all 38 HTML routes in `docs/design/ux-recovery/ROUTE-COVERAGE.json`, plus a component sheet.
- All screens pass the automated check at 1366×768 and 1024×768: primary actions/totals (`data-fold`) above the fold, no horizontal overflow, no JS errors.

## Rebuild

```
npm install            # tailwindcss 4, @tailwindcss/cli, @tabler/icons, playwright (dev only)
node build.mjs         # → dist/index.html (standalone) and dist/artifact.html
node shots.mjs [ids] --sizes=1366x768,1024x768   # viewport PNGs in shots/ + fold/overflow report
```

- `BRIEF.md`: design brief (IA, visual system, patterns, fold rules).
- `COMPONENTS.md`: component classes and layout recipes.
- `NOTES.md`: builder notes and QA resolutions.
- `src/app.css`: tokens + component layer. `src/shell.html`: sidebar/topbar. `src/proto.js`: attribute-driven behaviour, no inline handlers.
- `screens/*.html`: one file per screen state.

## Structure in one paragraph

A left sidebar (collapses to an icon rail below 1180px, becomes a drawer below 900px) holds a company switcher that replaces the old company bar, plus Home, Daily work, Sales, Purchases, Inventory, Banking, Reports and a collapsible Setup group. A 48px topbar carries breadcrumbs, search/jump (Ctrl K), a global **+ New** quick-create menu, the sample guide link and the user menu. Every screen uses one of nine patterns: list+detail split view, document editor with sticky actions and one-line-first line table, record view, report, wizard with a pinned footer, settings list, focused/auth page, full-screen POS, and Home.

## Items marked "proposed" (need an owner yes/no before the build)

Marked in the prototype with `data-proposed` (hover shows the explanation). They go beyond what the app does today:

1. Home page combining drafts, overdue AR, bills due, bank lines and setup status (each figure already exists per module).
2. Line editors start with one line + "Add line" instead of 5–10 fixed blank groups (UX audit P1).
3. Live line amounts, running totals and posting preview while editing.
4. Post directly from the journal editor once balanced (today: save, then post from review).
5. Receipt/payment allocation across several open invoices/bills in one screen (today: one open item per settlement).
6. FX gain/loss fields shown only for foreign-currency settlements (UX audit P1; needs a service change, not only UI).
7. Stock count preview (recorded → counted → difference → value) and goods-receipt stock/GRNI effect preview.
8. P&L period presets, a cost-of-sales grouping and a gross profit line (today the report returns flat income/expense).
9. Dedicated receivables/payables ageing report.
10. Confirmation dialogs before disabling a module or revoking a connection.
11. Connection scope choice (report-only vs full read).
12. Drill-down "back" preserving date range and filters; journal list status/text filters.
13. Account form hint showing which statement a new account appears on; warning before deactivating operational accounts.
14. POS cashier/till identity.

## Proposed build plan (phase 2, after approval)

1. **Foundation in the app**: add the Tailwind build (dev-only) that outputs `public/assets/app.css`; port tokens and components; add PHP view helpers (`templates/partials/ui/*`: page header, badge, table, field, doc header, totals, strip) so screens cannot drift; new `layout.php` shell; vanilla JS behaviours ported from `proto.js` (CSP-safe). Retire `core.css`, `starter.css`, `ledger-tables.css`, `sample-guide.css`. Decide whether DataTables stays (its look and markup fight the new tables; server-rendered filtering + pagination is proposed).
2. **Port screens in four lanes** (setup/access, daily work/POS, sales/purchases/stock, reports/admin), each against its prototype screenshots. Server-side behaviour, permissions, CSRF and posting services are unchanged.
3. **Verify on the real app**: run it locally (PHP 8.2 + MariaDB), Playwright screenshots at 1366×768 and 1024×768 compared with the prototype, the existing test suite, and the fold/overflow checks.
4. Proposed items are built only where approved; items 5, 6 and 8 need service-layer work and move to their own tickets.

Known prototype limitation: a few sample document numbers (for example EXP-000585) show different counterparties on the transactions and bank reconciliation screens. It's sample data only.
