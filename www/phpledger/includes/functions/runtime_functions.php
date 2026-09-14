<?php
declare(strict_types=1);

function pl_require_runtime(int $version): void
{
    if ($version < 80500 || $version >= 80600) {
        throw new RuntimeException('PHP Ledger foundation requires PHP 8.5.');
    }
}
