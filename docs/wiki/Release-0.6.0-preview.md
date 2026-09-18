*Historical release page, preserved as published. The current stable release is [[1.0.0|Release-1.0.0]], which consolidates this work with the 0.7 browser installer and 0.8 signed update/recovery work.*

## 0.6.0-preview: interface rebuild

This development preview rebuilds the PHP server-rendered interface with a warm light canvas, navy actions, local Inter fonts, a workspace sidebar, company switcher, compact lists and document forms. Tailwind CSS is compiled during development and included in the download; Node is not needed on the server. The existing CSP, PHP/MeekroDB architecture and central posting service remain in place.

- Home, global search/jump and quick-create navigation; compact account, party, journal, sales, purchase and inventory registers.
- Server-side GET filters, allow-listed sorting and 25/50/100-row pagination replace DataTables. Filter state remains in URLs; the `/tables` JSON API remains available.
- One-line-first document editors, exact live totals, posting previews, journal-editor posting and reviewed reversal/replacement correction previews.
- Stock-count and goods-receipt previews, linked purchase-order/receipt context, and physical-return guidance on supplier documents.
- Receivables/payables ageing, atomic allocation of one settlement across multiple open items, and FX gain/loss fields required only when applicable.
- Cost-of-sales classification, gross profit and P&L period presets. Existing unclassified charts preserve their prior net-profit calculations.
- Module/connection confirmations, server-enforced connection scope choices, account consequence guidance, and POS cashier identity.
- Existing tax-code definitions remain read-only; dated-rate changes remain available.

### Preview limitations and verification scope

Desktop/tablet are the design targets; mobile refinement and dark mode are deferred. Complete prototype-state visual acceptance, keyboard/zoom/accessibility review, consistent field-level validation recovery and the full nested report/source/action return journey remain unfinished. These are disclosed preview limitations, not completed acceptance claims. No accounting sign-off, WCAG certification, country compliance or production-readiness claim is made.

The recorded source check passed 282 tests with zero failures, PHP lint and PHPStan. A refreshed 33-state browser sweep produced 132 captures; this does not establish acceptance of every HTML route or all 75 prototype states. Valid local OAuth consent/cancel passed with JavaScript on/off. Consult the publication receipt for exact archive/runtime/upgrade and hosted verification; do not infer those checks from the source-test count.

Upgrade from 0.5.0-preview uses migrations 029-031. Back up and rehearse restoration first; preserve earlier migration files/checksums and run the included migration command once while writes are stopped. Re-running migrations must be a no-op. Never copy historical legacy SQL into the modern schema. See UPGRADE.md.

Media kit: https://github.com/rmak78/phpledger/releases/download/v0.6.0-preview/phpledger-0.6.0-preview-media-kit.zip


Install the application ZIP below, not GitHub’s automatic source archive. Compare its SHA-256 checksum before installation. This is a prerelease.
