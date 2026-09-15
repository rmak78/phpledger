<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require_once dirname(__DIR__) . '/includes/functions/runtime_functions.php';

/** Report prerequisites before loading configuration or attempting a database connection. */
function pl_install_runtime_issues(int $version, array $extensions, bool $autoloadExists): array
{
    $issues = [];
    try {
        pl_require_runtime($version);
    } catch (RuntimeException $error) {
        $issues[] = $error->getMessage();
    }
    $extensions = array_map('strtolower', $extensions);
    foreach (['bcmath', 'mbstring', 'pdo', 'pdo_mysql', 'session'] as $extension) {
        if (!in_array($extension, $extensions, true)) {
            $issues[] = 'Enable the PHP ' . $extension . ' extension.';
        }
    }
    if (!$autoloadExists) {
        $issues[] = 'Composer dependencies are missing. Run composer install from the package root.';
    }
    return $issues;
}

function pl_install_require_runtime(): void
{
    $issues = pl_install_runtime_issues(PHP_VERSION_ID, get_loaded_extensions(), is_file(dirname(__DIR__, 3) . '/vendor/autoload.php'));
    if ($issues !== []) {
        throw new DomainException(implode(' ', $issues));
    }
}

/** This checks the CLI identity only; it does not start a session or create a probe file. */
function pl_install_session_check(string $handler, string $path): array
{
    if ($handler !== 'files') {
        return ['status' => 'warning', 'message' => 'Custom session handler cannot be verified here. Verify its configuration and access in the web runtime.'];
    }
    $parts = explode(';', $path);
    $directory = end($parts) ?: sys_get_temp_dir();
    if (!is_dir($directory) || !is_writable($directory)) {
        return ['status' => 'error', 'message' => 'The file-session directory is missing or not writable by this CLI user. Configure session.save_path and verify the web user has access.'];
    }
    if (count($parts) > 1 && (int) $parts[0] > 0) {
        return ['status' => 'warning', 'message' => 'The session root is writable, but its configured subdirectory layout must be verified in the web runtime.'];
    }
    return ['status' => 'ok', 'message' => 'File-session directory is writable by this CLI user. Confirm the web user can also use it.'];
}

/** Inspect versioned receipts without issuing migrations or changing a receipt. */
function pl_install_schema_state(?array $receipts, array $checksums, int $tableCount): array
{
    if ($receipts === null) {
        if ($tableCount !== 0) {
            throw new DomainException('The database has tables without PHP Ledger migration receipts. Use a separate empty database; do not import legacy SQL.');
        }
        return ['status' => 'empty', 'applied' => 0, 'pending' => count($checksums)];
    }
    $seen = [];
    foreach ($receipts as $receipt) {
        $version = $receipt['version'];
        if (!isset($checksums[$version])) {
            throw new DomainException('The database contains an unknown migration. Use a compatible package; do not change its receipts.');
        }
        if ($receipt['status'] !== 'applied') {
            throw new DomainException('A previous migration is incomplete. Inspect or restore the database before retrying installation.');
        }
        if (!hash_equals($checksums[$version], (string) $receipt['checksum'])) {
            throw new DomainException('A migration checksum differs from this package. Restore the original file; do not edit the receipt.');
        }
        $seen[$version] = true;
    }
    $pending = count(array_diff_key($checksums, $seen));
    return ['status' => $pending === 0 ? 'current' : 'pending', 'applied' => count($seen), 'pending' => $pending];
}

function pl_install_database_check(): array
{
    if (!preg_match('/^8\.4\.\d+(?:\D|$)/D', (string) DB::queryFirstField('SELECT VERSION()'))) {
        throw new DomainException('This package supports MySQL 8.4 LTS. Check the selected database server before installing.');
    }
    $checksums = [];
    foreach (glob(__DIR__ . '/migrations/[0-9]*.php') ?: [] as $file) {
        $checksum = hash_file('sha256', $file);
        if ($checksum === false) {
            throw new DomainException('A migration file could not be read. Verify the package files.');
        }
        $checksums[basename($file, '.php')] = $checksum;
    }
    if ($checksums === []) {
        throw new DomainException('Migration files are missing from the package.');
    }
    $tables = DB::queryFirstColumn('SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = DATABASE()');
    $receipts = in_array('pl_schema_migrations', $tables, true)
        ? DB::query('SELECT version, checksum, status FROM pl_schema_migrations') : null;
    return pl_install_schema_state($receipts, $checksums, count($tables));
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    try {
        pl_install_require_runtime();
        fwrite(STDOUT, 'Runtime: PHP ' . PHP_VERSION . " (minimum 8.2), required extensions and Composer dependencies are available.\n");
        $session = pl_install_session_check((string) ini_get('session.save_handler'), (string) ini_get('session.save_path'));
        fwrite(STDOUT, 'Session ' . $session['status'] . ': ' . $session['message'] . "\n");
        if ($session['status'] === 'error') {
            exit(1);
        }
        try {
            require dirname(__DIR__) . '/includes/bootstrap.php';
        } catch (Throwable $error) {
            // A local config file can throw arbitrary exception text; never print it.
            throw new RuntimeException('Application configuration could not be loaded.');
        }
        $state = pl_install_database_check();
        fwrite(STDOUT, "Database: MySQL 8.4 connection available. No schema or account writes were performed.\n");
        fwrite(STDOUT, $state['status'] === 'current'
            ? "Database schema is current; all migration checksums match.\n"
            : "Ready to run migrations: {$state['pending']} pending. Back up an existing installation first.\n");
    } catch (DomainException $error) {
        fwrite(STDERR, $error->getMessage() . "\n");
        exit(1);
    } catch (Throwable $error) {
        fwrite(STDERR, "Database/configuration check failed. Check config.local.php or PL_DB_* settings, server access and credentials. No configuration values are printed.\n");
        exit(1);
    }
}
