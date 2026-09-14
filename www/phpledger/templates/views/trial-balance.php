<?php declare(strict_types=1); ?>
<section class="page-wrap" aria-labelledby="trial-title">
    <div class="page-heading">
        <div><p class="eyebrow">Reports</p><h1 id="trial-title">Trial balance</h1><p class="muted"><?= pl_e((string) $company['name']) ?> · <?= pl_e((string) $company['currency']) ?> · Through <?= pl_e(pl_date_label($asOf)) ?></p></div>
        <a class="button secondary" href="<?= pl_e(pl_url('/transactions')) ?>">Back to transactions</a>
    </div>
    <form action="<?= pl_e(pl_url('/reports/trial-balance')) ?>" method="get" class="panel actions">
        <div class="field"><label for="trial-date">Through date</label><input id="trial-date" name="as_of" type="date" required value="<?= pl_e($asOf) ?>"></div>
        <button class="button secondary" type="submit">Update report</button>
    </form>
    <p>Only posted entries are included. Select an account to see its activity and return to the source transaction. Drafts do not change these balances.</p>
    <?php if ($company['setup_status'] !== 'ready'): ?>
        <p class="alert">Opening review is outstanding. This report shows the records currently in these books; it is not confirmation that all opening balances or past documents are included.</p>
    <?php endif; ?>
    <?php if (!$report['balanced']): ?>
        <div class="alert" role="alert"><h2>The debit and credit totals do not agree</h2><p>Stop and ask the person responsible for these books to investigate before relying on this report.</p></div>
    <?php endif; ?>
    <div class="panel table-wrap" tabindex="0" role="region" aria-label="Trial balance accounts and totals">
        <table class="data-table">
            <caption>Account balances in <?= pl_e((string) $company['currency']) ?> through <?= pl_e(pl_date_label($asOf)) ?></caption>
            <thead><tr><th scope="col">Code</th><th scope="col">Account</th><th scope="col" class="amount">Debit</th><th scope="col" class="amount">Credit</th></tr></thead>
            <tbody>
                <?php foreach ($report['accounts'] as $row): ?>
                    <tr><td><?= pl_e((string) $row['code']) ?></td><th scope="row"><a href="<?= pl_e(pl_url('/reports/account', ['id' => $row['id'], 'as_of' => $asOf])) ?>"><?= pl_e((string) $row['name']) ?></a></th><td class="amount"><?= pl_e(pl_money((string) $row['debit'])) ?></td><td class="amount"><?= pl_e(pl_money((string) $row['credit'])) ?></td></tr>
                <?php endforeach; ?>
                <?php if ($report['accounts'] === []): ?><tr><td colspan="4">No accounts are available in this book.</td></tr><?php endif; ?>
            </tbody>
            <tfoot><tr><th scope="row" colspan="2">Total</th><td class="amount"><?= pl_e(pl_money((string) $report['total_debit'])) ?></td><td class="amount"><?= pl_e(pl_money((string) $report['total_credit'])) ?></td></tr></tfoot>
        </table>
    </div>
    <?php if ($report['balanced']): ?><p class="badge">Debit and credit totals agree</p><?php endif; ?>
    <p class="muted">Equal totals confirm that the posted debits and credits balance. They do not by themselves establish that every transaction has been recorded or categorized correctly.</p>
</section>
