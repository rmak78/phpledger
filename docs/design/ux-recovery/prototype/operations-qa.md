# Operations prototype QA

**Status: reviewed local mock; no remaining broken interaction or page overflow found in the tested scope.**

Reviewed on 16 September 2026 in isolated Playwright session `ux-prototype-ops` at `http://127.0.0.1:18216/`. The prototype explicitly labels its synthetic, tab-memory behavior. This review checks visible interactions and layout; it does not validate backend accounting, persistence or permissions.

## Coverage and results

| Area | Widths | Interaction result |
|---|---|---|
| Customers | 1440 / 768 / 390 | List opens invoice detail and Back to list returns. Receipt 10.00 reduces outstanding 27.50 to 17.50; linked credit 5.00 reduces it to 12.50; final receipt settles it to zero. |
| Receipt validation | 390 | An attempted 99.00 receipt against 12.50 outstanding is rejected, with the entered value retained. The receipt button is disabled after settlement. |
| Suppliers | 1440 / 768 / 390 | List/detail navigation works. A 10.00 payment reduces 32.50 outstanding to 22.50. A separate fresh replay settles the full 32.50, and the list says Settled. |
| Purchasing | 1440 / 768 / 390 | Receive 5 items changes received quantity from 0 to 5 and goods awaiting bills from 0.00 to 50.00. Posting the matched bill clears that balance to zero and replaces the posting action. |
| Source links | 390, with desktop settlement comparison | Inventory links open the purchasing and customer details. Open supplier bill opens BILL-0011 directly. Refreshing `#suppliers/detail` retains the detail view. |
| Inventory | 1440 / 768 / 390 | Movement values and sources remain visible. Show count review displays the stated reduction of 1 unit / 10.00 and explains the required reason/account; it does not simulate a stock posting. |

The final combined replay captured **14 states with zero page errors**. Browser traffic remained local. Only the isolated mock state changed.

## Confirmed defects and retest disposition

| Observed defect | Final disposition |
|---|---|
| At 768/390, Customers, Suppliers and Purchasing hid their lists without a route back. Party profile and detailed aging were unreachable. | Fixed by the prototype author during review. Lists now appear initially, rows open details, and Back to list works. |
| A fully settled document still said Part paid in the list while detail said Settled. | Fixed and retested for customer and supplier lists. |
| Open supplier bill and Inventory source links opened a module list on smaller screens. | Fixed with explicit detail destinations; click-through retested. |
| Refreshing a detail hash showed the list because initial state ignored the suffix. | Fixed and retested at 390px on `#suppliers/detail`. |

No reviewer edits were made to `app.js`, `style.css`, `index.html` or runtime code. The author made the fixes; this report records the subsequent browser checks.

## Evidence

- [QA JSON receipt](operations-qa.json) contains final source hashes, six accepted final targeted screenshot hashes, supporting responsive/flow evidence, counts and limitations.
- Originals and capture logs are in `output/playwright/ux-prototype-ops/`. **54 screenshots were inspected**, including initial defect captures, intermediate retakes and final checks. Earlier images remain review history; they do not all represent the final build.
- **39 full-page captures** had PNG width/height checked against their recorded DOM dimensions. All matched; all reported page widths matched their viewport widths. No page-level overflow was observed at the three requested widths.
- Twelve `*-retest-*` images cover the four module routes at all three widths. The PO received/billed captures show the intermediate accounting previews. The `final-*` images verify the targeted fixes.
- A scroll-restoration artifact in the final refresh capture painted an offscreen skip link into the full-page image. DOM inspection confirmed that it was unfocused at `y=-100`. The accepted image was recaptured from the top and inspected. It is not an application defect.

### Accepted final targeted captures

| Capture | Evidence |
|---|---|
| Supplier bill reached from Purchasing | [390px](../../../../output/playwright/ux-prototype-ops/final-supplier-direct-390.png) |
| Supplier list after settlement | [390px](../../../../output/playwright/ux-prototype-ops/final-supplier-settled-list-390.png) |
| Customer invoice reached from Inventory | [390px](../../../../output/playwright/ux-prototype-ops/final-customer-direct-390.png) |
| Customer list after settlement | [390px](../../../../output/playwright/ux-prototype-ops/final-customer-settled-list-390.png) / [1440px](../../../../output/playwright/ux-prototype-ops/final-customer-settled-list-1440.png) |
| Supplier detail after refresh | [390px](../../../../output/playwright/ux-prototype-ops/final-detail-refresh-390.png) |

## Limits and change boundary

- New invoice/bill creation, supplier credits, price/quantity variance handling, real stock adjustments and backend financial correctness were not tested. The mock's intentional simplifications are not treated as shipped accounting behavior.
- No full keyboard, screen-reader, contrast, zoom, touch-device or browser-matrix assessment was performed.
- References read: current prototype `app.js` and responsive CSS; the prior local operations audit; Playwright skill instructions. No Google Drive references were needed.
- Files added by this review: this Markdown receipt and `operations-qa.json`; ignored screenshots, capture logs and local replay helpers.
- Migrations: **no**. Schema changes: **no**. Secrets exposed: **no**. External/live calls: **no**. Production changed: **no**. Staging/commits: **none**.
- Application lint/tests were not rerun because the reviewer changed no application code. JSON parsing, evidence hashes, image dimensions and local report links were checked.
