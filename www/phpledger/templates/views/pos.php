<?php
declare(strict_types=1);
$receipt = $receipt ?? null;
$form = $form ?? ['input' => [], 'message' => ''];
$input = $input ?? $form['input'];
$canCheckout = pl_can_write($company) && $company['setup_status'] === 'ready';
?>
<section class="page-wrap pos-workspace" data-pos-root data-currency="<?= pl_e((string) $company['currency']) ?>">
<?php if ($receipt !== null): ?>
    <div class="page-heading pos-noprint"><div><p class="eyebrow">Cash sale recorded</p><h1>Receipt ready</h1><p class="muted">The receipt and its balanced journal have been saved together.</p></div><a class="button primary" href="<?= pl_e(pl_url('/pos')) ?>">Start another sale</a></div>
    <article class="panel pos-receipt" aria-labelledby="pos-receipt-title">
        <p class="eyebrow">Sample shop · Cash receipt</p><h2 id="pos-receipt-title"><?= pl_e((string) $company['name']) ?></h2>
        <p><?= pl_e((string) $receipt['number']) ?> · <?= pl_e(pl_date_label((string) $receipt['document']['date'])) ?></p>
        <?php if ($receipt['document']['status'] === 'reversed'): ?><p class="alert">This sale's accounting entry has been reversed. Its original receipt is preserved.</p><?php endif; ?>
        <div class="table-wrap"><table class="data-table"><caption>Recorded sale in <?= pl_e((string) $receipt['currency']) ?></caption><thead><tr><th scope="col">Item</th><th scope="col">Qty</th><th scope="col" class="amount">Each</th><th scope="col" class="amount">Total</th></tr></thead><tbody>
        <?php foreach ($receipt['items'] as $item): ?><tr><th scope="row"><?= pl_e((string) $item['name']) ?><span class="muted pos-sku"><?= pl_e((string) $item['sku']) ?></span></th><td><?= pl_e((string) $item['quantity']) ?></td><td class="amount"><?= pl_e(pl_money((string) $item['unit_price'])) ?></td><td class="amount"><?= pl_e(pl_money((string) $item['line_total'])) ?></td></tr><?php endforeach; ?>
        </tbody><tfoot><tr><th scope="row" colspan="3">Sale total</th><td class="amount"><?= pl_e(pl_money((string) $receipt['total'])) ?></td></tr><tr><th scope="row" colspan="3">Cash received</th><td class="amount"><?= pl_e(pl_money((string) $receipt['cash_received'])) ?></td></tr><tr><th scope="row" colspan="3">Change</th><td class="amount"><?= pl_e(pl_money((string) $receipt['change_due'])) ?></td></tr></tfoot></table></div>
        <p class="muted">Illustrative products and prices. No payment was collected by this application. This showcase does not calculate tax, stock movements, or cost of goods sold.</p>
        <p class="muted">Catalog <?= pl_e((string) $receipt['catalog_id']) ?> · <?= pl_e((string) $receipt['catalog_version']) ?></p>
        <div class="actions pos-noprint"><button class="button primary" type="button" data-pos-print>Print receipt</button><a class="button secondary" href="<?= pl_e(pl_url('/transactions/detail', ['id' => $receipt['document_id']])) ?>">View source transaction</a><a href="<?= pl_e(pl_url('/journals/detail', ['id' => $receipt['document']['journal_id']])) ?>">View journal</a></div>
    </article>
