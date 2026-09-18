# PHP Ledger 0.5 — component cheat-sheet

Foundation is built. Read this, then `screens/components.html` (visual sheet, class names printed
under every swatch), then copy the nearest existing screen (`screens/home.html`, `transactions-list.html`,
`ar-invoice-new.html`) for a full working example of its pattern. Don't invent new colors, radii, shadows
or spacing scales — everything you need is a Tailwind utility or a class below. If something's missing,
leave a note in `NOTES.md` instead of adding a new token.

**The rule of thumb:** layout (flex/grid/gap/padding/width) → Tailwind utilities, written in the markup.
Repeated UI pieces (a button, a badge, a table) → a named class from `src/app.css`. Never inline `style=""`,
never `ml-/mr-/pl-/pr-/left-/right-/text-left/text-right` (use the logical equivalents below — Urdu RTL is next).

## Screen file checklist

```html
<!--meta {"id":"bills-list","title":"Bills","route":"/bills","group":"Purchases","nav":"bills",
  "crumbs":[["Bills"]],"pattern":"list-detail","layout":"app","state":"Unpaid · 2 selected"} -->
<div class="...">
  ...
</div>
```
- One file = one screen **state**. A different state of the same route (posted vs. draft, empty vs.
  populated) is a separate file (`ar-invoice-new`, `ar-invoice-posted`, `ar-invoice-empty`…).
- `layout`: `app` (sidebar shell), `auth` (centered card, no sidebar), `bare` (nothing — POS, screen index).
- `nav` must equal one `data-nav` id from the sidebar in `src/shell.html` (home, transactions, pos, journals,
  invoices, customers, bills, purchase-orders, suppliers, products, bank-reconciliation, reports,
  profit-loss, balance-sheet, trial-balance, account-statement, cash-forecast, accounts, tax,
  opening-balances, opening-conversion, periods, modules, connections, help). Leave `""` for `bare`/`auth`.
  At render, proto.js scrolls the matching `[data-nav]` item into view and auto-expands its
  `.nav-group-collapsible` (currently just Setup) if it's collapsed — both generic, nothing a screen
  needs to do beyond setting `nav` correctly.
- `crumbs`: array of `[label, href?]`; the last entry has no href (it renders as the current page).
- `route`: the **real** route from `www/phpledger/public/index.php` — not the screen id.
- Link to another screen with `href="#/<screen-id>"`. Make a whole row clickable with `<tr data-href="#/<id>">`.
- Mark anything that must clear the fold with `data-fold="<label>"` (primary action, document total, filter
  bar, first rows…) — `shots.mjs` fails the screen if that element's bottom lands below the viewport.
