<?php
declare(strict_types=1);

/** @return array{applied: list<string>, skipped: list<string>} */
function pl_migrate(): array
{
    if (PHP_SAPI !== 'cli') {
        throw new DomainException('Migrations are available only from the command line.');
    }
    $lock = 'phpledger:migrate:' . substr(hash('sha256', (string) DB::queryFirstField('SELECT DATABASE()')), 0, 40);
    if ((int) DB::queryFirstField('SELECT GET_LOCK(%s, 10)', $lock) !== 1) {
        throw new DomainException('Another installer is running. Try again after it finishes.');
    }
    try {
        DB::query("CREATE TABLE IF NOT EXISTS pl_schema_migrations (
            version VARCHAR(100) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
            checksum CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
            status ENUM('applying','applied') NOT NULL,
            applied_at DATETIME NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");
        $files = glob(__DIR__ . '/migrations/[0-9]*.php') ?: [];
        sort($files, SORT_STRING);
        $result = ['applied' => [], 'skipped' => []];
        $known = [];
        foreach ($files as $file) {
            $known[basename($file, '.php')] = true;
        }
        foreach (DB::query('SELECT version FROM pl_schema_migrations') as $receipt) {
            if (!isset($known[$receipt['version']])) {
                throw new DomainException('This code is missing an installed migration. Use a compatible application release.');
            }
        }
        foreach ($files as $file) {
            $version = basename($file, '.php');
            $checksum = hash_file('sha256', $file);
            if ($checksum === false) {
                throw new DomainException('Cannot read migration: ' . $version);
            }
            $receipt = DB::queryFirstRow('SELECT * FROM pl_schema_migrations WHERE version = %s', $version);
            if ($receipt) {
                if (!hash_equals((string) $receipt['checksum'], (string) $checksum)) {
                    throw new DomainException('Migration checksum mismatch: ' . $version . '. Restore the original migration before continuing.');
                }
                if ($receipt['status'] !== 'applied') {
                    throw new DomainException('An earlier migration stopped: ' . $version . '. Review the database before resuming; MySQL schema changes cannot be rolled back automatically.');
                }
                $result['skipped'][] = $version;
                continue;
            }
            $statements = require $file;
            if (!is_array($statements) || $statements === []) {
                throw new DomainException('Invalid migration definition: ' . $version);
            }
            DB::insert('pl_schema_migrations', ['version' => $version, 'checksum' => $checksum, 'status' => 'applying']);
            foreach ($statements as $statement) {
                if (!is_string($statement) || trim($statement) === '') {
                    throw new DomainException('Invalid SQL statement in migration: ' . $version);
                }
                DB::query($statement);
            }
            DB::update('pl_schema_migrations', ['status' => 'applied', 'applied_at' => gmdate('Y-m-d H:i:s')], 'version = %s', $version);
            $result['applied'][] = $version;
        }
        return $result;
    } finally {
        DB::queryFirstField('SELECT RELEASE_LOCK(%s)', $lock);
    }
}

if (PHP_SAPI === 'cli' && realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    try {
        require_once __DIR__ . '/preflight.php';
        pl_install_require_runtime();
        try {
            require dirname(__DIR__) . '/includes/bootstrap.php';
        } catch (Throwable $error) {
            throw new RuntimeException('Application configuration could not be loaded.');
        }
        pl_install_database_check();
        $result = pl_migrate();
        fwrite(STDOUT, 'Migrations applied: ' . count($result['applied']) . '; already current: ' . count($result['skipped']) . PHP_EOL);
    } catch (Throwable $error) {
        fwrite(STDERR, ($error instanceof DomainException ? $error->getMessage() : 'Migration failed. Check the local database configuration and migration receipts.') . PHP_EOL);
        exit(1);
    }
}
