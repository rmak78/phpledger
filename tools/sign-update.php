<?php
declare(strict_types=1);

/** Publisher-only utility. Private signing keys never enter the application ZIP. */
function pl_release_update_payload(string $archivePath): array
{
    if (!class_exists(ZipArchive::class) || !is_file($archivePath) || is_link($archivePath)) {
        throw new RuntimeException('A regular release ZIP and the PHP zip extension are required.');
    }
    $size = filesize($archivePath);
    if ($size === false || $size < 1 || $size > 100_000_000) {
        throw new RuntimeException('Release ZIP exceeds the supported size.');
    }
    $zip = new ZipArchive();
    if ($zip->open($archivePath, ZipArchive::RDONLY) !== true) {
        throw new RuntimeException('Release ZIP cannot be opened.');
    }
    try {
        if ($zip->numFiles < 2 || $zip->numFiles > 20000) {
            throw new RuntimeException('Unexpected release file count.');
        }
        $inventory = [];
        $prefix = null;
        $manifestBytes = null;
        $total = 0;
        $casePaths = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $info = $zip->statIndex($i);
            if ($info === false || !preg_match('~^(phpledger-([^/]+))/([A-Za-z0-9_.\-/]+)$~D', $info['name'], $match)) {
                throw new RuntimeException('Release ZIP has an invalid path.');
            }
            $prefix ??= $match[1];
            $path = $match[3];
            if ($prefix !== $match[1] || preg_match('~(^|/)(\.|\.\.|\.git|\.env|storage|uploads)(/|$)~i', $path)
                || str_contains($path, '//') || str_ends_with($path, '/')
                || preg_match('~(^|/)config\.local\.php$|\.(pem|key|sql|log)$~i', $path)
                || isset($casePaths[strtolower($path)])) {
                throw new RuntimeException('Release ZIP contains a duplicate, private or unsafe path.');
            }
            $opsys = $attributes = 0;
            if (!$zip->getExternalAttributesIndex($i, $opsys, $attributes)
                || ($opsys === 3 && (($attributes >> 16) & 0170000) !== 0100000)) {
                throw new RuntimeException('Only regular files may be signed.');
            }
            if ($info['size'] > 10_000_000 || ($total += $info['size']) > 100_000_000) {
                throw new RuntimeException('Expanded release exceeds the supported size.');
            }
            $data = $zip->getFromIndex($i);
            if (!is_string($data) || strlen($data) !== $info['size']) {
                throw new RuntimeException('Release file cannot be verified.');
            }
            $casePaths[strtolower($path)] = true;
            $inventory[$path] = ['path' => $path, 'bytes' => strlen($data), 'sha256' => hash('sha256', $data)];
            if ($path === 'PACKAGE-MANIFEST.json') {
                $manifestBytes = $data;
            }
        }
        $manifest = json_decode($manifestBytes ?? '', true, 32, JSON_THROW_ON_ERROR);
        $version = $manifest['version'] ?? '';
        if (!is_string($version) || !preg_match('/^(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)(?:-[0-9a-z-]+(?:\.[0-9a-z-]+)*)?$/D', $version)
            || $prefix !== 'phpledger-' . $version) {
            throw new RuntimeException('Release version and archive root must agree.');
        }
        $channel = str_contains($version, '-') ? 'preview' : 'stable';
        if (($manifest['channel'] ?? $channel) !== $channel || !is_array($manifest['files'] ?? null)) {
            throw new RuntimeException('Release channel or inventory is invalid.');
        }
        $expected = [];
        foreach ($manifest['files'] as $entry) {
            $path = $entry['path'] ?? '';
            if (!is_string($path) || $path === 'PACKAGE-MANIFEST.json' || isset($expected[$path]) || !isset($inventory[$path])
                || $inventory[$path]['bytes'] !== ($entry['bytes'] ?? null)
                || $inventory[$path]['sha256'] !== ($entry['sha256'] ?? null)) {
                throw new RuntimeException('Release inventory does not match its files.');
            }
            $expected[$path] = true;
        }
        if (count($expected) + 1 !== count($inventory)) {
            throw new RuntimeException('Release ZIP contains unmanifested files.');
        }
        ksort($inventory, SORT_STRING);
        return ['schema' => 1, 'version' => $version, 'channel' => $channel,
            'archive_sha256' => hash_file('sha256', $archivePath), 'archive_bytes' => $size,
            'min_php' => '8.2.0', 'files' => array_values($inventory)];
    } finally {
        $zip->close();
    }
}

function pl_release_sign_update(string $archivePath, string $privateKeyPath, string $outputPath): void
{
    if (!is_file($privateKeyPath) || is_link($privateKeyPath) || filesize($privateKeyPath) > 65536) {
        throw new RuntimeException('Supply an external private release signing key.');
    }
    $keyBytes = file_get_contents($privateKeyPath);
    $key = $keyBytes === false ? false : openssl_pkey_get_private($keyBytes, getenv('PL_RELEASE_KEY_PASSPHRASE') ?: '');
    $details = $key === false ? false : openssl_pkey_get_details($key);
    if ($key === false || $details === false || $details['type'] !== OPENSSL_KEYTYPE_RSA || $details['bits'] < 3072) {
        throw new RuntimeException('Release signing requires an RSA private key of at least 3072 bits.');
    }
    $payload = json_encode(pl_release_update_payload($archivePath), JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    if (!openssl_sign($payload, $signature, $key, OPENSSL_ALGO_SHA256)) {
        throw new RuntimeException('Release signing failed.');
    }
    $bytes = json_encode(['payload' => base64_encode($payload), 'signature' => base64_encode($signature)], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n";
    $stream = @fopen($outputPath, 'xb');
    if ($stream === false) {
        throw new RuntimeException('Choose a new output file in an existing private working directory.');
    }
    try {
        if (fwrite($stream, $bytes) !== strlen($bytes) || !fflush($stream)) {
            throw new RuntimeException('Signed metadata could not be written completely.');
        }
    } finally {
        fclose($stream);
    }
}

if (PHP_SAPI === 'cli' && realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    $options = getopt('', ['archive:', 'key:', 'output:']);
    try {
        foreach (['archive', 'key', 'output'] as $required) {
            if (!is_string($options[$required] ?? null) || $options[$required] === '') {
                throw new RuntimeException('Usage: php tools/sign-update.php --archive=release.zip --key=/private/publisher.pem --output=release.update.json');
            }
        }
        pl_release_sign_update($options['archive'], $options['key'], $options['output']);
        fwrite(STDOUT, "Signed release metadata written. Keep the private key offline; distribute only the ZIP and signed metadata.\n");
    } catch (Throwable $error) {
        fwrite(STDERR, "Release metadata was not signed. Verify the archive inventory, signing key and new output path.\n");
        exit(1);
    }
}
