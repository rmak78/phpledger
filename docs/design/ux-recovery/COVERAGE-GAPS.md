# Current route coverage and remaining audit states

**Local review artifact, 16 September 2026. Keep local and untracked as instructed by the owner. No implementation or production acceptance is implied.**

All **38 HTML GET route paths** have at least one accepted screenshot state at **1440, 768 and 390px**. This is route coverage, not complete workflow coverage: the authorization page, for example, is represented only by its invalid-request state. There are no wholly uncaptured HTML route paths in the current registry.

The machine-readable [route and state inventory](ROUTE-COVERAGE.json) lists every active canonical GET surface, its source, accepted evidence and selected meaningful missing states. It also records image hashes and unchanged original paths. The three source audits remain the authority for their visual findings:

- [Setup, daily bookkeeping and samples](SETUP-DAILY-AUDIT.md)
- [AR/AP, parties, Purchasing and Inventory](OPERATIONS-AUDIT.md)
- [Reporting, reconciliation, tax and administration](REPORTING-ADMIN-AUDIT.md)

## Counts and counting rules

| Measure | Count | Interpretation |
|---|---:|---|
| Explicit web-route entries, all methods | 54 | Current `www/phpledger/public/index.php` registry |
| Web routes accepting GET | 41 | 38 HTML, one redirect, one CSV download, one JSON data endpoint |
| HTML routes with an accepted captured state | 38 of 38 | All three widths; successful workflows and other states can still be missing |
| Additional canonical GET endpoints before the HTML router | 16 | Health, two metadata endpoints, OpenAPI, MCP and eleven authorized API reads |
| Total canonical GET surfaces | 57 | 41 web-registry entries plus 16 pre-router endpoints |
| Accepted original screenshots mapped | 186 | 177 full-page images plus nine focused viewport images |
| Full-page state groups at all three widths | 59 | Includes query variants and global errors; these are not extra routes |
| New screenshots in this coverage pass | 6 | Account create/detail, prefix `60-` |

`/tables` is **JSON used by existing tables**, not a separate HTML data explorer. Its handler explicitly sets `application/json`. `/reports/export` is a CSV attachment and `/` redirects. This corrects the preliminary count of 39 HTML screens made before inspecting the table handler.

The two OAuth metadata handlers use prefix matching; their canonical paths are counted once. Unknown wildcard API paths, metadata suffix aliases, static assets, static marketing pages and the proposed prototype on port 18216 are not additional product screens. GET requests to POST-only actions are error cases, not newly registered GET screens.

The accepted evidence is from the real local application on port 18211 and the isolated restricted demo on port 18212, with the latter explicitly identified in the inventory. It is not production evidence. The original output files remain unchanged under `output/playwright/ux-audit/`. Rejected initial account-statement and expanded-receipt captures are excluded.

## HTML route map

Evidence group IDs below expand to exact paths, dimensions, capture URLs, hashes and audit ownership in [ROUTE-COVERAGE.json](ROUTE-COVERAGE.json). A group normally has one image at each of the three widths. Focused keyboard/detail images are additional evidence, not replacements for those captures.

