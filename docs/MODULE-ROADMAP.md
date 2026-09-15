# Core accounting and optional modules

Owner decisions adopted on 15 September 2026 from the supplied local `docs/strategy/DECISION-REGISTER.md`, section D, as corrected by the [country-neutral direction clarification](strategy/PRODUCT-DIRECTION-CLARIFICATION-2026-09-15.md). The accounting core stays open source; optional modules and commercial licensing follow [Licensing policy](LICENSING-POLICY.md). These decisions supersede the earlier order that put controlled commands and the installer before AR/AP. These are delivery priorities and gates, not new implemented features or dates.

## Current status

The public release is **0.1.5-preview**. The bounded [core completion](repository/sprint-05/CORE-COMPLETION.md) and [module foundation](repository/sprint-05/MODULE-FOUNDATION.md) have recorded evidence. Read API/MCP, OAuth/Connections and server-side tables are implemented in local commit `1541e27`; [the named-client matrix](INTEGRATIONS.md) remains open. Bridge/protocol checks do not establish application compatibility. Multi-year demo work is local and paused for this decision task. Independent accounting/security review and observed user acceptance remain separate gates.

API/MCP reads complete first. AR and AP follow immediately. Packaging distribution and the updater form a named milestone immediately after AP. Urdu, then Arabic, RTL and owner-equity/AOP reporting design belong in the first delivery layer, with no claim that translations or partner-allocation logic already exist.

## Historical starting point

The v0.1.1-preview baseline has shared PHP/MeekroDB services, authentication and company/book access, setup/readiness checks, a preliminary six-account template, exact atomic posting, duplicate prevention, linked reversals, receipt/expense drafts, reports and a synthetic cash POS showcase. Its browser routes and `/health` are not a public business API. There is no MCP server, supported module lifecycle, full chart-management journey or reviewed opening-import/period-close workflow. The shop catalog and POS code are currently wired into the application; a separate screen is not yet a plug-in contract.

The 0.1.2-preview core slice implements opening, period, running and closing account balances; account creation and audited name/status edits; and saved general journals with explicit review/post/reverse. Existing account codes, classifications and purposes remain fixed. See its [validation receipt](repository/sprint-04/CORE-0.1.2-VALIDATION.md) for technical and publication evidence. It does not complete bank reconciliation, reviewed opening imports, customer/vendor subledgers or a jurisdiction's financial-statement package.

## What belongs where

| Layer | Responsibility |
|---|---|
| Required accounting core | Companies/books, identities and permissions, chart of accounts and mappings, exact money/dates, general journals, source/audit references, periods, reviewed opening/cutover, cash/bank recording and reconciliation, trial balance, general ledger/account statements and reconciled core reports/exports. |
| Shared master data | One company-scoped contact identity with customer/vendor roles; one product/service identity with units and catalog attributes. Introduce these capabilities when the first consuming module is built; core-only books need no product catalog. Inventory owns quantity/valuation and modules add their own attributes. |
| Optional business modules | AR invoices/receipts/allocations; AP bills/payments/allocations; jurisdiction tax adapters; purchasing/inventory; shop POS; e-commerce/storefront; later restaurant, distribution and specialist workflows. Each owns its documents and subledger reports and submits to the core posting service. |
| Access adapters | The browser, versioned business API and MCP tools expose the same services and permissions. A transport never implements a second ledger, independent user store or bypass around readiness/period rules. |
| Industry interfaces | Shop and restaurant screens compose enabled capabilities and shared checkout. Their different user journeys do not require duplicate accounting engines or product/customer databases. |

With every optional module disabled, an authorized user must still manage accounts, record and reverse general journals and simple cash/bank receipts/expenses, reconcile balances and complete the supported accounting period. A core account statement is available for every account; aging, open-item customer/vendor statements and stock valuation require their owning modules. Optional software does not make applicable tax or reporting obligations optional.

## Ordered delivery milestones

The already implemented core/account/module foundations continue to require their documented accounting and usability gates. The next implementation order is:

