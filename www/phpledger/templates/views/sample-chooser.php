<?php declare(strict_types=1); ?>
<section class="page-wrap sample-chooser" aria-labelledby="sample-chooser-title">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Local synthetic catalogue</p>
            <h1 id="sample-chooser-title">Choose one sample company</h1>
            <p class="muted">Each choice creates one new, isolated company and book. Your existing businesses are never used as a target and are not changed.</p>
        </div>
        <a class="button secondary" href="<?= pl_e(pl_url('/companies')) ?>">Back to businesses</a>
    </div>
    <?php if ($form['message'] !== ''): ?><div class="alert" role="alert" tabindex="-1"><h2>Choose a sample</h2><p><?= pl_e((string) $form['message']) ?></p></div><?php endif; ?>
    <form method="post" action="<?= pl_e(pl_url('/sample-chooser')) ?>" class="panel form-grid">
        <?= pl_csrf_field() ?>
        <div class="field full-width">
            <label for="sample-pack">Sample company</label>
            <select id="sample-pack" name="sample_pack" required>
                <?php foreach (pl_demo_sample_choices() as $entry): ?>
                    <?php $label = $entry['id'] === 'accounting-starter' ? (string) $entry['name'] : (string) $entry['business'] . ' — ' . (string) $entry['name']; ?>
                    <option value="<?= pl_e((string) $entry['id']) ?>"<?= pl_web_text($form['input'], 'sample_pack') === $entry['id'] ? ' selected' : '' ?>><?= pl_e($label) ?></option>
                <?php endforeach; ?>
            </select>
            <p class="muted">The historical companies contain pinned synthetic source records, closed 2024–2025 history, an open 2026 practice year and editable drafts. The starter playground begins at zero balances.</p>
        </div>
        <div class="field">
            <label for="sample-currency">Functional currency</label>
            <select id="sample-currency" name="currency" required>
                <?php foreach (pl_base_currency_options() as $code => $label): ?><option value="<?= pl_e($code) ?>"<?= pl_web_text($form['input'], 'currency', 'USD') === $code ? ' selected' : '' ?>><?= pl_e($label) ?></option><?php endforeach; ?>
            </select>
            <p class="muted">Amounts are illustrative. Country tax and statutory rules are not enabled by choosing a sample.</p>
        </div>
        <div class="field full-width">
            <p class="small muted">Samples are synthetic teaching books. Provisioning uses the bundled, checksummed catalogue only and never sends messages, payments or provider requests.</p>
        </div>
        <div class="actions full-width"><button class="button primary" type="submit">Create this separate sample</button><a class="button secondary" href="<?= pl_e(pl_url('/companies')) ?>">Cancel</a></div>
    </form>
</section>
