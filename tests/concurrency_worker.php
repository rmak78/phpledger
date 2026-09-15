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
    if ($input['mode'] === 'pos_checkout') {
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
