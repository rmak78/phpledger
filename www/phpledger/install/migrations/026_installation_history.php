<?php
declare(strict_types=1);

// Additive upgrade: installation evidence is append-only. The existing
// pl_template_installations row remains the current chart snapshot for
// compatibility; sample receipts are recorded as separate immutable facts.
return [
    <<<'SQL'
CREATE TABLE pl_template_installation_history (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    snapshot_kind ENUM('chart','sample') NOT NULL,
    template_id VARCHAR(100) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    template_version VARCHAR(40) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    template_digest CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    snapshot JSON NOT NULL,
    snapshot_digest CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    confirmed_by BIGINT UNSIGNED NOT NULL,
    recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_installation_history_snapshot (company_id, book_id, snapshot_kind, snapshot_digest),
    KEY ix_installation_history_latest (company_id, book_id, snapshot_kind, id),
    CONSTRAINT fk_installation_history_book FOREIGN KEY (book_id, company_id) REFERENCES pl_books (id, company_id),
    CONSTRAINT fk_installation_history_actor FOREIGN KEY (confirmed_by) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
INSERT INTO pl_template_installation_history
    (company_id, book_id, snapshot_kind, template_id, template_version, template_digest, snapshot, snapshot_digest, confirmed_by)
SELECT company_id, book_id, 'chart', template_id, template_version, template_digest, snapshot,
       SHA2(CAST(snapshot AS CHAR), 256), confirmed_by
FROM pl_template_installations
SQL,
    <<<'SQL'
INSERT INTO pl_template_installation_history
    (company_id, book_id, snapshot_kind, template_id, template_version, template_digest, snapshot, snapshot_digest, confirmed_by)
SELECT company_id, book_id, 'sample', template_id, template_version, template_digest, snapshot,
       SHA2(CAST(JSON_EXTRACT(snapshot, '$.sample_pack') AS CHAR), 256), confirmed_by
FROM pl_template_installations
WHERE JSON_EXTRACT(snapshot, '$.sample_pack') IS NOT NULL
SQL,
    "CREATE TRIGGER pl_template_installation_history_no_update BEFORE UPDATE ON pl_template_installation_history FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Installation history is immutable'",
    "CREATE TRIGGER pl_template_installation_history_no_delete BEFORE DELETE ON pl_template_installation_history FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Installation history is immutable'",
];