| Order | Deliverable | Completion gate |
|---|---|---|
| 1. API/MCP reads | Complete the in-flight scoped read API, remote/local MCP, connection lifecycle and shared server-side tables. Native clients consume these same contracts. | Each named client completes authentication, discovery, explicit scope, four reports, pagination/source reads, browser parity, denied writes/cross-company reads, expiry/revocation and demo reset. Record versions/settings; inaccessible clients remain open. |
| 2. AR | Customer roles, invoices, credit notes, receipts, allocation, statements and aging; reconciled unpaid-document adoption. | Open documents equal the AR control balance through cutover, partial payment, credit/reversal and concurrent allocation; no duplicated cash-sale income or opening balance. |
| 3. AP | Vendor roles, bills, credit notes, payment recording, allocation, statements and aging. | Open bills equal AP control through cutover and corrections; recording a payment does not send money. |
| 4. Distribution packaging and updater | Softaculous, Installatron, Docker Hub image, Packagist and an in-app updater; coordinate the planned browser installer in this adoption milestone. | Fresh installs and upgrades on stated hosts, authentic/versioned packages, backups, migration receipts, failed-update recovery and source/database rollback compatibility. No live publication or automatic update is implied. |
| 5. Regional tax/e-invoicing connectors | Country-neutral connector contracts, reviewed entity/effective-period rules and immutable tax snapshots. Pakistan FBR is one planned open-source connector: sandbox scenario runner SN001–SN028, protected token storage, HS/UoM reference sync, QR/logo invoice printing, the applicable 72-hour edit-window workflow, debit/credit notes, and Annex-A/C/I built from ledger plus DI responses. | Per-connector current contract and sandbox verification, reconciled document/tax/provider outcomes, failure/retry/correction tests, source provenance and qualified local review for the supported scope. Actual submissions require separate configured authority. |
| 6. Purchasing and inventory | Purchase orders, receiving/returns, warehouses and stock movements; reviewed FIFO or weighted-average costing and COGS integration. | Quantities, valuation, AP/receipt controls and COGS reconcile under returns, backdating and concurrency. |
| 7. Shop POS | Shared checkout, phone-first counter entry, product/barcode entry, returns, tender and shift/day reconciliation; dependencies include AR where used, stock/costing and applicable tax. | Observed shop journeys, exact ledger/stock/tender reconciliation, retry/access/load/printer checks and supported hosting. The existing cash showcase is not a completed retail module. |
| 8. E-commerce/storefront | Online product/order/customer journeys using the same catalog, AR, stock, tax and checkout services. | Order/payment-state boundaries, stock and ledger reconciliation, idempotent confirmation, cancellation/refund policy and authorised provider integrations. This is separate from the parked installer-website-builder idea. |
| 9. Controlled API/MCP commands | Draft/validate/post/reverse commands with current scope, configured review policy, durable request identity and command receipts. | Lost responses/retries cause one effect; stale previews and changed content conflict; revocation and disabled capability checks apply at execution. No command implicitly sends messages, payments or provider submissions. |
| 10. Restaurant | Table/order lifecycle, modifiers, kitchen tickets/routing and bill split/merge over shared checkout; recipes are a separately reviewed stock capability. | Traceable orders, changes, splits, voids and settlement; restaurant-user and accounting acceptance. |
| 11. Distribution operations | Route/van orders, warehouse handoffs, delivery, collections and evening settlement. | Van stock, documents, cash/credit and ledger reconcile across handoffs and retry/failure cases. |
| 12. Specialist modules | Pharmacy, jewelry, workshop, membership and other validated scopes. | Each requires named dependencies, worked accounting cases and observed user acceptance. |

First-layer cross-cutting work: Urdu first, Arabic second, RTL and English fallback; readable phone counter/desktop accountant journeys; planned real-time owner's equity and, for AOPs, partners' capital accounts, profit-sharing ratios and drawings. The existing equity summary does not implement those partner-specific features.

Native desktop and Android clients are **planned paid add-ons**, not parked. They consume the API/MCP contracts; their disconnected work is queued entry with periodic low-bandwidth sync, never offline posting. Commercial terms must be declared before release under the licensing policy.

### Tax contract before AR/AP schemas

Define shared tax capability, line inputs, exact rounding, policy versions and immutable document/calculation/provider snapshots before AR/AP schemas are finalised. The contract must accommodate **FBR JSON/REST**, **ZATCA XML/UBL with cryptographic stamping**, **UAE Peppol PINT** and **Oman's regime**. These are distinct adapters; no single payload or credential model should be assumed for all four. Regional research also covers Pakistan, the UK, UAE, Saudi Arabia, Oman, Singapore, Malaysia, Sri Lanka and Bangladesh. Pakistan is one intended direction; the core has no mandatory Pakistan-first deployment gate. Research current rules and dates per jurisdiction before implementation.

