<?php
declare(strict_types=1);

// Every schema mutation is confined to a new, collision-checked disposable test database.
if (PHP_SAPI !== 'cli' || getenv('PL_ENV') !== 'test' || getenv('PL_DB_HOST') !== 'db_test'
    || getenv('PL_DB_NAME') !== 'phpledger_test' || getenv('PL_DB_USER') !== 'root') {
    fwrite(STDERR, "Currency upgrade verification requires the disposable db_test service and its local root account.\n");
    exit(2);
}
require dirname(__DIR__) . '/www/phpledger/includes/bootstrap.php';
require dirname(__DIR__) . '/www/phpledger/install/migrate.php';
if (DB::$host !== 'db_test' || DB::$user !== 'root' || DB::$dbName !== 'phpledger_test') {
    throw new RuntimeException('Effective configuration must remain on the disposable test database.');
}

function currency_upgrade_expect_rejection(callable $work, string $message): void
{
    try { $work(); } catch (Throwable $error) {
        if (str_contains($error->getMessage(), $message)) { return; }
        throw new RuntimeException('The guard failed for an unexpected reason.', 0, $error);
    }
    throw new RuntimeException('An expected currency-upgrade guard did not reject the operation.');
}

/** @phpstan-impure */
function currency_upgrade_acquire_lock(string $lock): void
{
    if ((int) DB::queryFirstField('SELECT GET_LOCK(%s, 10)', $lock) !== 1) {
        throw new RuntimeException('Cannot acquire the disposable migration/recovery lock.');
    }
}

