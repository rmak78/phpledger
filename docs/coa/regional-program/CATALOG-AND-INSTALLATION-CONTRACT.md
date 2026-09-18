# Regional chart catalogue and installation contract

**Status: proposed design for owner, UX and accounting review. Recorded 16 September 2026. No application, migration, catalogue release or regional compliance capability is implemented by this document.**

Implementation is gated on acceptance of both this contract and the companion setup/workflow designs. A research record, a working prototype and a package that an ordinary company may install are separate deliverables. The published accounting starter remains usable with its existing neutral chart while this work is reviewed.

## 1. Purpose and inspected baseline

Offer a small, explainable chart for a business through **one versioned neutral core + one primary industry profile + zero or one country package + explicitly selected, reviewed optional account groups**. Keep the accounting core common across countries. Selecting a chart must not activate country tax rules, statutory reports, an industry workflow or a payment connector.

This is an extension of the existing PHP/MeekroDB architecture and setup services, not a separate installer or database layer. It does not introduce additional accounting books. All installation operations retain the current company/book scope.

### Existing behaviour to preserve

| Inspected source | Current behaviour and consequence |
|---|---|
| [Neutral starter JSON](../../../resources/coa/core-starter-1.0.0.json) | Six accounts with stable semantic keys and runtime roles. Its literal status is `prerelease-foundation`. Do not relabel it as a newly reviewed regional release or replace its bytes in place. |
| [Setup services](../../../www/phpledger/includes/functions/setup_functions.php) | Exact starter-file digest check, durable setup request identity, fresh/existing/sample modes, isolated sample creation, chart mapping and an installed template record already exist. Extend these services. |
| [Migration 002](../../../www/phpledger/install/migrations/002_product_slice.php) | `pl_accounts` already has semantic keys and roles, and `pl_template_installations` has one record per company. The proposed multi-version installation history requires a later additive migration. |
| [Account services](../../../www/phpledger/includes/functions/core_functions.php) | Account code, root type and role are fixed after creation; name/status changes are audited. A catalogue must not silently rename codes or reclassify an existing account. |
| [Opening conversion](../../../www/phpledger/includes/functions/opening_conversion_functions.php) and [starter record](../../repository/sprint-06/ACCOUNTING-STARTER.md) | Reviewed unpaid-document and stock details attach to existing opening journal amounts. The catalogue must preserve the one-time opening posting and authoritative operational ledgers. |
| [Module services](../../../www/phpledger/includes/functions/module_functions.php) | Existing manifests, owner-controlled activation and dependencies govern optional operations. A chart recommendation does not grant permission or enable a module. |

**Historical design conflicts are recorded, not silently implemented.** The earlier [template model](../TEMPLATE_MODEL.md) says semantic keys and installed snapshots do not yet exist and proposes an opening path that posts unpaid documents through migration clearing. Those passages predate the current runtime. This contract uses the inspected baseline above: existing account identities remain stable; opening conversion does not post the balance again. Older suggested core keys such as `core.bank.operating` also do not replace the published `core.cash_bank` binding. A different opening or account-identity model would require a separate decision.

Current sample provisioning adds sample metadata to the installation row within its creation transaction. The current row is therefore not evidence of the fully immutable, versioned history proposed in section 7. Preserve its exact historical contents during any later upgrade.

## 2. Composition and stable identities

### Layer rules

| Layer | Cardinality | Responsibility |
|---|---:|---|
| Neutral core | Exactly 1 | Common account meanings and the minimum bindings needed by the accounting core, including shared AR/AP. |
| Primary industry | Exactly 1 in a composed catalogue selection | A bounded selection of business account needs. It may add no accounts where the neutral set suffices. It never imports a whole second industry implicitly. |
| Country | 0 or 1 | Reviewed suggestions for a stated country, legal form and reporting scope. Country selection is independent of functional currency. |
| Optional account group | 0 or more, explicitly selected | A small reviewed extension with declared dependencies and conflicts. It cannot act as a hidden second country or industry package. |

