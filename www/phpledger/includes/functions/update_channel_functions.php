<?php
declare(strict_types=1);

/*
 * Distribution channels and the public release feed. See docs/RELEASE-PROTOCOL.md.
 *
 * An installation's update mode says who owns its files. Only a "managed" installation
 * (release ZIP, Windows bundle) may replace its own code through the signed updater.
 * Container, Composer and panel installations are told how to upgrade on their channel
 * and are never modified in place.
 *
 * The feed at https://phpledger.com/releases/index.json is a notice, not a trust anchor:
 * the signed update metadata is still verified against the host-pinned publisher key.
 * These functions are pure; fetching the feed is a separate, opt-in step.
 */

const PL_RELEASE_FEED_URL = 'https://phpledger.com/releases/index.json';
const PL_RELEASE_FEED_SCHEMA = 1;
const PL_RELEASE_FEED_MAX_BYTES = 262144;
const PL_RELEASE_IMAGE = 'ghcr.io/phpledger/phpledger';

/** @return list<string> */
function pl_update_modes(): array
{
    return ['managed', 'container', 'composer', 'panel'];
}

/**
 * PL_UPDATE_MODE wins, then update_mode in the private configuration, then "managed".
 * An unrecognised value is refused rather than guessed, because guessing "managed"
 * would let the updater try to write into files another tool owns.
 */
function pl_update_mode(array $config = []): string
{
    $environment = getenv('PL_UPDATE_MODE');
    $value = is_string($environment) && $environment !== ''
        ? $environment
        : (isset($config['update_mode']) && is_string($config['update_mode']) ? $config['update_mode'] : 'managed');
    $value = strtolower(trim($value));
    if (!in_array($value, pl_update_modes(), true)) {
        throw new DomainException('Unknown update mode. Use managed, container, composer or panel.');
    }
    return $value;
}

function pl_update_mode_replaces_files(string $mode): bool
{
    return $mode === 'managed';
}

function pl_release_version_valid(string $version): bool
{
    return (bool) preg_match('/^(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)(?:-[0-9A-Za-z]+(?:\.[0-9A-Za-z]+)*)?$/D', $version);
}

/** Channels follow the version, never a separate stability claim. */
function pl_release_channel_for(string $version): string
{
    if (!pl_release_version_valid($version)) {
        throw new DomainException('Invalid release version.');
    }
    return str_contains($version, '-') ? 'preview' : 'stable';
}

/** Release downloads come from the project's GitHub releases or from phpledger.com. */
function pl_release_url_allowed(string $url): bool
{
    $parts = parse_url($url);
    if (!is_array($parts) || ($parts['scheme'] ?? '') !== 'https' || isset($parts['user']) || isset($parts['pass']) || isset($parts['port'])) {
        return false;
    }
    $host = strtolower((string) ($parts['host'] ?? ''));
    $path = (string) ($parts['path'] ?? '');
    if ($host === 'phpledger.com') {
        return str_starts_with($path, '/releases/');
    }
    return $host === 'github.com' && str_starts_with($path, '/phpledger/phpledger/releases/');
}

/**
 * Parse and validate the release feed. Unknown fields are ignored so the feed can grow;
 * an unknown schema is refused so an old installation never misreads a new format.
 *
 * @return array{schema: int, channels: array<string, array<string, mixed>|null>}
 */
