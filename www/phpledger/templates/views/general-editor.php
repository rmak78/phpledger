<?php
declare(strict_types=1);
$journalRows = is_array($input['lines'] ?? null) ? array_values($input['lines']) : [];
$hasFailure = pl_web_text($form, 'message') !== '';
while (count($journalRows) < min(100, max(10, count($draft['lines'] ?? []) + 2))) { $journalRows[] = []; }
$accountIds = array_map(static fn (array $row): int => (int) $row['id'], $company['accounts']);
?>
<section class="page-wrap core-page core-editor" aria-labelledby="journal-editor-title">
<a class="back-link" href="<?= pl_e(pl_url($draft ? '/general-journals/detail' : '/general-journals', $draft ? ['id' => $draft['id']] : [])) ?>"><?= pl_icon('arrow-left') ?> Back to <?= $draft ? 'journal' : 'journals' ?></a>
<div class="page-heading"><div><p class="eyebrow"><?= $draft ? pl_e($draft['number']) . ' · Editing draft' : 'Accounting core' ?></p><h1 id="journal-editor-title"><?= $draft ? 'Edit journal draft' : 'New general journal' ?></h1><p class="muted">Enter debits and credits in <?= pl_e($company['currency']) ?>. Saving a draft does not change your balances.</p></div><span class="badge draft">Draft</span></div>
<?php if ($hasFailure): ?><div class="alert" role="alert"><strong>Your draft was not saved.</strong><p><?= pl_e(pl_web_text($form, 'message')) ?></p><p>Your submitted values are retained.<?php if ($draft): ?> <a href="<?= pl_e(pl_url('/general-journals/edit', ['id' => $draft['id']])) ?>">Reload the latest saved version</a> before resolving a revision conflict.<?php endif; ?></p></div><?php endif; ?>
<form method="post" action="<?= pl_e(pl_url('/general-journals/save')) ?>" class="panel core-journal-form" data-core-journal>
<?= pl_csrf_field() ?><?= pl_scope_fields($company) ?><input type="hidden" name="creation_key" value="<?= pl_e(pl_web_text($input, 'creation_key')) ?>">
<?php if ($draft): ?><input type="hidden" name="id" value="<?= (int) $draft['id'] ?>"><input type="hidden" name="revision" value="<?= pl_web_id($input, 'revision') ?>"><?php endif; ?>
<div class="form-grid core-journal-meta">
<label class="field">Journal date<input type="date" name="date" value="<?= pl_e(pl_web_text($input, 'date')) ?>" required <?= !$draft && !$hasFailure ? 'data-local-today' : '' ?>></label>
<label class="field">Reference <span class="optional">optional</span><input name="reference" value="<?= pl_e(pl_web_text($input, 'reference')) ?>" maxlength="120"></label>
<label class="field full-width">Journal description<textarea name="description" rows="2" maxlength="500" required placeholder="Explain the purpose of this entry."><?= pl_e(pl_web_text($input, 'description')) ?></textarea></label>
</div>
<div class="section-heading"><h2>Journal lines</h2><span class="muted small">Up to 100 lines</span></div>
<p class="muted small" id="journal-line-help">Enter one positive debit or credit per line, using a decimal point and up to four decimal places. Leave unused rows completely blank. An unbalanced draft can be saved for later.</p>
<div class="table-wrap core-lines-wrap"><table class="data-table core-line-editor" aria-describedby="journal-line-help"><caption class="sr-only">Journal draft lines in <?= pl_e($company['currency']) ?></caption><thead><tr><th scope="col">Line</th><th scope="col">Account</th><th scope="col" class="amount">Debit</th><th scope="col" class="amount">Credit</th><th scope="col">Description <span class="optional">optional</span></th><th scope="col"><span class="sr-only">Actions</span></th></tr></thead><tbody data-journal-rows>
<?php foreach ($journalRows as $lineIndex => $rawLine): $line = is_array($rawLine) ? $rawLine : []; $lineAccountId = pl_web_id($line, 'account_id'); ?>
<tr data-journal-row><td class="core-line-number"><span data-line-number><?= $lineIndex + 1 ?></span></td>
<td data-line-field="account_id"><label><span class="sr-only" data-line-label="Account">Account, line <?= $lineIndex + 1 ?></span><select name="lines[<?= $lineIndex ?>][account_id]" data-journal-account><option value="">Choose account</option><?php if ($lineAccountId && !in_array($lineAccountId, $accountIds, true)): ?><option value="<?= $lineAccountId ?>" data-unavailable-account selected>Unavailable account <?= $lineAccountId ?> — choose another</option><?php endif; ?><?php foreach ($company['accounts'] as $lineAccount): if (!$lineAccount['is_active'] && (int) $lineAccount['id'] !== $lineAccountId) { continue; } ?><option value="<?= (int) $lineAccount['id'] ?>" <?= !$lineAccount['is_active'] ? 'data-unavailable-account' : '' ?> <?= (int) $lineAccount['id'] === $lineAccountId ? 'selected' : '' ?>><?= pl_e($lineAccount['code'] . ' · ' . $lineAccount['name'] . (!$lineAccount['is_active'] ? ' (inactive — choose another)' : '')) ?></option><?php endforeach; ?></select></label></td>
<td data-line-field="debit"><label><span class="sr-only" data-line-label="Debit">Debit, line <?= $lineIndex + 1 ?></span><input name="lines[<?= $lineIndex ?>][debit]" value="<?= pl_e(pl_web_text($line, 'debit')) ?>" inputmode="decimal" maxlength="21" pattern="[0-9]+(\.[0-9]{1,4})?" placeholder="0.00" autocomplete="off" data-journal-amount="debit"></label></td>
<td data-line-field="credit"><label><span class="sr-only" data-line-label="Credit">Credit, line <?= $lineIndex + 1 ?></span><input name="lines[<?= $lineIndex ?>][credit]" value="<?= pl_e(pl_web_text($line, 'credit')) ?>" inputmode="decimal" maxlength="21" pattern="[0-9]+(\.[0-9]{1,4})?" placeholder="0.00" autocomplete="off" data-journal-amount="credit"></label></td>
<td data-line-field="description"><label><span class="sr-only" data-line-label="Description">Description, line <?= $lineIndex + 1 ?></span><input name="lines[<?= $lineIndex ?>][description]" value="<?= pl_e(pl_web_text($line, 'description')) ?>" maxlength="500"></label></td>
<td class="core-line-action"><button type="button" class="text-button" data-remove-journal-row hidden aria-label="Remove line <?= $lineIndex + 1 ?>">Remove</button></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<div class="core-line-toolbar"><button type="button" class="button secondary" data-add-journal-row hidden><?= pl_icon('plus') ?> Add line</button><p class="muted small" data-journal-message aria-live="polite"></p></div>
<div class="core-draft-totals" data-journal-totals hidden aria-live="polite" aria-atomic="true"><span>Debit <strong class="amount" data-journal-total="debit">0.00</strong></span><span>Credit <strong class="amount" data-journal-total="credit">0.00</strong></span><span data-journal-balance>Enter journal lines</span></div>
<noscript><p class="muted small">Ten rows are provided for a new entry. Clear all fields in a row to omit it. Save your draft to review its calculated totals.</p></noscript>
<div class="form-actions"><a class="button secondary" href="<?= pl_e(pl_url($draft ? '/general-journals/detail' : '/general-journals', $draft ? ['id' => $draft['id']] : [])) ?>">Cancel</a><button class="button primary" type="submit" <?= $company['setup_status'] !== 'ready' ? 'disabled' : '' ?>>Save draft and review <?= pl_icon('arrow-right') ?></button></div>
</form>
<p class="muted small core-footnote">An opening-balance import is a separate reviewed workflow and is not available here.</p>
</section>

