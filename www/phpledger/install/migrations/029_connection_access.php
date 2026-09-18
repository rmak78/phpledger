<?php
declare(strict_types=1);

// Existing credentials retain their previously granted read access. New browser
// grants choose report-only by default; no posted accounting data is changed.
return [
    "ALTER TABLE pl_connections ADD access_mode ENUM('reports','full') NOT NULL DEFAULT 'full'",
];