function pl_release_feed_parse(string $json): array
{
    if (strlen($json) > PL_RELEASE_FEED_MAX_BYTES) {
        throw new DomainException('The release feed is too large.');
    }
    try {
        $feed = json_decode($json, true, 16, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        throw new DomainException('The release feed is not valid JSON.');
    }
    if (!is_array($feed) || ($feed['schema'] ?? null) !== PL_RELEASE_FEED_SCHEMA || !is_array($feed['channels'] ?? null)) {
        throw new DomainException('The release feed uses an unsupported format.');
    }
    $channels = [];
    foreach (['stable', 'preview'] as $channel) {
        $entry = $feed['channels'][$channel] ?? null;
        $channels[$channel] = $entry === null ? null : pl_release_feed_entry($entry, $channel);
    }
    return ['schema' => PL_RELEASE_FEED_SCHEMA, 'channels' => $channels];
}

/** @return array<string, mixed> */
function pl_release_feed_entry(mixed $entry, string $channel): array
{
    if (!is_array($entry)) {
        throw new DomainException('A release feed entry is malformed.');
    }
    $version = $entry['version'] ?? null;
    if (!is_string($version) || !pl_release_version_valid($version) || pl_release_channel_for($version) !== $channel) {
        throw new DomainException('A release feed entry has an invalid version for its channel.');
    }
    $published = $entry['published_at'] ?? null;
    if (!is_string($published) || !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/D', $published)) {
        throw new DomainException('A release feed entry has an invalid publication date.');
    }
    foreach (['zip', 'notes'] as $key) {
        if (!is_string($entry[$key] ?? null) || !pl_release_url_allowed($entry[$key])) {
            throw new DomainException('A release feed entry has an invalid ' . $key . ' address.');
        }
    }
    if (!is_string($entry['sha256'] ?? null) || !preg_match('/^[0-9a-f]{64}$/D', $entry['sha256'])) {
        throw new DomainException('A release feed entry has an invalid checksum.');
    }
    $updateJson = $entry['update_json'] ?? null;
    if ($updateJson !== null && (!is_string($updateJson) || !pl_release_url_allowed($updateJson))) {
        throw new DomainException('A release feed entry has an invalid signed-metadata address.');
    }
    $minPhp = $entry['min_php'] ?? null;
    if (!is_string($minPhp) || !preg_match('/^[0-9]+\.[0-9]+\.[0-9]+$/D', $minPhp)) {
        throw new DomainException('A release feed entry has an invalid PHP requirement.');
    }
    $image = $entry['image'] ?? null;
    if ($image !== null && (!is_string($image) || !preg_match('#^[a-z0-9]+(?:[._/-][a-z0-9]+)*(?::[A-Za-z0-9._-]{1,128}|@sha256:[0-9a-f]{64})$#D', $image))) {
        throw new DomainException('A release feed entry has an invalid container image.');
    }
    return [
        'version' => $version,
        'channel' => $channel,
        'published_at' => $published,
        'notes' => $entry['notes'],
        'zip' => $entry['zip'],
        'sha256' => $entry['sha256'],
        'update_json' => $updateJson,
        'min_php' => $minPhp,
        'image' => $image,
    ];
}

/**
 * The newer release on the installation's own channel, or null. A stable installation
 * is never offered a preview; a preview installation is offered either, whichever is newer.
 *
 * @param array{channels: array<string, array<string, mixed>|null>} $feed
 * @return array<string, mixed>|null
 */
function pl_release_feed_available(array $feed, string $current): ?array
{
    $candidates = [$feed['channels']['stable'] ?? null];
    if (pl_release_channel_for($current) === 'preview') {
        $candidates[] = $feed['channels']['preview'] ?? null;
    }
    $best = null;
    foreach ($candidates as $entry) {
        if (is_array($entry) && version_compare((string) $entry['version'], $current, '>')
            && ($best === null || version_compare((string) $entry['version'], (string) $best['version'], '>'))) {
            $best = $entry;
        }
    }
    return $best;
}

/**
 * Plain-language upgrade steps for an installation that must not replace its own files.
 *
 * @param array<string, mixed> $release a validated feed entry
 * @return list<string>
 */
function pl_update_mode_instructions(string $mode, array $release): array
{
    $version = (string) $release['version'];
    $backup = 'Back up the database and the private data directory first.';
    return match ($mode) {
        'managed' => [
            $backup,
            'Open the maintenance page and install ' . $version . ' with its signed update metadata.',
        ],
        'container' => [
            $backup,
            'Pull ' . (is_string($release['image'] ?? null) ? $release['image'] : PL_RELEASE_IMAGE . ':' . $version) . '.',
            'Restart the application container. It runs pending migrations when PL_AUTO_MIGRATE=1; otherwise run php www/phpledger/install/migrate.php in the container.',
        ],
        'composer' => [
            $backup,
            'Run composer create-project phpledger/phpledger:' . $version . ' into a new folder.',
            'Copy your private configuration and storage folder into it, point the web root at its www/phpledger/public folder, then run php www/phpledger/install/migrate.php.',
        ],
        'panel' => [
            $backup,
            'Use your hosting panel\'s upgrade button for PHP Ledger ' . $version . '.',
        ],
        default => throw new DomainException('Unknown update mode.'),
    };
}
