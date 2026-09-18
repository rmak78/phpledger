# Cash account selection integrity - local fix review

Status: implemented and checked locally; not committed, packaged or published.

Worktree: `C:/phpledger/.cache/ux-integrity-fix`, branch `codex/cash-account-integrity`, based on released application commit `680fa6fa3c7a35e6865207b52f38fbf9a9663947`. Checks below ran on 16 September 2026 UTC. The shared regional-program checkout and hosted application were not changed.

## Problem and resulting behavior

The receipt/expense editor listed only the account whose semantic key was `core.cash_bank`. The service correctly accepts all scoped, active asset accounts with operational role `cash_bank`. Historical sample packs and ordinary account administration can create additional valid cash accounts without that unique semantic key.

Consequently, reopening the service-agency pack's `PRACTICE-PETTY` draft omitted its saved Petty cash account. The browser selected the first available option, Cash and bank. A memo-only save could replace the draft's cash account; a subsequent reviewed posting would then use that replacement. GET requests did not mutate the draft or posted history. This is a high-priority accounting-input integrity issue, broader than the historical fixture.

The local fix:

- Lists all active asset/cash-bank accounts supplied by the existing company/book context.
- Keeps a valid saved or submitted cash account selected, including Petty cash.
- Shows an unavailable prior account's scoped label or numeric identity and an empty required choice. Save requires a deliberate valid replacement; invalid or missing failed-form values never default to the first bank.
- Aligns category choices with the existing income/expense operational-role checks and blocks an unavailable or mismatched category with the same explicit selection behavior.

The service's company/book, active-account and purpose checks remain the write authority. No posting mechanics, permissions, schema, migration or accounting history was changed.

## Files

- `www/phpledger/templates/views/editor.php`: account eligibility and unavailable-selection presentation.
- `tests/demo_pack_test.php`: two regressions rendering the real template from the multi-cash historical fixture, plus supporting test helpers.
- `tests/ledger_test.php`: shared cancellation-date fixture and explicit default-date / denied-backdate assertions.
- `tests/concurrency_test.php`, `tests/concurrency_worker.php`, `tests/core_test.php`, `tests/currency_test.php`, `tests/document_test.php`, `tests/module_test.php`, `tests/period_test.php`, `tests/report_test.php`: remove expired cancellation dates while preserving the financial, concurrency and historical-report assertions.
- This review receipt.

## Reversal-test date correction

The initial targeted runs exposed an existing fixed-date test failure; the first full runs then exposed six more instances of the same assumption. An ordinary cancellation on `2026-09-15` becomes a backdate after that day. The service correctly rejects a backdate that is neither the original posting date nor the permissioned owner exception.

The test-only correction creates an original posting two days before the current UTC cancellation date and an open accounting period containing both dates. The fiscal-end choice accommodates New Year and leap day. Tests retain exact balances, source identity, retry behavior, concurrent duplicate prevention and reports before/after the reversal. The central reversal test now explicitly checks the default cancellation date and rejects a non-original prior date before making the permitted ordinary reversal. The FX metadata test had the same defect with a `2026-09-16` cutoff and uses the same fixture.

The closed-period negative test now uses the original journal date and checks the specific open-period error. This proves the period guard instead of accidentally accepting a backdate-policy error that also contains the word "period". No service clock, posting rule, permission check or runtime implementation was changed by this test correction.

## Verification

The browser/targeted tests used a dedicated temporary MySQL 8.4 container. Final full-suite runs used two further disposable MySQL 8.4 containers, one per PHP runtime, independent of the shared audit and demo databases. Runtime versions were checked directly with `php -v`: PHP 8.2.33 and PHP 8.3.33. All three temporary database containers and the browser web container were removed after their checks; evidence files remain in the isolated worktree.

