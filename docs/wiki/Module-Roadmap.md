## Current package: 1.0.0

**1.0.0**, published 18 September 2026, is the first stable release, consolidating the 0.6.1 workflow-recovery closure, the 0.7 browser installer and the 0.8 signed update/automatic-backup/recovery work. [Download 1.0.0](https://github.com/phpledger/phpledger/releases/tag/v1.0.0) and its [matching media kit](https://github.com/phpledger/phpledger/releases/download/v1.0.0/phpledger-1.0.0-media-kit.zip). Independent accounting review, independent security review, supervised pilots with a real month-end close and unfamiliar-operator installation observation have **not** happened; they continue as post-release commitments. See [[Release 1.0.0|Release-1.0.0]].

# Core accounting and optional modules

1.0.0 combines the required accounting core (chart of accounts, journals, AR/AP with manually configured tax) with optional bundled Purchasing and Inventory, a scoped read API/MCP with OAuth/Connections, browser installation and signed automatic updates. Only individually tested API/MCP clients enter the verified compatibility matrix; other clients remain pending.

The core is country-neutral and useful with optional modules disabled. Regional connectors, product workflows and client interfaces reuse the same identities, company/book permissions, fixed-precision money and posting service.

## Delivery order after 1.0.0

1. **1.0.x:** production fixes and compatibility improvements.
2. **1.1:** reviewed Urdu/RTL, plus distribution packaging and updater channels — Softaculous, Installatron, Docker Hub, Packagist and published containers.
3. **1.2:** reviewed Arabic/RTL and demand-led reporting refinements.
4. Regional tax/e-invoicing connector framework. Pakistan FBR is one planned connector alongside ZATCA, UAE Peppol PINT and Oman; enable applicable reviewed rules before affected production use.
5. Advanced stock and costing (multiple locations, batches, serials, expiry, landed cost).
6. A stock/tax-integrated shop POS, then e-commerce/storefront. The bundled cash POS remains an illustrative demonstration, not a production retail module.
7. Controlled API/MCP write commands with explicit authority and durable retry receipts. 1.0.0 exposes read-only API/MCP access.
8. Restaurant, distribution operations and specialist modules.

Owner/partner equity reporting and phone-friendly entry remain planned cross-cutting work. Native desktop/Android clients remain planned paid add-ons over the API/MCP contracts. Offline means queued drafts; only the server posts, controls periods and reverses.

The installer-created customer website is parked. Khata is a reserved, optional unposted-subledger concept; formalisation uses normal accounting services. No module may hide posted entries or create a second ledger. [[Licensing]] requires advance declaration of future commercial modules; none is declared by 1.0.0.

See the [detailed module roadmap](https://github.com/phpledger/phpledger/blob/master/docs/MODULE-ROADMAP.md), [installer plan](https://github.com/phpledger/phpledger/blob/master/docs/INSTALLER.md) and [[Roadmap]]. Accounting review, access isolation, exact reconciliation, installation/recovery and observed use remain acceptance gates for each future module.
