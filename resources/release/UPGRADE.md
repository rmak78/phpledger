# Upgrade and recovery — PHP Ledger {{VERSION}}

Source revision: `{{SOURCE_COMMIT}}`.

This release adds currency, party/contact, outbound queue, internal open-item and same-identity correction foundations to the modern 0.2.1 preview. Preserve the complete supplied migration chain and all existing checksums. Recognized older modern schemas apply their missing migrations in order; source-level rehearsals cover foundation, core 0.1.2, opening-local and populated 0.2.1 baselines. Your exact package and backup still require a restoration rehearsal. No automatic upgrade from the historical application or customized schema is supported.

## Before changing an installation

Arrange a maintenance window at the web server or reverse proxy, blocking customer access and writes while keeping operator access. There is no customer maintenance-mode switch in this preview. Stop any locally added workers or integrations, and prevent concurrent schema changes. Record the installed package version, manifest, PHP/MySQL versions and migration state.

Keep a private, recoverable copy of:

- The complete installed application/package, including all migrations and dependency files.
- Private configuration and hosting/PHP settings, held separately from public artifacts.
- A consistent database backup containing table definitions, data, views and triggers, including `pl_schema_migrations`.

Backups contain private business and authentication data. Store them outside web roots with restricted access and your chosen encryption/retention policy. A download of the source code is not a database backup.

## Back up and rehearse restoration

Use MySQL 8.4 tools or an equivalent host-managed backup whose restoration you have verified. For a small InnoDB installation, an operator can use this Bash example after configuring a private MySQL login path called `phpledger-backup` and choosing a new backup filename:

```sh
mysqldump --login-path=phpledger-backup --single-transaction --triggers --no-tablespaces --set-gtid-purged=OFF --result-file=/private/backups/phpledger-before-upgrade.sql phpledger
```