An undecided user can save a setup draft or deliberately use the existing neutral-only setup. That fallback is visibly labelled **Neutral chart only**; it is not a fabricated industry choice or evidence that a composed regional package is ready. Do not require an answer about regional obligations to explore a separate synthetic sample.

The agreed country IDs are ISO alpha-2 **PK, IN, AE, GB, US**, displayed as Pakistan, India, United Arab Emirates, United Kingdom and United States. Keep country ID, jurisdiction/registration scope, reporting framework, legal form and currency as separate fields. Do not infer any of them from an IP address, browser language, tax identifier or currency.

The coordinated primary industry IDs for this research program are:

- `professional-services`
- `seasonal-services`
- `retail-shop`
- `wholesale-trader`
- `distributor`
- `restaurant-cafe`
- `membership-club`
- `pharmacy`
- `jewelry`
- `light-manufacturing`
- `service-workshop`

These identify research profiles, not eleven released templates or shipped sample companies. Retain candidate account namespaces from [account candidates](../ACCOUNT_CANDIDATES.md): `core.*`, `service.*`, `retail.*`, `wholesale.*`, `restaurant.*`, `club.*`, `pharmacy.*`, `jewelry.*`, `manufacturing.*` and `workshop.*`. A distributor can reuse wholesale meanings. Seasonal-service additions require an explicit new-key proposal; seasonality alone does not justify duplicate income/control accounts.

Package IDs are stable lowercase ASCII identifiers, independent of translated labels. Each released package version is immutable. A version change is required for any changed account definition, dependency, applicability, attribution or file content. A composition pins exact package versions and content digests; compatibility ranges may help discovery but never remain unresolved at confirmation.

No remote catalogue, provider lookup or automatic download occurs on setup page load. The future installer reads reviewed, bundled manifests from the established resources/package system. Website research records are not executable installer inputs.

## 3. Proposed data contract

This section defines a future validation contract; it does not create importable JSON. Existing [SOURCES.json](SOURCES.json) keeps its separate `research-provenance-1` schema. A later catalogue builder must explicitly convert reviewed research into a package, rather than treating a research status flag as an installation instruction.

### Package envelope

| Field | Required meaning |
|---|---|
| `schema_version`, `package_id`, `version`, `layer` | Contract version; immutable identity; exact version; `neutral`, `industry`, `country` or `optional`. |
| `display_name`, `summary`, `scope`, `excluded_scope` | Plain-language purpose and limits. A label must not promise functionality that the package only describes. |
| `country_code`, `industry_id` | Nullable where the layer does not use them; validated against known IDs. Optional groups declare applicability without masquerading as another layer. |
| `legal_form_scope`, `reporting_framework_scope`, `jurisdiction_scope` | Explicit supported values and exclusions. Unknown applicability never matches every value. |
| `compatibility` | Supported application/contract versions and required module service contracts. A template cannot claim support for an unavailable posting mechanism. |
| `requires`, `conflicts`, `account_groups` | Exact resolved dependencies in the release bundle, mutual exclusions, default/recommended/optional grouping and reasons. No arbitrary scripts or expressions. |
| `accounts`, `bindings`, `display_overrides` | Account definitions, supported workflow bindings and narrowly permitted label changes. No hidden behavioural override. |
| `source_ids`, `authorship`, `license`, `attribution` | Provenance and redistribution basis for the actual package contents, distinct from the licences of sources consulted. |
| `review_records`, `validation_records`, `release_record` | Companion evidence records tied to this content digest and stated scope; missing evidence is visible. These records are outside the payload they review. |
| `content_digest`, `status_decision_id` | Hash of the canonical package payload and current append-only lifecycle decision. Lifecycle decisions are outside the immutable payload to avoid a self-referential digest. |

A package payload contains identity, definitions, dependencies, applicability, authorship, licensing and source references. Its companion review/lifecycle register references the payload digest; the payload does not contain records that themselves quote that digest. This avoids circular hashes. A published bundle manifest pins the payload file and the exact companion-register revision/bytes. New review decisions append to the register; they do not mutate an already released payload.

