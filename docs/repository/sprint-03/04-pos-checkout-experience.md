The sample POS proves the cash-sale accounting path, but its layout and checkout flow need a more deliberate, compact experience. Make catalog browsing and cart editing fast while keeping confirmation clearly separate from ordinary keyboard input.

## Scope

- Refine the catalog, categories/search, cart and sale-review hierarchy using the agreed product identity and readable financial figures.
- Add a clear staged path from cart review to cash confirmation and posted receipt, with the selected business, total, tender and change visible at the appropriate step.
- Preserve server-resolved prices, exact decimals, immutable receipt snapshots, company/book permissions and the central posting service.
- Keep drafts/errors recoverable, and keep pending, rejected and posted states understandable on desktop, tablet and mobile.

## Acceptance

- [ ] Search and quantity/tender edits do not post a sale when Enter is pressed; only deliberate confirmation reaches checkout.
- [ ] Keyboard navigation and touch targets support the complete catalog → cart → review → cash confirmation → receipt journey.
- [ ] Invalid cash, changed catalog, denied access or a closed period gives a useful explanation and preserves recoverable cart input without a partial journal.
- [ ] An identical retry or double confirmation creates exactly one sale/receipt/journal; changed content with a reused key is rejected.
- [ ] Receipt totals, exact cash/change, source links and journal/report effects reconcile in the selected company's base currency.
- [ ] Desktop, tablet and mobile browser checks cover normal, empty, pending and error states; representative user sessions record task completion and the fixes needed.
- [ ] Receipt print output remains readable, and posted sales cannot be edited or removed through the interface.

## Boundary

Keep this candidate to a sample cash-sale showcase. Actual payment collection, stock/COGS, returns/refunds, tax, discounts, credit accounts, specialist-industry flows and till hardware remain outside this issue. A linked accounting reversal is not a retail return workflow.

Part of the [first package plan](https://github.com/rmak78/phpledger/wiki/First-Package).

## Linked work

The completed checkout experience and its evidence feed [#59 candidate acceptance](https://github.com/rmak78/phpledger/issues/59) and the artifact assembled by [#56 the installable package](https://github.com/rmak78/phpledger/issues/56). Preserve the accounting boundaries described by [#57 the reporting workstream](https://github.com/rmak78/phpledger/issues/57).