Replace the database and private output path. Do not reuse an existing backup filename, put a database password in arguments, or accept a failed/empty dump. Preserve the exit status, size and checksum. This example is for the application database, not server-wide users or replication recovery; have the database administrator adapt it for those needs. MySQL documents the [backup options and required privileges](https://dev.mysql.com/doc/refman/8.4/en/mysqldump.html).

Before relying on the backup, create a separate empty restoration database and use a restricted staging application with matching code. Review view/trigger definers and required restore privileges with the database administrator; preserve the guards rather than dropping them to make a restore appear successful. An example Bash import using a separately configured restoration login is:

```sh
mysql --login-path=phpledger-restore phpledger_restore < /private/backups/phpledger-before-upgrade.sql
```

Do not aim that command at the live database. Confirm definitions/data, all included migration receipts and their checksums, every installed accounting guard trigger, company scope, original/current source links and balanced totals. Check both effective-source views: preserve their security/definer metadata, verify their dependencies point only to the restoration database, and compare their returned source rows. A retained definer may need temporary read access to the isolated restoration database; scope and revoke that rehearsal grant explicitly. Run preflight and sign in on the isolated restoration. Compare a known transaction and its reports with the recorded pre-backup values. A completed SQL import alone is insufficient proof of recovery.

## Apply a reviewed update

The four new migrations are:

| Migration | Operator implications |
|---|---|
| `013_currency_foundation` | Adds company/book/account currency properties, versioned manual rates and journal-line snapshots. Existing domestic line metadata is backfilled while original amounts, source fields and hashes remain protected. Temporary insert guards and a restrictive update trigger cover the backfill; permanent immutability is restored before completion. |
| `014_parties_outbox` | Adds company-scoped party/contact storage, reserved vetting/history data and the outbound queue. Does not activate vetting or install external delivery. |
| `015_open_items` | Adds the authoritative GL-linked internal open-item ledger and command receipts. Does not adopt existing aggregate balances or opening registers. |
| `016_correction_identity` | Adds immutable source identity/revisions and two effective-source views used by existing reads. Views run with SQL SECURITY DEFINER under the account that executes this migration. |

Stop web traffic, schedulers and other writers throughout migration and verification. The migration advisory lock coordinates installers; it does not provide an application maintenance mode. Migration 013 exchanges guard triggers using nontransactional DDL. Keep maintenance active if any statement fails, and restore the matched backup before reopening unless a qualified operator has reviewed the exact interrupted state and recovery sequence. Do not remove restrictive guards to force writes through.

Use a stable dedicated migration account for migration 016. Retain that account and its required access to underlying tables; grant normal application users access to the effective views. Dropping a temporary migration account afterward can leave the views unusable. Inspect `SHOW CREATE VIEW` for `pl_effective_documents` and `pl_effective_general_drafts`, and verify reads as the actual restricted runtime user. For a separately managed hosted demo, validate that the stable reset account recreates the views successfully during its next reset; the customer package installs no demo scheduler.

Install the complete new vendor payload before running the new code. Preserve existing signing/encryption keys, configured public URL and private settings; initialize OAuth keys only for an installation that has never enabled them. Do not copy keys into the package. Earlier missing migrations, including `011_read_connections` and `012_demo_history_periods`, still run in their original order and require the same maintenance boundary. Older preflight binaries reject unknown receipts: rollback requires restoring matched database/source/configuration, not deleting new receipts or merely reverting application files.

Only continue when the new release explicitly supports your starting version/schema and the restoration rehearsal succeeds. Unpack the new package beside the existing directory. Verify its manifest/checksum, retain the previous version, and transfer private configuration deliberately. Do not overlay unknown old files into the new package.

Under maintenance, run from the new package root:

```sh
php www/phpledger/install/preflight.php
php www/phpledger/install/migrate.php
php www/phpledger/install/preflight.php
```

Stop on any nonzero exit status. Preflight must distinguish a recognized pending migration from an unknown/interrupted/altered schema; only a recognized upgrade path may proceed. An unknown migration receipt or checksum mismatch stops the upgrade. Switch the web root to the new `www/phpledger/public` after successful migration. Recheck sign-in, company selection, existing totals and account statement balances. In an isolated sample company, check account creation/rename/status and a general-journal draft, review, post and dated reversal before reopening access. Do not recreate the initial user during an upgrade.

The supplied migrations preserve existing account IDs, posted journals and source records; they do not replace the chart, calculate opening balances or clear existing-business readiness restrictions. Compare your saved totals and confirm 17 applied receipts, 56 tables, two effective-source views and 62 guard triggers. Compare each receipt checksum to the deployed migration file, including its exact bytes; do not change line endings after packaging. Confirm every historical line has its domestic currency snapshot, and the temporary currency-upgrade guards are absent after successful completion. Both `006_core_accounts_journals` and `006_opening_cutover` are distinct retained identities. Never renumber them. The bundled tax research stays disabled and unreviewed; installing it does not enable tax calculations or country adapters. Existing configured read integrations remain separate from these foundations; no write transport is enabled.

Migration `010_module_lifecycle` adds per-company state and immutable owner-decision receipts. Existing ordinary companies start with POS disabled even if an earlier package exposed checkout. After compatible installation, a company owner can open **Modules**, review the showcase and enable it with a reason. Existing receipts, source documents and reports remain readable while disabled. A changed manifest requires a reviewed upgrade decision; a missing migration/checksum mismatch requires package repair. The screen never installs SQL or drops historical data. New explicit sample companies enable their synthetic showcase during provisioning.

## Interrupted migration or rollback

MySQL DDL is not automatically rolled back as one application transaction. An `applying` receipt indicates an interrupted operation requiring review. Preserve the database and error evidence; do not edit migration files, delete receipts or manually mark a failed migration as applied. Do not repeatedly rerun the installer to conceal the failure.

Keep maintenance active. Have the operator restore the last verified database backup into an isolated replacement database, restore its matching code/configuration, and rerun the restoration checks before switching back. Restore code and database as a compatible pair; reverting application files alone does not undo a schema change. Any writes made after the backup require a separate reconciliation plan.

This package provides no automatic rollback, backup scheduler or one-click historical migration. Repository development/restore test tools are not shipped as customer commands.
