<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/www/phpledger/includes/functions/update_channel_functions.php';
require_once dirname(__DIR__) . '/www/phpledger/includes/functions/web_functions.php';

/** Pure checks for update modes and the release feed; no database or network. */
$checks = 0;
function channel_assert(bool $condition, string $message): void
{
    global $checks;
    if (!$condition) {
        throw new RuntimeException($message);
    }
    $checks++;
}
function channel_reject(callable $operation, string $message): void
{
    try {
        $operation();
    } catch (DomainException) {
        channel_assert(true, $message);
        return;
    }
    throw new RuntimeException($message . ' (accepted)');
}
function channel_feed(array $override = []): string
{
    $stable = [
        'version' => '1.0.0',
        'published_at' => '2026-09-18',
        'notes' => 'https://github.com/phpledger/phpledger/releases/tag/v1.0.0',
        'zip' => 'https://github.com/phpledger/phpledger/releases/download/v1.0.0/phpledger-1.0.0.zip',
        'sha256' => str_repeat('a', 64),
        'update_json' => null,
        'min_php' => '8.2.0',
        'databases' => ['mysql:8.4'],
        'image' => null,
        'min_client' => null,
        'future_field' => 'ignored',
    ];
    $feed = ['schema' => 1, 'generated_at' => '2026-09-19T00:00:00Z', 'channels' => ['stable' => $stable, 'preview' => null], 'history' => []];
    return json_encode(array_replace_recursive($feed, $override), JSON_THROW_ON_ERROR);
}