The bundle manifest also records exact file-byte digests. The payload digest identifies meaning under the named canonicalization version; the byte digest detects packaging changes. A lifecycle decision cannot make different bytes pass either comparison. Historical payloads and their publication manifests remain available when a newer decision retires their use.

### Account definitions and semantic meaning

Each account row requires:

| Field | Contract |
|---|---|
| `semantic_key` | Stable meaning, lowercase dotted ASCII, within the existing 100-character limit. Never reused for a different accounting meaning. |
| `label`, `description`, `reason` | Suggested display text, definition and a short explanation of why the business might need the account. |
| `suggested_code`, `root_type`, `normal_balance` | Code suggestion respecting current account-code limits; root type exactly `asset`, `liability`, `equity`, `income` or `expense`; normal balance `debit`, `credit` or explicitly justified `either`. Normal side is review metadata, not permission to discard a legitimate opposite balance. |
| `runtime_role` | A supported current role or null. The actual supported roles are `cash_bank`, `receivables`, `payables`, `owner_equity`, `income`, `expense`, each with the correct root type. |
| `capability_bindings` | Separate typed links for Inventory, Purchasing, tax or future reviewed modules. Do not put invented values such as `inventory`, `grni` or `tax_input` into the current role field. |
| `currency_policy`, `is_monetary` | An explicit compatible policy; never infer monetary status solely from root type. Do not change account currency properties through catalogue application after postings exist. |
| `reporting_tags`, `group_key`, `contra_of` | Optional reviewed metadata, with targets checked for existence/cycles. Proposed grouping does not create posting-capable parent accounts or jurisdictional statements. |
| `applicability`, `required_for`, `optional_group` | Visible conditions and the reason a binding is required. Unsupported optional functions remain unavailable even if a user creates similarly named accounts manually. |
| `source_ids`, `review_record_ids`, `provenance_note` | Traceable support for this row, including when it is an original proposal rather than a copied chart. |

Codes and labels are not identity. Two accounts called "Bank" are not automatically equivalent, and a source-system account number is not a mandated national code. Prefer preserving an existing code when an explicitly reviewed semantic match exists. Resolve new code collisions in the preview; do not silently suffix or renumber accounts.

One account may meet several *explicitly compatible* descriptive bindings, but `pl_accounts.semantic_key` remains one canonical key per account. Alias metadata can preserve older terminology only after a reviewed exact-meaning mapping; it does not create duplicate canonical keys or a second AR/AP ledger. Never map conflicting controls to one account. Multiple banks or AR/AP controls require concrete account selection and separate reconciliation where applicable, not a global "first matching role" default.

Shared customer/supplier balances stay in the existing party/open-item ledger. Chart installation must not create a GL account for every customer, vendor, invoice, product or project.

### Module binding examples and boundaries

- Preserve the published neutral key `core.cash_bank`; current cash/POS services depend on it. A proposal to split cash and bank requires an explicit compatibility plan, not a label override.
- An Inventory account is an eligible asset account mapped through the existing product service; cost of goods sold uses an eligible expense account. Chart metadata does not create products, stock quantities, batches or warehouses.
- Purchasing received-but-unbilled clearing is an eligible liability account; a variance account follows the current service's accepted income/expense constraints. The AP control remains separate and owned by the shared open-item ledger.
- Manual tax uses eligible input asset/output liability accounts, currently with null runtime roles and currency-neutral configuration. A country account suggestion does not configure tax codes, rates, registration eligibility or inclusive/exclusive price mode. Those remain an explicit, separate core-tax decision using existing services.
- AR/AP remain bundled required accounting services. Hiding their navigation does not remove their accounts, balances or permissions. Optional Inventory/Purchasing activation continues through the existing owner-controlled module service.

### Sources, licensing and reviewer evidence

Country source IDs remain `REG-PK-*`, `REG-IN-*`, `REG-AE-*`, `REG-GB-*`, `REG-US-*`; vendor comparisons use `REG-UPSTREAM-*`. Each cited source records publisher, title, exact URL, publication/revision/effective date when known, access date, evidence kind, retrieved artifact digest when available, scope and reuse terms. A search excerpt, page extraction and retained original PDF are visibly different evidence kinds. A missing date/hash is null with a reason, never invented.

