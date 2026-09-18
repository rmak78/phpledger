<?php
declare(strict_types=1);
// This test creates and removes only its own random database on db_test.
if (getenv('PL_ENV') !== 'test' || getenv('PL_DB_HOST') !== 'db_test') { fwrite(STDERR, "Use the isolated Docker test service.\n"); exit(2); }
require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/www/phpledger/includes/functions/update_functions.php';
require dirname(__DIR__) . '/www/phpledger/includes/functions/update_database_functions.php';
DB::$host = 'db_test'; DB::$user = 'root'; DB::$password = 'local-test-root-only'; DB::$dbName = 'information_schema'; DB::$encoding = 'utf8mb4';
$database = 'phpledger_update_test_' . bin2hex(random_bytes(8));
$directory = sys_get_temp_dir() . '/' . $database;
mkdir($directory, 0700);
$created = false;
try {
    DB::query('CREATE DATABASE %b CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci', $database); $created = true; DB::query('USE %b', $database);
    DB::query("CREATE TABLE pl_schema_migrations (version VARCHAR(100) PRIMARY KEY, checksum CHAR(64), status VARCHAR(10)) ENGINE=InnoDB");
    DB::insert('pl_schema_migrations', ['version' => '001_synthetic', 'checksum' => str_repeat('a', 64), 'status' => 'applied']);
    DB::query('CREATE TABLE pl_journal_lines (id BIGINT AUTO_INCREMENT PRIMARY KEY, journal_id BIGINT, company_id BIGINT, book_id BIGINT, debit DECIMAL(20,4), credit DECIMAL(20,4), memo VARBINARY(100), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, doubled DECIMAL(20,4) AS (debit*2) STORED) ENGINE=InnoDB');
    for ($index = 1; $index <= 601; $index++) { DB::insert('pl_journal_lines', ['journal_id' => $index, 'company_id' => 1, 'book_id' => 1, 'debit' => '1.0000', 'credit' => '1.0000', 'memo' => "synthetic\0\xff:$index"]); }
    DB::query('CREATE VIEW pl_view AS SELECT id,debit,credit FROM pl_journal_lines');
    DB::query("CREATE TRIGGER pl_guard BEFORE DELETE ON pl_journal_lines FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Immutable synthetic journal'");
    $receipt = null; $backupSteps = 0;
    while ($receipt === null && $backupSteps++ < 100) { $receipt = pl_update_database_backup($directory); }
    if ($receipt === null || $backupSteps < 6) { throw new RuntimeException('Backup was not chunked.'); }
    $original = pl_update_json($directory . '/manifest.json');
    DB::query('UPDATE pl_journal_lines SET memo=%s WHERE id=1', 'changed without changing balanced totals');
    try { pl_update_database_preserved($directory); throw new LogicException('Changed original source value was accepted.'); }
    catch (RuntimeException $expected) { /* Changed source value is rejected despite unchanged financial totals. */ }
    DB::query('DROP TRIGGER pl_guard'); DB::query('DELETE FROM pl_journal_lines WHERE id <= 300');
    DB::query('ALTER TABLE pl_journal_lines ADD broken_column INT');
    DB::query('CREATE TABLE pl_incomplete_migration (id INT PRIMARY KEY) ENGINE=InnoDB');
    DB::insert('pl_schema_migrations', ['version' => '002_failed', 'checksum' => str_repeat('b', 64), 'status' => 'applying']);
    $complete = false; $restoreSteps = 0; $interrupted = false;
    while (!$complete && $restoreSteps++ < 150) {
        $complete = pl_update_database_restore($directory, $receipt);
        $progress = pl_update_json($directory . '/restore.json');
        if (!$interrupted && $progress['phase'] === 'rows' && $progress['offset'] > 0) {
            // Simulate a committed batch whose next filesystem checkpoint was lost.
            $progress['offset'] = 0; pl_update_checkpoint($directory . '/restore.json', $progress); $interrupted = true;
        }
    }
    if (!$complete || !$interrupted || (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journal_lines') !== 601) { throw new RuntimeException('Resumable database recovery failed.'); }
    if ($original['financial'] !== pl_update_database_financial_digest()) { throw new RuntimeException('Restored financial totals differ.'); }
    try { DB::query('DELETE FROM pl_journal_lines WHERE id=1'); throw new RuntimeException('Immutable trigger missing.'); }
    catch (MeekroDBException $expected) { /* The original guard was restored. */ }
    echo "Database backup/recovery passed: $backupSteps snapshot steps, $restoreSteps recovery steps; 601 binary/generated/default-value rows, original schema, view, immutable trigger, receipts, balanced totals, and replay after a lost checkpoint.\n";
} finally {
    if ($created && preg_match('/^phpledger_update_test_[a-f0-9]{16}$/D', $database)) { DB::query('USE information_schema'); DB::query('DROP DATABASE %b', $database); }
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $file) { if ($file->isDir()) { rmdir($file->getPathname()); } else { unlink($file->getPathname()); } }
    rmdir($directory);
}
