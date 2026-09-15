<?php
declare(strict_types=1);

return [
    <<<'SQL'
CREATE TABLE pl_company_visibility (
    company_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
    show_ar TINYINT(1) NOT NULL DEFAULT 1,
    show_ap TINYINT(1) NOT NULL DEFAULT 1,
    revision INT UNSIGNED NOT NULL,
    updated_by BIGINT UNSIGNED NOT NULL,
    CONSTRAINT ck_visibility_values CHECK (show_ar IN (0,1) AND show_ap IN (0,1) AND revision > 0),
    CONSTRAINT fk_visibility_company FOREIGN KEY (company_id) REFERENCES pl_companies(id),
    CONSTRAINT fk_visibility_actor FOREIGN KEY (updated_by) REFERENCES pl_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_visibility_actions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    actor_id BIGINT UNSIGNED NOT NULL,
    request_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    payload_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    result_json JSON NOT NULL,
    reason VARCHAR(500) NOT NULL,
    recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_visibility_request (company_id, request_key),
    CONSTRAINT fk_visibility_action_company FOREIGN KEY (company_id) REFERENCES pl_companies(id),
    CONSTRAINT fk_visibility_action_actor FOREIGN KEY (actor_id) REFERENCES pl_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    "CREATE TRIGGER pl_visibility_actions_no_update BEFORE UPDATE ON pl_visibility_actions FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Visibility history is immutable'",
    "CREATE TRIGGER pl_visibility_actions_no_delete BEFORE DELETE ON pl_visibility_actions FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Visibility history is immutable'",
];
