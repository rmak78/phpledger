<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
try {
    require dirname(__DIR__) . '/www/phpledger/includes/bootstrap.php';
    // Foundation only. A reviewed connector must explicitly register its scoped transport.
    // Do not read destinations, credentials or handlers from arbitrary command-line input.
    $result = pl_dispatch_outbound_events([]);
    fwrite(STDOUT, json_encode($result, JSON_THROW_ON_ERROR) . PHP_EOL);
} catch (Throwable) {
    fwrite(STDERR, "Outbound dispatch failed; review local configuration and queue state.\n");
    exit(1);
}
