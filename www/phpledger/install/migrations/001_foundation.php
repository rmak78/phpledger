<?php
declare(strict_types=1);

// Fresh-install foundation. Deliberately contains no historical data or DROP statements.
return [
    <<<'SQL'
CREATE TABLE pl_users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(254) NOT NULL,
    display_name VARCHAR(120) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_login_attempts (
    subject_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
    window_started_at DATETIME NOT NULL,
    attempt_count INT UNSIGNED NOT NULL,
    blocked_until DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_companies (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(160) NOT NULL,
    currency CHAR(3) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    start_date DATE NOT NULL,
    fiscal_year_end CHAR(5) CHARACTER SET ascii NOT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_companies_creator FOREIGN KEY (created_by) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_company_members (
    company_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    role ENUM('owner','accountant','viewer') NOT NULL,
    PRIMARY KEY (company_id, user_id),
    CONSTRAINT fk_members_company FOREIGN KEY (company_id) REFERENCES pl_companies (id),
    CONSTRAINT fk_members_user FOREIGN KEY (user_id) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_books (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(120) NOT NULL,
    UNIQUE KEY uq_books_company (company_id),
    UNIQUE KEY uq_books_scope (id, company_id),
    CONSTRAINT fk_books_company FOREIGN KEY (company_id) REFERENCES pl_companies (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_accounts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(20) NOT NULL,
    name VARCHAR(120) NOT NULL,
    type ENUM('asset','liability','equity','income','expense') NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_accounts_code (book_id, code),
    UNIQUE KEY uq_accounts_scope (id, company_id, book_id),
    CONSTRAINT fk_accounts_book FOREIGN KEY (book_id, company_id) REFERENCES pl_books (id, company_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_periods (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('open','closed') NOT NULL DEFAULT 'open',
    UNIQUE KEY uq_periods_start (book_id, start_date),
    UNIQUE KEY uq_periods_scope (id, company_id, book_id),
    CONSTRAINT ck_period_dates CHECK (end_date >= start_date),
    CONSTRAINT fk_periods_book FOREIGN KEY (book_id, company_id) REFERENCES pl_books (id, company_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_journals (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    period_id BIGINT UNSIGNED NOT NULL,
    journal_date DATE NOT NULL,
    currency CHAR(3) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    description VARCHAR(500) NOT NULL,
    source_type VARCHAR(40) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    source_reference VARCHAR(120) NOT NULL,
    idempotency_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    payload_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    reversal_of_id BIGINT UNSIGNED NULL,
    posted_by BIGINT UNSIGNED NOT NULL,
    posted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_journals_request (book_id, idempotency_key),
    UNIQUE KEY uq_journals_reversal (reversal_of_id),
    UNIQUE KEY uq_journals_scope (id, company_id, book_id),
    KEY ix_journals_report (book_id, journal_date, id),
    CONSTRAINT fk_journals_book FOREIGN KEY (book_id, company_id) REFERENCES pl_books (id, company_id),
    CONSTRAINT fk_journals_period FOREIGN KEY (period_id, company_id, book_id) REFERENCES pl_periods (id, company_id, book_id),
    CONSTRAINT fk_journals_reversal FOREIGN KEY (reversal_of_id, company_id, book_id) REFERENCES pl_journals (id, company_id, book_id),
    CONSTRAINT fk_journals_user FOREIGN KEY (posted_by) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_journal_lines (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    journal_id BIGINT UNSIGNED NOT NULL,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    line_number SMALLINT UNSIGNED NOT NULL,
    account_id BIGINT UNSIGNED NOT NULL,
    description VARCHAR(500) NOT NULL,
    debit DECIMAL(20,4) NOT NULL,
    credit DECIMAL(20,4) NOT NULL,
    UNIQUE KEY uq_lines_number (journal_id, line_number),
    CONSTRAINT ck_lines_amount CHECK ((debit > 0 AND credit = 0) OR (credit > 0 AND debit = 0)),
    CONSTRAINT fk_lines_journal FOREIGN KEY (journal_id, company_id, book_id) REFERENCES pl_journals (id, company_id, book_id),
    CONSTRAINT fk_lines_account FOREIGN KEY (account_id, company_id, book_id) REFERENCES pl_accounts (id, company_id, book_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    "CREATE TRIGGER pl_journals_no_update BEFORE UPDATE ON pl_journals FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted journals are immutable; use a linked reversal'",
    "CREATE TRIGGER pl_journals_no_delete BEFORE DELETE ON pl_journals FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted journals are immutable; use a linked reversal'",
    "CREATE TRIGGER pl_lines_no_update BEFORE UPDATE ON pl_journal_lines FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted journal lines are immutable; use a linked reversal'",
    "CREATE TRIGGER pl_lines_no_delete BEFORE DELETE ON pl_journal_lines FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted journal lines are immutable; use a linked reversal'",
];
