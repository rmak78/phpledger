<?php
declare(strict_types=1);

// Empty in ordinary installations. Only the isolated demo reset CLI initializes generation 1.
return [
    <<<'SQL'
CREATE TABLE pl_demo_state (
    id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
    generation CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    reset_at DATETIME NOT NULL,
    next_reset_at DATETIME NOT NULL,
    CONSTRAINT ck_demo_singleton CHECK (id = 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_demo_visitors (
    user_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    generation CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_demo_company (company_id),
    CONSTRAINT fk_demo_user FOREIGN KEY (user_id) REFERENCES pl_users (id),
    CONSTRAINT fk_demo_company FOREIGN KEY (company_id) REFERENCES pl_companies (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
];