Linking to a source does not grant permission to redistribute its chart, standards or prose. Package authors must distinguish original suggested accounts from copied or adapted material. Release requires a recorded licence/attribution decision for every redistributed component. Unknown redistribution rights block inclusion of that component. Retaining a pointer for research is allowed without treating its contents as package data.

Review records identify the reviewer, review date, relevant jurisdiction/practice competence, exact package digest, scope, outcome, unresolved conditions and evidence reference. A software account with role `accountant` is not proof of professional qualification. An AI research summary is not an accountant review. Store only appropriate public reviewer metadata in a distributed catalogue; keep private evidence under the existing repository/process boundary.

## 4. Six visible statuses and the release gate

The exact user-visible lifecycle is:

| Status | Meaning | Permitted use |
|---|---|---|
| **Research** | Incomplete evidence or proposals; no accounting approval. | Read research notes and gaps. No application to a real company. |
| **Preview-only** | A structurally readable candidate with enough attribution to review. | Inspect a clearly labelled proposal/prototype and synthetic examples. No application to a real company. |
| **Accountant reviewed** | Named qualified review covers the exact candidate and scope. | Continue technical and UX validation. No application to a real company yet. |
| **Validated** | Required technical, accounting-example and setup acceptance evidence passes for the reviewed digest. | Release preparation only; no ordinary-company installation until publication decision. |
| **Released** | Maintainer release decision pins the validated artifact and compatibility. | Eligible for installation when all composition and company-specific checks also pass. |
| **Deprecated** | Retired, superseded or withdrawn, with reason and replacement guidance when available. | Read installed history and export it; no new installation or new composition based on this version. |

Normal progression is Research to Preview-only to Accountant reviewed to Validated to Released. Any version may be deprecated with an append-only reason. Deprecation never deletes installed accounts, disables historical reads or changes existing journals. Reinstatement or changed content requires a new version and the necessary review; it cannot erase an adverse decision. A change in applicability or evidence creates a new recorded review decision and blocks pending confirmations until resolved.

Status changes are controlled release/review actions, not settings that an ordinary installer can toggle. Store decision identity, prior/new state, actor, timestamp, reason and evidence. A package's current decision must be recoverable without overwriting its prior decisions.

**Only Released is potentially installable.** Eligibility is computed, not trusted from `runtime_importable: true` alone: exact digest, compatible app/contracts, valid evidence/licensing, no later deprecation/withdrawal, all dependencies released and applicable, and all required company mappings resolved. Optional groups must pass the same gate. A released neutral package cannot make a Research country package installable by aggregation.

All current country records in [the provenance register](SOURCES.json) are **Research**, unreviewed and non-importable. This document promotes none of them. The already-published six-account foundation remains an explicitly identified existing path; it must not be described as a country-reviewed package or used to waive release gates for new compositions.

## 5. Deterministic composition and explicit errors

The proposed composer is a pure function over validated manifests, exact pinned versions and explicit user choices. It has no database writes, live lookups, guessed classifications or executable package hooks.

1. Validate the contract, package identities, digests, lifecycle decisions and applicability. Refuse unsupported schema versions and unknown required fields/operations.
2. Enforce layer counts. Resolve exact dependency versions; topologically order dependencies with stable package-ID ordering for ties. Reject cycles, missing dependencies, mutually exclusive options and a second country.
3. Compose neutral, industry, country and explicitly selected optional groups in that declared order. Order is for reproducibility; it gives no layer permission to override accounting meaning.
4. Merge the same semantic key only when immutable meaning, type, runtime role, currency policy and binding constraints agree. Combine provenance. If a duplicate definition differs, show both origins and stop. Names that merely look similar never deduplicate automatically.
5. Apply only declared, reviewed display-label overrides. Record the original label, override origin and selected label. User-selected display names are captured separately from catalogue content.
6. Compare against the company's current accounts and explicit bring-your-own mappings. Preserve existing IDs and fixed attributes. Propose new accounts or existing matches; require explicit resolutions for code collisions and ambiguous semantics.
7. Validate required workflow bindings, root types, account activity, currency compatibility and scope. Validate optional requirements only for the explicitly chosen operation set; the chart must not silently switch modules on.
8. Produce the complete resolved account list, mappings, reasons, additions, display-only changes, excluded optional groups and blocking/nonblocking findings. Also produce a machine-readable plan and digest for review.

