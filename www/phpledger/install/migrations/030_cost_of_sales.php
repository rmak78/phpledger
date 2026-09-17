<?php
declare(strict_types=1);

// Existing expense accounts remain unclassified; no history moves automatically.
return [
    "ALTER TABLE pl_accounts ADD report_classification ENUM('cost_of_sales') NULL DEFAULT NULL",
];
