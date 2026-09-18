<?php declare(strict_types=1);
$labels = ['not_due'=>'Not overdue','1_30'=>'1–30 days','31_60'=>'31–60 days','61_90'=>'61–90 days','over_90'=>'Over 90 days'];
$sourcePath = $report['direction'] === 'receivable' ? '/ar' : '/ap';
?>
<div class="flex flex-col gap-4 py-5">
<?php pl_ui_page_header('Receivables & payables ageing', $company['name'] . ' · Unpaid balances at ' . pl_date_label($report['as_of'])); ?>
<form method="get" action="<?= pl_e(pl_url('/reports/ageing')) ?>" class="filter-bar" data-fold="primary action">
    <label class="field">Balance type<select class="select" name="direction"><option value="receivable" <?= $report['direction']==='receivable'?'selected':'' ?>>Receivables</option><option value="payable" <?= $report['direction']==='payable'?'selected':'' ?>>Payables</option></select></label>
    <label class="field">As of<input class="input" type="date" name="as_of" value="<?= pl_e($report['as_of']) ?>" required></label>
    <button class="btn btn-primary">Update report</button><a class="btn btn-ghost" href="<?= pl_e(pl_url('/reports')) ?>">All reports</a>
</form>
<p class="text-xs text-ink-muted">Days past the due date. Totals use recorded carrying amounts in <?= pl_e($company['currency']) ?>; document currency balances appear beside them. Drafts are excluded.</p>
<div class="grid grid-cols-2 gap-3 min-[900px]:grid-cols-5">
<?php foreach ($labels as $key=>$label): ?><section class="panel !m-0"><h2 class="text-xs text-ink-muted"><?= pl_e($label) ?></h2><p class="amount mt-2 text-lg font-semibold"><?= pl_e(pl_money($report['buckets'][$key])) ?></p></section><?php endforeach; ?>
</div>
<?php pl_ui_strip($report['reconciled']?'Open items reconcile to the control accounts.':'Control accounts and open items differ. Review the reconciliation below before relying on this report.', $report['reconciled']?'success':'warning'); ?>
<section class="panel !m-0"><div class="flex justify-between gap-3 mb-3"><h2 class="section-title"><?= (int)$report['count'] ?> unpaid documents</h2><strong class="amount">Total <?= pl_e($company['currency'].' '.pl_money($report['total_base'])) ?></strong></div>
<div class="table-wrap"><table class="table"><thead><tr><th>Customer / vendor</th><th>Document</th><th>Due date</th><th>Age</th><th class="text-end">Document balance</th><th class="text-end"><?= pl_e($company['currency']) ?> balance</th></tr></thead><tbody>
<?php foreach ($report['items'] as $item): ?><tr><th scope="row"><?= pl_e($item['legal_name']) ?></th><td><?php if ($item['document_id']): ?><a class="link" href="<?= pl_e(pl_url($sourcePath,['id'=>$item['document_id'],'as_of'=>$report['as_of'],'return_report'=>'ageing'])) ?>"><?= pl_e($item['number']) ?></a><?php else: ?><a class="link" href="<?= pl_e(pl_url('/opening-conversion')) ?>"><?= pl_e($item['number']) ?></a><?php endif; ?></td><td><?= pl_e($item['due_date'] ?? '—') ?></td><td><?= pl_e($labels[$item['bucket']]) ?></td><td class="amount"><?= pl_e($item['currency'].' '.pl_money($item['remaining_fc'])) ?></td><td class="amount"><?= pl_e(pl_money($item['remaining_base'])) ?></td></tr><?php endforeach; ?>
<?php if ($report['items']===[]): ?><tr><td colspan="6">No unpaid documents at this date.</td></tr><?php endif; ?>
</tbody></table></div></section>
<details class="panel !m-0" <?= !$report['reconciled']?'open':'' ?>><summary class="section-title">Control-account reconciliation</summary><div class="table-wrap mt-3"><table class="table"><thead><tr><th>Account</th><th class="text-end">Ledger</th><th class="text-end">Open items</th><th class="text-end">Difference</th></tr></thead><tbody>
<?php foreach ($report['controls'] as $control): ?><tr><th scope="row"><?= pl_e($control['code'].' · '.$control['name']) ?></th><td class="amount"><?= pl_e(pl_money($control['ledger_base'])) ?></td><td class="amount"><?= pl_e(pl_money($control['open_items_base'])) ?></td><td class="amount"><?= pl_e(pl_money($control['difference_base'])) ?></td></tr><?php endforeach; ?>
</tbody></table></div></details>
</div>
