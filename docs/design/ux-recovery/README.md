# PHP Ledger: local research and workflow recovery

**Status: research and design review ready; implementation acceptance pending.** Recorded 16 September 2026. This folder stays local and untracked at the owner's request. The existing 0.4.0-preview release and marketing pack are already published; this programme is not part of that release.

## Open the review

- [Clickable workflow prototype](prototype/index.html) — static HTML/CSS/JavaScript, synthetic state in the current tab only.
- [Screenshot gallery](index.html) — current-run accepted screenshots, grouped with findings.
- [Route and state inventory](ROUTE-COVERAGE.json) and [coverage gaps](COVERAGE-GAPS.md).
- [Setup, daily work and sample audit](SETUP-DAILY-AUDIT.md).
- [Customer, supplier, purchasing and inventory audit](OPERATIONS-AUDIT.md).
- [Reporting and administration audit](REPORTING-ADMIN-AUDIT.md).
- [Prototype design QA](prototype/design-qa.md) and [independent operations QA](prototype/operations-qa.md).

Local review server: `http://127.0.0.1:18217/`; prototype: `http://127.0.0.1:18217/prototype/`. Only this review folder is served, bound to loopback. It is not a public publication. If stopped, run `python -m http.server 18217 --bind 127.0.0.1 --directory C:/phpledger/docs/design/ux-recovery`.

## What the audit establishes

The registered application exposes 41 GET routes: 38 HTML surfaces, one redirect, one CSV download and one JSON table-data endpoint. Every HTML path has at least one captured current-run state. This does **not** mean every possible state or action was exercised. The inventory separately names pre-router health, API, MCP and metadata surfaces.

Captures use the released application, isolated synthetic companies, and user-approved isolated Playwright at 1440, 768 and 390px widths. They cover ordinary/empty/error/validation/success views, draft and reversed records, historical reports, and the local public-demo chooser. Each accepted screenshot was inspected. Credentials and browser storage were kept in ignored private test files.

### Highest-impact findings

| Priority | Observed problem | Proposed response |
|---|---|---|
| P1 | Receipt/expense editing omitted valid non-default cash accounts, allowing a saved Petty cash choice to become Cash and bank on Save | Separate local integrity patch prepared and tested; see [receipt](../../repository/sprint-06/CASH-ACCOUNT-INTEGRITY.md). The original audit remains unchanged. |
| P1 | Domestic AR/AP settlements require realised FX gain/loss accounts in both HTML and service validation | Define a zero-difference domestic path in the service, then simplify the form. Hiding fields alone is insufficient. |
| P1 | Mobile journal review can hide debit/credit while exposing Post; several actions/date fields clip off the left edge | Keep amounts and consequences in every review layout; verify individual control bounds as well as document width. |
| P1 | Invoice/order entry starts with five fixed blank line groups and omits known product defaults/running totals | Start with one product-driven line, explicit tax basis and continuously visible totals. |
| P2 | Stacked global/company/sample/module navigation and large report panels delay daily tasks | One context header, one scalable navigation model, focused list/detail workspaces. |
| P2 | Help still describes newly released AR/AP and inventory as future work | Update the runtime help from bounded release evidence during the accepted implementation pass. |

### Strengths to preserve

The general-journal editor already has useful mobile debit/credit cards. Account statements and report/source links retain meaningful accounting context. The P&L-to-account drill-down preserved its date range. Opening imports show explicit posting boundaries, and public samples clearly identify their fictional, isolated, temporary nature. The redesign reuses these strengths.

## Research delivered

| Workstream | Local artifact | State |
|---|---|---|
| Five countries: Pakistan, India, UAE, UK, US | [Country evidence](../../coa/regional-program/COUNTRY-PROVENANCE.md), [38-source register](../../coa/regional-program/SOURCES.json) | Research; 21 candidate account groups, six observed byte hashes; no accountant approval or runtime importability |
| Eleven industries and sample-company plan | [Industry/sample review](../../coa/regional-program/INDUSTRY-AND-SAMPLE-REVIEW.md) | All eleven profiles; proposed deterministic two-year history plus practice year; existing fixtures unchanged |
| Accounting review | [19 exact review cases](../../coa/regional-program/ACCOUNTING-REVIEW-CASES.json) | Expected journals, statement/control effects and ambiguous cases; not professional sign-off |
| Catalogue/setup contract | [Versioned installation contract](../../coa/regional-program/CATALOG-AND-INSTALLATION-CONTRACT.md) | Six statuses, composition, immutable snapshots, semantic roles, preview/retry rules and source/review gates proposed |

The proposed first accounting-review wave is Pakistan/India with services/retail. That is a review recommendation, not an approved release matrix. No country or industry package has been promoted to Released. Currency remains independent from country/legal form/tax compliance. Existing `core.cash_bank` bindings remain intact; candidate names require explicit compatibility mapping.

The eleven-company programme is not loaded or expanded yet. The four historical packs and seven one-month fixtures remain source material. A future loader must reconcile opening balances without double posting, commit atomically, reject real-company targets and retry idempotently. The current public demo's 100-record capacity is an unresolved policy dependency for deep histories.

