<?php
declare(strict_types=1);

// Historical periods are prepared before a new sample is assigned to a visitor.
// Existing demo companies stay protected regardless of the PHP provisioning scope.
// Run with web/scheduler stopped: MySQL trigger DDL is not transactional.
return [
    'DROP TRIGGER pl_demo_periods_no_update',
    "CREATE TRIGGER pl_demo_periods_no_update BEFORE UPDATE ON pl_periods FOR EACH ROW BEGIN IF EXISTS (SELECT 1 FROM pl_demo_state WHERE id = 1) AND (NOT EXISTS (SELECT 1 FROM pl_companies WHERE id = OLD.company_id AND is_sample = 1) OR EXISTS (SELECT 1 FROM pl_demo_visitors WHERE company_id = OLD.company_id)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Period changes are disabled in the public demo'; END IF; END",
    'DROP TRIGGER pl_demo_period_actions_no_insert',
    "CREATE TRIGGER pl_demo_period_actions_no_insert BEFORE INSERT ON pl_period_actions FOR EACH ROW BEGIN IF EXISTS (SELECT 1 FROM pl_demo_state WHERE id = 1) AND (NOT EXISTS (SELECT 1 FROM pl_companies WHERE id = NEW.company_id AND is_sample = 1) OR EXISTS (SELECT 1 FROM pl_demo_visitors WHERE company_id = NEW.company_id)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Period administration is disabled in the public demo'; END IF; END",
];
