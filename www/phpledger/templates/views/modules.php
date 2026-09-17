<?php
declare(strict_types=1);
$v=$form['input'];
$visibilityInput=($v['action']??'')==='visibility'?$v:[];
?>
<section class="flex flex-col gap-4 py-5" aria-labelledby="modules-title">
    <?php pl_ui_page_header('Modules', 'Choose the optional tools for ' . $company['name'] . '.', null, 'modules-title'); ?>
    <p>The accounting core, Accounts Receivable and Accounts Payable are bundled and always available. Purchasing and Inventory can be enabled below.</p>
    <section class="panel"><h2 class="section-title">Keep your navigation simple</h2><p>Hide areas you do not use. Financial totals, existing records and authorised services are unaffected.</p>
    <?php if ($company['role'] === 'owner'): ?><form method="post" action="<?= pl_e(pl_url('/modules')) ?>">
        <?= pl_csrf_field() ?><?= pl_scope_fields($company) ?><input type="hidden" name="action" value="visibility">
        <input type="hidden" name="revision" value="<?= pl_e((string)($visibilityInput?($visibilityInput['revision']??''):$visibility['revision'])) ?>"><input type="hidden" name="request_key" value="<?= pl_e($visibilityInput['request_key']??bin2hex(random_bytes(20))) ?>">
        <p><label><input type="checkbox" name="show_ar" value="1" <?= ($visibilityInput?($visibilityInput['show_ar']??'')==='1':$visibility['show_ar']) ? 'checked' : '' ?>> Show receivables</label></p>
        <p><label><input type="checkbox" name="show_ap" value="1" <?= ($visibilityInput?($visibilityInput['show_ap']??'')==='1':$visibility['show_ap']) ? 'checked' : '' ?>> Show payables</label></p>
        <label class="field">Reason<input class="input" name="reason" required maxlength="500" value="<?= pl_e($visibilityInput['reason']??'') ?>"></label><button class="btn btn-secondary">Save navigation</button>
        <?php if ($visibilityInput): ?><a class="btn btn-secondary" href="<?= pl_e(pl_url('/modules')) ?>">Reload saved navigation</a><?php endif; ?>
    </form><?php else: ?><p>Only the company owner can change navigation.</p><?php endif; ?>
    <p><a href="<?= pl_e(pl_url('/ar')) ?>">Open receivables</a> · <a href="<?= pl_e(pl_url('/ap')) ?>">Open payables</a></p></section>
    <?php if ($form['message'] !== ''): ?><div class="alert" role="alert" tabindex="-1" data-form-error><?= pl_e($form['message']) ?></div><?php endif; ?>
    <?php foreach ($modules as $module): $manifest = $module['manifest']; $state = $module['state']; $moduleInput=($v['module_id']??'')===$manifest['id']&&!$visibilityInput?$v:[]; ?>
    <section class="panel" aria-labelledby="module-<?= pl_e($manifest['id']) ?>">
        <div class="flex items-start justify-between gap-3"><div><h2 id="module-<?= pl_e($manifest['id']) ?>"><?= pl_e($manifest['name']) ?></h2><p class="muted">Installed version <?= pl_e($manifest['version']) ?></p></div><span class="badge"><?= $state['enabled'] ? ($module['current'] ? 'Enabled' : 'Upgrade review needed') : 'Disabled' ?></span></div>
        <p><?= pl_e(match ($manifest['id']) { 'inventory' => 'One stock location, product catalogue, stock movements, weighted-average cost and valuation. Enable this before Purchasing.', 'purchasing' => 'Purchase orders, partial receipts, later supplier bills and received-but-unbilled reconciliation. Requires Inventory and AP.', default => 'A six-product cash checkout showcase. Its sample products do not use the Inventory module.' }) ?></p>
        <p><?= pl_e($manifest['history']) ?></p>
        <?php if (in_array($manifest['id'],['inventory','purchasing'],true)): ?><p><a href="<?= pl_e(pl_url('/'.$manifest['id'])) ?>">Open module records and reports</a></p><?php endif; ?>
        <?php if ($module['problem']): ?><p class="alert"><?= pl_e($module['problem']) ?></p><?php endif; ?>
        <?php if ($company['role'] === 'owner'): ?>
        <form method="post" action="<?= pl_e(pl_url('/modules')) ?>">
            <?= pl_csrf_field() ?><?= pl_scope_fields($company) ?>
            <input type="hidden" name="module_id" value="<?= pl_e($manifest['id']) ?>">
            <input type="hidden" name="revision" value="<?= pl_e((string)($moduleInput?($moduleInput['revision']??''):$state['revision'])) ?>">
            <input type="hidden" name="digest" value="<?= pl_e($moduleInput?($moduleInput['digest']??''):$manifest['digest']) ?>">
            <input type="hidden" name="request_key" value="<?= pl_e($moduleInput['request_key']??bin2hex(random_bytes(20))) ?>">
            <div class="field"><label for="module-reason-<?= pl_e($manifest['id']) ?>">Reason for this change</label><input class="input" id="module-reason-<?= pl_e($manifest['id']) ?>" name="reason" maxlength="500" required value="<?= pl_e($moduleInput['reason']??'') ?>"></div>
            <div class="form-actions">
                <?php if (!$module['current']): ?><button class="btn btn-primary" name="enabled" value="1"><?= $state['enabled'] ? 'Apply reviewed upgrade' : 'Enable module' ?></button><?php endif; ?>
                <?php if ($state['enabled']): ?><?php pl_ui_confirmation('Disable ' . $manifest['name'], 'New operations in this module stop until it is enabled again. Existing documents, posted entries and reports remain available. Dependent modules must be disabled first.', static function (): void { ?><button class="btn btn-danger" name="enabled" value="0">Confirm disable</button><?php }); ?><?php endif; ?>
                <?php if ($moduleInput): ?><a class="btn btn-secondary" href="<?= pl_e(pl_url('/modules')) ?>">Reload saved module state</a><?php endif; ?>
            </div>
        </form>
        <?php else: ?><p class="muted">Only the company owner can change modules.</p><?php endif; ?>
    </section>
    <?php endforeach; ?>
    <h2 class="section-title">Recent changes</h2>
    <div class="panel table-wrap" tabindex="0" role="region" aria-label="Module changes; scroll horizontally on small screens"><table class="table"><thead><tr><th>Module</th><th>Change</th><th>Reason</th><th>By</th><th>Recorded</th></tr></thead><tbody>
    <?php foreach ($history as $event): ?><tr><td><?= pl_e($event['module_id']) ?></td><td><?= pl_e($event['action']) ?></td><td><?= pl_e($event['reason']) ?></td><td><?= pl_e($event['display_name']) ?></td><td><time data-local-time datetime="<?= pl_e(str_replace(' ', 'T', $event['recorded_at']) . 'Z') ?>"><?= pl_e($event['recorded_at']) ?> UTC</time></td></tr><?php endforeach; ?>
    <?php if ($history === []): ?><tr><td colspan="5">No module changes yet.</td></tr><?php endif; ?>
    </tbody></table></div>
</section>
