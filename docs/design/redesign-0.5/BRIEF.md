# PHP Ledger 0.5 — interface redesign brief (prototype phase)

Owner decisions (17 Sep 2026): prototype first, then rebuild the PHP templates. Tone **Claude-calm**. **Light theme now, dark-ready tokens.** Desktop + tablet only (mobile ignored for now). Tailwind approved. References the owner loves: **Frappe / ERPNext / Frappe Books** (layout: sidebar workspace, compact list views with filter bar, form views with sticky header actions) and **Akaunting** (clean document forms: invoice header, line table, totals block). Feel: calm, precise, uncluttered — "more like Claude".

The #1 requirement: **most actions complete above the fold.** The fold is **1366×768** (common SMB laptop, Pakistan-first market) and **1024×768** tablet landscape. Also sanity-check **768×1024** tablet portrait.

## Where things are

- `/home/claude/proto` — the prototype. `node build.mjs` → `dist/index.html` (open `#/<screen-id>`). `node shots.mjs <ids…> [--sizes=1366x768,1024x768,768x1024] [--full]` → viewport PNGs in `shots/` + a fold/overflow/JS-error report. **Look at your screenshots with the Read tool** and iterate until they are genuinely good.
- `/home/claude/src` — read-only copy of the current app. Current templates: `www/phpledger/templates/views/*.php`, `templates/layout.php`; HTML is partly built in `www/phpledger/includes/functions/*web_functions.php`; routes in `www/phpledger/public/index.php`. Audits: `docs/design/ux-recovery/*.md` (+ `ROUTE-COVERAGE.json`). Current-state screenshots (1440 wide): `docs/design/ux-recovery/evidence/*-1440.png`. Brand: `docs/BRAND.md`. Design history: `docs/DESIGN.md`. An earlier static prototype with useful workflow ideas: `docs/design/ux-recovery/prototype/`.
- **Content must be faithful to what the app actually does.** Read the current template + web functions + audit notes for each screen you design: keep every real field, action, status and safety message (e.g. "Posting adds this transaction to reports. A saved draft does not change your books."). You may re-organise, rename for clarity, and move secondary things into panels/tabs/menus. Do not invent features that don't exist; if a clearly-needed small improvement is proposed (e.g. running totals, "Add line"), mark it with `data-proposed` and a short `title` attribute explaining it. The audits list P1/P2 fixes — apply their proposed responses.

## File conventions

- One screen = one file `screens/<id>.html`, starting with a meta comment:
  `<!--meta {"id":"ar-invoice-new","title":"New invoice","route":"/ar?new=invoice","group":"Sales","nav":"invoices","crumbs":[["Sales","#/ar"],["Invoices","#/ar"],["New invoice"]],"pattern":"document-editor","layout":"app","state":"Draft, 3 lines"} -->`
  - `layout`: `app` (shell with sidebar/topbar), `auth` (centered focused page, no sidebar: login, companies, onboarding, errors, oauth), `bare` (no wrapper: POS full-screen, screen index).
  - `nav` must match a `data-nav` id in the sidebar (see IA below).
  - Multiple states of one route get separate files (`ar-list`, `ar-invoice-new`, `ar-invoice-posted`…). Route field = real route.
