<?php
declare(strict_types=1);
$canManage = pl_can_write($company) && !pl_demo_enabled();
$types = ['asset' => 'Asset', 'liability' => 'Liability', 'equity' => 'Equity', 'income' => 'Income', 'expense' => 'Expense'];
$roles = ['' => 'No operational purpose', 'cash_bank' => 'Cash / bank — Asset', 'receivables' => 'Receivables — Asset', 'payables' => 'Payables — Liability', 'owner_equity' => 'Owner equity — Equity', 'income' => 'Income — Income', 'expense' => 'Expense — Expense'];
$hasFailure = pl_web_text($form, 'message') !== '';
$isActive = $hasFailure ? pl_web_text($input, 'is_active') === '1' : (bool) ($input['is_active'] ?? true);
?>
<div class="core-console <?= $account || $isNew ? 'core-has-selection' : '' ?>">
<section class="core-account-list" aria-labelledby="accounts-title">
    <div class="page-heading"><div><p class="eyebrow">Accounting core</p><h1 id="accounts-title">Chart of accounts</h1><p class="muted"><?= count($company['accounts']) ?> accounts · <?= pl_e($company['currency']) ?></p></div><?php if ($canManage): ?><a class="button primary" href="<?= pl_e(pl_url('/accounts', ['new' => '1'])) ?>"><?= pl_icon('plus') ?> New account</a><?php endif; ?></div>
    <div class="table-wrap core-account-table" tabindex="0" role="region" aria-label="Accounts">
    <table class="data-table"><caption class="sr-only">Select an account to inspect its details, or open its statement.</caption><thead><tr><th scope="col">Code / account</th><th scope="col">Classification</th><th scope="col">Status</th><th scope="col">Activity</th></tr></thead><tbody>
    <?php foreach ($company['accounts'] as $row): $selected = $account && (int) $account['id'] === (int) $row['id']; ?>
    <tr class="<?= $selected ? 'selected' : '' ?>"><td><a class="record-link" href="<?= pl_e(pl_url('/accounts', ['id' => $row['id']])) ?>" <?= $selected ? 'aria-current="true"' : '' ?>><strong><?= pl_e($row['name']) ?></strong><span><?= pl_e($row['code']) ?></span></a></td><td><?= pl_e($types[$row['type']] ?? $row['type']) ?></td><td><span class="badge <?= $row['is_active'] ? 'posted' : '' ?>"><?= $row['is_active'] ? 'Active' : 'Inactive' ?></span></td><td><a href="<?= pl_e(pl_url('/reports/account', ['id' => $row['id']])) ?>" aria-label="<?= pl_e('Statement for ' . $row['name']) ?>">Statement</a></td></tr>
    <?php endforeach; ?>
    </tbody></table></div>
    <?php if (!$company['accounts']): ?><div class="empty-state"><h2>No accounts yet</h2><p>Add the accounts your business needs before recording a journal.</p></div><?php endif; ?>
    <p class="muted small core-footnote">Inactive accounts keep their statements and posted history.</p>
