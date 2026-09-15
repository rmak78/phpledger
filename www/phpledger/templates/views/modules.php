<?php declare(strict_types=1); ?>
<section class="page-wrap" aria-labelledby="modules-title">
    <div class="page-heading"><div><p class="eyebrow">Company settings</p><h1 id="modules-title">Modules</h1><p class="muted">Choose the optional tools for <?= pl_e($company['name']) ?>.</p></div></div>
    <p>Your accounting core is always available: accounts, journals, cash and bank transactions, reconciliation, reports and exports.</p>
    <?php if ($form['message'] !== ''): ?><div class="alert" role="alert" tabindex="-1" data-form-error><?= pl_e($form['message']) ?></div><?php endif; ?>
    <?php foreach ($modules as $module): $manifest = $module['manifest']; $state = $module['state']; ?>
    <section class="panel" aria-labelledby="module-<?= pl_e($manifest['id']) ?>">
        <div class="page-heading"><div><h2 id="module-<?= pl_e($manifest['id']) ?>"><?= pl_e($manifest['name']) ?></h2><p class="muted">Installed version <?= pl_e($manifest['version']) ?></p></div><span class="badge"><?= $state['enabled'] ? ($module['current'] ? 'Enabled' : 'Upgrade review needed') : 'Disabled' ?></span></div>
        <p>Try a six-product sample catalog, cash tender and change, a printable receipt and its accounting entry. Stock, tax, card processing and production retail are not included.</p>
        <p><?= pl_e($manifest['history']) ?></p>
        <?php if ($module['problem']): ?><p class="alert"><?= pl_e($module['problem']) ?></p><?php endif; ?>
        <?php if ($company['role'] === 'owner'): ?>
        <form method="post" action="<?= pl_e(pl_url('/modules')) ?>">
            <?= pl_csrf_field() ?><?= pl_scope_fields($company) ?>
            <input type="hidden" name="module_id" value="<?= pl_e($manifest['id']) ?>">
            <input type="hidden" name="revision" value="<?= pl_e((string) $state['revision']) ?>">
            <input type="hidden" name="digest" value="<?= pl_e($manifest['digest']) ?>">
            <input type="hidden" name="request_key" value="<?= pl_e(($form['input']['module_id'] ?? '') === $manifest['id'] ? ($form['input']['request_key'] ?? bin2hex(random_bytes(20))) : bin2hex(random_bytes(20))) ?>">
            <div class="field"><label for="module-reason-<?= pl_e($manifest['id']) ?>">Reason for this change</label><input id="module-reason-<?= pl_e($manifest['id']) ?>" name="reason" maxlength="500" required value="<?= pl_e(($form['input']['module_id'] ?? '') === $manifest['id'] ? ($form['input']['reason'] ?? '') : '') ?>"></div>
            <div class="form-actions">
                <?php if (!$module['current']): ?><button class="button primary" name="enabled" value="1"><?= $state['enabled'] ? 'Apply reviewed upgrade' : 'Enable module' ?></button><?php endif; ?>
                <?php if ($state['enabled']): ?><button class="button secondary" name="enabled" value="0">Disable module</button><?php endif; ?>
            </div>
        </form>
        <?php else: ?><p class="muted">Only the company owner can change modules.</p><?php endif; ?>
    </section>
    <?php endforeach; ?>
    <h2>Recent changes</h2>
    <div class="panel table-wrap" tabindex="0" role="region" aria-label="Module changes; scroll horizontally on small screens"><table class="data-table"><thead><tr><th>Module</th><th>Change</th><th>Reason</th><th>By</th><th>Recorded</th></tr></thead><tbody>
    <?php foreach ($history as $event): ?><tr><td><?= pl_e($event['module_id']) ?></td><td><?= pl_e($event['action']) ?></td><td><?= pl_e($event['reason']) ?></td><td><?= pl_e($event['display_name']) ?></td><td><time data-local-time datetime="<?= pl_e(str_replace(' ', 'T', $event['recorded_at']) . 'Z') ?>"><?= pl_e($event['recorded_at']) ?> UTC</time></td></tr><?php endforeach; ?>
    <?php if ($history === []): ?><tr><td colspan="5">No module changes yet.</td></tr><?php endif; ?>
    </tbody></table></div>
</section>
