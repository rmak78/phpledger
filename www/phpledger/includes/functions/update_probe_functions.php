<?php
declare(strict_types=1);

/** Match installer HTTPS semantics using trusted server variables only. */
function pl_update_secure_request(array $server): bool
{
    return (!empty($server['HTTPS']) && strtolower((string) $server['HTTPS']) !== 'off') || (int) ($server['SERVER_PORT'] ?? 0) === 443;
}

/** Runs in a fresh HTTP request before loading any saved application helper/vendor. */
function pl_update_runtime_probe(string $root, string $directory, string $operation): never
{
    header('Cache-Control: no-store'); header('Referrer-Policy: no-referrer');
    $local = in_array(getenv('PL_ENV'), ['local', 'test'], true) && in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true);
    if (!$local && !pl_update_secure_request($_SERVER)) { http_response_code(403); exit('Installation operations require HTTPS.'); }
    ini_set('session.use_strict_mode', '1'); ini_set('session.use_only_cookies', '1');
    session_name('phpledger_install_operator');
    session_set_cookie_params(['httponly' => true, 'secure' => !$local, 'samesite' => 'Strict', 'path' => '/']); session_start();
    $fingerprint = is_file($directory . '/operator.key') ? hash_file('sha256', $directory . '/operator.key') : '';
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || $fingerprint === '' || !isset($_SESSION['update_operator'], $_SESSION['update_until'], $_SESSION['update_csrf'])
        || !hash_equals($fingerprint, $_SESSION['update_operator']) || $_SESSION['update_until'] <= time()
        || !is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['update_csrf'], $_POST['csrf'])) {
        http_response_code(403); exit('Authenticate again through installation maintenance.');
    }
    session_write_close(); ignore_user_abort(true);
    ini_set('display_errors', '0');
    $control = fopen($directory . '/update.lock', 'c+b'); $barrier = fopen($directory . '/application.lock', 'c+b');
    if (!$control || !$barrier || !flock($control, LOCK_EX | LOCK_NB) || !flock($barrier, LOCK_EX | LOCK_NB)) { http_response_code(409); exit('Another installation request is still finishing. Retry maintenance.'); }
    $write = static function (string $path, array $value): void {
        $temporary = $path . '.' . bin2hex(random_bytes(8)); $bytes = json_encode($value, JSON_THROW_ON_ERROR);
        $handle = fopen($temporary, 'xb');
        if (!$handle) { throw new RuntimeException('Checkpoint unavailable.'); }
        try {
            chmod($temporary, 0600);
            if (fwrite($handle, $bytes) !== strlen($bytes) || !fflush($handle) || (function_exists('fsync') && !fsync($handle))) { throw new RuntimeException('Checkpoint unavailable.'); }
        } finally { fclose($handle); }
        if (!rename($temporary, $path)) { throw new RuntimeException('Checkpoint unavailable.'); }
    };
    try {
        $state = json_decode((string) file_get_contents($operation . '/state.json'), true, 32, JSON_THROW_ON_ERROR);
        if ($state['phase'] !== 'runtime' || $state['inflight']) { throw new RuntimeException('Probe state changed.'); }
        $state['inflight'] = true; $write($operation . '/state.json', $state);
        $GLOBALS['pl_update_probe_barrier'] = $barrier;
        define('PL_UPDATE_INTERNAL_PROBE', true);
        ob_start();
        try {
            require $root . '/www/phpledger/includes/bootstrap.php';
            if (!class_exists('DB', false) || (int) DB::queryFirstField('SELECT 1') !== 1) { throw new RuntimeException('Updated bootstrap is unavailable.'); }
            $state['phase'] = 'complete'; $state['error'] = null;
        } catch (Throwable $error) { $state['phase'] = 'recovering'; $state['error'] = 'runtime_probe_failed'; }
        finally { ob_end_clean(); }
        $state['inflight'] = false; $state['updated_at'] = gmdate('c'); $write($operation . '/state.json', $state);
        if ($state['phase'] === 'complete') {
            $write($directory . '/updates/last.json', $state);
            if (!unlink($directory . '/updates/active.json')) { throw new RuntimeException('Maintenance release failed.'); }
        }
    } catch (Throwable $failure) {
        http_response_code(503); echo 'Runtime verification could not finish. Reopen installation maintenance to recover safely.'; exit;
    } finally { flock($barrier, LOCK_UN); fclose($barrier); flock($control, LOCK_UN); fclose($control); }
    header('Location: maintenance.php', true, 303); exit;
}
