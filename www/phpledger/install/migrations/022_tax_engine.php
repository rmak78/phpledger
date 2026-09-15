<?php
declare(strict_types=1);

$sql=[
    <<<'SQL'
CREATE TABLE pl_tax_codes (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL,
 code VARCHAR(40) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 name VARCHAR(160) NOT NULL, treatment ENUM('standard','zero','exempt') NOT NULL,
 sales_account_id BIGINT UNSIGNED NOT NULL, purchase_account_id BIGINT UNSIGNED NOT NULL,
 created_by BIGINT UNSIGNED NOT NULL, reason VARCHAR(500) NOT NULL,
 creation_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 payload_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_tax_code(book_id,code), UNIQUE KEY uq_tax_code_request(book_id,creation_key), UNIQUE KEY uq_tax_code_scope(id,company_id,book_id),
 CONSTRAINT fk_tax_code_book FOREIGN KEY(book_id,company_id) REFERENCES pl_books(id,company_id),
 CONSTRAINT fk_tax_sales_account FOREIGN KEY(sales_account_id,company_id,book_id) REFERENCES pl_accounts(id,company_id,book_id),
 CONSTRAINT fk_tax_purchase_account FOREIGN KEY(purchase_account_id,company_id,book_id) REFERENCES pl_accounts(id,company_id,book_id),
 CONSTRAINT fk_tax_code_actor FOREIGN KEY(created_by) REFERENCES pl_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_tax_rates (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 tax_code_id BIGINT UNSIGNED NOT NULL, company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL,
 effective_from DATE NOT NULL, percentage DECIMAL(9,6) NOT NULL, revision INT UNSIGNED NOT NULL,
 entered_by BIGINT UNSIGNED NOT NULL, reason VARCHAR(500) NOT NULL,
 request_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 payload_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_tax_rate_revision(tax_code_id,effective_from,revision), UNIQUE KEY uq_tax_rate_request(book_id,request_key),
 UNIQUE KEY uq_tax_rate_scope(id,company_id,book_id),
 CONSTRAINT ck_tax_percentage CHECK(percentage>=0 AND percentage<=100 AND revision>0),
 CONSTRAINT fk_tax_rate_code FOREIGN KEY(tax_code_id,company_id,book_id) REFERENCES pl_tax_codes(id,company_id,book_id),
 CONSTRAINT fk_tax_rate_actor FOREIGN KEY(entered_by) REFERENCES pl_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    "ALTER TABLE pl_ar_documents ADD tax_total DECIMAL(20,4) NOT NULL DEFAULT 0, ADD total DECIMAL(20,4) NOT NULL DEFAULT 0, ADD original_revision INT UNSIGNED NULL",
    "ALTER TABLE pl_ar_document_lines ADD tax_code_id BIGINT UNSIGNED NULL, ADD tax_rate_id BIGINT UNSIGNED NULL, ADD tax_rate DECIMAL(9,6) NOT NULL DEFAULT 0, ADD tax_amount DECIMAL(20,4) NOT NULL DEFAULT 0, ADD tax_account_id BIGINT UNSIGNED NULL, ADD tax_label VARCHAR(160) NOT NULL DEFAULT '', ADD original_line_number SMALLINT UNSIGNED NULL",
    "ALTER TABLE pl_ar_document_lines ADD CONSTRAINT fk_ar_line_tax_code FOREIGN KEY(tax_code_id,company_id,book_id) REFERENCES pl_tax_codes(id,company_id,book_id), ADD CONSTRAINT fk_ar_line_tax_rate FOREIGN KEY(tax_rate_id,company_id,book_id) REFERENCES pl_tax_rates(id,company_id,book_id), ADD CONSTRAINT fk_ar_line_tax_account FOREIGN KEY(tax_account_id,company_id,book_id) REFERENCES pl_accounts(id,company_id,book_id), ADD CONSTRAINT ck_ar_line_tax_amount CHECK(tax_amount>=0 AND tax_rate>=0 AND tax_rate<=100)"
];
foreach (['pl_tax_codes','pl_tax_rates'] as $table) {
    foreach (['UPDATE','DELETE'] as $operation) { $sql[]="CREATE TRIGGER {$table}_no_".strtolower($operation)." BEFORE {$operation} ON {$table} FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Tax configuration history is append-only'"; }
}
return $sql;