</section>
<section class="core-account-detail" aria-labelledby="account-detail-title">
    <a class="back-link" href="<?= pl_e(pl_url('/accounts')) ?>"><?= pl_icon('arrow-left') ?> Back to accounts</a>
    <?php if ($hasFailure): ?><div class="alert" role="alert"><strong>Your account was not saved.</strong><p><?= pl_e(pl_web_text($form, 'message')) ?></p><p>Your submitted values are retained.<?php if ($account): ?> <a href="<?= pl_e(pl_url('/accounts', ['id' => $account['id']])) ?>">Reload the latest saved version</a> before resolving a revision conflict.<?php endif; ?></p></div><?php endif; ?>
    <?php if ($account || ($isNew && $canManage)): ?>
    <div class="section-heading"><h2 id="account-detail-title"><?= $account ? pl_e($account['name']) : 'New account' ?></h2><?php if ($account): ?><span class="muted small">Revision <?= (int) $account['revision'] ?></span><?php endif; ?></div>
    <?php if ($canManage): ?>
    <form method="post" action="<?= pl_e(pl_url('/accounts/save')) ?>" class="core-account-form">
        <?= pl_csrf_field() ?><?= pl_scope_fields($company) ?>
        <input type="hidden" name="creation_key" value="<?= pl_e(pl_web_text($input, 'creation_key')) ?>">
        <?php if ($account): ?><input type="hidden" name="id" value="<?= (int) $account['id'] ?>"><input type="hidden" name="revision" value="<?= pl_web_id($input, 'revision') ?>">
        <dl class="record-details"><div><dt>Code</dt><dd><?= pl_e($account['code']) ?></dd></div><div><dt>Classification</dt><dd><?= pl_e($types[$account['type']] ?? $account['type']) ?></dd></div><div><dt>Purpose</dt><dd><?= pl_e($roles[$account['role'] ?? ''] ?? 'No operational purpose') ?></dd></div></dl>
        <?php foreach (['code', 'type', 'role'] as $fixedField): ?><input type="hidden" name="<?= pl_e($fixedField) ?>" value="<?= pl_e((string) ($account[$fixedField] ?? '')) ?>"><?php endforeach; ?>
        <p class="muted small">Code, classification and purpose are fixed. Use a new account and a reviewed correction for a classification change.</p>
        <?php else: ?>
        <div class="form-grid">
            <label class="field">Account code<input name="code" value="<?= pl_e(pl_web_text($input, 'code')) ?>" maxlength="20" pattern="[A-Za-z0-9][A-Za-z0-9._-]{0,19}" required autocomplete="off"><span class="field-hint">Letters, numbers, dots, dashes or underscores.</span></label>
            <label class="field">Classification<select name="type" required><?php foreach ($types as $value => $label): ?><option value="<?= pl_e($value) ?>" <?= pl_web_text($input, 'type') === $value ? 'selected' : '' ?>><?= pl_e($label) ?></option><?php endforeach; ?></select></label>
            <label class="field full-width">Operational purpose<select name="role"><?php foreach ($roles as $value => $label): ?><option value="<?= pl_e($value) ?>" <?= pl_web_text($input, 'role') === $value ? 'selected' : '' ?>><?= pl_e($label) ?></option><?php endforeach; ?></select><span class="field-hint">Choose a purpose with the same classification, or no operational purpose.</span></label>
        </div>
        <?php endif; ?>
        <label class="field">Account name<input name="name" value="<?= pl_e(pl_web_text($input, 'name')) ?>" maxlength="120" required></label>
        <label class="core-checkbox"><input type="checkbox" name="is_active" value="1" <?= $isActive ? 'checked' : '' ?>><span>Active — available for new entries</span></label>
        <label class="field">Reason <?= $account ? 'for this change' : 'for adding this account' ?><textarea name="reason" rows="3" maxlength="500" required><?= pl_e(pl_web_text($input, 'reason')) ?></textarea></label>
        <div class="form-actions"><button class="button primary" type="submit"><?= $account ? 'Save account' : 'Create account' ?></button><?php if ($account): ?><a class="button secondary" href="<?= pl_e(pl_url('/reports/account', ['id' => $account['id']])) ?>">Open statement</a><?php endif; ?></div>
    </form>
    <?php else: ?>
    <dl class="record-details"><div><dt>Code</dt><dd><?= pl_e($account['code']) ?></dd></div><div><dt>Classification</dt><dd><?= pl_e($types[$account['type']] ?? $account['type']) ?></dd></div><div><dt>Purpose</dt><dd><?= pl_e($roles[$account['role'] ?? ''] ?? 'No operational purpose') ?></dd></div><div><dt>Status</dt><dd><?= $account['is_active'] ? 'Active' : 'Inactive' ?></dd></div></dl>
    <a class="button secondary" href="<?= pl_e(pl_url('/reports/account', ['id' => $account['id']])) ?>">Open statement</a>
    <p class="muted small core-footnote"><?= pl_demo_enabled() ? 'Account changes are unavailable in the public demo.' : 'Your role can view accounts and their history.' ?></p>
    <?php endif; ?>
    <?php if ($account): ?><details class="history-section core-history"><summary>Account history <span class="muted">· Latest 50 changes</span></summary><?php if (!$history): ?><p class="muted small">No account-management changes have been recorded.</p><?php endif; ?><ol><?php foreach ($history as $event): ?><li><strong><?= pl_e(ucfirst(str_replace('_', ' ', $event['action']))) ?></strong> · <?= pl_e($event['display_name']) ?><p><?= pl_e($event['reason']) ?></p><time data-local-time datetime="<?= pl_e(str_replace(' ', 'T', $event['recorded_at']) . 'Z') ?>"><?= pl_e($event['recorded_at']) ?> UTC</time></li><?php endforeach; ?></ol></details><?php endif; ?>
    <?php else: ?><div class="empty-detail"><h2 id="account-detail-title"><?= $isNew ? 'Account changes unavailable' : 'Choose an account' ?></h2><p><?= $isNew ? 'Accounts can be managed by an owner or accountant outside the public demo.' : 'Inspect its classification, open its statement or review its history.' ?></p></div><?php endif; ?>
</section></div>