<?php else: ?>
    <?php
    $quantities = [];
    foreach (is_array($input['items'] ?? null) ? $input['items'] : [] as $item) {
        if (is_array($item) && is_string($item['sku'] ?? null) && (is_string($item['quantity'] ?? null) || is_int($item['quantity'] ?? null))) {
            $quantities[$item['sku']] = (string) $item['quantity'];
        }
    }
    $checkoutKey = pl_web_text($input, 'checkout_key', bin2hex(random_bytes(24)));
    ?>
    <div class="page-heading"><div><p class="eyebrow">Point of sale · General shop showcase</p><h1>A quick sale. A clear record.</h1><p class="muted">Choose products, review the cart, and record a cash receipt.</p></div><span class="badge">Sample catalog</span></div>
    <p class="alert">Checkout records a receipt in <strong><?= pl_e((string) $company['name']) ?></strong>. These products and prices are illustrative. No actual payment is taken; inventory, tax and credit sales are not included.</p>
    <?php if (!$canCheckout): ?><p class="alert">Checkout needs an owner/accountant role and completed business setup. You can browse the sample catalog.</p><?php endif; ?>
    <?php if ($form['message'] !== ''): ?><div class="alert" role="alert" tabindex="-1" data-form-error><strong>Sale not completed</strong><p><?= pl_e((string) $form['message']) ?></p><p>Your cart and cash amount are kept below. Review them before retrying.</p></div><?php endif; ?>
    <form action="<?= pl_e(pl_url('/pos/checkout')) ?>" method="post" class="pos-grid" data-pos-form>
        <!-- A disabled default submitter blocks implicit Enter submission without JavaScript. -->
        <button type="submit" disabled hidden aria-hidden="true" tabindex="-1">Editing a sale</button>
        <?= pl_csrf_field() ?><?= pl_scope_fields($company) ?>
        <input type="hidden" name="checkout_key" value="<?= pl_e($checkoutKey) ?>">
        <input type="hidden" name="catalog_digest" value="<?= pl_e(pl_web_text($input, 'catalog_digest', (string) $catalog['digest'])) ?>">
        <section class="pos-catalog" aria-labelledby="pos-products-title">
            <h2 id="pos-products-title">Find a product</h2>
            <label class="field pos-search">Search the sample shop<input type="search" placeholder="Name or product code" data-pos-search></label>
            <div class="pos-categories" aria-label="Product categories"><button type="button" class="button secondary" data-pos-category="all" aria-pressed="true">All products</button><?php foreach (array_unique(array_column($catalog['products'], 'category')) as $category): ?><button type="button" class="button secondary" data-pos-category="<?= pl_e((string) $category) ?>" aria-pressed="false"><?= pl_e((string) $category) ?></button><?php endforeach; ?></div>
            <div class="pos-products">
            <?php foreach ($catalog['products'] as $index => $product): ?>
                <article class="panel pos-product" data-pos-product data-sku="<?= pl_e((string) $product['sku']) ?>" data-name="<?= pl_e((string) $product['name']) ?>" data-category="<?= pl_e((string) $product['category']) ?>" data-price="<?= pl_e((string) $product['unit_price']) ?>">
                    <div class="pos-product-mark" aria-hidden="true"><?= pl_e((string) $product['mark']) ?></div><p class="eyebrow"><?= pl_e((string) $product['category']) ?></p><h3><?= pl_e((string) $product['name']) ?></h3><p class="muted"><?= pl_e((string) $product['description']) ?></p><p class="pos-price"><?= pl_e((string) $company['currency']) ?> <?= pl_e(pl_money((string) $product['unit_price'])) ?></p>
                    <input type="hidden" name="items[<?= pl_e((string) $index) ?>][sku]" value="<?= pl_e((string) $product['sku']) ?>">
                    <div class="pos-product-actions"><label class="field" for="pos-qty-<?= pl_e((string) $index) ?>">Quantity<input id="pos-qty-<?= pl_e((string) $index) ?>" type="text" inputmode="numeric" pattern="(?:0|[1-9][0-9]?)" maxlength="2" name="items[<?= pl_e((string) $index) ?>][quantity]" value="<?= pl_e($quantities[$product['sku']] ?? '0') ?>" data-pos-quantity<?= !$canCheckout ? ' disabled' : '' ?>></label><button type="button" class="button secondary" data-pos-add aria-label="<?= pl_e('Add ' . $product['name']) ?>"<?= !$canCheckout ? ' disabled' : '' ?>>Add</button></div>
                </article>
            <?php endforeach; ?>
            </div><p class="muted" data-pos-no-results hidden>No products match. Try another name or category.</p>
        </section>
        <aside class="panel pos-cart" aria-labelledby="pos-cart-title"><p class="eyebrow">Current sale</p><h2 id="pos-cart-title">Your cart</h2><div data-pos-cart><p class="muted">Add a product or set its quantity to get started.</p></div><div class="pos-total"><span>Total (<?= pl_e((string) $company['currency']) ?>)</span><strong data-pos-total>0.00</strong></div>
            <label class="field">Sale date<input type="date" name="date" required value="<?= pl_e(pl_web_text($input, 'date', gmdate('Y-m-d'))) ?>"<?= !isset($input['date']) ? ' data-local-today' : '' ?>></label>
            <label class="field">Cash received (<?= pl_e((string) $company['currency']) ?>)<input type="text" inputmode="decimal" name="cash_received" maxlength="21" pattern="(?:0|[1-9][0-9]{0,15})(?:\.[0-9]{1,4})?" required value="<?= pl_e(pl_web_text($input, 'cash_received')) ?>" placeholder="0.00" data-pos-cash><span class="muted">Use a decimal point, without grouping separators.</span></label>
            <p class="pos-change" aria-live="polite" data-pos-change>Enter the cash received to see change.</p><p class="muted" aria-live="polite" data-pos-feedback>Up to 99 of each product and 200 units per sale.</p>
            <button type="submit" name="checkout_intent" value="record_cash_sale" class="button primary" data-pos-checkout<?= !$canCheckout ? ' disabled' : '' ?>>Record cash sale</button><p class="muted">Checkout saves one receipt and posts its balanced entry. Prices are checked again on the server.</p><noscript><p>Set product quantities and cash received, then activate Record cash sale to confirm. The server validates the total and change at checkout; live cart updates require JavaScript.</p></noscript>
        </aside>
    </form>
<?php endif; ?>
</section>
