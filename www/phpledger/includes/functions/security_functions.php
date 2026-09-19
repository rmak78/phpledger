<?php
declare(strict_types=1);

/** Escape untrusted text for HTML text and quoted attributes. */
function pl_e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function e(string $value): string
{
    return pl_e($value);
}

/** All request mutations must check the verb as well as the CSRF token. */
function pl_require_post(?string $method = null): void
{
    if (($method ?? ($_SERVER['REQUEST_METHOD'] ?? '')) !== 'POST') {
        throw new DomainException('This action requires a POST request.');
    }
}

/** Call only from the web bootstrap; CLI services do not need a session. */
function pl_session_start(bool $secure): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    if (headers_sent()) {
        throw new RuntimeException('The session must start before output.');
    }
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');
    ini_set('session.gc_maxlifetime', '1800');
    // A copy uploaded into a subfolder keeps its own cookie, so two copies on one host never share a sign-in.
    $base = pl_demo_enabled() || !function_exists('pl_base_path') ? '' : pl_base_path();
    session_name(pl_demo_enabled() ? 'phpledger_demo_session' : ($base === '' ? 'phpledger_session' : 'phpledger_session_' . substr(hash('sha256', $base), 0, 12)));
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => pl_demo_enabled() ? '/demo/' : $base . '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    if (!session_start()) {
        throw new RuntimeException('The session could not be started.');
    }
    // Copies on one host may share PHP's session directory; a session belongs to the copy that created it.
    $installation = hash('sha256', str_replace('\\', '/', (string) (realpath(dirname(__DIR__, 2)) ?: dirname(__DIR__, 2))));
    if (!isset($_SESSION['pl_installation'])) {
        $_SESSION['pl_installation'] = $installation;
    } elseif (!is_string($_SESSION['pl_installation']) || !hash_equals($_SESSION['pl_installation'], $installation)) {
        $_SESSION = [];
        if (!session_regenerate_id(true)) {
            throw new RuntimeException('The session could not be renewed.');
        }
        $_SESSION['pl_installation'] = $installation;
    }
}

function pl_require_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        throw new LogicException('An active web session is required.');
    }
}

/** Rotate CSRF state after every authentication boundary. */
function pl_csrf_rotate(): string
{
    pl_require_session();
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

function pl_csrf_token(): string
{
    pl_require_session();
    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        return pl_csrf_rotate();
    }
    return $_SESSION['csrf_token'];
}

function pl_require_csrf(?string $token): void
{
    pl_require_session();
    $expected = $_SESSION['csrf_token'] ?? null;
    if (!is_string($expected) || !is_string($token) || strlen($token) !== 64
        || !hash_equals($expected, $token)) {
        throw new DomainException('Your session changed. Refresh and try again.');
    }
}

/** Validate timestamps independently of HTTP, cookies, and the database. */
function pl_session_is_fresh(array $state, int $now): bool
{
    $started = $state['authenticated_at'] ?? null;
    $last = $state['last_activity'] ?? null;
    return is_int($started) && is_int($last)
        && $started <= $last && $last <= $now
        && ($now - $last) < 1800 && ($now - $started) < 43200;
}

function pl_login_session(array $user): void
{
    pl_require_session();
    $userId = (int) ($user['id'] ?? 0);
    if ($userId < 1) {
        throw new InvalidArgumentException('A valid user is required.');
    }
    $regional = $_SESSION['regional_suggestion'] ?? null;
    $_SESSION = is_array($regional) ? ['regional_suggestion' => $regional] : [];
    if (!session_regenerate_id(true)) {
        throw new RuntimeException('The session could not be renewed.');
    }
    $now = time();
    $_SESSION['user_id'] = $userId;
    $_SESSION['authenticated_at'] = $now;
    $_SESSION['last_activity'] = $now;
    $_SESSION['rotated_at'] = $now;
    pl_csrf_rotate();
}

function pl_logout_session(): void
{
    pl_require_session();
    $regional = $_SESSION['regional_suggestion'] ?? null;
    $_SESSION = is_array($regional) ? ['regional_suggestion' => $regional] : [];
    if (!session_regenerate_id(true)) {
        throw new RuntimeException('The session could not be renewed.');
    }
    pl_csrf_rotate();
}

function pl_current_user_id(): ?int
{
    pl_require_session();
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    $now = time();
    if (!is_int($_SESSION['user_id']) || !pl_session_is_fresh($_SESSION, $now)) {
        pl_logout_session();
        return null;
    }
    $userId = $_SESSION['user_id'];
    if (!pl_demo_session_valid($userId)) {
        pl_logout_session();
        return null;
    }
    $active = DB::queryFirstField('SELECT id FROM pl_users WHERE id = %i AND is_active = 1', $userId);
    if (!$active) {
        pl_logout_session();
        return null;
    }
    $rotated = $_SESSION['rotated_at'] ?? 0;
    if (!is_int($rotated) || $now - $rotated >= 900) {
        if (!session_regenerate_id(true)) {
            throw new RuntimeException('The session could not be renewed.');
        }
        $_SESSION['rotated_at'] = $now;
    }
    $_SESSION['last_activity'] = $now;
    return $userId;
}
