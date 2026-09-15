<?php
declare(strict_types=1);

// Installation does not activate optional modules or modify their historical financial records.
return [
    <<<'SQL'
CREATE TABLE pl_company_modules (
    company_id BIGINT UNSIGNED NOT NULL,
    module_id VARCHAR(60) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    enabled TINYINT(1) NOT NULL,
    version VARCHAR(30) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    manifest_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    revision INT UNSIGNED NOT NULL,
    updated_by BIGINT UNSIGNED NOT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (company_id, module_id),
    CONSTRAINT ck_module_state CHECK (enabled IN (0,1) AND revision > 0),
    CONSTRAINT fk_module_company FOREIGN KEY (company_id) REFERENCES pl_companies (id),
    CONSTRAINT fk_module_actor FOREIGN KEY (updated_by) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_module_actions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    module_id VARCHAR(60) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    actor_id BIGINT UNSIGNED NOT NULL,
    request_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    payload_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    action ENUM('enabled','disabled','upgraded') NOT NULL,
    reason VARCHAR(500) NOT NULL,
    before_state JSON NULL,
    result_json JSON NOT NULL,
    recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_module_request (company_id, request_key),
    KEY ix_module_history (company_id, id),
    CONSTRAINT fk_module_action_company FOREIGN KEY (company_id) REFERENCES pl_companies (id),
    CONSTRAINT fk_module_action_actor FOREIGN KEY (actor_id) REFERENCES pl_users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    "CREATE TRIGGER pl_module_actions_no_update BEFORE UPDATE ON pl_module_actions FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Module action history is immutable'",
    "CREATE TRIGGER pl_module_actions_no_delete BEFORE DELETE ON pl_module_actions FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Module action history cannot be deleted'",
];
