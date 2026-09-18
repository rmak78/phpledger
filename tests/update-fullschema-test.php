<?php
declare(strict_types=1);
if (getenv('PL_ENV') !== 'test' || getenv('PL_DB_HOST') !== 'db_test') { fwrite(STDERR, "Use the isolated Docker test service.\n"); exit(2); }
require dirname(__DIR__) . '/vendor/autoload.php';
DB::$host = 'db_test'; DB::$user = 'root'; DB::$password = 'local-test-root-only'; DB::$dbName = 'information_schema'; DB::$encoding = 'utf8mb4';
$database = 'phpledger_update_full_' . bin2hex(random_bytes(8));
$directory = sys_get_temp_dir() . '/' . $database;
mkdir($directory, 0700);
$created = false;
$granted = false;
$grantSchema = str_replace('_', '\\_', $database);
try {
    DB::query('CREATE DATABASE %b CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci', $database); $created = true; DB::query('USE %b', $database);
    DB::query("GRANT ALL PRIVILEGES ON %b.* TO 'ledger_test'@'%%'", $grantSchema); $granted = true;
    DB::disconnect();
    putenv('PL_DB_NAME=' . $database); putenv('PL_DB_USER=ledger_test'); putenv('PL_DB_PASSWORD=local-test-only');
    require dirname(__DIR__) . '/www/phpledger/includes/bootstrap.php';
    require dirname(__DIR__) . '/www/phpledger/includes/functions/install_functions.php';
    require dirname(__DIR__) . '/www/phpledger/includes/functions/update_database_functions.php';
    pl_migrate();
    $actor = pl_create_user('update-review@example.invalid', 'Sample recovery reviewer', 'Sample-update-fixture-password');
    $company = pl_create_company($actor, 'Sample recovery company', 'USD', '2026-01-01', '12-31');
    pl_post_journal($actor, $company['company_id'], $company['book_id'], [
        'date' => '2026-09-18', 'currency' => 'USD', 'source_type' => 'receipt', 'source_reference' => 'sample-recovery-receipt',
        'description' => 'Sample immutable recovery fixture', 'idempotency_key' => 'sample-update-recovery-fixture',
        'lines' => [['account_id' => $company['accounts']['1000'], 'debit' => '123.4567', 'credit' => '0'], ['account_id' => $company['accounts']['4000'], 'debit' => '0', 'credit' => '123.4567']],
    ]);
    $receipt = null; $backupSteps = 0;
    while ($receipt === null && $backupSteps++ < 1000) { $receipt = pl_update_database_backup($directory); }
    if ($receipt === null) { throw new RuntimeException('Full schema backup did not complete.'); }
    $original = pl_update_json($directory . '/manifest.json');
    $preserved = false; $verifySteps = 0;
    while (!$preserved && $verifySteps++ < 1000) { $preserved = pl_update_database_preserved($directory); }
    if (!$preserved) { throw new RuntimeException('Original record comparison did not complete.'); }
    DB::query('CREATE TABLE pl_failed_update (id INT PRIMARY KEY) ENGINE=InnoDB');
    DB::insert('pl_schema_migrations', ['version' => '999_synthetic_failed_update', 'checksum' => str_repeat('b', 64), 'status' => 'applying']);
    $complete = false; $restoreSteps = 0;
    while (!$complete && $restoreSteps++ < 2000) { $complete = pl_update_database_restore($directory, $receipt); }
    if (!$complete) { throw new RuntimeException('Full schema recovery did not complete.'); }
    pl_update_database_ledger_check();
    echo 'Full application database recovery passed under schema-scoped ledger_test grants: ' . count($original['tables']) . ' tables, ' . count($original['views']) . ' views, ' . count($original['triggers']) . " triggers, original immutable posting, all receipts and original-record comparison; $backupSteps snapshot / $verifySteps record verification / $restoreSteps recovery steps.\n";
} finally {
    DB::disconnect(); DB::$user = 'root'; DB::$password = 'local-test-root-only'; DB::$dbName = 'information_schema';
    if ($created && preg_match('/^phpledger_update_full_[a-f0-9]{16}$/D', $database)) {
        if ($granted) { DB::query("REVOKE ALL PRIVILEGES ON %b.* FROM 'ledger_test'@'%%'", $grantSchema); }
        DB::query('DROP DATABASE %b', $database);
    }
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $file) { if ($file->isDir()) { rmdir($file->getPathname()); } else { unlink($file->getPathname()); } }
    rmdir($directory);
}
