<?php
declare(strict_types=1);

require_once __DIR__ . '/installation_state_functions.php';
require_once __DIR__ . '/install_functions.php';
require_once __DIR__ . '/install_oauth_functions.php';
require_once __DIR__ . '/security_functions.php';
require_once __DIR__ . '/demo_functions.php';

function pl_install_setup_key(): ?string
{
    if (getenv('PL_ENV') === 'demo' || is_file(pl_install_directory() . '/installed.json')) {
        return null;
    }
    $key = getenv('PL_SETUP_KEY');
    if ($key === false || $key === '') {
        $path = pl_install_directory() . '/setup.key';
        $key = is_file($path) && filesize($path) <= 256 ? file_get_contents($path) : false;
    }
    if (!is_string($key)) {
        return null;
    }
    $key = trim($key);
    return strlen($key) >= 32 && strlen($key) <= 256 && !preg_match('/[\x00-\x20\x7f]/', $key) ? $key : null;
}

/** A setup-key proof is installation scoped and expires independently of application sign-in. */
function pl_install_authorized(string $key, array $session, int $now): bool
{
    $proof = $session['install_proof'] ?? null;
    return is_array($proof) && is_string($proof['key_hash'] ?? null) && is_int($proof['at'] ?? null)
        && $proof['at'] <= $now && $now - $proof['at'] < 1800
        && hash_equals(hash('sha256', $key), $proof['key_hash']);
}

/** The caller holds install.lock; rate limits survive cookie deletion. */
function pl_install_verify_key(string $expected, string $provided, int $now): void
{
    $attempts = pl_install_read_state('attempts.json');
    $started = (int) ($attempts['started'] ?? 0);
    $count = $started <= $now && $now - $started < 900 ? (int) ($attempts['count'] ?? 0) : 0;
    if ($count >= 10) {
        throw new DomainException('Too many setup attempts. Wait 15 minutes before trying again.');
    }
    if (!hash_equals($expected, $provided)) {
        pl_install_save_state(['started' => $count === 0 ? $now : $started, 'count' => $count + 1], 'attempts.json');
        throw new DomainException('The private setup key was not accepted.');
    }
    pl_install_save_state(['started' => $now, 'count' => 0], 'attempts.json');
}

/** @return array{host:string,port:int,database:string,user:string,password:string} */
function pl_install_database_input(array $input, string $prefix = ''): array
{
    $get = static function (string $name) use ($input, $prefix): string {
        $value = $input[$prefix . $name] ?? '';
        return is_string($value) ? $value : '';
    };
    $host = trim($get('host'));
    $database = trim($get('database'));
    $user = trim($get('user'));
    $password = $get('password');
    $port = $get('port');
    if ($host === '' || strlen($host) > 253 || preg_match('/[\x00-\x20;]/', $host)
        || !preg_match('/^[A-Za-z0-9_][A-Za-z0-9_$-]{0,63}$/D', $database)
        || $user === '' || strlen($user) > 128 || preg_match('/[\x00-\x1f]/', $user)
        || !ctype_digit($port) || (int) $port < 1 || (int) $port > 65535
        || $password === '' || strlen($password) > 1024 || str_contains($password, "\0")) {
        throw new InvalidArgumentException('Enter the database host, port, dedicated database name, user and password from your hosting panel.');
    }
    if (strtolower($user) === 'root') {
        throw new InvalidArgumentException('Use a dedicated database account, not the MySQL root account.');
    }
    return ['host' => $host, 'port' => (int) $port, 'database' => $database, 'user' => $user, 'password' => $password];
}

function pl_install_database_identity(array $config): string
{
    return hash('sha256', json_encode([$config['host'], (int) $config['port'], $config['database']], JSON_THROW_ON_ERROR));
}

