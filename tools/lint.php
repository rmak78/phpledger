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
echo "PHP lint: {$count} files, {$failed} failures.\n";
exit($failed === 0 ? 0 : 1);
