<?php
declare(strict_types=1);

return [
    <<<'SQL'
CREATE TABLE pl_opening_previews (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    cutover_date DATE NOT NULL,
    request_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    payload_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    payload JSON NOT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_opening_preview_request (book_id, request_key),
    UNIQUE KEY uq_opening_preview_scope (id, company_id, book_id),
    CONSTRAINT fk_opening_preview_book FOREIGN KEY (book_id, company_id) REFERENCES pl_books (id, company_id),
    CONSTRAINT fk_opening_preview_actor FOREIGN KEY (created_by) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_opening_cutovers (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    preview_id BIGINT UNSIGNED NOT NULL,
    journal_id BIGINT UNSIGNED NULL,
    status ENUM('confirmed','reversed') NOT NULL DEFAULT 'confirmed',
    active_book_id BIGINT UNSIGNED GENERATED ALWAYS AS (CASE WHEN status = 'confirmed' THEN book_id ELSE NULL END) STORED,
    confirmed_by BIGINT UNSIGNED NOT NULL,
    confirmed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reversed_journal_id BIGINT UNSIGNED NULL,
    reversed_by BIGINT UNSIGNED NULL,
    reversed_at DATETIME NULL,
    reversal_reason VARCHAR(400) NULL,
    UNIQUE KEY uq_opening_active_book (active_book_id),
    UNIQUE KEY uq_opening_confirm_preview (preview_id),
    UNIQUE KEY uq_opening_cutover_scope (id, company_id, book_id),
    CONSTRAINT fk_opening_cutover_preview FOREIGN KEY (preview_id, company_id, book_id) REFERENCES pl_opening_previews (id, company_id, book_id),
    CONSTRAINT fk_opening_cutover_journal FOREIGN KEY (journal_id, company_id, book_id) REFERENCES pl_journals (id, company_id, book_id),
    CONSTRAINT fk_opening_reverse_journal FOREIGN KEY (reversed_journal_id, company_id, book_id) REFERENCES pl_journals (id, company_id, book_id),
    CONSTRAINT fk_opening_confirm_actor FOREIGN KEY (confirmed_by) REFERENCES pl_users (id),
    CONSTRAINT fk_opening_reverse_actor FOREIGN KEY (reversed_by) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_opening_documents (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    cutover_id BIGINT UNSIGNED NOT NULL,
    account_id BIGINT UNSIGNED NOT NULL,
    kind ENUM('receivable','payable') NOT NULL,
    party VARCHAR(160) NOT NULL,
    reference VARCHAR(120) NOT NULL,
    document_date DATE NOT NULL,
    due_date DATE NOT NULL,
    outstanding DECIMAL(20,4) NOT NULL,
    identity_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    UNIQUE KEY uq_opening_document_identity (cutover_id, identity_hash),
    CONSTRAINT ck_opening_outstanding CHECK (outstanding > 0),
    CONSTRAINT fk_opening_document_cutover FOREIGN KEY (cutover_id, company_id, book_id) REFERENCES pl_opening_cutovers (id, company_id, book_id),
    CONSTRAINT fk_opening_document_account FOREIGN KEY (account_id, company_id, book_id) REFERENCES pl_accounts (id, company_id, book_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    "CREATE TRIGGER pl_opening_previews_no_update BEFORE UPDATE ON pl_opening_previews FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Opening previews are immutable; prepare a new preview'",
    "CREATE TRIGGER pl_opening_previews_no_delete BEFORE DELETE ON pl_opening_previews FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Opening previews retain their source evidence'",
    "CREATE TRIGGER pl_opening_documents_no_update BEFORE UPDATE ON pl_opening_documents FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Opening documents retain their confirmed cutover snapshot'",
    "CREATE TRIGGER pl_opening_documents_no_delete BEFORE DELETE ON pl_opening_documents FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Opening documents retain their confirmed cutover snapshot'",
    "CREATE TRIGGER pl_opening_cutovers_no_delete BEFORE DELETE ON pl_opening_cutovers FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cutovers retain their confirmation and reversal history'",
    <<<'SQL'
CREATE TRIGGER pl_opening_cutovers_guard BEFORE UPDATE ON pl_opening_cutovers FOR EACH ROW
BEGIN
    IF NEW.id <> OLD.id OR NEW.company_id <> OLD.company_id OR NEW.book_id <> OLD.book_id
       OR NEW.preview_id <> OLD.preview_id OR NEW.confirmed_by <> OLD.confirmed_by OR NEW.confirmed_at <> OLD.confirmed_at
       OR OLD.status = 'reversed'
       OR NOT ((OLD.journal_id IS NULL AND NEW.journal_id IS NOT NULL AND NEW.status = OLD.status
                AND NEW.reversed_by IS NULL AND NEW.reversed_at IS NULL AND NEW.reversed_journal_id IS NULL AND NEW.reversal_reason IS NULL)
          OR (NEW.journal_id <=> OLD.journal_id) AND NEW.status = 'reversed' AND NEW.reversed_by IS NOT NULL
                AND NEW.reversed_at IS NOT NULL AND NEW.reversal_reason IS NOT NULL
                AND (OLD.journal_id IS NULL OR NEW.reversed_journal_id IS NOT NULL))
    THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cutover history is immutable; use a linked reversal'; END IF;
END
SQL,
];
