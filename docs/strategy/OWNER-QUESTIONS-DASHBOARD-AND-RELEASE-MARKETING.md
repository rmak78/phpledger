# Owner questions dashboard and release marketing plan

## Superseding owner scope: 16 September 2026

The next release bundles AR and AP in the required accounting core, with separate Purchasing and Inventory modules in the same delivery. Inventory remains a basic shared stock service. Quotes move to a separate optional plugin and are excluded. A manually configured core tax engine supplies codes, dated rates and inclusive/exclusive entry; country rules, forms and e-filing follow separately. See [the current starter implementation record](../repository/sprint-06/ACCOUNTING-STARTER.md). Earlier phased quote/stock sequencing below is historical and does not override this scope. Published 0.3.0 capability claims remain unchanged until the next release is published.


**Status:** Planned; implementation queued
**Date:** 16 September 2026
**Product:** PHP Ledger

## Purpose

Organise the product around six questions a business owner, accountant, or bookkeeper needs to answer:

1. Who owes me money, and how old is it?
2. Who do I owe?
3. What stock do I have, and what did it cost?
4. What is in the bank?
5. What numbers do I need for my tax return?
6. Can I give my customer an invoice or receipt?
7. Can I send a quote to a potential client who asked for one?

The dashboard is a summary and navigation layer over authoritative accounting services. It must never become a second balance store or calculate financial totals from browser data. Every displayed number must drill down to source records and, where applicable, the posted journal.

## Current capability boundary

| Question | Current status | Honest current claim |
|---|---|---|
| Receivables and ageing | Not operational | Opening unpaid-document evidence exists, but customer subledgers, invoices, settlements, allocations, and ageing are not shipped. |
| Payables | Not operational | Vendor bills, settlements, allocations, and payable ageing are not shipped. |
| Stock and cost | Not available | The POS showcase does not deduct stock or post cost of sales; inventory valuation is not shipped. |
| Bank position | Partial | Book cash/bank balances and bank CSV reconciliation are available; this is not a live bank-feed claim. |
| Tax-return numbers | Not available | The ledger and exports provide evidence, but tax calculation, statutory returns, and regional connectors are not shipped. |
| Customer documents | Receipt partial; invoice not shipped | The cash POS can print a receipt. Formal invoices, credit notes, statements, and customer balances require AR. |
| Quotes | Not available | Quotes are pre-sale documents. They need their own status, expiry, acceptance, and explicit conversion path; a quote alone does not post to the books. |

The current preview must continue to describe receivables, payables, inventory, cost of sales, tax, and formal invoicing as future modules until their release gates pass.

## Product implementation sequence

### Phase 1: Receivables and payables

Build the shared open-item and allocation model, then expose the operator workflows:

- customer and vendor parties and contacts;
- invoice and bill drafts and posted documents;
- due dates and payment terms;
- partial and full settlements;
- allocations, credit notes, advances, and corrections;
- customer and vendor balances;
- ageing buckets: current, 1–30, 31–60, 61–90, and 90+ days;
- opening unpaid-document conversion without double-posting opening evidence;
- permissioned drill-down from summary to party, document, settlement, and journal.

The correction model is reversal plus corrected repost under the same document identity, preserving immutable history and the existing posting controls.

### Phase 2: Invoices, bills, and receipts

Complete the customer-facing document layer:

- quotes with draft, sent, accepted, declined, expired, and cancelled states;
- quote validity date, version, customer, line items, prices, taxes, terms, and notes;
- explicit operator conversion of an accepted quote into an invoice, preserving the quote link;
- no revenue, receivable, tax, stock, or bank posting from a quote alone;
- printable invoice, bill, receipt, and statement outputs;
- business, customer, supplier, item, tax, currency, and payment-term fields;
- document numbering and source links;
- payment status and outstanding amount;
- credit notes and corrected documents;
- explicit operator-triggered download or delivery only;
- document, payment, and journal consistency checks.

The existing cash POS receipt remains a valid earlier capability, but it must not be described as a formal invoice.

Quotes should appear in the dashboard as non-posted sales-document work, separate from receivables:

- quotes awaiting response;
- quotes expiring soon;
- accepted quotes not yet converted to invoices;
- quoted value, explicitly labelled as non-posted;
- conversion rate only when status history is retained and the metric is defined.

An accepted quote is not automatically a sale. Conversion to an invoice, delivery, or cash sale remains an explicit reviewed action.

### Phase 3: Inventory and cost of sales

Connect purchasing and sales to stock and the ledger:

- products, units, and locations where required;
- purchases and stock receipts;
- stock movements and adjustments;
- documented valuation method, such as weighted average or FIFO;
- quantity on hand and value at cost;
- sales deduction, returns, and corrections;
- cost of goods sold and gross margin;
- stock-to-ledger reconciliation.

Product selling price must never be presented as inventory cost. Quantity, cost value, and sales value must be separate measures.

### Phase 4: Tax reporting and jurisdiction adapters

