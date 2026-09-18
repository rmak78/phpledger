# Published 0.5 data upgrade evidence

Verified locally on 18 September 2026 with PHP 8.3.33 / MySQL 8.4.
Baseline: `v0.5.0-preview`, commit `849ae91f1f6f41527fc4f0ab44f560ccaa8362bb`.

The baseline's `www/phpledger`, `resources` and fixture builders were extracted
with `git archive` into a temporary directory inside an isolated test container.
No historical runtime files were restored into the application checkout.
`tools/verify-preview-upgrade.php seed` loaded that published bootstrap, applied
its migration chain through 028 and used its normal services to create:

- A confirmed purchase order, partial goods receipt and partial receipt billing.
- A supplier payment leaving an outstanding payable and unbilled receipt balance.
- A stock invoice, partial customer receipt and customer credit/stock return.
- A foreign-currency invoice, manual rate evidence and partial settlement with FX.

The verifier recorded canonical hashes for all 80 baseline tables and report
snapshots. A separate PHP process loaded the new bootstrap and applied exactly
029, 030 and 031. Checks passed for:

- Every original table's original columns and rows unchanged (migration receipts
  are checked by the migration runner; added nullable/default columns are separate).
- Exact trial balance and net profit, AR/AP balances and reconciliations, and
  inventory valuation unchanged after upgrade.
- Published settlement and goods-receipt retries return the same final pre-upgrade
  state and do not change historical rows.
- A new stock invoice posts through the central service after upgrade, with a
  balanced trial balance and reconciled AR/AP and inventory.
- Migration replay applies nothing.

An initial verifier assertion compared a goods receipt captured before partial
billing with its later returned view, which includes billed quantities. The
verifier was corrected to snapshot the final pre-upgrade receipt view. Repeated
seed/check runs then passed. No application behavior was changed to satisfy it.

The verifier uses only a random `phpledger_preview_verify_...` schema on `db_test`,
checks the effective host/account, and removes that schema after the check. It
never runs against the development or hosted database. These sample cases
provide upgrade evidence; they do not assert coverage of every possible customer
dataset, every PHP version, package installation or browser acceptance.
