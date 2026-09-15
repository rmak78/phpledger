# Core accounting and optional modules

The 0.1.6-preview maintenance package preserves the published accounting core and eleven migrations. API/MCP read access remains a separate [development candidate](https://github.com/rmak78/phpledger/blob/codex/integration-delivery/docs/INTEGRATIONS.md); actual named-client acceptance is still open.

The core is country-neutral and useful with optional modules disabled. Regional connectors, product workflows and client interfaces reuse the same identities, company/book permissions, fixed-precision money and posting service.

## Delivery order

1. Complete scoped API/MCP reads and the real-client compatibility gates.
2. AR, then AP, with reconciled customer/vendor open items and cutover adoption.
3. Distribution packaging and updater: Softaculous, Installatron, Docker Hub, Packagist and the planned browser installer.
4. Regional tax/e-invoicing connector framework. Pakistan FBR is one planned connector alongside ZATCA, UAE Peppol PINT and Oman. Define contracts before AR/AP schemas; enable applicable reviewed rules before affected production use.
5. Purchasing/inventory and costing.
6. Shop POS, then e-commerce/storefront.
7. Controlled API/MCP commands with explicit authority and durable retry receipts.
8. Restaurant, distribution operations and specialist modules.

Urdu then Arabic/RTL with English fallback, owner/partner equity reporting and phone-friendly entry remain planned cross-cutting work. Native desktop/Android clients remain planned paid add-ons over the API/MCP contracts. Offline means queued drafts; only the server posts, controls periods and reverses.

The installer-created customer website is parked. Khata is a reserved, optional unposted-subledger concept; formalisation uses normal accounting services. No module may hide posted entries or create a second ledger. [[Licensing]] requires advance declaration of future commercial modules; none is declared by this maintenance release.

See the [detailed module roadmap](https://github.com/rmak78/phpledger/blob/master/docs/MODULE-ROADMAP.md), [installer plan](https://github.com/rmak78/phpledger/blob/master/docs/INSTALLER.md) and [[Roadmap]]. Accounting review, access isolation, exact reconciliation, installation/recovery and observed use remain acceptance gates.
