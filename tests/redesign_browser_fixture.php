<?php
declare(strict_types=1);

// Sample setup states for browser acceptance. Never use a live database.
if (getenv('PL_ENV') !== 'test' || getenv('PL_DB_NAME') !== 'phpledger_test') {
    fwrite(STDERR, "Use the isolated test database.\n"); exit(2);
}
require_once dirname(__DIR__) . '/www/phpledger/includes/bootstrap.php';
$suffix = bin2hex(random_bytes(5));
$email = 'redesign-browser-' . $suffix . '@example.test';
$actor = pl_create_user($email, 'Sample Setup Owner', 'Sample-browser-only-2026!');
$states = [];
foreach (['opening', 'review', 'conversion'] as $state) {
    $fixture = pl_create_company($actor, 'Sample ' . ucfirst($state) . ' ' . $suffix, 'USD', '2026-09-01');
    $company = $fixture['company_id']; $book = $fixture['book_id'];
    DB::update('pl_companies', ['setup_status' => $state === 'review' ? 'review_required' : 'opening_required'], 'id=%i', $company);
    if ($state === 'conversion') {
        $preview = pl_preview_opening($actor, $company, $book, [
            'cutover_date' => '2026-09-01', 'source' => 'Sample reviewed balances',
            'balances' => [
                ['account_code' => '1100', 'debit' => '300', 'credit' => '0'],
                ['account_code' => '2000', 'debit' => '0', 'credit' => '200'],
                ['account_code' => '3000', 'debit' => '0', 'credit' => '100'],
            ],
            'unpaid_documents' => [
                ['kind' => 'receivable', 'account_code' => '1100', 'party' => 'Sample customer', 'reference' => 'SYN-INV-01', 'document_date' => '2026-08-15', 'due_date' => '2026-09-15', 'outstanding' => '300'],
                ['kind' => 'payable', 'account_code' => '2000', 'party' => 'Sample supplier', 'reference' => 'SYN-BILL-01', 'document_date' => '2026-08-20', 'due_date' => '2026-09-20', 'outstanding' => '200'],
            ],
        ], 'sample-cutover-' . $suffix);
        pl_confirm_opening($actor, $company, $book, (int) $preview['id'], $preview['payload_hash'], true);
        $party = pl_save_party($actor, $company, $book, ['legal_name' => 'Sample mapped party', 'entity_type' => 'private_company', 'country_code' => 'GB', 'is_customer' => true, 'is_vendor' => true, 'currency' => 'USD', 'request_key' => 'sample-party-' . $suffix, 'reason' => 'Sample browser acceptance']);
        $fixture['party_id'] = $party['id'];
    }
    $states[$state] = $fixture;
}
echo json_encode(['email' => $email, 'states' => $states], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT) . "\n";
