<?php
 declare(strict_types=1);
 $kind = pl_web_text($input, 'kind', 'expense');
 $cashAccounts = array_filter($company['accounts'], static fn (array $a): bool => $a['role'] === 'cash_bank' && $a['type'] === 'asset' && $a['is_active']);
 $categoryAccounts = array_filter($company['accounts'], static fn (array $a): bool => in_array($a['type'], ['income','expense'], true) && $a['role'] === $a['type'] && $a['is_active']);
 $defaultCash = array_key_first($cashAccounts);
 $cashId = pl_web_id($input, 'money_account_id', $defaultCash === null ? 0 : (int) $cashAccounts[$defaultCash]['id']);
 $returnPath = pl_url($document ? '/transactions/detail' : '/transactions', ($document ? ['id'=>$document['id']] : []) + $returnFilters);
?>
<section class="py-5" aria-labelledby="transaction-editor-title">
    <a class="back-link" href="<?= pl_e($returnPath) ?>"><?= pl_icon('arrow-left') ?> Back to transactions</a>
    <?php pl_ui_document_header($document ? $document['number'] . ' · Edit draft' : 'New ' . ($kind === 'receipt' ? 'receipt' : 'expense'), 'draft', static function () use ($returnPath): void { ?>
        <a class="btn btn-ghost" href="<?= pl_e($returnPath) ?>">Cancel</a><button class="btn btn-primary" form="transaction-editor" type="submit">Save draft</button>
    <?php }, 'transaction-editor-title'); ?>
    <?php if ($form['message']): ?><div class="alert alert-danger" role="alert" tabindex="-1" data-form-error><strong>Your draft has not been changed.</strong><p><?= pl_e($form['message']) ?></p><p>Your submitted values are kept below.<?php if ($document): ?> <a href="<?= pl_e(pl_url('/transactions/edit', ['id'=>$document['id']])) ?>">Reload the latest saved version</a> to resolve a revision conflict.<?php endif; ?></p></div><?php endif; ?>
    <form id="transaction-editor" class="panel doc-body" method="post" action="<?= pl_e(pl_url('/transactions/save')) ?>" data-document-form>
        <?= pl_csrf_field() ?><?= pl_scope_fields($company) ?>
        <?php pl_ui_return_filters($returnFilters); ?>
        <input type="hidden" name="creation_key" value="<?= pl_e(pl_web_text($input,'creation_key')) ?>">
        <?php if ($document): ?><input type="hidden" name="id" value="<?= (int) $document['id'] ?>"><input type="hidden" name="revision" value="<?= pl_web_id($input,'revision',(int)$document['revision']) ?>"><?php endif; ?>
        <fieldset class="flex flex-wrap items-center gap-4"><legend class="field-label">Transaction type</legend><label><input type="radio" name="kind" value="expense" <?= $kind !== 'receipt' ? 'checked' : '' ?>> Money out · Expense</label><label><input type="radio" name="kind" value="receipt" <?= $kind === 'receipt' ? 'checked' : '' ?>> Money in · Receipt</label></fieldset>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <?php pl_ui_field('transaction-date','Date',static function () use ($input,$document,$form): void { ?><input class="input" id="transaction-date" type="date" name="date" value="<?= pl_e(pl_web_text($input,'date')) ?>" required <?= !$document && !$form['input'] ? 'data-local-today' : '' ?>><?php }, 'The accounting date for this transaction.'); ?>
        <?php pl_ui_field('transaction-amount','Amount (' . $company['currency'] . ')',static function () use ($input): void { ?><input class="input input-amount" id="transaction-amount" type="text" inputmode="decimal" name="amount" value="<?= pl_e(pl_web_text($input,'amount')) ?>" placeholder="0.00" maxlength="21" pattern="[0-9]+(\.[0-9]{1,4})?" required autocomplete="off"><?php }, 'Use a decimal point, without separators.'); ?>
        <?php pl_ui_field('transaction-party','Counterparty name',static function () use ($input): void { ?><input class="input" id="transaction-party" name="counterparty" value="<?= pl_e(pl_web_text($input,'counterparty')) ?>" maxlength="160" required><?php }); ?>
        <?php pl_ui_field('transaction-cash','Cash / bank account',static function () use ($cashAccounts,$cashId): void { ?><select class="select" id="transaction-cash" name="money_account_id" required><?php foreach ($cashAccounts as $account): ?><option value="<?= (int)$account['id'] ?>" <?= $cashId === (int)$account['id'] ? 'selected' : '' ?>><?= pl_e($account['name']) ?></option><?php endforeach; ?></select><?php }); ?>
        <?php pl_ui_field('transaction-category','Category',static function () use ($categoryAccounts,$input): void { ?><select class="select" id="transaction-category" name="category_account_id" required><option value="">Choose a category</option><?php foreach ($categoryAccounts as $account): ?><option value="<?= (int)$account['id'] ?>" data-category-kind="<?= $account['type'] === 'income' ? 'receipt' : 'expense' ?>" <?= pl_web_id($input,'category_account_id') === (int)$account['id'] ? 'selected' : '' ?>><?= pl_e($account['name']) ?></option><?php endforeach; ?></select><?php }); ?>
        <?php pl_ui_field('transaction-reference','Reference (optional)',static function () use ($input): void { ?><input class="input" id="transaction-reference" name="reference" value="<?= pl_e(pl_web_text($input,'reference')) ?>" maxlength="120" placeholder="Receipt number or your own reference"><?php }); ?>
        <div class="sm:col-span-2 lg:col-span-4"><?php pl_ui_field('transaction-memo','Memo (optional)',static function () use ($input): void { ?><textarea class="textarea" id="transaction-memo" name="memo" rows="2" maxlength="500" placeholder="What was this transaction for?"><?= pl_e(pl_web_text($input,'memo')) ?></textarea><?php }); ?></div>
        </div>
        <p class="text-xs text-ink-muted">This entry uses one category in <?= pl_e($company['currency']) ?>. Split entries, tax and foreign-currency amounts are not supported by this receipt/expense form.</p>
        <section class="rounded-panel border border-border p-3" aria-labelledby="posting-preview-title">
            <div class="flex flex-wrap items-center justify-between gap-2"><h2 class="section-title" id="posting-preview-title">Posting preview</h2><button class="btn btn-secondary btn-sm" name="editor_action" value="preview" type="submit">Update posting preview</button></div>
            <?php if ($preview): ?><div class="mt-3" data-server-preview><?php $entryLines=$preview['lines']; require __DIR__ . '/../partials/entry.php'; ?></div><p class="text-xs text-ink-muted mt-2">This preview uses the same posting payload as the saved transaction. Update it after changing fields. Permissions, revisions and open periods are checked again when posting.</p><?php else: ?><p class="text-xs text-ink-muted mt-2">Enter the transaction details, then update the preview to inspect the exact journal lines without saving or posting.</p><?php endif; ?>
            <p class="text-xs text-ink-muted mt-2">Posting adds this transaction to reports. A saved draft does not change your books.</p>
        </section>
    </form>
</section>