Canonicalization uses the proposed identifier `coa-canonical-json-1`: UTF-8 JSON without a BOM or insignificant whitespace; object keys ordered lexicographically by UTF-8 bytes; Unicode and slashes unescaped except where JSON requires escaping. Require valid, already NFC-normalized authored strings; do not silently normalize user mappings at confirmation. Reject duplicate JSON object keys. Encode quantities/amounts as exact normalized decimal strings and never convert them through floats.

Set-like arrays sort by their declared identity: accounts by semantic key, optional groups by group ID, source IDs alphabetically, and bindings by binding key then scope. Package order follows the dependency/layer rules above with package-ID tie breaks. Any meaningfully ordered array declares its ordering in the contract and preserves it. The composition digest excludes volatile timestamps, actor IDs and database account IDs; the separately scoped preview hash includes the concrete company/book and existing-account mapping. Thus the same definitions can have the same composition digest without making previews portable between companies. Pin file bytes separately. A future implementation must publish fixtures proving equivalent input order yields an identical composition digest.

Errors identify the field/account, each relevant package and a next action. Examples: "Account code 1200 is already used; choose a different code for the new account", "This country profile does not cover the selected legal form", and "The review changed; open a new preview". Do not resolve errors by silently converting Unknown to No, mapping by amount/name alone or dropping a required control account.

The generated review separates **blocking errors**, **decisions needed** and **informational limitations**. Acknowledging an informational limitation cannot bypass an accounting, permission, provenance or compatibility error.

## 6. Preview, confirmation, retries and isolation

### Saved preview

A preview is immutable and contains its target mode (new company or a specific existing company/book), canonical choices, exact package versions/digests/status-decision IDs, user account mappings, proposed additions, the resolved composition, validation findings and digest. For an existing company it also pins a fingerprint of relevant accounts/revisions, mappings, current installation, module requirements and readiness state. Opening-data previews remain separate evidence objects; the chart preview references them when relevant without folding money into template metadata.

An existing company's preview uses the current company access helper; account/mapping confirmation requires existing owner/accountant write access. New-company setup retains the active authenticated creator check and creates owner membership atomically. Optional module activation remains owner-only. Viewer access to appropriate chart history does not imply permission to change it. Public-demo restrictions apply on every service call as they do today.

### Confirm sequence

1. Require explicit confirmation, current membership/permissions and a valid CSRF token on browser POST. Pass typed values to the service; never trust browser-rendered eligibility.
2. Bind the request key to actor/operation/target scope and the reviewed payload hash. For an existing company, resolve only scoped company/book IDs and take the existing book lock. For a new company, preserve the existing per-actor setup serialization before company/book creation. Avoid a second lock or connection system.
3. Under the transaction, recheck current catalogue decisions, package bytes, relevant account revisions/mappings, required module contracts and readiness. Any changed preview input invalidates the preview. Do not upgrade versions or substitute another account while confirming.
4. Apply the exact approved account additions/bindings, persist installation history and the durable result receipt in the same transaction. Route writes through existing services extended for this purpose; do not bypass their type, audit or authorization rules.
5. Commit once. A failure rolls back all new accounts, binding changes, installation records and readiness changes. Return a stable result identifying the installed version and next setup task.

Successful retries with the same key and payload return the same durable result, even if the catalogue has changed since that successful application; they do not reapply anything. The same key with a different payload is a conflict. A request without a successful receipt must pass current permissions and all current eligibility checks. Retrying a completed receipt still requires access to that company; a receipt must never disclose another company's data.

