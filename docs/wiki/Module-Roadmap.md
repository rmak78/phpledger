# Core first, optional modules next

**0.1.4-preview publication:** the owner authorized the completed foundation/module/running-balance changes to go live first. Current release status is recorded in the repository's `docs/repository/sprint-05/PREVIEW-0.1.3-VALIDATION.md`; earlier local wording below is historical. The next implementation order remains API/MCP reads, controlled commands, then optional AR/AP. Subledgers and reviewed statement packages are unfinished. SEO discovery and campaign preparation are high priority in parallel.

PHP Ledger's required core lets an authorized user manage accounts, record and reverse journals, reconcile balances and complete the supported accounting period with every optional module disabled. The 0.1.4-preview release consolidates opening/cutover, reasoned periods, bank reconciliation, core CSV exports and the bundled module lifecycle. Its account ledger shows running balances on desktop, tablet and mobile.

AR, AP, purchasing/inventory, tax and industry POS will be optional modules. They reuse the same identities, permissions, money rules and central posting service. The local bundled core/POS lifecycle now has versioned manifests, reviewed migration compatibility, owner-only enable/disable decisions and immutable history. Ordinary companies default off; existing receipts survive disablement. It is a two-manifest foundation, not a third-party installer. The repository receipt `docs/repository/sprint-05/MODULE-FOUNDATION.md` records its scope; that source is not yet pushed. API/MCP reads are next, then controlled commands and optional AR/AP. Accounting/security and observed-user acceptance remain open.

## Delivery order

| Order | Deliverable | Key gate |
|---|---|---|
| 1 | Core account statements, chart management and general journals | Scoped, reconciling balances; audited account edits; saved drafts; exact posting, retries and linked reversals. |
| 2 | Opening/cutover, period completion, bank reconciliation and core reports/exports | Reviewed two-period core-only example, recovery and observed user acceptance. |
| 3 | Module contracts, lifecycle and shared master data | Compatibility/dependencies, company enablement, retained historical access and core-only operation. |
| 4 | Business API and MCP reads | Same authorized companies, accounts, journals and report totals as the browser. |
| 5 | Business API and MCP commands | Scoped draft/validate/post/reverse operations with execution-time checks and durable receipts. |
| 6 | AR | Customer invoices, receipts, allocations, aging and reconciled control balances. |
| 7 | AP | Vendor bills, payment recording, allocations, aging and reconciled control balances. |
| 8 | Purchasing and inventory costing | Quantities, valuation, receiving/returns and COGS reconcile. |
| 9 | First reviewed Pakistan tax adapter | Defined entity/transaction scope, effective rules, adjustments and tax-to-ledger reconciliation. |
| 10 | Shop POS | Shared checkout, sale/return/settlement, supported tender and hardware behavior. |
| 11 | Restaurant POS | Tables/orders, modifiers, kitchen routing and split/merge settlement. |
| 12 | Distribution | Route/van stock, deliveries, collections and evening settlement. |
| 13 | Specialist modules | Reviewed pharmacy, jewelry, workshop, membership and other bounded workflows. |

This order is a delivery plan with acceptance gates, not a list of completed features. The [detailed module plan](https://github.com/rmak78/phpledger/blob/master/docs/MODULE-ROADMAP.md) records the contracts and dependencies.

## Contracts before dependent documents

Tax contracts must reserve document-line inputs, exact rounding, effective policy versions and immutable calculation snapshots before AR/AP document schemas are finalized. The first complete adapter is later in the illustrative sequence; any required adapter must move ahead of affected production transactions. Missing tax rules must never become an assumed zero.

Core cutover must reconcile any retained AR/AP controls to external unpaid-document schedules while the modules are unavailable. Later activation must match them without reposting opening balances.

Shared contacts will carry customer/vendor roles, and one shared product/service identity will support module attributes. Inventory owns quantity and valuation; restaurant operations own order/table/kitchen state. A core-only company should not require a product catalog.

## Enablement, history and access

Installing a module must not silently activate it for every company. Compatibility, dependencies and company capabilities will be checked on the server for browser, API and MCP operations.

Disabling a module must preserve posted journals, source snapshots, audit, exports and authorized historical reads. A correction involving module documents must use its supported correction workflow and reconcile its subledger.

API/MCP reads precede commands. Commands need explicit permissions, company scope, configured review policy, current-state checks and idempotent receipts. These adapters must not introduce a second ledger or authentication system. Recording a payment does not send money.

No supported module lifecycle, public business API or MCP server is available in the preview. [[Tax research|Tax-Research]] contains research candidates only.

[[Architecture]] · [[Accounting and reports|Accounting-and-Reports]] · [[Roadmap]]
