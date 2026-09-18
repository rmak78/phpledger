<?php
declare(strict_types=1);
$periodInput = $form['input'];
$canManagePeriods = pl_can_write($company) && !pl_demo_enabled();
$canReopenPeriods = ($company['role'] ?? '') === 'owner' && !pl_demo_enabled();
$nextStart = isset($periods[0]) && $periods[0]['end_date'] < '9998-12-31'
    ? (new DateTimeImmutable($periods[0]['end_date']))->modify('+1 day')->format('Y-m-d') : '';
$createInput = pl_web_text($periodInput, 'action') === 'create' ? $periodInput : [];
?>
<section class="flex flex-col gap-4 py-5" aria-labelledby="periods-title">
    <div class="page-header">
        <div><p class="eyebrow">Books and controls</p><h1 class="page-title" id="periods-title">Accounting periods</h1><p class="muted">Create date ranges, close completed periods and review their history.</p></div>
        <a class="btn btn-secondary" href="<?= pl_e(pl_url('/reports')) ?>">View reports</a>
    </div>
    <?php if ($form['message'] !== ''): ?>
        <div class="alert alert-danger" role="alert" tabindex="-1" data-form-error><h2 class="section-title mb-2">Period action needs attention</h2><p><?= pl_e((string) $form['message']) ?></p><p>Your entered values are preserved where the action is still available. Review the current period before trying again.</p></div>
    <?php endif; ?>
    <div class="rounded-panel border border-border bg-surface p-4"><h2 class="section-title mb-2">Close posting dates after review</h2><p>Closing prevents new entries and reversals dated within the period. Existing entries remain readable. Review drafts, reports and reconciliations before closing. A business owner can reopen a period with a recorded reason.</p><p class="muted">Closing does not create year-end adjustment entries, transfer profit, or certify the accounts.</p></div>
    <?php if ($canManagePeriods): ?>
        <details class="rounded-panel border border-border bg-surface p-4"<?= $createInput !== [] ? ' open' : '' ?>><summary><strong>Create an accounting period</strong></summary>
            <form action="<?= pl_e(pl_url('/periods')) ?>" method="post" class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                <?= pl_csrf_field() ?><?= pl_scope_fields($company) ?>
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="request_key" value="<?= pl_e(pl_web_text($createInput, 'request_key', bin2hex(random_bytes(16)))) ?>">
                <div class="field"><label for="period-start">Start date</label><input class="input" id="period-start" type="date" name="start_date" value="<?= pl_e(pl_web_text($createInput, 'start_date', $nextStart)) ?>" required></div>
                <div class="field"><label for="period-end">End date</label><input class="input" id="period-end" type="date" name="end_date" value="<?= pl_e(pl_web_text($createInput, 'end_date')) ?>" required></div>
                <div class="field sm:col-span-2"><label for="period-create-reason">Reason</label><input class="input" id="period-create-reason" name="reason" maxlength="500" value="<?= pl_e(pl_web_text($createInput, 'reason')) ?>" required><p class="muted">Dates include both endpoints and must not overlap another period.</p></div>
                <div class="flex gap-2 sm:col-span-2"><button class="btn btn-primary" type="submit">Create open period</button></div>
            </form>
        </details>
    <?php elseif (pl_demo_enabled()): ?>
        <div class="rounded-panel border border-border bg-surface p-4"><p>Period administration is disabled in the public sample.</p></div>
    <?php else: ?>
        <div class="rounded-panel border border-border bg-surface p-4"><p>Your role can read periods and their history. An owner or accountant can create and close periods.</p></div>
    <?php endif; ?>
    <div class="rounded-panel border border-border bg-surface p-4"><h2 class="section-title mb-2">Periods</h2>
        <div class="table-wrap" tabindex="0" role="region" aria-label="Accounting periods"><table class="table"><thead><tr><th scope="col">Start date</th><th scope="col">End date</th><th scope="col">Status</th><th scope="col">Action</th></tr></thead><tbody>
            <?php foreach ($periods as $period): ?>
                <?php
                $periodId = (int) $period['id'];
                $action = $period['status'] === 'open' ? 'close' : 'reopen';
                $rowInput = pl_web_id($periodInput, 'period_id') === $periodId && pl_web_text($periodInput, 'action') === $action ? $periodInput : [];
                ?>
                <tr><td><?= pl_e(pl_date_label($period['start_date'])) ?></td><td><?= pl_e(pl_date_label($period['end_date'])) ?></td><td><span class="badge <?= $period['status']==='open'?'badge-info':'badge-posted' ?>"><?= $period['status'] === 'open' ? 'Open' : 'Closed' ?></span></td><td>
                    <?php if ($canManagePeriods && ($action === 'close' || $canReopenPeriods)): ?>
                        <details data-confirmation<?= $rowInput !== [] ? ' open' : '' ?>><summary class="btn btn-secondary btn-sm"><?= $action === 'close' ? 'Close period' : 'Reopen period' ?></summary>
                            <div data-confirmation-body><h2 class="section-title"><?= $action === 'close' ? 'Close period' : 'Reopen period' ?> · <?= pl_e(pl_date_label($period['start_date']).' – '.pl_date_label($period['end_date'])) ?></h2><p class="field-hint"><?= $action === 'close' ? 'Prevents new entries and reversals dated in this period. Existing entries remain readable.' : 'Allows new entries and reversals dated in this period again. Only the owner can reopen it.' ?></p>
                            <?php if ($rowInput && $form['message']!==''): ?><p class="alert alert-danger" role="alert"><?= pl_e($form['message']) ?></p><?php endif; ?>
                            <form action="<?= pl_e(pl_url('/periods')) ?>" method="post" class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                                <?= pl_csrf_field() ?><?= pl_scope_fields($company) ?>
                                <input type="hidden" name="action" value="<?= pl_e($action) ?>"><input type="hidden" name="period_id" value="<?= $periodId ?>"><input type="hidden" name="revision" value="<?= (int) $period['revision'] ?>">
                                <input type="hidden" name="request_key" value="<?= pl_e(bin2hex(random_bytes(16))) ?>">
                                <div class="field sm:col-span-2"><label for="period-reason-<?= $periodId ?>">Reason for <?= $action === 'close' ? 'closing' : 'reopening' ?></label><input class="input" id="period-reason-<?= $periodId ?>" name="reason" maxlength="500" value="<?= pl_e(pl_web_text($rowInput, 'reason')) ?>" required></div>
                                <div class="flex gap-2 sm:col-span-2"><button class="btn btn-secondary" type="submit"><?= $action === 'close' ? 'Confirm close' : 'Confirm reopen' ?></button></div>
                            </form><button type="button" class="btn btn-ghost mt-3" data-confirmation-cancel hidden>Cancel</button></div>
                        </details>
                    <?php else: ?><span class="muted"><?= $period['status'] === 'closed' ? 'Owner can reopen' : 'Read only' ?></span><?php endif; ?>
                </td></tr>
            <?php endforeach; ?>
        </tbody></table></div>
    </div>
    <div class="rounded-panel border border-border bg-surface p-4"><h2 class="section-title mb-2">Recent administration history</h2><p class="muted">Latest 100 actions. Periods created during initial business setup have no separate administration action.</p>
        <?php if ($history === []): ?><p>No period administration actions have been recorded.</p>
        <?php else: ?><ol class="flex flex-col gap-3 border-s border-border ps-3" aria-label="Period administration history">
            <?php foreach ($history as $event): ?><li><div class="flex flex-wrap items-center gap-2"><?php pl_ui_badge($event['action']==='close'?'posted':'info',['create'=>'Created open','close'=>'Closed','reopen'=>'Reopened'][$event['action']]); ?><p class="text-sm font-medium"><?= pl_e(pl_date_label($event['start_date']).' – '.pl_date_label($event['end_date'])) ?></p></div><p class="text-xs text-ink-muted mt-1"><?= pl_e($event['reason']) ?></p><p class="text-xs text-ink-muted"><time datetime="<?= pl_e(str_replace(' ', 'T', $event['created_at']) . 'Z') ?>" data-local-time><?= pl_e($event['created_at']) ?> UTC</time> · <?= pl_e($event['actor_name']) ?></p></li><?php endforeach; ?>
        </ol><?php endif; ?>
    </div>
</section>
