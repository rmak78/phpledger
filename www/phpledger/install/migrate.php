<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require_once __DIR__ . '/preflight.php';

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
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
