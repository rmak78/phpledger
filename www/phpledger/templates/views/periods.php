<?php
declare(strict_types=1);
$periodInput = $form['input'];
$canManagePeriods = pl_can_write($company) && !pl_demo_enabled();
$canReopenPeriods = ($company['role'] ?? '') === 'owner' && !pl_demo_enabled();
$nextStart = isset($periods[0]) && $periods[0]['end_date'] < '9998-12-31'
    ? (new DateTimeImmutable($periods[0]['end_date']))->modify('+1 day')->format('Y-m-d') : '';
$createInput = pl_web_text($periodInput, 'action') === 'create' ? $periodInput : [];
?>
<section class="page-wrap" aria-labelledby="periods-title">
    <div class="page-heading">
        <div><p class="eyebrow">Books and controls</p><h1 id="periods-title">Accounting periods</h1><p class="muted">Create date ranges, close completed periods and review their history.</p></div>
        <a class="button secondary" href="<?= pl_e(pl_url('/reports')) ?>">View reports</a>
    </div>
    <?php if ($form['message'] !== ''): ?>
        <div class="alert" role="alert" tabindex="-1" data-form-error><h2>Period action needs attention</h2><p><?= pl_e((string) $form['message']) ?></p><p>Your entered values are preserved where the action is still available. Review the current period before trying again.</p></div>
    <?php endif; ?>
    <div class="panel"><h2>Close posting dates after review</h2><p>Closing prevents new entries and reversals dated within the period. Existing entries remain readable. Review drafts, reports and reconciliations before closing. A business owner can reopen a period with a recorded reason.</p><p class="muted">Closing does not create year-end adjustment entries, transfer profit, or certify the accounts.</p></div>
    <?php if ($canManagePeriods): ?>
        <details class="panel"<?= $createInput !== [] ? ' open' : '' ?>><summary><strong>Create an accounting period</strong></summary>
            <form action="<?= pl_e(pl_url('/periods')) ?>" method="post" class="form-grid">
                <?= pl_csrf_field() ?><?= pl_scope_fields($company) ?>
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="request_key" value="<?= pl_e(pl_web_text($createInput, 'request_key', bin2hex(random_bytes(16)))) ?>">
                <div class="field"><label for="period-start">Start date</label><input id="period-start" type="date" name="start_date" value="<?= pl_e(pl_web_text($createInput, 'start_date', $nextStart)) ?>" required></div>
                <div class="field"><label for="period-end">End date</label><input id="period-end" type="date" name="end_date" value="<?= pl_e(pl_web_text($createInput, 'end_date')) ?>" required></div>
                <div class="field full-width"><label for="period-create-reason">Reason</label><input id="period-create-reason" name="reason" maxlength="500" value="<?= pl_e(pl_web_text($createInput, 'reason')) ?>" required><p class="muted">Dates include both endpoints and must not overlap another period.</p></div>
                <div class="actions full-width"><button class="button primary" type="submit">Create open period</button></div>
            </form>
        </details>
    <?php elseif (pl_demo_enabled()): ?>
        <div class="panel"><p>Period administration is disabled in the public sample.</p></div>
    <?php else: ?>
        <div class="panel"><p>Your role can read periods and their history. An owner or accountant can create and close periods.</p></div>
    <?php endif; ?>
    <div class="panel"><h2>Periods</h2>
        <div class="table-wrap" tabindex="0" role="region" aria-label="Accounting periods"><table class="data-table"><thead><tr><th scope="col">Start date</th><th scope="col">End date</th><th scope="col">Status</th><th scope="col">Action</th></tr></thead><tbody>
            <?php foreach ($periods as $period): ?>
                <?php
                $periodId = (int) $period['id'];
                $action = $period['status'] === 'open' ? 'close' : 'reopen';
                $rowInput = pl_web_id($periodInput, 'period_id') === $periodId && pl_web_text($periodInput, 'action') === $action ? $periodInput : [];
                ?>
                <tr><td><?= pl_e(pl_date_label($period['start_date'])) ?></td><td><?= pl_e(pl_date_label($period['end_date'])) ?></td><td><span class="badge"><?= $period['status'] === 'open' ? 'Open' : 'Closed' ?></span></td><td>
                    <?php if ($canManagePeriods && ($action === 'close' || $canReopenPeriods)): ?>
                        <details<?= $rowInput !== [] ? ' open' : '' ?>><summary><?= $action === 'close' ? 'Close period' : 'Reopen period' ?></summary>
                            <form action="<?= pl_e(pl_url('/periods')) ?>" method="post" class="form-grid">
                                <?= pl_csrf_field() ?><?= pl_scope_fields($company) ?>
                                <input type="hidden" name="action" value="<?= pl_e($action) ?>"><input type="hidden" name="period_id" value="<?= $periodId ?>"><input type="hidden" name="revision" value="<?= (int) $period['revision'] ?>">
                                <input type="hidden" name="request_key" value="<?= pl_e(bin2hex(random_bytes(16))) ?>">
                                <div class="field full-width"><label for="period-reason-<?= $periodId ?>">Reason for <?= $action === 'close' ? 'closing' : 'reopening' ?></label><input id="period-reason-<?= $periodId ?>" name="reason" maxlength="500" value="<?= pl_e(pl_web_text($rowInput, 'reason')) ?>" required></div>
                                <div class="actions full-width"><button class="button secondary" type="submit"><?= $action === 'close' ? 'Confirm close' : 'Confirm reopen' ?></button></div>
                            </form>
                        </details>
                    <?php else: ?><span class="muted"><?= $period['status'] === 'closed' ? 'Owner can reopen' : 'Read only' ?></span><?php endif; ?>
                </td></tr>
            <?php endforeach; ?>
        </tbody></table></div>
    </div>
    <div class="panel"><h2>Recent administration history</h2><p class="muted">Latest 100 actions. Periods created during initial business setup have no separate administration action.</p>
        <?php if ($history === []): ?><p>No period administration actions have been recorded.</p>
        <?php else: ?><div class="table-wrap" tabindex="0" role="region" aria-label="Period administration history"><table class="data-table"><thead><tr><th scope="col">Period</th><th scope="col">Action</th><th scope="col">Reason</th><th scope="col">Recorded by</th><th scope="col">Time</th></tr></thead><tbody>
            <?php foreach ($history as $event): ?><tr><td><?= pl_e(pl_date_label($event['start_date'])) ?> – <?= pl_e(pl_date_label($event['end_date'])) ?></td><td><?= pl_e(['create' => 'Created open', 'close' => 'Closed', 'reopen' => 'Reopened'][$event['action']]) ?></td><td><?= pl_e($event['reason']) ?></td><td><?= pl_e($event['actor_name']) ?></td><td><time datetime="<?= pl_e(str_replace(' ', 'T', $event['created_at']) . 'Z') ?>" data-local-time><?= pl_e($event['created_at']) ?> UTC</time></td></tr><?php endforeach; ?>
        </tbody></table></div><?php endif; ?>
    </div>
</section>
