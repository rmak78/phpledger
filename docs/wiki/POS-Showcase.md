# A shop sale with a visible accounting trail

The current POS is a cash-sale showcase retained in the 0.1.2-preview package and hosted demo, using six fictional products. It demonstrates how a simple shop transaction reaches the same accounting records as other receipts. Click-to-add product tiles and a separate review/cash screen are included.

## Try the complete journey

1. Check the business whose books will receive the sale.
2. Search or filter the sample catalog. Click a product to add one; repeat to increase its quantity.
3. Use cart plus/minus or Remove, then choose **Review sale**.
4. Enter cash received or choose **Exact amount**, check the change, then deliberately choose **Record cash sale**.
5. Read the receipt, cash/change and linked journal.

Editing or searching should not silently complete checkout. Invalid input keeps the cart available for correction. Prices and totals are validated on the server; an identical retry returns the same saved sale rather than posting again.

The receipt retains the original product names, quantities and prices. Later catalog changes do not rewrite that snapshot. A linked accounting reversal preserves the original record; it is not yet a stock-return or payment-refund workflow.

## What the showcase includes

Search/categories, a small sample cart, exact cash/change calculation, explicit confirmation, a printable browser receipt and a source-linked journal. Illustrative prices use the selected business's base currency. Reusing the same numerical sample prices across currencies is not exchange conversion or a claim about local retail prices.

## What production retail still needs

Inventory and COGS, purchasing, returns, discounts, taxes, customer credit, payment-provider reconciliation, tills/hardware and end-of-day controls remain future work. The showcase does not collect payments or provide restaurant, pharmacy or fiscal-device compliance.

The next priority is the accounting core. A supported module lifecycle will later separate checkout and industry interfaces from core accounting. Production shop and restaurant capabilities follow the [[module roadmap|Module-Roadmap]]; the current showcase is not an installable add-on.

Uncertain checkout outcomes keep the original cart and cash immutable; **Retry original sale** retrieves its receipt or safely completes the same request. If the session expires, check posted transactions before starting a replacement sale. Production retail follows the dependable inventory/accounting foundation, with realistic checkout load and usability checks. See [[First package|First-Package]] and [[Roadmap]].