Start with a generic tax-reporting contract and add jurisdiction-specific adapters only after review:

- tax classifications and tax periods;
- input/output tax summaries;
- sales and purchase registers;
- withholding and exception reports where applicable;
- return-oriented exports linked to source transactions;
- FBR digital-invoicing client as the first researched adapter where validated;
- an adapter shape that can later support ZATCA, UAE Peppol PINT, Oman, and other regimes.

Selecting PKR or another currency must not imply tax compliance. A tax-return claim requires jurisdiction, entity, period, rule, connector, and accounting review evidence.

### Phase 5: Unified owner dashboard

After the underlying modules are authoritative, add the dashboard cards:

- **Receivables:** total outstanding, overdue total, oldest balance, and customer count;
- **Payables:** total outstanding, due soon, overdue total, and supplier count;
- **Bank and cash:** book balance, reconciled balance, unreconciled difference, and last reconciliation date;
- **Inventory:** quantity, cost value, low-stock exceptions, and valuation date;
- **Tax period:** selected-period totals, exceptions, and return package status;
- **Documents:** invoices due, unpaid invoices, recent receipts, and actions requiring review.
- **Quotes:** awaiting response, expiring soon, accepted but not invoiced, and quoted value clearly marked as non-posted.

Each card needs a clear as-of date, company/book scope, currency, empty state, stale-data state, and source link. “Live bank balance” and “tax return ready” are prohibited unless separately implemented and validated.

## Implementation queue

The queued implementation task is deliberately ordered as follows:

1. Review the current AR/AP foundations and posting-service contracts.
2. Specify and implement the customer/vendor invoice and bill lifecycle.
3. Implement settlement and allocation workflows, including partial payments and corrections.
4. Implement customer/vendor ageing and source-linked reports.
5. Implement invoice, bill, statement, and payment-receipt documents.
6. Reconcile opening-document cutover with the new subledger.
7. Design and implement inventory and cost-of-sales posting.
8. Define the generic tax-reporting contract and the first reviewed jurisdiction adapter.
9. Build the unified dashboard only after the underlying reports pass reconciliation.
10. Update public product copy only from the validated release receipt.

No item authorises production deployment, provider activation, tax filing, payment collection, outbound delivery, or external messaging.

## Release acceptance gates

Before a capability is marked as available:

- exact-money and balanced-journal tests pass;
- permissions, company/book isolation, CSRF, idempotency, period locks, and reversals are tested;
- opening balances and imported history are reconciled;
- figures drill down to immutable source and journal records;
- browser checks cover desktop, tablet, and mobile layouts;
- representative synthetic businesses exercise normal, empty, overdue, corrected, and failed states;
- independent accounting/security review and supported-host checks are recorded where relevant;
- documentation, demo scope, release notes, and marketing copy agree;
- production status is reported separately from local and synthetic evidence.

Passing technical tests alone is not accounting sign-off or production readiness.

## Release marketing operating procedure

Every implementation release produces a marketing pack as part of release completion. The pack must be generated from the release receipt and contain:

1. factual press release;
2. customer-facing release announcement;
3. LinkedIn post;
4. Facebook/Instagram or short-form social post;
5. short demo/video script;
6. three to five approved visual cards or screenshots;
7. “included” and “not included” sections;
8. validation and availability statement;
9. a call to action such as download, demo, guide, pilot, or support;
10. owner approval before publication or sending.

The press release must state what changed, why it matters, what users can do now, what remains in development, and where the release can be obtained. It must not convert a roadmap item, research note, synthetic demo, or technical test into a production capability claim.

## Campaign narrative

The six questions are suitable as a campaign narrative now, but the campaign must distinguish the product promise from current availability.

### Campaign headline

**Six questions every business owner should be able to answer.**

With quotes included, the broader campaign line can be:

**Seven questions from first enquiry to paid customer.**

### Current-preview version

> PHP Ledger is building a clear, traceable answer to the questions behind everyday business decisions: who owes you, who you owe, what is in stock, what it cost, what is in the bank, what your accountant needs for tax, and whether you can give a customer a proper document.
>
> The current preview already includes double-entry accounting, receipts and expenses, profit and loss, balance sheet, cash and bank reporting, reconciliation, account ledgers, exports, and a cash-sale receipt showcase. Receivables, payables, formal invoicing, inventory valuation, cost of sales, and tax modules are being built next.

Quotes should be described separately until the quote workflow ships:

> Start with a clear quote, keep the customer's request and agreed terms visible, and convert it to an invoice only when the work is accepted. Quotes stay outside the books until that explicit conversion.

### Future module version

Use only after the relevant release gates pass:

> Know who owes you, who you owe, what your stock is worth, what is in the bank, which tax numbers need review, and which customer documents need action—all from a traceable accounting system.

## Documentation and ownership

This document is the planning source for the six-question dashboard and recurring release-marketing requirement. Module-specific design documents remain authoritative for their technical contracts. Release receipts remain authoritative for what shipped. The website may publish only validated claims from those receipts.
