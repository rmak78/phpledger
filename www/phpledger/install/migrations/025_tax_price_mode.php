<?php
declare(strict_types=1);

return [
    <<<'SQL'
CREATE TABLE pl_tax_settings (
 book_id BIGINT UNSIGNED NOT NULL PRIMARY KEY, company_id BIGINT UNSIGNED NOT NULL,
 price_mode ENUM('exclusive','inclusive') NOT NULL DEFAULT 'exclusive', revision INT UNSIGNED NOT NULL,
 updated_by BIGINT UNSIGNED NOT NULL,
 CONSTRAINT ck_tax_setting_revision CHECK(revision>0),
 CONSTRAINT fk_tax_setting_book FOREIGN KEY(book_id,company_id) REFERENCES pl_books(id,company_id),
 CONSTRAINT fk_tax_setting_actor FOREIGN KEY(updated_by) REFERENCES pl_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_tax_setting_actions (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL,
 actor_id BIGINT UNSIGNED NOT NULL, reason VARCHAR(500) NOT NULL,
 request_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL, payload_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 result_json JSON NOT NULL, recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_tax_setting_request(book_id,request_key),
 CONSTRAINT fk_tax_setting_action_book FOREIGN KEY(book_id,company_id) REFERENCES pl_books(id,company_id),
 CONSTRAINT fk_tax_setting_action_actor FOREIGN KEY(actor_id) REFERENCES pl_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    "ALTER TABLE pl_ar_documents ADD price_mode ENUM('exclusive','inclusive') NOT NULL DEFAULT 'exclusive'",
    "CREATE TRIGGER pl_tax_setting_actions_no_update BEFORE UPDATE ON pl_tax_setting_actions FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Tax setting history is immutable'",
    "CREATE TRIGGER pl_tax_setting_actions_no_delete BEFORE DELETE ON pl_tax_setting_actions FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Tax setting history is immutable'",
];
