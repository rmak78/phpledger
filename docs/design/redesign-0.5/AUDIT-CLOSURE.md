# 0.6.0-preview audit closure checkpoint

18 September 2026. Local implementation and synthetic browser evidence only.
**Publication remains gated.** A closed defect below does not establish accounting
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
| P1 Populated bank reconciliation | Closed locally | Synthetic CSV preview/import, match/unmatch, completion and 26-row cancellation verified with JS/no-JS; no duplicate payment posting. |
| P1 Tax/module/period impact review | Closed locally | Immutable tax definitions/dated rates, consequence confirmations, reasoned period history and stale input checks retained. |
| P1 Invalid OAuth recovery | Closed locally for malformed/expired requests | Request-specific recovery, unchanged 403 rejection and no untrusted redirect verified at four widths. Valid consent/cancel acceptance remains separate. |
| P2 Long report/settings pages | Partial | Compact report directory/settings and paged lists; final history density/scroll review pending. |
| P2 Financial statement links | Closed locally for layout/returns | Explicit link styling and context-preserving account returns; final keyboard coverage pending. |
| P2 Shared navigation | Closed locally for shell layout | Sidebar/rail/drawer replaces clipped navigation stacks; earlier drawer/command-palette checks recorded. |
| P2 Metadata/graph/technical wording | Partial | Token contrast and chart/table presentation improved; final rendered-state and wording review pending. |

The companion setup criteria remain governed by explicit purpose selection,
digest-bound review, exact opening reconciliation and single posting. Their
service checks pass in the full suite; this is not a substitute for the remaining
setup browser-state/field-error/assistive-technology acceptance.

## Release gates still open

- Complete prototype-to-runtime state coverage, visual review and all 38 route receipts.
- Complete remaining field-error findings and final acceptance of correction-preview/purchasing-context changes.
- Legacy CSS dependency removed; finish consolidated visual acceptance after token component retirement.
- Finish representative keyboard/zoom/accessibility checks; record the approval-review rejection honestly.
- Verify the final package on fresh/0.5 upgrade data, supported runtimes and restore path.
- Align version, README, Wiki, help, website, demo, release notes, archive/checksum and media kit; publish only after verification.

The owner has already authorized publication after the gates pass. No extra
publication permission is being requested by this checkpoint.
