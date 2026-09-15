<?php
declare(strict_types=1);

$sql = [
    <<<'SQL'
CREATE TABLE pl_products (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL,
 sku VARCHAR(60) NOT NULL, name VARCHAR(160) NOT NULL,
 kind ENUM('stock','nonstock') NOT NULL, base_unit VARCHAR(40) NOT NULL,
 inventory_account_id BIGINT UNSIGNED NULL, cogs_account_id BIGINT UNSIGNED NULL,
 sales_account_id BIGINT UNSIGNED NOT NULL, purchase_account_id BIGINT UNSIGNED NOT NULL,
 selling_price DECIMAL(20,4) NOT NULL DEFAULT 0,
 is_active BOOLEAN NOT NULL DEFAULT TRUE, revision INT UNSIGNED NOT NULL DEFAULT 1,
 created_by BIGINT UNSIGNED NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_product_sku (company_id, sku),
 UNIQUE KEY uq_product_scope (id, company_id, book_id),
 FOREIGN KEY (book_id, company_id) REFERENCES pl_books(id, company_id),
 FOREIGN KEY (inventory_account_id, company_id, book_id) REFERENCES pl_accounts(id, company_id, book_id),
 FOREIGN KEY (cogs_account_id, company_id, book_id) REFERENCES pl_accounts(id, company_id, book_id),
 FOREIGN KEY (sales_account_id, company_id, book_id) REFERENCES pl_accounts(id, company_id, book_id),
 FOREIGN KEY (purchase_account_id, company_id, book_id) REFERENCES pl_accounts(id, company_id, book_id),
 FOREIGN KEY (created_by) REFERENCES pl_users(id),
 CONSTRAINT ck_product_accounts CHECK ((kind='stock' AND inventory_account_id IS NOT NULL AND cogs_account_id IS NOT NULL) OR (kind='nonstock' AND inventory_account_id IS NULL AND cogs_account_id IS NULL)),
 CONSTRAINT ck_product_price CHECK (selling_price >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_inventory_movements (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL, product_id BIGINT UNSIGNED NOT NULL,
 movement_date DATE NOT NULL, kind ENUM('receipt','issue','customer_return','purchase_return','adjustment','value_adjustment','opening') NOT NULL,
 quantity_delta DECIMAL(20,4) NOT NULL, value_delta DECIMAL(20,4) NOT NULL,
 inventory_account_id BIGINT UNSIGNED NOT NULL, offset_account_id BIGINT UNSIGNED NULL,
 journal_id BIGINT UNSIGNED NULL, original_movement_id BIGINT UNSIGNED NULL,
 opening_journal_line_id BIGINT UNSIGNED NULL,
 source_type VARCHAR(40) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 source_reference VARCHAR(120) NOT NULL,
 source_document_id BIGINT UNSIGNED NULL, source_journal_id BIGINT UNSIGNED NULL,
 reason VARCHAR(500) NOT NULL, created_by BIGINT UNSIGNED NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_stock_movement_scope (id, company_id, book_id),
 UNIQUE KEY uq_stock_journal (journal_id),
 UNIQUE KEY uq_stock_source (book_id, source_type, source_reference),
 KEY ix_stock_history (book_id, product_id, movement_date, id),
 FOREIGN KEY (product_id, company_id, book_id) REFERENCES pl_products(id, company_id, book_id),
 FOREIGN KEY (inventory_account_id, company_id, book_id) REFERENCES pl_accounts(id, company_id, book_id),
 FOREIGN KEY (offset_account_id, company_id, book_id) REFERENCES pl_accounts(id, company_id, book_id),
 FOREIGN KEY (journal_id, company_id, book_id) REFERENCES pl_journals(id, company_id, book_id),
 FOREIGN KEY (source_journal_id, company_id, book_id) REFERENCES pl_journals(id, company_id, book_id),
 FOREIGN KEY (original_movement_id, company_id, book_id) REFERENCES pl_inventory_movements(id, company_id, book_id),
 FOREIGN KEY (opening_journal_line_id, company_id, book_id) REFERENCES pl_journal_lines(id, company_id, book_id),
 FOREIGN KEY (created_by) REFERENCES pl_users(id),
 CONSTRAINT ck_stock_nonempty CHECK (quantity_delta <> 0 OR value_delta <> 0),
 CONSTRAINT ck_stock_opening CHECK ((kind='opening' AND opening_journal_line_id IS NOT NULL AND journal_id IS NULL) OR (kind<>'opening' AND opening_journal_line_id IS NULL)),
 CONSTRAINT ck_stock_journal CHECK (kind='opening' OR value_delta=0 OR journal_id IS NOT NULL)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_inventory_commands (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL,
 request_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 payload_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 result_json JSON NOT NULL, actor_id BIGINT UNSIGNED NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_stock_command (book_id, request_key),
 FOREIGN KEY (book_id, company_id) REFERENCES pl_books(id, company_id),
 FOREIGN KEY (actor_id) REFERENCES pl_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_inventory_opening_previews (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL,
 payload_json JSON NOT NULL, payload_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 created_by BIGINT UNSIGNED NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_stock_opening_preview_scope (id, company_id, book_id),
 FOREIGN KEY (book_id, company_id) REFERENCES pl_books(id, company_id),
 FOREIGN KEY (created_by) REFERENCES pl_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
];
foreach (['pl_inventory_movements','pl_inventory_commands','pl_inventory_opening_previews'] as $table) {
    foreach (['UPDATE','DELETE'] as $verb) {
        $sql[] = "CREATE TRIGGER {$table}_no_" . strtolower($verb) . " BEFORE {$verb} ON {$table} FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Inventory history is immutable'";
    }
}
return $sql;
