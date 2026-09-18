# 0.6.0-preview audit closure checkpoint

18 September 2026. Local implementation and sample browser evidence only.
**Published as 0.6.0-preview on 18 September after the owner instructed publication with the outstanding limitations disclosed.** See [publication receipt](../../repository/PUBLICATION-2026-09-18.md). Remaining findings below track follow-up acceptance, not an unpublished release. A closed defect below does not establish accounting
sign-off, observed usability or WCAG conformance. Mobile refinement is deferred
by the owner; page-overflow safety, keyboard access and readable controls are not.

Evidence: [implementation checkpoints](IMPLEMENTATION-NOTES.md),
[state/capture index](evidence-0.6.0/index.json),
[automated sweep](evidence-0.6.0/route-sweep/results.json),
[token contrast](evidence-0.6.0/contrast-tokens.json).
The sweep covers 33 states at four widths, not all 38 routes or 75 prototype entries.
Individual workflow checks cited below are recorded in the implementation log.

## Setup and daily work

Source: [SETUP-DAILY-AUDIT.md](../ux-recovery/SETUP-DAILY-AUDIT.md).
This audit numbers screens rather than assigning each a formal P1/P2 rank.

| Finding | Current disposition | Evidence or remaining work |
|---|---|---|
| 01 Login introduction/contrast | Partial | Focused form and text-token contrast improved; final login/error and assistive-technology acceptance pending. |
| 02 Company cards/density | Implemented, acceptance pending | Compact company rows and separate sample chooser; large-company-list navigation still needs review. |
| 03 Onboarding structure | Implemented, acceptance pending | Six-step flow and explicit chart/purpose choices; complete state evidence remains pending. |
| 04 Validation correction links | Open | Summary focus/value retention exist; full linked field-error coverage is not complete. |
| 05 Preview/confirmation bounds | Partial | Compact setup preview exists; final confirmation/zoom bounds need consolidated evidence. |
| 06 Setup review/shell | Closed locally | Shared shell and compact role mappings; JS/no-JS fold captures pass. |
| 07 Opening expert input/mobile fields | Partial | Bounded balance table, CSV disclosures and sticky preview; richer import mapping is outside this rebuild, keyboard/zoom review pending. |
| 08 Conversion prerequisite | Closed locally | Opening-required state links directly to opening balances; mapping and control preview appear after cutover. |
| 09 Transaction navigation/filters | Closed locally | One GET filter contract, split view and retained return state; broader accessibility acceptance remains separate. |
| 10 Receipt/expense guidance | Closed locally | Compact editor and inline posting preview; local fold captures pass. |
| 11 Saved cash-account substitution risk | Addressed previously, final recheck pending | Existing-account preservation is recorded in earlier recovery work; final edited-source fixture must remain in acceptance evidence. |
| 12 Posted detail/list mismatch | Closed locally | Direct record view and coherent tabs; filters preserved on returns. |
| 13 Posted journal mobile review | Partial | Compact journal and exact totals verified; internal table scrolling still needs final keyboard/zoom review. |
| 14 Journal register/filtering | Closed locally for desktop/tablet | Paged GET status/text filters and split detail; phone column refinement deferred. |
| 15 One-line journal editor | Closed locally | One initial row, Add line, exact live totals and direct post verified with JS on/off. |
| 16 Saved journal amount visibility | Partial | Compact record and exact totals verified at four widths; full scroll-region accessibility pending. |
| 17 Journal edit retention | Closed locally | Draft/edit/cancel/post and list returns verified with JS on/off. |
| 18 Reversed journal traceability | Closed locally | Linked reversal and history verified; immutable service unchanged. |
| 19 POS context/density | Partial | Cashier/context header implemented; final POS state and accessibility evidence pending. |
| 20 POS cash review | Implemented, acceptance pending | Existing exact cash validation retained; earlier flow checks recorded separately. |
| 21 POS receipt/print bounds | Open final gate | Recheck print/action rectangles and final receipt evidence. |
| 22 Historical guide density | Closed locally for layout | Native exercise disclosures and compact panels; actual historical dates retained. Four widths and exercise links passed JS/no-JS. |
| 23 Help feature inventory | Closed locally | Current capability/limit text and correction-first guidance; company shell retained. |
| 24 Missing-route recovery | Closed locally | Safe 404 recovery captured at both fold targets. |
| 25 Wrong-method recovery | Closed locally | Plain 405 message and local links captured; no mutation. |
| 26 Expired demo advice | Partial | Refresh-specific copy and retry added; an actual expired-generation browser capture remains pending. |
| 27 Public chooser | Implemented, acceptance pending | Compact sample entry implemented; full final chooser/error states remain pending. |
| 28 Starter guide | Closed locally for layout | Four-width JS/no-JS captures and invoice exercise link verified; full demo accounting acceptance is a separate gate. |
| 29 Keyboard spot check | Partial | Earlier skip-link/dialog checks exist. Combined nine-pattern command was rejected by automatic approval review without a specific reason; no pass claimed. |

