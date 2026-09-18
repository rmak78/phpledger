<?php declare(strict_types=1); ?>
<div class="flex flex-col gap-4 py-5">
<?php pl_ui_page_header('Balance sheet',$company['name'].' · As of '.pl_date_label($asOf).' · Posted entries only',static function () use ($asOf): void { ?>
<a class="btn btn-secondary" href="<?= pl_e(pl_url('/reports/export',['report'=>'balance-sheet','to'=>$asOf])) ?>"><?= pl_icon('download') ?> Export CSV</a>
<?php }); ?>
<div class="grid grid-cols-2 md:grid-cols-4 gap-px overflow-hidden rounded-panel border border-border bg-border">
<?php foreach (['total_assets'=>'Total assets','total_liabilities'=>'Total liabilities','total_equity'=>'Equity including earned profit'] as $key=>$label): ?><div class="bg-surface px-4 py-3"><p class="text-xs text-ink-muted"><?= pl_e($label) ?></p><p class="amount-lg mt-1"><?= pl_e(pl_money($report[$key])) ?></p></div><?php endforeach; ?>
<div class="flex flex-col justify-center gap-1 bg-surface px-4 py-3"><strong class="<?= $report['balanced']?'balanced-label':'text-danger' ?>"><?= $report['balanced']?'Assets = liabilities + equity':'This statement needs review' ?></strong><span class="text-xs tabular-nums text-ink-muted"><?= pl_e(pl_money($report['total_assets'])) ?> <?= $report['balanced']?'=':'≠' ?> <?= pl_e(pl_money($report['total_liabilities_equity']).' '.$company['currency']) ?></span></div></div>
<form method="get" action="<?= pl_e(pl_url('/reports/balance-sheet')) ?>" class="filter-bar"><label class="field">As of date<input class="input" type="date" name="as_of" value="<?= pl_e($asOf) ?>" required></label><button class="btn btn-secondary">Update</button><a class="btn btn-ghost" href="<?= pl_e(pl_url('/reports')) ?>">All reports</a></form>
<div class="table-wrap p-2 max-w-[60rem]" tabindex="0" role="region" aria-label="Balance sheet accounts"><table class="stmt-table"><thead><tr><th scope="col">Account</th><th scope="col" class="num"><?= pl_e($company['currency']) ?></th></tr></thead><tbody>
<?php foreach (['assets'=>'Assets','liabilities'=>'Liabilities','equity'=>'Equity'] as $key=>$label): ?>
<tr class="stmt-row"><th scope="rowgroup" class="stmt-group-label"><?= pl_e($label) ?></th><td></td></tr>
<?php foreach ($report[$key] as $row): ?><tr class="stmt-row stmt-indent-1"><th scope="row"><span class="me-1 text-xs text-ink-muted"><?= pl_e($row['code']) ?></span><a class="link" href="<?= pl_e(pl_url('/reports/account',['id'=>$row['id'],'as_of'=>$asOf,'return_report'=>'balance-sheet'])) ?>"><?= pl_e($row['name']) ?></a></th><td class="num"><?= pl_e(pl_money($row['amount'])) ?></td></tr><?php endforeach; ?>
<?php if ($key==='equity'): ?><tr class="stmt-row stmt-indent-1"><th scope="row">Earned profit to date<span class="block text-xs font-normal text-ink-muted">Unclosed income less expenses</span></th><td class="num"><?= pl_e(pl_money($report['earned_profit'])) ?></td></tr><?php endif; ?>
<tr class="stmt-row <?= $key==='equity'?'stmt-total':'stmt-subtotal' ?>"><th scope="row">Total <?= pl_e(strtolower($label)) ?></th><td class="num"><?= pl_e(pl_money($report['total_'.$key])) ?></td></tr>
<?php endforeach; ?></tbody></table></div>
<p class="text-xs text-ink-muted">Amounts in <?= pl_e($company['currency']) ?> · Posted entries only. Earned profit is shown separately from recorded equity. Account classification and completeness still need review; balanced totals alone do not establish correct books.</p>
</div>
