<?php
declare(strict_types=1);

// SQL CHECK permits UNKNOWN; require both reviewed split magnitudes explicitly.
return [
    <<<'SQL'
ALTER TABLE pl_open_item_entries ADD CONSTRAINT ck_open_item_allocation_present CHECK (
 opening_document_id IS NULL OR (allocated_amount_fc IS NOT NULL AND allocated_amount_base IS NOT NULL)
)
SQL,
];
