<?php
declare(strict_types=1);

// Owner request, 19 September 2026: the installer offers an optional logo that the app shows
// in its header and on the sign-in page. Images live in the database, so ordinary database
// backups include them and no uploaded file is ever stored inside a web folder.
return [
    <<<'SQL'
CREATE TABLE pl_installation_assets (
    name VARCHAR(40) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
    media_type VARCHAR(40) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    width SMALLINT UNSIGNED NOT NULL,
    height SMALLINT UNSIGNED NOT NULL,
    content MEDIUMBLOB NOT NULL,
    sha256 CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    updated_by BIGINT UNSIGNED NOT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_installation_asset_actor FOREIGN KEY (updated_by) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
];
