# A shop sale with a visible accounting trail

The current POS is a working local cash-sale showcase using six fictional products. It demonstrates how a simple shop transaction reaches the same accounting records as other receipts. Its screen and checkout presentation are being refined for the next package.

## Try the complete journey

1. Check the business whose books will receive the sale.
2. Search or filter the sample catalog and add items to the cart.
3. Adjust quantities and review the sale total.
4. Enter cash received and deliberately choose **Record cash sale**.
5. Read the receipt, cash/change and linked journal.

Editing or searching should not silently complete checkout. Invalid input keeps the cart available for correction. Prices and totals are validated on the server; an identical retry returns the same saved sale rather than posting again.

The receipt retains the original product names, quantities and prices. Later catalog changes do not rewrite that snapshot. A linked accounting reversal preserves the original record; it is not yet a stock-return or payment-refund workflow.

## What the showcase includes

Search/categories, a small sample cart, exact cash/change calculation, explicit confirmation, a printable browser receipt and a source-linked journal. Illustrative prices use the selected business's base currency. Reusing the same numerical sample prices across currencies is not exchange conversion or a claim about local retail prices.

## What production retail still needs

Inventory and COGS, purchasing, returns, discounts, taxes, customer credit, payment-provider reconciliation, tills/hardware and end-of-day controls remain future work. The showcase does not collect payments or provide restaurant, pharmacy or fiscal-device compliance.

The next POS pass will make the catalog, cart, review and cash confirmation more deliberate and readable. Production retail follows the dependable inventory/accounting foundation, with realistic checkout load and usability checks. See [[First package|First-Package]] and [[Roadmap]].
