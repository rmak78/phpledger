<?php
declare(strict_types=1);

/** Shared compact quantity/price editor. Row changes work through POST without JS. */
function pl_ui_commercial_lines(array $rows, array $options, bool $credit = false, bool $purchase = false): void
{
    $rows = $rows === [] ? [[]] : array_values($rows);
    $fields = ['description'=>'Description','product_id'=>'Product'];
    if (!$purchase) { $fields['account_id']='Account'; }
    if ($credit) { $fields['original_line_number']='Original line'; }
    $fields += ['quantity'=>'Qty','unit_price'=>'Unit price'];
    if (!$purchase) { $fields['tax_code_id']='Tax code'; }
    $widths=$purchase?['description'=>'w-[35%]','product_id'=>'w-[22%]','quantity'=>'w-[10%]','unit_price'=>'w-[14%]']
        :['description'=>$credit?'w-[24%]':'w-[31%]','product_id'=>'w-[14%]','account_id'=>'w-[14%]','original_line_number'=>'w-[7%]','quantity'=>'w-[7%]','unit_price'=>'w-[10%]','tax_code_id'=>'w-[10%]'];
    ?>
    <div class="table-wrap relative" tabindex="0" role="region" aria-label="Document lines">
    <table class="table doc-lines-table"><caption class="sr-only">Document lines</caption><thead><tr>
    <?php foreach ($fields as $name=>$label): ?><th scope="col" class="<?= $widths[$name] ?><?= in_array($name,['quantity','unit_price'],true)?' text-end':'' ?>"><?= pl_e($label) ?></th><?php endforeach; ?>
    <th scope="col" class="text-end <?= $purchase?'w-[15%]':'w-[10%]' ?>">Entered amount</th><th scope="col" class="w-[4%]"><span class="sr-only">Actions</span></th></tr></thead><tbody data-commercial-rows>
    <?php foreach ($rows as $index=>$raw): $line=is_array($raw)?$raw:[]; ?>
    <tr data-commercial-row>
    <?php foreach ($fields as $name=>$label): $id='commercial-'.$index.'-'.$name; ?>
    <td><label class="sr-only" for="<?= $id ?>" data-commercial-label="<?= pl_e($label) ?>"><?= pl_e($label.', line '.($index+1)) ?></label>
    <?php if (isset($options[$name])): ?><select class="select" id="<?= $id ?>" name="lines[<?= $index ?>][<?= $name ?>]" data-commercial-field="<?= $name ?>"><option value="">Choose…</option>
    <?php if (($line[$name]??'')!=='' && $line[$name]!==null && !isset($options[$name][$line[$name]])): ?><option value="<?= pl_e((string)$line[$name]) ?>" selected>Unavailable selection <?= pl_e((string)$line[$name]) ?> — choose another</option><?php endif; ?>
    <?php foreach ($options[$name] as $value=>$text): ?><option value="<?= pl_e((string)$value) ?>"<?= (string)($line[$name]??'')===(string)$value?' selected':'' ?>><?= pl_e($text) ?></option><?php endforeach; ?></select>
    <?php else: ?><input class="input<?= in_array($name,['quantity','unit_price'],true)?' input-amount':'' ?>" id="<?= $id ?>" name="lines[<?= $index ?>][<?= $name ?>]" data-commercial-field="<?= $name ?>" value="<?= pl_e((string)($line[$name]??'')) ?>" maxlength="<?= $name==='description'?500:21 ?>"<?= $name==='description'?'':' inputmode="decimal"' ?>><?php endif; ?></td>
    <?php endforeach; ?>
    <?php $amount='—'; try { $amount=pl_ar_line_amount((string)($line['quantity']??''),(string)($line['unit_price']??'')); } catch (DomainException) {} ?>
    <td class="amount" data-commercial-amount><?= pl_e($amount) ?></td><td><button class="btn btn-ghost btn-icon btn-sm" name="remove_line" value="<?= $index ?>" formnovalidate data-remove-commercial-row aria-label="Remove line <?= $index+1 ?>"><?= pl_icon('trash') ?></button></td>
    </tr><?php endforeach; ?></tbody></table></div>
    <div class="flex flex-wrap items-center justify-between gap-3 mt-3"><button class="btn btn-secondary btn-sm" name="editor_action" value="add_line" formnovalidate data-add-commercial-row><?= pl_icon('plus') ?> Add line</button><p class="text-xs text-ink-muted" data-commercial-total aria-live="polite">Entered amounts use quantity × unit price. Review tax and posting totals before confirming.</p></div>
    <?php
}
