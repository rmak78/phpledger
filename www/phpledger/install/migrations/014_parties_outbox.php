<?php
declare(strict_types=1);

// Generic master data and transport foundation; no vetting or connector activation.
$sql = [];
$suffix = ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci';
$sql[] = "CREATE TABLE pl_parties (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, company_id BIGINT UNSIGNED NOT NULL,
 legal_name VARCHAR(160) NOT NULL, trading_name VARCHAR(160) NOT NULL DEFAULT '',
 entity_type VARCHAR(60) NOT NULL, country_code CHAR(2) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 is_customer TINYINT(1) NOT NULL, is_vendor TINYINT(1) NOT NULL,
 linked_entity_id BIGINT UNSIGNED NULL, assigned_owner_id BIGINT UNSIGNED NULL,
 status ENUM('draft','under_review','approved','on_hold','blacklisted','archived') NOT NULL DEFAULT 'draft',
 revision INT UNSIGNED NOT NULL DEFAULT 1, created_by BIGINT UNSIGNED NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_party_scope(id,company_id),
 CONSTRAINT ck_party_roles CHECK(is_customer IN (0,1) AND is_vendor IN (0,1) AND (is_customer=1 OR is_vendor=1)),
 CONSTRAINT fk_party_company FOREIGN KEY(company_id) REFERENCES pl_companies(id),
 CONSTRAINT fk_party_entity FOREIGN KEY(linked_entity_id) REFERENCES pl_companies(id),
 CONSTRAINT fk_party_owner FOREIGN KEY(company_id,assigned_owner_id) REFERENCES pl_company_members(company_id,user_id),
 CONSTRAINT fk_party_creator FOREIGN KEY(created_by) REFERENCES pl_users(id)
)" . $suffix;
$sql[] = "CREATE TABLE pl_party_financial_profiles (
 party_id BIGINT UNSIGNED NOT NULL, company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL,
 currency CHAR(3) CHARACTER SET ascii COLLATE ascii_bin NOT NULL, payment_terms VARCHAR(500) NOT NULL DEFAULT '',
 credit_limit DECIMAL(20,4) NULL, ar_account_id BIGINT UNSIGNED NULL, ap_account_id BIGINT UNSIGNED NULL,
 PRIMARY KEY(party_id,book_id), CONSTRAINT ck_party_credit CHECK(credit_limit IS NULL OR credit_limit>=0),
 CONSTRAINT fk_party_fin_party FOREIGN KEY(party_id,company_id) REFERENCES pl_parties(id,company_id),
 CONSTRAINT fk_party_fin_book FOREIGN KEY(book_id,company_id) REFERENCES pl_books(id,company_id),
 CONSTRAINT fk_party_ar FOREIGN KEY(ar_account_id,company_id,book_id) REFERENCES pl_accounts(id,company_id,book_id),
 CONSTRAINT fk_party_ap FOREIGN KEY(ap_account_id,company_id,book_id) REFERENCES pl_accounts(id,company_id,book_id)
)" . $suffix;
$sql[] = "CREATE TABLE pl_party_identifiers (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, party_id BIGINT UNSIGNED NOT NULL, company_id BIGINT UNSIGNED NOT NULL,
 country_code CHAR(2) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 scheme VARCHAR(40) CHARACTER SET ascii COLLATE ascii_bin NOT NULL, value VARCHAR(120) NOT NULL,
 normalized_value VARCHAR(120) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 UNIQUE KEY uq_party_identifier(company_id,country_code,scheme,normalized_value),
 CONSTRAINT fk_party_identifier FOREIGN KEY(party_id,company_id) REFERENCES pl_parties(id,company_id)
)" . $suffix;
$sql[] = "CREATE TABLE pl_party_details (
 party_id BIGINT UNSIGNED NOT NULL PRIMARY KEY, company_id BIGINT UNSIGNED NOT NULL,
 localized_names JSON NOT NULL, registrations JSON NOT NULL, addresses JSON NOT NULL,
 registration_status ENUM('unknown','registered','unregistered') NOT NULL DEFAULT 'unknown',
 supply_country_code CHAR(2) NULL, supply_subdivision VARCHAR(100) NOT NULL DEFAULT '',
 filer_status VARCHAR(60) NOT NULL DEFAULT 'unknown', filer_checked_on DATE NULL,
 withholding_applicable TINYINT(1) NULL, exemption_reference VARCHAR(160) NOT NULL DEFAULT '',
 exemption_expires_on DATE NULL, default_tax_treatment VARCHAR(120) NOT NULL DEFAULT '',
 notes TEXT NOT NULL,
 CONSTRAINT fk_party_details FOREIGN KEY(party_id,company_id) REFERENCES pl_parties(id,company_id)
)" . $suffix;
$sql[] = "CREATE TABLE pl_contacts (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, party_id BIGINT UNSIGNED NOT NULL, company_id BIGINT UNSIGNED NOT NULL,
 name VARCHAR(160) NOT NULL, designation VARCHAR(120) NOT NULL DEFAULT '', email VARCHAR(254) NOT NULL DEFAULT '',
 language_preference VARCHAR(35) CHARACTER SET ascii NOT NULL DEFAULT 'en',
 role ENUM('billing','receiving','authorised_signatory','owner') NOT NULL,
 is_primary TINYINT(1) NOT NULL DEFAULT 0, notes TEXT NOT NULL, revision INT UNSIGNED NOT NULL DEFAULT 1,
 created_by BIGINT UNSIGNED NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_contact_scope(id,party_id,company_id),
 CONSTRAINT ck_contact_primary CHECK(is_primary IN (0,1)),
 CONSTRAINT fk_contact_party FOREIGN KEY(party_id,company_id) REFERENCES pl_parties(id,company_id),
 CONSTRAINT fk_contact_creator FOREIGN KEY(created_by) REFERENCES pl_users(id)
)" . $suffix;
$sql[] = "CREATE TABLE pl_contact_phones (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, contact_id BIGINT UNSIGNED NOT NULL,
 party_id BIGINT UNSIGNED NOT NULL, company_id BIGINT UNSIGNED NOT NULL,
 phone VARCHAR(40) NOT NULL, normalized_phone VARCHAR(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 is_whatsapp TINYINT(1) NOT NULL DEFAULT 0,
 UNIQUE KEY uq_contact_phone(contact_id,normalized_phone), KEY ix_company_phone(company_id,normalized_phone),
 CONSTRAINT ck_phone_whatsapp CHECK(is_whatsapp IN (0,1)),
 CONSTRAINT fk_phone_contact FOREIGN KEY(contact_id,party_id,company_id) REFERENCES pl_contacts(id,party_id,company_id)
)" . $suffix;
$sql[] = "CREATE TABLE pl_party_bank_accounts (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, party_id BIGINT UNSIGNED NOT NULL, company_id BIGINT UNSIGNED NOT NULL,
 title VARCHAR(160) NOT NULL, bank VARCHAR(160) NOT NULL, iban VARCHAR(34) NULL, account_number VARCHAR(80) NULL,
 created_by BIGINT UNSIGNED NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_party_bank FOREIGN KEY(party_id,company_id) REFERENCES pl_parties(id,company_id),
 CONSTRAINT fk_party_bank_actor FOREIGN KEY(created_by) REFERENCES pl_users(id)
)" . $suffix;
$sql[] = "CREATE TABLE pl_party_attachments (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, party_id BIGINT UNSIGNED NOT NULL, company_id BIGINT UNSIGNED NOT NULL,
 private_storage_key VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, mime_type VARCHAR(100) NOT NULL,
 sha256 CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL, created_by BIGINT UNSIGNED NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_party_attachment FOREIGN KEY(party_id,company_id) REFERENCES pl_parties(id,company_id),
 CONSTRAINT fk_party_attachment_actor FOREIGN KEY(created_by) REFERENCES pl_users(id)
)" . $suffix;
$sql[] = "CREATE TABLE pl_party_tags (
 party_id BIGINT UNSIGNED NOT NULL, company_id BIGINT UNSIGNED NOT NULL, tag VARCHAR(80) NOT NULL,
 PRIMARY KEY(party_id,tag), CONSTRAINT fk_party_tag FOREIGN KEY(party_id,company_id) REFERENCES pl_parties(id,company_id)
)" . $suffix;
$sql[] = "CREATE TABLE pl_party_status_history (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, party_id BIGINT UNSIGNED NOT NULL, company_id BIGINT UNSIGNED NOT NULL,
 from_status ENUM('draft','under_review','approved','on_hold','blacklisted','archived') NULL,
 to_status ENUM('draft','under_review','approved','on_hold','blacklisted','archived') NOT NULL,
 actor_id BIGINT UNSIGNED NOT NULL, reason VARCHAR(500) NOT NULL, recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_party_status FOREIGN KEY(party_id,company_id) REFERENCES pl_parties(id,company_id),
 CONSTRAINT fk_party_status_actor FOREIGN KEY(actor_id) REFERENCES pl_users(id)
)" . $suffix;
$sql[] = "CREATE TABLE pl_party_actions (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL,
 party_id BIGINT UNSIGNED NOT NULL, contact_id BIGINT UNSIGNED NULL, actor_id BIGINT UNSIGNED NOT NULL,
 request_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 payload_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 action VARCHAR(40) NOT NULL, result_revision INT UNSIGNED NOT NULL, reason VARCHAR(500) NOT NULL,
 phone_duplicate_acknowledged TINYINT(1) NOT NULL DEFAULT 0, duplicate_reason VARCHAR(500) NOT NULL DEFAULT '',
 recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_party_action_request(company_id,request_key),
 CONSTRAINT fk_party_action_party FOREIGN KEY(party_id,company_id) REFERENCES pl_parties(id,company_id),
 CONSTRAINT fk_party_action_contact FOREIGN KEY(contact_id,party_id,company_id) REFERENCES pl_contacts(id,party_id,company_id),
 CONSTRAINT fk_party_action_book FOREIGN KEY(book_id,company_id) REFERENCES pl_books(id,company_id),
 CONSTRAINT fk_party_action_actor FOREIGN KEY(actor_id) REFERENCES pl_users(id)
)" . $suffix;
$sql[] = "CREATE TABLE pl_outbound_events (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL,
 event_type VARCHAR(80) CHARACTER SET ascii COLLATE ascii_bin NOT NULL, event_version SMALLINT UNSIGNED NOT NULL DEFAULT 1,
 event_key VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 payload JSON NOT NULL, payload_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_outbound_event_key(book_id,event_key), UNIQUE KEY uq_outbound_event_scope(id,company_id,book_id),
 CONSTRAINT fk_outbound_event_book FOREIGN KEY(book_id,company_id) REFERENCES pl_books(id,company_id)
)" . $suffix;
$sql[] = "CREATE TABLE pl_outbound_deliveries (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, event_id BIGINT UNSIGNED NOT NULL,
 company_id BIGINT UNSIGNED NOT NULL, book_id BIGINT UNSIGNED NOT NULL,
 consumer VARCHAR(80) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 status ENUM('ready','leased','succeeded','dead') NOT NULL DEFAULT 'ready', attempts INT UNSIGNED NOT NULL DEFAULT 0,
 available_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, lease_token CHAR(32) CHARACTER SET ascii COLLATE ascii_bin NULL,
 leased_until DATETIME NULL, completed_at DATETIME NULL,
 UNIQUE KEY uq_outbound_consumer(event_id,consumer), KEY ix_outbound_due(consumer,company_id,book_id,status,available_at),
 CONSTRAINT fk_outbound_delivery FOREIGN KEY(event_id,company_id,book_id) REFERENCES pl_outbound_events(id,company_id,book_id)
)" . $suffix;
$sql[] = "CREATE TABLE pl_outbound_attempts (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, delivery_id BIGINT UNSIGNED NOT NULL,
 attempt_number INT UNSIGNED NOT NULL, outcome ENUM('succeeded','retry','dead','lease_expired') NOT NULL,
 recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_outbound_attempt FOREIGN KEY(delivery_id) REFERENCES pl_outbound_deliveries(id)
)" . $suffix;
foreach (['pl_party_status_history', 'pl_party_actions', 'pl_outbound_events', 'pl_outbound_attempts'] as $table) {
    foreach (['update', 'delete'] as $action) {
        $sql[] = "CREATE TRIGGER {$table}_no_{$action} BEFORE " . strtoupper($action) . " ON {$table} FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Foundation history is immutable'";
    }
}
return $sql;
