<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') { exit(1); }
// Installation-owner CLI: generate private files without connecting to any database.
$directory = rtrim(getenv('PL_OAUTH_KEY_DIRECTORY') ?: dirname(__DIR__) . '/www/phpledger/storage/oauth', '/\\');
if (is_dir($directory)) { fwrite(STDERR, "OAuth directory already exists. Existing keys were preserved.\n"); exit(2); }
$key = openssl_pkey_new(['private_key_bits' => 3072, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
if (!$key || !openssl_pkey_export($key, $private)) { throw new RuntimeException('Could not create the signing key.'); }
$details = openssl_pkey_get_details($key);
if (!$details) { throw new RuntimeException('Could not obtain the public key.'); }
umask(0077);
if (!mkdir($directory, 0700, true)) { throw new RuntimeException('Could not create the private OAuth directory.'); }
foreach (['private.key' => $private, 'public.key' => $details['key'], 'encryption.key' => bin2hex(random_bytes(32))] as $name => $contents) {
    $handle = fopen($directory . '/' . $name, 'x');
    if (!$handle || fwrite($handle, $contents) !== strlen($contents)) { throw new RuntimeException('Key creation failed. Inspect private storage before retrying.'); }
    fclose($handle);
    chmod($directory . '/' . $name, 0600);
}
echo "OAuth keys created in private storage. Preserve them with the installation backup; no credentials were printed.\n";
