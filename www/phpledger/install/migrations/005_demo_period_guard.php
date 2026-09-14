<?php
declare(strict_types=1);

// Separate additive guard preserves the already-installed 003 checksum.
return [
    "CREATE TRIGGER pl_demo_periods_no_update BEFORE UPDATE ON pl_periods FOR EACH ROW BEGIN IF EXISTS (SELECT 1 FROM pl_demo_state WHERE id = 1) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Period changes are disabled in the public demo'; END IF; END",
];
