<?php declare(strict_types=1);
require_once __DIR__.'/commercial-lines.php';
$enteredTotal='0.0000';
try { foreach ($v['lines']??[] as $line) { $enteredTotal=bcadd($enteredTotal,pl_ar_line_amount((string)($line['quantity']??''),(string)($line['unit_price']??'')),4); } } catch (DomainException) { $enteredTotal='—'; }
?>
<div class="py-5"><section class="rounded-panel border border-border bg-surface">
<?php pl_ui_document_header($order?$order['number']:'New purchase order','draft',static function (): void { ?>
<a class="btn btn-ghost" href="<?= pl_e(pl_url('/purchasing')) ?>">Cancel</a>
<button class="btn btn-secondary" form="purchase-order-editor">Save draft for review</button>
<button class="btn btn-primary" form="purchase-order-editor" name="editor_action" value="confirm_order">Confirm order</button>
<?php }); ?>
<form id="purchase-order-editor" method="post" action="<?= pl_e(pl_url('/purchasing')) ?>" class="doc-body" data-commercial-form>
<?php pl_starter_form($company,'save',['id'=>$order['id']??0,'revision'=>$saveInput?($saveInput['revision']??''):($order['revision']??0),'request_key'=>$saveInput['request_key']??null]); ?>
<?php if ($form['message']!==''): ?><div class="alert alert-danger" role="alert" tabindex="-1" data-form-error><?= pl_e($form['message']) ?><p>Your entered values are retained.<?php if ($order): ?> <a href="<?= pl_e(pl_url('/purchasing',['id'=>$order['id']])) ?>">Reload the current order</a> to resolve a revision conflict.<?php endif; ?></p></div><?php endif; ?>
<p class="text-xs text-ink-muted">An order is a commitment. Accounting starts when goods are received or a supplier bill is posted.</p>
<section class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
<?php pl_starter_select('Supplier','party_id',$supplierOptions,$v['party_id']??''); pl_starter_field('Order date','date',$v['date']??$v['document_date']??gmdate('Y-m-d'),'date'); pl_starter_field('Currency','currency',$v['currency']??$company['currency']); pl_starter_field('Reference','reference',$v['reference']??'','text',false); pl_starter_field('Notes','notes',$v['notes']??'','text',false); ?>
</section>
<section><h2 class="section-title mb-2">Order lines</h2><?php pl_ui_commercial_lines($v['lines']??[],['product_id'=>$productOptions],false,true); ?>
<p class="text-xs text-ink-muted mt-3">Purchase order prices are tax-exclusive; tax is applied when the supplier bill is matched. Amounts allow four decimal places.</p>
<div class="flex justify-end mt-3" data-fold="document total"><?php pl_ui_totals(['Total order value ('.$company['currency'].')'=>$order?pl_money($order['total']):'â€”']); ?></div>
</section></form></section></div>
