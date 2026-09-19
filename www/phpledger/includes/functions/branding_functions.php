<?php
declare(strict_types=1);

/*
 * The optional logo chosen during installation. It is shown in the app header and on the
 * sign-in page, stored in the database and served by /logo. Only PNG, JPEG and WebP are
 * accepted: SVG can carry scripts, and the file type is checked from its contents.
 */

const PL_LOGO_MAX_BYTES = 1048576;

/** @return array<int, string> */
function pl_logo_types(): array
{
    return [IMAGETYPE_PNG => 'image/png', IMAGETYPE_JPEG => 'image/jpeg', IMAGETYPE_WEBP => 'image/webp'];
}

/**
 * Read an optional upload from $_FILES; null when no file was chosen.
 *
 * @return array{bytes: string, media_type: string, width: int, height: int}|null
 */
function pl_logo_from_upload(mixed $file): ?array
{
    if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $path = $file['tmp_name'] ?? null;
    if (($file['error'] ?? null) !== UPLOAD_ERR_OK || !is_string($path) || !is_uploaded_file($path)) {
        throw new InvalidArgumentException('The logo could not be uploaded. Choose a PNG, JPEG or WebP image up to 1 MB.');
    }
    $bytes = file_get_contents($path);
    return pl_logo_validate($bytes === false ? '' : $bytes);
}

/** @return array{bytes: string, media_type: string, width: int, height: int} */
function pl_logo_validate(string $bytes): array
{
    if ($bytes === '' || strlen($bytes) > PL_LOGO_MAX_BYTES) {
        throw new InvalidArgumentException('Choose a logo image up to 1 MB.');
    }
    $info = @getimagesizefromstring($bytes);
    $types = pl_logo_types();
    $type = is_array($info) ? (int) $info[2] : 0;
    $detected = (new finfo(FILEINFO_MIME_TYPE))->buffer($bytes);
    if (!isset($types[$type]) || $detected !== $types[$type]) {
        throw new InvalidArgumentException('Use a PNG, JPEG or WebP image for the logo. SVG and other formats are not accepted.');
    }
    $width = (int) $info[0];
    $height = (int) $info[1];
    if ($width < 16 || $height < 16 || $width > 4000 || $height > 4000) {
        throw new InvalidArgumentException('Use a logo between 16 and 4,000 pixels on each side.');
    }
    if ($width > $height * 6) {
        throw new InvalidArgumentException('Use a logo no more than six times wider than it is tall, so it fits the menu.');
    }
    return ['bytes' => $bytes, 'media_type' => $types[$type], 'width' => $width, 'height' => $height];
}

/** Replace the logo. Bytes travel as hexadecimal so no character-set conversion can alter them. */
function pl_logo_save(array $logo, int $actorId): void
{
    DB::query('INSERT INTO pl_installation_assets (name, media_type, width, height, content, sha256, updated_by) VALUES (%s, %s, %i, %i, UNHEX(%s), %s, %i) '
        . 'ON DUPLICATE KEY UPDATE media_type = VALUES(media_type), width = VALUES(width), height = VALUES(height), content = VALUES(content), sha256 = VALUES(sha256), updated_by = VALUES(updated_by)',
        'logo', $logo['media_type'], $logo['width'], $logo['height'], bin2hex($logo['bytes']), hash('sha256', $logo['bytes']), $actorId);
}

/**
 * The current logo's details for templates (no image bytes), cached for the request.
 * Any database problem simply shows the PHP Ledger logo instead.
 *
 * @return array{media_type: string, width: int, height: int, sha256: string}|null
 */
function pl_logo_current(): ?array
{
    static $logo = false;
    if ($logo !== false) {
        return $logo;
    }
    try {
        $row = class_exists('DB', false) ? DB::queryFirstRow("SELECT media_type, width, height, sha256 FROM pl_installation_assets WHERE name = 'logo'") : null;
    } catch (Throwable $error) {
        $row = null;
    }
    return $logo = is_array($row) ? ['media_type' => (string) $row['media_type'], 'width' => (int) $row['width'], 'height' => (int) $row['height'], 'sha256' => (string) $row['sha256']] : null;
}

/** Address of the logo image; the version changes whenever the image does, so browsers may cache it. */
function pl_logo_url(array $logo): string
{
    return pl_url('/logo', ['v' => substr($logo['sha256'], 0, 16)]);
}

/** Serve the logo image; it appears on the sign-in page, so no session is needed. */
function pl_logo_http(): never
{
    $logo = DB::queryFirstRow("SELECT media_type, content, sha256 FROM pl_installation_assets WHERE name = 'logo'");
    header_remove('Set-Cookie');
    header('X-Content-Type-Options: nosniff');
    header("Content-Security-Policy: default-src 'none'; sandbox");
    if (!is_array($logo) || !in_array($logo['media_type'], pl_logo_types(), true)) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=utf-8');
        header('Cache-Control: no-store');
        echo 'No logo has been added.';
        exit;
    }
    $etag = '"' . $logo['sha256'] . '"';
    header('Content-Type: ' . $logo['media_type']);
    header('Cache-Control: public, max-age=86400');
    header('ETag: ' . $etag);
    if (trim((string) ($_SERVER['HTTP_IF_NONE_MATCH'] ?? '')) === $etag) {
        http_response_code(304);
        exit;
    }
    header('Content-Length: ' . strlen((string) $logo['content']));
    echo $logo['content'];
    exit;
}
