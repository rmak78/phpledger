<?php
declare(strict_types=1);

// Creates and removes only a new, random schema on the disposable local test service.
if (PHP_SAPI !== 'cli' || getenv('PL_ENV') !== 'test' || getenv('PL_DB_HOST') !== 'db_test'
    || getenv('PL_DB_NAME') !== 'phpledger_test' || getenv('PL_DB_USER') !== 'root') {
    fwrite(STDERR, "Starter upgrade verification requires the disposable db_test service and its local root account.\n");
    exit(2);
}
require dirname(__DIR__) . '/www/phpledger/includes/bootstrap.php';
require dirname(__DIR__) . '/www/phpledger/install/migrate.php';
if (DB::$host !== 'db_test' || DB::$user !== 'root' || DB::$dbName !== 'phpledger_test') {
    throw new RuntimeException('Effective configuration must remain on the disposable test database.');
}

function starter_upgrade_assert(bool $condition, string $message): void
{
    if (!$condition) { throw new RuntimeException($message); }
}

/** Canonical row order is independent of a table's physical order or compound key. */
function starter_upgrade_rows(string $table, ?array $originalColumns = null): array
{
    if (!preg_match('/^pl_[a-z0-9_]+$/D', $table)) { throw new RuntimeException('Unexpected table identifier.'); }
    $rows = DB::query('SELECT * FROM %b', $table);
    foreach ($rows as &$row) {
        if ($originalColumns !== null) { $row = array_intersect_key($row, array_fill_keys($originalColumns, true)); }
        ksort($row);
    }
    unset($row);
    usort($rows, static fn(array $a, array $b): int => strcmp(json_encode($a, JSON_THROW_ON_ERROR), json_encode($b, JSON_THROW_ON_ERROR)));
    return $rows;
}

/**
 * Reconstruct a published 015 recognition fixture, including its exact canonical hash.
 * The current open-item reader requires columns introduced by 020 and cannot run against 015.
 * These fixture-only direct inserts are confined to the disposable historical schema.
 */
