# Upgrade and recovery — PHP Ledger {{VERSION}}

Source revision: `{{SOURCE_COMMIT}}`.

This package adds the accounting starter to the modern foundation: required AR/AP workflows and manual core tax, optional Purchasing/Inventory, and reviewed opening conversions. Preserve the complete supplied migration chain and all existing checksums. Recognized older modern schemas apply their missing migrations in order. The repository includes disposable verification for the published 0.3.0 baseline through migration 016; your exact package and backup still require a restoration rehearsal. No automatic upgrade from the historical application, an unpublished quote worktree or a customized schema is supported.

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

For an installation already through `016_correction_identity`, the starter adds these nine migrations:

| Migration | Operator implications |
|---|---|
| `017_ar_ap_documents` | Adds invoices, bills and linked credits with immutable source revisions and action history. No existing customer or supplier balances are automatically adopted. |
| `018_inventory` | Adds shared products, stock movements, command receipts and opening review records. Installation does not enable Inventory for a company or guess opening stock quantities. |
| `019_purchasing` | Adds purchase orders, goods receipts, supplier-bill matches and returns. Purchasing activation requires Inventory; supplier debt remains in AP. |
| `020_opening_conversion` | Adds explicit allocation of existing opening journal amounts to reviewed unpaid documents, plus immutable conversion receipts. Conversion is a later owner action, not an automatic migration side effect. |
| `021_module_visibility` | Adds audited navigation preferences. Hiding AR/AP does not disable accounting services or change balances. |
| `022_tax_engine` | Adds manually configured tax codes/rate history and frozen document tax snapshots. No country rate catalog is activated. |
| `023_inventory_product_audit` | Extends the existing shared audit classification for product changes. |
| `024_opening_allocation_guard` | Requires exact non-null amount allocations whenever opening evidence shares an existing journal line. |
| `025_tax_price_mode` | Adds audited default price-entry settings and each document's frozen exclusive/inclusive mode. |

Earlier installations also require these retained foundation migrations, in their original order:

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

The supplied migrations preserve existing account IDs, posted journals and source records; they do not replace the chart, calculate opening balances or clear existing-business readiness restrictions. Compare saved totals, verify the 26 supplied migration identities through `025_tax_price_mode`, and check that the installed tables/views/guards match the reviewed package. Compare each receipt checksum to the deployed migration file, including its exact bytes; do not change line endings after packaging. Confirm every historical line has its domestic currency snapshot, and the temporary currency-upgrade guards are absent after successful completion. Both `006_core_accounts_journals` and `006_opening_cutover` are distinct retained identities. Never renumber them. The bundled country tax research stays disabled and unreviewed; manual core tax configuration is separate and no country adapter is activated. Existing read integrations retain their scope; no new financial write transport is enabled.

Migration `010_module_lifecycle` retains per-company state and immutable owner decisions. Existing optional-module decisions remain subject to their supplied manifests; newly introduced Inventory and Purchasing start disabled. After compatible installation, the owner can review and enable Inventory, then Purchasing, in **Modules**. Required AR/AP stays available; its navigation can be hidden separately. Existing receipts, source documents and reports remain readable while optional operations are disabled. A changed optional manifest requires a reviewed upgrade decision; a missing migration/checksum mismatch requires package repair. The screen never installs SQL or drops historical data. New explicit sample companies enable their synthetic POS showcase during provisioning.

Before reopening access, use an isolated synthetic company to check invoice/bill review and posting, a partial payment, a linked credit, ageing and control reconciliation. If Purchasing/Inventory will be activated, check an order, partial goods receipt, later matched supplier bill, stock valuation and received-but-unbilled reconciliation. Test inclusive/exclusive tax with explicitly reviewed manual codes and verify that credits retain their original snapshots. New receipt/payment journal lines use the existing bank CSV reconciliation; do not create a duplicate bank ledger.

Existing aggregate opening debts and inventory values need separate reviewed conversions after upgrade. Map parties/products explicitly and reconcile to the original opening journal basis; do not recreate those balances as newly posted documents. Conversion may reject a control or product basis with ambiguous or later activity. Preserve that evidence for review instead of bypassing the restriction. No opening conversion or optional module activation is performed by the migration command itself.

## Interrupted migration or rollback

MySQL DDL is not automatically rolled back as one application transaction. An `applying` receipt indicates an interrupted operation requiring review. Preserve the database and error evidence; do not edit migration files, delete receipts or manually mark a failed migration as applied. Do not repeatedly rerun the installer to conceal the failure.

Keep maintenance active. Have the operator restore the last verified database backup into an isolated replacement database, restore its matching code/configuration, and rerun the restoration checks before switching back. Restore code and database as a compatible pair; reverting application files alone does not undo a schema change. Any writes made after the backup require a separate reconciliation plan.

This package provides no automatic rollback, backup scheduler or one-click historical migration. Repository development/restore test tools are not shipped as customer commands.
