<?php
declare(strict_types=1);

// Installation credentials must never be accepted over HTTP.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$options = getopt('', ['email:', 'name:', 'username:', 'password-stdin']);
if (!is_array($options) || !isset($options['email'], $options['name'])
    || !is_string($options['email']) || !is_string($options['name']) || (isset($options['username']) && !is_string($options['username']))) {
    fwrite(STDERR, "Usage: php install/create-admin.php --email=you@example.com --name=Owner [--username=owner] [--password-stdin]\n");
    fwrite(STDERR, "Provide the password with PL_ADMIN_PASSWORD or --password-stdin. Never pass it as an argument.\n");
    exit(1);
}

$password = array_key_exists('password-stdin', $options)
    ? rtrim((string) fgets(STDIN, 4096), "\r\n")
    : getenv('PL_ADMIN_PASSWORD');
putenv('PL_ADMIN_PASSWORD');

if (!is_string($password) || $password === '') {
    fwrite(STDERR, "A password is required. Use PL_ADMIN_PASSWORD or --password-stdin.\n");
    exit(1);
}

try {
    require_once __DIR__ . '/preflight.php';
    pl_install_require_runtime();
    try {
        require dirname(__DIR__) . '/includes/bootstrap.php';
    } catch (Throwable $error) {
        throw new RuntimeException('Application configuration could not be loaded.');
    }
    if (pl_install_database_check()['status'] !== 'current') {
        throw new DomainException('Run the package migrations before creating an administrator account.');
    }
    $id = pl_create_user($options['email'], $options['name'], $password, $options['username'] ?? null);
    unset($password);
    fwrite(STDOUT, "Administrator account created (user {$id}). Create a company to become its owner.\n");
} catch (InvalidArgumentException | DomainException $error) {
    unset($password);
    fwrite(STDERR, $error->getMessage() . "\n");
    exit(1);
} catch (Throwable $error) {
    unset($password);
    fwrite(STDERR, "Account creation failed. Check database setup and that the email is not already registered.\n");
    exit(1);
}
