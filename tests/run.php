<?php
declare(strict_types=1);
ob_start();

if (getenv('PL_ENV') !== 'test' || getenv('PL_DB_NAME') !== 'phpledger_test') {
    fwrite(STDERR, "Tests require PL_ENV=test and the isolated phpledger_test database.\n");
    exit(2);
}
require_once dirname(__DIR__) . '/www/phpledger/includes/bootstrap.php';
require_once dirname(__DIR__) . '/www/phpledger/install/migrate.php';
pl_migrate();

$results = [];
function test(string $name, callable $action): void
{
    global $results;
    try {
        $action();
        $results[] = ['name' => $name, 'passed' => true];
    } catch (Throwable $error) {
        $results[] = ['name' => $name, 'passed' => false, 'error' => $error->getMessage()];
    }
}
function assert_true(bool $actual, string $message = ''): void
{
    if (!$actual) {
        throw new RuntimeException($message ?: 'Expected condition to be true.');
    }
}
function assert_same(mixed $expected, mixed $actual, string $message = ''): void
{
    if ($expected !== $actual) {
        throw new RuntimeException(($message ?: 'Values differ.') . ' Expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
}
function assert_throws(callable $action, string $class = Throwable::class, ?string $messageContains = null): void
{
    try {
        $action();
    } catch (Throwable $error) {
        if (!$error instanceof $class || ($messageContains !== null && !str_contains($error->getMessage(), $messageContains))) {
            throw new RuntimeException('Unexpected exception: ' . get_class($error) . ': ' . $error->getMessage());
        }
        return;
    }
    throw new RuntimeException('Expected exception was not thrown.');
}

$suites = ['auth_test.php', 'ledger_test.php', 'concurrency_test.php', 'document_test.php', 'regional_test.php', 'report_test.php', 'pos_test.php', 'core_test.php', 'opening_test.php', 'period_test.php', 'reconciliation_test.php', 'core_completion_test.php', 'module_test.php', 'installer_test.php', 'connection_test.php', 'demo_pack_test.php'];
if (($argv[1] ?? '') === '--suite=installer') {
    $suites = ['installer_test.php'];
}
if (($argv[1] ?? '') === '--suite=connections') {
    $suites = ['ledger_test.php', 'connection_test.php'];
}
if (($argv[1] ?? '') === '--suite=demo-packs') {
    $suites = ['ledger_test.php', 'demo_pack_test.php'];
}
foreach ($suites as $suite) {
    if (is_file(__DIR__ . '/' . $suite)) {
        require __DIR__ . '/' . $suite;
    }
}
// Keep all output until session tests are complete; printing earlier would prevent session rotation.
foreach ($results as $result) {
    echo ($result['passed'] ? 'PASS ' : 'FAIL ') . $result['name'];
    if (!$result['passed']) {
        echo ': ' . $result['error'];
    }
    echo "\n";
}
$failed = count(array_filter($results, static fn (array $result): bool => !$result['passed']));
echo count($results) . " tests, {$failed} failures.\n";
exit($failed === 0 && count($results) > 0 ? 0 : 1);