## Operations

Source: [OPERATIONS-AUDIT.md](../ux-recovery/OPERATIONS-AUDIT.md).

| Priority/finding | Current disposition | Evidence or remaining work |
|---|---|---|
| P1 Domestic FX choices | Closed locally | Shared settlement and converted-opening payments hide unnecessary FX controls; service tests cover zero/difference/net-zero cases. |
| P1 Product-first lines/totals | Closed locally for ordinary editors | One-row editors, product assistance and exact totals/previews; correction reversal/replacement preview now implemented and service/browser tested; final visual acceptance remains. |
| P1 Daily tasks precede maintenance/reports | Closed locally | AR/AP/PO/product registers and focused records rebuilt; ageing/reconciliation remain separate accessible tasks. |
| P2 Eligible purchase returns | Closed locally | Receipt/bill return basis and linked credit exercised in purchasing browser flow. |
| P2 Essential phone columns | Deferred refinement; accessibility open | Owner deferred phone refinement. Four-width page-overflow checks pass; labelled horizontal-region keyboard review is still required. |
| P2 Reviewed defaults/accountant controls | Partial | Product defaults, optional party accounting fields and count/receipt previews implemented; remaining manual FX/clearing choices need review. |
| P2 Business labels/source state | Partial | Derived PO receipt states and friendly document status implemented; remaining raw source references need final review. |
| P2 AP bill/credit purchasing context | Closed locally | Bill/credit to purchase-order and exact receipt links, return guidance and linked physical-return credit passed scoped service and JS/no-JS browser checks. Final CSS captures remain pending. |

## Reporting and administration

Source: [REPORTING-ADMIN-AUDIT.md](../ux-recovery/REPORTING-ADMIN-AUDIT.md).

| Priority/finding | Current disposition | Evidence or remaining work |
|---|---|---|
| P1 Offscreen dates/recovery actions | Partial | Four-width report checks and OAuth recovery action bounds pass; 200% zoom acceptance pending. |
| P1 Mobile chart/statement/history | Partial | Compact/paged reports, grouped account balances and clear statement links; complete keyboard/scroll cues pending. |
| P1 Reporting context | Partial | P&L presets and report/account/source return filters verified; nested source actions still require full return-chain acceptance. |
| P1 Populated bank reconciliation | Closed locally | Sample CSV preview/import, match/unmatch, completion and 26-row cancellation verified with JS/no-JS; no duplicate payment posting. |
| P1 Tax/module/period impact review | Closed locally | Immutable tax definitions/dated rates, consequence confirmations, reasoned period history and stale input checks retained. |
| P1 Invalid OAuth recovery | Closed locally for malformed/expired requests; consent/cancel browser checks passed | Request-specific recovery, unchanged 403 rejection and no untrusted redirect verified at four widths. Valid consent and cancel passed with JS on/off at both folds; callback state retained using an intercepted local callback. Token exchange and external client compatibility are separate checks. |
| P2 Long report/settings pages | Partial | Compact report directory/settings and paged lists; final history density/scroll review pending. |
| P2 Financial statement links | Closed locally for layout/returns | Explicit link styling and context-preserving account returns; final keyboard coverage pending. |
| P2 Shared navigation | Closed locally for shell layout | Sidebar/rail/drawer replaces clipped navigation stacks; earlier drawer/command-palette checks recorded. |
| P2 Metadata/graph/technical wording | Partial | Token contrast and chart/table presentation improved; final rendered-state and wording review pending. |

