<?php declare(strict_types=1);
$values=[];
foreach (is_array($input['allocations']??null)?$input['allocations']:[] as $row) { if (is_array($row)) { $values[pl_web_id($row,'item_id')]=pl_web_text($row,'amount_fc'); } }
$receipt=$direction==='receivable';
?>
<div class="flex flex-col gap-4 py-5">
<?php pl_ui_page_header($receipt?'Record customer receipt':'Record supplier payment', 'Allocate one payment across open items for one party and currency.', static function () use ($items): void { if ($items!==[]): ?><button class="btn btn-primary" form="settlement-editor" name="action" value="preview_settlement">Update payment preview</button><?php endif; }); ?>
<?php if ($form['message']!==''): ?><div class="alert alert-danger" role="alert" tabindex="-1" data-form-error><?= pl_e($form['message']) ?></div><?php endif; ?>
<form method="get" action="<?= pl_e(pl_url($path)) ?>" class="filter-bar">
<input type="hidden" name="settle" value="1">
<?php pl_starter_select($receipt?'Customer':'Vendor','party_id',pl_starter_options(array_filter($parties,static fn(array $party):bool=>(bool)$party[$receipt?'is_customer':'is_vendor']),'legal_name'),$partyId); ?>
<?php pl_starter_select('Document currency','currency',array_combine($currencies,$currencies),$currency); ?>
<button class="btn btn-secondary">Show open items</button><a class="btn btn-ghost" href="<?= pl_e(pl_url($path)) ?>">Back to documents</a>
</form>
<?php if ($items===[]): ?><?php pl_ui_empty('Choose open items', 'Select a party and currency with unpaid balances. No payment is posted from this screen until you preview and confirm.'); ?>
<?php else: ?>
<form id="settlement-editor" method="post" action="<?= pl_e(pl_url($path)) ?>" data-settlement-form class="grid grid-cols-1 min-[900px]:grid-cols-[minmax(0,1fr)_300px] gap-4">
<?= pl_csrf_field() ?><?= pl_scope_fields($company) ?>
<input type="hidden" name="request_key" value="<?= pl_e(pl_web_text($input,'request_key',bin2hex(random_bytes(20)))) ?>"><input type="hidden" name="party_id" value="<?= $partyId ?>"><input type="hidden" name="currency" value="<?= pl_e($currency) ?>">
<section class="panel !m-0"><div class="grid grid-cols-2 gap-3 mb-3">
<?php pl_starter_field('Payment date','date',pl_web_text($input,'date',gmdate('Y-m-d')),'date'); pl_starter_field('Payment amount ('.$currency.')','amount_fc',pl_web_text($input,'amount_fc')); ?>
<?php pl_starter_select('Cash or bank account','bank_account_id',pl_starter_options(array_filter($accounts,static fn(array $a):bool=>$a['role']==='cash_bank')),pl_web_id($input,'bank_account_id')); pl_starter_field('Payment reference','description',pl_web_text($input,'description')); ?>
<?php if ($currency!==$company['currency']): ?><?php pl_starter_field('Actual exchange rate (optional)','actual_rate',pl_web_text($input,'actual_rate'),'text',false); ?><p class="field-hint">Enter the actual <?= pl_e($company['currency']) ?> per <?= pl_e($currency) ?>, or use the stored rate for the payment date.</p><?php endif; ?>
</div>
<p class="text-xs text-ink-muted mb-3">Enter the amount to allocate to each document; leave the others blank. Up to 30 allocations. Allocations must equal the payment exactly; unallocated money is rejected.</p>
<div class="table-wrap max-h-64 overflow-auto"><table class="table"><thead><tr><th>Document</th><th>Due</th><th class="text-end">Remaining</th><th>Allocate <?= pl_e($currency) ?></th></tr></thead><tbody>
<?php foreach ($items as $index=>$item): ?><tr><th scope="row"><?= pl_e($item['number']) ?></th><td><?= pl_e($item['due_date']??'—') ?></td><td class="amount"><?= pl_e(pl_money($item['remaining_fc'])) ?></td><td><input type="hidden" name="allocations[<?= $index ?>][item_id]" value="<?= (int)$item['id'] ?>"><input class="input min-w-24" inputmode="decimal" name="allocations[<?= $index ?>][amount_fc]" value="<?= pl_e($values[$item['id']]??'') ?>" aria-label="<?= pl_e('Allocate to '.$item['number']) ?>" data-allocation-amount maxlength="21"></td></tr><?php endforeach; ?>
</tbody></table></div><p class="text-xs text-ink-muted mt-3" data-allocation-total aria-live="polite">Update the preview to validate the allocation total.</p>
</section>
<aside class="panel !m-0">
<h2 class="section-title">Payment preview</h2>
<?php if ($preview): ?><div data-settlement-preview>
<?php pl_ui_totals(['Payment ('.$preview['currency'].')'=>pl_money($preview['amount_fc']),'Carrying amount ('.$preview['functional_currency'].')'=>pl_money($preview['allocated_base']),'Bank amount ('.$preview['functional_currency'].')'=>pl_money($preview['settlement_base'])]); ?>
<?php if ($preview['fx_kind']!==null): ?><p class="text-sm my-3">Realised FX <?= pl_e($preview['fx_kind']) ?>: <strong><?= pl_e($preview['functional_currency'].' '.pl_money($preview['fx_amount'])) ?></strong></p><?php pl_starter_select('Realised FX '.$preview['fx_kind'].' account',$preview['fx_kind'].'_account_id',pl_starter_options(array_filter($accounts,static fn(array $a):bool=>$a['type']===($preview['fx_kind']==='gain'?'income':'expense'))),pl_web_id($input,$preview['fx_kind'].'_account_id')); ?>
<?php else: ?><p class="text-xs text-ink-muted my-3">No exchange difference. FX accounts are not required.</p><?php endif; ?>
<p class="text-xs text-ink-muted my-3"><?= count($preview['allocations']) ?> open items · One balanced journal and bank entry. Reversing this payment reverses all its allocations.</p>
<button class="btn btn-primary w-full" name="action" value="confirm_settlement">Confirm <?= $receipt?'receipt':'payment' ?></button>
</div><?php else: ?><p class="text-sm text-ink-muted mt-3">Enter the payment and its allocations, then update the preview. The server checks remaining balances and calculates any exchange difference.</p><?php endif; ?>
</aside></form>
<?php endif; ?></div>
