<?php
declare(strict_types=1);
$startMode = pl_web_text($input, 'start_mode', 'fresh');
$businessName = pl_web_text($input, 'name');
$baseCurrency = pl_web_text($input, 'currency', $regional['currency'] ?? 'USD');
$startDate = pl_web_text($input, 'start_date', gmdate('Y-m-d'));
$fiscalYearEnd = pl_web_text($input, 'fiscal_year_end', '12-31');
$zeroConfirmed = ($input['zero_balances_confirmed'] ?? false) === true
    || pl_web_text($input, 'zero_balances_confirmed') === '1';
$startLabels = ['fresh' => 'Start a new business', 'existing' => 'Bring past records', 'sample' => 'Explore a sample company'];
?>
<section class="page-wrap" aria-labelledby="onboarding-title">
    <div class="page-heading">
        <div>
            <p class="eyebrow"><?= $preview ? 'Step 2 of 2' : 'Step 1 of 2' ?></p>
            <h1 id="onboarding-title"><?= $preview ? 'Review your setup' : 'Set up a business' ?></h1>
            <p class="muted"><?= $preview ? 'Check your details and starter accounts before creating the books.' : 'A few essentials first. Review the account template before anything is created.' ?></p>
        </div>
        <a class="button secondary" href="<?= pl_e(pl_url('/companies')) ?>">Back to businesses</a>
    </div>
    <?php if ($form['message'] !== ''): ?>
        <div class="alert" role="alert" tabindex="-1" id="setup-error" data-form-error>
            <h2>Check your setup</h2>
            <p><?= pl_e((string) $form['message']) ?></p>
            <p>Your setup details have been kept so you can correct them.</p>
        </div>
    <?php endif; ?>
    <?php if (!$preview): ?>
        <?php require __DIR__ . '/../partials/regional.php'; ?>
        <form action="<?= pl_e(pl_url('/onboarding')) ?>" method="post" class="panel form-grid">
            <?= pl_csrf_field() ?>
            <input type="hidden" name="action" value="preview">
            <fieldset class="field full-width">
                <legend>How would you like to start?</legend>
                <label class="checkbox"><input type="radio" name="start_mode" value="fresh"<?= $startMode === 'fresh' ? ' checked' : '' ?>> Start a new business with no prior balances</label>
                <label class="checkbox"><input type="radio" name="start_mode" value="existing"<?= $startMode === 'existing' ? ' checked' : '' ?>> Bring past records from an existing business</label>
                <label class="checkbox"><input type="radio" name="start_mode" value="sample"<?= $startMode === 'sample' ? ' checked' : '' ?>> Explore a separate sample company</label>
                <p class="muted">For an existing business, save setup and then preview its opening trial balance and unpaid documents. Posting starts after the confirmed cutover day. Sample setup uses its own fictional business name and records.</p>
            </fieldset>
            <div class="field full-width">
                <label for="business-name">Business name</label>
                <input id="business-name" name="name" type="text" maxlength="160" autocomplete="organization" value="<?= pl_e($businessName) ?>" aria-describedby="business-name-help">
                <p class="muted" id="business-name-help">Required for your own business. A sample company receives a separate fictional name.</p>
            </div>
            <div class="field">
                <label for="base-currency">Base currency</label>
                <select id="base-currency" name="currency" required aria-describedby="currency-help">
                    <?php foreach (pl_base_currency_options() as $code => $label): ?>
                        <option value="<?= pl_e($code) ?>"<?= $baseCurrency === $code ? ' selected' : '' ?>><?= pl_e($label) ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="muted" id="currency-help">Transactions use this one currency. Currency conversion and country tax rules are not included in this preview.</p>
            </div>
            <div class="field">
                <label for="accounting-start">Accounting start date</label>
                <input id="accounting-start" name="start_date" type="date" required value="<?= pl_e($startDate) ?>" aria-describedby="start-date-help">
                <p class="muted" id="start-date-help">The first date for these books. This business date stays the same in every timezone.</p>
            </div>
            <div class="field">
                <label for="fiscal-year-end">Fiscal year end</label>
                <input id="fiscal-year-end" name="fiscal_year_end" type="text" required pattern="[0-9]{2}-[0-9]{2}" maxlength="5" value="<?= pl_e($fiscalYearEnd) ?>" placeholder="12-31" aria-describedby="fiscal-help">
                <p class="muted" id="fiscal-help">Enter month-day, such as 12-31 for 31 December or 06-30 for 30 June.</p>
            </div>
            <div class="field full-width">
                <label class="checkbox"><input type="checkbox" name="zero_balances_confirmed" value="1"<?= $zeroConfirmed ? ' checked' : '' ?>> For a new business, I confirm there are no opening balances or unpaid invoices/bills to bring forward.</label>
                <p class="muted">Leave this unchecked when bringing past records. Sample companies do not require this confirmation.</p>
            </div>
            <div class="actions full-width">
                <button class="button primary" type="submit">Review setup and accounts</button>
                <a class="button secondary" href="<?= pl_e(pl_url('/companies')) ?>">Cancel</a>
            </div>
        </form>
    <?php else: ?>
        <div class="panel">
            <h2><?= pl_e($businessName) ?></h2>
            <dl class="form-grid">
                <div><dt>Starting point</dt><dd><?= pl_e($startLabels[$startMode] ?? $startMode) ?></dd></div>
                <div><dt>Base currency</dt><dd><?= pl_e($baseCurrency) ?></dd></div>
                <div><dt>Accounting start</dt><dd><?= pl_e(pl_date_label($startDate)) ?></dd></div>
                <div><dt>Fiscal year end</dt><dd><?= pl_e($fiscalYearEnd) ?> <span class="muted">(month-day)</span></dd></div>
            </dl>
            <?php if ($startMode === 'existing'): ?>
                <div class="alert"><h3>Next, review your opening balances</h3><p>This saves your business setup. Then enter or import a balanced trial balance and unpaid invoices/bills, review the preview, and confirm cutover. Receipt/expense entry stays unavailable until that review is complete.</p></div>
            <?php elseif ($startMode === 'sample'): ?>
                <p class="alert">This creates a clearly marked, separate sample company with fictional receipts, expenses, and saved drafts. Your real companies are not changed.</p>
            <?php else: ?>
                <p>No prior balances or unpaid documents will be loaded. Your new books start empty.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <section class="panel" aria-labelledby="starter-title">
        <p class="eyebrow">Account template preview</p>
        <h2 id="starter-title"><?= pl_e((string) $template['name']) ?></h2>
        <p><?= pl_e((string) $template['notice']) ?></p>
        <p class="muted">Version <?= pl_e((string) $template['version']) ?>. This preliminary template is recorded with your setup. Industry/country templates and accountant review remain later work.</p>
        <div class="table-wrap" tabindex="0" role="region" aria-label="Starter chart of accounts">
            <table class="data-table">
                <caption>Accounts that will be created for this business</caption>
                <thead><tr><th scope="col">Code</th><th scope="col">Account</th><th scope="col">Type</th></tr></thead>
                <tbody>
                    <?php foreach ($template['accounts'] as $definition): ?>
                        <tr><td><?= pl_e((string) $definition['code']) ?></td><td><?= pl_e((string) $definition['name']) ?></td><td><?= pl_e(ucfirst((string) $definition['type'])) ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php if ($preview): ?>
        <form action="<?= pl_e(pl_url('/onboarding')) ?>" method="post" class="actions">
            <?= pl_csrf_field() ?>
            <input type="hidden" name="action" value="confirm">
            <button class="button primary" type="submit"><?= $startMode === 'sample' ? 'Create separate sample company' : ($startMode === 'existing' ? 'Save setup with opening review pending' : 'Create business and accounts') ?></button>
            <a class="button secondary" href="<?= pl_e(pl_url('/onboarding')) ?>">Edit setup details</a>
        </form>
    <?php endif; ?>
</section>
