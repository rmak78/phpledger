<?php
declare(strict_types=1);

// 017 was expanded during the 0.5 development cycle after the 0.4 preview
// had already been installed. This migration upgrades that exact legacy shape
// without rewriting posted documents, journals or the original receipt.
$hasCompletedUpgrade = (int) DB::queryFirstField(
    "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'pl_ar_document_revisions'"
) > 0;

if ($hasCompletedUpgrade) {
    return ['SELECT 1'];
}

return [
    "ALTER TABLE pl_ar_documents MODIFY kind ENUM('quote','invoice','bill','customer_credit','supplier_credit') NOT NULL, MODIFY status ENUM('draft','sent','accepted','declined','expired','cancelled','posted','corrected','reversed') NOT NULL DEFAULT 'draft', ADD COLUMN original_document_id BIGINT UNSIGNED NULL AFTER converted_invoice_id, ADD COLUMN rounding_account_id BIGINT UNSIGNED NULL AFTER original_document_id",
    'ALTER TABLE pl_ar_documents DROP INDEX uq_ar_document_item, ADD KEY ix_ar_document_item (open_item_id), ADD CONSTRAINT fk_ar_document_rounding FOREIGN KEY (rounding_account_id, company_id, book_id) REFERENCES pl_accounts (id, company_id, book_id), ADD CONSTRAINT fk_ar_document_original FOREIGN KEY (original_document_id, company_id, book_id) REFERENCES pl_ar_documents (id, company_id, book_id)',
    'ALTER TABLE pl_ar_document_lines ADD COLUMN account_id BIGINT UNSIGNED NULL AFTER line_total, ADD COLUMN product_id BIGINT UNSIGNED NULL AFTER account_id, ADD CONSTRAINT fk_ar_line_account FOREIGN KEY (account_id, company_id, book_id) REFERENCES pl_accounts (id, company_id, book_id)',
    "ALTER TABLE pl_ar_document_events MODIFY from_status ENUM('draft','sent','accepted','declined','expired','cancelled','posted','corrected','reversed') NULL, MODIFY to_status ENUM('draft','sent','accepted','declined','expired','cancelled','posted','corrected','reversed') NOT NULL",
    <<<'SQL'
CREATE TABLE pl_ar_document_revisions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    document_id BIGINT UNSIGNED NOT NULL,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    revision INT UNSIGNED NOT NULL,
    journal_id BIGINT UNSIGNED NOT NULL,
    open_item_id BIGINT UNSIGNED NOT NULL,
    source_snapshot JSON NOT NULL,
    actor_id BIGINT UNSIGNED NOT NULL,
    reason VARCHAR(500) NOT NULL,
    recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_ar_revision (document_id, revision),
    UNIQUE KEY uq_ar_revision_journal (journal_id),
    CONSTRAINT fk_ar_revision_document FOREIGN KEY (document_id, company_id, book_id) REFERENCES pl_ar_documents (id, company_id, book_id),
    CONSTRAINT fk_ar_revision_journal FOREIGN KEY (journal_id, company_id, book_id) REFERENCES pl_journals (id, company_id, book_id),
    CONSTRAINT fk_ar_revision_item FOREIGN KEY (open_item_id, company_id, book_id) REFERENCES pl_open_items (id, company_id, book_id),
    CONSTRAINT fk_ar_revision_actor FOREIGN KEY (actor_id) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    "CREATE TRIGGER pl_ar_revisions_no_update BEFORE UPDATE ON pl_ar_document_revisions FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted document revisions are immutable'",
    "CREATE TRIGGER pl_ar_revisions_no_delete BEFORE DELETE ON pl_ar_document_revisions FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted document revisions are immutable'",
    "DROP TRIGGER pl_ar_lines_no_posted_update",
    "CREATE TRIGGER pl_ar_lines_no_posted_insert BEFORE INSERT ON pl_ar_document_lines FOR EACH ROW BEGIN IF EXISTS (SELECT 1 FROM pl_ar_documents d WHERE d.id = NEW.document_id AND d.journal_id IS NOT NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted document lines are immutable'; END IF; END",
    "CREATE TRIGGER pl_ar_lines_no_posted_update BEFORE UPDATE ON pl_ar_document_lines FOR EACH ROW BEGIN IF EXISTS (SELECT 1 FROM pl_ar_documents d WHERE d.id IN (OLD.document_id, NEW.document_id) AND d.journal_id IS NOT NULL) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted document lines are immutable'; END IF; END",
];
