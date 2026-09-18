<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/tools/sign-update.php';
require_once dirname(__DIR__) . '/www/phpledger/includes/functions/update_download_functions.php';

/** Entirely synthetic publisher keys and ZIPs, never a trusted production key. */
$directory = sys_get_temp_dir() . '/phpledger-signing-' . bin2hex(random_bytes(8));
if (!mkdir($directory, 0700)) { throw new RuntimeException('Cannot create synthetic workspace.'); }
$checks = 0;
function signing_assert(bool $condition): void {
    global $checks;
    if (!$condition) { throw new RuntimeException('Signing assertion failed.'); }
    $checks++;
}
function signing_reject(callable $operation): void {
    try { $operation(); } catch (Throwable) { signing_assert(true); return; }
    throw new RuntimeException('Unsafe signing operation was accepted.');
}
function signing_zip(string $path, array $files): void {
    $zip = new ZipArchive();
    if ($zip->open($path, ZipArchive::CREATE | ZipArchive::EXCL) !== true) { throw new RuntimeException('Cannot create fixture.'); }
    foreach ($files as $name => $bytes) {
        $zip->addFromString('phpledger-1.0.0/' . $name, $bytes);
        $zip->setExternalAttributesName('phpledger-1.0.0/' . $name, ZipArchive::OPSYS_UNIX, 0100644 << 16);
    }
    $zip->close();
}
try {
    signing_assert(pl_update_download_url_allowed('https://github.com/rmak78/phpledger/releases/download/v1.0.0/phpledger-1.0.0.zip'));
    signing_assert(pl_update_download_url_allowed('https://release-assets.githubusercontent.com/example.zip?token=synthetic'));
    foreach (['http://github.com/release.zip', 'https://127.0.0.1/release.zip', 'https://github.com.evil.invalid/file', 'https://user:pass@github.com/file', 'https://github.com:8443/file', 'https://github.com/file#fragment'] as $unsafeUrl) {
        signing_assert(!pl_update_download_url_allowed($unsafeUrl));
    }
    $key = openssl_pkey_new(['private_key_bits' => 3072, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    if ($key === false || !openssl_pkey_export($key, $private)) { throw new RuntimeException('Cannot create synthetic key.'); }
    file_put_contents($directory . '/synthetic.pem', $private);
    $details = openssl_pkey_get_details($key);
    if ($details === false) { throw new RuntimeException('Cannot export synthetic public key.'); }
    $content = '<?php // original synthetic payload';
    $manifest = ['version' => '1.0.0', 'channel' => 'stable', 'files' => [
        ['path' => 'www/phpledger/public/index.php', 'bytes' => strlen($content), 'sha256' => hash('sha256', $content)],
    ]];
    $files = ['www/phpledger/public/index.php' => $content, 'PACKAGE-MANIFEST.json' => json_encode($manifest, JSON_THROW_ON_ERROR)];
    signing_zip($directory . '/release.zip', $files);
    pl_release_sign_update($directory . '/release.zip', $directory . '/synthetic.pem', $directory . '/release.update.json');
    $envelope = json_decode(file_get_contents($directory . '/release.update.json'), true, 32, JSON_THROW_ON_ERROR);
    $payload = base64_decode($envelope['payload'], true);
    $signature = base64_decode($envelope['signature'], true);
    signing_assert(is_string($payload) && is_string($signature));
    signing_assert(openssl_verify($payload, $signature, $details['key'], OPENSSL_ALGO_SHA256) === 1);
    signing_assert(openssl_verify($payload . ' ', $signature, $details['key'], OPENSSL_ALGO_SHA256) !== 1);
    $metadata = json_decode($payload, true, 32, JSON_THROW_ON_ERROR);
    signing_assert($metadata['channel'] === 'stable' && count($metadata['files']) === 2);
    signing_assert($metadata['archive_sha256'] === hash_file('sha256', $directory . '/release.zip'));
    signing_assert(!str_contains(file_get_contents($directory . '/release.update.json'), 'PRIVATE KEY'));
    signing_reject(fn () => pl_release_sign_update($directory . '/release.zip', $directory . '/synthetic.pem', $directory . '/release.update.json'));
    signing_zip($directory . '/tampered.zip', array_replace($files, ['www/phpledger/public/index.php' => '<?php // changed']));
    signing_reject(fn () => pl_release_update_payload($directory . '/tampered.zip'));
    signing_zip($directory . '/extra.zip', $files + ['unexpected.php' => 'unexpected']);
    signing_reject(fn () => pl_release_update_payload($directory . '/extra.zip'));
    signing_zip($directory . '/traversal.zip', $files + ['../escape.php' => 'unsafe']);
    signing_reject(fn () => pl_release_update_payload($directory . '/traversal.zip'));
    signing_zip($directory . '/private.zip', $files + ['www/phpledger/includes/config.local.php' => 'synthetic configuration']);
    signing_reject(fn () => pl_release_update_payload($directory . '/private.zip'));
    $wrongChannel = $manifest;
    $wrongChannel['channel'] = 'preview';
    signing_zip($directory . '/channel.zip', array_replace($files, ['PACKAGE-MANIFEST.json' => json_encode($wrongChannel, JSON_THROW_ON_ERROR)]));
    signing_reject(fn () => pl_release_update_payload($directory . '/channel.zip'));
    fwrite(STDOUT, "Release signing: {$checks} checks passed; synthetic keys only.\n");
} finally {
    // Only our flat random directory is ever cleaned up.
    foreach (glob($directory . '/*') ?: [] as $file) { if (is_file($file) && !is_link($file)) { unlink($file); } }
    rmdir($directory);
}
