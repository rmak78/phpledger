<?php
declare(strict_types=1);

// Source identities stay stable; posted versions and their journals are append-only.
$sql = [
    <<<'SQL'
CREATE TABLE pl_ar_documents (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    kind ENUM('invoice','bill','customer_credit','supplier_credit') NOT NULL,
    status ENUM('draft','posted') NOT NULL DEFAULT 'draft',
    party_id BIGINT UNSIGNED NOT NULL,
    document_date DATE NOT NULL,
    due_date DATE NULL,
    currency CHAR(3) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    subtotal DECIMAL(20,4) NOT NULL,
    reference VARCHAR(120) NOT NULL,
    terms VARCHAR(500) NOT NULL DEFAULT '',
    notes TEXT NOT NULL,
    revision INT UNSIGNED NOT NULL DEFAULT 1,
    creation_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    creation_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    journal_id BIGINT UNSIGNED NULL,
    open_item_id BIGINT UNSIGNED NULL,
    original_document_id BIGINT UNSIGNED NULL,
    rounding_account_id BIGINT UNSIGNED NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    updated_by BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_ar_document_creation (book_id, creation_key),
    UNIQUE KEY uq_ar_document_journal (journal_id),
    KEY ix_ar_document_item (open_item_id),
    KEY ix_ar_document_reference (book_id, reference),
    UNIQUE KEY uq_ar_document_scope (id, company_id, book_id),
    KEY ix_ar_document_list (book_id, kind, document_date, id),
    CONSTRAINT ck_ar_document_revision CHECK (revision > 0),
    CONSTRAINT ck_ar_document_subtotal CHECK (subtotal > 0),
    CONSTRAINT ck_ar_document_dates CHECK (due_date IS NULL OR due_date >= document_date),
    CONSTRAINT fk_ar_document_book FOREIGN KEY (book_id, company_id) REFERENCES pl_books (id, company_id),
    CONSTRAINT fk_ar_document_party FOREIGN KEY (party_id, company_id) REFERENCES pl_parties (id, company_id),
    CONSTRAINT fk_ar_document_journal FOREIGN KEY (journal_id, company_id, book_id) REFERENCES pl_journals (id, company_id, book_id),
    CONSTRAINT fk_ar_document_item FOREIGN KEY (open_item_id, company_id, book_id) REFERENCES pl_open_items (id, company_id, book_id),
    CONSTRAINT fk_ar_document_rounding FOREIGN KEY (rounding_account_id, company_id, book_id) REFERENCES pl_accounts (id, company_id, book_id),
    CONSTRAINT fk_ar_document_original FOREIGN KEY (original_document_id, company_id, book_id) REFERENCES pl_ar_documents (id, company_id, book_id),
    CONSTRAINT fk_ar_document_creator FOREIGN KEY (created_by) REFERENCES pl_users (id),
    CONSTRAINT fk_ar_document_updater FOREIGN KEY (updated_by) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_ar_document_lines (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    document_id BIGINT UNSIGNED NOT NULL,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    line_number SMALLINT UNSIGNED NOT NULL,
    description VARCHAR(500) NOT NULL,
    quantity DECIMAL(20,4) NOT NULL,
    unit_price DECIMAL(20,4) NOT NULL,
    line_total DECIMAL(20,4) NOT NULL,
    account_id BIGINT UNSIGNED NULL,
    product_id BIGINT UNSIGNED NULL,
    UNIQUE KEY uq_ar_line_number (document_id, line_number),
    CONSTRAINT ck_ar_line_values CHECK (quantity > 0 AND unit_price > 0 AND line_total > 0),
    CONSTRAINT fk_ar_line_account FOREIGN KEY (account_id, company_id, book_id) REFERENCES pl_accounts (id, company_id, book_id),
    CONSTRAINT fk_ar_line_document FOREIGN KEY (document_id, company_id, book_id) REFERENCES pl_ar_documents (id, company_id, book_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_ar_document_actions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    document_id BIGINT UNSIGNED NULL,
    actor_id BIGINT UNSIGNED NOT NULL,
    action VARCHAR(40) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    request_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    payload_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    result_json JSON NOT NULL,
    recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_ar_action_request (book_id, request_key),
    CONSTRAINT fk_ar_action_book FOREIGN KEY (book_id, company_id) REFERENCES pl_books (id, company_id),
    CONSTRAINT fk_ar_action_document FOREIGN KEY (document_id, company_id, book_id) REFERENCES pl_ar_documents (id, company_id, book_id),
    CONSTRAINT fk_ar_action_actor FOREIGN KEY (actor_id) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_ar_document_events (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    document_id BIGINT UNSIGNED NOT NULL,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    from_status ENUM('draft','posted','corrected','reversed') NULL,
    to_status ENUM('draft','posted','corrected','reversed') NOT NULL,
    actor_id BIGINT UNSIGNED NOT NULL,
    reason VARCHAR(500) NOT NULL,
    recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ar_event_document FOREIGN KEY (document_id, company_id, book_id) REFERENCES pl_ar_documents (id, company_id, book_id),
    CONSTRAINT fk_ar_event_actor FOREIGN KEY (actor_id) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_ar_document_revisions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    document_id BIGINT UNSIGNED NOT NULL,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    revision INT UNSIGNED NOT NULL,
    journal_id BIGINT UNSIGNED NOT NULL,
    open_item_id BIGINT UNSIGNED NOT NULL,
    source_snapshot JSON NOT NULL,
    actor_id BIGINT UNSIGNED NOT NULL,
    reason VARCHAR(500) NOT NULL,
    recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_ar_revision (document_id, revision),
    UNIQUE KEY uq_ar_revision_journal (journal_id),
    CONSTRAINT fk_ar_revision_document FOREIGN KEY (document_id, company_id, book_id) REFERENCES pl_ar_documents (id, company_id, book_id),
    CONSTRAINT fk_ar_revision_journal FOREIGN KEY (journal_id, company_id, book_id) REFERENCES pl_journals (id, company_id, book_id),
    CONSTRAINT fk_ar_revision_item FOREIGN KEY (open_item_id, company_id, book_id) REFERENCES pl_open_items (id, company_id, book_id),
    CONSTRAINT fk_ar_revision_actor FOREIGN KEY (actor_id) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    "CREATE TRIGGER pl_ar_revisions_no_update BEFORE UPDATE ON pl_ar_document_revisions FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted document revisions are immutable'",
    "CREATE TRIGGER pl_ar_revisions_no_delete BEFORE DELETE ON pl_ar_document_revisions FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted document revisions are immutable'",
    "CREATE TRIGGER pl_ar_lines_no_posted_insert BEFORE INSERT ON pl_ar_document_lines FOR EACH ROW BEGIN IF EXISTS (SELECT 1 FROM pl_ar_documents d WHERE d.id = NEW.document_id AND d.journal_id IS NOT NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted document lines are immutable'; END IF; END",
    "CREATE TRIGGER pl_ar_documents_no_posted_update BEFORE UPDATE ON pl_ar_documents FOR EACH ROW BEGIN IF OLD.journal_id IS NOT NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted customer/vendor documents are immutable; use a linked reversal or correction'; END IF; END",
    "CREATE TRIGGER pl_ar_documents_no_posted_delete BEFORE DELETE ON pl_ar_documents FOR EACH ROW BEGIN IF OLD.journal_id IS NOT NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted customer/vendor documents cannot be deleted'; END IF; END",
    "CREATE TRIGGER pl_ar_lines_no_posted_update BEFORE UPDATE ON pl_ar_document_lines FOR EACH ROW BEGIN IF EXISTS (SELECT 1 FROM pl_ar_documents d WHERE d.id IN (OLD.document_id, NEW.document_id) AND d.journal_id IS NOT NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted document lines are immutable'; END IF; END",
    "CREATE TRIGGER pl_ar_lines_no_posted_delete BEFORE DELETE ON pl_ar_document_lines FOR EACH ROW BEGIN IF EXISTS (SELECT 1 FROM pl_ar_documents d WHERE d.id = OLD.document_id AND d.journal_id IS NOT NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted document lines cannot be deleted'; END IF; END",
    "CREATE TRIGGER pl_ar_actions_no_update BEFORE UPDATE ON pl_ar_document_actions FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Document action history is immutable'",
    "CREATE TRIGGER pl_ar_actions_no_delete BEFORE DELETE ON pl_ar_document_actions FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Document action history is immutable'",
    "CREATE TRIGGER pl_ar_events_no_update BEFORE UPDATE ON pl_ar_document_events FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Document status history is immutable'",
    "CREATE TRIGGER pl_ar_events_no_delete BEFORE DELETE ON pl_ar_document_events FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Document status history is immutable'",
];
return $sql;
