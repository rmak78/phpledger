<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') { exit(1); }
require_once dirname(__DIR__) . '/www/phpledger/includes/bootstrap.php';
require_once PL_APP . '/includes/functions/integration_http_functions.php';
echo json_encode(pl_read_openapi(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n";
