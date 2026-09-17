<?php
declare(strict_types=1);

// Effective receipt/expense views join an enum kind to an ASCII source type.
// Put source_id before that type comparison so the join does not scan a book's
// entire posting identity history for every source row. Existing uniqueness stays.
return [
    'ALTER TABLE pl_posting_identities ADD KEY ix_posting_source_lookup (book_id, company_id, source_id, source_type)',
];
