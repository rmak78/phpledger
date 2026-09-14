# Chart of accounts research

Research snapshot: **14 September 2026**. Status: **unreviewed; no installable templates or regulatory coverage delivered**.

The installation/business-onboarding wizard should offer a small, understandable chart assembled from a country-neutral core, one business profile, and an optional reviewed country package. This research prepares that catalog. It does not modify the current six-account foundation, activate tax rules, or change existing companies.

## Findings that affect the product

- **Start with the business, not a list of hundreds of accounts.** Offer professional services/agency, retail, wholesale/distribution, restaurant/cafe, pharmacy, jewelry, and light manufacturing. Show the recommended accounts with a short explanation and let an accountant inspect details. Specialist account suggestions do not mean the corresponding ERP module is implemented.
- **Keep account purpose separate from its number and label.** A bank, receivable control, or inventory account needs a stable posting role. Numbers, display names, translations, and report groupings can vary without changing its identity. ERPNext's documented distinction between root categories, functional types, and party subledgers is useful comparison evidence. [ERPNext chart guidance](https://docs.frappe.io/erpnext/chart-of-accounts) (SRC-ERP-DOC).
- **A country chart is not a tax engine.** Pakistan's federal goods/ICT services context and provincial services authorities require a registration/jurisdiction model; India's GST components require separate controls. Select the country explicitly and collect relevant registrations only when a reviewed country package is available. No rates or filing behavior are inferred from an account name. [FBR basics](https://www.fbr.gov.pk/sales-tax/51148/101149), [Punjab services act](https://reg.pra.punjab.gov.pk/ptms/SalesTaxAct2012.aspx), [GST credit ledger](https://tutorial.gst.gov.in/userguide/ledgers/Electronic_Credit_Ledger.htm).
- **Use upstream catalogs as evidence, with review.** Five Odoo charts were inspected at a pinned source revision; India and UAE ERPNext trees provide a second comparison. Their licenses and limitations are recorded in [Sources](SOURCES.md). No upstream chart has been copied into PHP Ledger's runtime.
- **Migration is part of setup.** Keep the user's existing chart where sensible, map it to required roles, and reconcile opening receivables/payables against unpaid documents once. An uncertain match must remain visible and unresolved, rather than creating an account automatically.

## Coverage and priorities

| Package | Evidence gathered | Next gate |
|---|---|---|
| Country-neutral core | Original candidate accounts and common posting roles | Accounting review and synthetic transaction examples |
| Seven business profiles | Original candidate accounts, scope limits, and industry questions | Practitioner review per business profile; inventory/specialist workflows remain later releases |
| Pakistan (launch research priority) | Pinned 123-row Odoo chart; federal and Punjab/Sindh authority references; financial-reporting applicability source inventory | Pakistan accountant review; entity classification and other provincial coverage; current rules and reporting requirements |
| India (launch research priority) | Pinned 102-row Odoo chart; ERPNext account tree; GST ledger guidance and ICAI reporting references | Indian accountant review; entity/GST registration model, tax-rule and disclosure validation |
| UAE (later extension) | Pinned 170-row Odoo chart and ERPNext tree; FTA recordkeeping guide | Current jurisdiction/entity/tax treatment review |
| United Kingdom (later extension) | Pinned 118-row Odoo chart; HMRC digital VAT recordkeeping guidance | Entity/reporting-framework and VAT/MTD implementation review |
| United States (later extension) | Pinned 101-row Odoo chart; IRS small-business recordkeeping guidance | State/local sales-tax, entity and reporting-policy research |

Counts refer to source rows, not approved PHP Ledger accounts or coverage scores. The core remains country-neutral. Pakistan/India are research priorities, not a declaration of tax support or a change to the roadmap's country-adapter gates.

## Research package

- [Account candidates](ACCOUNT_CANDIDATES.md): original core, industry, and country role proposals with types and normal balances.
- [Template and wizard model](TEMPLATE_MODEL.md): composition, source/version provenance, installation snapshot, permissions, upgrades, and migration reconciliation.
- [Sources](SOURCES.md): primary references, pinned datasets, licensing, observed quality issues, and access limits.
- [Discovery gaps](DISCOVERY_GAPS.md): decisions and evidence needed before an installable package is approved.
- [Structured research index](../../resources/coa/research-index.json): research metadata for later catalog work; `runtime_importable` is false.

## Future implementation sequence

1. Review the neutral core and service/retail candidates with accountants, using purchase, sale, receipt, payment, reversal, and cutover examples.
2. Approve a catalog format and add semantic roles/template provenance through a separate reviewed foundation change. Keep the existing central posting interface.
3. Build preview-and-confirm onboarding and role-aware chart import. Release only reviewed templates; keep unreviewed choices unavailable for real setup.
4. Validate Pakistan and India independently. First publish reviewed account packages; tax calculation, electronic invoicing, and filing integrations each need their own evidence and implementation.
5. Expand industry accounting alongside the relevant ERP module, and evaluate UAE/UK/US after local research and demand justify them.

No interviews or accountant approvals were obtained in this research session. Sources were read publicly; no people were contacted, no external records were changed, and no production changes were made.
