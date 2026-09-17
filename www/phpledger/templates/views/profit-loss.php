<?php declare(strict_types=1); ?>
<div class="flex flex-col gap-4 py-5">
<?php pl_ui_page_header('Profit & loss', $company['name'].' · '.pl_date_label($from).' to '.pl_date_label($to).' · Posted entries only', static function () use ($from,$to): void { ?>
<a class="btn btn-secondary" href="<?= pl_e(pl_url('/reports/export',['report'=>'profit-loss','from'=>$from,'to'=>$to])) ?>"><?= pl_icon('download') ?> Export CSV</a>
<?php }); ?>
<form method="get" action="<?= pl_e(pl_url('/reports/profit-loss')) ?>" class="filter-bar" data-report-period data-fold="primary action">
<div class="field"><label for="report-preset">Period</label><select id="report-preset" class="select" name="preset"><?php foreach (['month'=>'This month','last_month'=>'Last month','quarter'=>'This quarter','year'=>'This year','custom'=>'Custom'] as $value=>$label): $dates=pl_report_period($value,gmdate('Y-m-d')); ?><option value="<?= pl_e($value) ?>" <?= $preset===$value?'selected':'' ?> <?= $dates?'data-from="'.pl_e($dates['from']).'" data-to="'.pl_e($dates['to']).'"':'' ?>><?= pl_e($label) ?></option><?php endforeach; ?></select></div>
<label class="field">From<input class="input" type="date" name="from" value="<?= pl_e($from) ?>" required></label><label class="field">To<input class="input" type="date" name="to" value="<?= pl_e($to) ?>" required></label><button class="btn btn-primary">Update</button><a class="btn btn-ghost" href="<?= pl_e(pl_url('/reports')) ?>">All reports</a>
</form>
<p class="text-xs text-ink-muted">Presets use calendar periods through today; last month covers the full month. Choose Custom for your own dates.</p>
<div class="table-wrap p-2 max-w-[60rem]"><table class="stmt-table"><thead><tr><th>Account</th><th class="num"><?= pl_e($company['currency']) ?></th></tr></thead><tbody>
<?php foreach (['income'=>'Income','cost_of_sales'=>'Cost of sales','expenses'=>'Expenses'] as $key=>$label): ?>
<tr class="stmt-row"><th scope="rowgroup" class="stmt-group-label"><?= pl_e($label) ?></th><td></td></tr>
<?php foreach ($report[$key] as $row): ?><tr class="stmt-row stmt-indent-1"><th scope="row"><span class="me-1 text-xs text-ink-muted"><?= pl_e($row['code']) ?></span><a class="link" href="<?= pl_e(pl_url('/reports/account',['id'=>$row['id'],'from'=>$from,'as_of'=>$to,'return_report'=>'profit-loss','return_preset'=>$preset])) ?>"><?= pl_e($row['name']) ?></a></th><td class="num"><?= pl_e(pl_money($row['amount'])) ?></td></tr><?php endforeach; ?>
<?php if ($key==='cost_of_sales' && $report[$key]===[]): ?><tr class="stmt-row"><td colspan="2" class="text-xs text-ink-muted">No accounts classified as cost of sales. Existing expenses stay in Expenses until explicitly classified.</td></tr><?php endif; ?>
<tr class="stmt-row stmt-subtotal"><th scope="row">Total <?= pl_e(strtolower($label)) ?></th><td class="num"><?= pl_e(pl_money($report['total_'.$key])) ?></td></tr>
<?php if ($key==='cost_of_sales'): ?><tr class="stmt-row stmt-total"><th scope="row">Gross profit</th><td class="num"><?= pl_e(pl_money($report['gross_profit'])) ?></td></tr><?php endif; ?>
<?php endforeach; ?>
<tr class="stmt-row stmt-total"><th scope="row">Net profit / loss</th><td class="num"><?= pl_e(pl_money($report['net_profit'])) ?></td></tr>
</tbody></table></div>
<p class="text-xs text-ink-muted">Includes posted entries and dated reversals. Account links retain this date range. Cost-of-sales classification changes presentation across all periods; it does not change posted entries or net profit.</p>
</div>