## One proposed design system, eight lanes

| Lane | Clickable direction | Required engineering/acceptance work |
|---|---|---|
| 1. Setup and migration | Six steps; explicit country/currency/legal form; neutral choice; research restriction; chart-name/code preview; past-record choice; confirmation | Accept catalogue contract before any migration. Implement source hashes, stale previews, duplicate-safe confirmation, exact import differences and immutable snapshots. |
| 2. Daily bookkeeping | Selected draft → edit → saved review → post → linked reversal, under the same identity | Preserve all account choices/entered values; enforce permissions, periods, revision conflicts and posting through existing services. |
| 3. Customer/supplier | Outstanding list/detail, partial payment, credit review, ageing and control links | Service-backed domestic settlement rules; product-driven lines; over/duplicate allocation and credits tested. Supplier credits need a fuller prototype/acceptance case. |
| 4. Purchasing/inventory | Order → receive → matched bill; stock source movements and count effect | Preserve GRNI/stock/AP atomicity; explicit mismatch and physical-return paths; prevent unavailable defaults and duplicate receiving. |
| 5. Tax/administration | Inclusive/exclusive default; manual dated sample code; hide navigation separately from activation | Keep posted snapshots immutable; enforce module dependencies and action permissions; no inferred national rules. |
| 6. Reports/reconciliation | Shared dates → account → source; control comparison; proposed bank match | Real filters and authoritative calculations; populated bank matching, imports, exports and reconciliation tests. |
| 7. POS/samples | Cash cart → receipt; distinct eleven-company exploration chooser | Keep sample/real identity unmistakable; preserve reset consequences; no provider calls. Deep histories require approved loader and capacity design. |
| 8. Shared system | Compact header, single navigation, status/action hierarchy, labelled forms, mobile list/detail, error/loading/success examples | Keyboard/focus/labels/contrast/zoom/touch/reduced-motion testing on actual implementation, plus observed owner/accountant usability. |

The prototype is a bounded interactive design document. Its examples are separate exercises, not one reconciled runtime company. It has no backend, persistence, authentication, ledger, import execution or tax-compliance behaviour. It deliberately does not implement invoice/bill production workflows a second time.

## Validation and limits

- Release checks remain in the [0.4.0 publication receipt](../../repository/sprint-06/PREVIEW-0.4.0-PUBLICATION.json): 225 tests on PHP 8.2/8.3, exact archive fresh/upgrade checks and live synthetic journey evidence.
- Separate local integrity patch: 227 tests, zero failures on both PHP 8.2.33 and 8.3.33; 168-file lint and PHPStan passed. Two rendered-form regressions fail against the original editor. No patch package, upgrade or live cutover has been performed.
- Prototype: JavaScript syntax check; desktop/tablet/mobile captures; setup walkthrough, saved draft, tax-mode default, visibility, cash receipt/change, receipt/credit/settlement, purchase/receipt/bill and source-link interactions. Independent operations replay completed 14 states with zero page errors.
- Accessibility spot checks: visible skip-link focus and actual focus transfer to `main`; setup labels/landmarks; Inter loaded; reduced-motion emulation. Full screen-reader, browser-zoom, touch-device, keyboard journey and WCAG 2.2 AA review remain pending.
- Important state gaps: valid OAuth consent, populated bank reconciliation, populated opening conversion, some new AP/credit/permission/module-disabled states. These are listed in the route inventory; screenshots do not establish their correctness.
- Research JSON, source/reference links, exact case arithmetic and original fixture validation were checked by the assigned agents. Source-access, rights and accountant-review limits remain explicit.

## Repository and publication boundary

Local commit `45fbcd7` records the owner's decision to stop tracking `docs/`, while keeping all files on disk; README/Contributing links point to the published documentation revision. It has not been pushed. The existing separate Quotes worktree/branches and unrelated `Claude outputs/ERPNEXT-REVIEW.md` deletion were preserved.

The integrity patch is isolated at `C:/phpledger/.cache/ux-integrity-fix`, branch `codex/cash-account-integrity`: `81e90d2` updates reversal test dates, `f940548` preserves cash choices. Neither is deployed or mixed into the research checkout.

References: repository README, architecture, roadmap, design/brand and strategy records, current routes/services/templates/tests; primary country/professional/upstream sources are enumerated in the provenance register. No Google Drive references were used. Migrations/schema changes in this phase: **none**. Raw secrets exposed: **no**. External calls: public reference reads and release-download verification; no messages/provider writes. Production changes in this phase: **none**; the earlier authorised 0.4.0 app/website/media publication is complete.

## Next decision

Review the catalogue contract and this coherent workflow direction before catalogue/schema/loader engineering. Keep accounting review, technical verification, observed usability, accessibility and production readiness as separate gates. The immediate maintenance priority is releasing the validated cash-account fix; domestic settlement configuration and focused invoice entry follow in the accepted UX implementation pass.