- A real but not-yet-built improvement gets `data-proposed title="Proposed: why, and what today's app does
  instead."` on the element itself (see `ar-invoice-new.html`'s totals block and Add-line button). Don't put
  `data-proposed` (or any other attribute) directly on an `<i data-icon="…">` tag — build.mjs's icon inliner
  only matches `<i data-icon="name">` or `<i data-icon="name" class="…">` immediately followed by `></i>`;
  wrap the icon in a `<span>` if it needs more attributes.
- Icons: `<i data-icon="trash"></i>` or `<i data-icon="trash" class="icon size-4"></i>` (Tabler outline,
  inlined at build; build.mjs prints `MISSING ICONS` if a name doesn't exist under
  `node_modules/@tabler/icons/icons/outline/`).

## Logical properties (RTL) — use these, not the physical ones

| Physical (never) | Logical (always) |
|---|---|
| `ml-*` / `mr-*` | `ms-*` / `me-*` |
| `pl-*` / `pr-*` | `ps-*` / `pe-*` |
| `left-*` / `right-*` | `start-*` / `end-*` |
| `text-left` / `text-right` | `text-start` / `text-end` |
| `border-l` / `border-r` | `border-s` / `border-e` |
| `rounded-l-*` / `rounded-r-*` | `rounded-s-*` / `rounded-e-*` |

Block-direction utilities (`border-t/b`, `pt-/pb-`, `mt-/mb-`, `top-/bottom-`) are already RTL-safe — only
inline/horizontal ones need the logical form.

## Typography

`.eyebrow` (uppercase label above a title — use sparingly; most page headers now just need a plain
muted meta line under the title, see Page header below) · `.page-title` (screen h1) · `.section-title`
(h2 inside a screen) · `.amount-lg` / `.amount-xl` (tabular-nums money, 22px/28px) · `.link`
(brand-blue text link — **not underlined at rest**, underline appears on hover/focus only; that's the
rule for every standalone UI link — nav-style links, "Review invoices", a header action styled as a
link. An inline link *inside a sentence of prose* — e.g. `.strip a` / `.alert a` — is the one exception
and stays underlined at rest, because scanability inside a paragraph needs it even when not hovered).
Table/amount cells: add `.num` (right-aligns + tabular-nums) or `.tabular-nums` directly.

## Buttons

```html
<button class="btn btn-primary">Post</button>
<button class="btn btn-secondary">Save draft</button>
<button class="btn btn-ghost">Cancel</button>
<button class="btn btn-danger">Delete</button>
<button class="btn btn-secondary btn-sm">Small</button>
<button class="btn btn-ghost btn-icon" aria-label="More"><i data-icon="dots" class="icon"></i></button>
```
One primary button per screen/panel. `btn-sm` for in-table/toolbar actions. `btn-icon` makes it square
(pair with `btn-sm` for a compact icon button). `disabled` attribute dims it automatically.

## Inputs & fields

```html
<div class="field">
  <label class="field-label" for="x">Customer</label>
  <select class="select" id="x">…</select>
  <p class="field-hint">Optional helper text.</p>
</div>
```
`.input` / `.select` / `.textarea` — 34px tall, full width of their container (set the container's width
with Tailwind, e.g. `max-w-xs`). `.input-amount` right-aligns + tabular-nums for money entry.
`<span class="optional">(optional)</span>` inside a `.field-label` for non-required fields.
Error state: wrap in `.field-error` (reddens the control) and add `<p class="field-error-text">`.
Search box: `<label class="search-field"><i data-icon="search" class="icon"></i><input type="search" …></label>`.

**Date field** — never `type="date"`: a native date input renders in the browser/OS locale
(mm/dd/yyyy on most screenshot rigs), which fights the app's own "15 Sep 2026" format everywhere
else and makes screenshots inconsistent. Use `type="text"` with a pre-formatted value and a calendar
icon affordance instead:
```html
<div class="field">
  <label class="field-label" for="x">Due date</label>
  <span class="input-affix"><input class="input" id="x" type="text" value="15 Oct 2026"><i data-icon="calendar" class="icon input-affix-icon"></i></span>
</div>
```
`.input-affix` positions the icon inside the field's end edge and pads the input so text never runs
under it — the same wrapper works for any single trailing-icon input, not just dates.

## Badges (status — always dot + text, never colour alone)

`badge-draft` `badge-unpaid` `badge-reversed` (neutral) · `badge-posted` `badge-paid` (green) ·
`badge-partially-paid` `badge-due-soon` (amber) · `badge-overdue` (red) · `badge-info` (blue) ·
`badge-sample` (brand-tinted "Sample" pill) · `badge-on-brand` (white-on-navy — only for a badge that
genuinely sits on a solid brand-navy fill; the app's tone is calm surfaces first, so reach for this
rarely, not as the default "important number" treatment — see the Home recipe's cash card, which is a
normal `.panel` with the figure itself in navy, not a navy-filled card).
```html
<span class="badge badge-posted"><i class="badge-dot"></i>Posted</span>
```

## Tabs

Segmented (filter bar, e.g. Drafts/Posted/Reversed/All):
```html
<div class="tabs-seg" role="tablist" aria-label="…">
  <button class="tabs-seg-item" aria-selected="true">Drafts</button>
  <button class="tabs-seg-item" aria-selected="false">Posted</button>
</div>
```
Underline (record detail sections — wire up with `proto.js`'s tab behaviour):
```html
<div class="tabs-underline" data-tabs role="tablist" aria-label="Record sections">
  <button class="tabs-underline-item" data-tab="details" aria-selected="true">Details</button>
  <button class="tabs-underline-item" data-tab="journal" aria-selected="false">Journal</button>
</div>
<div class="tab-panel" data-panel="details">…</div>
<div class="tab-panel" data-panel="journal" hidden>…</div>
```
`data-panel` containers must be **direct siblings of each other**, siblings of the `[data-tabs]` element,
under the same parent (proto.js scopes with `tabsEl.parentElement.querySelectorAll(':scope > [data-panel]')`).

## Table

```html
<div class="table-wrap">
  <table class="table">
    <thead><tr><th>Date / reference</th><th class="num">Amount</th><th>Status</th></tr></thead>
    <tbody>
      <tr data-href="#/id"><td><span class="row-title">14 Sep 2026</span><span class="row-sub">EXP-000590</span></td>
        <td class="num">3,250.00</td><td><span class="badge badge-draft">…</span></td></tr>
    </tbody>
    <tfoot><tr class="table-totals"><td>Total</td><td class="num">3,250.00</td></tr></tfoot>
  </table>
</div>
```
Always wrap `.table` in `.table-wrap` (border, radius, and the `overflow-x:auto` that lets a wide table
scroll internally instead of the page). `th`/`td.num` right-aligns. `.row-title` + `.row-sub` for a
two-line cell (primary line + muted secondary line). `data-href` on a `<tr>` makes the whole row clickable
(proto.js also sets `aria-selected` on it for list-detail selection) — pair with `cursor-pointer` for free
via the `.table tbody tr[data-href]` rule. Selected row: `<tr aria-selected="true">`.

## Editable line-items table (document editor)

Use `.doc-lines-table` (not plain `.table`) for an editable table whose cells hold `<input>`/`<select>` —
it forces `table-layout:fixed` so column widths (set via an explicit `w-[…%]` on every single `<th>`,
none left width-less) stay honest instead of the browser silently starving a column to fit a
`<select>`'s widest *option*. **Widths are percentages, not rem/px** — that's what makes the same
markup fit both 1366×768 (~1090px of doc-body width) and 1024×768 (~880-920px with the rail sidebar)
with no horizontal scroll: percentages are relative to the table's own `width:100%`, so `table-layout:
fixed` scales every column by the same factor at any width instead of holding a hardcoded floor. The
split below is the tuned, screenshot-verified proportions for an 8-column line (description / product /
account / qty / unit price / tax code / amount / remove) — reuse these exact percentages rather than
re-guessing per screen (a 6- or 7-column table, e.g. purchase orders or a credit note, has its own set
that still sums to 100% — see the existing screens for those):

| Column | Width | Notes |
|---|---|---|
| Description | `w-[25.4%]` | the one column that reads as "flexible" but still gets a hard width |
| Product | `w-[15.7%]` | must show a full option like `Cotton 60"` |
| Account | `w-[15.7%]` | must show a full option like `Fabric sales` |
| Qty | `w-[7.1%]`, `num` | |
| Unit price | `w-[10.3%]`, `num` | |
| Tax code | `w-[11.2%]` | a `<select>` needs room for its own chevron on top of the text — too narrow clips the "%" in "GST 18%" |
| Amount | `w-[11.2%]`, `num` | usually `data-proposed` — see below; must stay visible at every fold width |
| Remove | `w-[3.4%]` | icon-only button, no header text; has a floor from its 36px icon button regardless of percentage |

`.doc-lines-table td` and its `input`/`select` also carry tighter padding than a plain `.table`
(`.375rem` instead of `.75rem`/`.625rem`) — at 1024px width the narrowest columns are only 40-60px, and
the normal padding would eat most of that before a single digit rendered. Don't add `.table`'s default
padding back on this table for that reason.

The percentage scaling above is deliberately unbounded above 1024px and has no floor below it either —
that's correct for the two supported desktop widths (1024/1366) but means a much narrower view (e.g.
768px) runs out of room for legible digits before it runs out of columns. `.doc-lines-table` therefore
also carries `@media (max-width: 1023px) { min-width: 46rem; }`, which hands back to `.table-wrap`'s own
`overflow-x:auto` below that point — the table scrolls sideways instead of a four-decimal unit price or a
six-figure amount truncating mid-number. This only engages under 1024px; don't lower that breakpoint or
the 1024×768 fix above regresses.
```html
<table class="table doc-lines-table">
  <thead><tr><th class="w-[25.4%]">Description</th><th class="w-[15.7%]">Product</th>
    <th class="w-[15.7%]">Account</th><th class="num w-[7.1%]">Qty</th>
    <th class="num w-[10.3%]">Unit price</th><th class="w-[11.2%]">Tax code</th>
    <th class="num w-[11.2%]">Amount</th><th class="w-[3.4%]"></th></tr></thead>
  <tbody id="lines-body">
    <tr data-row>
      <td><input class="input" value="…"></td>
      <td><select class="select">…</select></td>
      <td><select class="select">…</select></td>
      <td><input class="input input-amount" value="1"></td>
      <td><input class="input input-amount" value="0.00"></td>
      <td><select class="select">…</select></td>
      <td class="num">0.00</td>
      <td><button type="button" class="btn btn-ghost btn-icon btn-sm" data-remove-row aria-label="Remove line"><i data-icon="trash" class="icon"></i></button></td>
    </tr>
  </tbody>
</table>
<template id="line-template"><!-- one blank <tr data-row> matching the shape above --></template>
```
Below the table, put the Add-line button beside the totals block (not stacked above them) — see the
document-editor recipe below; that one change is what keeps a 3-line invoice's Posting preview above
the fold at 1366×768.

Start with **one** line, not five blank groups (P1 fix — mark the Add-line button `data-proposed`).
`data-remove-row` never removes the table's last row (enforced in proto.js). Keep cell content short —
this table is already tight (e.g. "Fabric sales" not "Sales – Fabrics", "GST 18%" not
"GST 18% (sample, manual)"); long strings in a `<select>`'s options also inflate its rendered width even
though `table-layout:fixed` stops it from stealing space from neighbours.

## Definition grid (record detail's "Details" tab)

```html
<dl class="dgrid">
  <div class="dgrid-row"><dt>Paid to</dt><dd>Sialkot Packaging</dd></div>
</dl>
```

## Panel

`.panel` = bordered, radius-10, `1.25rem` padding surface — for anything that needs visible separation
(a balance card, a menu-less grouped block). Not everything is a card: list rows and form sections sit
directly on the canvas with dividers, not panels. `.panel-header` for a title+action row inside one.

## Alerts & context strips

Inline alert (validation, a warning inside a form):
```html
<div class="alert alert-warning"><i data-icon="alert-triangle" class="icon"></i>
  <div><span class="alert-title">Heads up</span><p>…</p></div></div>
```
`alert-danger` / `alert-info` / `alert-warning`. Full-width dismissible strip under the topbar (setup
readiness, public-demo notice) — put it in `.shell-strips`, ≤32px:
```html
<div class="strip strip-warning" data-dismissible>
  <i data-icon="alert-triangle" class="icon"></i>
  <p>Opening balances required — <a href="#/opening-balances">Review setup →</a></p>
  <button class="strip-dismiss" data-dismiss aria-label="Dismiss"><i data-icon="x" class="icon"></i></button>
</div>
```
`strip-info` / `strip-warning` / `strip-sample`.

A screen's own markup sits inside `[data-slot="content"]`, below the shell's strips region — so to
actually land a strip there, mark the element `data-shell-strip` anywhere in your screen fragment;
proto.js moves every `[data-shell-strip]` element into the shell's real `[data-slot="strips"]` region at
render time, in document order. You don't need any extra JS for this — it's generic. `readiness-strip.html`
and `demo-transactions.html` are the reference screens.

## Menu / popover (native `<details>`, outside-click + Escape close via proto.js)

```html
<details class="menu">
  <summary class="btn btn-secondary"><i data-icon="filter" class="icon"></i>Filters<i data-icon="chevron-down" class="icon"></i></summary>
  <div class="menu-panel"><!-- add menu-panel-wide for a 280px+ panel, menu-panel-end to right-align -->
    <p class="menu-label">Section</p>
    <button class="menu-item" type="button"><i data-icon="…" class="icon"></i>Item</button>
    <div class="menu-divider"></div>
  </div>
</details>
```

## Dialog

Native `<dialog class="dialog">`, opened with `data-dialog-open="<id>"` on any button, closed by a
`<form method="dialog">` button, backdrop click, or Escape — all free, no JS.
```html
<button type="button" data-dialog-open="confirm-dialog" class="btn btn-danger">Delete</button>
<dialog id="confirm-dialog" class="dialog">
  <div class="dialog-header"><h2 class="text-sm font-semibold">Delete draft?</h2>
    <form method="dialog"><button class="btn btn-ghost btn-icon" aria-label="Close"><i data-icon="x" class="icon"></i></button></form></div>
  <div class="dialog-body">…</div>
  <div class="dialog-footer"><form method="dialog"><button class="btn btn-ghost">Cancel</button></form>
    <button type="button" class="btn btn-danger">Delete</button></div>
</dialog>
```
Right-hand `.sheet` (fixed, 420px, slides from the inline-end edge) for settings-list inline editing —
same open/close mechanics, just different positioning; see `.sheet` in `app.css`.

## Kbd / command palette

`<span class="kbd-group"><span class="kbd">Ctrl</span><span class="kbd">K</span></span>` for a shortcut
hint. The command palette itself (`Ctrl/Cmd+K` or `/` opens it globally) is built once in `proto.js` —
don't hand-build another one; if a screen needs its own search-to-jump trigger, just add
`data-command-open` to a button/`.search-trigger`.

## Empty state

```html
<div class="empty-state">
  <span class="empty-state-icon"><i data-icon="inbox" class="icon"></i></span>
  <h2>No drafts yet</h2>
  <p>Expenses and receipts you save without posting show up here.</p>
  <button class="btn btn-primary mt-1">New transaction</button>
</div>
```

## Stepper / wizard

```html
<div data-wizard class="grid grid-cols-[220px_1fr] gap-8">
  <div class="stepper">
    <div class="stepper-step is-done"><span class="stepper-index"><i data-icon="check" class="icon"></i></span>
      <span class="stepper-label">Business details</span></div>
    <div class="stepper-step is-current"><span class="stepper-index">2</span>
      <span class="stepper-label">Opening balances<span class="stepper-hint">Optional</span></span></div>
    <div class="stepper-step"><span class="stepper-index">3</span><span class="stepper-label">Review</span></div>
  </div>
  <div>
    <div data-step-panel="0" hidden>…</div>
    <div data-step-panel="1">…</div>
    <div data-step-panel="2" hidden>…</div>
  </div>
</div>
<div class="wizard-footer">
  <button type="button" class="btn btn-ghost" data-step-back>Back</button>
  <button type="button" class="btn btn-primary" data-step-next>Continue</button>
</div>
```
`data-step-panel` indices are 0-based and must match the stepper's position. `.wizard-footer` is sticky —
always visible without scrolling.

## Page header

```html
<header class="page-header">
  <div><h1 class="page-title">Receipts &amp; expenses</h1><p class="mt-1 text-sm text-ink-muted">3 drafts · PKR 113,850.00</p></div>
  <div class="page-header-actions"><button class="btn btn-primary" data-fold="primary action"><i data-icon="plus" class="icon"></i>New transaction</button></div>
</header>
```
Title is the **object**, not the current filter/state (`"Receipts & expenses"`, not `"Drafts"`) — the
filter tabs already say which subset you're looking at. The count/total goes in a plain muted line
under the title, not an `.eyebrow` above it — an eyebrow reads as a section label, and stacking one
above every page header becomes noise once 40 screens have one. Reach for `.eyebrow` only where it
genuinely labels something (a small caption above a big number), not as a default page-header slot.
A secondary navigation action (e.g. "Account ledger") that used to be a loose underlined link under
the header belongs in `.page-header-actions` instead, styled `.btn.btn-ghost` — it reads as an action,
not a stray sentence, and it doesn't cost its own row.
`.page-header` is `position:sticky` + has its own top padding by default — that's right for a page
whose header sits directly under the topbar. Nested one level in (e.g. inside a flex column, as
`transactions-list.html` no longer needs to do now that the ledger link moved into the header — but
keep the pattern in mind for anything else nested a level deeper), add `!static !py-0` to cancel both
so it doesn't double-stick or double-pad.

## Filter bar

```html
<div class="filter-bar" data-fold="filter bar">
  <div class="tabs-seg" role="tablist">…</div>
  <label class="search-field">…</label>
  <select class="select max-w-[7.5rem]">…</select>
  <details class="menu"><summary class="btn btn-secondary"><i data-icon="filter" class="icon"></i>Filters</summary>…</details>
  <button class="btn btn-secondary btn-icon" aria-label="Export"><i data-icon="download" class="icon"></i></button>
</div>
```
One row at 1366×768; wraps only once the viewport narrows (1024×768 and below). To hold that: keep the
type/status `<select>` narrow (`max-w-[7.5rem]`, not wider — it only ever holds short words), drop the
`Filters` button's chevron-down icon (the icon+label is already a clear "opens a menu" affordance; the
extra caret was pure width with no added meaning), and rely on `.search-field`'s built-in
`flex:1 1 140px; min-width:110px` inside `.filter-bar` to shrink before anything wraps. Don't widen the
segmented tabs or add more controls to this row without re-checking it still holds at 1366.

