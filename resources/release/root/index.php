<?php
declare(strict_types=1);

/*
 * PHP Ledger entry point for a copy uploaded inside a website, such as
 * public_html/accounts on shared hosting or C:\xampp\htdocs\phpledger.
 * The .htaccess file beside it sends every page here and keeps the other
 * folders private. A server whose document root is www/phpledger/public
 * never runs this file.
 */

$plRulesActive = false;
foreach (['PL_HTACCESS', 'REDIRECT_PL_HTACCESS'] as $plName) {
    $plRulesActive = $plRulesActive || ($_SERVER[$plName] ?? getenv($plName)) === '1';
}
$plFolder = rtrim(str_replace('\\', '/', dirname(str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php')))), '/.');
$plFolderValid = $plFolder === '' || preg_match('#^(?:/[a-zA-Z0-9_~-][a-zA-Z0-9._~-]*)+$#D', $plFolder) === 1;

if (!$plRulesActive || !$plFolderValid) {
    http_response_code(503);
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');
    header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'; frame-ancestors 'none'; base-uri 'none'");
    $plMessage = !$plRulesActive
        ? 'This web server is not using the .htaccess file in the PHP Ledger folder, so PHP Ledger will not start: its private folders would not be protected. On Apache or LiteSpeed hosting, make sure the .htaccess file was uploaded and that mod_rewrite and .htaccess files are allowed. Otherwise, point the website\'s document root at the www/phpledger/public folder.'
        : 'Rename the PHP Ledger folder using only letters, numbers, dots, dashes and underscores (for example "accounts"), then open the new address.';
    echo '<!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>PHP Ledger setup</title>'
        . '<style>body{font:1rem/1.6 system-ui,sans-serif;max-width:40rem;margin:3rem auto;padding:0 1rem;color:#172336}</style>'
        . '<h1>PHP Ledger needs one setting</h1><p>' . htmlspecialchars($plMessage, ENT_QUOTES, 'UTF-8') . '</p><p>Nothing was installed or changed.</p></html>';
    exit;
}

define('PL_WEB_ADAPTER', true);
define('PL_WEB_BASE_PATH', $plFolder);
unset($plRulesActive, $plName, $plFolder, $plFolderValid);
require __DIR__ . '/www/phpledger/public/index.php';
