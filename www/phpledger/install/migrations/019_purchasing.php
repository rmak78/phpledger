<?php
declare(strict_types=1);

$sql = [
    <<<'SQL'
CREATE TABLE pl_purchase_orders (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL, party_id BIGINT UNSIGNED NOT NULL,
 document_date DATE NOT NULL, currency CHAR(3) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 reference VARCHAR(120) NOT NULL, notes VARCHAR(2000) NOT NULL,
 status ENUM('draft','confirmed','cancelled') NOT NULL DEFAULT 'draft', revision INT UNSIGNED NOT NULL DEFAULT 1,
 creation_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL, creation_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 created_by BIGINT UNSIGNED NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_purchase_order_scope (id, company_id, book_id), UNIQUE KEY uq_purchase_order_creation (book_id, creation_key),
 FOREIGN KEY (book_id, company_id) REFERENCES pl_books(id, company_id), FOREIGN KEY (party_id, company_id) REFERENCES pl_parties(id, company_id),
 FOREIGN KEY (created_by) REFERENCES pl_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_purchase_order_lines (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 order_id BIGINT UNSIGNED NOT NULL, company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL,
 line_number INT UNSIGNED NOT NULL, product_id BIGINT UNSIGNED NOT NULL, description VARCHAR(300) NOT NULL,
 quantity DECIMAL(20,4) NOT NULL, unit_price DECIMAL(20,4) NOT NULL,
 UNIQUE KEY uq_purchase_order_line_scope (id, company_id, book_id), UNIQUE KEY uq_purchase_order_line_number (order_id, line_number),
 CHECK (quantity > 0 AND unit_price > 0),
 FOREIGN KEY (order_id, company_id, book_id) REFERENCES pl_purchase_orders(id, company_id, book_id),
 FOREIGN KEY (product_id, company_id, book_id) REFERENCES pl_products(id, company_id, book_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_purchase_receipts (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL, order_id BIGINT UNSIGNED NOT NULL,
 document_date DATE NOT NULL, grni_account_id BIGINT UNSIGNED NOT NULL,
 currency CHAR(3) CHARACTER SET ascii COLLATE ascii_bin NOT NULL, rate DECIMAL(28,12) NOT NULL, rate_snapshot JSON NOT NULL,
 actor_id BIGINT UNSIGNED NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_purchase_receipt_scope (id, company_id, book_id),
 FOREIGN KEY (order_id, company_id, book_id) REFERENCES pl_purchase_orders(id, company_id, book_id),
 FOREIGN KEY (grni_account_id, company_id, book_id) REFERENCES pl_accounts(id, company_id, book_id), FOREIGN KEY (actor_id) REFERENCES pl_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_purchase_receipt_lines (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL, receipt_id BIGINT UNSIGNED NOT NULL, order_line_id BIGINT UNSIGNED NOT NULL,
 movement_id BIGINT UNSIGNED NOT NULL, journal_id BIGINT UNSIGNED NOT NULL,
 quantity DECIMAL(20,4) NOT NULL, amount_fc DECIMAL(20,4) NOT NULL, amount_base DECIMAL(20,4) NOT NULL,
 UNIQUE KEY uq_purchase_receipt_line_scope (id, company_id, book_id), UNIQUE KEY uq_purchase_receipt_order_line (receipt_id, order_line_id),
 UNIQUE KEY uq_purchase_receipt_movement (movement_id), CHECK (quantity > 0 AND amount_fc > 0 AND amount_base > 0),
 FOREIGN KEY (receipt_id, company_id, book_id) REFERENCES pl_purchase_receipts(id, company_id, book_id),
 FOREIGN KEY (order_line_id, company_id, book_id) REFERENCES pl_purchase_order_lines(id, company_id, book_id),
 FOREIGN KEY (journal_id, company_id, book_id) REFERENCES pl_journals(id, company_id, book_id),
 FOREIGN KEY (movement_id, company_id, book_id) REFERENCES pl_inventory_movements(id, company_id, book_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_purchase_bill_matches (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL, receipt_line_id BIGINT UNSIGNED NOT NULL,
 bill_document_id BIGINT UNSIGNED NOT NULL, bill_line_number INT UNSIGNED NOT NULL,
 quantity DECIMAL(20,4) NOT NULL, unit_price DECIMAL(20,4) NOT NULL,
 receipt_basis_base DECIMAL(20,4) NOT NULL, bill_amount_base DECIMAL(20,4) NOT NULL,
 variance_journal_id BIGINT UNSIGNED NULL, variance_account_id BIGINT UNSIGNED NULL,
 actor_id BIGINT UNSIGNED NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_purchase_match_scope (id, company_id, book_id), UNIQUE KEY uq_purchase_bill_line (bill_document_id, bill_line_number),
 CHECK (quantity > 0 AND unit_price > 0 AND receipt_basis_base > 0 AND bill_amount_base > 0),
 FOREIGN KEY (receipt_line_id, company_id, book_id) REFERENCES pl_purchase_receipt_lines(id, company_id, book_id),
 FOREIGN KEY (bill_document_id, company_id, book_id) REFERENCES pl_ar_documents(id, company_id, book_id),
 FOREIGN KEY (variance_journal_id, company_id, book_id) REFERENCES pl_journals(id, company_id, book_id),
 FOREIGN KEY (variance_account_id, company_id, book_id) REFERENCES pl_accounts(id, company_id, book_id), FOREIGN KEY (actor_id) REFERENCES pl_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_purchase_returns (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL, receipt_line_id BIGINT UNSIGNED NOT NULL,
 match_id BIGINT UNSIGNED NULL, credit_document_id BIGINT UNSIGNED NULL,
 movement_id BIGINT UNSIGNED NOT NULL, journal_id BIGINT UNSIGNED NOT NULL, variance_journal_id BIGINT UNSIGNED NULL,
 document_date DATE NOT NULL, quantity DECIMAL(20,4) NOT NULL, receipt_basis_base DECIMAL(20,4) NOT NULL,
 reason VARCHAR(500) NOT NULL, actor_id BIGINT UNSIGNED NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_purchase_return_movement (movement_id),
 CHECK (quantity > 0 AND receipt_basis_base > 0),
 FOREIGN KEY (receipt_line_id, company_id, book_id) REFERENCES pl_purchase_receipt_lines(id, company_id, book_id),
 FOREIGN KEY (match_id, company_id, book_id) REFERENCES pl_purchase_bill_matches(id, company_id, book_id),
 FOREIGN KEY (journal_id, company_id, book_id) REFERENCES pl_journals(id, company_id, book_id), FOREIGN KEY (actor_id) REFERENCES pl_users(id),
 FOREIGN KEY (credit_document_id, company_id, book_id) REFERENCES pl_ar_documents(id, company_id, book_id),
 FOREIGN KEY (movement_id, company_id, book_id) REFERENCES pl_inventory_movements(id, company_id, book_id),
 FOREIGN KEY (variance_journal_id, company_id, book_id) REFERENCES pl_journals(id, company_id, book_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_purchase_commands (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL,
 request_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL, payload_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 action VARCHAR(40) NOT NULL, result_json JSON NOT NULL, actor_id BIGINT UNSIGNED NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_purchase_command (book_id, request_key), FOREIGN KEY (book_id, company_id) REFERENCES pl_books(id, company_id), FOREIGN KEY (actor_id) REFERENCES pl_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
];
foreach (['pl_purchase_receipts', 'pl_purchase_receipt_lines', 'pl_purchase_bill_matches', 'pl_purchase_returns', 'pl_purchase_commands'] as $table) {
    foreach (['UPDATE', 'DELETE'] as $verb) { $sql[] = "CREATE TRIGGER {$table}_no_" . strtolower($verb) . " BEFORE {$verb} ON {$table} FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Purchasing history is immutable'"; }
}
$sql[] = <<<'SQL'
CREATE TRIGGER pl_purchase_orders_guard BEFORE UPDATE ON pl_purchase_orders FOR EACH ROW
BEGIN
 IF NEW.id <> OLD.id OR NEW.company_id <> OLD.company_id OR NEW.book_id <> OLD.book_id OR NEW.creation_key <> OLD.creation_key OR NEW.creation_hash <> OLD.creation_hash OR NEW.created_by <> OLD.created_by OR NEW.created_at <> OLD.created_at OR OLD.status = 'cancelled' OR NEW.revision <> OLD.revision + 1
 OR (OLD.status = 'confirmed' AND (NEW.status <> 'cancelled' OR NEW.party_id <> OLD.party_id OR NEW.document_date <> OLD.document_date OR NEW.currency <> OLD.currency OR NEW.reference <> OLD.reference OR NEW.notes <> OLD.notes))
 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Confirmed purchase orders are immutable; cancel unreceived quantities explicitly'; END IF;
END
SQL;
$sql[] = "CREATE TRIGGER pl_purchase_orders_no_delete BEFORE DELETE ON pl_purchase_orders FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Purchase order history is retained'";
foreach (['INSERT', 'UPDATE', 'DELETE'] as $verb) {
    $row = $verb === 'INSERT' ? 'NEW' : 'OLD';
    $sql[] = "CREATE TRIGGER pl_purchase_order_lines_guard_" . strtolower($verb) . " BEFORE {$verb} ON pl_purchase_order_lines FOR EACH ROW BEGIN IF (SELECT status FROM pl_purchase_orders WHERE id = {$row}.order_id) <> 'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Confirmed purchase order lines are immutable'; END IF; END";
}
return $sql;
