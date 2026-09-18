# Module foundation: local Sprint 05

Date: 15 September 2026. This follows the [consolidated core checkpoint](CORE-COMPLETION.md) and implements module-roadmap milestone 3 for the required core and the bundled cash POS showcase. API/MCP reads are the next implementation milestone. This is local source and validation, not a published release or production retail approval.

## Implemented boundary

- `resources/modules/core.json` and `pos-showcase.json` declare stable IDs, package versions, contract 1, exact dependencies, exclusive capabilities, migration identities, routes, action roles, settings, reports, and API/MCP operation lists. The registry accepts only the two project-owned manifests. It loads metadata, never uploaded code.
- The required core cannot be disabled. New ordinary companies and existing companies upgraded from earlier packages start with POS disabled. Installing migration `010_module_lifecycle` does not activate optional functionality. Explicitly creating a sample company enables its showcase through the same audited owner service; the isolated demo follows that provisioning path.
- Owners review and enable, disable or apply a changed manifest at `/modules`. Accountants/viewers can read status/history but cannot administer modules. Browser POSTs require CSRF and the selected company/book. Public-demo visitors cannot administer modules.
- Enabling checks declared migration receipts and source checksums, exact dependency versions, the reviewed manifest digest and company revision. The service locks the company and saves the state and immutable action receipt in one transaction. A matching retry returns the stored values; changed content and stale revisions fail. Audit insertion failure rolls back the state change. Packages/migrations are installed by the existing command-line process, never by a browser action.
- POS review and checkout repeat company/book, action permission, current module state, package compatibility and readiness checks in the service transaction. Navigation is only a hint. Disabling blocks new cart/review/checkout/retry operations, including a previously prepared checkout. Current reads prevent an older transaction snapshot from retaining authorization after disablement.
- Existing receipt snapshots, source documents, journals and core exports remain readable under their existing company/book permissions. The existing full-document linked reversal is available for this cash-only showcase and retains its original receipt. This is not a future inventory/AR return implementation. Re-enabling a compatible version restores new operations.

## Supported compatibility matrix

| Installed metadata | Company state | New operations | Historical reads |
|---|---|---|---|
| Core 1.0.0, contract 1 | Required | Accounting, reports and exports | Available with membership |
| Core 1.0.0 + POS showcase 1.0.0 | POS disabled/default | Core only | Existing POS receipts and accounting sources remain available |
| Core 1.0.0 + POS showcase 1.0.0 | Owner enabled, matching digest and migrations | Core plus sample cash checkout | Available with membership |
| Missing/incompatible migration or changed enabled POS metadata | Review/repair required | POS fails closed | Existing receipt/source services remain available |

There is no third-party installer, module marketplace, automatic dependency downloader, arbitrary semantic-version range, AR/AP/inventory/tax adapter or business API/MCP transport in this matrix. Current `api_operations` and `mcp_operations` lists are empty. Disabling retains tables/data; uninstall/drop-data is not an available lifecycle action. A manifest change requires a new owner review before operation even if its display version was accidentally left unchanged.

## Reserved contracts for the next consuming modules

The following are interface requirements, not implemented master-data or tax services. They reserve ownership before AR/AP schemas are designed and avoid unused duplicate tables now.

| Contract | Required input and result | Ownership and rejection rules |
|---|---|---|
| `contacts.identity.v1` | Actor/company; stable contact ID and revision; display/legal name; explicit customer/vendor roles. Consumers retain contact ID plus their immutable issued-document name/address snapshot. | One company-scoped identity may have both roles. AR/AP add their own terms and subledger attributes. Cross-company, inactive or stale identities fail at document execution. No module creates a second contact database. |
| `catalog.item.v1` | Actor/company; stable item ID/revision; SKU; service/non-stock/stocked kind; unit ID; exact quantity and price strings; applicable tax classification. A priced review returns identity/version and immutable description/unit/price inputs. | Shared catalog owns identities, not on-hand stock or valuation. Inventory owns quantity/costing; checkout owns the reviewed sale. The current six-product fixture remains explicitly sample and is not a production master catalog. |
| `tax.calculate.v1` | Actor/company/book; business date; jurisdiction and entity registration profile; policy/adapter ID and version; currency/precision; document kind; line IDs, classifications, exact quantities/prices/discounts and inclusive/exclusive basis. | Reserve `tax.calculate` for an independently reviewed adapter. Missing capability, unsupported classification/date/profile or uncertain applicability returns an explicit unsupported/error state, never a fabricated zero tax. |
| Tax result snapshot | Exact net/tax/gross per line and total; components with base/rate/amount; rounding method and adjustment; adapter/policy/effective-version and source/evidence references; digest of reviewed inputs/result. | Use fixed precision and an explicit rounding boundary. The owning document saves the reviewed inputs/result atomically with posting and revalidates its digest/version at execution. Corrections link the original snapshot. Core consumes balanced lines and never infers a jurisdiction's tax policy. |

No existing disabled tax research catalog satisfies this contract. A declared explicit no-tax sample scenario is distinct from an unsupported taxable business. The first required tax adapter must precede production acceptance for affected AR/AP/POS documents. Units, quantity precision and discount/rounding policy need the consuming module's reviewed examples before schema implementation.

## Installation and operator recovery

