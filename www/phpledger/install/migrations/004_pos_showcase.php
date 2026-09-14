<?php
declare(strict_types=1);

return [
    <<<'SQL'
CREATE TABLE pl_pos_sales (
    document_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    checkout_key VARCHAR(96) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    request_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    currency CHAR(3) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    total DECIMAL(20,4) NOT NULL,
    cash_received DECIMAL(20,4) NOT NULL,
    change_due DECIMAL(20,4) NOT NULL,
    catalog_id VARCHAR(100) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    catalog_version VARCHAR(40) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    catalog_digest CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    cart_snapshot JSON NOT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_pos_checkout (book_id, checkout_key),
    CONSTRAINT fk_pos_document FOREIGN KEY (document_id, company_id, book_id) REFERENCES pl_documents (id, company_id, book_id),
    CONSTRAINT fk_pos_actor FOREIGN KEY (created_by) REFERENCES pl_users (id),
    CONSTRAINT ck_pos_total CHECK (total > 0),
    CONSTRAINT ck_pos_cash CHECK (cash_received >= total AND change_due = cash_received - total)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    "CREATE TRIGGER pl_pos_sales_no_update BEFORE UPDATE ON pl_pos_sales FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted POS snapshots are immutable'",
    "CREATE TRIGGER pl_pos_sales_no_delete BEFORE DELETE ON pl_pos_sales FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted POS snapshots cannot be deleted'",
];
