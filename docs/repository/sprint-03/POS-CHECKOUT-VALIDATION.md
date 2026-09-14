# POS checkout: local candidate validation

Validated 15 September 2026 (Asia/Karachi), against branch `sprint03/pos-checkout` based on `0c4572427bfaa1d4ac63c44656d78efe25c5b845`. This candidate is local; it has not replaced the published package or public demo. The separate worktree preserves unrelated website work.

## Implemented experience

- Clicking a product tile adds one; repeated clicks increment its quantity. Cart plus/minus controls and Remove update exact totals. Optional quantity typing remains available and does not swallow the next control click.
- Search and category filters preserve the cart. Mobile has a direct View cart action. Selection, empty states, keyboard focus and reduced motion are supported.
- Review sale obtains a read-only server quote. Cash confirmation is a separate screen with exact amount, change and a deliberate Record cash sale action. Edit cart retains quantities, date, cash and checkout identity.
- Enter in search, quantity or cash fields cannot post. Product buttons support keyboard activation. Without JavaScript, quantity inputs and server review/validation remain usable.
- Server-side scope, permissions, readiness, catalog pricing, exact money, duplicate prevention, period locks and atomic posting remain enforced. Unexpected checkout failures retain the original canonical request and matching quote in a company/book-scoped session record. The warning survives refresh; original cash/cart cannot be edited until retry resolves the outcome. Exact committed retries succeed without loading the current catalog; definite validation rejection permits correction again.

## Executed checks

| Check | Result |
|---|---|
| Dedicated PHP 8.5.10 / MySQL 8.4 integration suite | 67 tests, 0 failures |
| Recovery HTTP acceptance | 14 checks, 0 failures; own synthetic session injection, persistent warning, immutable retry, scope/CSRF and one accounting effect |
| POS HTTP acceptance | 29 checks, 0 failures |
| Core HTTP acceptance | 23 checks, 0 failures; HTTP period-lock step skipped because this isolated Compose project uses a separate runtime path |
| Period rejection and rollback | Covered by integration suite, including same-key corrected checkout and late snapshot failure |
| PHP syntax | 58 files passed |
| PHPStan level 5 | No errors |
| JavaScript syntax / Python smoke-script compilation | Passed |
| Git whitespace check | Passed |
| Browser | Chrome at 1440, 768 and 390 pixels; product clicks, keyboard add, cart controls, search Enter, separate review, edit recovery, insufficient cash, exact cash, explicit posting and receipt checked |
| Fallback / accessibility | No-JavaScript server review and cash-error recovery; actual Tab focus visibility; reduced motion; no horizontal overflow in cart/review; compact mobile quantity control |

Browser example: three notebooks at 4.50 and one pen at 1.25 total **USD 14.75**. Confirming **20.00** cash produced **5.25** change and source receipt **126** in isolated synthetic company/book **128**. Review and edit did not create accounting entries; HTTP and integration assertions cover final journal/source linkage and duplicate behavior.

An initial browser assertion used programmatic focus after a mouse click; the corrected check used actual Tab navigation and confirmed the focus outline. A quantity-blur interaction that consumed the next cart button click was fixed and retested. No application JavaScript error was observed; a pre-existing missing favicon returned 404.

Local preview: `http://127.0.0.1:18205/pos`. Runtime and raw evidence live under ignored `.cache/pos-runtime/`; reviewed screenshots are under ignored `output/playwright/` (`pos-cart-desktop-final.png`, `pos-cart-mobile-final.png`, `pos-review-1440.png`, `pos-review-768.png`, `pos-review-390.png`, and receipt images). These temporary artifacts are not package inputs.

## Pre-release recovery review

An independent review found that re-quoting an uncertain sale against a changed catalog could destroy the original retry payload, and that its flash warning disappeared on refresh. The fix adds `POST /pos/retry`, an immutable company/book-scoped session request, and a persistent recovery screen. An unresolved request blocks stale-tab cart edits and new checkout attempts in that book. Committed retry lookup precedes catalog loading, so the original receipt can be recovered after a catalog change or outage. Writer access and readiness remain enforced. A definite rejection returns the original cart for editing; it creates no accounting effect.

The dedicated POS runtime passed the updated **67-test integration suite** and **14 recovery HTTP checks**. New integration tests cover exact request/snapshot preservation and committed retry in a private runtime mirror with no catalog. The HTTP harness injects the already-committed request into its own synthetic session, then checks warning persistence, absent editing controls, stale-tab blocking, CSRF, company/book scope, forged financial fields, same receipt/report totals and recovery from definite insufficient-cash rejection. This proves the recovery workflow using controlled state injection; it is not a real dropped network/commit-response test. The state is session-bound, so after authentication/session expiry the operator must inspect posted transactions before replacing a sale.

Commands: `docker compose -f .cache/pos-runtime/compose.yaml run --rm test php tests/run.php`; `python .cache/pos-runtime/pos-recovery-http-smoke.py --company-id 128 --compose .cache/pos-runtime/compose.yaml`, with private synthetic credentials. The local shared HTTP helper guards port **18205**; committed helpers keep **18200**. Additional checks: changed PHP files linted, Python compiled, PHPStan passed with no errors, and `git diff --check` passed. Runtime logs are `recovery-integration.log` and `recovery-http.log` under ignored `.cache/pos-runtime/`.

## Remaining gates

Representative cashier sessions, true browser 200% zoom, print/hardware testing, pending network-failure visual testing, and a final package install/restore rehearsal have not been repeated for this candidate. Existing package restoration evidence is not claimed as proof of this changed candidate. Country reporting approval, inventory/COGS, tax and real payments remain outside this POS change. Issue #58 stays open until its remaining acceptance gates are met.

## Change record

Changed: `.gitignore`; `www/phpledger/public/index.php`; `www/phpledger/includes/functions/pos_functions.php`; `www/phpledger/templates/views/pos.php`; `www/phpledger/public/assets/pos.js`; `www/phpledger/public/assets/pos.css`; `tests/pos_test.php`; `tests/pos-http-smoke.py`; `tests/pos-catalog-retry-worker.php`; `tests/pos-recovery-http-smoke.py`; `docs/POS.md`; `docs/ROADMAP.md`; this receipt and the Sprint 03 README.

Routes checked: `GET /pos`, `POST /pos/review`, `GET /pos/review`, `POST /pos/edit`, `POST /pos/checkout`, `POST /pos/retry`, `GET /pos/receipt`; core authentication, company setup and transaction routes through HTTP acceptance. Commands: PHP integration runner, local-only HTTP smoke scripts, PHP lint, PHPStan, `node --check`, Python compilation, Playwright CLI and `git diff --check`.

References: local AGENTS, README, architecture, roadmap, design, POS and Sprint 03 scope/validation documentation; Playwright skill. No Google Drive documents read for this candidate. New migrations: **no**. Schema changes: **no**. Raw secrets exposed: **no**. External calls: **yes**, read-only GitHub inspection and tooling retrieval; test requests used localhost. Live/production changes: **no**. No commit, push or deployment performed for this candidate.