| HTML route | Accepted state groups | Important limit |
|---|---|---|
| `/login` | `01-login`, `27-demo-chooser` | Normal sign-in and restricted-demo chooser; failed login/resume missing |
| `/companies` | `02-companies` | Owner with three companies; no empty/reader/large-list state |
| `/onboarding` | `03-onboarding`, `04-setup-validation`, `05-setup-preview` | Initial, retained-input validation and review; creation not confirmed in this visual audit |
| `/setup/review` | `06-setup-review` | Opening-required business only |
| `/opening-balances` | `07-opening-balances` | Initial entry; expanded CSV, balanced preview and confirmed cutover missing |
| `/opening-conversion` | `08-opening-conversion` | Empty before cutover; mapping and confirmed allocation views missing |
| `/transactions` | `09-transactions` | Draft list and selection; full filters/paging/navigation sequence missing |
| `/transactions/new` | `10-expense-new` | Blank editor; receipt variant/error/save sequence missing |
| `/transactions/edit` | `11-expense-edit` | Existing expense; saved cash-account preservation needs investigation |
| `/transactions/detail` | `12-transaction-detail` | Posted historical receipt; draft/reversed standalone states missing |
| `/journals/detail` | `13-journal-detail` | Posted simple-source journal; settlement/stock/opening sources missing |
| `/general-journals` | `14-general-journals` | Populated register; empty/reader/search interactions missing |
| `/general-journals/new` | `15-journal-new` | Initial responsive line editor; add/remove/error focus needs testing |
| `/general-journals/edit` | `17-journal-edit` | Reopened saved draft; stale and failed-save states missing |
| `/general-journals/detail` | `16-journal-draft-success`, `18-reversed-journal` | Saved draft and historical reversal; new post/reverse sequence not performed |
| `/ar` | `30-ar-list`, `30-ar-new`, `30-ar-draft`, `30-ar-posted`; selected-product/keyboard details | Partial settlement is fixture state; no successful payment/credit/correction audit sequence |
| `/ap` | `30-ap-list`, `30-ap-bill` | Partially settled bill; new/draft bill, credit and successful payment sequence missing |
| `/parties` | `30-parties`, `30-party-form` | Register/new form; edit, contacts and duplicate recovery missing |
| `/purchasing` | `30-purchasing-list`, `30-purchase-new`, `30-purchase-order`, `30-purchase-receipt` | Partial receipt and matched bill visible; bill preview/variance and return results missing |
| `/inventory` | `30-inventory`, `30-inventory-product`, `30-inventory-count` | Stock product and count form; new/nonstock/opening/value-adjustment results missing |
| `/accounts` | `50-accounts`, `60-account-new`, `60-account-detail` | List/new/detail captured; history expansion, save and validation missing |
| `/reports` | `50-reports` | Hub; no empty/unready/reader variants |
| `/reports/account` | `50-account-statement-ready`, `50-period-drilldown`; viewport details | Actual date-preserving P&L drill-down; full search/paging/source-return sequence missing |
| `/reports/trial-balance` | `50-trial-balance` | Populated report; phone date field has confirmed negative-left clipping |
| `/reports/profit-loss` | `50-profit-loss` | Populated report; empty/loss/invalid-period states missing |
| `/reports/balance-sheet` | `50-balance-sheet` | Populated report; empty/alternative dates and earnings trace missing |
| `/reports/cash-forecast` | `50-cash-forecast` | Default manual scenario; changed/invalid-input/result states missing |
| `/bank-reconciliation` | `50-bank-reconciliation` | Empty import entry only; populated matching flow remains a major gap |
| `/periods` | `50-periods`; viewport details | Closed/open periods and history; create/close/reopen interactions missing |
| `/tax` | `50-tax` | Synthetic manual code and exclusive setting; inclusive/error/history variants missing |
| `/modules` | `50-modules` | Enabled/disabled configuration visible; dependency impact and toggle results missing |
| `/connections` | `50-connections` | Empty connection history; consent, credential and revocation states missing |
| `/oauth/authorize` | `50-oauth-invalid` | **Invalid request only**; valid consent is uncaptured |
| `/pos` | `19-pos` | Empty sample cart; broader cart/keyboard/disabled cases missing |
| `/pos/review` | `20-pos-review` | One-item cash review; retry/stale/invalid variants missing |
| `/pos/receipt` | `21-pos-receipt` | Actual local synthetic sale; print not invoked |
| `/sample-guide` | `22-sample-guide`, `28-starter-guide`; `29-keyboard-skip` | One historical guide plus starter; other historical guides not separately captured |
| `/help` | `23-help` | Current page; stale feature copy is documented, not silently corrected |

Supplemental global states are `24-not-found` (404), `25-wrong-method` (405 on the POST-only save route), and `26-public-demo` (local expired-generation 503). They do not establish every route's access/error recovery.

## Highest-priority remaining state coverage

These are **audit gaps**, not claims that the features are absent or broken. Existing service tests or earlier release receipts must not substitute for the missing current-state UX evidence.

