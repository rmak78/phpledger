<?php
 declare(strict_types=1);
 $v=$form['input'];
 $confirmInput=($v['action']??'')==='confirm'?$v:[];
?>
<section class="page-wrap starter-detail">
<?php pl_starter_header('Opening debts','Map unpaid opening documents into the shared receivables and payables ledger.',$form,$company); ?>
<p class="starter-note">Conversion preserves the original trial balance and creates no new journal. Each unpaid document needs an explicit party mapping, with an exact reconciliation to its existing control account.</p>
<?php foreach ($cutovers as $cutover):
    if (!$cutover['documents']) { continue; }
    $previewInput=($v['action']??'')==='preview'&&(int)($v['cutover_id']??0)===(int)$cutover['id']?$v:[];
    $selectedParties=[];
    foreach (is_array($previewInput['mappings']??null)?$previewInput['mappings']:[] as $mapping) {
        if (is_array($mapping)) { $selectedParties[(int)($mapping['opening_document_id']??0)]=$mapping['party_id']??''; }
    }
?><section class="panel"><h2>Cutover <?= pl_e($cutover['cutover_date']) ?></h2>
<?php if ($company['role']==='owner'): ?><form method="post" action="<?= pl_e(pl_url('/opening-conversion')) ?>"><?php pl_starter_form($company,'preview',['cutover_id'=>$cutover['id'],'request_key'=>$previewInput['request_key']??null]); ?>
<?php foreach ($cutover['documents'] as $index=>$document): ?><fieldset class="starter-line"><legend><?= pl_e($document['reference'].' | '.$document['kind']) ?></legend><p><?= pl_e($document['party']) ?> | Due <?= pl_e($document['due_date']) ?> | <?= pl_e($company['currency'].' '.pl_money($document['outstanding'])) ?></p>
<?php pl_starter_hidden("mappings[$index][opening_document_id]",$document['id']); pl_starter_select('Map to existing party',"mappings[$index][party_id]",pl_starter_options(array_filter($parties,fn($p)=>(bool)$p[$document['kind']==='receivable'?'is_customer':'is_vendor']),'legal_name'),$selectedParties[(int)$document['id']]??''); ?></fieldset><?php endforeach; ?>
<button class="button secondary">Preview mapped debts</button></form><?php else: ?><p>The company owner must review opening conversion.</p><?php endif; ?></section><?php endforeach; ?>
<?php if ($review): ?><section class="panel"><h2>Confirm reviewed opening debts</h2>
<?php foreach ($review['review']['documents'] as $row): ?><p><?= pl_e($row['reference'].' | '.$row['original_party'].' to '.$row['party_name']) ?> | <?= pl_e($row['amount_base']) ?></p><?php endforeach; ?>
<?php foreach ($review['review']['accounts'] as $row): ?><p>Control <?= pl_e($row['account_code']) ?> | Existing basis <?= pl_e($row['basis_amount']) ?> | Mapped debts <?= pl_e($row['document_total']) ?></p><?php endforeach; ?>
<?php if ($company['role']==='owner'): ?>
<form method="post" action="<?= pl_e(pl_url('/opening-conversion')) ?>"><?php
pl_starter_form($company,'confirm',['expected_hash'=>$confirmInput?($confirmInput['expected_hash']??''):$review['review']['payload_hash'],'request_key'=>$confirmInput['request_key']??null]);
pl_starter_field('Reason','reason',$confirmInput['reason']??'');
?><p><label><input type="checkbox" name="confirmed" value="1" required <?= ($confirmInput['confirmed']??'')==='1'?'checked':'' ?>> I reviewed the party mappings and reconciled amounts.</label></p>
<button class="button primary">Confirm conversion</button><?php if ($confirmInput): ?><a class="button secondary" href="<?= pl_e(pl_url('/opening-conversion')) ?>">Reload current review</a><?php endif; ?></form>
<?php else: ?><p>Only the company owner can confirm opening conversion.</p><?php endif; ?></section><?php endif; ?>
<section class="panel"><h2>Converted opening debts</h2><?php if (!$items): ?><p>No opening debts have been converted. New businesses can start with their ordinary invoice and bill flows.</p><?php endif; ?>
<?php foreach ($items as $item): $settleInput=($v['action']??'')==='settle'&&(int)($v['item_id']??0)===(int)$item['item_id']?$v:[]; ?>
<article class="starter-line"><h3><?= pl_e($item['reference'].' | '.$item['legal_name']) ?></h3><p><?= pl_e(ucfirst($item['direction'])) ?> | Remaining <strong><?= pl_e($item['currency'].' '.pl_money($item['remaining_fc'])) ?></strong></p>
<?php if (pl_can_write($company)&&bccomp($item['remaining_fc'],'0',4)>0): ?><details <?= $settleInput?'open':'' ?>><summary>Record payment</summary>
<form method="post" action="<?= pl_e(pl_url('/opening-conversion')) ?>"><?php pl_starter_form($company,'settle',['item_id'=>$item['item_id'],'request_key'=>$settleInput['request_key']??null]); ?><div class="starter-grid"><?php
pl_starter_field('Date','date',$settleInput['date']??gmdate('Y-m-d'),'date');
pl_starter_field('Amount','amount_fc',$settleInput['amount_fc']??$item['remaining_fc']);
pl_starter_select('Cash or bank','bank_account_id',pl_starter_options(array_filter($accounts,fn($a)=>$a['role']==='cash_bank')),$settleInput['bank_account_id']??'');
pl_starter_select('Realised FX gain','gain_account_id',pl_starter_options(array_filter($accounts,fn($a)=>$a['type']==='income')),$settleInput['gain_account_id']??'');
pl_starter_select('Realised FX loss','loss_account_id',pl_starter_options(array_filter($accounts,fn($a)=>$a['type']==='expense')),$settleInput['loss_account_id']??'');
pl_starter_field('Actual rate (optional)','actual_rate',$settleInput['actual_rate']??'','text',false);
pl_starter_field('Description / payment reference','description',$settleInput['description']??'');
?></div><button class="button secondary">Record payment</button></form></details><?php endif; ?></article><?php endforeach; ?></section>
</section>
