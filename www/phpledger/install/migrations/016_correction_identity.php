<?php
declare(strict_types=1);

// Business identity and source snapshots are independent of immutable ledger posting IDs.
$sql = [
    <<<'SQL'
CREATE TABLE pl_posting_identities (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL,
 source_type VARCHAR(40) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 source_id BIGINT UNSIGNED NOT NULL,
 original_journal_id BIGINT UNSIGNED NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_posting_identity (book_id, source_type, source_id),
 UNIQUE KEY uq_posting_original (original_journal_id),
 UNIQUE KEY uq_posting_identity_scope (id, company_id, book_id),
 CONSTRAINT fk_posting_identity_book FOREIGN KEY (book_id, company_id) REFERENCES pl_books(id, company_id),
 CONSTRAINT fk_posting_identity_journal FOREIGN KEY (original_journal_id, company_id, book_id) REFERENCES pl_journals(id, company_id, book_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_posting_revisions (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 identity_id BIGINT UNSIGNED NOT NULL,
 company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL,
 revision INT UNSIGNED NOT NULL,
 journal_id BIGINT UNSIGNED NOT NULL,
 source_snapshot JSON NOT NULL,
 actor_id BIGINT UNSIGNED NOT NULL,
 recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_posting_revision (identity_id, revision),
 UNIQUE KEY uq_posting_revision_journal (journal_id),
 CONSTRAINT ck_posting_revision CHECK (revision > 0),
 CONSTRAINT fk_posting_revision_identity FOREIGN KEY (identity_id, company_id, book_id) REFERENCES pl_posting_identities(id, company_id, book_id),
 CONSTRAINT fk_posting_revision_journal FOREIGN KEY (journal_id, company_id, book_id) REFERENCES pl_journals(id, company_id, book_id),
 CONSTRAINT fk_posting_revision_actor FOREIGN KEY (actor_id) REFERENCES pl_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
    <<<'SQL'
CREATE TABLE pl_correction_actions (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL,
 identity_id BIGINT UNSIGNED NOT NULL,
 request_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 payload_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 actor_id BIGINT UNSIGNED NOT NULL,
 reason VARCHAR(400) NOT NULL,
 reversal_date DATE NOT NULL,
 result_json JSON NOT NULL,
 recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_correction_request (book_id, request_key),
 CONSTRAINT fk_correction_identity FOREIGN KEY (identity_id, company_id, book_id) REFERENCES pl_posting_identities(id, company_id, book_id),
 CONSTRAINT fk_correction_actor FOREIGN KEY (actor_id) REFERENCES pl_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL,
];
foreach (['pl_posting_identities', 'pl_posting_revisions', 'pl_correction_actions'] as $table) {
    foreach (['UPDATE', 'DELETE'] as $verb) {
        $sql[] = "CREATE TRIGGER {$table}_no_" . strtolower($verb) . " BEFORE {$verb} ON {$table} FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Posting identity and correction history are immutable'";
    }
}
// Views use the latest authoritative snapshot before filtering/sorting/pagination.
foreach (['documents' => ['kind','document_date','amount','money_account_id','category_account_id','counterparty','reference','memo'], 'general_drafts' => ['document_date','reference','description','lines']] as $name => $fields) {
    $type = $name === 'documents' ? 'd.kind' : "'general_journal'";
    $columns = ['d.id', 'd.company_id', 'd.book_id'];
    foreach ($fields as $field) {
        $extract = "JSON_UNQUOTE(JSON_EXTRACT(v.source_snapshot, '$.{$field}'))";
        if ($field === 'lines') { $extract = "JSON_EXTRACT(v.source_snapshot, '$.lines')"; }
        if ($field === 'amount') { $extract = "CAST({$extract} AS DECIMAL(20,4))"; }
        if (str_ends_with($field, '_id')) { $extract = "CAST({$extract} AS UNSIGNED)"; }
        if ($field === 'document_date') { $extract = "CAST({$extract} AS DATE)"; }
        $columns[] = "COALESCE({$extract}, d.`{$field}`) AS `{$field}`";
    }
    array_push($columns, 'COALESCE(v.revision,d.revision) AS revision', 'd.creation_key', 'd.creation_hash', 'COALESCE(v.journal_id,d.journal_id) AS journal_id', 'd.journal_id AS original_journal_id', 'd.created_by', 'COALESCE(v.actor_id,d.updated_by) AS updated_by', 'd.created_at', 'COALESCE(v.recorded_at,d.updated_at) AS updated_at');
    $sql[] = 'CREATE VIEW pl_effective_' . $name . ' AS SELECT ' . implode(', ', $columns)
        . " FROM pl_{$name} d LEFT JOIN pl_posting_identities i ON i.company_id=d.company_id AND i.book_id=d.book_id AND i.source_type={$type} AND i.source_id=d.id"
        . ' LEFT JOIN pl_posting_revisions v ON v.identity_id=i.id AND v.revision=(SELECT MAX(v2.revision) FROM pl_posting_revisions v2 WHERE v2.identity_id=i.id)';
}
return $sql;
