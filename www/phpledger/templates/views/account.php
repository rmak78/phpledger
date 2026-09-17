<?php
declare(strict_types=1);

// Presentation only: the statement service supplies every signed balance.
$statementBalance = static function (string $amount): string {
    $comparison = bccomp($amount, '0', 4);
    return pl_money($comparison === 0 ? '0.0000' : ($comparison < 0 ? substr($amount, 1) : $amount))
        . ($comparison > 0 ? ' Dr' : ($comparison < 0 ? ' Cr' : ''));
};
$statementFrom = $activity['from'] ?? $from ?? null;
$firstPage = ($activity['page'] ?? 1) === 1;
$lastPage = ($activity['page'] ?? 1) === ($activity['pages'] ?? 1);
?>
<section class="page-wrap account-statement" aria-labelledby="account-title">
    <?php if (pl_web_text($_GET,'return_report')==='profit-loss'): ?><a class="btn btn-ghost" href="<?= pl_e(pl_url('/reports/profit-loss',['from'=>$statementFrom,'to'=>$asOf,'preset'=>'custom'])) ?>"><?= pl_icon('arrow-left') ?> Back to profit &amp; loss</a><?php endif; ?>
    <div class="page-heading">
        <div>
            <p class="eyebrow"><?= pl_e((string) $company['name']) ?> &middot; <?= pl_e((string) $company['currency']) ?></p>
            <h1 id="account-title"><?= $activity ? 'Account statement' : 'Account ledger' ?></h1>
            <?php if ($activity): ?>
            <p class="statement-account-name"><strong><?= pl_e((string) $activity['account']['code']) ?> &middot; <?= pl_e((string) $activity['account']['name']) ?></strong><?php if (!$activity['account']['is_active']): ?> <span class="badge">Inactive account</span><?php endif; ?></p>
            <p class="muted"><?= $statementFrom !== null ? pl_e(pl_date_label($statementFrom)) . ' &ndash; ' : 'All posted history through ' ?><?= pl_e(pl_date_label($asOf)) ?></p>
            <?php else: ?><p class="muted">Choose an account to see every posted movement and its running balance.</p><?php endif; ?>
        </div>
        <a class="button secondary" href="<?= pl_e(pl_url('/reports')) ?>">Back to reports</a>
    </div>

    <?php if ($company['setup_status'] !== 'ready'): ?>
        <div class="statement-setup-note" role="note"><strong>Opening balances still need review.</strong> This statement includes only recorded journals. Complete the business setup and reconcile opening balances and unpaid documents before treating these balances as complete.</div>
    <?php endif; ?>

    <form action="<?= pl_e(pl_url('/reports/account')) ?>" method="get" class="panel actions statement-filters">
        <div class="field statement-account-field"><label for="statement-account">Account</label><select id="statement-account" name="id" required><option value="">Choose an account</option><?php foreach ($company['accounts'] as $choice): ?><option value="<?= pl_e((string) $choice['id']) ?>" <?= (int) $choice['id'] === (int) ($activity['account']['id'] ?? 0) ? 'selected' : '' ?>><?= pl_e($choice['code'] . ' - ' . $choice['name'] . (!$choice['is_active'] ? ' (inactive)' : '')) ?></option><?php endforeach; ?></select></div>
        <div class="field"><label for="activity-from">From date <span class="optional">optional</span></label><input id="activity-from" name="from" type="date" value="<?= pl_e($statementFrom ?? '') ?>"></div>
        <div class="field"><label for="activity-date">Through date</label><input id="activity-date" name="as_of" type="date" required value="<?= pl_e($asOf) ?>"></div>
        <button class="button secondary" type="submit"><?= $activity ? 'Update statement' : 'Open statement' ?></button>
        <?php pl_ui_list_controls($filters); ?>
        <?php if ($activity): ?><a class="button secondary" href="<?= pl_e(pl_url('/reports/export', ['report' => 'account', 'account_id' => $activity['account']['id'], 'from' => $statementFrom ?? '', 'to' => $asOf])) ?>">Export all pages CSV</a><?php endif; ?>
    </form>

    <?php if (!$activity): ?><div class="statement-empty"><h2>Opening balance, movement and closing balance</h2><p>View cash, bank, income, expenses or any other ledger account. Each posted debit and credit updates that account's running balance. Drafts are excluded; earlier balances carry forward when you choose a date range.</p></div></section><?php return; endif; ?>

    <dl class="statement-summary" aria-label="Statement balances across all pages">
        <div><dt>Opening balance</dt><dd class="amount"><?= pl_e($statementBalance((string) $activity['opening_balance'])) ?></dd><dd class="statement-summary-note"><?= $statementFrom !== null ? 'Before ' . pl_e(pl_date_label($statementFrom)) : 'Before recorded history' ?></dd></div>
        <div><dt>Total debits</dt><dd class="amount"><?= pl_e(pl_money((string) $activity['debit_movement'])) ?></dd><dd class="statement-summary-note">In the selected period</dd></div>
        <div><dt>Total credits</dt><dd class="amount"><?= pl_e(pl_money((string) $activity['credit_movement'])) ?></dd><dd class="statement-summary-note">In the selected period</dd></div>
        <div class="statement-closing"><dt>Closing balance</dt><dd class="amount"><?= pl_e($statementBalance((string) $activity['closing_balance'])) ?></dd><dd class="statement-summary-note">Through <?= pl_e(pl_date_label($asOf)) ?></dd></div>
    </dl>
    <p class="statement-key muted">Opening balance + debits &minus; credits = closing balance. Dr means debit; Cr means credit. Totals cover all pages and exclude drafts.</p>

    <?php if ($activity['movements'] === []): ?>
        <div class="statement-empty"><h2>No movements in this period</h2><p>The opening balance carries forward unchanged. Widen the date range to explore earlier entries. Saved drafts do not appear in this statement.</p></div>
    <?php endif; ?>

    <div class="panel table-wrap statement-table-wrap" tabindex="0" role="region" aria-label="Account statement entries; scroll horizontally on smaller screens">
        <table class="table account-ledger-table">
            <caption>Posted entries in <?= pl_e((string) $company['currency']) ?> &middot; Date, journal and line order</caption>
            <thead><tr><th scope="col"><?php pl_ui_sort('/reports/account', $filters, 'date', 'Date'); ?></th><th scope="col"><?php pl_ui_sort('/reports/account', $filters, 'journal', 'Journal'); ?></th><th scope="col"><?php pl_ui_sort('/reports/account', $filters, 'description', 'Description'); ?></th><th scope="col"><?php pl_ui_sort('/reports/account', $filters, 'source', 'Source'); ?></th><th scope="col" class="amount"><?php pl_ui_sort('/reports/account', $filters, 'debit', 'Debit'); ?></th><th scope="col" class="amount"><?php pl_ui_sort('/reports/account', $filters, 'credit', 'Credit'); ?></th><th scope="col" class="amount"><?php pl_ui_sort('/reports/account', $filters, 'balance', 'Running balance'); ?></th></tr></thead>
            <tbody>
                <tr class="statement-forward-row">
                    <th colspan="6" scope="row"><?= $firstPage ? 'Opening balance' : 'Brought forward from previous page' ?><span class="row-secondary"><?= $firstPage ? ($statementFrom !== null ? 'Before ' . pl_e(pl_date_label($statementFrom)) : 'Before recorded history') : 'Balance before the first entry on this page' ?></span></th>
                    <td class="amount"><?= pl_e($statementBalance((string) $activity['page_opening_balance'])) ?></td>
                </tr>
                <?php foreach ($activity['movements'] as $row): ?>
                    <tr>
                        <td><?= pl_e(pl_date_label((string) $row['date'])) ?></td>
                        <td><a href="<?= pl_e(pl_url('/journals/detail', ['id' => $row['journal_id']])) ?>"><?= pl_e((string) $row['journal_reference']) ?></a><?php if ($row['reversal_of_id'] !== null): ?> <span class="badge">Reversal</span><?php endif; ?></td>
                        <td><?= pl_e((string) $row['description']) ?></td>
                        <td><?php if ($row['document_id'] !== null): ?><a href="<?= pl_e(pl_url('/transactions/detail', ['id' => $row['document_id']])) ?>">View transaction</a><?php elseif ($row['general_id'] !== null): ?><a href="<?= pl_e(pl_url('/general-journals/detail', ['id' => $row['general_id']])) ?>">View general journal</a><?php else: ?><?= pl_e(ucfirst((string) $row['source_type'])) ?><?php endif; ?></td>
                        <td class="amount" data-label="Debit"><?= pl_e(pl_money((string) $row['debit'])) ?></td>
                        <td class="amount" data-label="Credit"><?= pl_e(pl_money((string) $row['credit'])) ?></td>
                        <td class="amount statement-running" data-label="Running balance"><?= pl_e($statementBalance((string) $row['running_balance'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="statement-forward-row statement-ending-row">
                    <th colspan="6" scope="row"><?= $lastPage ? 'Closing balance' : 'Carried forward to next page' ?><span class="row-secondary"><?= $lastPage ? 'Through ' . pl_e(pl_date_label($asOf)) : 'Balance after the last entry on this page' ?></span></th>
                    <td class="amount"><?= pl_e($statementBalance((string) $activity['page_closing_balance'])) ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <?php if ($activity['movements'] !== []): ?>
        <nav class="actions statement-pagination" aria-label="Account statement pages">
            <?php if (!$firstPage): ?><a class="button secondary" href="<?= pl_e(pl_url('/reports/account', array_replace($filters, ['page' => $activity['page'] - 1]))) ?>">Previous page</a><?php endif; ?>
            <p class="muted">Page <?= pl_e((string) $activity['page']) ?> of <?= pl_e((string) $activity['pages']) ?> &middot; <?= pl_e((string) $activity['total']) ?> posted lines</p>
            <?php if (!$lastPage): ?><a class="button secondary" href="<?= pl_e(pl_url('/reports/account', array_replace($filters, ['page' => $activity['page'] + 1]))) ?>">Next page</a><?php endif; ?>
        </nav>
    <?php endif; ?>
</section>
