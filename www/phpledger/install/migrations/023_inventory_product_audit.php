<?php
declare(strict_types=1);

// Extend the existing immutable audit stream; preserve every earlier migration receipt.
return ["ALTER TABLE pl_core_audit MODIFY entity_type ENUM('account','general_journal','product') NOT NULL"];
