<?php
declare(strict_types=1);

/** PASSWORD_DEFAULT currently uses bcrypt, whose input limit is 72 bytes. */
function pl_hash_password(string $password): string
{
    if (strlen($password) < 12 || strlen($password) > 72 || str_contains($password, "\0")) {
        throw new InvalidArgumentException('Use a password between 12 and 72 bytes.');
    }
    return password_hash($password, PASSWORD_DEFAULT);
}

function pl_verify_password(string $password, string $hash): bool
{
    return strlen($password) <= 72 && !str_contains($password, "\0") && password_verify($password, $hash);
}

/** CLI setup entry point; there is no public user-creation route. */
function pl_create_user(string $email, string $displayName, string $password): int
{
    pl_demo_require_setup_action();
    $email = strtolower(trim($email));
    $displayName = trim($displayName);
    if (strlen($email) > 254 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        throw new InvalidArgumentException('Enter a valid email address.');
    }
    if ($displayName === '' || !mb_check_encoding($displayName, 'UTF-8') || mb_strlen($displayName, 'UTF-8') > 120) {
        throw new InvalidArgumentException('Enter a name up to 120 characters.');
    }
    $hash = pl_hash_password($password);
    DB::insert('pl_users', [
        'email' => $email,
        'display_name' => $displayName,
        'password_hash' => $hash,
        'is_active' => 1,
    ]);
    return (int) DB::insertId();
}

/**
 * Count login attempts in durable server-side buckets, locked in stable order.
 * No email address, password, or client address is stored in the limiter.
 * A failed/disabled/unknown/throttled login always returns the same null result.
 */
function pl_authenticate(string $email, string $password, string $clientIp): ?array
{
    $email = strtolower(trim($email));
    $now = time();
    $accountKey = hash('sha256', 'account:' . $email);
    $clientKey = hash('sha256', 'client:' . $clientIp);
    $limits = [$accountKey => 5, $clientKey => 30];
    ksort($limits, SORT_STRING);

    DB::startTransaction();
    try {
        $buckets = [];
        $blocked = false;
        foreach ($limits as $key => $limit) {
            DB::query(
                'INSERT INTO pl_login_attempts (subject_hash, window_started_at, attempt_count, blocked_until) '
                . 'VALUES (%s, %s, 0, NULL) ON DUPLICATE KEY UPDATE subject_hash = subject_hash',
                $key,
                gmdate('Y-m-d H:i:s', $now)
            );
            $bucket = DB::queryFirstRow('SELECT * FROM pl_login_attempts WHERE subject_hash = %s FOR UPDATE', $key);
            if (!$bucket) {
                throw new RuntimeException('Login attempt tracking is unavailable.');
            }
            $windowStart = strtotime($bucket['window_started_at'] . ' UTC');
            if ($windowStart === false || $now - $windowStart >= 900) {
                $bucket['attempt_count'] = 0;
                $bucket['window_started_at'] = gmdate('Y-m-d H:i:s', $now);
                $bucket['blocked_until'] = null;
            }
            if ((int) $bucket['attempt_count'] >= $limit) {
                $blocked = true;
            }
            $buckets[$key] = $bucket;
        }
        if ($blocked) {
            DB::commit();
            return null;
        }

        $user = strlen($email) <= 254 && filter_var($email, FILTER_VALIDATE_EMAIL) !== false
            ? DB::queryFirstRow('SELECT id, email, display_name, password_hash, is_active FROM pl_users WHERE email = %s', $email)
            : null;
        // Public dummy bcrypt hash keeps unknown-user verification on the same code path.
        $dummyHash = '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
        $valid = pl_verify_password($password, $user['password_hash'] ?? $dummyHash)
            && $user !== null && (int) $user['is_active'] === 1;

        foreach ($buckets as $key => $bucket) {
            $count = ($valid && $key === $accountKey) ? 0 : (int) $bucket['attempt_count'] + 1;
            $windowStart = ($valid && $key === $accountKey) ? gmdate('Y-m-d H:i:s', $now) : $bucket['window_started_at'];
            DB::update('pl_login_attempts', [
                'window_started_at' => $windowStart,
                'attempt_count' => $count,
                'blocked_until' => $count >= $limits[$key]
                    ? gmdate('Y-m-d H:i:s', (int) strtotime($windowStart . ' UTC') + 900)
                    : null,
            ], 'subject_hash = %s', $key);
        }

        if ($valid && password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            DB::update('pl_users', ['password_hash' => pl_hash_password($password)], 'id = %i', (int) $user['id']);
        }
        DB::commit();
        if (!$valid) {
            return null;
        }
        return ['id' => (int) $user['id'], 'email' => $user['email'], 'display_name' => $user['display_name']];
    } catch (Throwable $error) {
        DB::rollback();
        throw $error;
    }
}

/** Server-side boundary for every company read and write, including CLI services. */
function pl_require_company_access(int $actorId, int $companyId, bool $write = false): array
{
    if ($actorId < 1 || $companyId < 1) {
        throw new DomainException('You do not have access to this company.');
    }
    $member = DB::queryFirstRow(
        'SELECT m.company_id, m.user_id, m.role FROM pl_company_members m '
        . 'INNER JOIN pl_users u ON u.id = m.user_id '
        // A current read also prevents stale permissions inside a caller-owned transaction.
        . 'WHERE m.company_id = %i AND m.user_id = %i AND u.is_active = 1 FOR SHARE',
        $companyId,
        $actorId
    );
    if (!$member || !in_array($member['role'], ['owner', 'accountant', 'viewer'], true)
        || ($write && !in_array($member['role'], ['owner', 'accountant'], true))) {
        throw new DomainException('You do not have access to this company.');
    }
    pl_demo_require_company($actorId, $companyId);
    return $member;
}
