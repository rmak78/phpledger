<?php
declare(strict_types=1);

/**
 * 1.4.7 (decision register B101): maintenance has stopped for lack of funds. The Updates and privacy
 * screen, which shows the installed version and checks for releases, and the package documents say so.
 */
const PL_MAINTENANCE_NOTICE = 'Maintenance of PHP Ledger has stopped for lack of funds, so no further updates are planned. To help restart it, see https://phpledger.com/funding/.';

test('1.4.7 says maintenance has stopped on the Updates screen and at the top of the package documents', function (): void {
    $root = dirname(__DIR__);
    assert_same('1.4.7', trim((string) file_get_contents($root . '/www/phpledger/VERSION')));
    $view = (string) file_get_contents($root . '/www/phpledger/templates/views/updates.php');
    assert_true(str_contains($view, "pl_t('" . PL_MAINTENANCE_NOTICE . "')"), 'The Updates screen carries the maintenance notice through pl_t().');
    foreach (['resources/release/README.txt', 'resources/release/INSTALL.md', 'resources/release/UPGRADE.md'] as $document) {
        $head = implode("\n", array_slice(preg_split('/\R/', (string) file_get_contents($root . '/' . $document)) ?: [], 0, 4));
        assert_true(str_contains($head, PL_MAINTENANCE_NOTICE), $document . ' opens with the maintenance notice.');
    }
});
