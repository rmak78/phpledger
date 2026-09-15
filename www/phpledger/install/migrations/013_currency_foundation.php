<?php
declare(strict_types=1);

// Stop application/cron writers and back up populated installations before this upgrade.
// An interrupted receipt deliberately requires inspection or backup restoration.
$owner = "COALESCE(IS_USED_LOCK(CONCAT('phpledger:migrate:', LEFT(SHA2(DATABASE(), 256), 40))), 0) = CONNECTION_ID()";
$statements = [];
foreach (['pl_journals', 'pl_journal_lines'] as $table) {
    $statements[] = "CREATE TRIGGER {$table}_currency_upgrade_insert BEFORE INSERT ON {$table} FOR EACH ROW BEGIN IF NOT ({$owner}) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Currency upgrade in progress; posting is unavailable'; END IF; END";
}
$statements[] = "ALTER TABLE pl_companies ADD functional_currency CHAR(3) CHARACTER SET ascii COLLATE ascii_bin NULL, ADD presentation_currency CHAR(3) CHARACTER SET ascii COLLATE ascii_bin NULL, ADD group_id BIGINT UNSIGNED NULL, ADD parent_entity_id BIGINT UNSIGNED NULL, ADD ownership_pct DECIMAL(7,4) NULL, ADD consolidation_method ENUM('full','equity','combined','excluded') NULL, ADD CONSTRAINT fk_company_parent FOREIGN KEY (parent_entity_id) REFERENCES pl_companies(id), ADD CONSTRAINT ck_company_ownership CHECK (ownership_pct IS NULL OR ownership_pct BETWEEN 0 AND 100)";
$statements[] = 'UPDATE pl_companies SET functional_currency = currency, presentation_currency = currency';
$statements[] = 'ALTER TABLE pl_companies MODIFY functional_currency CHAR(3) CHARACTER SET ascii COLLATE ascii_bin NOT NULL, MODIFY presentation_currency CHAR(3) CHARACTER SET ascii COLLATE ascii_bin NOT NULL, ADD CONSTRAINT ck_company_currency CHECK (currency = functional_currency)';
$statements[] = "CREATE TRIGGER pl_company_functional_immutable BEFORE UPDATE ON pl_companies FOR EACH ROW BEGIN IF NEW.parent_entity_id = OLD.id THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'A company cannot be its own parent'; END IF; IF NOT (OLD.functional_currency <=> NEW.functional_currency) OR NOT (OLD.currency <=> NEW.currency) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Functional currency is immutable'; END IF; END";
$statements[] = 'ALTER TABLE pl_books ADD functional_currency CHAR(3) CHARACTER SET ascii COLLATE ascii_bin NULL, ADD presentation_currency CHAR(3) CHARACTER SET ascii COLLATE ascii_bin NULL';
$statements[] = 'UPDATE pl_books b JOIN pl_companies c ON c.id = b.company_id SET b.functional_currency = c.functional_currency, b.presentation_currency = c.presentation_currency';
$statements[] = 'ALTER TABLE pl_books MODIFY functional_currency CHAR(3) CHARACTER SET ascii COLLATE ascii_bin NOT NULL, MODIFY presentation_currency CHAR(3) CHARACTER SET ascii COLLATE ascii_bin NOT NULL';
$statements[] = "CREATE TRIGGER pl_book_functional_immutable BEFORE UPDATE ON pl_books FOR EACH ROW BEGIN IF NOT (OLD.functional_currency <=> NEW.functional_currency) OR NOT (OLD.company_id <=> NEW.company_id) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Book functional currency and company are immutable'; END IF; END";
$statements[] = "CREATE TRIGGER pl_book_currency_insert BEFORE INSERT ON pl_books FOR EACH ROW BEGIN IF NOT (NEW.functional_currency <=> (SELECT functional_currency FROM pl_companies WHERE id = NEW.company_id)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Book currency must match its company'; END IF; END";
$statements[] = "ALTER TABLE pl_accounts ADD currency CHAR(3) CHARACTER SET ascii COLLATE ascii_bin NULL, ADD is_monetary TINYINT(1) NULL, ADD revaluation_account_id BIGINT UNSIGNED NULL, ADD group_account_id BIGINT UNSIGNED NULL, ADD CONSTRAINT ck_account_monetary CHECK (is_monetary IS NULL OR is_monetary IN (0,1)), ADD CONSTRAINT fk_account_revaluation FOREIGN KEY (revaluation_account_id,company_id,book_id) REFERENCES pl_accounts(id,company_id,book_id)";
$statements[] = "UPDATE pl_accounts SET is_monetary = CASE WHEN role IN ('cash_bank','receivables','payables') THEN 1 WHEN role IN ('owner_equity','income','expense') THEN 0 ELSE NULL END";
$statements[] = <<<'SQL'
CREATE TABLE pl_currency_rates (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL,
 from_currency CHAR(3) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 to_currency CHAR(3) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 rate_date DATE NOT NULL, rate_type ENUM('spot','actual','closing','average','tax','documentary') NOT NULL,
 rate DECIMAL(28,12) NOT NULL, source VARCHAR(120) NOT NULL,
 revision INT UNSIGNED NOT NULL, supersedes_id BIGINT UNSIGNED NULL,
 fetched_at DATETIME NULL, entered_by BIGINT UNSIGNED NOT NULL,
 entered_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, note VARCHAR(500) NOT NULL,
 idempotency_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 payload_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 UNIQUE KEY uq_rate_revision(book_id,from_currency,to_currency,rate_date,rate_type,source,revision),
 UNIQUE KEY uq_rate_request(book_id,idempotency_key), UNIQUE KEY uq_rate_scope(id,company_id,book_id),
 UNIQUE KEY uq_rate_correction(supersedes_id),
 CONSTRAINT fk_rate_book FOREIGN KEY(book_id,company_id) REFERENCES pl_books(id,company_id),
 CONSTRAINT fk_rate_actor FOREIGN KEY(entered_by) REFERENCES pl_users(id),
 CONSTRAINT fk_rate_previous FOREIGN KEY(supersedes_id,company_id,book_id) REFERENCES pl_currency_rates(id,company_id,book_id),
 CONSTRAINT ck_rate_positive CHECK(rate > 0 AND revision > 0 AND from_currency <> to_currency)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL;
foreach (['UPDATE', 'DELETE'] as $action) {
    $statements[] = 'CREATE TRIGGER pl_rates_no_' . strtolower($action) . " BEFORE {$action} ON pl_currency_rates FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Currency rates are append-only'";
}
$statements[] = "ALTER TABLE pl_journal_lines ADD currency CHAR(3) CHARACTER SET ascii COLLATE ascii_bin NULL, ADD amount_fc DECIMAL(20,4) NULL, ADD rate DECIMAL(28,12) NULL, ADD rate_type ENUM('spot','actual','closing','average','tax','documentary') NULL, ADD rate_source_id BIGINT UNSIGNED NULL, ADD amount_base DECIMAL(20,4) NULL, ADD rate_is_stale TINYINT(1) NULL, ADD ic_counterparty_entity_id BIGINT UNSIGNED NULL";
$unchanged = implode(' AND ', array_map(static fn(string $field): string => "(OLD.{$field} <=> NEW.{$field})", ['id','journal_id','company_id','book_id','line_number','account_id','description','debit','credit']));
$statements[] = "CREATE TRIGGER pl_lines_currency_backfill BEFORE UPDATE ON pl_journal_lines FOR EACH ROW BEGIN IF NOT COALESCE((({$owner}) AND EXISTS (SELECT 1 FROM pl_schema_migrations WHERE version = '013_currency_foundation' AND status = 'applying') AND {$unchanged} AND OLD.currency IS NULL AND OLD.amount_fc IS NULL AND OLD.rate IS NULL AND OLD.rate_type IS NULL AND OLD.amount_base IS NULL AND OLD.rate_is_stale IS NULL AND OLD.rate_source_id IS NULL AND OLD.ic_counterparty_entity_id IS NULL AND NEW.currency = (SELECT currency FROM pl_journals WHERE id = OLD.journal_id) AND NEW.amount_fc = OLD.debit + OLD.credit AND NEW.amount_base = OLD.debit + OLD.credit AND NEW.rate = 1 AND NEW.rate_type = 'spot' AND NEW.rate_is_stale = 0 AND NEW.rate_source_id IS NULL AND NEW.ic_counterparty_entity_id IS NULL), 0) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Only initial domestic currency metadata may be backfilled'; END IF; END";
$statements[] = 'DROP TRIGGER pl_lines_no_update';
$statements[] = "UPDATE pl_journal_lines l JOIN pl_journals j ON j.id = l.journal_id SET l.currency = j.currency, l.amount_fc = l.debit + l.credit, l.amount_base = l.debit + l.credit, l.rate = 1, l.rate_type = 'spot', l.rate_is_stale = 0";
$statements[] = "CREATE TRIGGER pl_lines_no_update BEFORE UPDATE ON pl_journal_lines FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posted journal lines are immutable; use a linked reversal'";
$statements[] = 'DROP TRIGGER pl_lines_currency_backfill';
$statements[] = "ALTER TABLE pl_journal_lines MODIFY currency CHAR(3) CHARACTER SET ascii COLLATE ascii_bin NOT NULL, MODIFY amount_fc DECIMAL(20,4) NOT NULL, MODIFY rate DECIMAL(28,12) NOT NULL, MODIFY rate_type ENUM('spot','actual','closing','average','tax','documentary') NOT NULL, MODIFY amount_base DECIMAL(20,4) NOT NULL, MODIFY rate_is_stale TINYINT(1) NOT NULL, ADD CONSTRAINT ck_line_fx CHECK(amount_fc > 0 AND rate > 0 AND amount_base = debit + credit AND rate_is_stale IN (0,1)), ADD CONSTRAINT fk_line_rate FOREIGN KEY(rate_source_id,company_id,book_id) REFERENCES pl_currency_rates(id,company_id,book_id), ADD CONSTRAINT fk_line_ic FOREIGN KEY(ic_counterparty_entity_id) REFERENCES pl_companies(id), ADD CONSTRAINT ck_line_ic CHECK(ic_counterparty_entity_id IS NULL OR ic_counterparty_entity_id <> company_id)";
foreach (['pl_journals', 'pl_journal_lines'] as $table) { $statements[] = "DROP TRIGGER {$table}_currency_upgrade_insert"; }
return $statements;
