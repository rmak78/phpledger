<?php
declare(strict_types=1);

// Machine grants reuse existing users and company/book membership. No financial writes.
return [
    <<<'SQL'
CREATE TABLE pl_connection_clients (
    client_id VARCHAR(512) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    redirect_uris JSON NOT NULL,
    source ENUM('registered','metadata','personal') NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NULL,
    revoked_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_connections (
    id CHAR(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
    actor_id BIGINT UNSIGNED NOT NULL,
    client_id VARCHAR(512) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    name VARCHAR(120) NOT NULL,
    kind ENUM('personal','oauth') NOT NULL,
    oauth_scopes VARCHAR(80) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    resource VARCHAR(512) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    demo_generation CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    revoked_at DATETIME NULL,
    KEY ix_connections_actor (actor_id, expires_at),
    CONSTRAINT fk_connection_actor FOREIGN KEY (actor_id) REFERENCES pl_users (id),
    CONSTRAINT fk_connection_client FOREIGN KEY (client_id) REFERENCES pl_connection_clients (client_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_connection_books (
    connection_id CHAR(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    company_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (connection_id, company_id, book_id),
    KEY ix_connection_company (company_id, connection_id),
    CONSTRAINT fk_connection_scope FOREIGN KEY (connection_id) REFERENCES pl_connections (id),
    CONSTRAINT fk_connection_book FOREIGN KEY (book_id, company_id) REFERENCES pl_books (id, company_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_connection_tokens (
    token_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
    connection_id CHAR(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    kind ENUM('personal','access','refresh','code') NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    revoked_at DATETIME NULL,
    KEY ix_connection_tokens (connection_id, kind),
    CONSTRAINT fk_connection_token FOREIGN KEY (connection_id) REFERENCES pl_connections (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_connection_rates (
    subject_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
    window_started_at DATETIME NOT NULL,
    attempt_count INT UNSIGNED NOT NULL,
    KEY ix_connection_rate_age (window_started_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_connection_audit (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    connection_id CHAR(32) CHARACTER SET ascii COLLATE ascii_bin NULL,
    actor_id BIGINT UNSIGNED NULL,
    action VARCHAR(60) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    outcome VARCHAR(30) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY ix_connection_audit (connection_id, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_connection_sessions (
    session_id CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
    connection_id CHAR(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    state MEDIUMTEXT NOT NULL,
    expires_at DATETIME NOT NULL,
    KEY ix_connection_session (connection_id, expires_at),
    CONSTRAINT fk_connection_session FOREIGN KEY (connection_id) REFERENCES pl_connections (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
];
