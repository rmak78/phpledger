<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$root = dirname(__DIR__);
$configurationPath = getenv('PL_INSTALL_CONFIG_PATH') ?: $root . '/www/phpledger/includes/config.local.php';
try { $configuration = is_file($configurationPath) ? require $configurationPath : []; }
catch (Throwable $error) { fwrite(STDERR, "Private installation configuration requires host review.\n"); exit(1); }
$directory = getenv('PL_INSTALL_DIRECTORY') ?: ($configuration['installation_directory'] ?? $root . '/www/phpledger/storage/installation');
$active = $directory . '/updates/active.json';
if (!is_file($active)) { fwrite(STDOUT, "No installation update requires continuation.\n"); exit; }
try {
    $pointer = json_decode((string) file_get_contents($active), true, 8, JSON_THROW_ON_ERROR);
    $id = $pointer['id'] ?? '';
    if (!is_string($id) || !preg_match('/^[a-f0-9]{32}$/D', $id)) { throw new RuntimeException('Invalid operation pointer.'); }
    $runtime = $directory . '/updates/' . $id . '/runtime';
    require_once $runtime . '/update_functions.php';
    require_once $runtime . '/update_database_functions.php';
    // Local filesystem access to this host-controlled private key is CLI operator proof.
    pl_update_operator($directory, (string) file_get_contents($directory . '/operator.key'));
    $limit = in_array('--drain', $argv, true) ? 2000 : 1;
    for ($step = 0; $step < $limit; $step++) {
        $state = pl_update_step($root);
        if ($state['phase'] === 'runtime') { fwrite(STDOUT, "Database and files verified. Complete the fresh browser runtime check through installation maintenance before reopening.\n"); exit(3); }
        if ($state['error'] === 'recovery_pending') { fwrite(STDERR, "Recovery is pending; keep maintenance active and retry when host dependencies recover.\n"); exit(1); }
        if (in_array($state['phase'], ['complete', 'restored', 'aborted'], true)) { break; }
    }
    fwrite(STDOUT, 'Installation state: ' . $state['phase'] . ".\n");
    exit(in_array($state['phase'], ['complete', 'restored', 'aborted'], true) ? 0 : 3);
} catch (Throwable $error) {
    fwrite(STDERR, "Installation recovery could not continue. Private storage and database access require host review; maintenance remains active.\n"); exit(1);
}
