<?php
declare(strict_types=1);
$documentSource = null;
if (preg_match('/^document:([1-9][0-9]*)$/D', (string) $journal['source_reference'], $sourceMatch)) {
    $documentSource = $sourceMatch[1];
}
?>
<section class="page-wrap" aria-labelledby="journal-title">
    <div class="page-heading">
        <div><p class="eyebrow">Posted journal</p><h1 id="journal-title"><?= pl_e((string) $journal['reference']) ?></h1><p class="muted"><?= pl_e((string) $company['name']) ?> · <?= pl_e((string) $journal['currency']) ?> · <?= pl_e(pl_date_label((string) $journal['journal_date'])) ?></p></div>
        <a class="button secondary" href="<?= pl_e(pl_url('/reports/trial-balance')) ?>">Trial balance</a>
    </div>
    <div class="panel">
        <h2>Entry details</h2>
        <p><?= pl_e((string) $journal['description']) ?></p>
        <dl class="form-grid">
            <div><dt>Source type</dt><dd><?= pl_e(ucfirst((string) $journal['source_type'])) ?></dd></div>
            <div><dt>Source reference</dt><dd><?= pl_e((string) $journal['source_reference']) ?></dd></div>
            <div><dt>Posted at</dt><dd><time datetime="<?= pl_e(str_replace(' ', 'T', (string) $journal['posted_at']) . 'Z') ?>" data-local-time><?= pl_e((string) $journal['posted_at']) ?> UTC</time></dd></div>
        </dl>
        <div class="actions">
            <?php if ($documentSource !== null): ?><a class="button secondary" href="<?= pl_e(pl_url('/transactions/detail', ['id' => $documentSource])) ?>">View source transaction</a><?php endif; ?>
            <?php if ($journal['source_type'] === 'opening_balance'): ?><a class="button secondary" href="<?= pl_e(pl_url('/opening-balances')) ?>">View opening cutover</a><?php endif; ?>
            <?php if ($journal['reversal_of_id'] !== null): ?><a class="button secondary" href="<?= pl_e(pl_url('/journals/detail', ['id' => $journal['reversal_of_id']])) ?>">View original journal</a><?php endif; ?>
        </div>
        <?php if ($journal['reversal_of_id'] !== null): ?><p class="badge">Linked reversal</p><?php endif; ?>
        <p class="muted">Posted entries are preserved. Corrections use a linked reversal, so the original and the correction remain traceable.</p>
    </div>
    <div class="panel table-wrap" tabindex="0" role="region" aria-label="Posted journal lines">
        <table class="data-table">
            <caption>Journal lines in <?= pl_e((string) $journal['currency']) ?></caption>
            <thead><tr><th scope="col">Account</th><th scope="col">Description</th><th scope="col" class="amount">Debit</th><th scope="col" class="amount">Credit</th></tr></thead>
            <tbody>
                <?php foreach ($journal['lines'] as $line): ?>
                    <tr><th scope="row"><a href="<?= pl_e(pl_url('/reports/account', ['id' => $line['account_id'], 'as_of' => $journal['journal_date']])) ?>"><?= pl_e($line['code'] . ' — ' . $line['name']) ?></a></th><td><?= pl_e((string) $line['description']) ?></td><td class="amount"><?= pl_e(pl_money((string) $line['debit'])) ?></td><td class="amount"><?= pl_e(pl_money((string) $line['credit'])) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