The companion setup criteria remain governed by explicit purpose selection,
digest-bound review, exact opening reconciliation and single posting. Their
service checks pass in the full suite; this is not a substitute for the remaining
setup browser-state/field-error/assistive-technology acceptance.

## Remaining acceptance and original release checklist

- Complete prototype-to-runtime state coverage, visual review and all 38 route receipts.
- Complete remaining field-error findings and final acceptance of correction-preview/purchasing-context changes.
- Legacy CSS dependency removed; finish consolidated visual acceptance after token component retirement.
- Finish representative keyboard/zoom/accessibility checks; record the approval-review rejection honestly.
- Completed for publication: exact package fresh/0.5 upgrade checks on PHP 8.2/8.3/8.4 and local/hosted restoration verification; see the publication receipt.
- Align version, README, GitHub Wiki, repository About (description, website URL and topics), version/package manifests, website download/share metadata, help, demo, release notes, archive/checksum and media kit; publish only after verification. Record each published surface as updated or reviewed unchanged, with verification evidence, in the publication receipt and owner handoff.

The original checkpoint above was followed by the owner instruction to publish the current preview with its limitations disclosed. Package/accounting/CI checks passed; the CLA was personally signed by the owner. GitHub, demo, website, Wiki and metadata are now published and verified.

## 0.6.1 local workflow follow-up — 18 September 2026

This section records later local work; it does not change the historical 0.6.0
publication receipt. The current registry has **41 HTML GET paths**, rather than
the 38 in the 16 September inventory: `/home`, `/sample-chooser` and
`/reports/ageing` were added. `/`, `/tables` and `/reports/export` remain redirect,
JSON and CSV surfaces respectively. Browser installation and maintenance/update
operations are separate pre-router entry points and require their own evidence.
The prototype directory contains 75 HTML entries, including its component sheet
and prototype index; that number is not 75 independently verified runtime states.

The focused workflow implementation and browser checks now cover cash and journal
field recovery, save/edit/post/reversal returns with JavaScript on and off,
invoice/purchase input recovery, and ageing date/direction through document,
journal, settlement selection and correction-editor returns. Linked field hints
cover the main cash, journal, invoice/bill and purchase-order editors. Domain-state
errors still use the general server message. Evidence:
`output/playwright/workflow061-validation.json` and matching workflow captures.
These checks close the exercised nested return chains, not every state in the
original audits or every form's field-error coverage.

A read-only smoke pass requested all 41 current route paths at 1366×768,
1024×768, 768×1024 and 390×844, producing 164 captures. After authenticating and
selecting the existing sample company, the browser blocked all methods except
GET/HEAD; no blocked financial write was attempted. Evidence:
`output/playwright/workflow061-route-smoke-auth.json` and
`output/playwright/workflow061-route-auth-*.png`.

- 32 route paths rendered their direct ordinary screen. No page overflow,
  JavaScript exception, unexpected console error or marked desktop/tablet
  primary-action fold failure was observed in those states.
- Five paths exercised 403 recovery only: `/transactions/edit` without a draft
  ID, `/general-journals/detail` without an ID, `/pos/receipt` without a sale ID,
  `/sample-guide` in an ordinary company and `/oauth/authorize` without a request.
  Their expected 403 resource messages are retained in the evidence.
- Two paths redirected as expected: authenticated `/login` to `/companies`, and
  `/pos/review` without a cart to `/pos`.
- Two paths showed fallback states: `/transactions/detail` without an ID showed
  the register; `/general-journals/edit` without an ID showed a new editor. These
  are not edited/posted document acceptance.
- The initial run used an expired session and is explicitly rejected for route
  acceptance in `output/playwright/workflow061-route-smoke.json`; its sign-in
  redirects are not passes. No original publication evidence was overwritten.