| Check | Result |
|---|---|
| PHP 8.2.33 full suite | **227 tests, zero failures.** |
| PHP 8.3.33 full suite | **227 tests, zero failures.** |
| PHP lint, both runtimes | 168 application, test and tool files; zero failures. All ten changed test files also passed explicit PHP 8.2 `php -l`. |
| PHPStan, both runtimes | No errors. |
| Sample data, both runtimes | Seven packs, 77 events, 42 documents and 16 items valid; all eight deliberately invalid in-memory packs rejected. |
| New editor regressions, both runtimes | Both passed: actual rendered selection survives save/post; unavailable, foreign, wrong-purpose, missing and malformed selections cannot silently replace the account. |
| Same editor tests against released template | Both regressions failed as expected: four valid cash choices collapsed to one; unavailable selection submitted the first bank. |
| Browser flow, separate local PHP 8.5.10 instance | Sign in; select sample company; open petty-cash draft; save a memo-only edit; confirm account unchanged; deactivate that sample account; confirm visible warning, empty required choice and blocked Save; deliberately choose Reserve bank and save successfully. |
| Responsive browser checks | 1440x1000, 768x1024 and 390x844 screenshots inspected. Mobile document width equaled viewport width. |
| Diff whitespace | `git diff --check` passed. |

Before the date correction, the surrounding `--suite=demo-packs` runs on PHP 8.2.33 and 8.3.33 each completed **22 tests: 21 passed, one failed**. The failure already exists in the released code: `tests/ledger_test.php:149` supplies a fixed reversal date of `2026-09-15`, now before the current UTC date and different from the original posting date. The reversal policy correctly rejects it. The original-template comparison had that same failure plus the two new regression failures. An additional PHP 8.5.10 targeted run showed the same result. The first full-suite runs after the single ledger-test correction each had 227 tests and six remaining fixed-date failures. The final full checks above include all date-fixture corrections; reversal policy remains unchanged.

Final ignored local evidence is in `.cache/check-php82.txt`, `.cache/check-php83.txt`, and `.cache/changed-test-lint.txt`. Initial failure evidence is preserved in `.cache/full-php82-before-date-cleanup.txt`, `.cache/full-php83-before-date-cleanup.txt`, `.cache/demo-packs-php82.txt`, `.cache/demo-packs-php83.txt`, `.cache/demo-packs-php85.txt`, and `.cache/demo-packs-released-template.txt`. Browser captures are `output/playwright/cash-editor-*.png`. Browser snapshots and commands remain under `.playwright-cli/`. The only browser console error observed was the existing missing `/favicon.ico` resource. PowerShell formatted PHPStan's stderr configuration notice as `NativeCommandError` in the combined logs; separate native-exit checks confirmed analysis exit code 0 on both runtimes, with stdout/stderr preserved as `.cache/analyse-php82-*.txt` and `.cache/analyse-php83-*.txt`.

## Adjacent review and release recommendation

General-journal editing already preserves unavailable account identities and has a placeholder; no equivalent semantic-key filter was found there. Receipt/expense categories had a placeholder, preventing first-option substitution, but previously offered some roleless accounts that the service rejects; the local category filter now agrees with the service.

A separate AR/AP usability finding is unchanged: both settlement HTML and `pl_settle_open_item()` require realised gain/loss account IDs even for a domestic zero-difference payment. Hiding those fields alone would not change the service contract. That issue needs its own bounded decision and tests.

Recommend a small maintenance release for the cash-account fix after integration review. The date-sensitive fixture failures are corrected and the local full-suite matrix is clean. Do not mix regional catalogue research into that release. Packaging, upgrade proof, hosted cutover and live verification have not been performed for this patch.

References read: released AGENTS, README, architecture and roadmap; document, account and demo-pack services; receipt/expense and general-journal templates; existing demo-pack tests. No Google Drive references were used. Migrations: no. Schema changed: no. Raw secrets exposed: no. External/live application calls: no; browser and database checks used local sample services only. Live/production changed: no. Only sample local fixtures were changed. No commit, push, package or deployment was made.


## Root integration review

Reviewed and committed locally on `codex/cash-account-integrity`: `81e90d2` (date fixtures), `f940548` (cash-account editor and regressions). The running application and published package remain unchanged. This receipt stays local and untracked under the owner's documentation policy.
