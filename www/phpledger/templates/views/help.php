<?php declare(strict_types=1); ?>
<section class="page-wrap" aria-labelledby="help-title">
    <div class="page-heading"><div><p class="eyebrow">Getting started</p><h1 id="help-title">From first entry to a clear report</h1><p class="muted">A short guide to the working accounting preview.</p></div><a class="button secondary" href="<?= pl_e(pl_url('/companies')) ?>">Your businesses</a></div>
    <div class="panel">
        <h2>Choose the right starting point</h2>
        <ul>
            <li><strong>New business:</strong> confirm there are no prior balances or unpaid documents, review the neutral account template, and create empty books.</li>
            <li><strong>Sample company:</strong> explore fictional posted entries and editable drafts in a separate, clearly marked company.</li>
            <li><strong>Existing business:</strong> save setup, then open <strong>Opening balances</strong>. Enter balances or upload/paste the documented CSV, include remaining unpaid invoices/bills, and review the balanced preview. Confirmation brings forward balances at the close of your accounting start date; new transactions start the next day.</li>
        </ul>
        <?php if (!pl_demo_enabled()): ?><a class="button primary" href="<?= pl_e(pl_url('/onboarding')) ?>">Set up or explore a business</a><?php else: ?><p class="alert">In this public demo your sample is created for you and resets hourly. Business administration, imports, deletion and period changes are disabled.</p><?php endif; ?>
    </div>
    <div class="panel">
        <h2>Record a receipt or expense</h2>
        <ol>
            <li>Open the intended business and check its name and currency.</li>
            <li>Create a receipt for money coming in or an expense for money going out. Enter the date, amount, cash/bank account, category, and a useful description.</li>
            <li><strong>Save draft</strong> to keep your work. A saved draft does not affect the books.</li>
            <li>Review the saved details and journal effect, then <strong>Post</strong> when they are correct.</li>
            <li>Open the trial balance, select the affected account, and follow its activity back to the source transaction.</li>
        </ol>
        <p>Owners and accountants can write; viewers can inspect the books and reports. A saved draft is editable. A posted transaction is preserved.</p>
    </div>
    <div class="panel">
        <h2>Correct a posted transaction</h2>
        <p>Open the transaction and create a linked reversal with a date and a clear reason. The reversal must fall in an open period and cannot predate the original. Both entries remain visible. Create a new correct transaction when needed.</p>
        <p>If a draft changed in another tab or by another person, compare your preserved entries with the latest saved version before saving again. Repeated posting must never be used to create a second copy.</p>
    </div>
    <div class="panel">
        <h2>Try the sample shop</h2>
        <p>The point-of-sale showcase has six fictional products. Add quantities, review the cart, enter cash received, and record the sale. Its receipt links to the same accounting journal and reports as other receipts.</p>
        <p>Check the business name before checkout: the sale is recorded in those books. No actual payment is collected. Inventory, cost of goods sold, tax, discounts, credit sales, and restaurant operations are not included.</p>
        <a class="button secondary" href="<?= pl_e(pl_url('/pos')) ?>">Open point of sale</a>
    </div>
    <div class="panel">
        <h2>What this preview includes</h2>
        <p>Essential setup, a preliminary country-neutral account template, one base currency per company, receipts and expenses, cash POS, reports, linked reversals, opening cutover, period administration, and bank CSV reconciliation.</p>
        <p>Detailed historical journal imports, invoice collection and bill settlement, XLSX import, currency conversion, country tax rules, inventory, and production retail features remain future work. The broader industry sample packs are research scenarios, not installed operational modules.</p>
        <p class="muted">If an installation already contains foundation records, an owner or accountant can review the existing account mappings and balances. That review preserves the old records and cannot be used to skip missing opening data.</p>
    </div>
    <div class="panel"><h2>Close periods and reconcile a bank account</h2><p>Use <strong>Periods</strong> to create a date range or close it with a reason. Closing stops new postings in that range and preserves existing entries. Only the owner can reopen it, with a recorded reason. Closing does not create year-end profit-transfer entries or accounting statements.</p><p>Use <strong>Bank reconciliation</strong> to preview a statement CSV and confirm its import. Check opening and closing balances, then explicitly match each bank row to a posted line. Amounts must agree; multiple suggestions need your choice. Complete reconciliation only when all bank rows are matched and the adjusted bank balance equals the ledger. Unmatched ledger payments/deposits carry forward as outstanding.</p><p>The first statement requires you to confirm that all earlier bank entries have cleared and its opening balance equals the ledger before its start. Later statements must be consecutive. Completed reconciliations preserve their totals and prevent backdated changes to that bank account; later correcting entries remain traceable.</p></div>
</section>
