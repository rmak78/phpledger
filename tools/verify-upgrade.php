<?php
declare(strict_types=1);

// This destructive cleanup is confined to a new random database in the disposable test service.
if (PHP_SAPI !== 'cli' || getenv('PL_ENV') !== 'test' || getenv('PL_DB_HOST') !== 'db_test'
    || getenv('PL_DB_NAME') !== 'phpledger_test' || getenv('PL_DB_USER') !== 'root') {
    fwrite(STDERR, "Upgrade verification requires the disposable db_test service and its local root account.\n");
    exit(2);
}
require dirname(__DIR__) . '/www/phpledger/includes/bootstrap.php';
require dirname(__DIR__) . '/www/phpledger/install/migrate.php';
$upgradeDatabase = 'phpledger_upgrade_verify_' . bin2hex(random_bytes(12));
$created = false;
try {
    if (!preg_match('/^phpledger_upgrade_verify_[a-f0-9]{24}$/D', $upgradeDatabase)
        || (int) DB::queryFirstField('SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = %s', $upgradeDatabase) !== 0) {
        throw new RuntimeException('Refusing to reuse an upgrade database.');
    }
    DB::query('CREATE DATABASE %b CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci', $upgradeDatabase);
    $created = true;
    DB::useDB($upgradeDatabase);
    foreach (require PL_APP . '/install/migrations/001_foundation.php' as $statement) {
        DB::query($statement);
    }
    DB::query("CREATE TABLE pl_schema_migrations (version VARCHAR(100) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY, checksum CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL, status ENUM('applying','applied') NOT NULL, applied_at DATETIME NULL) ENGINE=InnoDB");
    DB::insert('pl_schema_migrations', ['version' => '001_foundation', 'checksum' => hash_file('sha256', PL_APP . '/install/migrations/001_foundation.php'), 'status' => 'applied', 'applied_at' => gmdate('Y-m-d H:i:s')]);
    $actor = pl_create_user('upgrade@example.invalid', 'Synthetic upgrade owner', 'Synthetic upgrade passphrase 471!');
    DB::insert('pl_companies', ['name' => 'Prior foundation synthetic company', 'currency' => 'USD', 'start_date' => '2026-01-01', 'fiscal_year_end' => '12-31', 'created_by' => $actor]);
    $company = (int) DB::insertId();
    DB::insert('pl_company_members', ['company_id' => $company, 'user_id' => $actor, 'role' => 'owner']);
    DB::insert('pl_books', ['company_id' => $company, 'name' => 'Primary book']);
    $book = (int) DB::insertId();
    DB::insert('pl_periods', ['company_id' => $company, 'book_id' => $book, 'start_date' => '2026-01-01', 'end_date' => '2026-12-31']);
    $period = (int) DB::insertId();
    $accounts = [];
    foreach (pl_starter_template()['accounts'] as $definition) {
        DB::insert('pl_accounts', ['company_id' => $company, 'book_id' => $book, 'code' => $definition['code'], 'name' => $definition['code'] === '1000' ? 'Preserved custom bank name' : $definition['name'], 'type' => $definition['type']]);
        $accounts[$definition['semantic_key']] = (int) DB::insertId();
    }
    DB::insert('pl_journals', ['company_id' => $company, 'book_id' => $book, 'period_id' => $period, 'journal_date' => '2026-09-14', 'currency' => 'USD', 'description' => 'Prior synthetic receipt', 'source_type' => 'receipt', 'source_reference' => 'upgrade-fixture', 'idempotency_key' => 'upgrade-fixture', 'payload_hash' => hash('sha256', 'synthetic-old-fixture'), 'posted_by' => $actor]);
    $journal = (int) DB::insertId();
    foreach ([['core.cash_bank', '125.0000', '0.0000'], ['core.income.sales', '0.0000', '125.0000']] as $index => [$key, $debit, $credit]) {
        DB::insert('pl_journal_lines', ['journal_id' => $journal, 'company_id' => $company, 'book_id' => $book, 'line_number' => $index + 1, 'account_id' => $accounts[$key], 'description' => 'Prior synthetic line', 'debit' => $debit, 'credit' => $credit]);
    }
    $beforeHeader = DB::queryFirstRow('SELECT * FROM pl_journals WHERE id = %i', $journal);
    $beforeLines = DB::query('SELECT * FROM pl_journal_lines WHERE journal_id = %i ORDER BY line_number', $journal);
    $beforeAccounts = DB::query('SELECT id, company_id, book_id, code, name, type, is_active FROM pl_accounts ORDER BY id');
    $migration = pl_migrate();
    if ($migration['applied'] !== ['002_product_slice', '003_demo_isolation', '004_pos_showcase', '005_demo_period_guard', '006_opening_cutover', '007_period_administration', '008_bank_reconciliation', '009_bank_draft_cancellation'] || $migration['skipped'] !== ['001_foundation']) {
        throw new RuntimeException('Unexpected upgrade migration receipt.');
    }
    if ($beforeHeader !== DB::queryFirstRow('SELECT * FROM pl_journals WHERE id = %i', $journal)
        || $beforeLines !== DB::query('SELECT * FROM pl_journal_lines WHERE journal_id = %i ORDER BY line_number', $journal)
        || $beforeAccounts !== DB::query('SELECT id, company_id, book_id, code, name, type, is_active FROM pl_accounts ORDER BY id')) {
        throw new RuntimeException('The additive upgrade changed prior accounting records.');
    }
    $context = pl_company_context($actor, $company);
    if ($context['setup_status'] !== 'review_required' || $context['template'] !== null
        || count(array_filter($context['accounts'], static fn (array $account): bool => $account['role'] !== null)) !== 0) {
        throw new RuntimeException('Prior companies were not safely held for chart/opening review.');
    }
    pl_confirm_existing_setup($actor, $company, $book, $accounts, true);
    if (pl_trial_balance($actor, $company, $book)['total_debit'] !== '125.0000'
        || pl_migrate()['applied'] !== [] || DB::queryFirstField('SELECT @@session.time_zone') !== '+00:00') {
        throw new RuntimeException('Review/replay/UTC validation failed.');
    }
    echo "Upgrade passed: original 001 -> 009, preserved six accounts and posted journal/lines, required review, explicit role mapping, reconciled report, replay and UTC session.\n";
} finally {
    DB::useDB('phpledger_test');
    if ($created && preg_match('/^phpledger_upgrade_verify_[a-f0-9]{24}$/D', $upgradeDatabase)) {
        DB::query('DROP DATABASE %b', $upgradeDatabase);
        echo "Removed only this run's isolated upgrade database; phpledger_test and development data remain intact.\n";
    }
}