## Pagination

```html
<div class="pagination">
  <span class="pagination-summary">3 records · Total <strong class="text-ink">PKR 113,850.00</strong></span>
  <div class="pagination-controls">
    <button class="btn btn-ghost btn-icon btn-sm" disabled aria-label="Previous page"><i data-icon="chevron-left" class="icon"></i></button>
    <span class="pagination-page">Page 1 of 1</span>
    <button class="btn btn-ghost btn-icon btn-sm" disabled aria-label="Next page"><i data-icon="chevron-right" class="icon"></i></button>
  </div>
</div>
```

## Avatar

`<span class="avatar">AK</span>` (28px, user menu) · `<span class="avatar-sm">RT</span>` (22px, dense lists).

---

## Layout recipes — one per page pattern

### 1. List + detail (split view)

```html
<header class="page-header !static !py-0">
  <div><h1 class="page-title">Receipts &amp; expenses</h1><p class="mt-1 text-sm text-ink-muted">3 drafts · PKR 113,850.00</p></div>
  <div class="page-header-actions">
    <a class="btn btn-ghost" href="#/account-statement"><i data-icon="book-2" class="icon"></i>Account ledger</a>
    <button class="btn btn-primary" data-fold="new transaction button"><i data-icon="plus" class="icon"></i>New transaction</button>
  </div>
</header>
<div class="split-view">
  <section class="split-view-list has-detail" aria-label="…">
    <div class="flex flex-col gap-3">
      <div class="filter-bar" data-fold="filter bar">…</div>
      <div class="table-wrap" data-fold="first rows"><table class="table">…</table></div>
      <div class="pagination">…</div>
    </div>
  </section>
  <section class="split-view-detail" aria-label="…">
    <a class="split-view-back link" href="#/list-id"><i data-icon="arrow-left" class="icon"></i>Back to list</a>
    <div class="border-b border-border px-5 pb-4 pt-4"><!-- number, status badge, amount --></div>
    <div class="flex min-h-0 flex-1 flex-col overflow-y-auto px-5 pt-4"><!-- tabs-underline + tab-panels --></div>
    <div class="panel-actions"><p class="panel-actions-note">…</p><button class="btn btn-secondary">Edit draft</button><button class="btn btn-primary" data-fold="post button">Post</button></div>
  </section>
</div>
```
Page title is the object ("Receipts & expenses"), never the current filter/state ("Drafts") — see
Page header above. A secondary link that used to sit under the header (e.g. "Account ledger & running
balances") moves into `.page-header-actions` as a `.btn.btn-ghost`, shortened to fit a button label
(put the full phrase in a `title=""` attribute if you want it as a tooltip).

**Do not** put a Tailwind `flex`/`display` utility directly on `.split-view-list` — it already carries
`.has-detail { display:none }` at ≤899px in `@layer components`, and a utility class (higher layer) would
beat it, breaking the mobile "detail replaces list" behaviour. Put `flex flex-col gap-…` on an inner
wrapper `<div>` instead, exactly as above. At ≤1180px the detail column narrows (440px→380px); at ≤899px
it stacks and the list hides while a record is open, with `.split-view-back` becoming visible.

`.split-view-list` also carries `min-width: 0` generically now, so as a grid item it can shrink below its
content-based automatic minimum instead of a wide table (many un-wrappable columns) pushing the fixed
detail column past the viewport. You don't need a screen-scoped override for a wide table — it's covered.

`.panel-actions-note` (the hint text beside the action buttons in a detail panel's footer) always takes
its own full row (`flex: 1 1 100%`) rather than sharing the row with the buttons — this keeps a longer
note (icon + text + a link, next to two buttons) from shrinking to a sliver and wrapping character-by-
character at narrower widths. Don't add a `basis-full`/`w-full` utility on it — it's already the default.

**Same trap on `.split-view-back`**: give it only `class="split-view-back link"`, nothing else. It
already carries its own `display:none` (shown only ≤899px) plus its flex/gap/margin in the component
layer for exactly this reason — any Tailwind `inline-flex`/`flex` utility riding along on the same
element out-ranks the component's `display:none` and leaves the "Back to list" link visible on
desktop too.

### 2. Document editor

```html
<div class="py-5">
  <div class="flex flex-col rounded-panel border border-border bg-surface">
    <header class="doc-header" data-fold="doc header">
      <div class="doc-heading"><span class="doc-type">Invoice</span><h1 class="doc-number">New</h1>
        <span class="badge badge-draft"><i class="badge-dot"></i>Draft</span><span class="doc-savestate">Not saved yet</span></div>
      <div class="doc-actions"><button class="btn btn-ghost">Cancel</button><button class="btn btn-secondary">Save draft</button>
        <button class="btn btn-primary" data-fold="primary action">Post</button></div>
    </header>
    <div class="doc-body">
      <section><div class="grid grid-cols-2 gap-x-4 gap-y-4 md:grid-cols-3 lg:grid-cols-4"><!-- header fields, dates via .input-affix --></div></section>
      <section>
        <h2 class="section-title mb-2">Lines</h2>
        <div class="table-wrap"><table class="table doc-lines-table"><!-- see the line-items table section above for the exact column widths --></table></div>
        <div class="mt-3 flex flex-wrap items-start justify-between gap-4">
          <div class="flex flex-col items-start gap-2">
            <button type="button" class="btn btn-ghost btn-sm" data-add-row="…" data-row-template="…" data-proposed><i data-icon="plus" class="icon"></i>Add line</button>
            <p class="max-w-xs text-xs text-ink-muted">Amounts allow four decimal places…</p>
          </div>
          <div class="doc-totals" data-fold="document total">
            <div class="doc-totals-row"><span>Subtotal</span><span class="amount">…</span></div>
            <div class="doc-totals-row is-grand"><span>Total</span><span class="amount">…</span></div>
          </div>
        </div>
      </section>
      <section>
        <details class="w-full rounded-panel border border-border"><!-- collapsible posting preview: .journal-table --></details>
      </section>
    </div>
  </div>
</div>
```
Field grid: **`lg:grid-cols-4`**, not `xl:grid-cols-4` — the fold target is 1024×768, and `xl` (1280px)
doesn't kick in there, adding a field row that can push the totals below the fold. Date fields use the
`.input-affix` pattern from Inputs & fields, never `type="date"`.

**Put the Add-line button beside the totals block, not stacked above it** — a
`flex flex-wrap items-start justify-between` row with Add-line (+ its hint text) on the start side and
`.doc-totals` on the end side, immediately after the lines table, in the *same* `<section>` as the
table (not a separate one). This is what recovers enough vertical space for the Posting preview
header to clear the fold at 1366×768 on a 3-line invoice; stacking them (Add-line's own row, then a
separate totals section below) costs roughly one extra row of height for no layout benefit, since
both are short. `.doc-totals` no longer needs `align-self`/`width:100%` for this — it's a plain flex
item on the row, capped at `max-width:300px` with a `min-width:220px` floor. The posting preview
`<details>` stays in its own `<section>` after that row.

### 3. Record view (posted/reversed document)

Same shell as the document editor's header/body (`.doc-header`/`.doc-body`) but fields render as read-only
`.dgrid` rows instead of inputs, the primary header action is a follow-up (Record payment / Reverse /
Print), and the lines table is a plain `.table` (not `.doc-lines-table` — nothing is editable). Add a
"Related documents" list (payments, reversal link) as a simple `.table` or link list before/after the
posting-preview equivalent (here just "Journal effect", not collapsible — it's a permanent record).

### 4. Report

```html
<div class="flex flex-col gap-4 py-5">
  <header class="page-header !static !py-0">…title, Export…</header>
  <div class="filter-bar" data-fold="filter bar"><!-- as-of/from-to, compare, basis, Export --></div>
  <div class="table-wrap p-2">
    <table class="stmt-table">
      <thead><tr><th>Account</th><th class="num">Amount</th></tr></thead>
      <tbody>
        <tr class="stmt-row"><td class="stmt-group-label">Revenue</td><td class="num"></td></tr>
        <tr class="stmt-row stmt-indent-1"><td><a class="link" href="#/account-statement">Fabric sales</a></td><td class="num">…</td></tr>
        <tr class="stmt-row stmt-subtotal"><td>Total revenue</td><td class="num">…</td></tr>
        <tr class="stmt-row stmt-total"><td>Net profit</td><td class="num">…</td></tr>
      </tbody>
    </table>
  </div>
</div>
```
No `.table-wrap`'s `overflow-x:auto` escape hatch here in spirit — a report must fit 1024px width without
horizontal scroll, so don't add more columns than that allows; drill down to `account-statement` instead
of widening the table. `stmt-indent-1`/`-2` nests a group; `stmt-subtotal`/`stmt-total` give the weight
hierarchy. Amounts that drill down are `<a class="link">`, not the whole row.

### 5. Wizard / guided flow

See the Stepper section above — `data-wizard` wrapper, `.stepper` + `[data-step-panel]`, `.wizard-footer`
pinned at the bottom. Keep each step to one focused question/preview; totals/counts the step is about to
commit go in a small `.panel` inside that step so the "what happens next" boundary is explicit.

### 6. Settings list

A plain `.table` (or a simple `.dgrid`-per-row list for very few items) with a row action column
(`btn-ghost btn-icon btn-sm` … or a `.menu`), and either a `.sheet` (edit-in-place, slides from the
inline-end) or an inline `<details>` expand under the row for editing — don't build a whole document-editor
page for a settings row.

### 7. Focused / auth

```html
<!--meta {..., "layout":"auth"} -->
<div class="auth-panel">
  <h1 class="page-title text-center">Sign in</h1>
  <form class="mt-5 flex flex-col gap-4"><div class="field">…</div><button class="btn btn-primary mt-1">Continue</button></form>
</div>
```
The `layout-auth` template already centers this with the logo above it (`.auth-shell`/`.auth-card`) — a
screen with `"layout":"auth"` only supplies what goes inside `.auth-panel`.

### 8. POS

`layout: "bare"` (full-screen, no shell). Two-pane `grid grid-cols-[1fr_380px]` (or similar) — product
grid + category tabs + search on the start side, cart (`.table`-like rows with qty steppers, `.doc-totals`-
style total block, Checkout `.btn-primary`) fixed on the end side, both fitting 1024×768 with no scroll on
the cart/checkout column.

### 9. Home

```html
<header class="page-header !static !py-0" data-fold="page header">
  <div><h1 class="page-title">Home</h1><p class="mt-1 text-sm text-ink-muted">Ravi Textile Traders · 17 Sep 2026</p></div>
  <div class="page-header-actions"><button class="btn btn-primary" data-fold="new transaction button"><i data-icon="plus" class="icon"></i>New transaction</button></div>
</header>
<div class="flex flex-wrap items-center gap-2" data-fold="quick actions">
  <a class="btn btn-secondary btn-sm" href="#/transactions-list"><i data-icon="receipt-2" class="icon"></i>Expense</a>
  <!-- …Receipt, Invoice, Bill, Journal, same pattern -->
</div>
<section><!-- Needs attention --></section>
<section><!-- Cash & bank, and what's due --></section>
<section><!-- Recent activity --></section>
```
**No decorative greeting.** DESIGN.md is explicit that Home never says "Good morning, {name}" — the
page title is just "Home", and the muted line under it is data (business · date), not a sentence
pretending to be a person. Don't reintroduce a greeting hero on any other start-of-flow screen either.

**Quick actions is a compact row of small buttons directly under the header**, not a grid of large
icon tiles further down the page — that's what keeps it above the fold at 1366×768 once the greeting
hero (which used to occupy this space) is gone. Use `.btn.btn-secondary.btn-sm` + icon, not `.panel`
tiles — a panel-per-action reads as a bigger commitment than "New transaction" already is in the
header.

Sections after that are each a plain `<section>` on the canvas (nothing needs its own `.panel` wrapper
at the section level, per brief — "not a KPI wall"): **Needs attention** (`.panel !p-2` containing a
`<ul>` of `data-href`-less `<a>` rows, each an icon chip + two-line text + chevron) → **Cash & bank, and
what's due** (`grid grid-cols-1 gap-4 min-[900px]:grid-cols-3`: three plain `.panel`s — cash/bank,
receivable, payable) → **Recent activity** (`.table`). Use `min-[900px]:grid-cols-3` (not Tailwind's
default `sm:` 640px) for the balance row — at exactly 768px width three cards wrap awkwardly under `sm:`.

**The cash/bank card is a normal `.panel`, not a navy-filled one.** Calm-tone surfaces first — a
solid `bg-brand` card reads heavier than the rest of the page for what is, functionally, the same kind
of number as the receivable/payable cards beside it. Put the emphasis in the *figure* instead:
`<p class="amount-xl text-brand">…</p>`. Its "Posted balance" pill is a normal `.badge` (`badge-posted`
reads naturally here since the text already says "Posted"), not `.badge-on-brand` — that class is for
the rare case a badge genuinely sits on a solid brand fill, which this no longer is.

---

## Spacing rules

- Page vertical rhythm: `py-5` around the whole page content, `gap-4` to `gap-8` between major sections
  (list-detail pages use a tighter `gap-4`; the home dashboard uses `gap-8` between its five sections).
- Section-to-content: `mb-3` between a `.section-title` and what follows it.
- Field grids: `gap-x-4 gap-y-4`.
- Button clusters (`.doc-actions`, `.page-header-actions`): `gap-2` (`.5rem`, matches the component's own
  built-in gap where one exists — don't re-specify it).
- Card/panel internal padding is fixed by `.panel` (`1.25rem`) and `.doc-header`/`.doc-body`
  (`.75rem`/`1.25rem` block, `1.25rem` inline) — don't override with utility padding on those elements.

## Do / don't

- **Do** start every editable line-items table with one line + "Add line", mark it `data-proposed`.
- **Do** show status with a coloured dot **and** text (`.badge`), never colour alone.
- **Do** right-align and `tabular-nums` every amount (`.num` / `.amount` / `.input-amount` already do this).
- **Do** give every control a `<label for>` + stable `id`, even ones that look self-explanatory.
- **Do** reuse `.panel-actions` for any sticky bottom action bar (split-view detail panel, document editor
  footer if you add one, a `.sheet`'s footer) instead of writing bespoke sticky-footer CSS per screen.
- **Don't** add `style="…"` for anything — if a one-off pixel value is unavoidable, use an arbitrary
  Tailwind value (`max-w-[9.5rem]`) rather than inline style.
- **Don't** write `onclick=`/`onchange=` etc. — add a `data-*` attribute and extend `proto.js`'s delegated
  listener if the behaviour doesn't exist yet (note it in `NOTES.md` so it's not duplicated).
- **Don't** invent a new badge colour for a status that already maps to one of the seven above.
- **Don't** nest a `.panel` inside a `.panel` for visual grouping — use a plain `<section>` with a
  `.section-title`, or a `<div class="border-t border-border pt-4">` divider instead.
- **Don't** use `xl:` for anything that must hold at 1024×768 — the tablet fold is under that breakpoint;
  use `lg:` (1024px) or a custom `min-[…]:` value instead (see the home balance-row and document-editor
  field-grid recipes above for why).
- **Don't** put a Tailwind display/layout utility (`flex`, `inline-flex`, `hidden`, `block`…) on the same
  element as a component class that sets `display` for a breakpoint (`.split-view-list.has-detail`,
  `.split-view-back`, `.company-switcher-panel`'s open/close…). Utilities always win over `@layer
  components` regardless of source order, so the utility silently overrides the responsive behaviour.
  Put the layout utility on an inner wrapper `<div>` instead, or extend the component's own CSS.
- **Don't** write a decorative greeting ("Good morning, {name}") anywhere — DESIGN.md rules it out. A
  page's header is the object's name (or "Home") plus a plain muted meta line, never a sentence.
- **Don't** use `type="date"` — it renders in the browser/OS locale (often mm/dd/yyyy), which breaks the
  app's own date format in every screenshot. Use the `.input-affix` text+calendar-icon pattern instead
  (see Inputs & fields).
- **Don't** underline a standalone UI link by default — `.link` is hover/focus-only now. Only an inline
  link inside a sentence of prose (`.strip a`, `.alert a`) stays underlined at rest.

## Reference screens (read the file, then the rendered screenshot)

- `screens/home.html` — pattern 9, needs-attention list, brand cash panel, quick actions, recent activity.
- `screens/transactions-list.html` — pattern 1, filter bar, selected-row detail panel, posting preview tab.
- `screens/ar-invoice-new.html` — pattern 2, full document editor with editable lines and live totals.
- `screens/components.html` — every component above, rendered with its class name printed underneath.
