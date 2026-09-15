<?php
declare(strict_types=1);

return [
    'ALTER TABLE pl_journal_lines ADD UNIQUE KEY uq_lines_bank_scope (id, company_id, book_id, account_id)',
    <<<'SQL'
CREATE TABLE pl_bank_statements (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    account_id BIGINT UNSIGNED NOT NULL,
    reference VARCHAR(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    opening_balance DECIMAL(20,4) NOT NULL,
    closing_balance DECIMAL(20,4) NOT NULL,
    baseline_date DATE NOT NULL,
    baseline_balance DECIMAL(30,4) NOT NULL,
    baseline_confirmed TINYINT(1) NOT NULL,
    previous_statement_id BIGINT UNSIGNED NULL,
    import_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    payload_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    status ENUM('draft','completed') NOT NULL DEFAULT 'draft',
    revision INT UNSIGNED NOT NULL DEFAULT 1,
    imported_by BIGINT UNSIGNED NOT NULL,
    imported_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_by BIGINT UNSIGNED NULL,
    completed_at DATETIME NULL,
    completion_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NULL,
    ledger_balance DECIMAL(30,4) NULL,
    outstanding_balance DECIMAL(30,4) NULL,
    UNIQUE KEY uq_bank_statement_scope (id, company_id, book_id, account_id),
    UNIQUE KEY uq_bank_statement_ref (book_id, account_id, reference),
    UNIQUE KEY uq_bank_statement_request (book_id, import_key),
    KEY ix_bank_statement_dates (company_id, book_id, account_id, end_date),
    CONSTRAINT ck_bank_statement_dates CHECK (start_date <= end_date AND baseline_date <= start_date),
    CONSTRAINT fk_bank_statement_account FOREIGN KEY (account_id, company_id, book_id) REFERENCES pl_accounts (id, company_id, book_id),
    CONSTRAINT fk_bank_statement_previous FOREIGN KEY (previous_statement_id, company_id, book_id, account_id) REFERENCES pl_bank_statements (id, company_id, book_id, account_id),
    CONSTRAINT fk_bank_statement_importer FOREIGN KEY (imported_by) REFERENCES pl_users (id),
    CONSTRAINT fk_bank_statement_completer FOREIGN KEY (completed_by) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_bank_statement_rows (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    statement_id BIGINT UNSIGNED NOT NULL,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    account_id BIGINT UNSIGNED NOT NULL,
    line_number SMALLINT UNSIGNED NOT NULL,
    transaction_date DATE NOT NULL,
    reference VARCHAR(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
    description VARCHAR(500) NOT NULL,
    money_in DECIMAL(20,4) NOT NULL,
    money_out DECIMAL(20,4) NOT NULL,
    UNIQUE KEY uq_bank_line_number (statement_id, line_number),
    UNIQUE KEY uq_bank_row_reference (book_id, account_id, reference),
    UNIQUE KEY uq_bank_row_scope (id, company_id, book_id, account_id),
    CONSTRAINT ck_bank_row_amount CHECK ((money_in > 0 AND money_out = 0) OR (money_out > 0 AND money_in = 0)),
    CONSTRAINT fk_bank_row_statement FOREIGN KEY (statement_id, company_id, book_id, account_id) REFERENCES pl_bank_statements (id, company_id, book_id, account_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_bank_matches (
    row_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    account_id BIGINT UNSIGNED NOT NULL,
    journal_line_id BIGINT UNSIGNED NOT NULL,
    matched_by BIGINT UNSIGNED NOT NULL,
    matched_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_bank_match_line (journal_line_id),
    CONSTRAINT fk_bank_match_row FOREIGN KEY (row_id, company_id, book_id, account_id) REFERENCES pl_bank_statement_rows (id, company_id, book_id, account_id),
    CONSTRAINT fk_bank_match_line FOREIGN KEY (journal_line_id, company_id, book_id, account_id) REFERENCES pl_journal_lines (id, company_id, book_id, account_id),
    CONSTRAINT fk_bank_match_user FOREIGN KEY (matched_by) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_bank_match_events (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    row_id BIGINT UNSIGNED NOT NULL,
    action ENUM('match','unmatch') NOT NULL,
    journal_line_id BIGINT UNSIGNED NOT NULL,
    actor_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bank_event_row FOREIGN KEY (row_id) REFERENCES pl_bank_statement_rows (id),
    CONSTRAINT fk_bank_event_line FOREIGN KEY (journal_line_id) REFERENCES pl_journal_lines (id),
    CONSTRAINT fk_bank_event_actor FOREIGN KEY (actor_id) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    "CREATE TRIGGER pl_bank_rows_no_update BEFORE UPDATE ON pl_bank_statement_rows FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Imported bank rows are immutable'",
    "CREATE TRIGGER pl_bank_rows_no_delete BEFORE DELETE ON pl_bank_statement_rows FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Imported bank rows are retained'",
    <<<'SQL'
CREATE TRIGGER pl_bank_statement_guard BEFORE UPDATE ON pl_bank_statements FOR EACH ROW
BEGIN
    IF OLD.status = 'completed' OR NOT (NEW.company_id = OLD.company_id AND NEW.book_id = OLD.book_id AND NEW.account_id = OLD.account_id
        AND BINARY NEW.reference = BINARY OLD.reference AND NEW.start_date = OLD.start_date AND NEW.end_date = OLD.end_date
        AND NEW.opening_balance = OLD.opening_balance AND NEW.closing_balance = OLD.closing_balance
        AND NEW.baseline_date = OLD.baseline_date AND NEW.baseline_balance = OLD.baseline_balance AND NEW.baseline_confirmed = OLD.baseline_confirmed
        AND (NEW.previous_statement_id <=> OLD.previous_statement_id) AND NEW.import_key = OLD.import_key AND NEW.payload_hash = OLD.payload_hash
        AND NEW.imported_by = OLD.imported_by AND NEW.imported_at = OLD.imported_at) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Imported statement content and completed history are immutable';
    END IF;
END
SQL,
    "CREATE TRIGGER pl_bank_statement_no_delete BEFORE DELETE ON pl_bank_statements FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Bank statement history is retained'",
    "CREATE TRIGGER pl_bank_events_no_update BEFORE UPDATE ON pl_bank_match_events FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Bank match history is immutable'",
    "CREATE TRIGGER pl_bank_events_no_delete BEFORE DELETE ON pl_bank_match_events FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Bank match history is retained'",
    <<<'SQL'
CREATE TRIGGER pl_bank_match_insert_guard BEFORE INSERT ON pl_bank_matches FOR EACH ROW
BEGIN
    IF EXISTS (SELECT 1 FROM pl_bank_statement_rows r JOIN pl_bank_statements s ON s.id = r.statement_id WHERE r.id = NEW.row_id AND s.status = 'completed') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Completed statement matches are immutable';
    END IF;
END
SQL,
    "CREATE TRIGGER pl_bank_matches_no_update BEFORE UPDATE ON pl_bank_matches FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Remove and recreate draft matches with an audit event'",
    <<<'SQL'
CREATE TRIGGER pl_bank_match_delete_guard BEFORE DELETE ON pl_bank_matches FOR EACH ROW
BEGIN
    IF EXISTS (SELECT 1 FROM pl_bank_statement_rows r JOIN pl_bank_statements s ON s.id = r.statement_id WHERE r.id = OLD.row_id AND s.status = 'completed') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Completed statement matches are immutable';
    END IF;
END
SQL,
    <<<'SQL'
CREATE TRIGGER pl_bank_row_insert_guard BEFORE INSERT ON pl_bank_statement_rows FOR EACH ROW
BEGIN
    IF EXISTS (SELECT 1 FROM pl_bank_statements WHERE id = NEW.statement_id AND status = 'completed') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Completed statement rows are immutable';
    END IF;
END
SQL,
];
