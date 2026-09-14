<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli' || getenv('PL_ENV') !== 'demo' || getenv('PL_DB_HOST') !== 'db_test' || getenv('PL_DB_USER') !== 'ledger_demo_test') {
    exit(2);
}
try {
    require dirname(__DIR__) . '/www/phpledger/includes/bootstrap.php';
} catch (PlDemoUnavailable) {
    echo "busy\n";
    exit(0);
}
fwrite(STDERR, "Expected the other request's maintenance lock to prevent admission.\n");
exit(1);
