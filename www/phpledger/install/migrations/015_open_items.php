<?php
declare(strict_types=1);

$statements = [
    'ALTER TABLE pl_journal_lines ADD UNIQUE KEY uq_line_scoped_identity (id, company_id, book_id)',
    <<<'SQL'
CREATE TABLE pl_open_item_accounts (
    account_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    activated_by BIGINT UNSIGNED NOT NULL,
    reason VARCHAR(500) NOT NULL,
    activated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_open_item_account_scope (account_id, company_id, book_id),
    FOREIGN KEY (account_id, company_id, book_id) REFERENCES pl_accounts (id, company_id, book_id),
    FOREIGN KEY (activated_by) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_open_items (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    party_id BIGINT UNSIGNED NOT NULL,
    control_account_id BIGINT UNSIGNED NOT NULL,
    direction ENUM('receivable','payable') NOT NULL,
    currency CHAR(3) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    source_reference VARCHAR(120) NOT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_open_item_scope (id, company_id, book_id),
    UNIQUE KEY uq_open_item_source (book_id, source_reference),
    FOREIGN KEY (party_id, company_id) REFERENCES pl_parties (id, company_id),
    FOREIGN KEY (control_account_id, company_id, book_id) REFERENCES pl_open_item_accounts (account_id, company_id, book_id),
    FOREIGN KEY (book_id, company_id) REFERENCES pl_books (id, company_id),
    FOREIGN KEY (created_by) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_open_item_entries (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    item_id BIGINT UNSIGNED NOT NULL,
    kind ENUM('recognition','allocation','recognition_reversal','allocation_reversal') NOT NULL,
    journal_line_id BIGINT UNSIGNED NOT NULL,
    reversal_of_id BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_open_item_entry_scope (id, company_id, book_id),
    UNIQUE KEY uq_open_item_entry_line (journal_line_id),
    UNIQUE KEY uq_open_item_entry_reversal (reversal_of_id),
    KEY ix_open_item_activity (item_id, id),
    FOREIGN KEY (item_id, company_id, book_id) REFERENCES pl_open_items (id, company_id, book_id),
    FOREIGN KEY (journal_line_id, company_id, book_id) REFERENCES pl_journal_lines (id, company_id, book_id),
    FOREIGN KEY (reversal_of_id, company_id, book_id) REFERENCES pl_open_item_entries (id, company_id, book_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_open_item_commands (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    actor_id BIGINT UNSIGNED NOT NULL,
    request_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    payload_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    result_json JSON NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_open_item_command (book_id, request_key),
    FOREIGN KEY (book_id, company_id) REFERENCES pl_books (id, company_id),
    FOREIGN KEY (actor_id) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
];
foreach (['pl_open_item_accounts', 'pl_open_items', 'pl_open_item_entries', 'pl_open_item_commands'] as $table) {
    foreach (['UPDATE', 'DELETE'] as $action) {
        $statements[] = "CREATE TRIGGER {$table}_no_" . strtolower($action) . " BEFORE {$action} ON {$table} FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Open-item accounting history is immutable'";
    }
}
return $statements;
