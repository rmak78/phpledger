<?php
declare(strict_types=1);
if (getenv('PL_ENV') !== 'test' || getenv('PL_DB_NAME') !== 'phpledger_test') {
    exit(2);
}
require_once dirname(__DIR__) . '/www/phpledger/includes/bootstrap.php';
$input = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
$deadline = microtime(true) + 15;
while (!is_file($input['barrier'])) {
    if (microtime(true) > $deadline) {
        throw new RuntimeException('Concurrent test barrier timed out.');
    }
    usleep(10000);
}
$fixture = $input['fixture'];
try {
    if ($input['mode'] === 'module_set') {
        $module = pl_set_company_module($fixture['actor_id'], $fixture['company_id'], 'pos-showcase', $input['enabled'], $input['revision'], pl_module_registry()['pos-showcase']['digest'], 'Synthetic concurrent decision', $input['key']);
        $journal = ['id' => $module['revision']];
    } elseif ($input['mode'] === 'general_save') {
        $draft = pl_save_general_draft($fixture['actor_id'], $fixture['company_id'], $fixture['book_id'], $input['general_input'], $input['draft_id'], $input['revision']);
        $journal = ['id' => $draft['id']];
    } elseif ($input['mode'] === 'account_update') {
        $account = pl_save_account($fixture['actor_id'], $fixture['company_id'], $fixture['book_id'], $input['account_input'], $input['account_id'], $input['revision']);
        $journal = ['id' => $account['id']];
    } elseif ($input['mode'] === 'general_post') {
        $draft = pl_post_general_draft($fixture['actor_id'], $fixture['company_id'], $fixture['book_id'], $input['draft_id'], $input['revision']);
        $journal = ['id' => $draft['journal_id']];
    } elseif ($input['mode'] === 'account_create') {
        $account = pl_save_account($fixture['actor_id'], $fixture['company_id'], $fixture['book_id'], $input['account_input']);
        $journal = ['id' => $account['id']];
    } elseif ($input['mode'] === 'pos_checkout') {
        $sale = pl_checkout_pos($fixture['actor_id'], $fixture['company_id'], $fixture['book_id'], $input['pos_input']);
        $journal = ['id' => $sale['document_id']];
    } elseif ($input['mode'] === 'setup') {
        $company = pl_setup_company($fixture['actor_id'], $input['setup_input'], $input['key']);
        $journal = ['id' => $company['id']];
    } elseif ($input['mode'] === 'opening_confirm') {
        $cutover = pl_confirm_opening($fixture['actor_id'], $fixture['company_id'], $fixture['book_id'], $input['preview_id'], $input['hash'], true);
        $journal = ['id' => $cutover['id']];
    } elseif ($input['mode'] === 'bank_import') {
        $statement = pl_bank_import_statement($fixture['actor_id'], $fixture['company_id'], $fixture['book_id'], $input['bank_input'], $input['key'], $input['digest']);
        $journal = ['id' => $statement['id']];
    } elseif ($input['mode'] === 'bank_complete') {
        $statement = pl_bank_complete_statement($fixture['actor_id'], $fixture['company_id'], $fixture['book_id'], $input['statement_id'], $input['revision'], $input['key']);
        $journal = ['id' => $statement['id']];
    } elseif ($input['mode'] === 'document_create') {
        $document = pl_save_document($fixture['actor_id'], $fixture['company_id'], $fixture['book_id'], $input['document_input']);
        $journal = ['id' => $document['id']];
    } elseif ($input['mode'] === 'document_post') {
        $document = pl_post_document($fixture['actor_id'], $fixture['company_id'], $fixture['book_id'], $input['document_id'], $input['revision']);
        $journal = ['id' => $document['journal_id']];
    } else {
        $journal = $input['mode'] === 'reverse'
        ? pl_reverse_journal($fixture['actor_id'], $fixture['company_id'], $fixture['book_id'], $input['journal_id'], '2026-09-15', $input['key'], 'Concurrent correction proof')
            : pl_post_journal($fixture['actor_id'], $fixture['company_id'], $fixture['book_id'], $input['payload']);
    }
    echo json_encode(['id' => $journal['id']], JSON_THROW_ON_ERROR);
} catch (Throwable $error) {
    fwrite(STDERR, get_class($error) . ': ' . $error->getMessage());
    exit(1);
}
