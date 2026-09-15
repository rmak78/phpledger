<?php
declare(strict_types=1);

const PL_ROOT = __DIR__ . '/../../..';
const PL_APP = __DIR__ . '/..';

require_once __DIR__ . '/functions/runtime_functions.php';
pl_require_runtime(PHP_VERSION_ID);
require_once PL_ROOT . '/vendor/autoload.php';
date_default_timezone_set('UTC');

$plEnvironment = getenv('PL_ENV') ?: 'production';
$plConfig = [
    'host' => getenv('PL_DB_HOST') ?: '127.0.0.1',
    'port' => (int) (getenv('PL_DB_PORT') ?: 3306),
    'database' => getenv('PL_DB_NAME') ?: 'phpledger',
    'user' => getenv('PL_DB_USER') ?: 'phpledger',
    'password' => getenv('PL_DB_PASSWORD') ?: '',
];
$plLocalConfig = __DIR__ . '/config.local.php';
if (is_file($plLocalConfig)) {
    $plOverride = require $plLocalConfig;
    if (!is_array($plOverride)) {
        throw new RuntimeException('Invalid local configuration.');
    }
    $plConfig = array_replace($plConfig, $plOverride);
}
if ($plConfig['password'] === '') {
    throw new RuntimeException('A database password must be configured.');
}
require_once __DIR__ . '/functions/demo_functions.php';
pl_demo_validate_configuration($plConfig);
DB::$host = $plConfig['host'];
DB::$port = $plConfig['port'];
DB::$dbName = pl_demo_reset_process() ? 'information_schema' : $plConfig['database'];
DB::$user = $plConfig['user'];
DB::$password = $plConfig['password'];
DB::$encoding = 'utf8mb4';
DB::$nested_transactions = true;
// Persist DATETIME/TIMESTAMP events in UTC; business accounting DATE values stay unchanged.
DB::query("SET time_zone = '+00:00'");
pl_demo_acquire_maintenance_lock();

require_once __DIR__ . '/functions/security_functions.php';
require_once __DIR__ . '/functions/auth_functions.php';
require_once __DIR__ . '/functions/currency_functions.php';
require_once __DIR__ . '/functions/ledger_functions.php';
require_once __DIR__ . '/functions/setup_functions.php';
require_once __DIR__ . '/functions/document_functions.php';
require_once __DIR__ . '/functions/core_functions.php';
require_once __DIR__ . '/functions/regional_functions.php';
require_once __DIR__ . '/functions/report_functions.php';
require_once __DIR__ . '/functions/export_functions.php';
require_once __DIR__ . '/functions/pos_functions.php';
require_once __DIR__ . '/functions/opening_functions.php';
require_once __DIR__ . '/functions/period_functions.php';
require_once __DIR__ . '/functions/reconciliation_functions.php';
require_once __DIR__ . '/functions/module_functions.php';
require_once __DIR__ . '/functions/connection_functions.php';
require_once __DIR__ . '/functions/read_functions.php';
require_once __DIR__ . '/functions/demo_pack_functions.php';
