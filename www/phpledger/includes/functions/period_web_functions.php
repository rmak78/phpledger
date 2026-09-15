<?php
declare(strict_types=1);

function pl_web_periods(int $actorId, int $companyId, int $bookId, array $user, array $company, string $method): never
{
    if ($method === 'POST') {
        try {
            pl_require_post($method);
            pl_require_csrf(pl_web_text($_POST, 'csrf'));
            pl_web_assert_scope($company, $_POST);
            $action = pl_web_text($_POST, 'action');
            if ($action === 'create') {
                $result = pl_create_period($actorId, $companyId, $bookId, [
                    'start_date' => pl_web_text($_POST, 'start_date'), 'end_date' => pl_web_text($_POST, 'end_date'),
                    'reason' => pl_web_text($_POST, 'reason'), 'request_key' => pl_web_text($_POST, 'request_key'),
                ]);
            } elseif (in_array($action, ['close', 'reopen'], true)) {
                $result = pl_change_period_status($actorId, $companyId, $bookId, pl_web_id($_POST, 'period_id'), $action === 'close' ? 'closed' : 'open', pl_web_id($_POST, 'revision'), pl_web_text($_POST, 'reason'), pl_web_text($_POST, 'request_key'));
            } else {
                throw new DomainException('Choose a valid period action.');
            }
            pl_notice('Period action recorded: ' . ($result['action'] === 'create' ? 'created' : ($result['action'] === 'close' ? 'closed' : 'reopened')) . '. Review the current status below.');
            pl_redirect('/periods');
        } catch (DomainException $error) {
            pl_form_failure('/periods', $_POST, $error->getMessage());
        }
    }
    pl_render('periods', [
        'title' => 'Accounting periods', 'user' => $user, 'company' => $company,
        'periods' => pl_list_periods($actorId, $companyId, $bookId),
        'history' => pl_period_history($actorId, $companyId, $bookId), 'form' => pl_form_state('/periods'),
    ]);
}