Pakistan FBR is one connector candidate within the regional milestone above. Connector release order follows validated demand and readiness. AR/AP can be developed with synthetic/no-tax cases; their production use for taxable transactions waits for applicable supported rules. Missing tax capability never means zero tax. Shipping the open-source FBR client is separate from being the integrator of record: the product plan uses PRAL's free integration route. FBR requires a valid licensed integrator to configure a registered person's live transmission, and identifies PRAL as providing free integration on demand. [FBR DI FAQs 6 and 12](https://www.fbr.gov.pk/faqs/173967/173969), checked 15 September 2026. BixiSoft's planned licensed-integrator application follows a proven client and is a future company decision, not a product dependency. IP whitelisting, current scenario/version coverage and live regulatory requirements retain explicit verification gates.

### Optional khata concept

“Khata (informal unposted sub-ledger)” is a candidate add-on whose entries remain visibly unposted. A reviewed formalisation creates invoices or journals through the normal accounting services and preserves source identity and duplicate protection. It cannot hide posted transactions or bypass statutory books. The owner reserves the product decision; no module or commercial licence is declared here.



Core opening imports must not mark an existing business ready with unexplained AR/AP controls. Before those modules exist, accept only a supported core-only cutover whose reviewer has reconciled any retained control totals to external unpaid-document schedules, with that limitation visible. Later module activation must match those balances and schedules without reposting them. If this cannot be established, keep opening readiness unresolved.

## Versioned module contract

“Plug and play” means a compatible, reviewed package can be installed and enabled with declared dependencies and configuration; it does not mean arbitrary uploaded PHP can execute. Keep the existing application, bootstrap, router, MeekroDB connection and central posting interface. Start with project-owned modules in the same repository/package and a small explicit registry; a marketplace or remote installer is not required.

- A manifest declares stable module ID/version, supported core-contract versions, dependencies/capabilities, owned migrations/data, routes/navigation, permissions, settings, reports and API/MCP operations. Validate compatibility before enabling it for a company.
- Separate package installation/upgrades from per-company enablement. Installing a package never grants permission or activates it for every company. Use reviewed additive migrations; never rewrite applied migration checksums or automatically drop posted data.
- Enforce capability, actor/action permission, company/book scope, readiness and period checks on the server for every operation. Navigation and MCP tool discovery reflect those checks but do not replace execution-time authorization.
- A module owns its operational documents and immutable posting snapshots. Cross-module access uses typed service contracts; only the core service writes posted journals. Atomic source/ledger work and durable duplicate identities remain mandatory.
- Disabling blocks new module operations and explains blocked dependants. Refuse incompatible dependency removal. Preserve posted journals, source snapshots, audit, exports and authorized read access; re-enabling a compatible version restores operations. Corrections still use core linked reversals, but a module-backed document requires its supported correction service and subledger reconciliation; disablement must not allow an isolated ledger reversal to bypass them.
- Verify core-only install, each module alone with its required dependencies, selected combinations, upgrades, disable/re-enable, failure rollback and historical reads. Publish a compatibility matrix before claiming interchangeable modules.

Project-owned current core/POS code uses AGPL-3.0-or-later; published 0.1.0 through 0.1.5 previews retain MIT. [Licensing policy](LICENSING-POLICY.md) governs commercial licensing and future declared commercial modules, superseding the former no-paid-feature-tier statement. Dependency and legacy notices remain separate.

## Shop and restaurant dependencies

| Interface/capability | Required | Conditional |
|---|---|---|
| Basic non-stock/service checkout | Core + shared catalog + POS checkout | Tax adapter when applicable; contacts/AR for customer credit rather than anonymous immediate settlement. |
| Stocked shop | Basic checkout + inventory quantity/costing | Purchasing/AP for procure-to-pay; hardware and payment-provider adapters require separately tested support. |
| Restaurant | Core + catalog + checkout + restaurant order/table/modifier/kitchen module | Inventory/recipes for ingredient quantity and costing; AR for credit accounts; applicable tax/payment adapters. |
| Distribution | Core + catalog + inventory + distribution | AR for credit sales/collections, AP/purchasing for procurement, applicable tax and any later offline capability. |

Changing industry interface must not rewrite accounts, old documents or posted amounts. Snapshot the originating interface/module version on new operational documents where needed; keep historic documents readable with their original meaning. Offline entry is draft queuing only; checkout posting, period control and reversal execute on the server. Live payments and fiscal-device integration retain separate review gates.

## API and MCP contract decisions

The API is for integrations; MCP gives compatible assistants discoverable, typed access to the same business capabilities. Use an explicit versioned route namespace and OpenAPI description, with exact decimal strings, stable IDs, business DATE values, UTC event timestamps, bounded pagination, structured errors and correlation/command IDs. Final route/tool names and supported protocol versions are implementation decisions to record and test. OpenAPI describes an HTTP interface; it does not supply accounting validation or access controls. See the [official OpenAPI specification](https://spec.openapis.org/oas/v3.2.1.html).

Deliver reads before mutations. Scope machine/client identities to allowed companies/books and actions; support expiration/revocation and audit without shared global administrator credentials. Keep authentication transport separate from business authorization while reusing existing identities and service checks. For an HTTP MCP transport, implement the selected version's authorization/discovery requirements, token audience validation and secure token handling; do not treat a browser session cookie as a complete MCP authentication design. See [MCP authorization](https://modelcontextprotocol.io/specification/2025-11-25/basic/authorization).

After the sequenced e-commerce/storefront milestone, start controlled write access with draft and validation operations. Posting, reversal and period controls are distinct commands: require their specific scopes, show or return a reviewable financial effect and revalidate at execution. Configure review/approval policy per company, role and command; do not assume either unconditional AI acceptance or an unavoidable per-record queue. Retries return the durable result only for matching content. Tool annotations and model intent are not permission checks; the server must reject unauthorized or stale commands. See [MCP tools and their security considerations](https://modelcontextprotocol.io/specification/2025-11-25/server/tools).

No API or MCP command sends messages, collects payments or calls providers merely because it posts an accounting record. Those actions require an explicit separately enabled integration and authorization. Documentation alone does not implement routes, scopes, credentials, migrations or a protocol server.

## Parallel work and release boundary

Accounting-profile research, core report review and early module/API contract design can run alongside core implementation. Shipping dependent modules waits for their prerequisite gates. Reviewed jurisdiction-specific financial statements, multicurrency, multilingual formatting, alternative-book definitions and document scanning retain the requirements in the main roadmap; this sequence does not silently implement or remove them.

The core and bundled module lifecycle have the recorded evidence above. Complete in-flight API/MCP reads, then AR and AP, following the ordered table. Controlled commands follow e-commerce/storefront. Existing POS receipts remain readable after company disablement. Each milestone stays local until authorized publication, and passing technical tests remains distinct from accounting review and observed usability. Eight disabled [country tax catalogs](tax/README.md) prepare later modules without enabling tax calculations.

Every release updates the repository docs, GitHub Wiki, README, website and demo together. Record the package version and source commit, publish truthful capability/limitation changes, migrate the isolated demo safely, and verify each public surface. The release receipt must identify any surface still pending; a local edit is not a publication.


## Large-table interface and MeekroDB follow-up (15 September 2026)

The owner requested DataTables with server-side pagination for large lists. It is implemented in the local read-access candidate, with financial/access checks recorded in the integration receipt. The next order is reads, AR and AP; controlled commands follow e-commerce/storefront. Keep the compact P&L/Balance Sheet sections as readable financial statements; prioritize Transactions, Journals, account movements and larger bank lists for the table interface.

The local candidate uses bounded page lengths 25/50/100. Account running balances are calculated in canonical date/journal/line order before pagination. The DataTables adapter retains server-enforced company/book permissions, allowlisted sort fields/directions, validated filters, literal bounded search, scoped total/filtered counts and integer draw IDs. Do not calculate balances from the current browser page or redefine them by an arbitrary sort. Start ledger movement display in its canonical chronological order; broader search must retain an explicitly defined chronological-balance meaning. Return exact decimal strings; expose the same results through browser/API/MCP services with transport-specific authorization. Export uses the existing all-pages server workflow. The candidate bundles DataTables 3.0.4 locally and retains responsive/keyboard/no-JavaScript access. See [DataTables server-side protocol](https://datatables.net/manual/core/server-side) and [security guidance](https://datatables.net/manual/core/security).

MeekroDB audit: the current package pins v3.1.5 and uses a single configured connection, typed query placeholders, specialized row/field/column readers, associative insert/update helpers and nested transactions. The central transaction wrapper also handles full-transaction deadlock replay and savepoint state. These are substantial existing uses, not a database layer awaiting adoption. Remaining improvement candidates are shared filter/count/page contracts, narrow SELECT projections, measured query plans/index coverage and redacted timing hooks. Row walking for larger exports or bulk helpers for non-posted staging data should be introduced only if measurement justifies them. Raw SQL/parameter logging, an ORM, a second connection layer or generic upserts into posted financial records are not proposed. Library source/reference: [MeekroDB](https://github.com/SergeyTsalkov/meekrodb).

Acceptance: browser/API/MCP count and amount parity, no cross-company count/data leaks, bounded invalid requests, exact amounts, stable pagination under defined ordering, statement opening/running/closing reconciliation, responsive layouts and representative query timing. The local integration receipt records the implemented table adapter and measured synthetic queries. This document adds no endpoint or capacity claim.

## Installer placement — decision superseding the earlier plan

The [browser installer](INSTALLER.md) remains a planned adoption workflow using the existing migration/auth/onboarding services. Packaging distribution and the in-app updater now form the named milestone immediately after AP; this supersedes the former commands → installer → AR/AP order. Design may continue earlier, but the installation wizard does not need to create a customer's marketing website. That website-builder idea is parked. Current CLI installation and reviewed upgrade procedures remain available; no new installer/updater capability is implemented by this decision.
