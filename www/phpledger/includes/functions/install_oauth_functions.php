<?php
declare(strict_types=1);

require_once __DIR__ . '/installation_state_functions.php';

/** Validate an explicit public origin; never infer it from an untrusted Host header. */
function pl_install_public_url(string $input): string
{
    $url = rtrim(trim($input), '/');
    $parts = parse_url($url);
    $local = in_array(getenv('PL_ENV'), ['local', 'test'], true) && getenv('PL_INSTALL_ALLOW_HTTP') === '1';
    if (!is_array($parts) || !isset($parts['scheme'], $parts['host'])
        || isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment'])
        || (($parts['path'] ?? '') !== '') || strlen($url) > 480 || preg_match('/[\x00-\x20\x7f]/', $url)
        || ($parts['scheme'] !== 'https' && !($local && $parts['scheme'] === 'http' && in_array($parts['host'], ['127.0.0.1', 'localhost'], true)))) {
        throw new InvalidArgumentException('Enter the public HTTPS application URL, such as https://books.example.com, without a path, query or sign-in details.');
    }
    return $url;
}

/** Create the existing RSA/encryption key format atomically. Existing keys are never rotated. */
function pl_install_oauth_keys(string $directory): bool
{
    $directory = pl_install_private_path($directory);
    if (file_exists($directory)) {
        if (!is_dir($directory) || is_link($directory)) {
            throw new DomainException('The OAuth key directory needs operator review. Existing state was preserved.');
        }
        foreach (['private.key', 'public.key', 'encryption.key'] as $name) {
            $path = $directory . '/' . $name;
            if (!is_file($path) || is_link($path) || !is_readable($path)) {
                throw new DomainException('OAuth key creation was interrupted or the key directory is incomplete. Preserve it and restore the matching keys before continuing.');
            }
            if ($name !== 'public.key' && PHP_OS_FAMILY !== 'Windows' && ((int) fileperms($path) & 0007) !== 0) {
                throw new DomainException('Restrict private OAuth key permissions in your hosting panel before continuing.');
            }
        }
        $private = openssl_pkey_get_private((string) file_get_contents($directory . '/private.key'));
        $public = openssl_pkey_get_public((string) file_get_contents($directory . '/public.key'));
        $privateDetails = $private ? openssl_pkey_get_details($private) : false;
        $publicDetails = $public ? openssl_pkey_get_details($public) : false;
        if (!$privateDetails || !$publicDetails || $privateDetails['type'] !== OPENSSL_KEYTYPE_RSA || $privateDetails['bits'] < 3072
            || !hash_equals($privateDetails['key'], $publicDetails['key'])
            || !preg_match('/^[a-f0-9]{64}$/D', trim((string) file_get_contents($directory . '/encryption.key')))) {
            throw new DomainException('The existing OAuth keys do not form a valid matching set. Existing keys were preserved.');
        }
        return false;
    }
    $mask = umask(0077);
    $staging = $directory . '.new-' . bin2hex(random_bytes(12));
    try {
        if (!is_dir(dirname($directory)) && !mkdir(dirname($directory), 0700, true) && !is_dir(dirname($directory))) {
            throw new DomainException('Create a writable private parent directory for OAuth keys in your hosting panel.');
        }
        if (!mkdir($staging, 0700)) {
            throw new RuntimeException('Private OAuth staging could not be created.');
        }
        $key = openssl_pkey_new(['private_key_bits' => 3072, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        if (!$key || !openssl_pkey_export($key, $private)) {
            throw new RuntimeException('Could not create the private OAuth signing key.');
        }
        $details = openssl_pkey_get_details($key);
        if (!$details) {
            throw new RuntimeException('Could not obtain the OAuth public key.');
        }
        foreach (['private.key' => $private, 'public.key' => $details['key'], 'encryption.key' => bin2hex(random_bytes(32))] as $name => $contents) {
            pl_install_write_private($staging . '/' . $name, $contents, false);
        }
        if (file_exists($directory) || !rename($staging, $directory)) {
            throw new DomainException('The OAuth key destination changed. Existing keys were preserved.');
        }
        return true;
    } finally {
        if (is_dir($staging)) {
            foreach (['private.key', 'public.key', 'encryption.key'] as $name) {
                if (is_file($staging . '/' . $name)) {
                    unlink($staging . '/' . $name);
                }
            }
            @rmdir($staging);
        }
        umask($mask);
    }
}
