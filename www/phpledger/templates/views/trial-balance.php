<?php declare(strict_types=1); ?>
<section class="flex flex-col gap-4 py-5" aria-labelledby="trial-title">
<?php pl_ui_page_header('Trial balance',$company['name'].' · '.$company['currency'].' · Through '.pl_date_label($asOf).' · Posted entries only',static function () use ($asOf): void { ?>
<a class="btn btn-secondary" href="<?= pl_e(pl_url('/reports/export',['report'=>'trial-balance','to'=>$asOf])) ?>"><?= pl_icon('download') ?> Export CSV</a>
<?php },'trial-title'); ?>
<form action="<?= pl_e(pl_url('/reports/trial-balance')) ?>" method="get" class="filter-bar">
<div class="field"><label class="field-label" for="trial-date">Through date</label><input class="input" id="trial-date" name="as_of" type="date" required value="<?= pl_e($asOf) ?>"></div>
<button class="btn btn-secondary" type="submit">Update report</button><a class="btn btn-ghost" href="<?= pl_e(pl_url('/reports')) ?>">All reports</a>
</form>
<?php if ($company['setup_status']!=='ready'): ?><p class="alert alert-info">Opening review is outstanding. This report shows the records currently in these books; it is not confirmation that all opening balances or past documents are included.</p><?php endif; ?>
<?php if (!$report['balanced']): ?><div class="alert alert-danger" role="alert"><h2>The debit and credit totals do not agree</h2><p>Stop and ask the person responsible for these books to investigate before relying on this report.</p></div><?php endif; ?>
<div class="table-wrap" tabindex="0" role="region" aria-label="Trial balance accounts and totals"><table class="table"><caption class="sr-only">Account balances in <?= pl_e($company['currency']) ?> through <?= pl_e(pl_date_label($asOf)) ?></caption>
<thead><tr><th scope="col">Code</th><th scope="col">Account</th><th scope="col">Type</th><th scope="col" class="num">Debit</th><th scope="col" class="num">Credit</th></tr></thead><tbody>
<?php foreach ($report['accounts'] as $row): ?><tr><td class="text-ink-muted"><?= pl_e($row['code']) ?></td><th scope="row"><a class="link" href="<?= pl_e(pl_url('/reports/account',['id'=>$row['id'],'as_of'=>$asOf,'return_report'=>'trial-balance'])) ?>"><?= pl_e($row['name']) ?></a></th><td class="text-ink-muted"><?= pl_e(ucfirst($row['type'])) ?></td><td class="num"><?= pl_e(pl_money($row['debit'])) ?></td><td class="num"><?= pl_e(pl_money($row['credit'])) ?></td></tr><?php endforeach; ?>
<?php if ($report['accounts']===[]): ?><tr><td colspan="5">No accounts are available in this book.</td></tr><?php endif; ?>
</tbody><tfoot><tr class="table-totals"><th scope="row" colspan="3">Total</th><td class="num"><?= pl_e(pl_money($report['total_debit'])) ?></td><td class="num"><?= pl_e(pl_money($report['total_credit'])) ?></td></tr></tfoot></table></div>
<?php if ($report['balanced']): ?><p class="balanced-label"><?= pl_icon('check') ?> Debit and credit totals agree</p><?php endif; ?>
<p class="text-xs text-ink-muted">Select an account to see its posted activity. Equal totals confirm that posted debits and credits balance. They do not by themselves establish that every transaction has been recorded or categorized correctly.</p>
</section>
