<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/www/phpledger/install/preflight.php';

function installer_process(string $script, array $arguments = [], string $input = '', array $overrides = []): array
{
    $environment = getenv();
    if (!is_array($environment)) {
        throw new RuntimeException('Process environment unavailable.');
    }
    $process = proc_open([PHP_BINARY, $script, ...$arguments], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, null, array_replace($environment, $overrides));
    if (!is_resource($process)) {
        throw new RuntimeException('Installer process unavailable.');
    }
    fwrite($pipes[0], $input);
    fclose($pipes[0]);
    $output = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    return ['status' => proc_close($process), 'output' => $output];
}

test('installer prerequisites identify missing runtime, extensions and dependencies', function (): void {
    assert_same([], pl_install_runtime_issues(80510, ['bcmath', 'mbstring', 'PDO', 'pdo_mysql', 'session'], true));
    $issues = pl_install_runtime_issues(80400, [], false);
    assert_same(7, count($issues));
    assert_true(str_contains(implode(' ', $issues), 'PHP 8.5'));
    assert_true(str_contains(implode(' ', $issues), 'Composer dependencies'));
    assert_throws(fn () => pl_require_runtime(80600), RuntimeException::class, 'PHP 8.5');
    assert_same('error', pl_install_session_check('files', sys_get_temp_dir() . '/missing-session-' . bin2hex(random_bytes(4)))['status']);
    assert_same('warning', pl_install_session_check('redis', 'unverified-custom-handler')['status']);
    assert_same('ok', pl_install_session_check('files', sys_get_temp_dir())['status']);
});

test('installer schema inspection distinguishes fresh, pending and unsafe receipts', function (): void {
    $hash = str_repeat('a', 64);
    $known = ['001_fixture' => $hash, '002_fixture' => $hash];
    $receipt = ['version' => '001_fixture', 'checksum' => $hash, 'status' => 'applied'];
    assert_same(['status' => 'empty', 'applied' => 0, 'pending' => 2], pl_install_schema_state(null, $known, 0));
    assert_same('pending', pl_install_schema_state([$receipt], $known, 1)['status']);
    assert_same('current', pl_install_schema_state([$receipt], ['001_fixture' => $hash], 1)['status']);
    assert_throws(fn () => pl_install_schema_state(null, $known, 1), DomainException::class, 'separate empty database');
    assert_throws(fn () => pl_install_schema_state([$receipt], [], 1), DomainException::class, 'unknown migration');
    assert_throws(fn () => pl_install_schema_state([array_replace($receipt, ['status' => 'applying'])], $known, 1), DomainException::class, 'incomplete');
    assert_throws(fn () => pl_install_schema_state([array_replace($receipt, ['checksum' => str_repeat('b', 64)])], $known, 1), DomainException::class, 'checksum');
});