| Priority | Lane and missing states | Required evidence before claiming the redesigned flow is covered |
|---|---|---|
| P1 | Opening balances to operational conversion | Expanded import, row errors, reconciled preview, explicit party mapping, confirmed cutover and allocation history. Trace one opening journal; conversion must not post it again. Include stale preview and ambiguous source recovery. |
| P1 | Bank reconciliation | A populated statement from preview through candidate selection, saved match, removal, outstanding items and completed/reopened history. Keep bank amount/date/source visible and reconcile the displayed difference. |
| P1 | AR/AP collection and payment | Complete one domestic partial payment, final payment, reversal and linked credit on both sides. Capture retained values/errors and correction identity. Existing partial balances are fixture observations, not completion evidence. |
| P1 | Receipt to supplier bill and return | Review matched quantities, net clearing, tax and explicit variance; then show billed and unbilled return results and linked AP credit. Include stale receipt/bill preview and fully matched eligibility. |
| P1 | Accounting choice preservation | Investigate the saved Petty cash versus Cash and bank edit discrepancy before making a cosmetic redesign claim. Capture valid saved choice, unavailable choice and safe recovery without silent substitution. |
| P1 | Narrow-screen action visibility | Recheck setup confirmation, POS print, trial-balance date and OAuth/demo recovery actions after approved changes. Measure element bounds, not only whole-page scroll width. Show amounts before post/confirm actions. |
| P2 | Authorization and administration | Valid OAuth consent/decline/resume, an existing scoped connection, expiry/revocation, module dependency conflicts, reader permissions and restricted-demo behavior. Keep credentials out of screenshots. |
| P2 | Stock and master-data maintenance | New/nonstock products, stale physical counts, stock opening preview, party duplicate errors, account history and inactive records. Capture reason/revision handling and preserved posted history. |
| P2 | Tax and foreign-currency entry | Inclusive/exclusive examples, date-effective/manual tax rates, manually entered document and settlement rates, and understandable validation. Do not imply tax filing, providers, foreign bank settlement or revaluation are delivered. |
| P2 | Reporting and accessibility | Empty/custom periods; keyboard search/order/pagination; report-to-source and back with context; 200%/400% zoom, measured contrast, screen-reader announcements and focus recovery. Two skip-link checks do not cover these tasks. |

Shared permission/scope, stale revision, duplicate action, empty/large-volume and interrupted-session cases are also recorded per route or in the inventory's shared-gap lists. The list is deliberately finite and prioritized; it does not enumerate every possible data combination.

## New read-only account captures

The independent `ux-reporting` session was reopened against the synthetic Cedar Studio company on port 18211. Only session/company selection and read-only navigation changed. No Create account, Save account or deactivation action was submitted. The session was closed after capture.

### New account: clear fields, accountant choices precede the business name

**Task/context:** add a chart account, `/accounts?new=1`. [Desktop](evidence/60-account-new-1440.png), [tablet](evidence/60-account-new-768.png), [phone](evidence/60-account-new-390.png) were captured and visually inspected before these notes.

**Strengths:** explicit labels, active-status meaning, change reason and Create account action are clear. On tablet/phone the list disappears and Back to accounts provides a route out. Fields and the primary action stay inside the viewport; the mobile action fills the available width.

**Friction:** code, classification and operational purpose appear before account name. A non-accountant begins with three technical decisions, and the duplicated default wording "Expense - Expense" adds little explanation. The desktop keeps the complete twenty-row chart beside the short form, extending the page to 2070px. The phone shell consumes roughly 294px before Back, and the account-name field is near the bottom of the first 1000px viewport.

**Acceptance direction:** lead with the account name and explain its purpose, then offer a reviewed classification/purpose choice with accountant detail available. Preserve the existing immutable classification contract and required reason. Keep the selected company visible without repeating four navigation/context strips.

**Limits:** no classification changes, validation, keyboard completion, save, contrast measurement or screen-reader test was performed. The accepted full-page sizes were 1440x2070, 768x1091 and 390x1210; no document-level overflow, page exception or PHP error text was recorded. These measurements do not prove all controls are accessible.

