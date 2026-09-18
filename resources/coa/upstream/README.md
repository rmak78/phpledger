# Upstream chart-of-accounts mirror

**Status: Research. `runtime_importable: false` on every file. Nothing in this folder is installed into a company, shipped in the release package (see `tools/package-files.json`), or accountant reviewed.**

This folder mirrors every country chart of accounts that Odoo and ERPNext publish, converted into one PHP Ledger-shaped JSON schema so that country packages can be authored, compared and reviewed against them. It extends the pinned-source research in [docs/coa/SOURCES.md](../../../docs/coa/SOURCES.md) — the same two commits are used — and is governed by the [regional catalogue contract](../../../docs/coa/regional-program/CATALOG-AND-INSTALLATION-CONTRACT.md): a mirrored chart is evidence, never a released package.

| Source | Pinned revision | Licence | Charts |
|---|---|---|---|
| [odoo/odoo](https://github.com/odoo/odoo) branch `19.0` | `f84eeb3ed1421e0cc07c906ff6444734dc01d35f` (2026-09-14) | LGPL-3.0 ([LICENSE](odoo-19.0/LICENSE)) | 161 templates covering 120 countries/territories plus the generic and SYSCOHADA/SYSCEBNL regional charts |
| [frappe/erpnext](https://github.com/frappe/erpnext) branch `version-16` | `4048fb70e14d1843956fcdabb7c3cca75a1cbcdd` (2026-09-08) | GPL-3.0 ([LICENSE](erpnext-version-16/LICENSE)) | 99 charts: 73 `verified` JSON trees, 24 `unverified` JSON trees, and the two Python "Standard" charts |

`index.json` is the catalogue: one entry per chart with counts and quality flags, plus a per-country roll-up (122 countries have at least one chart; 60 have both sources).

## Layout

```
resources/coa/upstream/
  index.json                         catalogue + per-country roll-up
  odoo-19.0/<template>.json          one file per Odoo chart template (pk, in, ae, uk, us, sa, ...)
  odoo-19.0/csv/<template>.csv       flat account list for review in a spreadsheet
  odoo-19.0/LICENSE                  Odoo's LGPL-3 text
  erpnext-version-16/verified/*.json   and /unverified/*.json, with csv/ beside each
  erpnext-version-16/LICENSE         ERPNext's GPL-3 text
```

Odoo file names are Odoo template codes, not ISO codes: `uk` is GB, `jo_standard` is Jordan, `de_skr03`/`de_skr04` are the two German charts, `ca_2023` is Canada. Use `index.json` (`country_code`) to find a country rather than guessing the file name.

**Alias charts.** 78 files carry `"accounts_identical_to": "<chart_id>"` and an empty `accounts` array because upstream ships the same rows under several names: Odoo's 17 OHADA member-state templates all inherit `syscohada`/`syscebnl` unchanged, the French overseas templates (`gf`, `gp`, `mq`, `re`, `yt`, `mc`) inherit `fr`, `xi` inherits `uk`, and ERPNext ships one SYSCOHADA tree per member state. The alias file still records the country, role defaults and provenance; open the target file for the rows.

## Chart document (`phpledger.coa.upstream-chart`, format version 1)

| Field | Meaning |
|---|---|
| `chart_id`, `name`, `country_code`, `country_name`, `country_basis` | Identity. `country_basis` says whether the country came from the template's own fiscal country, was inherited from a parent template, or derived from the template/module code. Country names come from `resources/locale/country-defaults-cldr48.json`. |
| `source` | Repository, branch, commit, commit date, retrieval time, licence and attribution, contributing modules, the exact upstream files with `sha256` and row counts, and (Odoo) the template inheritance chain. |
| `template` | Odoo: `code_digits`, bank/cash/transfer code prefixes, default tax ids, Anglo-Saxon flag. ERPNext: hierarchy note and whether account numbers exist. |
| `role_defaults` | Which upstream accounts Odoo's template designates as the default receivable, payable, income, expense, stock valuation, POS receivable, bank suspense, FX gain/loss, cash-difference and early-payment-discount accounts, resolved to codes. For ERPNext the accounts typed Receivable/Payable/Cash/Bank/Stock/COGS/Round Off/Temporary. This is the starting point for PHP Ledger's `runtime_role` bindings. |
| `type_map` | The exact mechanical mapping applied from the upstream `account_type` vocabulary. |
| `summary` | Counts by root type and subtype, duplicate codes, missing codes, unmapped types, translation languages, and (ERPNext) roots that carry no `root_type`. |
| `groups` (Odoo only) | `account.group` prefix ranges with a derived parent and path; accounts point at their group through `parent_source_id`. |
| `accounts` | One object per line. |

Each account row has `source_id` (Odoo xmlid, or the ERPNext ` / `-joined path), `code`, `name`, `root_type`, `subtype`, `normal_balance`, `is_group`, `parent_source_id`, and — only when present — `translations` (Odoo `name@lang` columns; Arabic names exist for SA, OM, QA, KW, BH, EG, JO, IQ and LB — not for AE), `reconcile`, `description`, `role_hint`, `flags`, `depth` (ERPNext) and `source` (the untouched upstream values: `account_type`, `tag_ids`, `tax_ids`, `account_category`, `tax_rate`, ...). Absent keys mean "not provided upstream".

### Vocabulary

`root_type` is PHP Ledger's five-value enum (`asset`, `liability`, `equity`, `income`, `expense`) or `null` for Odoo `off_balance` accounts and for ERPNext nodes whose tree gives no root type. `normal_balance` follows the root type; ERPNext *Accumulated Depreciation* is marked `credit` with the flag `contra`. Odoo has no contra marker, so contra assets there still read `debit` — a review task, not a fact.

`subtype` is a shared vocabulary across both sources: `receivable`, `payable`, `cash_bank` (Odoo) / `cash`, `bank` (ERPNext), `credit_card`, `current_asset`, `non_current_asset`, `prepayment`, `fixed_asset`, `accumulated_depreciation`, `capital_work_in_progress`, `inventory`, `tax`, `stock_received_not_billed`, `asset_received_not_billed`, `service_received_not_billed`, `stock_delivered_not_billed`, `stock_adjustment`, `valuation_expense`, `asset_valuation_expense`, `current_liability`, `non_current_liability`, `equity`, `retained_earnings`, `income`, `other_income`, `expense`, `direct_cost`, `depreciation`, `other_expense`, `chargeable`, `temporary`, `round_off`, `off_balance`, `group`, `unspecified`.

`role_hint` names the current PHP Ledger runtime role a row would plausibly bind to — `receivables`, `payables`, `cash_bank`, and `income`/`expense` on the account Odoo itself uses as the default. It is a hint for the package author; the contract's `runtime_role` still requires a reviewed decision, and no hint is emitted for groups. No `owner_equity` hint is derived because neither source designates a single owner-equity account.

`flags`: `off_balance`, `missing_code` (only in charts that otherwise carry numbers), `missing_account_type`, `unmapped_account_type` (none occurred at these pins), `inactive_in_source`, `contra`, `missing_root_type`, `default_for_income`, `default_for_expense`.

## Coverage for PHP Ledger's stated markets

| Country | Odoo 19.0 | ERPNext v16 | Notes |
|---|---|---|---|
| Pakistan | `pk` — 123 accounts, 7-digit codes, 44 groups | — | Only upstream PK chart. ERPNext has none (already noted in the ERPNext review). |
| India | `in` — 102 accounts | `verified/in_standard_chart_of_accounts` — 49 posting accounts, names only | Odoo carries GST/TDS/TCS accounts; ERPNext tree is the generic standard chart plus GST groups. |
| United Arab Emirates | `ae` — 170 accounts | `verified/ae_uae_chart_template_standard` — 197 posting accounts, names only | Both have VAT input/output/reverse-charge distinctions. |
| United Kingdom | `uk` — 118 accounts (`xi` = Northern Ireland alias) | — | |
| United States | `us` — 101 accounts (module `l10n_us_account`) | ERPNext "Standard"/"Standard with Numbers" are US-style generic charts (no country) | |
| Saudi Arabia, Oman, Qatar, Kuwait, Bahrain | `sa` 169, `om` 139, `qa` 136, `kw` 136, `bh` 137 | — | All carry Arabic `translations` — useful for the Arabic UI plan. |
| Singapore | `sg` — 135 accounts | `verified/sg_default_coa` (138) and `sg_fnb_coa` (191, restaurant profile) | ERPNext's F&B chart is a ready comparison for the restaurant-cafe industry profile. |
| Malaysia, Sri Lanka, Bangladesh | `my` 77, `lk` 100, `bd` 135 | — | |
| Egypt, Jordan, Iraq, Lebanon | `eg` 205, `jo_standard` 140, `iq` 137, `lb` 373 | — | Arabic translations present. |

No upstream chart has Urdu names. Odoo's `l10n_pk` module has no `ur.po` at this pin either.

## Known limitations of the upstream data

- **ERPNext `unverified` charts are structurally incomplete.** Sixteen of them (CH, CL, CN, DE, EC, ES, GR, HR, IT, MA, PE, PL, PT, RO, SYSCOHADA, VE) have root nodes without a `root_type`, so 13,258 nodes carry `root_type: null` and the flag `missing_root_type`. The converter does **not** guess a classification from names — that would violate the contract's rule against silently converting Unknown. Prefer the Odoo chart for those countries.
- **Many ERPNext trees have no account numbers** (`account_numbers_present: false`), including India, UAE and Singapore. Their value is the hierarchy and typing, not numbering.
- **Odoo `off_balance` accounts** (489 rows in the non-alias charts, mostly Spanish, French and OHADA) have no PHP Ledger root type and are kept only with `root_type: null` and the flag `off_balance`.
- **Odoo charts are flat**; hierarchy comes from `account.group` prefix ranges, and only about half the templates define them (PK has 44 groups and US 14; IN, AE, GB, SA, SG, MY and LK have none, so their structure must come from code prefixes or the ERPNext tree).
- Upstream account numbers are Odoo's or ERPNext's editorial choices, not statutory numbering, and neither source is evidence of current tax rules.

## Licensing

Odoo's charts are LGPL-3.0 and ERPNext's are GPL-3.0; both are compatible with PHP Ledger's AGPL-3.0-or-later core, and the licence texts are mirrored here. They are **not** compatible with the planned dual-licensed/commercial track: a country package copied from these files could not be offered under a proprietary licence. The contract already requires released packages to be original PHP Ledger work informed by sources, with a recorded licence and attribution decision per component. Whether a bare list of account names and numbers is copyrightable at all is a question for counsel, not something this mirror decides.

## Regenerating

```
git clone --filter=blob:none --depth 1 https://github.com/odoo/odoo.git    # then checkout f84eeb3e...
git clone --filter=blob:none --depth 1 https://github.com/frappe/erpnext.git  # then checkout 4048fb70...
python3 tools/import-upstream-coa.py --odoo-src ../odoo --odoo-branch 19.0 \
    --erpnext-src ../erpnext --erpnext-branch version-16 --out resources/coa/upstream --repo-root .
```

The tool needs only the Python standard library and reads nothing from the network. Sparse checkouts of `addons/l10n_*/{data/template,models,__manifest__.py}`, `addons/account/{data/template,models/template_generic_coa.py,__manifest__.py}` and `erpnext/accounts/doctype/account/chart_of_accounts/` are sufficient. Re-running against a different commit changes `source.commit`, the file digests and possibly the rows; treat that as a new research snapshot and record it in `docs/coa/SOURCES.md`.

## Next step

Authoring a PHP Ledger country package (contract section 3) from one of these files means: choose a semantic key per account, decide `runtime_role` and capability bindings from `role_defaults`/`role_hint`, keep or renumber codes, drop `off_balance` rows, add `reason` text, and record the source file digest in `source_ids`. That output is a new file under a package builder, not an edit to this mirror.
