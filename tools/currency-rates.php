<?php
declare(strict_types=1);

// Local operator command: a scoped manual rate entry, never a provider fetch.
if (PHP_SAPI !== 'cli') { exit(1); }
try {
    if (count($argv) !== 5 || !ctype_digit($argv[1]) || !ctype_digit($argv[2]) || !ctype_digit($argv[3])) {
        throw new DomainException('Usage: php tools/currency-rates.php ACTOR_ID COMPANY_ID BOOK_ID INPUT.json');
    }
    $contents = file_get_contents($argv[4]);
    if ($contents === false) { throw new DomainException('Cannot read rate input.'); }
    $input = json_decode($contents, true, 64, JSON_THROW_ON_ERROR);
    if (!is_array($input)) { throw new DomainException('Rate input must be a JSON object.'); }
    require dirname(__DIR__) . '/www/phpledger/includes/bootstrap.php';
    $row = pl_currency_rate_enter((int) $argv[1], (int) $argv[2], (int) $argv[3], $input);
    fwrite(STDOUT, 'Manual rate recorded: ' . $row['id'] . '; revision ' . $row['revision'] . PHP_EOL);
} catch (Throwable $error) {
    fwrite(STDERR, ($error instanceof DomainException ? $error->getMessage() : 'Rate entry failed; inspect the local input and database configuration.') . PHP_EOL);
    exit(1);
}