- The ageing phone capture was visually inspected. The sweep is automated smoke
  evidence, not a visual comparison of every capture or screen-reader acceptance.

The following current-state work remains before claiming complete 0.6.1 UI
acceptance. Existing focused checks and older receipts remain useful evidence;
they must be mapped to the actual state rather than inferred from a route visit.

| Routes / lane | Required remaining state evidence |
|---|---|
| `/login`, `/companies`, `/sample-chooser`, `/onboarding`, `/setup/review` | Failed/throttled sign-in and resume; empty/large/reader company lists; each purpose/chart step, retained validation, preview, repeated/stale confirmation and completed setup. |
| `/opening-balances`, `/opening-conversion` | Expanded import, row errors, balanced and mismatched preview, explicit party mapping, confirmed cutover/allocation history, duplicate/stale confirmation and keyboard/zoom review. |
| `/transactions/edit`, `/transactions/detail`, `/general-journals/edit`, `/general-journals/detail`, `/journals/detail` | Map the focused JS/no-JS draft/post/reversal checks to current prototype states and final four-width captures; add stale revision, unavailable account, reader permission and non-cash/non-AR journal source variants. |
| `/ar`, `/ap`, `/purchasing` | Full current partial/final settlement and reversal states; linked customer/supplier credit and correction/cancellation histories; goods receipt, variance/tax bill preview and billed/unbilled returns; retained invalid FX/clearing choices and stale preview recovery. Ordinary invoice/order error and return checks do not cover all of these. |
| `/parties`, `/inventory`, `/accounts` | Duplicate/edit validation, role/contact variants, inactive/empty/reader states; stock opening/count/value-adjustment previews and results, stale quantities, disabled-module retained history; account history/immutable-classification review. |
| `/reports*`, `/bank-reconciliation` | Final keyboard/200% zoom and scroll-region review; empty/loss/invalid periods; source-return variants for settlements, stock and opening journals; current populated bank import/match/unmatch/completion/cancellation states reconciled against the existing functional receipt. |
| `/periods`, `/tax`, `/modules`, `/connections`, `/oauth/authorize` | Current stale/permission/error states, period history, dated tax/inclusive settings, module impact confirmations, connection creation/revocation and valid consent/cancel/resume. Earlier valid OAuth evidence is separate from this invalid-request smoke pass. |
| `/pos`, `/pos/review`, `/pos/receipt` | Populated cart and keyboard operation; reviewed tender/change; successful receipt and print/action bounds; duplicate/stale/unconfirmed outcome recovery. The empty-cart redirect and missing-sale denial do not cover these. |
| `/sample-guide`, public demo, global errors | Historical and starter guide states; public chooser/reset countdown; actual expired-generation recovery; missing-route/wrong-method and contextual safe returns at current dimensions. |
| All affected screens | Prototype-to-runtime state mapping, visual inspection, 200% zoom, keyboard focus/scroll access and assistive-technology review. Phone visual refinement remains distinct from overflow/access safety. |

For exact prototype reconciliation, 42 entry IDs were absent from the old
33-state sweep: `ar-credit-note`, `ar-invoice-draft`, `ar-invoice-posted`,
`companies`, `components`, `demo-transactions`, `demo`, `error-not-found`,
`error-unavailable`, `error-wrong-method`, `expense-edit-dirty`, `goods-receipt`,
`index`, `journal-entry`, `journal-posted`, `journal-reversed`,
`journal-unbalanced`, `login-error`, `login`, `oauth-consent`, `oauth-invalid`,
`onboarding-business`, `onboarding-chart`, `onboarding-confirm`,
`onboarding-preview`, `onboarding-purpose`, `onboarding-validation`,
`opening-balances`, `opening-conversion-confirm`, `opening-conversion-preview`,
`period-drilldown`, `pos-receipt`, `pos-recovery`, `pos-review`, `pos`,
`readiness-strip`, `sample-chooser`, `sample-guide`, `setup-review`, `stock-count`,
`transaction-posted`, `transaction-reversed`. `components` and `index` are
prototype-only entries. This is an inventory of unmatched old sweep IDs, not a
claim that all 42 features are absent or that later workflow evidence is missing.
