<?php
declare(strict_types=1);

// Recover a mistaken draft import without deleting its original statement or bank references.
return [
    <<<'SQL'
ALTER TABLE pl_bank_statements
    MODIFY status ENUM('draft','completed','cancelled') NOT NULL DEFAULT 'draft',
    ADD cancelled_by BIGINT UNSIGNED NULL,
    ADD cancelled_at DATETIME NULL,
    ADD cancellation_reason VARCHAR(400) NULL,
    ADD cancellation_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NULL,
    ADD active_reference VARCHAR(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin
        GENERATED ALWAYS AS (CASE WHEN status <> 'cancelled' THEN reference ELSE NULL END) STORED,
    DROP INDEX uq_bank_statement_ref,
    ADD UNIQUE KEY uq_bank_statement_active_ref (book_id, account_id, active_reference),
    ADD CONSTRAINT fk_bank_statement_canceller FOREIGN KEY (cancelled_by) REFERENCES pl_users (id),
    ADD CONSTRAINT ck_bank_cancellation CHECK (
        (status = 'cancelled' AND cancelled_by IS NOT NULL AND cancelled_at IS NOT NULL AND cancellation_reason IS NOT NULL AND CHAR_LENGTH(cancellation_reason) > 0 AND cancellation_key IS NOT NULL)
        OR (status <> 'cancelled' AND cancelled_by IS NULL AND cancelled_at IS NULL AND cancellation_reason IS NULL AND cancellation_key IS NULL))
SQL,
    'DROP TRIGGER pl_bank_rows_no_update',
    'ALTER TABLE pl_bank_statement_rows ADD active_reference VARCHAR(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL',
    'UPDATE pl_bank_statement_rows SET active_reference = reference',
    'ALTER TABLE pl_bank_statement_rows DROP INDEX uq_bank_row_reference, ADD UNIQUE KEY uq_bank_row_active_ref (book_id, account_id, active_reference)',
    <<<'SQL'
CREATE TRIGGER pl_bank_rows_no_update BEFORE UPDATE ON pl_bank_statement_rows FOR EACH ROW
BEGIN
    IF NOT (NEW.id = OLD.id AND NEW.statement_id = OLD.statement_id AND NEW.company_id = OLD.company_id
        AND NEW.book_id = OLD.book_id AND NEW.account_id = OLD.account_id AND NEW.line_number = OLD.line_number
        AND NEW.transaction_date = OLD.transaction_date AND BINARY NEW.reference = BINARY OLD.reference
        AND BINARY NEW.description = BINARY OLD.description AND NEW.money_in = OLD.money_in AND NEW.money_out = OLD.money_out
        AND NEW.active_reference IS NULL AND OLD.active_reference IS NOT NULL
        AND EXISTS (SELECT 1 FROM pl_bank_statements WHERE id = OLD.statement_id AND status = 'cancelled')) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Imported bank rows are immutable; only cancelled reference claims may be released';
    END IF;
END
SQL,
    'DROP TRIGGER pl_bank_statement_guard',
    <<<'SQL'
CREATE TRIGGER pl_bank_statement_guard BEFORE UPDATE ON pl_bank_statements FOR EACH ROW
BEGIN
    IF OLD.status <> 'draft' OR NOT (NEW.id = OLD.id AND NEW.company_id = OLD.company_id AND NEW.book_id = OLD.book_id AND NEW.account_id = OLD.account_id
        AND BINARY NEW.reference = BINARY OLD.reference AND NEW.start_date = OLD.start_date AND NEW.end_date = OLD.end_date
        AND NEW.opening_balance = OLD.opening_balance AND NEW.closing_balance = OLD.closing_balance
        AND NEW.baseline_date = OLD.baseline_date AND NEW.baseline_balance = OLD.baseline_balance AND NEW.baseline_confirmed = OLD.baseline_confirmed
        AND (NEW.previous_statement_id <=> OLD.previous_statement_id) AND NEW.import_key = OLD.import_key AND NEW.payload_hash = OLD.payload_hash
        AND NEW.imported_by = OLD.imported_by AND NEW.imported_at = OLD.imported_at) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Imported statement content and terminal history are immutable';
    END IF;
    IF NEW.status = 'cancelled' AND EXISTS (SELECT 1 FROM pl_bank_statement_rows r JOIN pl_bank_matches m ON m.row_id = r.id WHERE r.statement_id = OLD.id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Remove all draft matches before cancelling this statement';
    END IF;
END
SQL,
    <<<'SQL'
CREATE TRIGGER pl_bank_statement_insert_guard BEFORE INSERT ON pl_bank_statements FOR EACH ROW
BEGIN
    IF NEW.status <> 'draft' OR NEW.revision <> 1 OR NEW.completed_by IS NOT NULL OR NEW.completed_at IS NOT NULL
        OR NEW.completion_key IS NOT NULL OR NEW.ledger_balance IS NOT NULL OR NEW.outstanding_balance IS NOT NULL
        OR NEW.cancelled_by IS NOT NULL OR NEW.cancelled_at IS NOT NULL OR NEW.cancellation_reason IS NOT NULL OR NEW.cancellation_key IS NOT NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Bank statements must be imported as new drafts';
    END IF;
END
SQL,
    'DROP TRIGGER pl_bank_match_insert_guard',
    <<<'SQL'
CREATE TRIGGER pl_bank_match_insert_guard BEFORE INSERT ON pl_bank_matches FOR EACH ROW
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pl_bank_statement_rows r JOIN pl_bank_statements s ON s.id = r.statement_id WHERE r.id = NEW.row_id AND s.status = 'draft') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Only draft statement rows can be matched';
    END IF;
END
SQL,
    'DROP TRIGGER pl_bank_row_insert_guard',
    <<<'SQL'
CREATE TRIGGER pl_bank_row_insert_guard BEFORE INSERT ON pl_bank_statement_rows FOR EACH ROW
BEGIN
    IF NEW.active_reference IS NULL OR BINARY NEW.active_reference <> BINARY NEW.reference
        OR NOT EXISTS (SELECT 1 FROM pl_bank_statements WHERE id = NEW.statement_id AND status = 'draft') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'New bank rows require a draft statement and their original active reference';
    END IF;
END
SQL,
];
