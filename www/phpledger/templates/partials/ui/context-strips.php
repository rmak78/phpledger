<?php declare(strict_types=1); ?>
<?php if (pl_demo_enabled() && $view !== 'error'): $demoState = DB::queryFirstRow('SELECT next_reset_at FROM pl_demo_state WHERE id = 1'); ?>
<div class="demo-banner"><span><strong>Public demo</strong> · Separate synthetic data for each visitor. Destructive actions are disabled.</span><span>Resets <time data-local-time datetime="<?= pl_e(str_replace(' ', 'T', $demoState['next_reset_at']) . 'Z') ?>"><?= pl_e($demoState['next_reset_at']) ?> UTC</time> · <span data-demo-expiry="<?= pl_e(str_replace(' ', 'T', $demoState['next_reset_at']) . 'Z') ?>"><?= max(0, (int) ceil((strtotime($demoState['next_reset_at'] . ' UTC') - time()) / 60)) ?> minutes remaining</span></span></div>
<?php endif; ?>
<?php if ($company): ?>
<?php if ($company['setup_status'] !== 'ready'): ?>
<div class="readiness-banner"><?= pl_icon('info-circle') ?><div><strong><?= $company['setup_status'] === 'opening_required' ? 'Opening balances required.' : 'Review your existing setup.' ?></strong> <?= $company['setup_status'] === 'opening_required' ? 'Reconcile opening balances and unpaid documents before recording or posting transactions.' : 'Confirm account mappings and opening balances before posting new documents.' ?> <?php if (pl_can_write($company)): ?><a href="<?= pl_e(pl_url($company['setup_status'] === 'opening_required' ? '/opening-balances' : '/setup/review')) ?>">Review setup</a><?php endif; ?></div></div>
<?php endif; ?>
<?php endif; ?>
