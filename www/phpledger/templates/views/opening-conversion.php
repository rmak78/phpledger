<?php
 declare(strict_types=1);
 $v=$form['input'];
 $confirmInput=($v['action']??'')==='confirm'?$v:[];
?>
<section class="page-wrap starter-detail">
<?php pl_starter_header('Opening documents','Map unpaid opening documents into the shared receivables and payables ledger.',$form,$company); ?>
<p class="starter-note">Conversion preserves the original trial balance and creates no new journal. Each unpaid document needs an explicit party mapping, with an exact reconciliation to its existing control account.</p>
<?php foreach ($cutovers as $cutover):
    if (!$cutover['documents']) { continue; }
    $previewInput=($v['action']??'')==='preview'&&(int)($v['cutover_id']??0)===(int)$cutover['id']?$v:[];
    $selectedParties=[];
    foreach (is_array($previewInput['mappings']??null)?$previewInput['mappings']:[] as $mapping) {
        if (is_array($mapping)) { $selectedParties[(int)($mapping['opening_document_id']??0)]=$mapping['party_id']??''; }
    }
?><details class="panel"<?= $review && (int) $review['review']['cutover_id'] === (int) $cutover['id'] ? '' : ' open' ?>><summary class="section-title">Cutover <?= pl_e($cutover['cutover_date']) ?> · Party mappings</summary>
<?php if ($company['role']==='owner'): ?><form method="post" action="<?= pl_e(pl_url('/opening-conversion')) ?>"><?php pl_starter_form($company,'preview',['cutover_id'=>$cutover['id'],'request_key'=>$previewInput['request_key']??null]); ?>
<?php pl_ui_table(['Row','Document','Kind','Due','Outstanding','Map to party'], static function () use ($cutover, $company, $parties, $selectedParties): void {
foreach ($cutover['documents'] as $index=>$document): ?><tr><td><?= (int) $index + 1 ?></td><td><?= pl_e($document['reference']) ?><span class="row-secondary"><?= pl_e($document['party']) ?></span></td><td><?= pl_e(ucfirst($document['kind'])) ?></td><td><?= pl_e(pl_date_label($document['due_date'])) ?></td><td class="amount"><?= pl_e($company['currency'].' '.pl_money($document['outstanding'])) ?></td><td>
<?php pl_starter_hidden("mappings[$index][opening_document_id]",$document['id']); pl_starter_select('Party for ' . $document['reference'],"mappings[$index][party_id]",pl_starter_options(array_filter($parties,fn($p)=>(bool)$p[$document['kind']==='receivable'?'is_customer':'is_vendor']),'legal_name'),$selectedParties[(int)$document['id']]??''); ?></td></tr><?php endforeach;
}, 'Unpaid opening documents and party mappings'); ?>
<div class="panel-actions" data-fold="primary action"><button class="btn btn-primary">Preview mapped debts</button></div></form><?php else: ?><p>The company owner must review opening conversion.</p><?php endif; ?></details><?php endforeach; ?>
<?php if ($review): ?><section class="panel"><h2>Confirm reviewed opening debts</h2>
<?php pl_ui_table(['Document','Original party','Mapped party','Amount'], static function () use ($review): void {
foreach ($review['review']['documents'] as $row): ?><tr><td><?= pl_e($row['reference']) ?></td><td><?= pl_e($row['original_party']) ?></td><td><?= pl_e($row['party_name']) ?></td><td class="amount"><?= pl_e(pl_money($row['amount_base'])) ?></td></tr><?php endforeach;
}, 'Reviewed opening mappings'); ?>
<?php pl_ui_table(['Control account','Existing basis','Mapped debts'], static function () use ($review): void {
foreach ($review['review']['accounts'] as $row): ?><tr><td><?= pl_e($row['account_code']) ?></td><td class="amount"><?= pl_e(pl_money($row['basis_amount'])) ?></td><td class="amount"><?= pl_e(pl_money($row['document_total'])) ?></td></tr><?php endforeach;
}, 'Opening document control reconciliation'); ?>
<?php if ($company['role']==='owner'): ?>
<form method="post" action="<?= pl_e(pl_url('/opening-conversion')) ?>"><?php
pl_starter_form($company,'confirm',['expected_hash'=>$confirmInput?($confirmInput['expected_hash']??''):$review['review']['payload_hash'],'request_key'=>$confirmInput['request_key']??null]);
pl_starter_field('Reason','reason',$confirmInput['reason']??'');
?><p><label><input type="checkbox" name="confirmed" value="1" required <?= ($confirmInput['confirmed']??'')==='1'?'checked':'' ?>> I reviewed the party mappings and reconciled amounts.</label></p>
<div class="panel-actions" data-fold="primary action"><button class="btn btn-primary">Confirm conversion</button><?php if ($confirmInput): ?><a class="btn btn-secondary" href="<?= pl_e(pl_url('/opening-conversion')) ?>">Reload current review</a><?php endif; ?></div></form>
<?php else: ?><p>Only the company owner can confirm opening conversion.</p><?php endif; ?></section><?php endif; ?>
<section class="panel"><h2>Converted opening debts</h2><?php if (!$items): ?><p>No opening debts have been converted. New businesses can start with their ordinary invoice and bill flows.</p><?php endif; ?>
<?php foreach ($items as $item): $settleInput=($v['action']??'')==='settle'&&(int)($v['item_id']??0)===(int)$item['item_id']?$v:[]; ?>
<article class="starter-line"><h3><?= pl_e($item['reference'].' | '.$item['legal_name']) ?></h3><p><?= pl_e(ucfirst($item['direction'])) ?> | Remaining <strong><?= pl_e($item['currency'].' '.pl_money($item['remaining_fc'])) ?></strong></p>
<?php if (pl_can_write($company)&&bccomp($item['remaining_fc'],'0',4)>0): ?><details <?= $settleInput?'open':'' ?>><summary>Record payment</summary>
<form method="post" action="<?= pl_e(pl_url('/opening-conversion')) ?>"><?php pl_starter_form($company,'settle',['item_id'=>$item['item_id'],'request_key'=>$settleInput['request_key']??null]); ?><div class="starter-grid"><?php
pl_starter_field('Date','date',$settleInput['date']??gmdate('Y-m-d'),'date');
pl_starter_field('Amount','amount_fc',$settleInput['amount_fc']??$item['remaining_fc']);
pl_starter_select('Cash or bank','bank_account_id',pl_starter_options(array_filter($accounts,fn($a)=>$a['role']==='cash_bank')),$settleInput['bank_account_id']??'');
pl_starter_select('Realised FX gain (optional)','gain_account_id',pl_starter_options(array_filter($accounts,fn($a)=>$a['type']==='income')),$settleInput['gain_account_id']??'',false);
pl_starter_select('Realised FX loss (optional)','loss_account_id',pl_starter_options(array_filter($accounts,fn($a)=>$a['type']==='expense')),$settleInput['loss_account_id']??'',false);
pl_starter_field('Actual rate (optional)','actual_rate',$settleInput['actual_rate']??'','text',false);
pl_starter_field('Description / payment reference','description',$settleInput['description']??'');
?></div><button class="button secondary">Record payment</button></form></details><?php endif; ?></article><?php endforeach; ?></section>
</section>
