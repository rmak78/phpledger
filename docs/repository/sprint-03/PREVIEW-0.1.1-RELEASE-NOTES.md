# 0.1.1-preview: click-to-add POS

**Status: release candidate under validation.** The published download remains [v0.1.0-preview](https://github.com/rmak78/phpledger/releases/tag/v0.1.0-preview) until this candidate's archive, checksum and publication are verified. This is a foundation evaluation preview, not a stable release or completion of the supported-pilot gates.

## What changes

Click or tap a product to add one. Repeated clicks increase its quantity; the cart provides plus, minus and Remove controls. Search and category filters keep the current cart, and mobile users have a direct View cart action.

Choose **Review sale** to inspect server-verified prices and totals on a separate screen. Enter cash received or choose **Exact amount**, check the change, then explicitly choose **Record cash sale**. **Edit cart** retains quantities, date, cash and the original checkout identity. Keyboard edits cannot accidentally post a sale. Without JavaScript, quantity fields and server-rendered review remain available.

Reviewing a cart does not write accounting records. Final confirmation resolves catalog prices again and uses the existing atomic posting service. Rejected submissions retain their input. If the server cannot confirm whether checkout completed, it preserves the original request for an identical retry instead of asking the cashier to start another sale.

## Install and upgrade

The candidate targets PHP 8.5.x and MySQL 8.4. Its intended release assets are `phpledger-0.1.1-preview.zip` and the accompanying SHA-256 file. Use the packaged `INSTALL.md` and `UPGRADE.md`; automatic GitHub source archives do not contain installed dependencies.

This change adds **no migration and no schema changes**. Existing migrations 001–005 remain unchanged. Back up the application and database before replacing application files, preserve private configuration, and follow the upgrade checks. Validation of the final archive and recovery is a separate release gate; the prior package's receipt alone does not establish this candidate's upgrade result.

## Evidence and limits

The [local checkout validation](POS-CHECKOUT-VALIDATION.md) records 65 integration tests, 29 POS and 23 core HTTP checks, lint/static checks, and desktop/tablet/mobile browser checks. It covers exact totals, input recovery, deliberate confirmation and retained accounting protections. Final archive installation, upgrade, restoration, asset checksums, release source revision and public availability must be recorded by the release owner before this status changes to published.

This remains the six-product synthetic cash-sale showcase. It does not add inventory, stock deductions, cost of goods sold, tax, discounts, credit sales, card processing, hardware integration or offline operation. Recording a cash sale does not collect a payment. The existing single-base-currency accounting model is unchanged.

Qualified accounting review and representative cashier sessions remain open. No Pakistan, UK or UAE accounting framework is certified by this update. Historical imports, opening-balance cutover, AR/AP, translations, exchange-rate posting and AI scanning remain later work. MIT applies to new project-owned code and documentation, with separate dependency, asset and historical terms retained.

The public demo is updated independently from the downloadable package; a published ZIP does not by itself establish which build is running at `phpledger.com/demo/`.