function starter_upgrade_seed_open_item(int $actor, array $fixture, int $party): array
{
    $company = $fixture['company_id']; $book = $fixture['book_id']; $control = $fixture['accounts']['1100'];
    pl_activate_open_item_account($actor, $company, $book, $control, 'Synthetic published foundation control');
    return pl_ledger_transaction(function () use ($actor, $fixture, $party, $company, $book, $control): array {
        pl_require_company_access($actor, $company, true); pl_ledger_book($company, $book, true);
        DB::insert('pl_open_items', ['company_id' => $company, 'book_id' => $book, 'party_id' => $party, 'control_account_id' => $control,
            'direction' => 'receivable', 'currency' => 'USD', 'source_reference' => 'synthetic-0.3-open-item', 'created_by' => $actor]);
        $item = (int) DB::insertId();
        $payload = pl_normalize_journal(['date' => '2026-02-01', 'currency' => 'USD', 'source_type' => 'open_item_recognition',
            'source_reference' => 'open-item:' . $item, 'idempotency_key' => 'synthetic-0.3-recognition', 'description' => 'Synthetic published foundation recognition',
            'lines' => [['account_id' => $control, 'debit' => '250.5001', 'credit' => '0'],
                ['account_id' => $fixture['accounts']['4000'], 'debit' => '0', 'credit' => '250.5001']]]);
        $hash = hash('sha256', json_encode(['company_id' => $company, 'book_id' => $book, 'reversal_of_id' => null, 'payload' => $payload], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
        DB::insert('pl_journals', ['company_id' => $company, 'book_id' => $book, 'period_id' => $fixture['period_id'], 'journal_date' => $payload['date'],
            'currency' => $payload['currency'], 'description' => $payload['description'], 'source_type' => $payload['source_type'], 'source_reference' => $payload['source_reference'],
            'idempotency_key' => $payload['idempotency_key'], 'payload_hash' => $hash, 'posted_by' => $actor]);
        $journal = (int) DB::insertId(); $controlLine = null;
        foreach ($payload['lines'] as $index => $line) {
            DB::insert('pl_journal_lines', ['journal_id' => $journal, 'company_id' => $company, 'book_id' => $book, 'line_number' => $index + 1] + $line);
            if ($line['account_id'] === $control) { $controlLine = (int) DB::insertId(); }
        }
        DB::insert('pl_open_item_entries', ['company_id' => $company, 'book_id' => $book, 'item_id' => $item, 'kind' => 'recognition', 'journal_line_id' => $controlLine]);
        return ['item_id' => $item, 'journal_id' => $journal];
    });
}

$database = 'phpledger_starter_verify_' . bin2hex(random_bytes(12));
$created = false; $ownsLock = false;
$lock = 'phpledger:migrate:' . substr(hash('sha256', $database), 0, 40);
$files = glob(PL_APP . '/install/migrations/[0-9]*.php') ?: [];
sort($files, SORT_STRING);
$versions = array_map(static fn(string $file): string => basename($file, '.php'), $files);
$baseline = array_values(array_filter($versions, static fn(string $version): bool => $version <= '016_correction_identity'));
$expectedNew = array_values(array_filter($versions, static fn(string $version): bool => $version > '016_correction_identity'));
starter_upgrade_assert(count($baseline) === 17 && count($expectedNew) === 9 && end($expectedNew) === '025_tax_price_mode', 'Review this verifier when the published baseline or nine-migration starter chain changes.');

try {
    starter_upgrade_assert(preg_match('/^phpledger_starter_verify_[a-f0-9]{24}$/D', $database) === 1
        && (int) DB::queryFirstField('SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME=%s', $database) === 0, 'Refusing to reuse a verification schema.');
    DB::query('CREATE DATABASE %b CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci', $database); $created = true;
    DB::useDB($database);
    starter_upgrade_assert((int) DB::queryFirstField('SELECT GET_LOCK(%s,10)', $lock) === 1, 'Cannot acquire the isolated baseline migration lock.'); $ownsLock = true;
    DB::query("CREATE TABLE pl_schema_migrations (version VARCHAR(100) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY, checksum CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL, status ENUM('applying','applied') NOT NULL, applied_at DATETIME NULL) ENGINE=InnoDB");
    foreach ($files as $file) {
        $version = basename($file, '.php');
        if (!in_array($version, $baseline, true)) { continue; }
        // 013's temporary backfill guards require BOTH the named lock and an applying receipt.
        DB::insert('pl_schema_migrations', ['version' => $version, 'checksum' => hash_file('sha256', $file), 'status' => 'applying']);
        foreach (require $file as $statement) { DB::query($statement); }
        DB::update('pl_schema_migrations', ['status' => 'applied', 'applied_at' => gmdate('Y-m-d H:i:s')], 'version=%s', $version);
    }
    DB::queryFirstField('SELECT RELEASE_LOCK(%s)', $lock); $ownsLock = false;

    $actor = pl_create_user('starter-upgrade@example.invalid', 'Synthetic starter upgrade owner', bin2hex(random_bytes(24)));
    $fixture = pl_create_company($actor, 'Synthetic published 0.3 book', 'USD', '2026-01-01');
    $company = $fixture['company_id']; $book = $fixture['book_id'];
    $cashInput = ['kind' => 'receipt', 'date' => '2026-01-10', 'amount' => '125.0001', 'money_account_id' => $fixture['accounts']['1000'],
        'category_account_id' => $fixture['accounts']['4000'], 'counterparty' => 'Synthetic original payer', 'reference' => 'synthetic-original-cash', 'memo' => 'Preserve exact published history', 'creation_key' => 'synthetic-old-cash'];
    $cash = pl_save_document($actor, $company, $book, $cashInput);
    $cash = pl_post_document($actor, $company, $book, $cash['id'], 1);
    $party = pl_save_party($actor, $company, $book, ['legal_name' => 'Synthetic retained party', 'entity_type' => 'private_company', 'country_code' => 'GB',
        'is_customer' => true, 'is_vendor' => true, 'currency' => 'USD', 'request_key' => 'synthetic-old-party', 'reason' => 'Synthetic published party and profile']);
    pl_currency_rate_enter($actor, $company, $book, ['from_currency' => 'EUR', 'to_currency' => 'USD', 'rate_date' => '2026-01-09', 'rate' => '1.234567890123',
        'source' => 'manual', 'note' => 'Synthetic retained manual rate evidence', 'idempotency_key' => 'synthetic-old-rate']);
    $oldItem = starter_upgrade_seed_open_item($actor, $fixture, (int) $party['id']);

    $openingFixture = pl_create_company($actor, 'Synthetic retained opening book', 'USD', '2026-01-01');
    DB::update('pl_companies', ['setup_status' => 'opening_required'], 'id=%i', $openingFixture['company_id']);
    $openingInput = ['cutover_date' => '2026-01-01', 'source' => 'Synthetic old opening evidence', 'balances' => [
        ['account_code' => '1000', 'debit' => '1000', 'credit' => '0'], ['account_code' => '1100', 'debit' => '75', 'credit' => '0'],
        ['account_code' => '2000', 'debit' => '0', 'credit' => '30'], ['account_code' => '3000', 'debit' => '0', 'credit' => '1045']],
        'unpaid_documents' => [['kind' => 'receivable', 'account_code' => '1100', 'party' => 'Synthetic opening customer', 'reference' => 'synthetic-old-invoice', 'document_date' => '2025-12-20', 'due_date' => '2026-01-20', 'outstanding' => '75'],
            ['kind' => 'payable', 'account_code' => '2000', 'party' => 'Synthetic opening supplier', 'reference' => 'synthetic-old-bill', 'document_date' => '2025-12-21', 'due_date' => '2026-01-21', 'outstanding' => '30']]];
    $opening = pl_preview_opening($actor, $openingFixture['company_id'], $openingFixture['book_id'], $openingInput, 'synthetic-old-opening');
    pl_confirm_opening($actor, $openingFixture['company_id'], $openingFixture['book_id'], (int) $opening['id'], $opening['payload_hash'], true);

    $tables = DB::queryFirstColumn("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA=%s AND TABLE_TYPE='BASE TABLE' AND TABLE_NAME<>'pl_schema_migrations' ORDER BY TABLE_NAME", $database);
    $snapshot = []; $rowCount = 0;
    foreach ($tables as $table) {
        $rows = starter_upgrade_rows($table); $rowCount += count($rows);
        $columns = DB::queryFirstColumn('SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=%s AND TABLE_NAME=%s ORDER BY ORDINAL_POSITION', $database, $table);
        $snapshot[$table] = ['columns' => $columns, 'rows' => $rows];
    }
    $receipts = DB::query('SELECT * FROM pl_schema_migrations ORDER BY version');
    $migration = pl_migrate();
    starter_upgrade_assert($migration['applied'] === $expectedNew && $migration['skipped'] === $baseline, 'Unexpected starter migration chain or baseline receipt list.');
    foreach ($snapshot as $table => $before) {
        starter_upgrade_assert($before['rows'] === starter_upgrade_rows($table, $before['columns']), 'Upgrade changed original rows or column values in ' . $table . '.');
    }
    starter_upgrade_assert($receipts === DB::query('SELECT * FROM pl_schema_migrations WHERE version IN %ls ORDER BY version', $baseline), 'Upgrade rewrote a published migration receipt.');
    starter_upgrade_assert(pl_get_document($actor, $company, $book, $cash['id'])['amount'] === '125.0001', 'Published cash source is no longer readable.');
    starter_upgrade_assert(pl_post_document($actor, $company, $book, $cash['id'], 1)['journal_id'] === $cash['journal_id'], 'Published cash posting replay changed its journal.');
    starter_upgrade_assert(pl_get_open_item($actor, $company, $book, $oldItem['item_id'])['remaining_fc'] === '250.5001', 'Existing open-item basis changed during upgrade.');
    foreach (['inventory','purchasing','pos-showcase'] as $optional) {
        starter_upgrade_assert(!pl_module_state($company, $optional)['enabled'], 'Upgrade enabled an optional module without an owner decision.');
    }

    $draft = pl_save_ar_document($actor, $company, $book, ['kind' => 'invoice', 'party_id' => (int) $party['id'], 'date' => '2026-02-02', 'due_date' => '2026-03-02', 'currency' => 'USD',
        'reference' => 'synthetic-new-starter-invoice', 'creation_key' => 'synthetic-new-invoice', 'lines' => [['description' => 'Synthetic service', 'quantity' => '4', 'unit_price' => '25', 'account_id' => $fixture['accounts']['4000']]]]);
    $invoice = pl_post_ar_document($actor, $company, $book, $draft['id'], 1);
    starter_upgrade_assert($invoice['outstanding_fc'] === '100.0000', 'New starter invoice did not recognize its expected open item.');
    foreach (['40','60'] as $index => $amount) {
        $payment = ['bank_account_id' => $fixture['accounts']['1000'], 'gain_account_id' => $fixture['accounts']['4000'], 'loss_account_id' => $fixture['accounts']['5000'],
            'amount_fc' => $amount, 'date' => '2026-02-03', 'description' => 'Synthetic upgraded invoice payment', 'idempotency_key' => 'synthetic-new-payment-' . $index];
        $receipt = pl_settle_ar_document($actor, $company, $book, $invoice['id'], $payment);
        starter_upgrade_assert($receipt === pl_settle_ar_document($actor, $company, $book, $invoice['id'], $payment), 'New settlement retry did not return the same receipt.');
    }
    starter_upgrade_assert(pl_get_ar_document($actor, $company, $book, $invoice['id'])['outstanding_fc'] === '0.0000', 'New starter invoice failed to settle fully.');
    pl_settle_open_item($actor, $company, $book, ['item_id' => $oldItem['item_id'], 'bank_account_id' => $fixture['accounts']['1000'], 'gain_account_id' => $fixture['accounts']['4000'], 'loss_account_id' => $fixture['accounts']['5000'],
        'amount_fc' => '250.5001', 'date' => '2026-02-04', 'description' => 'Synthetic settlement of preserved 0.3 item', 'idempotency_key' => 'synthetic-old-item-payment']);
    starter_upgrade_assert(pl_get_open_item($actor, $company, $book, $oldItem['item_id'])['remaining_fc'] === '0.0000', 'Preserved open item was not usable after upgrade.');
    $report = pl_ar_ap_open_items($actor, $company, $book, 'receivable', '2026-02-04');
    starter_upgrade_assert($report['total_base'] === '0.0000' && $report['reconciled'], 'Upgraded AR and its general-ledger control did not reconcile.');
    $trial = pl_trial_balance($actor, $company, $book);
    starter_upgrade_assert($trial['balanced'] && $trial['total_debit'] === '475.5002', 'Upgraded exact cash/income balances are incorrect.');
    starter_upgrade_assert(pl_migrate()['applied'] === [], 'Migration replay applied additional changes.');
    echo json_encode(['status' => 'passed', 'baseline' => 'published-0.3.0-through-016', 'baseline_receipts_preserved' => count($receipts),
        'baseline_tables_preserved' => count($tables), 'baseline_rows_preserved' => $rowCount, 'new_migrations' => $migration['applied'], 'total_migrations' => count($versions),
        'checks' => ['original-column values and rows unchanged', 'published checksums/status/timestamps unchanged', 'cash source and posting replay', 'party/profile and manual FX rate preserved',
            'opening journal and unpaid evidence preserved', '015 open-item basis preserved and settled', 'new AR invoice and partial/final settlement', 'settlement retry', 'AR-to-GL reconciliation', 'exact trial balance', 'optional modules remain disabled', 'empty migration replay'],
        'fixture_boundary' => '015 recognition is a canonical persisted synthetic fixture; current readers require 020 columns.', 'final_trial_debit' => $trial['total_debit']], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
} finally {
    if ($ownsLock) { DB::queryFirstField('SELECT RELEASE_LOCK(%s)', $lock); }
    DB::useDB('phpledger_test');
    if ($created && preg_match('/^phpledger_starter_verify_[a-f0-9]{24}$/D', $database)) {
        DB::query('DROP DATABASE %b', $database);
        echo "Removed only this run's random starter verification schema; phpledger_test and its browser fixtures remain intact.\n";
    }
}
