<?php declare(strict_types=1); ?>
<section class="auth-panel" aria-labelledby="consent-title">
    <p class="eyebrow">Authorize a reporting client</p>
    <?php pl_ui_page_header('Connect ' . $client->getName(), 'You are signed in as ' . $user['display_name'] . '. Choose the company this client may read.', null, 'consent-title'); ?>
    <section class="panel">
        <h2 class="section-title">Requested access</h2>
        <p>Accounts, transactions, posted journals and financial reports for the selected company and book. Your current membership and permissions are checked on every request. <strong>Financial writes are unavailable.</strong></p>
        <dl class="dgrid">
            <div class="dgrid-row"><dt>Client identifier</dt><dd class="break-all"><code><?= pl_e($client->getIdentifier()) ?></code></dd></div>
            <div class="dgrid-row"><dt>Return address</dt><dd class="break-all"><code><?= pl_e($pending['query']['redirect_uri']) ?></code></dd></div>
        </dl>
        <p class="text-xs text-ink-muted mt-3">Financial results will be sent to this client. Review its identity and data handling before allowing access.</p>
        <p class="text-xs text-ink-muted"><?= pl_demo_enabled() ? 'Access ends at the next hourly sample reset. Start a fresh sample and reconnect afterward.' : 'The grant expires in 30 days and can be revoked sooner in Connections. Access tokens last up to 15 minutes.' ?></p>
    </section>
    <?php if ($message !== ''): ?><div class="alert alert-warning" role="alert" tabindex="-1" data-form-error><?= pl_e($message) ?></div><?php endif; ?>
    <form method="post" action="<?= pl_e(pl_url('/oauth/authorize')) ?>" class="flex flex-col gap-4">
        <?= pl_csrf_field() ?><input type="hidden" name="consent_nonce" value="<?= pl_e($pending['nonce']) ?>">
        <?php pl_ui_field('consent-company', 'Company and book', static function () use ($companies): void { ?>
            <select class="select" id="consent-company" name="company_id" required>
                <option value="">Choose a company</option>
                <?php foreach ($companies as $availableCompany): ?><option value="<?= (int) $availableCompany['id'] ?>"><?= pl_e($availableCompany['name']) ?> · Book <?= (int) $availableCompany['book_id'] ?></option><?php endforeach; ?>
            </select>
        <?php }); ?>
        <?php pl_ui_connection_scope('consent-scope'); ?>
        <div class="panel-actions" data-fold="primary action"><button class="btn btn-primary" name="decision" value="allow">Allow read access</button><button class="btn btn-ghost" name="decision" value="deny" formnovalidate>Cancel</button></div>
    </form>
</section>
