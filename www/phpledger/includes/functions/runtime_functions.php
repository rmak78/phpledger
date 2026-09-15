<?php
declare(strict_types=1);

function pl_require_runtime(int $version): void
{
    if ($version < 80200) {
        throw new RuntimeException('PHP Ledger requires PHP 8.2 or newer; PHP 8.3 is recommended.');
    }
}
