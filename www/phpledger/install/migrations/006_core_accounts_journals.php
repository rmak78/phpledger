<?php
declare(strict_types=1);

// Additive core workflow metadata; existing account identities and journals are preserved.
return [
    "ALTER TABLE pl_accounts ADD revision INT UNSIGNED NOT NULL DEFAULT 1, ADD creation_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NULL, ADD creation_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NULL, ADD UNIQUE KEY uq_account_creation (book_id, creation_key), ADD CONSTRAINT ck_account_revision CHECK (revision > 0)",
    <<<'SQL'
CREATE TABLE pl_general_drafts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    document_date DATE NOT NULL,
    reference VARCHAR(120) NOT NULL,
    description VARCHAR(500) NOT NULL,
    `lines` JSON NOT NULL,
    revision INT UNSIGNED NOT NULL DEFAULT 1,
    creation_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    creation_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    journal_id BIGINT UNSIGNED NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    updated_by BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_general_creation (book_id, creation_key),
    UNIQUE KEY uq_general_journal (journal_id),
    KEY ix_general_list (book_id, document_date, id),
    CONSTRAINT ck_general_revision CHECK (revision > 0),
    CONSTRAINT fk_general_book FOREIGN KEY (book_id, company_id) REFERENCES pl_books (id, company_id),
    CONSTRAINT fk_general_journal FOREIGN KEY (journal_id, company_id, book_id) REFERENCES pl_journals (id, company_id, book_id),
    CONSTRAINT fk_general_creator FOREIGN KEY (created_by) REFERENCES pl_users (id),
    CONSTRAINT fk_general_updater FOREIGN KEY (updated_by) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_core_audit (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    actor_id BIGINT UNSIGNED NOT NULL,
    entity_type ENUM('account','general_journal') NOT NULL,
    entity_id BIGINT UNSIGNED NOT NULL,
    action VARCHAR(40) NOT NULL,
    reason VARCHAR(500) NOT NULL,
    before_state JSON NULL,
    after_state JSON NOT NULL,
    recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY ix_core_audit_entity (book_id, entity_type, entity_id, id),
    CONSTRAINT fk_core_audit_book FOREIGN KEY (book_id, company_id) REFERENCES pl_books (id, company_id),
    CONSTRAINT fk_core_audit_actor FOREIGN KEY (actor_id) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    "CREATE TRIGGER pl_general_no_posted_update BEFORE UPDATE ON pl_general_drafts FOR EACH ROW BEGIN IF OLD.journal_id IS NOT NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted general journals are immutable'; END IF; END",
    "CREATE TRIGGER pl_general_no_posted_delete BEFORE DELETE ON pl_general_drafts FOR EACH ROW BEGIN IF OLD.journal_id IS NOT NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted general journals cannot be deleted'; END IF; END",
    "CREATE TRIGGER pl_core_audit_no_update BEFORE UPDATE ON pl_core_audit FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Core audit records are immutable'",
    "CREATE TRIGGER pl_core_audit_no_delete BEFORE DELETE ON pl_core_audit FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Core audit records cannot be deleted'",
];
