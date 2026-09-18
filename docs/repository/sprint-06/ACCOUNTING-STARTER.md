# Accounting starter: implementation and validation

Status: **0.4.0-preview is published**, 16 September 2026 (Pakistan time). See the [publication receipt](PREVIEW-0.4.0-PUBLICATION.json) for the exact source, archives and live checks. This implementation record is not professional accounting/tax approval.

## Approved scope and ownership

The owner approved AR, AP, Purchasing and Inventory in one next release, then clarified that AR/AP belong in the base accounting core and added the configurable core tax engine. AR and AP have separate internal services/manifests and cannot be disabled. Company owners may hide their navigation without changing permission checks, reports or vertical-module access.

Purchasing and Inventory are bundled optional modules using the existing module lifecycle. Inventory owns products, stock movements and valuation; Purchasing owns orders, receipts and receipt/bill matching. Supplier debt belongs to AP; customer debt belongs to AR. A future distributor module owns routes/deliveries/collections and calls these services rather than maintaining another receivables ledger.

The prior unfinished quote implementation is preserved in commit `e57ea2c` and branch `codex/quotes-plugin`. It is outside the starter branch/runtime. That preservation does not represent a completed or released Quotes plugin.

## Working browser flows

| Route | Behavior |
|---|---|
| `/parties` | Shared customer/vendor identity, both-role parties, draft/profile maintenance and contact entry with existing identity/phone duplicate controls. |
| `/ar`, `/ap` | Draft/review/post invoices and bills, partial/final allocated payments, linked credits, source/ledger drilldown, same-identity corrections, historical ageing and control reconciliation. |
| `/purchasing` | Draft/confirm orders, partial receipts, later bill preview/confirmation, matched quantities, explicit price/rate differences, supplier returns and received-but-unbilled reconciliation. |
| `/inventory` | Product catalogue, one stock location, receipts/issues, counts, reviewed value adjustments, movement history, moving weighted-average valuation and stock-to-ledger checks. |
| `/opening-conversion` | Explicit party mapping and reviewed opening-debt conversion, followed by payment recording against converted items. |
| `/tax` | Manual tax codes, dated rate revisions, tax-account mapping and owner-controlled default inclusive/exclusive price entry. |
| `/modules` | Optional Purchasing/Inventory enablement and owner-controlled AR/AP navigation visibility. |

The existing bank CSV matching/reconciliation remains the bank workflow. New payment journal lines use it without a second bank transaction model. The existing cash POS showcase continues to use its sample catalogue and does **not** deduct shared inventory.

## Accounting contracts

- All financial posting passes through the existing central service under book scope, permission and period checks. Command receipts bind request keys to content. No new HTTP/MCP financial write transport is exposed.
- AR/AP documents own immutable source revisions; outstanding balances come from the shared open-item entries and linked actual journal-line amounts. Corrections append reversing/replacement journals under the same document identity.
- Each payment allocates to one document. Credits are limited to the original document's remaining balance. Advances, unapplied credits, overpayments and cash refunds are not included.
- Stock issue consumes moving weighted-average cost, including the final carrying residual. No negative stock or insertion before a later stock movement for the same item. Customer returns use original issue cost. A physical return and a supplier credit are separate, linked records.
- Goods receipts value inventory at the explicitly reviewed net purchase-order cost/rate and post to received-but-unbilled clearing. AP bills match those receipt quantities. Reviewed price/rate variances use an explicit variance account; receipt costs are never silently rewritten.
- Purchase returns after intervening sales or receipts at other prices can require a reviewed inventory value adjustment. Services reject unexplained negative value or nonzero value at zero quantity. A value adjustment checks expected quantity and carrying value under the book lock.
- Matched Purchasing bills/credits and variance journals reject standalone reversal that would detach their matching history. Converted opening bases and stock journals likewise reject independent reversal. These barriers apply inside the central posting funnel, including internal callers.
- Opening conversion maps the existing domestic unpaid register and inventory basis to explicit parties/products. It requires exact control reconciliation and rejects ambiguous or already-used bases. Conversion links immutable allocations to existing journal lines and posts no second opening balance.

### Core tax calculation

Tax codes, accounts and dated percentage revisions are entered manually. No country tax rates or applicability are inferred. Standard, zero-rated and exempt codes are distinguishable; each requires explicit dated configuration. This first engine uses one selected tax code per line and recoverable input/output tax account mappings.

Price entry defaults to tax-exclusive. The owner can choose an inclusive default; each document retains its reviewed mode. Exclusive entry calculates tax on net; inclusive entry preserves the entered gross and extracts net/tax with exact arithmetic. Display separates net, tax and total. A rate change invalidates an affected draft posting review rather than silently changing its total. Posted documents keep their snapshots, and credits inherit original revision, line, mode and remaining net/tax basis.

Rates use up to six decimal places, money uses four and exchange rates twelve. Currency conversion rounding requires an explicitly selected expense account for a bounded difference (maximum `0.0100`); it is not hidden in a document balance. Existing actual-rate settlement and the outgoing foreign-bank restriction remain in force.

Country packs, nonrecoverable/partial-recovery tax policies, withholding, compound taxes, statutory forms and e-filing are deferred. Advanced stock features (batches, serials, expiry, multiple locations, landed cost, LC and manufacturing) belong to later plugins/extensions.

