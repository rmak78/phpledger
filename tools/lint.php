<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$failed = 0;
$count = 0;
foreach (['www/phpledger', 'tests', 'tools'] as $directory) {
    if (!is_dir($root . '/' . $directory)) {
        continue;
    }
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/' . $directory, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if ($file->getExtension() !== 'php' || str_contains($file->getPathname(), '/storage/')) {
            continue;
        }
        $output = [];
        exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file->getPathname()) . ' 2>&1', $output, $code);
        ++$count;
        if ($code !== 0) {
            ++$failed;
            echo implode("\n", $output) . "\n";
        }
        $output = [];
    }
}
// www/phpledger/VERSION is the single version source (docs/RELEASE-PROTOCOL.md).
$version = is_file($root . '/www/phpledger/VERSION') ? trim((string) file_get_contents($root . '/www/phpledger/VERSION')) : '';
if (!preg_match('/^(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)(?:-[0-9A-Za-z]+(?:\.[0-9A-Za-z]+)*)?$/D', $version)) {
    ++$failed;
    echo "www/phpledger/VERSION must hold one semantic version such as 1.0.0 or 1.1.0-rc.1.\n";
}
// All database access goes through MeekroDB so the engine can change (decision register B12).
$application = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/www/phpledger', FilesystemIterator::SKIP_DOTS));
foreach ($application as $file) {
    if ($file->getExtension() !== 'php' || str_contains($file->getPathname(), '/storage/')) {
        continue;
    }
    if (preg_match('/\bnew\s+\\\\?(?:PDO|mysqli)\s*\(|\bmysqli_[a-z_]+\s*\(/i', (string) file_get_contents($file->getPathname()))) {
        ++$failed;
        echo substr($file->getPathname(), strlen($root) + 1) . ": open database connections through MeekroDB, not PDO or mysqli directly.\n";
    }
}
echo "PHP lint: {$count} files, {$failed} failures.\n";
exit($failed === 0 ? 0 : 1);