Installation status is not financial readiness. A fresh zero-balance company may become ready only after the existing explicit zero-balance confirmation and other setup checks. A business bringing past data stays `opening_required` until its opening process completes. A chart preview cannot unlock posting by changing that flag directly.

All table references, request receipts, mappings, preview lookups and history exports use company/book scope. A request must reject an account, preview, source or installation from another company even if the user is a member of both. Two simultaneous confirmations cannot create duplicate accounts or competing current installation records. No provider call, email, webhook or payment belongs inside the transaction.

## 7. Installed history and a later additive migration plan

**Proposed only; no migration number is reserved here.** Extend the existing installation model after contract and UX approval. Do not edit migration 002 or rewrite previously applied migration receipts.

| Planned record | Minimum responsibility |
|---|---|
| Immutable installation version | Company/book, predecessor, reason, mode, canonical choices, exact package bytes/digests, review/release decisions, account-ID mappings, selected labels/codes, resolved metadata, actor and confirmation time. |
| Installation action event | Append-only apply/supersede/revert-metadata/deprecation-notice action, reason, prior/next installation IDs and durable request identity. |
| Current installation reference | A transactionally changed pointer/revision to the current version; it is not the historical source of truth. |
| Saved preview/result receipt | Scoped immutable reviewed payload, expected state, confirmation receipt and outcome. Reuse established preview/idempotency conventions where their contracts fit. |

Every installed snapshot is self-contained enough to explain and export the chart after a source package is removed from a future distribution. Hashes point to preserved content; a URL alone is insufficient. Historical installation and action records receive append-only database guards consistent with existing financial history protections.

The upgrade preserves every existing `pl_template_installations.snapshot` exactly. Create a baseline history record referencing or copying its exact bytes and known IDs/digests; label unrecorded choices/review details **Unknown from legacy installation**, not inferred approval. Keep any recorded sample-pack metadata intact. Preserve account IDs, codes, types, roles, journal counts/amounts and company setup state. A compatibility reader can expose the latest installation while the new history becomes authoritative; two writable installation histories are not permitted.

New sample creation either persists its final installation snapshot after synthetic provisioning succeeds inside the same transaction, or records sample provenance in a separate append-only companion event. It must not mutate an already-confirmed chart snapshot. This changes metadata mechanics only; it does not authorize rebuilding current sample companies.

### Additions, upgrades and reversible metadata

An available package update is a proposal, never an automatic update to installed accounts. Produce a version-to-version diff and fresh preview. After posting, allow reviewed additions and supported metadata/mapping changes; refuse wholesale template replacement, code/type/role rewriting, account deletion and any relabelling that would disguise a different meaning. Even before posting, use an explicit diff; do not recreate the company invisibly.

An undo creates a new installation version/action pointing to the previously accepted compatible metadata. It does not delete the intervening history. It may restore display/group metadata and future defaults only when current account, module and document references allow that change. Account classification and fixed identity are never "undone" by rewriting them. Newly added accounts are retained; an unused one may be explicitly deactivated through the existing audited service. Used accounts and all historical references remain available.

Reverting catalogue metadata does **not** reverse journals, delete open items, erase stock movements, change a frozen document snapshot, or recalculate past reports with a different accounting meaning. Financial corrections continue through existing linked reversal/correction workflows. If a proposed mapping change needs a balance transfer or changes a control account with activity, stop with a separate reviewed migration/correction requirement.

## 8. Six-step setup experience

This specifies content and decisions for the UX proposal, not new routes. Use progressive disclosure: ordinary questions first; exact packages, account types, provenance and audit details remain expandable. Save progress safely and preserve answers after validation errors.

