<?php
declare(strict_types=1);
ob_start();
if (PHP_SAPI !== 'cli' || getenv('PL_ENV') !== 'demo' || getenv('PL_DB_HOST') !== 'db_test' || getenv('PL_DB_NAME') !== 'phpledger_demo' || getenv('PL_DB_USER') !== 'ledger_demo_test') {
    throw new RuntimeException('This smoke check requires only the restricted isolated demo account in db_test.');
}
require dirname(__DIR__) . '/www/phpledger/includes/bootstrap.php';
function demo_check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}
function demo_denied(callable $action): void
{
    try {
        $action();
    } catch (DomainException | MeekroDBException) {
        return;
    }
    throw new RuntimeException('An expected demo denial did not occur.');
}
pl_session_start(false);
$pipes = [];
$probe = proc_open([PHP_BINARY, __DIR__ . '/demo_lock_probe.php'], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
if (!is_resource($probe)) {
    throw new RuntimeException('Could not start the independent demo lock probe.');
}
fclose($pipes[0]);
$probeOutput = stream_get_contents($pipes[1]);
$probeError = stream_get_contents($pipes[2]);
fclose($pipes[1]);
fclose($pipes[2]);
demo_check(proc_close($probe) === 0 && trim($probeOutput) === 'busy' && $probeError === '', 'Concurrent demo admission bypassed the maintenance lock.');
$_SERVER['REQUEST_METHOD'] = 'POST';
demo_denied(static fn () => pl_demo_begin_visit(str_repeat('0', 64)));
$first = pl_demo_begin_visit(pl_csrf_token());
$f = $first['user']['id'];
$company = pl_company_context($f, $first['company_id']);
demo_check($company['is_sample'] && pl_current_user_id() === $f, 'Visitor did not get its own active sample context.');
demo_check(pl_demo_begin_visit(pl_csrf_token())['company_id'] === $first['company_id'], 'Repeated start duplicated a visitor.');
demo_denied(static fn () => pl_demo_begin_visit(pl_csrf_token(), 'CAD'));
demo_check(pl_demo_begin_visit(pl_csrf_token(), 'PKR')['company_id'] === $first['company_id'] && pl_company_context($f, $first['company_id'])['currency'] === 'USD', 'An existing sample currency was silently changed.');
$draft = pl_list_documents($f, $first['company_id'], $first['book_id'], ['status' => 'draft'])['documents'][0];
$posted = pl_post_document($f, $first['company_id'], $first['book_id'], $draft['id'], $draft['revision']);
demo_check($posted['status'] === 'posted', 'Demo posting did not work.');
demo_check(pl_reverse_document($f, $first['company_id'], $first['book_id'], $draft['id'], $draft['date'], 'Synthetic demo correction')['status'] === 'reversed', 'Traceable demo correction did not work.');
demo_denied(static fn () => pl_create_user('blocked@example.invalid', 'Blocked', 'Synthetic blocked password'));
demo_denied(static fn () => pl_create_company($f, 'Blocked extra company', 'USD', gmdate('Y-m-d')));
demo_denied(static fn () => pl_setup_company($f, [], 'blocked-setup'));
demo_denied(static fn () => pl_confirm_existing_setup($f, $first['company_id'], $first['book_id'], [], true));
demo_denied(static fn () => DB::delete('pl_documents', 'id = %i', $draft['id']));
demo_denied(static fn () => DB::update('pl_periods', ['status' => 'closed'], 'book_id = %i', $first['book_id']));
demo_denied(static fn () => DB::update('pl_documents', ['amount' => '999'], 'id = %i', $draft['id']));
$oldGeneration = $_SESSION['demo_generation'];
$_SESSION['demo_generation'] = str_repeat('0', 64);
demo_check(pl_current_user_id() === null, 'A stale generation retained a reused numeric identity.');
$second = pl_demo_begin_visit(pl_csrf_token(), 'PKR');
demo_check(pl_company_context($second['user']['id'], $second['company_id'])['currency'] === 'PKR', 'Explicit sample currency choice was ignored.');
demo_check($first['company_id'] !== $second['company_id'] && $f !== $second['user']['id'], 'Visitors shared a company or account.');
demo_denied(static fn () => pl_get_document($second['user']['id'], $first['company_id'], $first['book_id'], $draft['id']));
demo_check(pl_list_documents($second['user']['id'], $second['company_id'], $second['book_id'])['total'] === 6, 'The other visitor inherited edited records.');
putenv('PL_DEMO_MAX_DOCUMENTS=10');
$accounts = array_column($company['accounts'], 'id', 'semantic_key');
$input = ['kind' => 'expense', 'date' => gmdate('Y-m-d'), 'amount' => '1', 'money_account_id' => $accounts['core.cash_bank'], 'category_account_id' => $accounts['core.expense.general'], 'counterparty' => 'Synthetic capacity test', 'memo' => '', 'reference' => ''];
for ($i = 0; $i < 4; $i++) {
    pl_save_document($f, $first['company_id'], $first['book_id'], $input + ['creation_key' => 'capacity:' . $i]);
}
demo_denied(static fn () => pl_save_document($f, $first['company_id'], $first['book_id'], $input + ['creation_key' => 'capacity:overflow']));
putenv('PL_DEMO_MAX_VISITORS=2');
pl_logout_session();
demo_denied(static fn () => pl_demo_begin_visit(pl_csrf_token()));
demo_check((int) DB::queryFirstField('SELECT COUNT(*) FROM pl_demo_visitors') === 2, 'Visitor capacity was bypassed.');
demo_check(is_string($oldGeneration) && strlen($oldGeneration) === 64, 'A generation marker was not recorded.');
echo "Demo smoke passed: concurrent maintenance-lock denial, CSRF start, private visitor sessions, idempotent start, scoped posting/reversal, cross-visitor denial, setup/admin/delete/period/posted-edit denial, stale generation expiry, document and visitor limits.\n";
