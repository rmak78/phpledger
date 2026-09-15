<?php
declare(strict_types=1);

return [
    'ALTER TABLE pl_periods ADD COLUMN revision INT UNSIGNED NOT NULL DEFAULT 1',
    <<<'SQL'
CREATE TABLE pl_period_actions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    period_id BIGINT UNSIGNED NOT NULL,
    action ENUM('create','close','reopen') NOT NULL,
    prior_status ENUM('open','closed') NULL,
    resulting_status ENUM('open','closed') NOT NULL,
    resulting_revision INT UNSIGNED NOT NULL,
    reason VARCHAR(500) NOT NULL,
    actor_id BIGINT UNSIGNED NOT NULL,
    request_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    payload_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    result_json JSON NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_period_actions_request (book_id, request_key),
    KEY ix_period_actions_history (book_id, id),
    CONSTRAINT fk_period_actions_period FOREIGN KEY (period_id, company_id, book_id) REFERENCES pl_periods (id, company_id, book_id),
    CONSTRAINT fk_period_actions_actor FOREIGN KEY (actor_id) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    "CREATE TRIGGER pl_period_actions_no_update BEFORE UPDATE ON pl_period_actions FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Period action history is immutable'",
    "CREATE TRIGGER pl_period_actions_no_delete BEFORE DELETE ON pl_period_actions FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Period action history is immutable'",
    "CREATE TRIGGER pl_demo_period_actions_no_insert BEFORE INSERT ON pl_period_actions FOR EACH ROW BEGIN IF EXISTS (SELECT 1 FROM pl_demo_state WHERE id = 1) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Period administration is disabled in the public demo'; END IF; END",
];
