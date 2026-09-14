# General-shop POS showcase

The user added POS to the current showcase scope during Sprint 02. This is a working cash-sale journey for a small synthetic shop catalog, backed by the existing accounting services. It is not an inventory, tax, payment-processing, or production retail release. The same six illustrative prices are expressed in the selected company's base currency; this is not exchange-rate conversion.

## Customer-facing flow

Open **Point of sale**, search/filter the six products, add or edit quantities, and review the cart. Enter the sale date and cash received. Checkout records one cash receipt, its balanced journal, and an immutable product/price snapshot together. The receipt shows the sale total, tendered cash, change, source transaction, and journal, and can be printed using the browser.

An explicit **Record cash sale** action is required. The browser guards against implicit Enter submission while editing, and the route requires the named checkout intent before reaching the accounting service. This prevents an ordinary search/quantity edit from being treated as cash-sale confirmation. The report/POS redesign requested by the user remains outstanding and publication is on hold.

The screen explicitly names the business whose books will receive the entry. Products/prices are illustrative; the application does not collect money. Owners/accountants can check out only when the company is ready. Viewers may read a scoped receipt. Public-demo visitors retain the existing private-company isolation, transaction-capacity limits, hourly expiry/reset, and server permissions.

Changing quantity to zero or removing an item changes only the unposted cart. A failed checkout preserves the cart, date, cash amount, and request identity for correction/retry. A saved sale cannot be edited or deleted. The existing linked accounting reversal retains the receipt and original item prices; this is not an inventory return/refund workflow.

## Interfaces and exact accounting

- Browser routes: `GET /pos`, `POST /pos/checkout`, and `GET /pos/receipt?id=<document-id>`. All URLs use the existing base-path helper, including under `/demo`; POST must check CSRF and submitted company/book scope before invoking the service.
- `pl_pos_catalog()` reads `resources/core/pos-catalog.json`: `general-shop-showcase` version `1.0.0`, six original fictional products with exact decimal prices and a digest of the file.
- `pl_checkout_pos(actorId, companyId, bookId, input)` accepts `checkout_key`, `catalog_digest`, `date`, `items` (a list of product code/quantity pairs), and `cash_received`. It rejects supplied price/total fields. `pl_get_pos_receipt(...)` returns the scoped receipt snapshot and its existing source document/journal.
- Use each SKU once, quantities 0–99, at most six catalog products and 200 units total. Zero quantities are omitted; an empty sale is invalid. Prices and totals are resolved on the server with BCMath/exact decimal strings. Cash must cover the total; the journal debits cash/bank and credits sales income for the **sale total**, with tender/change recorded on the receipt.
- New checkout requires the displayed catalog digest to match the current catalog. The confirmed item name/category/unit-price/quantity/line-total snapshot remains unchanged after later catalog updates. Request normalization sorts products and normalizes money; an identical retry returns the same receipt, while the same checkout key with different content conflicts.
- The existing book lock serializes concurrent checkout attempts. `pl_save_document()`, `pl_post_document()`, and the POS snapshot insert share one enclosing transaction. Missing accounts, closed periods, insufficient cash, access denial, or a late snapshot failure cannot leave a partial sale/journal.

## Storage and limits

Additive migration **004_pos_showcase** adds `pl_pos_sales`, keyed by its existing `pl_documents` source, with company/book scope, currency, total/cash/change, checkout identity, catalog metadata/digest, and the item snapshot. Two triggers reject changes/deletion of these posted snapshots. The separate **005_demo_period_guard** migration contains the demo period-control guard; existing applied migration checksums must not be rewritten.

There is one cash/bank role and one income category in this showcase. Stock quantity, reservations, purchasing, inventory valuation, cost of goods sold, tax, discounts, credit/customer balances, restaurant operations, payment providers, hardware integrations, fiscal-device compliance, and actual refunds are not implemented by this module. Cheque and receipt-document scanning remain separately deferred work.

## Validation status

The eight POS tests passed in the combined **54-test target-runtime suite**: exact price/change and source linkage; canonical duplicate/conflict handling; forged-price/invalid-quantity/stale-catalog/insufficient-cash rejection; permission/company/book/readiness isolation; closed-period rollback; injected late-snapshot rollback; two-process duplicate checkout; and reversal preserving the original price snapshot. Strict exception/message assertions ensure the injected database rejection is the reason the rollback test passes.

Local PHP syntax and JavaScript syntax checks passed. The latest independent local HTTP suite passed **21 checks** in synthetic company/book 3: checkout of two notebooks and three pens produced source document 14, **USD 12.75** total, **20.00** cash, **7.25** change, and one balanced journal. Duplicate checkout returned the same receipt; invalid CSRF, missing/invalid explicit confirmation, altered scope, forged total, insufficient cash, changed-content retry, GET checkout, and logged-out access were rejected. Missing/invalid confirmation had no report effect. Failed checkout preserved the cart. This synthetic sale and the earlier 17-check run's source 13 remain for inspection.

Run `python tests/pos-http-smoke.py --company-id <synthetic-company-id>` with private `PL_HTTP_EMAIL`/`PL_HTTP_PASSWORD` environment variables after creating an `HTTP Acceptance` company with the core HTTP script. It only targets local port 18200 and leaves one posted synthetic sale. Browser cart/filter/keyboard/mobile/print behavior, final restoration evidence, and public deployment remain separate gates. See [Validation](VALIDATION.md) for the exact receipts and limits.