### Existing account: identity protected, inspection starts in an edit form

**Task/context:** inspect Cash and bank, `/accounts?id=5511`. [Desktop](evidence/60-account-detail-1440.png), [tablet](evidence/60-account-detail-768.png), [phone](evidence/60-account-detail-390.png) were captured and visually inspected.

**Strengths:** code, classification and purpose are clearly fixed, with guidance to use a new account and reviewed correction for a classification change. Revision 1 is visible. Only name, active status and reason are editable; Open statement is a direct action. On phone, both Save and Open statement are full-width and visible without horizontal scrolling.

**Friction:** a user inspecting an account immediately receives a Save form rather than a concise balance/activity summary. The chart already offers statement links, but the detail gives more visual emphasis to maintenance than inspection. History is collapsed, and the active-status control does not explain the effect on existing module defaults at this point. The desktop retains the full list and long unused right-column space.

**Acceptance direction:** make account inspection and statement/history the first view, with an explicit permissioned edit action. Keep the fixed identity rules and change reason, and explain or preview the impact of deactivation on new entries and configured defaults.

**Limits:** history was not expanded and no account was changed. Full-page sizes were 1440x2070, 768x1091 and 390x1215; the same narrow visual/geometry checks passed. Inactive records, stale revision, failed save and read-only permissions remain uncaptured.

## Nonvisual GET evidence

Fresh read-only HTTP checks used the same local synthetic browser session. Safe summaries are embedded directly in the JSON inventory; no cookies, tokens, CSRF values or full response bodies are included.

| Surface | Observed result | What remains unverified |
|---|---|---|
| `/` | Authenticated normal session: HTTP 303, Location `/companies` | Anonymous and demo-authenticated redirect responses |
| `/tables` without scope | HTTP 400 JSON with changed-company recovery text | Other invalid/denied variants; not an HTML page |
| `/tables?table=transactions&...` | HTTP 200 JSON; 3 filtered rows, 29 total records | Full search/sort/paging contract and empty/error variants |
| `/tables?table=account&...` | HTTP 200 JSON; 25 rows, 49 total records | Bank/general-journal variants and exact running-balance parity |
| `/reports/export?report=trial-balance&to=2025-12-31` | HTTP 200, UTF-8 CSV attachment with expected report filename/header; 32 lines | Financial/content parity, other three export types, spreadsheet formula-safety and access errors |
| `/health` | HTTP 200 JSON with `status: ok` | Unavailable dependency and method rejection |

The eleven API read paths are companies, capabilities, accounts, transactions, general-journals, journal, source-detail, trial-balance, profit-loss, balance-sheet and account-statement. These and the MCP/metadata/OpenAPI handlers were inventoried from the current shared catalogue and HTTP dispatcher. **No connection credential was created or used and those endpoints were not exercised in this coverage pass.** The MCP HTTP boundary accepting GET is not proof that a stream succeeds without the required protocol/session context.

## Validation and boundaries

- Parsed the current registry and checked that every one of its 41 GET paths occurs exactly once in the inventory; enumerated the additional 16 canonical pre-router endpoints separately.
- Resolved every referenced accepted image, read its PNG dimensions and verified byte equality to its original output file. The inventory includes all 186 accepted current-run images and excludes prototype/rejected images.
- Inspected all six new account captures; the other accepted images retain the explicit visual-review ownership in their source audits.
- Validated JSON syntax, route/evidence references, image hashes, full-width group counts and local Markdown links. No application tests or PHP lint were needed for these documentation/evidence-only changes.
- Earlier source audits include a saved synthetic journal draft and local practice POS sale. This coverage pass added **no financial or settings mutation**. It did not repeat those actions or treat them as blanket workflow proof.
- No application, migration, schema or test file changed; no raw secrets were exposed; no live application/provider calls or production changes were made; no files were staged or committed.

The next review should select the P1 state sequences above, then test the accepted prototype's proposed interactions against those cases. A complete route map is a useful baseline; it is not usability acceptance, financial correctness, accounting review or accessibility conformance.
