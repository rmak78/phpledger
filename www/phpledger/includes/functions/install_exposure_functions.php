<?php
declare(strict_types=1);

require_once __DIR__ . '/installation_state_functions.php';

/**
 * A copy uploaded inside a website (shared hosting, XAMPP) relies on the
 * package .htaccess rules to hide its private folders. Before setup creates any
 * secret, fetch harmless probe files through the site's own address. A front
 * proxy that serves static files itself would otherwise bypass those rules.
 *
 * Returns 'not-needed' when the document root is www/phpledger/public, then
 * 'protected', 'exposed' or 'unknown' (the site could not reach itself).
 */
function pl_install_exposure_status(array $server): string
{
    if (!defined('PL_WEB_ADAPTER')) {
        return 'not-needed';
    }
    $directory = pl_install_directory(true);
    pl_install_protect_directory($directory);
    $cachePath = $directory . '/exposure.json';
    $cached = is_file($cachePath) ? json_decode((string) file_get_contents($cachePath), true) : null;
    if (is_array($cached) && ($cached['status'] ?? '') === 'protected' && is_int($cached['at'] ?? null) && time() - $cached['at'] < 600) {
        return 'protected';
    }
    $origin = pl_install_probe_origin($server);
    $root = realpath(dirname(__DIR__, 4));
    if ($origin === null || $root === false) {
        return 'unknown';
    }
    $token = bin2hex(random_bytes(16));
    $probes = [[pl_install_probe_url($origin, $root, $root . '/vendor/composer/installed.json'), '"packages"']];
    foreach (['exposure-probe.txt', 'exposure-probe.key'] as $name) {
        $path = $directory . '/' . $name;
        pl_install_write_private($path, "PHP Ledger private-folder check. If a browser shows this text, private files are exposed.\n" . $token . "\n");
        $probes[] = [pl_install_probe_url($origin, $root, $path), $token];
    }
    $status = 'protected';
    foreach ($probes as [$url, $marker]) {
        if ($url === null) {
            continue; // Private storage configured outside the uploaded folder is not reachable through it.
        }
        $result = pl_install_probe($url);
        if ($result === null) {
            $status = 'unknown';
        } elseif ($result['status'] === 200 && str_contains($result['body'], $marker)) {
            $status = 'exposed';
            break;
        }
    }
    if ($status !== 'unknown') {
        // The manual check link on the setup page needs the text probe only while the result is unknown.
        foreach (['exposure-probe.txt', 'exposure-probe.key'] as $name) {
            @unlink($directory . '/' . $name);
        }
    }
    pl_install_write_private($cachePath, json_encode(['status' => $status, 'at' => time()], JSON_THROW_ON_ERROR) . "\n");
    return $status;
}

/** The manual check link shown when the automatic check could not reach the site; null when not applicable. */
function pl_install_exposure_check_link(): ?string
{
    $root = realpath(dirname(__DIR__, 4));
    $path = pl_install_directory() . '/exposure-probe.txt';
    if ($root === false || !is_file($path)) {
        return null;
    }
    $url = pl_install_probe_url('', $root, $path);
    return $url === null ? null : pl_url($url);
}

/** A second layer under the package rules: Apache and LiteSpeed refuse every request into this folder. */
function pl_install_protect_directory(string $directory): void
{
    $path = $directory . '/.htaccess';
    if (!is_file($path)) {
        pl_install_write_private($path, "# PHP Ledger private installation state: never served to browsers.\n"
            . "<IfModule mod_authz_core.c>\n    Require all denied\n</IfModule>\n"
            . "<IfModule !mod_authz_core.c>\n    Order deny,allow\n    Deny from all\n</IfModule>\n");
        @chmod($path, 0644);
    }
}

/** The site's own scheme, host and folder. The host is restricted to a plain name or address. */
function pl_install_probe_origin(array $server): ?string
{
    $host = strtolower((string) ($server['HTTP_HOST'] ?? ''));
    if (!preg_match('/^(?:[a-z0-9-]+(?:\.[a-z0-9-]+)*|\[[0-9a-f:.]+\])(?::[0-9]{1,5})?$/D', $host)) {
        return null;
    }
    $secure = (!empty($server['HTTPS']) && strtolower((string) $server['HTTPS']) !== 'off') || (int) ($server['SERVER_PORT'] ?? 0) === 443;
    return ($secure ? 'https://' : 'http://') . $host . (function_exists('pl_base_path') ? pl_base_path() : '');
}

/** Map a file under the uploaded folder to its would-be public address; null when it lies outside. */
function pl_install_probe_url(string $origin, string $root, string $path): ?string
{
    $root = rtrim(str_replace('\\', '/', $root), '/');
    $resolved = realpath($path);
    $path = str_replace('\\', '/', $resolved === false ? $path : $resolved);
    if (!str_starts_with(strtolower($path), strtolower($root) . '/')) {
        return null;
    }
    return $origin . '/' . implode('/', array_map('rawurlencode', explode('/', substr($path, strlen($root) + 1))));
}

/** One bounded GET: no redirects, no credentials, a short timeout and only a small body. */
function pl_install_probe(string $url): ?array
{
    if (!function_exists('curl_init')) {
        return null;
    }
    $curl = curl_init($url);
    if ($curl === false) {
        return null;
    }
    $body = '';
    curl_setopt_array($curl, [
        CURLOPT_FOLLOWLOCATION => false, CURLOPT_CONNECTTIMEOUT => 2, CURLOPT_TIMEOUT => 3,
        CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS, CURLOPT_HTTPHEADER => ['Accept: */*'],
        CURLOPT_USERAGENT => 'PHP Ledger installer private-folder check',
        CURLOPT_WRITEFUNCTION => static function ($handle, string $chunk) use (&$body): int {
            $body .= $chunk;
            return strlen($body) > 65536 ? 0 : strlen($chunk);
        },
    ]);
    $completed = curl_exec($curl);
    $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    curl_close($curl);
    if ($status === 0 || ($completed === false && strlen($body) <= 65536)) {
        return null;
    }
    return ['status' => $status, 'body' => $body];
}
