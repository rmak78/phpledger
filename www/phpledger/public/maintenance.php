<?php
declare(strict_types=1);

// This entrypoint must not load application bootstrap, its database, or templates.
// A running operation uses its saved recovery runtime even if new application code fails.
$root = dirname(__DIR__, 3);
$configurationPath = getenv('PL_INSTALL_CONFIG_PATH') ?: dirname(__DIR__) . '/includes/config.local.php';
try { $configuration = is_file($configurationPath) ? require $configurationPath : []; }
catch (Throwable $error) { http_response_code(503); exit('Private installation configuration requires host review.'); }
if (!is_array($configuration)) { http_response_code(503); exit('Private installation configuration requires host review.'); }
$directory = getenv('PL_INSTALL_DIRECTORY') ?: ($configuration['installation_directory'] ?? dirname(__DIR__) . '/storage/installation');
$runtime = dirname(__DIR__) . '/includes/functions';
$active = $directory . '/updates/active.json';
if (is_file($active)) {
    $state = json_decode((string) file_get_contents($active), true);
    $id = is_array($state) ? ($state['id'] ?? '') : '';
    if (!is_string($id) || !preg_match('/^[a-f0-9]{32}$/D', $id)) { http_response_code(503); exit('Installation recovery state requires host review.'); }
    $runtime = $directory . '/updates/' . $id . '/runtime';
    $operation = $directory . '/updates/' . $id;
    $operationState = json_decode((string) file_get_contents($operation . '/state.json'), true);
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['action'] ?? '') === 'continue'
        && is_array($operationState) && ($operationState['phase'] ?? '') === 'runtime' && empty($operationState['inflight'])) {
        require_once $runtime . '/update_probe_functions.php';
        pl_update_runtime_probe($root, $directory, $operation);
    }
}
try {
    require_once $runtime . '/update_functions.php';
    require_once $runtime . '/update_database_functions.php';
    require_once $runtime . '/update_web_functions.php';
    pl_update_web($root);
} catch (Throwable $error) {
    http_response_code(503);
    header('Cache-Control: no-store');
    echo 'Installation recovery is unavailable. Restore the private recovery runtime from the hosting backup; keep application access in maintenance.';
}