| Step | User-facing task | Required outcome |
|---|---|---|
| **1. About your business** | Name; fresh business or existing books; country/neutral choice; functional currency; legal form; accounting start/fiscal year. | Explain immutable functional currency before confirmation. Country and currency remain independent. Unknown legal form is explicit and blocks only choices that depend on it. |
| **2. What your business does** | Pick one main activity, review a short explanation and choose only the additional activities needed. | One primary industry in a composed selection; show the actual account/module consequences. "I'm not sure" saves progress or offers the deliberate neutral-only path. Do not auto-select a complicated vertical. |
| **3. Regional needs** | Show eligible country package and status, supported legal/framework/jurisdiction scope and missing coverage. | Offer no country package; distinguish Unknown from Not applicable and an explicit known selection. No automatic tax registration/rates. Research and unavailable packages are explanatory, not selectable install options. |
| **4. Review your accounts** | Grouped recommended accounts, why each is needed, additions/counts and optional-account choices; expand accountant details. | Preserve required bindings. Offer **Use my existing chart** with explicit mapping, type/code conflicts and a saveable unresolved state. Show exact package/version/status and a clear preview summary. |
| **5. Start fresh or bring balances** | Confirm zero opening balances or prepare a cutover with source chart, balances and unpaid/stock details. | Fresh requires explicit zero-balance confirmation. Existing books use the current opening preview and reconciliation process. No guessed matches, silent balancing entries or posting from a file preview. |
| **6. Confirm and start** | Show company, accounting dates, currency, choices, accounts to add, mappings, readiness and the next task. | Confirm the exact reviewed plan atomically. If opening work remains, say so and route there; do not present the company as ready to post. Retain a printable/exportable setup receipt. |

The word **Unknown** is a first-class answer. For example, an owner unsure of a tax registration can continue with a neutral accounting chart, defer the country-specific group and receive a visible review task. The application must not interpret that uncertainty as unregistered, exempt, zero-rated or approved. If unknown data affects a required account/control, keep confirmation blocked with a concrete explanation and saved progress.

### Bring your own chart

Retain original source codes/labels and upload provenance; preview every new account and every proposed match. Require explicit mapping of the current six required core meanings and any selected optional service needs. Duplicate codes, mismatched types/roles, ambiguous controls, incompatible currencies and unsupported account attributes block confirmation. Never alter the source chart merely to force a template match. Missing required accounts can be added only as an explicit reviewed change in a new preview.

An existing chart can remain broader than the selected profile. Unmapped extra accounts are retained and reported; only required unresolved meanings block the relevant setup step. A many-to-one mapping is not a general escape hatch for conflicting balances or controls. Unsupported legacy complexity needs a separately reviewed conversion.

### Keep examples separate from real books

Place **Try a sample business** outside the real-company setup sequence, or as a clear exit that creates a new isolated sample. State that data is fictional before creation and keep sample identity visible afterward. Never merge sample transactions, contacts, tax examples or opening amounts into a real company; do not convert a sample into a real business by removing its label.

The published five demo choices remain one focused Accounting starter playground and four existing historical examples. The proposed eleven-company program is separate research/design work, with its own versions, expected balances and acceptance evidence. Chart availability and sample availability are separate statuses. A detailed fictional sample does not qualify its regional template for release.

## 9. Opening-data compatibility

Chart installation does not post journals. Confirming an existing business's cutover posts the reviewed balanced opening journal once through the central posting service. Subsequent AR/AP conversion explicitly maps parties and reconciles unpaid documents to that existing control basis. Stock conversion explicitly maps product quantities/values to existing inventory opening lines. Preserve immutable source allocations and reject duplicates, ambiguity or amount/quantity differences.

The catalogue must not implement the older proposal to post opening unpaid documents again through migration clearing. A shared aggregate opening journal line may support multiple immutable operational allocations, but their total must equal its existing basis. Source IDs and dates survive conversion. No balancing plug or newly selected template can explain away a reconciliation difference.

Chart-to-template mappings may be resolved before cutover. Once opening or ordinary activity uses a control, remapping it through setup is not allowed as a shortcut; route the user to a separately reviewed accounting migration/correction. Preserve current guards against independently reversing converted openings. Closed-period checks, account reconciliation and readiness continue to be enforced by their existing services.

## 10. Acceptance evidence required before implementation and release

### Design acceptance

