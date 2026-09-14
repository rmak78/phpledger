<?php
declare(strict_types=1);

// Additive upgrade: prior foundation companies require an explicit chart/opening review.
return [
    "ALTER TABLE pl_companies ADD is_sample TINYINT(1) NOT NULL DEFAULT 0, ADD setup_status ENUM('ready','opening_required','review_required') NOT NULL DEFAULT 'review_required', ADD setup_request_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NULL, ADD setup_payload_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NULL, ADD UNIQUE KEY uq_company_setup_request (created_by, setup_request_key)",
    "ALTER TABLE pl_accounts ADD semantic_key VARCHAR(100) CHARACTER SET ascii COLLATE ascii_bin NULL, ADD role VARCHAR(40) CHARACTER SET ascii COLLATE ascii_bin NULL, ADD UNIQUE KEY uq_account_semantic (book_id, semantic_key)",
    <<<'SQL'
CREATE TABLE pl_template_installations (
    company_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
    book_id BIGINT UNSIGNED NOT NULL,
    template_id VARCHAR(100) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    template_version VARCHAR(40) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    template_digest CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    snapshot JSON NOT NULL,
    confirmed_by BIGINT UNSIGNED NOT NULL,
    confirmed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_template_book FOREIGN KEY (book_id, company_id) REFERENCES pl_books (id, company_id),
    CONSTRAINT fk_template_actor FOREIGN KEY (confirmed_by) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_documents (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    kind ENUM('receipt','expense') NOT NULL,
    document_date DATE NOT NULL,
    amount DECIMAL(20,4) NOT NULL,
    money_account_id BIGINT UNSIGNED NOT NULL,
    category_account_id BIGINT UNSIGNED NOT NULL,
    counterparty VARCHAR(160) NOT NULL,
    reference VARCHAR(120) NOT NULL,
    memo VARCHAR(500) NOT NULL,
    revision INT UNSIGNED NOT NULL DEFAULT 1,
    creation_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    creation_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    journal_id BIGINT UNSIGNED NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    updated_by BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_documents_creation (book_id, creation_key),
    UNIQUE KEY uq_documents_journal (journal_id),
    UNIQUE KEY uq_documents_scope (id, company_id, book_id),
    KEY ix_documents_list (book_id, document_date, id),
    CONSTRAINT ck_document_amount CHECK (amount > 0),
    CONSTRAINT ck_document_revision CHECK (revision > 0),
    CONSTRAINT ck_document_accounts CHECK (money_account_id <> category_account_id),
    CONSTRAINT fk_document_book FOREIGN KEY (book_id, company_id) REFERENCES pl_books (id, company_id),
    CONSTRAINT fk_document_money FOREIGN KEY (money_account_id, company_id, book_id) REFERENCES pl_accounts (id, company_id, book_id),
    CONSTRAINT fk_document_category FOREIGN KEY (category_account_id, company_id, book_id) REFERENCES pl_accounts (id, company_id, book_id),
    CONSTRAINT fk_document_journal FOREIGN KEY (journal_id, company_id, book_id) REFERENCES pl_journals (id, company_id, book_id),
    CONSTRAINT fk_document_creator FOREIGN KEY (created_by) REFERENCES pl_users (id),
    CONSTRAINT fk_document_updater FOREIGN KEY (updated_by) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    "CREATE TRIGGER pl_documents_no_posted_update BEFORE UPDATE ON pl_documents FOR EACH ROW BEGIN IF OLD.journal_id IS NOT NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted source documents are immutable; use a linked reversal'; END IF; END",
    "CREATE TRIGGER pl_documents_no_posted_delete BEFORE DELETE ON pl_documents FOR EACH ROW BEGIN IF OLD.journal_id IS NOT NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted source documents cannot be deleted'; END IF; END",
];
