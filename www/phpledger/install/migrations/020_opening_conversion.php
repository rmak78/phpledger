<?php
declare(strict_types=1);

$sql = [
    'ALTER TABLE pl_opening_documents ADD UNIQUE KEY uq_opening_document_scope (id, company_id, book_id)',
    <<<'SQL'
ALTER TABLE pl_open_item_entries
 ADD opening_document_id BIGINT UNSIGNED NULL,
 ADD allocated_amount_fc DECIMAL(20,4) NULL,
 ADD allocated_amount_base DECIMAL(20,4) NULL,
 ADD ordinary_journal_line_id BIGINT UNSIGNED GENERATED ALWAYS AS (CASE WHEN opening_document_id IS NULL THEN journal_line_id ELSE NULL END) STORED,
 ADD UNIQUE KEY uq_open_item_ordinary_line (ordinary_journal_line_id),
 ADD UNIQUE KEY uq_open_item_opening_document (opening_document_id),
 ADD CONSTRAINT fk_open_item_opening_document FOREIGN KEY (opening_document_id, company_id, book_id) REFERENCES pl_opening_documents (id, company_id, book_id),
 ADD CONSTRAINT ck_open_item_opening_allocation CHECK (
   (opening_document_id IS NULL AND allocated_amount_fc IS NULL AND allocated_amount_base IS NULL)
   OR (opening_document_id IS NOT NULL AND kind = 'recognition' AND reversal_of_id IS NULL AND allocated_amount_fc > 0 AND allocated_amount_base > 0)
 )
SQL,
    // Add the scoped FK's leading index before removing the old unique line index.
    'ALTER TABLE pl_open_item_entries ADD KEY ix_open_item_journal_line_scope (journal_line_id, company_id, book_id)',
    'ALTER TABLE pl_open_item_entries DROP INDEX uq_open_item_entry_line',
    <<<'SQL'
CREATE TABLE pl_opening_conversions (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL,
 cutover_id BIGINT UNSIGNED NOT NULL,
 request_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 payload_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 review_json JSON NOT NULL, result_json JSON NOT NULL,
 reason VARCHAR(500) NOT NULL, actor_id BIGINT UNSIGNED NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_opening_conversion_cutover (cutover_id),
 UNIQUE KEY uq_opening_conversion_key (book_id, request_key),
 FOREIGN KEY (cutover_id, company_id, book_id) REFERENCES pl_opening_cutovers (id, company_id, book_id),
 FOREIGN KEY (actor_id) REFERENCES pl_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
];
foreach (['UPDATE', 'DELETE'] as $verb) {
    $sql[] = 'CREATE TRIGGER pl_opening_conversions_no_' . strtolower($verb) . " BEFORE {$verb} ON pl_opening_conversions FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Reviewed opening conversion is immutable'";
}
return $sql;