test('preflight and migration replay preserve receipts and existing data', function (): void {
    $before = DB::query('SELECT * FROM pl_schema_migrations ORDER BY version');
    $users = (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_users');
    $journals = (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journals');
    foreach (['preflight.php', 'migrate.php'] as $name) {
        $result = installer_process(dirname(__DIR__) . '/www/phpledger/install/' . $name);
        assert_same(0, $result['status'], $result['output']);
        assert_true(str_contains($result['output'], $name === 'preflight.php' ? 'schema is current' : 'Migrations applied: 0; already current: ' . count($before)));
    }
    assert_same($before, DB::query('SELECT * FROM pl_schema_migrations ORDER BY version'));
    assert_same($users, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_users'));
    assert_same($journals, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journals'));
});

test('installer connection failures do not expose credentials or stack traces', function (): void {
    $secret = 'Synthetic-private-check-' . bin2hex(random_bytes(12));
    foreach (['preflight.php', 'migrate.php', 'create-admin.php'] as $name) {
        $args = $name === 'create-admin.php' ? ['--email=wrong-db@example.invalid', '--name=Fixture', '--password-stdin'] : [];
        $result = installer_process(dirname(__DIR__) . '/www/phpledger/install/' . $name, $args, 'Synthetic account passphrase 427!' . "\n", ['PL_DB_PASSWORD' => $secret]);
        assert_same(1, $result['status']);
        assert_true(!str_contains($result['output'], $secret));
        assert_true(!str_contains($result['output'], 'Stack trace'));
        assert_true(!str_contains($result['output'], 'PDOException'));
    }
});

test('missing autoload and invalid local configuration produce safe installer failures', function (): void {
    $root = sys_get_temp_dir() . '/phpledger-installer-' . bin2hex(random_bytes(8));
    $install = $root . '/www/phpledger/install';
    $includes = $root . '/www/phpledger/includes';
    mkdir($install, 0700, true);
    mkdir($includes . '/functions', 0700, true);
    mkdir($root . '/vendor', 0700);
    $files = [
        $install . '/preflight.php' => dirname(__DIR__) . '/www/phpledger/install/preflight.php',
        $includes . '/bootstrap.php' => dirname(__DIR__) . '/www/phpledger/includes/bootstrap.php',
        $includes . '/functions/runtime_functions.php' => dirname(__DIR__) . '/www/phpledger/includes/functions/runtime_functions.php',
    ];
    foreach ($files as $target => $source) {
        copy($source, $target);
    }
    try {
        $missing = installer_process($install . '/preflight.php');
        assert_same(1, $missing['status']);
        assert_true(str_contains($missing['output'], 'Composer dependencies are missing'));
        assert_true(!str_contains($missing['output'], 'Warning:'));
        file_put_contents($root . '/vendor/autoload.php', '<?php require ' . var_export(dirname(__DIR__) . '/vendor/autoload.php', true) . ';');
        $secret = 'Synthetic-invalid-config-' . bin2hex(random_bytes(8));
        file_put_contents($includes . '/config.local.php', '<?php return ' . var_export($secret, true) . ';');
        $invalid = installer_process($install . '/preflight.php');
        assert_same(1, $invalid['status']);
        assert_true(str_contains($invalid['output'], 'Database/configuration check failed'));
        assert_true(!str_contains($invalid['output'], $secret));
        assert_true(!str_contains($invalid['output'], 'Stack trace'));
        file_put_contents($includes . '/config.local.php', '<?php throw new DomainException(' . var_export($secret, true) . ');');
        $thrown = installer_process($install . '/preflight.php');
        assert_same(1, $thrown['status']);
        assert_true(!str_contains($thrown['output'], $secret));
    } finally {
        foreach ([...array_keys($files), $root . '/vendor/autoload.php', $includes . '/config.local.php'] as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        foreach ([$install, $includes . '/functions', $includes, $root . '/www/phpledger', $root . '/www', $root . '/vendor', $root] as $directory) {
            rmdir($directory);
        }
    }
});

test('repeating administrator creation preserves the original account and password', function (): void {
    $email = 'installer-' . bin2hex(random_bytes(8)) . '@example.invalid';
    $script = dirname(__DIR__) . '/www/phpledger/install/create-admin.php';
    $args = ['--email=' . $email, '--name=Original installer owner', '--password-stdin'];
    $password = 'Original synthetic passphrase 481!';
    $created = installer_process($script, $args, $password . "\n");
    assert_same(0, $created['status'], $created['output']);
    $before = DB::queryFirstRow('SELECT * FROM pl_users WHERE email=%s', $email);
    assert_true(is_array($before));
    $replayed = installer_process($script, $args, "Replacement synthetic passphrase 482!\n");
    assert_same(1, $replayed['status']);
    assert_same($before, DB::queryFirstRow('SELECT * FROM pl_users WHERE email=%s', $email));
    assert_true(pl_verify_password($password, $before['password_hash']));
    assert_true(!pl_verify_password('Replacement synthetic passphrase 482!', $before['password_hash']));
});
