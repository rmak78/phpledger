<?php
declare(strict_types=1);
$journalRows = is_array($input['lines'] ?? null) ? array_values($input['lines']) : [];
$hasFailure = pl_web_text($form, 'message') !== '';
if ($journalRows === []) { $journalRows[] = []; }
$accountIds = array_map(static fn (array $row): int => (int) $row['id'], $company['accounts']);
?>
<section class="page-wrap core-page core-editor" aria-labelledby="journal-editor-title">
<a class="back-link" href="<?= pl_e(pl_url($draft ? '/general-journals/detail' : '/general-journals', $draft ? ['id' => $draft['id'],'return_filters'=>$filters] : $filters)) ?>"><?= pl_icon('arrow-left') ?> Back to <?= $draft ? 'journal' : 'journals' ?></a>
<?php pl_ui_document_header($draft ? $draft['number'] . ' · Edit draft' : 'New general journal', 'draft', static function () use ($company, $draft, $filters): void { ?>
<a class="btn btn-ghost" href="<?= pl_e(pl_url($draft ? '/general-journals/detail' : '/general-journals', $draft ? ['id'=>$draft['id'],'return_filters'=>$filters] : $filters)) ?>">Cancel</a>
<button class="btn btn-secondary" form="journal-editor" type="submit" <?= $company['setup_status'] !== 'ready' ? 'disabled' : '' ?>>Save draft and review</button>
<button class="btn btn-primary" form="journal-editor" name="editor_action" value="post_reviewed_journal" type="submit" <?= $company['setup_status'] !== 'ready' ? 'disabled' : '' ?>>Post journal</button>
<?php }, 'journal-editor-title'); ?>
<?php if ($hasFailure): ?><div class="alert alert-danger" role="alert" tabindex="-1" data-form-error><strong>Your draft was not saved or posted.</strong><p><?= pl_e(pl_web_text($form, 'message')) ?></p><p>Your submitted values are retained.<?php if ($draft): ?> <a href="<?= pl_e(pl_url('/general-journals/edit', ['id' => $draft['id'],'return_filters'=>$filters])) ?>">Reload the latest saved version</a> before resolving a revision conflict.<?php endif; ?></p></div><?php endif; ?>
<form id="journal-editor" method="post" action="<?= pl_e(pl_url('/general-journals/save')) ?>" class="panel core-journal-form" data-core-journal>
<?= pl_csrf_field() ?><?= pl_scope_fields($company) ?><?php pl_ui_return_filters($filters); ?><input type="hidden" name="creation_key" value="<?= pl_e(pl_web_text($input, 'creation_key')) ?>">
<?php if ($draft): ?><input type="hidden" name="id" value="<?= (int) $draft['id'] ?>"><input type="hidden" name="revision" value="<?= pl_web_id($input, 'revision') ?>"><?php endif; ?>
<div class="form-grid core-journal-meta">
<?php pl_ui_field('journal-date', 'Journal date', static function () use ($input, $draft, $hasFailure): void { ?><input class="input" id="journal-date" type="date" name="date" value="<?= pl_e(pl_web_text($input, 'date')) ?>" required <?= !$draft && !$hasFailure ? 'data-local-today' : '' ?>><?php }); ?>
<?php pl_ui_field('journal-reference', 'Reference (optional)', static function () use ($input): void { ?><input class="input" id="journal-reference" name="reference" value="<?= pl_e(pl_web_text($input, 'reference')) ?>" maxlength="120"><?php }); ?>
<?php pl_ui_field('journal-description', 'Journal description', static function () use ($input): void { ?><textarea class="textarea journal-description" id="journal-description" name="description" rows="1" maxlength="500" required placeholder="Explain the purpose of this entry."><?= pl_e(pl_web_text($input, 'description')) ?></textarea><?php }); ?>
</div>
<div class="section-heading"><h2 class="section-title">Journal lines</h2><span class="muted small">Up to 100 lines</span></div>
<p class="muted small" id="journal-line-help">Enter one positive debit or credit per line, using a decimal point and up to four decimal places. Leave unused rows completely blank. An unbalanced draft can be saved for later.</p>
<div class="table-wrap core-lines-wrap"><table class="table doc-lines-table core-line-editor" aria-describedby="journal-line-help"><caption class="sr-only">Journal draft lines in <?= pl_e($company['currency']) ?></caption><thead><tr><th scope="col">Line</th><th scope="col">Account</th><th scope="col" class="amount">Debit</th><th scope="col" class="amount">Credit</th><th scope="col">Description <span class="optional">optional</span></th><th scope="col"><span class="sr-only">Actions</span></th></tr></thead><tbody data-journal-rows>
<?php foreach ($journalRows as $lineIndex => $rawLine): $line = is_array($rawLine) ? $rawLine : []; $lineAccountId = pl_web_id($line, 'account_id'); ?>
<tr data-journal-row><td class="core-line-number"><span data-line-number><?= $lineIndex + 1 ?></span></td>
<td data-line-field="account_id"><label><span class="sr-only" data-line-label="Account">Account, line <?= $lineIndex + 1 ?></span><select aria-label="Account, line <?= $lineIndex + 1 ?>" class="select" name="lines[<?= $lineIndex ?>][account_id]" data-journal-account><option value="">Choose account</option><?php if ($lineAccountId && !in_array($lineAccountId, $accountIds, true)): ?><option value="<?= $lineAccountId ?>" data-unavailable-account selected>Unavailable account <?= $lineAccountId ?> — choose another</option><?php endif; ?><?php foreach ($company['accounts'] as $lineAccount): if (!$lineAccount['is_active'] && (int) $lineAccount['id'] !== $lineAccountId) { continue; } ?><option value="<?= (int) $lineAccount['id'] ?>" <?= !$lineAccount['is_active'] ? 'data-unavailable-account' : '' ?> <?= (int) $lineAccount['id'] === $lineAccountId ? 'selected' : '' ?>><?= pl_e($lineAccount['code'] . ' · ' . $lineAccount['name'] . (!$lineAccount['is_active'] ? ' (inactive — choose another)' : '')) ?></option><?php endforeach; ?></select></label></td>
<td data-line-field="debit"><label><span class="sr-only" data-line-label="Debit">Debit, line <?= $lineIndex + 1 ?></span><input class="input" name="lines[<?= $lineIndex ?>][debit]" value="<?= pl_e(pl_web_text($line, 'debit')) ?>" inputmode="decimal" maxlength="21" pattern="[0-9]+(\.[0-9]{1,4})?" placeholder="0.00" autocomplete="off" data-journal-amount="debit"></label></td>
<td data-line-field="credit"><label><span class="sr-only" data-line-label="Credit">Credit, line <?= $lineIndex + 1 ?></span><input class="input" name="lines[<?= $lineIndex ?>][credit]" value="<?= pl_e(pl_web_text($line, 'credit')) ?>" inputmode="decimal" maxlength="21" pattern="[0-9]+(\.[0-9]{1,4})?" placeholder="0.00" autocomplete="off" data-journal-amount="credit"></label></td>
<td data-line-field="description"><label><span class="sr-only" data-line-label="Description">Description, line <?= $lineIndex + 1 ?></span><input class="input" name="lines[<?= $lineIndex ?>][description]" value="<?= pl_e(pl_web_text($line, 'description')) ?>" maxlength="500"></label></td>
<td class="core-line-action"><button type="submit" name="remove_line" value="<?= $lineIndex ?>" formnovalidate class="btn btn-ghost btn-sm" data-remove-journal-row aria-label="Remove line <?= $lineIndex + 1 ?>">Remove</button></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<div class="core-line-toolbar"><button type="submit" name="editor_action" value="add_line" formnovalidate class="btn btn-secondary" data-add-journal-row><?= pl_icon('plus') ?> Add line</button><p class="muted small" data-journal-message aria-live="polite"></p></div>
<div class="core-draft-totals" data-journal-totals data-fold="document total" hidden aria-live="polite" aria-atomic="true"><span>Debit <strong class="amount" data-journal-total="debit">0.00</strong></span><span>Credit <strong class="amount" data-journal-total="credit">0.00</strong></span><span data-journal-balance>Enter journal lines</span></div>
<noscript><p class="muted small">Add or remove a line without saving. Save your draft to review the server-calculated totals before posting.</p></noscript>
<p class="text-xs text-ink-muted mt-3">Posting adds this transaction to reports. A saved draft does not change your books.</p>
</form>
<p class="muted small core-footnote">An opening-balance import is a separate reviewed workflow and is not available here.</p>
</section>