$upgradeDatabase = 'phpledger_currency_verify_' . bin2hex(random_bytes(12));
$created = false;
$lock = 'phpledger:migrate:' . substr(hash('sha256', $upgradeDatabase), 0, 40);
$ownsLock = false;
$second = null;
try {
    if (!preg_match('/^phpledger_currency_verify_[a-f0-9]{24}$/D', $upgradeDatabase)
        || (int) DB::queryFirstField('SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = %s', $upgradeDatabase) !== 0) {
        throw new RuntimeException('Refusing to reuse a currency verification database.');
    }
    DB::query('CREATE DATABASE %b CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci', $upgradeDatabase);
    $created = true;
    DB::useDB($upgradeDatabase);
    DB::query("CREATE TABLE pl_schema_migrations (version VARCHAR(100) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY, checksum CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL, status ENUM('applying','applied') NOT NULL, applied_at DATETIME NULL) ENGINE=InnoDB");
    $files = glob(PL_APP . '/install/migrations/*.php') ?: [];
    sort($files, SORT_STRING);
    foreach ($files as $file) {
        $version = basename($file, '.php');
        if ($version > '012_demo_history_periods') { continue; }
        foreach (require $file as $statement) { DB::query($statement); }
        DB::insert('pl_schema_migrations', ['version' => $version, 'checksum' => hash_file('sha256', $file), 'status' => 'applied', 'applied_at' => gmdate('Y-m-d H:i:s')]);
    }
    $actor = pl_create_user('currency-upgrade@example.invalid', 'Sample currency upgrade owner', 'Sample upgrade password 293!');
    DB::insert('pl_companies', ['name' => 'Sample prior currency book', 'currency' => 'USD', 'start_date' => '2026-01-01', 'fiscal_year_end' => '12-31', 'created_by' => $actor, 'setup_status' => 'ready']);
    $company = (int) DB::insertId();
    DB::insert('pl_company_members', ['company_id' => $company, 'user_id' => $actor, 'role' => 'owner']);
    DB::insert('pl_books', ['company_id' => $company, 'name' => 'Primary book']);
    $book = (int) DB::insertId();
    DB::insert('pl_periods', ['company_id' => $company, 'book_id' => $book, 'start_date' => '2026-01-01', 'end_date' => '2026-12-31']);
    $period = (int) DB::insertId();
    $accounts = [];
    foreach ([['1000', 'Sample cash', 'asset', 'cash_bank'], ['4000', 'Sample revenue', 'income', 'income']] as [$code, $name, $type, $role]) {
        DB::insert('pl_accounts', ['company_id' => $company, 'book_id' => $book, 'code' => $code, 'name' => $name, 'type' => $type, 'role' => $role]);
        $accounts[] = (int) DB::insertId();
    }
    // This is the exact canonical pre-013 shape and hashing order, not a placeholder hash.
    $legacy = ['date' => '2026-09-14', 'currency' => 'USD', 'source_type' => 'receipt', 'source_reference' => 'currency-upgrade-original', 'idempotency_key' => 'currency-upgrade-original', 'description' => 'Sample original posting', 'lines' => [
        ['account_id' => $accounts[0], 'debit' => '125.0000', 'credit' => '0.0000', 'description' => 'Sample original line'],
        ['account_id' => $accounts[1], 'debit' => '0.0000', 'credit' => '125.0000', 'description' => 'Sample original line'],
    ]];
    $hash = hash('sha256', json_encode(['company_id' => $company, 'book_id' => $book, 'reversal_of_id' => null, 'payload' => $legacy], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    DB::insert('pl_journals', ['company_id' => $company, 'book_id' => $book, 'period_id' => $period, 'journal_date' => $legacy['date'], 'currency' => 'USD', 'description' => $legacy['description'], 'source_type' => $legacy['source_type'], 'source_reference' => $legacy['source_reference'], 'idempotency_key' => $legacy['idempotency_key'], 'payload_hash' => $hash, 'posted_by' => $actor]);
    $journal = (int) DB::insertId();
    foreach ($legacy['lines'] as $index => $line) {
        DB::insert('pl_journal_lines', ['journal_id' => $journal, 'company_id' => $company, 'book_id' => $book, 'line_number' => $index + 1] + $line);
    }
    $beforeHeader = DB::queryFirstRow('SELECT * FROM pl_journals WHERE id = %i', $journal);
    $beforeLines = DB::query('SELECT * FROM pl_journal_lines WHERE journal_id = %i ORDER BY line_number', $journal);
    $file = PL_APP . '/install/migrations/013_currency_foundation.php';
    $statements = require $file;
    DB::insert('pl_schema_migrations', ['version' => '013_currency_foundation', 'checksum' => hash_file('sha256', $file), 'status' => 'applying']);
    currency_upgrade_acquire_lock($lock);
    $ownsLock = true;
    $pausedAt = null;
    foreach ($statements as $index => $statement) {
        DB::query($statement);
        if ($statement === 'DROP TRIGGER pl_lines_no_update') { $pausedAt = $index; break; }
    }
    if ($pausedAt === null) { throw new RuntimeException('The tested migration no longer has the expected guarded pause point.'); }
    currency_upgrade_expect_rejection(fn() => DB::query('UPDATE pl_journal_lines SET debit = debit + 1 WHERE id = %i', $beforeLines[0]['id']), 'Only initial domestic');
    $backfill = $statements[$pausedAt + 1];
    if (!str_starts_with($backfill, 'UPDATE pl_journal_lines l JOIN pl_journals')) { throw new RuntimeException('Review the changed backfill sequence before using this verifier.'); }
    $second = new PDO('mysql:host=db_test;port=' . DB::$port . ';dbname=' . $upgradeDatabase . ';charset=utf8mb4', 'root', DB::$password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    currency_upgrade_expect_rejection(fn() => $second->exec($backfill), 'Only initial domestic');
    currency_upgrade_expect_rejection(fn() => $second->exec("INSERT INTO pl_journals (company_id,book_id,period_id,journal_date,currency,description,source_type,source_reference,idempotency_key,payload_hash,posted_by) SELECT company_id,book_id,period_id,journal_date,currency,description,source_type,'blocked-source','blocked-key',payload_hash,posted_by FROM pl_journals LIMIT 1"), 'Currency upgrade in progress');
    currency_upgrade_expect_rejection(fn() => $second->exec('INSERT INTO pl_journal_lines (journal_id,company_id,book_id,line_number,account_id,description,debit,credit) SELECT journal_id,company_id,book_id,99,account_id,description,debit,credit FROM pl_journal_lines LIMIT 1'), 'Currency upgrade in progress');
    DB::queryFirstField('SELECT RELEASE_LOCK(%s)', $lock);
    $ownsLock = false;
    currency_upgrade_expect_rejection(fn() => pl_migrate(), 'An earlier migration stopped');
    // Reviewed recovery: identical checksum, known completed prefix, same guarded connection lock.
    currency_upgrade_acquire_lock($lock);
    $ownsLock = true;
    foreach (array_slice($statements, $pausedAt + 1) as $statement) { DB::query($statement); }
    $afterLines = DB::query('SELECT * FROM pl_journal_lines WHERE journal_id = %i ORDER BY line_number', $journal);
    $oldKeys = array_fill_keys(array_keys($beforeLines[0]), true);
    if ($beforeHeader !== DB::queryFirstRow('SELECT * FROM pl_journals WHERE id = %i', $journal)
        || $beforeLines !== array_map(static fn(array $line): array => array_intersect_key($line, $oldKeys), $afterLines)) {
        throw new RuntimeException('Backfill changed original journal data or its hash.');
    }
    foreach ($afterLines as $line) {
        if ($line['currency'] !== 'USD' || $line['amount_fc'] !== '125.0000' || $line['amount_base'] !== '125.0000'
            || $line['rate'] !== '1.000000000000' || $line['rate_type'] !== 'spot' || (int) $line['rate_is_stale'] !== 0
            || $line['rate_source_id'] !== null || $line['ic_counterparty_entity_id'] !== null) {
            throw new RuntimeException('Recovered domestic metadata is incomplete.');
        }
    }
    currency_upgrade_expect_rejection(fn() => DB::query('UPDATE pl_journal_lines SET amount_fc = amount_fc WHERE id = %i', $beforeLines[0]['id']), 'immutable');
    currency_upgrade_expect_rejection(fn() => DB::query('DELETE FROM pl_journal_lines WHERE id = %i', $beforeLines[0]['id']), 'immutable');
    $triggerNames = DB::queryFirstColumn('SELECT TRIGGER_NAME FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = %s', $upgradeDatabase);
    foreach (['pl_lines_no_update','pl_lines_no_delete','pl_journals_no_update','pl_journals_no_delete'] as $name) {
        if (!in_array($name, $triggerNames, true)) { throw new RuntimeException('An original immutable guard is missing.'); }
    }
    if (array_filter($triggerNames, static fn(string $name): bool => str_contains($name, 'currency_upgrade') || $name === 'pl_lines_currency_backfill')) { throw new RuntimeException('A temporary guard survived successful recovery.'); }
    DB::update('pl_schema_migrations', ['status' => 'applied', 'applied_at' => gmdate('Y-m-d H:i:s')], 'version = %s', '013_currency_foundation');
    DB::queryFirstField('SELECT RELEASE_LOCK(%s)', $lock);
    $ownsLock = false;
    pl_migrate();
    $retry = pl_post_journal($actor, $company, $book, $legacy);
    if ((int) $retry['id'] !== $journal || (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journals') !== 1
        || DB::queryFirstField('SELECT payload_hash FROM pl_journals WHERE id = %i', $journal) !== $hash) {
        throw new RuntimeException('The populated upgrade broke legacy command replay or rewrote history.');
    }
    $changed = $legacy;
    $changed['description'] = 'Different request content';
    currency_upgrade_expect_rejection(fn() => pl_post_journal($actor, $company, $book, $changed), 'different journal');
    if (pl_migrate()['applied'] !== []) { throw new RuntimeException('Migration replay was not empty.'); }
    echo "Currency upgrade passed: populated baseline, restricted backfill, non-owner connection guards, interrupted receipt, reviewed recovery, unchanged history, complete metadata, restored guards and legacy replay.\n";
} finally {
    $second = null;
    if ($ownsLock) { DB::queryFirstField('SELECT RELEASE_LOCK(%s)', $lock); }
    DB::useDB('phpledger_test');
    if ($created && preg_match('/^phpledger_currency_verify_[a-f0-9]{24}$/D', $upgradeDatabase)) {
        DB::query('DROP DATABASE %b', $upgradeDatabase);
        echo "Removed only this run's isolated currency-upgrade database.\n";
    }
}
