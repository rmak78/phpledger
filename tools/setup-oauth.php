<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') { exit(1); }
// The browser installer uses this same internal generator with separate host proof.
try {
    require_once dirname(__DIR__) . '/www/phpledger/includes/functions/install_oauth_functions.php';
    $directory = rtrim(getenv('PL_OAUTH_KEY_DIRECTORY') ?: dirname(__DIR__) . '/www/phpledger/storage/oauth', '/\\');
    $created = pl_install_oauth_keys($directory);
    fwrite(STDOUT, $created
        ? "OAuth keys created in private storage. Preserve them with the installation backup; no credentials were printed.\n"
        : "Existing matching OAuth keys were preserved; no credentials were printed.\n");
    exit($created ? 0 : 2);
} catch (Throwable $error) {
    fwrite(STDERR, "OAuth key setup could not complete. Check private storage and existing key integrity; existing keys were preserved.\n");
    exit(1);
}
