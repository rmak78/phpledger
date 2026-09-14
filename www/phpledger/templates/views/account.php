<?php declare(strict_types=1); ?>
<section class="page-wrap" aria-labelledby="account-title">
    <div class="page-heading">
        <div><p class="eyebrow">Account activity · <?= pl_e((string) $activity['account']['code']) ?></p><h1 id="account-title"><?= pl_e((string) $activity['account']['name']) ?></h1><p class="muted"><?= pl_e((string) $company['name']) ?> · <?= pl_e((string) $company['currency']) ?> · Through <?= pl_e(pl_date_label($asOf)) ?></p></div>
        <a class="button secondary" href="<?= pl_e(($activity['from'] ?? null) ? pl_url('/reports/profit-loss', ['from' => $activity['from'], 'to' => $asOf]) : pl_url('/reports/trial-balance', ['as_of' => $asOf])) ?>">Back to <?= ($activity['from'] ?? null) ? 'profit & loss' : 'trial balance' ?></a>
    </div>
    <form action="<?= pl_e(pl_url('/reports/account')) ?>" method="get" class="panel actions">
        <input type="hidden" name="id" value="<?= pl_e((string) $activity['account']['id']) ?>">
        <div class="field"><label for="activity-from">From date <span class="optional">optional</span></label><input id="activity-from" name="from" type="date" value="<?= pl_e($activity['from'] ?? '') ?>"></div>
        <div class="field"><label for="activity-date">Through date</label><input id="activity-date" name="as_of" type="date" required value="<?= pl_e($asOf) ?>"></div>
        <button class="button secondary" type="submit">Update activity</button>
    </form>
    <div class="panel">
        <dl class="form-grid">
            <div><dt>Total debits</dt><dd class="amount"><?= pl_e(pl_money((string) $activity['debit_movement'])) ?></dd></div>
            <div><dt>Total credits</dt><dd class="amount"><?= pl_e(pl_money((string) $activity['credit_movement'])) ?></dd></div>
            <div><dt>Net debit minus credit</dt><dd class="amount"><?= pl_e(pl_money((string) $activity['balance'])) ?></dd></div>
        </dl>
        <p class="muted">Totals include posted activity <?= ($activity['from'] ?? null) ? 'from ' . pl_e(pl_date_label($activity['from'])) . ' ' : '' ?>through this date, across all pages. A negative net balance means credits exceed debits.</p>
    </div>
    <?php if ($activity['movements'] === []): ?>
        <div class="panel"><h2>No posted activity through this date</h2><p>Saved drafts do not appear here. Return to transactions to review or post a receipt or expense when the business is ready.</p><a class="button secondary" href="<?= pl_e(pl_url('/transactions')) ?>">Open transactions</a></div>
    <?php else: ?>
        <div class="panel table-wrap" tabindex="0" role="region" aria-label="Posted account activity">
            <table class="data-table">
                <caption>Posted activity in <?= pl_e((string) $company['currency']) ?></caption>
                <thead><tr><th scope="col">Date</th><th scope="col">Journal</th><th scope="col">Description</th><th scope="col">Source</th><th scope="col" class="amount">Debit</th><th scope="col" class="amount">Credit</th></tr></thead>
                <tbody>
                    <?php foreach ($activity['movements'] as $row): ?>
                        <tr>
                            <td><?= pl_e(pl_date_label((string) $row['date'])) ?></td>
                            <td><a href="<?= pl_e(pl_url('/journals/detail', ['id' => $row['journal_id']])) ?>"><?= pl_e((string) $row['journal_reference']) ?></a><?php if ($row['reversal_of_id'] !== null): ?> <span class="badge">Reversal</span><?php endif; ?></td>
                            <td><?= pl_e((string) $row['description']) ?></td>
                            <td><?php if ($row['document_id'] !== null): ?><a href="<?= pl_e(pl_url('/transactions/detail', ['id' => $row['document_id']])) ?>">View transaction</a><?php else: ?><?= pl_e(ucfirst((string) $row['source_type'])) ?><?php endif; ?></td>
                            <td class="amount"><?= pl_e(pl_money((string) $row['debit'])) ?></td>
                            <td class="amount"><?= pl_e(pl_money((string) $row['credit'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <nav class="actions" aria-label="Account activity pages">
            <?php if ($activity['page'] > 1): ?><a class="button secondary" href="<?= pl_e(pl_url('/reports/account', ['id' => $activity['account']['id'], 'as_of' => $asOf, 'from' => $activity['from'] ?? null, 'page' => $activity['page'] - 1])) ?>">Previous page</a><?php endif; ?>
            <p class="muted">Page <?= pl_e((string) $activity['page']) ?> of <?= pl_e((string) $activity['pages']) ?> · <?= pl_e((string) $activity['total']) ?> posted lines</p>
            <?php if ($activity['page'] < $activity['pages']): ?><a class="button secondary" href="<?= pl_e(pl_url('/reports/account', ['id' => $activity['account']['id'], 'as_of' => $asOf, 'from' => $activity['from'] ?? null, 'page' => $activity['page'] + 1])) ?>">Next page</a><?php endif; ?>
        </nav>
    <?php endif; ?>
</section>