- Links between screens: `href="#/<screen-id>"`. Clickable table rows: `<tr data-href="#/<id>">`.
- Icons: `<i data-icon="receipt"></i>` or `<i data-icon="receipt" class="size-4"></i>` → inlined Tabler outline SVG at build (Tabler is already the app's icon set). Names: https://tabler.io/icons (check `node_modules/@tabler/icons/icons/outline/<name>.svg` exists; build prints MISSING ICONS).
- **Production constraints to respect now so the port is easy:** no inline `style=""`, no inline `on*=` handlers, no external requests. Behaviour only via `data-*` attributes handled in `src/proto.js` (existing: `data-toggle="<id>"` toggles `hidden`, `data-dismiss` inside `data-dismissible`, `data-sidebar-toggle`, `data-href` rows). Native `<details>`/`<dialog>` are fine.
- **RTL-ready (Urdu is next):** use logical utilities only — `ms-/me-/ps-/pe-/start-/end-/text-start/text-end/border-s/border-e/rounded-s…`. Never `ml-/mr-/pl-/pr-/left-/right-/text-left/text-right`.
- Mark must-be-visible elements with `data-fold="<label>"` (primary action, document total, filter bar, first rows…). `shots.mjs` fails any that land below the fold at the checked size.
- Every form control has a `<label for>` and stable `id`. Visible focus. Status never by colour alone (dot + text). Amounts right-aligned, `tabular-nums`. Touch targets ≥ 36px on tablet widths.

## Visual system — "Claude-calm"

Tokens live in `src/app.css` (`@theme` + CSS variables on `:root`, so dark mode later only redefines variables). Owned by the foundation step; screen builders use the utilities/components, and ask (by leaving a note in `NOTES.md`) rather than inventing new colours.

- Canvas `#FAF9F7` (warm off-white), surface `#FFFFFF`, subtle surface `#F4F3F0`, border `#E8E6E1`, strong border `#D6D3CC`.
- Text `#1F1E1C`, muted `#6B6862`, faint `#9A968E`.
- Brand navy `#0C2052` (designer-specified): primary buttons, active nav text, headings accent. Blue `#4656E8`: links, focus ring, selected row tint `#EEF0FF`, active indicator. Charcoal `#424242` (designer) for logo text contexts.
- Semantic (separate from brand): posted/success `#1F7A4D` on `#E8F4EE`; warning/overdue-soon `#9A6200` on `#FBF1DE`; danger/overdue `#B42318` on `#FDECEA`; info `#2F5BD3` on `#EEF2FD`; draft = neutral stone.
- Type: **Inter** (the app's approved face, variable file in `src/fonts`). Base 14px/20px; small 12.5px; page title 20px/600; section title 15px/600; big money figures 22–28px/600 tabular. Letter-spaced 11px uppercase eyebrow only where it encodes something.
- Shape: controls radius 6px, cards/panels 10px; hairline borders instead of shadows; one soft shadow only for popovers/dialogs. No gradients except the logo. No emoji. Not everything is a card — lists and forms sit on the canvas with dividers; panels only where separation means something.
- Density: inputs/selects 34px tall (36px at tablet), table rows 38–40px, sidebar items 32px.

## Information architecture (the shell)

**Sidebar** (240px; collapses to a 56px icon rail via a toggle and automatically at < 1180px; at < 900px becomes an overlay drawer opened from a topbar menu button):
1. Company switcher block at top (Frappe/Slack style) — business name, "Primary book · PKR", `Sample` badge; opens a menu: switch business, add business, business setup. This **replaces** the current separate company bar.
2. `home` Home
3. Daily work: `transactions` Receipts & expenses · `pos` Point of sale (module) · `journals` Journals
4. Sales: `invoices` Invoices (AR) · `customers` Customers
5. Purchases: `bills` Bills (AP) · `purchase-orders` Purchase orders · `suppliers` Suppliers
6. Inventory: `products` Products & stock
7. Banking: `bank-reconciliation` Bank reconciliation
8. Reports: `reports` All reports · `profit-loss` Profit & loss · `balance-sheet` Balance sheet · `trial-balance` Trial balance · `account-statement` Account statement · `cash-forecast` Cash forecast
9. Setup (collapsible group near bottom): `accounts` Chart of accounts · `tax` Tax codes · `opening-balances` Opening balances · `opening-conversion` Opening documents · `periods` Periods · `modules` Modules · `connections` Connections & API
10. Footer: `help` Help · version "0.5.0-preview".

Module-gated items (POS, purchasing, inventory) show only when enabled — in the prototype show them all.

**Topbar** (48px, sticky): sidebar toggle · breadcrumbs · search/jump field "Search or jump to…  Ctrl K" · **+ New** menu (Expense, Receipt, Invoice, Bill, Journal entry, Purchase order, Customer or supplier, Product) — quick-create from anywhere · "Sample guide" link (sample companies only) · user menu (email, switch business, sign out).

**Context strips** (only when true, each ≤ 32px, dismissible where allowed): public demo notice with reset countdown; setup readiness ("Opening balances required — Reconcile opening balances before posting. Review setup →").

## Page patterns (build every screen from these)

1. **List + detail (split view)** — Frappe list view + Review Console. Sticky page header: title, count, primary action. Filter bar in one row: status segmented tabs (Drafts / Posted / Reversed / All or Unpaid / Overdue / Paid…), search, date range, "Filters" popover, export. Dense table with sticky header, row selection highlights; right detail panel (~420–480px) shows the selected record: header (number, status, amount), key fields as a compact definition grid, tabs Details / Journal / History, and the record's actions pinned at the panel bottom. Pagination footer compact. At 1024 the panel overlays/narrows; at 768 detail replaces the list with a back link.
2. **Document editor** (invoice, bill, expense/receipt, journal, purchase order, goods receipt) — Akaunting clarity + Frappe sticky header. Sticky header: doc type + number (or "New"), status badge, dirty/save state text, actions (Cancel · Save draft · Post/Submit primary). Body: header fields in a 4-column grid (3 at tablet); **line items as an editable table** starting with ONE line and "Add line" (not five blank groups); product pick fills description/price/account/tax; running totals block (subtotal, tax, total, amount due) right-aligned under the table and always visible; posting preview (debit/credit) in a collapsible right panel or tab. Helper text as short muted hints or an info popover, not paragraphs. A 3-line invoice with totals and Post button must fit at 1366×768.
3. **Record view** (posted/reversed documents, journal detail, receipt): header with status + primary follow-up action (Record payment, Reverse, Print/receipt), summary grid, lines, journal effect, related documents (payments, reversal link), history timeline. Reversed records show the linked reversal prominently.
4. **Report** — filter bar (as-of / from–to, compare, basis) + Export; statement table with indented groups, subtotals, totals; amounts link to account statement (drill-down keeps the date range); "Posted figures only" note where relevant. Report fits width without horizontal scroll at 1024.
5. **Wizard / guided flow** (onboarding 6 steps, sample chooser, setup review, opening balances, opening documents import): left or top stepper, one focused panel, Continue/Back pinned in a footer bar that's always visible; previews show counts/totals and the explicit confirmation boundary.
6. **Settings list** (periods, modules, tax codes, connections): compact table/list with inline state and row actions, side sheet or inline expand for editing.
7. **Focused/auth** (login, businesses, demo, errors, OAuth consent): centered, calm, brand visible, no sidebar.
8. **POS** (full-screen, touch): product grid + categories + search on the left, cart with qty steppers/remove, totals, cash tendered/change, Checkout — all visible at 1024×768 without scrolling.
9. **Home** — daily start page (not a KPI wall): needs-attention list (drafts to post, overdue invoices, bills due, unreconciled bank lines, setup readiness), cash & bank balances, receivable/payable due summary, quick actions, recent activity; each item links to its source. Only data the app can already compute (see `reports.php` overview + AR/AP ageing).

## Sample data (sample, clearly labelled)

Main sample business: **"Ravi Textile Traders"** (Sample), Primary book, **PKR**, owner "Ayesha Khan". Customers e.g. Lahore Fabric House, Bismillah Garments, Karachi Linen Co.; suppliers e.g. Faisalabad Yarn Mills, Sialkot Packaging, K-Electric (utilities), PTCL (internet). Numbering style from the app: `INV-000194`, `BILL-000041`, `EXP-000590`, `REC-000588`, `JRN-000112`, `PO-000017`, `GRN-000009`. Tax code sample "GST 18% (sample, manual)". Amounts realistic for a small PKR trader (e.g. invoice PKR 185,400.00). Dates around Sep 2026. Keep all numbers internally consistent (lines sum to totals, debits = credits, ageing buckets sum).

## Quality bar

Before you finish: build, screenshot every screen you own at 1366×768 and 1024×768 (plus 768×1024 for a couple), **read the PNGs**, fix what looks cramped, misaligned, clipped, or generic. No horizontal overflow, no JS errors, primary actions above the fold. Report back: screens delivered (id → route → pattern), fold results, anything you could not fit above the fold and why, and any proposed (`data-proposed`) changes.