/** Configure the existing single MeekroDB connection; no parallel database layer. */
function pl_install_connect(array $config): void
{
    pl_install_require_runtime();
    require_once dirname(__DIR__, 4) . '/vendor/autoload.php';
    DB::disconnect();
    DB::$host = $config['host'];
    DB::$port = (int) $config['port'];
    DB::$dbName = $config['database'];
    DB::$user = $config['user'];
    DB::$password = $config['password'];
    DB::$encoding = 'utf8mb4';
    DB::$nested_transactions = true;
    DB::query("SET time_zone = '+00:00'");
}

/** Reject takeover and unknown databases even when the host's setup key is valid. */
function pl_install_check_target(array $config, array $state): array
{
    pl_install_connect($config);
    $schema = pl_install_database_check();
    if ($schema['status'] === 'empty'
        && ((int) DB::queryFirstField('SELECT COUNT(*) FROM information_schema.routines WHERE routine_schema = DATABASE()') > 0
            || (int) DB::queryFirstField('SELECT COUNT(*) FROM information_schema.events WHERE event_schema = DATABASE()') > 0)) {
        throw new DomainException('Use a separate empty database without stored routines or events.');
    }
    $identity = pl_install_database_identity($config);
    if ($schema['status'] !== 'empty' && (!isset($state['database_id']) || !hash_equals((string) $state['database_id'], $identity))) {
        throw new DomainException('This database is not an empty installation target. Existing installations must use the upgrade procedure.');
    }
    if (isset($state['database_id']) && !hash_equals((string) $state['database_id'], $identity)) {
        throw new DomainException('This setup is already bound to another database. The existing installation was preserved.');
    }
    $tables = DB::queryFirstColumn('SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = DATABASE()');
    if (in_array('pl_users', $tables, true) && (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_users') > 0
        && !isset($state['owner_email'])) {
        throw new DomainException('This database already has users. Browser installation cannot claim an existing application.');
    }
    return $schema;
}

/** Existing configuration is never replaced by browser installation. */
function pl_install_check_existing_configuration(array $runtime): void
{
    $path = pl_install_config_path();
    if (is_file($path)) {
        try {
            $existing = require $path;
        } catch (Throwable $error) {
            throw new RuntimeException('The private configuration could not be loaded.');
        }
        if (!is_array($existing)) {
            throw new DomainException('The existing private configuration needs operator review.');
        }
        foreach ($runtime as $key => $value) {
            if (!array_key_exists($key, $existing) || (string) $existing[$key] !== (string) $value) {
                throw new DomainException('The existing private configuration was preserved. Use its database settings or review it in your hosting panel.');
            }
        }
    }
}

/** Even if setup markers are lost, an existing configured application cannot be claimed. */
function pl_install_refuse_existing_application(array $state): void
{
    if ($state !== []) {
        return;
    }
    $path = pl_install_config_path();
    if (!is_file($path) && (getenv('PL_DB_PASSWORD') === false || getenv('PL_DB_PASSWORD') === '')) {
        return;
    }
    $config = ['host' => getenv('PL_DB_HOST') ?: '127.0.0.1', 'port' => (int) (getenv('PL_DB_PORT') ?: 3306),
        'database' => getenv('PL_DB_NAME') ?: 'phpledger', 'user' => getenv('PL_DB_USER') ?: 'phpledger', 'password' => getenv('PL_DB_PASSWORD') ?: ''];
    if (is_file($path)) {
        try {
            $override = require $path;
        } catch (Throwable $error) {
            throw new RuntimeException('The private configuration could not be loaded.');
        }
        if (!is_array($override)) {
            throw new DomainException('The existing private configuration needs operator review.');
        }
        $config = array_replace($config, $override);
    }
    pl_install_connect($config);
    if (pl_install_database_check()['status'] !== 'empty') {
        throw new DomainException('This configured application already contains a schema. Use its sign-in or upgrade procedure; browser setup cannot replace it.');
    }
}

function pl_install_config_document(array $config): string
{
    return "<?php\ndeclare(strict_types=1);\nreturn " . var_export($config, true) . ";\n";
}

/** Preflight the normal runtime identity after migration, before it is persisted. */
function pl_install_verify_runtime(array $config): void
{
    pl_install_connect($config);
    if (pl_install_database_check()['status'] !== 'current') {
        throw new DomainException('Complete all migrations before saving runtime configuration.');
    }
    // Check the view definers using the normal runtime identity, not only schema credentials.
    $views = DB::queryFirstColumn("SELECT TABLE_NAME FROM information_schema.views WHERE table_schema = DATABASE()");
    foreach ($views as $view) {
        DB::query('SELECT * FROM %b LIMIT 0', $view);
    }
    DB::startTransaction();
    try {
        // A no-op DML check verifies common runtime grants without adding or changing an account.
        DB::query('UPDATE pl_users SET is_active = is_active WHERE id = -1');
        DB::query('DELETE FROM pl_login_attempts WHERE subject_hash = %s', str_repeat('0', 64));
        DB::rollback();
    } catch (Throwable $error) {
        DB::rollback();
        throw $error;
    }
}

/** Complete setup through the existing account service; retries cannot replace an account. */
function pl_install_finish(array $schemaConfig, array $runtimeConfig, string $email, string $name, string $password, array $state): array
{
    require_once __DIR__ . '/auth_functions.php';
    pl_install_check_target($schemaConfig, $state);
    pl_install_verify_runtime($runtimeConfig);
    pl_install_check_existing_configuration($runtimeConfig);
    pl_install_oauth_keys((string) $runtimeConfig['oauth_key_directory']);
    if (!is_file(pl_install_config_path())) {
        throw new DomainException('Save the private configuration file before creating the first account.');
    }
    $email = strtolower(trim($email));
    $name = trim($name);
    if (isset($state['owner_email']) && (!is_string($state['owner_email']) || !hash_equals($state['owner_email'], $email))) {
        throw new DomainException('Resume with the original installation account. Its identity cannot be replaced.');
    }
    // Validate before writing an identity intent or touching a user row.
    if (strlen($email) > 254 || filter_var($email, FILTER_VALIDATE_EMAIL) === false || $name === ''
        || !mb_check_encoding($name, 'UTF-8') || mb_strlen($name, 'UTF-8') > 120) {
        throw new InvalidArgumentException('Enter a valid email address and a name up to 120 characters.');
    }
    pl_hash_password($password);
    $state['owner_email'] = $email;
    $state['phase'] = 'account';
    pl_install_save_state($state);
    $users = DB::query('SELECT id, email, password_hash, is_active FROM pl_users');
    if ($users === []) {
        $id = pl_create_user($email, $name, $password);
    } elseif (count($users) === 1 && $users[0]['email'] === $email && (int) $users[0]['is_active'] === 1
        && pl_verify_password($password, $users[0]['password_hash'])) {
        $id = (int) $users[0]['id'];
    } else {
        throw new DomainException('The existing account was preserved. Resume with its original credentials or ask the installation operator.');
    }
    $operatorKey = pl_install_directory() . '/operator.key';
    if (!is_file($operatorKey)) {
        pl_install_write_private($operatorKey, bin2hex(random_bytes(32)) . "\n", false);
    }
    $receipt = ['format' => 1, 'database_id' => pl_install_database_identity($runtimeConfig), 'initial_owner_id' => $id,
        'completed_at' => gmdate('c'), 'schema_receipts' => (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_schema_migrations')];
    pl_install_save_state($receipt, 'installed.json');
    $state['phase'] = 'complete';
    pl_install_save_state($state);
    return ['id' => $id, 'email' => $email, 'display_name' => $name];
}

/** Local-only HTTP is an explicit development opt-in, never a production fallback. */
function pl_install_secure_request(array $server): bool
{
    return (!empty($server['HTTPS']) && strtolower((string) $server['HTTPS']) !== 'off') || (int) ($server['SERVER_PORT'] ?? 0) === 443;
}

function pl_install_http(): never
{
    require_once __DIR__ . '/web_functions.php';
    header('Cache-Control: no-store, private');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: no-referrer');
    header("Content-Security-Policy: default-src 'none'; style-src 'self'; img-src 'self'; font-src 'self'; script-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'none'");
    header('Content-Type: text/html; charset=utf-8');
    $error = '';
    $view = 'locked';
    $state = [];
    $schema = null;
    $lock = null;
    $applicationLock = null;
    try {
        if (!in_array($_SERVER['REQUEST_METHOD'] ?? '', ['GET', 'POST'], true)) {
            http_response_code(405);
            header('Allow: GET, POST');
            throw new DomainException('Use the installation form to continue.');
        }
        $key = pl_install_setup_key();
        if ($key === null) {
            http_response_code(404);
        } else {
            $secure = pl_install_secure_request($_SERVER);
            if (!$secure && !(in_array(getenv('PL_ENV'), ['local', 'test'], true) && getenv('PL_INSTALL_ALLOW_HTTP') === '1')) {
                http_response_code(400);
                throw new DomainException('Configure HTTPS in your hosting panel before entering setup credentials.');
            }
            pl_session_start($secure);
            $view = 'key';
            $lock = pl_install_operation_lock();
            $state = pl_install_read_state();
            $authorized = pl_install_authorized($key, $_SESSION, time());
            if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
                pl_require_csrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null);
                $action = pl_web_text($_POST, 'action');
                if ($action === 'unlock') {
                    pl_install_verify_key($key, pl_web_text($_POST, 'setup_key'), time());
                    if (!session_regenerate_id(true)) {
                        throw new RuntimeException('Setup session could not be renewed.');
                    }
                    $_SESSION['install_proof'] = ['key_hash' => hash('sha256', $key), 'at' => time()];
                    pl_csrf_rotate();
                    $authorized = true;
                } elseif (!$authorized) {
                    throw new DomainException('Enter the private setup key to resume this installation.');
                }
            }
            if ($authorized) {
                $view = 'database';
                pl_install_require_runtime();
                pl_install_refuse_existing_application($state);
                $sessionConfig = $_SESSION['install_database'] ?? null;
                $runtimeConfig = $_SESSION['install_runtime'] ?? null;
                if (is_array($sessionConfig) && is_array($runtimeConfig)) {
                    $view = match ($state['phase'] ?? '') {
                        'review' => 'review', 'migrating' => 'migrating', 'configuration' => 'configuration', 'account' => 'account', default => 'database',
                    };
                }
                if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
                    $action = pl_web_text($_POST, 'action');
                    if ($action === 'database') {
                        $view = 'database';
                        $candidate = pl_install_database_input($_POST);
                        $runtime = pl_web_text($_POST, 'separate_runtime') === '1'
                            ? pl_install_database_input(array_merge($_POST, ['runtime_host' => $candidate['host'], 'runtime_port' => (string) $candidate['port'], 'runtime_database' => $candidate['database']]), 'runtime_')
                            : $candidate;
                        $runtime['public_url'] = pl_install_public_url(pl_web_text($_POST, 'public_url'));
                        $runtime['oauth_key_directory'] = pl_install_private_path((string) (getenv('PL_OAUTH_KEY_DIRECTORY') ?: pl_install_directory() . '/oauth'));
                        $runtime['installation_directory'] = pl_install_directory();
                        pl_install_check_existing_configuration($runtime);
                        $schema = pl_install_check_target($candidate, $state);
                        if ($schema['status'] === 'empty' && $state === []) {
                            $state = ['format' => 1, 'database_id' => pl_install_database_identity($candidate), 'phase' => 'review', 'created_at' => gmdate('c')];
                            pl_install_save_state($state);
                        }
                        $_SESSION['install_database'] = $sessionConfig = $candidate;
                        $_SESSION['install_runtime'] = $runtimeConfig = $runtime;
                    } elseif (in_array($action, ['migrate', 'save_config', 'download_config', 'finish'], true)) {
                        if (!is_array($sessionConfig) || !is_array($runtimeConfig)) {
                            throw new DomainException('Re-enter the database credentials to resume. No stored migration receipt will be changed.');
                        }
                        $applicationLock = pl_install_operation_lock('application.lock');
                        $schema = pl_install_check_target($sessionConfig, $state);
                        if ($action === 'migrate') {
                            if (!in_array($state['phase'] ?? '', ['review', 'migrating', 'configuration'], true)) {
                                throw new DomainException('This installation state needs operator review.');
                            }
                            $state['phase'] = 'migrating';
                            pl_install_save_state($state);
                            pl_migrate(1);
                            $schema = pl_install_database_check();
                            if ($schema['status'] === 'current') {
                                $state['phase'] = 'configuration';
                                pl_install_save_state($state);
                            }
                        } elseif ($action === 'save_config' || $action === 'download_config') {
                            $runtimeConfig['public_url'] = pl_install_public_url(pl_web_text($_POST, 'public_url', (string) ($runtimeConfig['public_url'] ?? '')));
                            $runtimeConfig['oauth_key_directory'] = pl_install_private_path((string) (getenv('PL_OAUTH_KEY_DIRECTORY') ?: ($runtimeConfig['oauth_key_directory'] ?? pl_install_directory() . '/oauth')));
                            $runtimeConfig['installation_directory'] = pl_install_directory();
                            $_SESSION['install_runtime'] = $runtimeConfig;
                            pl_install_verify_runtime($runtimeConfig);
                            pl_install_check_existing_configuration($runtimeConfig);
                            pl_install_oauth_keys((string) $runtimeConfig['oauth_key_directory']);
                            if ($action === 'download_config') {
                                header('Content-Type: application/octet-stream');
                                header('Content-Disposition: attachment; filename="config.local.php"');
                                echo pl_install_config_document($runtimeConfig);
                                exit;
                            }
                            if (!is_file(pl_install_config_path())) {
                                pl_install_write_private(pl_install_config_path(), pl_install_config_document($runtimeConfig), false);
                            }
                            $state['phase'] = 'account';
                            pl_install_save_state($state);
                        } else {
                            $user = pl_install_finish($sessionConfig, $runtimeConfig, pl_web_text($_POST, 'email'), pl_web_text($_POST, 'name'), is_string($_POST['password'] ?? null) ? $_POST['password'] : '', $state);
                            pl_login_session($user); // Clears temporary schema/runtime credentials and setup proof.
                            header('Location: ' . pl_url('/onboarding'), true, 303);
                            exit;
                        }
                    } elseif ($action !== 'unlock') {
                        throw new DomainException('Choose an action from the installation form.');
                    }
                }
                if (is_array($sessionConfig) && is_array($runtimeConfig)) {
                    $schema = pl_install_check_target($sessionConfig, $state);
                    $view = $schema['status'] === 'current' ? (is_file(pl_install_config_path()) ? 'account' : 'configuration')
                        : (($state['phase'] ?? '') === 'review' ? 'review' : 'migrating');
                }
            }
        }
    } catch (InvalidArgumentException | DomainException $exception) {
        $error = $exception->getMessage();
        if (http_response_code() < 400) {
            http_response_code(400);
        }
    } catch (Throwable $exception) {
        // Database drivers may include credentials or raw SQL; never render/log their message.
        $error = 'Setup could not complete this step. Check database credentials, grants, private storage and migration status in your hosting panel. Existing state was preserved.';
        http_response_code(503);
    } finally {
        if (is_resource($applicationLock)) {
            flock($applicationLock, LOCK_UN);
            fclose($applicationLock);
        }
        if (is_resource($lock)) {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }
    require dirname(__DIR__, 2) . '/templates/views/install.php';
    exit;
}