$previous = getenv('PL_UPDATE_MODE');
try {
    // Version source of truth.
    $version = pl_app_version();
    channel_assert(pl_release_version_valid($version), 'www/phpledger/VERSION does not hold a valid semantic version.');
    channel_assert($version === trim((string) file_get_contents(dirname(__DIR__) . '/www/phpledger/VERSION')), 'pl_app_version() does not read www/phpledger/VERSION.');
    channel_assert(pl_release_channel_for('1.0.0') === 'stable' && pl_release_channel_for('1.1.0-rc.1') === 'preview', 'Channel is not derived from the version.');
    channel_reject(fn () => pl_release_channel_for('1.0'), 'Short versions must be refused.');
    channel_reject(fn () => pl_release_channel_for('01.0.0'), 'Leading zeros must be refused.');

    // Update modes: environment wins, configuration next, managed by default; unknown values are refused.
    putenv('PL_UPDATE_MODE');
    channel_assert(pl_update_mode() === 'managed', 'Default mode must be managed.');
    channel_assert(pl_update_mode(['update_mode' => 'panel']) === 'panel', 'Configuration mode is ignored.');
    putenv('PL_UPDATE_MODE=Container');
    channel_assert(pl_update_mode(['update_mode' => 'panel']) === 'container', 'Environment mode must win and ignore case.');
    putenv('PL_UPDATE_MODE=containr');
    channel_reject(fn () => pl_update_mode(), 'A misspelt mode must not fall back to managed.');
    putenv('PL_UPDATE_MODE');
    channel_assert(pl_update_mode_replaces_files('managed'), 'Managed installations own their files.');
    foreach (['container', 'composer', 'panel'] as $mode) {
        channel_assert(!pl_update_mode_replaces_files($mode), $mode . ' installations must never be modified in place.');
    }

    // Feed parsing.
    $feed = pl_release_feed_parse(channel_feed());
    channel_assert($feed['channels']['stable']['version'] === '1.0.0' && $feed['channels']['preview'] === null, 'Valid feed not parsed.');
    channel_assert(!array_key_exists('future_field', $feed['channels']['stable']), 'Unknown fields must be dropped, not trusted.');
    channel_reject(fn () => pl_release_feed_parse(channel_feed(['schema' => 2])), 'An unknown schema must be refused.');
    channel_reject(fn () => pl_release_feed_parse('{'), 'Invalid JSON must be refused.');
    channel_reject(fn () => pl_release_feed_parse(str_repeat(' ', PL_RELEASE_FEED_MAX_BYTES + 1)), 'An oversized feed must be refused.');
    channel_reject(fn () => pl_release_feed_parse(channel_feed(['channels' => ['stable' => ['version' => '1.1.0-rc.1']]])), 'A preview version on the stable channel must be refused.');
    channel_reject(fn () => pl_release_feed_parse(channel_feed(['channels' => ['stable' => ['zip' => 'https://example.com/phpledger-1.0.0.zip']]])), 'Downloads from other hosts must be refused.');
    channel_reject(fn () => pl_release_feed_parse(channel_feed(['channels' => ['stable' => ['zip' => 'http://github.com/phpledger/phpledger/releases/download/v1.0.0/x.zip']]])), 'Plain HTTP downloads must be refused.');
    channel_reject(fn () => pl_release_feed_parse(channel_feed(['channels' => ['stable' => ['zip' => 'https://github.com/someone/phpledger/releases/download/v1.0.0/x.zip']]])), 'Other GitHub repositories must be refused.');
    channel_reject(fn () => pl_release_feed_parse(channel_feed(['channels' => ['stable' => ['sha256' => 'ABC']]])), 'A malformed checksum must be refused.');
    channel_reject(fn () => pl_release_feed_parse(channel_feed(['channels' => ['stable' => ['image' => 'ghcr.io/phpledger/phpledger; rm -rf /']]])), 'An unsafe image reference must be refused.');
    $withImage = pl_release_feed_parse(channel_feed(['channels' => ['stable' => ['image' => 'ghcr.io/phpledger/phpledger:1.0.0', 'update_json' => 'https://phpledger.com/releases/1.0.0.update.json']]]));
    channel_assert($withImage['channels']['stable']['image'] === 'ghcr.io/phpledger/phpledger:1.0.0', 'A valid image reference was not kept.');

    // Which release is offered.
    $both = pl_release_feed_parse(channel_feed(['channels' => [
        'stable' => ['version' => '1.0.1'],
        'preview' => ['version' => '1.1.0-rc.1', 'published_at' => '2026-10-01', 'notes' => 'https://github.com/phpledger/phpledger/releases/tag/v1.1.0-rc.1',
            'zip' => 'https://github.com/phpledger/phpledger/releases/download/v1.1.0-rc.1/phpledger-1.1.0-rc.1.zip', 'sha256' => str_repeat('b', 64), 'update_json' => null, 'min_php' => '8.2.0', 'image' => null],
    ]]));
    channel_assert(pl_release_feed_available($both, '1.0.0')['version'] === '1.0.1', 'A stable installation must see the newer stable release only.');
    channel_assert(pl_release_feed_available($both, '1.0.1') === null, 'An up-to-date stable installation must see nothing.');
    channel_assert(pl_release_feed_available($both, '1.1.0-beta.1')['version'] === '1.1.0-rc.1', 'A preview installation must see the newest release on either channel.');
    channel_assert(pl_release_feed_available($both, '1.0.2') === null, 'A newer local build must never be offered a downgrade.');

    // Instructions never tell a non-managed installation to use the in-place updater.
    $release = $withImage['channels']['stable'];
    foreach (pl_update_modes() as $mode) {
        $steps = pl_update_mode_instructions($mode, $release);
        channel_assert(count($steps) >= 2 && str_contains($steps[0], 'Back up'), $mode . ' instructions must start with a backup.');
        channel_assert(($mode === 'managed') === str_contains(implode(' ', $steps), 'maintenance page'), $mode . ' instructions point at the wrong upgrade path.');
    }
    channel_assert(str_contains(implode(' ', pl_update_mode_instructions('container', $release)), 'ghcr.io/phpledger/phpledger:1.0.0'), 'Container instructions must name the image.');
    echo "Update channel checks passed: {$checks}.\n";
} finally {
    $previous === false ? putenv('PL_UPDATE_MODE') : putenv('PL_UPDATE_MODE=' . $previous);
}