1. Back up the application configuration and database using the existing runbook. Install a reviewed package and run the existing preflight/migration commands. Never rename or edit applied migrations; both distinct `006_*` identities remain valid. Migration `010_module_lifecycle` adds two tables and two immutable-history triggers without rewriting financial data.
2. Sign in as the company owner and open **Modules**. Inspect the installed version and status, enter a reason and enable the showcase only for a suitable sample evaluation. Installing the package alone leaves existing companies disabled.
3. If a revision/package warning occurs, reload and review the current state. A migration/checksum failure requires repair through the normal deployment/migration recovery process, not an unchecked browser override. Enablement failures leave the previous committed state intact.
4. To stop checkout, disable with a reason. Verify the status and recent-change receipt. Reports and old sources remain available. New operations already holding the transaction locks complete before the disable decision; later operations observe the disabled state.

No real provider, payment, message or remote account is involved in these steps. The runtime is local PHP 8.5/MySQL 8.4 with the existing MeekroDB and authentication services.

## Executed validation

| Check | Evidence |
|---|---|
| `docker compose --profile test run --rm test composer check` | 121 tests, 0 failures; 90 PHP files linted; PHPStan 0 errors; seven sample packs validated and eight invalid packs rejected. Includes two-period reconciliation, core-only post/export/reversal, missing dependencies/contracts, stale/revoked identity, immutable audit, failure rollback, concurrent retries and current-state disable checks. |
| `python tests/module-http-smoke.py` | 26 assertions, 0 failures at local port 18200. Owner/viewer/anonymous access, CSRF, cross-scope, manifest/revision conflicts, enable/retry, reviewed cash sale, disable/direct POST denial, retained source/receipt/export, unchanged ledger totals and re-enable. Sample company 27, receipt document 49. |
| Chrome via Playwright | 12 functional assertions and 12 captures across 1440/768/390px: disabled, stale-package error, enabled and disabled-with-history. Keyboard action and error focus passed; no document overflow, broken images, page errors or external requests. Local ignored artifacts: `output/playwright/modules/`. |
| Fresh and upgrades | Fresh 11-migration install/post/replay and upgrades from foundation, released 0.1.2 core and opening-local baselines passed in isolated random databases. Existing accounts and posted journal/line values preserved. |
| Isolated demo | Restricted runtime grants, visitor isolation, concurrent reset protection, setup/admin denial, real generation replacement and sample provisioning passed in `db_test` only. |
| Backup/restore | 27 table definitions/data checksums, 12,735 rows, 35 guard triggers and 11 migration receipts matched after isolated restoration; scoped source links and balanced journals passed. |
| Packaging | Six builder tests passed; allowlist includes both manifests, both helpers, migration and view. Package construction/restore results are recorded in the final closeout below. |

The full run initially exposed an isolated POS retry fixture missing the newly required module metadata and a test comparing MySQL JSON field order. The fixture now supplies only the module/install metadata while still omitting the catalog; retry recovery passes. Durable result comparison checks values independent of JSON key order. The browser table heading says Recorded because existing terminal formatting localizes its UTC-stored time.

### Final package verification

Built an unpublished `0.1.3-local` evaluation ZIP from clean source commit `22b5725`, using the unchanged production dependency set from the prior package. The archive is retained locally under `.cache/sprint05-package/`; it is not a GitHub release or replacement for the public download. Its SHA-256 is `a37bf7ec564c904c9d348b99cb4132d240bb7f80c2dff73e7fe057fb9493565f`. An independent rebuild from the same source produced identical bytes.

The actual unpacked archive, mounted read-only with its own production vendor and resources, passed fresh installation and all three upgrade baselines. External acceptance scripts were mounted separately, outside the package. A second fresh disposable database proved default-off state, owner enablement, server review, one 12.7500 sample cash sale, exact CSV export, disablement denial, retained receipt, balanced linked reversal and re-enable with three audit receipts. Every temporary package database was removed after its check; development and existing test data remained intact. The first attempt to nest a new helper mount inside the read-only package was rejected by Docker before PHP execution; mounting the harness outside the package resolved it without altering the extracted files.

Final targeted PHP lint/PHPStan checks and upgrade checks also passed after adding explicit assertions that installation never activates ordinary companies or invents owner decisions. The only source changes after the packaged commit are documentation of these results and current roadmap wording.

## Scope and next milestone

The technical scope of this bundled lifecycle is complete. Independent accounting/security review, observed core-user acceptance, production hosting/load validation and jurisdiction-qualified reports remain open gates. Schema installation failure recovery follows the existing migration runbook; no transactional DDL rollback is promised.

Next: milestone 4, versioned API and MCP reads over the same scoped services, exact decimal strings, bounded pagination, revocable machine access and real local-client parity tests. Mutation commands follow in milestone 5; optional AR/AP follow afterward. The [website SEO/campaign handoff](../../design/website/README.md#seo-and-campaign-handoff-status) remains a separate tracked workstream.

Files changed: two manifests, module services/view/migration, POS service/routes/navigation/sample provisioning, regression and HTTP tests, package allowlist, PHPStan return annotation and existing status/architecture/development/POS docs. References read: repository instructions and local product/design/accounting/roadmap/release docs; official MCP/OpenAPI references for the next adapter design; Claude website/marketing plan and website handoffs. Google Drive: none. Migrations: yes, `010_module_lifecycle`; schema changed: yes, local databases only. Raw secrets exposed: no. External calls: read-only official specification research, no application/provider or account writes. Live/production changed: no; no push, deployment, campaign send, payment or public release.