## Schema and upgrade

Nine additive migrations extend the published 0.3.0 schema:

| Migration | Purpose |
|---|---|
| `017_ar_ap_documents` | Invoice/bill/credit sources, lines, immutable revisions, actions and events. |
| `018_inventory` | Shared catalogue, stock movements, command receipts and opening review records. |
| `019_purchasing` | Orders, receipts, matches, returns and command receipts. |
| `020_opening_conversion` | Scoped opening allocations and immutable reviewed conversion receipts. |
| `021_module_visibility` | Audited company navigation preferences. |
| `022_tax_engine` | Tax codes/rate history and document tax snapshots. |
| `023_inventory_product_audit` | Adds product identity to the existing shared audit classification. |
| `024_opening_allocation_guard` | Enforces non-null exact amounts for opening allocations. |
| `025_tax_price_mode` | Audited tax entry settings and frozen document price mode. |

There are 26 migration receipts after installation (the historical migration names include two `006` entries). All previously published migrations retain their checksums. Stop application/worker traffic and take a matched database/code backup before migration. Do not use the unpublished quote worktree schema as an installation baseline. The authorized hosted demo applied these nine migrations after an isolated restoration and upgrade of its frozen backup; the publication receipt records the live outcome.

## Validation record

The [validation record](ACCOUNTING-STARTER-VALIDATION.json) separates checks from remaining release gates. All data was sample and isolated from the existing development and live databases.

- Final full `composer check` on PHP 8.2.33 and PHP 8.3.33: **225 tests, zero failures on each runtime**. Each run linted 168 PHP files with no failures, passed PHPStan, validated seven sample packs (77 events, 42 documents and 16 items), and rejected eight invalid fixtures. The final two demo template label changes were linted separately; the exact final archive was then verified below.
- Fresh installation: 26 migration receipts. All 17 published migration files retain their exact checksums; index blobs, working LF bytes and fresh receipts agree.
- Populated 0.3.0 upgrade: nine new migrations, 55 data tables and 51 sample rows preserved along with all 17 prior receipts. Existing cash, opening and open-item history remained usable; new partial/final AR settlement and retry reconciled exactly.
- Backup/restore: 79 tables, 10,048 rows, two views, 104 guard triggers and 26 receipts verified, with source links and balanced journals retained. Temporary verifier databases were removed.
- Browser: invoices, partial/final receipts, customer credits, purchase orders, partial deliveries, inclusive supplier bills, matching, supplier returns, stock sales and tax snapshots exercised through the UI. AR, AP, stock and received-but-unbilled reconciliation differences reached zero. Five bank movements reconciled to `884.2500`, with zero unmatched rows and a completed statement.
- Responsive checks: ten new/related route states at 1440, 768 and 390 pixels returned HTTP 200 with no document overflow, clipped navigation, visible PHP errors or JavaScript page errors. Fresh screenshots were captured; the long forms and stacked navigation remain known UX debt for the later full audit.
- Form-rendering/controller checks: 34 assertions covered retained values, failed-preview invalidation, reviewed revisions and keyed lines. Document print output was inspected separately.
- Restricted-demo service smoke: **29 checks passed** under PHP 8.2.33 with the actual `PL_ENV=demo` restrictions and SELECT/INSERT/UPDATE-only database privileges. Same-count and growing AR/PO draft edits worked without DELETE; line removal preserved the existing draft and returned an error. Exact retries succeeded at capacity, compound overflow rolled back financial and stock effects, and new commands were denied at the limit. Public product setup and open-item activation remained disabled.
- Restricted-demo HTTP entry, partial receipts, DEMO5 bill/invoice and partial payments passed in the separate browser fixture. Its expected balances were AR `27.5000`, AP `32.5000` and three stock items valued at `30.0000`.
- Exact final archive: **four scenarios passed** using only freshly extracted package code: fresh installation and upgrade from the published 0.3.0 ZIP on each of PHP 8.2.33 and PHP 8.3.33. All 1,488 manifest files matched. Each installation had 26 receipts; each upgrade applied exactly nine additions and preserved all 17 previous receipts and 16 financial rows in this separate package fixture. Migration replay was empty. Packaged purchasing, AP payment, taxed stock invoicing, partial receipt and linked customer return/credit reconciled AR `6.2500`, AP `60.0000`, stock quantity `9.0000`, stock value `90.0000` and received-but-unbilled `0.0000`. Posting/payment retries returned the prior result, and all four temporary databases were removed.

The verified archive is `phpledger-0.4.0-preview.zip`, source commit `680fa6fa3c7a35e6865207b52f38fbf9a9663947`, 3,179,444 bytes, SHA-256 `b777f9b831db7a6e774e53f510c17ea001514f278ef9a585245ac013dc3aecf6`. Its old-package baseline matched the published 0.3.0 checksum and all 1,453 old manifest files. These are local installation and service results; actual publication and hosted cutover are recorded separately by the release owner.

Local detailed evidence remains in ignored `.cache/starter/` and `output/playwright/`. Checked-in evidence contains sample identifiers and summarized results only. Technical checks do not close professional accounting, observed owner usability or WCAG 2.2 AA review gates.
