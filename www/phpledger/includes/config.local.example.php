<?php
declare(strict_types=1);
// Optional customer-hosting configuration. Copy to config.local.php, outside public/.
return [
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'phpledger',
    'user' => 'phpledger',
    'password' => '', // Set a unique password. Never commit config.local.php.
];