- Owner accepts composition cardinality, neutral-only fallback, six-step flow, bring-your-own mapping and sample separation.
- Accounting reviewers accept account meanings, supported entity/framework scopes and opening compatibility; unresolved country evidence remains visible.
- Engineering review accepts compatibility with current semantic keys, runtime roles, module bindings, scoped permissions and installation-history migration.
- UX review covers an uncertain owner, a simple fresh business, an accountant bringing an existing chart and a multi-company operator. Prototype observation is recorded separately from financial correctness.

These are open gates. Document acceptance permits implementation planning; it does not mark any package Accountant reviewed, Validated or Released.

### Future technical and accounting checks

| Scenario | Required result |
|---|---|
| Same manifests/options in different input order | Same canonical composition/digest and readable account order. |
| Missing dependency, cycle, duplicate JSON key, second country, incompatible legal form | Specific blocking error; no writes. |
| Same semantic key with different type/meaning or same code for different new meanings | Conflict shown with both origins; no silent override/deduplication. |
| Research/Preview-only/reviewed/Validated/Deprecated dependency inside an otherwise released selection | Ordinary-company installation rejected. |
| Released version deprecated or evidence changed after preview | Fresh confirmation rejected; previously successful retry returns its existing receipt without reapplying. |
| Account, role mapping, module requirement or current installation changes after preview | Stale preview rejected; original saved preview remains readable. |
| Same request repeated or submitted concurrently | One result, one set of additions, one installation action; altered payload conflicts. |
| Injected failure after account creation but before receipt | Entire installation transaction rolls back. |
| Another company's account/preview/receipt supplied; viewer applies; owner removed mid-flow | Server rejection without information leakage or partial writes. |
| Existing 0.4 installation upgraded to history model | Original snapshot bytes/account IDs/postings/balances/readiness retained; unknown evidence labelled honestly. |
| Metadata update/revert after postings, disable optional module, retire catalogue package | History remains readable; balances and frozen document/stock/open-item data unchanged. |
| Bring-your-own chart with extra accounts, duplicate controls or unresolved mapping | Extra accounts preserved; ambiguity blocks relevant confirmation. |
| Opening conversion after chart selection | One opening journal; mapped operational totals equal existing controls; repeat/over-allocation rejected. |
| Country selection, tax uncertainty or account-label override | No automatic rate/code/registration/module activation or claim of filing compliance. |
| Sample creation attempted in a real or populated company | Rejected; new sample is isolated and reconciles to its pinned fixture. |
| Export after original package removed from the current distribution | Full installed definitions, mapping history, versions, evidence references and digests remain explainable. |

All checks in this table are requirements for future implementation, **not tests reported as run by this document task**. Reuse the existing test harness and fixed-precision financial services. No new runtime, migrations, provider integrations or production changes are authorized by this design artifact alone.

## 11. Review handoff and references

The next review should settle any remaining ambiguity about required core mappings for complex imported charts, precise optional-group boundaries and metadata changes permitted after posting. It should then pair this contract with the current-route audit and six-step prototype. Do not start catalogue/application migrations while those design decisions remain unaccepted.

Read alongside:

- [Country provenance checkpoint](COUNTRY-PROVENANCE.md) and [source register](SOURCES.json): incomplete country research, exact evidence levels and gaps.
- [COA research overview](../README.md), [historical template model](../TEMPLATE_MODEL.md), [account candidates](../ACCOUNT_CANDIDATES.md) and [discovery gaps](../DISCOVERY_GAPS.md): original research and proposals, with conflicts identified in section 1.
- [Architecture](../../ARCHITECTURE.md), [development guidance](../../DEVELOPMENT.md) and [accounting starter implementation](../../repository/sprint-06/ACCOUNTING-STARTER.md): current shared service and accounting boundaries.
- [Country-neutral product direction](../../strategy/PRODUCT-DIRECTION-CLARIFICATION-2026-09-15.md): Pakistan is a reference market, not a structural restriction on the product.

No external document was edited for this contract. Country-law conclusions remain with the separate provenance/review work; this document introduces no new claim about current tax rules or mandatory charts.
