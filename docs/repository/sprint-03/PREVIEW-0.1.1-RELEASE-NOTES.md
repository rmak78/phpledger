# 0.1.1-preview: click-to-add POS

**Status: published.** [Download v0.1.1-preview](https://github.com/rmak78/phpledger/releases/tag/v0.1.1-preview); the hosted demo runs the same runtime source. This is a foundation evaluation preview, not a stable release or completion of the supported-pilot gates.

## What changes

Click or tap a product to add one. Repeated clicks increase its quantity; the cart provides plus, minus and Remove controls. Search and category filters keep the current cart, and mobile users have a direct View cart action.

Choose **Review sale** to inspect server-verified prices and totals on a separate screen. Enter cash received or choose **Exact amount**, check the change, then explicitly choose **Record cash sale**. **Edit cart** retains quantities, date, cash and the original checkout identity. Keyboard edits cannot accidentally post a sale. Without JavaScript, quantity fields and server-rendered review remain available.

Reviewing a cart does not write accounting records. Final confirmation resolves catalog prices again and uses the existing atomic posting service. Rejected submissions retain their input. If the server cannot confirm whether checkout completed, it preserves the original request for an identical retry instead of asking the cashier to start another sale.

## Install and upgrade

The package targets PHP 8.5.x and MySQL 8.4. Its verified release assets are `phpledger-0.1.1-preview.zip` and the accompanying SHA-256 file. Use the packaged `INSTALL.md` and `UPGRADE.md`; automatic GitHub source archives do not contain installed dependencies.

This change adds **no migration and no schema changes**. Existing migrations 001–005 remain unchanged. Back up the application and database before replacing application files, preserve private configuration, and follow the upgrade checks. The actual archive passed fresh installation, the unmodified 0.1.0 upgrade and isolated backup restoration; see the receipt.

## Evidence and limits

The [artifact and publication receipt](PREVIEW-0.1.1-VALIDATION.md) records 67 integration tests, 69 artifact HTTP checks, installation, upgrade, restoration, reproducible archive checks, CI and 78 public-demo checks. The [earlier local receipt](POS-CHECKOUT-VALIDATION.md) remains available.

This remains the six-product synthetic cash-sale showcase. It does not add inventory, stock deductions, cost of goods sold, tax, discounts, credit sales, card processing, hardware integration or offline operation. Recording a cash sale does not collect a payment. The existing single-base-currency accounting model is unchanged.

Qualified accounting review and representative cashier sessions remain open. No Pakistan, UK or UAE accounting framework is certified by this update. Historical imports, opening-balance cutover, AR/AP, translations, exchange-rate posting and AI scanning remain later work. MIT applies to new project-owned code and documentation, with separate dependency, asset and historical terms retained.

The public demo is updated independently from the downloadable package; a published ZIP does not by itself establish which build is running at `phpledger.com/demo/`.
