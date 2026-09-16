<?php
declare(strict_types=1);
$reviewInput = $form['input'];
$roleInput = is_array($reviewInput['roles'] ?? null) ? $reviewInput['roles'] : [];
?>
<section class="page-wrap" aria-labelledby="review-title">
    <div class="page-heading">
        <div><p class="eyebrow">Business setup</p><h1 id="review-title">Review your existing books</h1><p class="muted"><?= pl_e((string) $company['name']) ?> · <?= pl_e((string) $company['currency']) ?></p></div>
        <a class="button secondary" href="<?= pl_e(pl_url('/transactions')) ?>">Back to transactions</a>
    </div>
    <?php if ($form['message'] !== ''): ?>
        <div class="alert" role="alert" tabindex="-1" data-form-error><h2>Review needs attention</h2><p><?= pl_e((string) $form['message']) ?></p><p>Your selections are preserved below.</p></div>
    <?php endif; ?>
    <?php if ($company['setup_status'] === 'opening_required'): ?>
        <div class="panel"><h2>Opening balances still need reconciliation</h2><p>Preview and reconcile this business's opening trial balance and unpaid invoices/bills before recording or posting transactions.</p><a class="button secondary" href="<?= pl_e(pl_url('/opening-balances')) ?>">Review opening cutover</a></div>
    <?php elseif ($company['setup_status'] !== 'review_required'): ?>
        <div class="panel"><h2>Setup review is complete</h2><p>Your current setup does not need the prior-foundation account review.</p><a class="button primary" href="<?= pl_e(pl_url('/transactions')) ?>">Open transactions</a></div>
    <?php elseif (!pl_can_write($company)): ?>
        <div class="panel"><h2>An authorised owner needs to review this setup</h2><p>You can read these books, but your role cannot change account assignments or confirm the opening setup.</p><a class="button secondary" href="<?= pl_e(pl_url('/reports/trial-balance')) ?>">View trial balance</a></div>
    <?php else: ?>
        <div class="panel"><h2>Keep your existing records intact</h2><p>Choose which existing account serves each purpose below. This records account roles without replacing account names, balances, or posted journals. Each purpose needs a separate active account of the matching type.</p><p class="muted"><?= pl_e((string) $template['name']) ?>, version <?= pl_e((string) $template['version']) ?>. <?= pl_e((string) $template['notice']) ?></p><a href="<?= pl_e(pl_url('/reports/trial-balance')) ?>">Review the current trial balance</a></div>
        <form action="<?= pl_e(pl_url('/setup/review')) ?>" method="post" class="panel form-grid">
            <?= pl_csrf_field() ?>
            <?= pl_scope_fields($company) ?>
            <?php foreach ($template['accounts'] as $definition): ?>
                <?php $fieldId = 'role-' . preg_replace('/[^a-zA-Z0-9_-]/', '-', (string) $definition['semantic_key']); ?>
                <div class="field">
                    <label for="<?= pl_e($fieldId) ?>"><?= pl_e((string) $definition['name']) ?> <span class="muted">(<?= pl_e((string) $definition['type']) ?>)</span></label>
                    <select id="<?= pl_e($fieldId) ?>" name="roles[<?= pl_e((string) $definition['semantic_key']) ?>]" required>
                        <option value="">Choose an existing account</option>
                        <?php foreach ($company['accounts'] as $available): ?>
                            <?php if (!$available['is_active'] || $available['type'] !== $definition['type']) { continue; } ?>
                            <option value="<?= pl_e((string) $available['id']) ?>"<?= pl_web_id($roleInput, (string) $definition['semantic_key']) === (int) $available['id'] ? ' selected' : '' ?>><?= pl_e($available['code'] . ' — ' . $available['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endforeach; ?>
            <div class="field full-width">
                <label class="checkbox"><input type="checkbox" name="reviewed" value="1" required<?= pl_web_text($reviewInput, 'reviewed') === '1' ? ' checked' : '' ?>> I have reviewed the existing accounts and opening balances, including any unpaid invoices and bills.</label>
                <p class="muted">This confirms a review of records already in these books. It does not import missing history or confirm tax compliance.</p>
            </div>
            <div class="actions full-width"><button class="button primary" type="submit">Confirm reviewed setup</button><a class="button secondary" href="<?= pl_e(pl_url('/transactions')) ?>">Cancel</a></div>
        </form>
    <?php endif; ?>
</section>
