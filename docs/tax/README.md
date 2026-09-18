# Tax research and disabled seed catalog

Research date: **15 September 2026**. Eight country catalogs prepare the optional tax module while core accounting is completed. **Researched is not activated or supported.** Every candidate remains disabled and unreviewed; no runtime importer or calculator consumes these files. Applicable tax obligations still have to be supported before an affected operational workflow is accepted for production. [Module order and gates](../MODULE-ROADMAP.md), [core accounting rule register](../accounting/CORE_RULE_REGISTER.md).

| Country | Currency metadata | Research and candidate file |
|---|---|---|
| Pakistan — first review priority | PKR | [Pakistan research](PAKISTAN.md), [catalog](../../resources/tax/pakistan.json) |
| United Kingdom — second | GBP | [UK/UAE research](UK-UAE.md), [catalog](../../resources/tax/united-kingdom.json) |
| United Arab Emirates — third | AED | [UK/UAE research](UK-UAE.md), [catalog](../../resources/tax/united-arab-emirates.json) |
| Malaysia | MYR | [Asia research](ASIA.md), [catalog](../../resources/tax/malaysia.json) |
| Bangladesh | BDT | [Asia research](ASIA.md), [catalog](../../resources/tax/bangladesh.json) |
| Sri Lanka | LKR | [Asia research](ASIA.md), [catalog](../../resources/tax/sri-lanka.json) |
| Nepal | NPR | [Asia research](ASIA.md), [catalog](../../resources/tax/nepal.json) |
| Singapore | SGD | [Asia research](ASIA.md), [catalog](../../resources/tax/singapore.json) |

The remaining five countries are parallel discovery, without an implied release order. Currency metadata does not extend runtime currency support or choose accounting/tax rules. Every catalog covers `restaurant`, `membership_club`, `pharmacy`, `trader`, `distributor`, `retail_shop` and `workshop`. An industry profile lists alternatives and classification questions; it does not instruct the application to combine every referenced tax.

## Schema 1 contract

- Required state: integer `schema_version: 1`, `status: research_only`, boolean `enabled: false`, boolean `review_required: true`; every regime has `review_status: unreviewed`. Unknown fields are rejected, including activation flags hidden on a regime.
- Country identity uses the eight declared ISO country/currency pairs and matching filenames. Source and regime IDs have their own country prefix and are unique across the catalog. Source references are exact, local and case-sensitive; ID uniqueness is also checked without regard to case.
- Sources retain title, HTTPS URL without credentials, publisher and `checked_on`. Each candidate has an authority, jurisdiction, tax family, applicability, non-empty conditions and source references. The validator checks structure and links between records, not the truth of a source, its current availability or completeness of amendments.
- `research_date` and `checked_on` are research metadata. `effective_from`/`effective_to` describe a sourced legal scope or remain null. A same-day commencement can be legitimate; future and expired rules can also be useful research. Date syntax/order checks cannot establish legal effect. Never fill missing legal dates from a check date. A null end date does not establish indefinite validity.
- `rate_percent` is null or a canonical nonnegative decimal string with up to four whole and four fractional digits. This is a representation bound, not a 100% legal ceiling; duties can exceed 100%. Numeric floats, negative values, exponent notation and invented single rates for schedules are rejected or excluded from authoring. A sourced numeric value still needs transaction-specific approval.
- Each of the seven industry profiles has mapped regimes and at least two classification questions. Both directions of regime/profile coverage are checked. Missing, unresolved, exempt, outside-scope and not-registered are distinct from an established taxable zero rate; none may silently become zero.
- Supported family labels distinguish `vat`, `gst`, `sales_tax`, `service_tax`, `input_tax`, `income_tax`, `corporate_income_tax`, `personal_income_tax`, `advance_income_tax`, `withholding_tax`, `minimum_tax`, `turnover_tax`, `turnover_levy` and `supplementary_duty`. They identify research subjects, not implemented calculation engines. Additions require an intentional contract change.

Full contracts are embodied by the authored JSON and [standalone validator](../../tools/validate-tax-catalog.php). Explicit non-country JSON Schema files named `schema.json` or `*.schema.json` may be skipped; ordinary files with missing country fields fail. All eight country files must be present. No external dependency, application bootstrap, database connection, network request or write operation is used by validation.

## Validate locally

```text
php -l tools/validate-tax-catalog.php
php tools/validate-tax-catalog.php --self-test
```

The self-test mutates copies in memory to prove rejection of unsafe states, missing evidence fields, malformed rates/dates, identifier collisions and broken references. It also checks valid representation boundaries, same-day dates and schema-file handling. It does not alter the files or exercise real tax calculations. A passing run means **catalog structure valid**, not law verified, filing ready or accountant approved.

## Later reviewed profiles and transaction snapshots

Import is future module work, separate from copying research JSON into the repository. Before enabling a profile, record entity/legal form, authority/jurisdiction, registration, business/transaction scope, relevant tax and accounting periods, source edition/entry, amendments, interpretation, reviewer identity/date and approved policy version. Review input recoverability and withholding/settlement independently of output rates and profit tax. Qualified review must cover the complete chosen scope; ICAP/ICMAP/ACCA references are guidance, not automatic product certification.

Version approved profiles rather than overwriting them. Preserve the profile/source version, transaction tax point, classifications, registration evidence, taxable bases, exact rates/amounts, rounding, recovery decisions and review receipt with each calculation. Post the financial effect through the existing atomic, scoped, idempotent core service. Credit notes, reversals and tax adjustments retain links to the original calculation; a newly researched rule must not rewrite posted history or an issued result.

Before implementation acceptance, reconcile representative sale, purchase, mixed/exempt/zero, refund, withholding and period-crossing examples to the ledger and tax controls. Reject missing or overlapping classifications, unavailable required modules and stale previews. An entity's financial-reporting framework and its tax profile remain separately selected. Provider filing, fiscal-device connections and payments require their own reviewed implementation and authorization.

Official sources and access limits are listed in the three country research documents and each catalog; those documents link to the publications without bundling copyrighted reference sets. No Google Drive references were supplied for this catalog. Country-specific source gaps, historical/future date traps and qualified review remain open even when structural validation passes.
