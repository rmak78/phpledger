# PHP Ledger 0.5.0 sample-data lane audit

**Date:** 2026-09-16  
**Scope:** inspection of the business matrix, authored sample material, successor demo packs, validation code, and the local operational replay adapter.  
**Status:** local working report. The `docs/` tree is intentionally local and ignored by the public repository.

## Executive finding

The current successor packs are structurally consistent and now have a bounded operational replay adapter, but they are not yet eleven fully validated operational business simulations. `tools/build-demo-packs.py` still carries a shared reconciled ledger schedule for the closed successor history; the per-company operational contracts are preserved alongside it and the supported portion is now replayed through the existing AR/AP, Purchasing, Inventory and general-journal services during isolated provisioning. Each pack has 71 posted events plus 3 drafts in the successor ledger, while the four newly generated candidates carry 8–10 additional supported-event examples in their explicit contracts. The successor checkpoints cover the 2024–2026 calendar; database replay, report comparison and hosted-capacity evidence remain open because Docker is unavailable locally.

The deeper source material is more useful: it contains distinct contacts, items, locations, opening evidence, operational event contracts, and scenario checks for seven businesses. The successor builder retains that material as `source_material.research_evidence`; the importer now creates deterministic operational master data and posts supported events, while location transfers, advanced revenue timing and other specialist controls remain explicitly staged. The other four successors carry generated candidate contracts with explicit provenance. Independent database evidence is still required before these fixtures can be treated as release-validated business history.

## 1. Matrix inventory and exact coverage

The only workbook found under the repository is:

`docs/PHPLedger-SMB-Vertical-Research-Matrix.xlsx`

The workbook has 15 sheets:

| Sheet | Used range | Exact data rows | Coverage |
|---|---:|---:|---|
| Methodology | A1:B14 | 3–14 | Research date, evidence rule, compliance caveat, geography, navigation. |
| Master Matrix | A1:S101 | 2–101 | The authoritative 100-segment table. |
| Trade-Distribution | A1:S11 | 2–11 | IDs 1–10, ten segments. |
| Retail-Specialty | A1:S15 | 2–15 | IDs 11–24, fourteen segments. |
| Automotive | A1:S8 | 2–8 | IDs 25–31, seven segments. |
| Food-Hospitality | A1:S11 | 2–11 | IDs 32–41, ten segments. |
| Healthcare | A1:S8 | 2–8 | IDs 42–48, seven segments. |
| Beauty-Fitness-Pet | A1:S10 | 2–10 | IDs 49–57, nine segments. |
| Education | A1:S7 | 2–7 | IDs 58–63, six segments. |
| Professional | A1:S9 | 2–9 | IDs 64–71, eight segments. |
| Construction-Property | A1:S12 | 2–12 | IDs 72–82, eleven segments. |
| Manufacturing | A1:S13 | 2–13 | IDs 83–94, twelve segments. |
| Agriculture | A1:S7 | 2–7 | IDs 95–100, six segments. |
| Source Index | A1:G100 | 2–100 | 99 indexed source rows. |
| Summary | A1:E22 | 3–19, 22 | Counts and evidence-classification summary. |

The 100 Master Matrix segments, grouped by family, are:

- **Trade & Distribution, IDs 1–10:** wholesale/stockist; FMCG distributor; pharmaceutical distributor; lubricant/automotive-fluid distributor; beverage/food distributor; building-material distributor; importer/general trading company; exporter/export trading company; clearing and forwarding/customs agent; e-commerce/marketplace seller.
- **Retail & Specialty, IDs 11–24:** grocery/minimart/supermarket; apparel/clothing retailer; footwear/shoe retailer; cosmetics/perfume/beauty retail; electronics/appliance retailer; mobile-phone/accessories shop; stationery/bookstore; hardware/building-supply retail; pharmacy/medical store; optical/eyewear store; jewellery/gold shop; auto spare-parts retailer; tyre/battery/lubricant shop; agricultural-input store.
- **Automotive & Mobility, IDs 25–31:** automobile dealership/3S center; auto repair workshop/service center; motorcycle workshop/service center; car wash; auto detailing/PPF/ceramic coating; petrol/fuel station; car rental/small fleet rental.
- **Food, Hospitality & Events, IDs 32–41:** full-service restaurant; QSR/fast food; cafe/coffee shop; bakery/bakery cafe; cloud kitchen/delivery-only restaurant; food truck/mobile food business; catering company; banquet/marriage hall/event venue; event/wedding planning agency; hotel/guest house/serviced apartment.
- **Healthcare & Clinical, IDs 42–48:** general/specialist clinic; dental clinic; diagnostic/pathology laboratory; small hospital/day-care hospital; physiotherapy/rehabilitation clinic; aesthetic/medical spa clinic; veterinary clinic.
- **Beauty, Fitness, Personal & Pet, IDs 49–57:** hair salon/barbershop; spa/wellness center; gym/fitness club; yoga/Pilates/class studio; sports court/club; pet grooming salon; pet boarding/daycare; laundry/dry cleaning; tailoring/boutique/custom stitching.
- **Education & Childcare, IDs 58–63:** private school; tuition/coaching center; preschool/daycare; vocational/training institute; eLearning/online academy; driving school.
- **Professional & Creative Services, IDs 64–71:** accounting/bookkeeping firm; law firm; IT/software/consulting firm; marketing/advertising/media agency; photography/videography studio; printing press/commercial printer; travel agency/tour operator; insurance broker/agency.
- **Construction, Property & Field Services, IDs 72–82:** general/civil contractor; electrical contractor; HVAC contractor; plumbing contractor; cleaning/janitorial company; pest-control company; landscaping/lawn-care company; solar EPC/installer; real-estate brokerage; property management; property developer/housing society.
- **Manufacturing & Process, IDs 83–94:** packaging/corrugated-box manufacturer; plastic/injection-molding manufacturer; leather tannery/leather manufacturer; engineering/metal fabrication unit; garment/textile manufacturing; pharmaceutical manufacturing; cosmetics/chemical blending manufacturer; repacking/bulk-break operation; rice mill/rice exporter; flour mill; custom furniture/carpentry manufacturer; cement/concrete-block/building-product manufacturer.
- **Agriculture & Livestock, IDs 95–100:** aarthi/mandi commission agent; grain trader/beopari; dairy farm/milk collection center; poultry farm; farm/orchard operator; cold storage operator.

### Mapping the accepted eleven companies

| Accepted sample | Current pack | Matrix anchor | Fit and caution |
|---|---|---|---|
| Cedar Studio | `service-agency` | ID 67, marketing/advertising/media agency; also IDs 64–71 professional services | Generated candidate now carries clients, contractor, retainer/sprint, bills, split settlement and correction evidence; projects, milestones and retainer/deposit lifecycle remain outside runtime. |
| Sunrise Garden Services | `seasonal-business` | ID 78, landscaping/lawn-care company | Generated candidate now carries peak/low-season invoices, split receipt, supplier cost and operating costs; recurring route, site, crew, materials, equipment and service completion remain outside runtime. |
| Willow Corner Shop | `retail-shop` | ID 17, stationery/bookstore, based on the notebook/pen items | Better labelled as a stationery shop than generic retail. No till sessions, barcode/variant records, purchase receipts, or daily sales mix. |
| Harbour Trade | `trader` | ID 1, wholesale/stockist | Has a generic stock sale and settlement pattern. No supplier/customer terms, price lists, credit limits, delivery, or multi-unit trade behaviour. |
| Harbor Supply Company | `distributor` | ID 2, FMCG distributor | The source material describes a van and route stops, but the successor does not operationally import route, stop, dispatch, or secondary-sale events. |
| Cedar Table | `restaurant` | ID 32 full-service restaurant or ID 34 cafe/coffee shop | Current fixture labels are food and beverage, but tables/KOT, covers, modifiers, recipes, waste, supplier delivery, and service charges are not implemented. |
| Riverside Community Club | `membership-club` | Nearest anchors ID 53 sports court/club and ID 58–63 education/membership-like services | The matrix has no exact community-club row. Keep the company as a clearly labelled membership-income teaching case until membership administration, renewals, and nonprofit/charity treatment are separately researched. |
| Meadow Training Pharmacy | `pharmacy` | ID 19, pharmacy/medical store | The current source correctly limits this to non-medicinal training stock. The name must never imply medicine dispensing, patient records, DRAP compliance, expiry control, or tax compliance. |
| Lantern Finch Jewelry Studio | `jewelry-studio` | ID 21, jewellery/gold shop | Generated candidate now carries material receipt, sale, return, settlement, repair service, workshop cost and count evidence; it must not imply commodity, hallmarking, consignment, appraisal, or purity accounting. |
| Maple Bench Works | `light-manufacturing` | ID 93, custom furniture/carpentry manufacturer | Generated candidate now carries material receipt, sale/return, settlement, supplier payment, workshop cost and count evidence; it does not model BOM, work order, WIP, material issue, waste, or overhead absorption. |
| Wheel & Spoke Workshop | `service-workshop` | ID 26, auto repair workshop/service center | Customer-owned bicycle separation is a good accounting boundary. Job cards, custody, labour parts, vehicle history, warranty, and technician workflow are not implemented. |

## 2. Current material and validation evidence

### Authored source packs

`resources/sample-data` contains seven authored candidate packs: `distributor`, `membership-club`, `pharmacy`, `restaurant`, `retail-shop`, `service-workshop`, and `trader`.

Their common shape is materially richer than the successor packs. Across the seven packs there are 77 events, 42 documents, and 16 items. Each pack has four contacts, two opening documents, one industry scenario, and a report snapshot as of 2026-09-30. Stock packs carry explicit stock movements and quantities; the membership pack carries deferred/prepayment recognition instead.

The seven event contracts contain, by pack:

| Pack | Event kinds present | Stock movement rows | Current documents | Scenario boundary |
|---|---|---:|---:|---|
| distributor | purchase, stock transfer, cash sale, credit sale, receipt, supplier payment, expense, sales return, cash transfer, reversal | 15 | bill, cash sale, invoice, credit note | Route/van notes are references, not route software. |
| membership-club | prepayment, credit sale, cash sale, receipt, supplier payment, expense bill, expense, cash transfer, revenue recognition | 0 | prepayment, invoice, cash sale, bill | Deferred income is shown without claiming club administration or charity compliance. |
| pharmacy | purchase, cash sale, credit sale, receipt, supplier payment, expense, sales return, cash transfer, reversal | 7 | bill, cash sale, invoice, credit note | Training items are explicitly non-medicinal. |
| restaurant | purchase, cash sale, credit sale, receipt, supplier payment, expense, sales return, cash transfer, reversal | 7 | bill, cash sale, invoice, credit note | Table/KOT is a source scenario, not an implemented restaurant module. |
| retail-shop | purchase, cash sale, credit sale, receipt, supplier payment, expense, sales return, cash transfer, reversal | 7 | bill, cash sale, invoice, credit note | Register close is a scenario; barcode/register sessions are future. |
| service-workshop | purchase, cash sale, credit sale, receipt, supplier payment, expense, sales return, cash transfer, reversal | 7 | bill, cash sale, invoice, credit note | Customer property is excluded from owned inventory and assets. |
| trader | purchase, cash sale, credit sale, receipt, supplier payment, expense, sales return, cash transfer, reversal | 7 | bill, cash sale, invoice, credit note | One-location trade example; pricing-list and order workflows are future. |

### Successor demo packs

All eleven current successor JSONs advertise `start_date=2024-01-01`, `history_end=2025-12-31`, and `practice_end=2026-12-31`. Each advertises 74 source records, 72 journal postings, three drafts, 25 monthly support rows, and 36 checkpoints. The builder check passes for all eleven.

Those headline counts conceal the key gaps:

- The current 25 monthly support rows are January 2024 through December 2025 plus January 2026. There are no February–December 2026 posted activities in the successor ledger; the generated candidate contracts are explicitly dated within the open 2026 practice year but have not yet been replayed through the importer.
- The 36 checkpoints are repeated monthly report snapshots. They make the open year visible but do not create monthly source activity.
- Each pack has 46 general-journal events and 25 generic bank receipt events. The same event keys, dates, descriptions, and account structure are reused across companies, aside from the business prefix and a small set of revenue/stock constants.
 - Successor packs have no `documents`, `contacts`, `items`, `locations`, or `stock_movements` fields at the top level. The richer seven-pack authored contract and four-pack generated candidate contract exist inside `source_material.research_evidence`, and none of those operational contracts is currently imported into the operational company.
 - Four packs have generated rather than authored evidence: `service-agency`, `seasonal-business`, `jewelry-studio`, and `light-manufacturing`.
- The current runtime notice is honest about unsupported operations, but the pack names and chooser still need a visually prominent “bookkeeping teaching sample” treatment so a visitor does not infer a complete vertical product.

### Validation results

Read-only checks run in this lane:

- `C:\xampp\php\php.exe tools/validate-sample-data.php --self-test` — passed: 7 packs, 77 events, 42 documents, 16 items; 8 deliberate invalid mutations rejected.
- `python tools/build-demo-packs.py --check` — passed for all 11 packs; each reports 74 sources and 36 month checkpoints.
- `docker compose --profile test run --rm test php tests/run.php --suite=demo-packs` — attempted, but the local Docker API returned HTTP 500 on the Docker Desktop Linux engine ping. This is an environment failure, not evidence of a fixture failure.

The PHP suites validate the seven authored packs and the successor importer’s current generic ledger path. The generated-candidate tests validate identity, date, balance, amount/control and stock-movement invariants. These checks do not establish that every vertical event contract is operationally imported or that monthly/annual statements are independently generated from source documents.

## 3. Bounded realistic synthetic-data specification

The following specification is deliberately limited to workflows the current accounting services can represent. Specialist operational records should be added only after a separately accepted runtime contract.

### Common pack contract

Each versioned pack should contain:

1. A stable ID, semantic version, SHA-256 digest, synthetic marker, provenance status, and a visible capability boundary.
2. A 36-month calendar from 2024-01 through 2026-12. 2024 and 2025 close only after independent reconciliation. 2026 remains open and includes real posted practice history plus editable drafts.
3. Durable event identities: `source_id`, `source_reference`, `idempotency_key`, event date, document identity, related source identity where relevant, and expected journal effect.
4. A deterministic master-data set sized for the demo allowance: 5–12 contacts, 4–20 items for stock businesses, 1–3 locations, and 10–20 semantic accounts. Use exact four-decimal strings for money and integer quantities; generate with `Decimal`, never floating point.
5. A bounded transaction set of approximately 60–100 source records. Per year, target 3–8 customer documents, 3–8 supplier documents, 2–6 receipts/payments, 1–3 credits/returns, and 1–2 corrections. Spread activity across months according to the business pattern instead of repeating one amount every month.
6. Independent expectations for each month and year: trial balance, balance sheet, profit and loss, cash/bank movement, document-level AR/AP ageing, and any applicable stock quantity and value. Keep the expectation generator independent of PHP posting code.

### Industry-specific scenario overlays

These are bounded bookkeeping scenarios, not promises of complete vertical modules:

- **Cedar Studio:** recurring retainers, one milestone invoice, one late customer receipt, contractor/software bills, a credit/correction, and month-end work-in-progress note kept non-posting unless the runtime supports it.
- **Sunrise Garden Services:** low-season and peak-season service invoices, recurring maintenance agreement, plant/material purchases, one equipment repair, customer receipt split across dates, and a weather/route note kept descriptive only.
- **Willow Corner Shop:** several stationery SKUs, a supplier delivery in two receipts, cash/till sale summaries, one credit customer invoice, a return linked to the original sale, a counted till-to-bank transfer, and a shrinkage/count adjustment only if the supported inventory service can post it.
- **Harbour Trade:** purchase order intent as non-posting source data if PO support is unavailable, receipt of goods, credit sale on terms, partial customer settlement, supplier bill/payment, return, and unit/pack conversion recorded explicitly.
- **Harbor Supply Company:** two locations or a warehouse plus van only if location services support it; otherwise keep route/van events as non-posting notes. Include wholesale cases, retailer credit, a partial delivery/receipt, a return, and a route cash-to-bank transfer with no duplicate revenue.
- **Cedar Table:** food and beverage categories, daily cash summaries, supplier bills, one credit event, wastage as an explicitly non-posting teaching note, and a table/KOT reference. Do not claim recipes, kitchen production, food safety, or statutory tax treatment.
- **Riverside Community Club:** annual and monthly membership services, deferred-income recognition, event supplier bill, one refund/credit only if supported, and member-group contacts. Do not infer nonprofit, charity, dues, or renewal compliance.
- **Meadow Training Pharmacy:** rename or subtitle as non-medicinal training inventory, with ordinary lot IDs only if the existing stock service supports them. Use packaging/storage supplies, purchase, sale, return, and supplier settlement. No medicine, prescription, patient, controlled-substance, DRAP, FBR, or expiry-compliance claims.
- **Lantern Finch Jewelry Studio:** material and finished-piece bookkeeping with illustrative weight/purity fields kept descriptive, purchase, customer deposit only if supported, sale, return/correction, and workshop expense. Do not post commodity revaluation, hallmarking, consignment, appraisal, or purity rules.
- **Maple Bench Works:** timber/hardware material purchases, a simple finished-product conversion represented as a clearly labelled manual teaching schedule, sale, return, and job expense. Do not claim BOM, production planning, WIP, overhead absorption, recipes, or factory compliance until implemented and reviewed.
- **Wheel & Spoke Workshop:** parts purchase, labour/service invoice, partial customer receipt, returned part, and explicit customer-owned bicycle/property record with zero stock and zero journal effect. Do not treat customer property as a business asset or claim job-card, custody, warranty, technician, or vehicle-history software.

### Source discipline

Use the matrix’s official vendor/product pages for workflow discovery, not as compliance certification. The most relevant primary sources for the selected overlays include:

- [HysabOne FMCG distribution](https://hysabone.com/industries/fmcg/) for route planning, trade schemes, expiry-oriented handling, sales-force activity, and secondary sales. The page is vendor evidence; its GST/FBR claims are not adopted as fixture facts.
- [HysabOne Pharmacy](https://hysabone.com/industries/pharmacy/) for the existence of batch/expiry and supplier workflows in a Pakistan-market product. The sample must remain non-medicinal until legal and product review exists.
- [HysabOne Automobile and workshop](https://hysabone.com/industries/automobile/) for job cards, spare-parts tracking, customer vehicle history, warranties, and multi-location claims. The current sample uses only the accounting boundary that customer-owned property is not owned stock.
- [ERPNext Repack Entry](https://docs.frappe.io/erpnext/repack-entry) for a documented input/output repack transaction and cost-distribution concept. It is a workflow reference, not authorization to implement manufacturing accounting in the sample.
- The workbook’s [Source Index](../PHPLedger-SMB-Vertical-Research-Matrix.xlsx) and its 99 linked official product/category pages for the remaining segment-specific research. The workbook’s Methodology sheet explicitly records that vendor compliance claims are not independently certified.

No source should be used to invent Pakistani tax rates, FBR/DRAP readiness, payroll withholding, medical practice, food safety, commodity accounting, or production compliance. If a source documents only a category, the fixture should record a category-level scenario and mark detailed operations as unavailable.

## 4. P0/P1/P2 backlog

### P0 — release blockers

1. Replace the one-size-fits-all event generator with a versioned per-company source contract. Preserve the existing four historical packs and seven authored packs as provenance, but make the successor source explicit.
2. Extend every selected successor to actual monthly source activity across the agreed 2024/2025 history and meaningful 2026 open-year practice. Do not fill empty months with copied checkpoint balances.
3. Import supported invoices, bills, receipts, supplier payments, credits/returns, partial allocations, and corrections through the existing central posting services. Do not use manual GL support schedules as a substitute where an operational service exists.
4. Add independent document-level AR/AP expectations and applicable inventory quantities/values to every successor. Add GRNI/received-but-unbilled expectations for packs that exercise partial receipts.
5. Carry the seven authored source contracts into the successor pack without silently dropping fields. The four additional successors now carry generated candidate provenance; specialist research and runtime replay remain explicit release blockers.
6. Replay all eleven through the real importer in a disposable test database, with atomic rollback, retry/idempotency, altered-manifest rejection, real-company rejection, and cross-company isolation tests. The current builder check is not enough.

### P1 — high-value realism and teaching quality

1. Add 5–12 synthetic contacts and 4–20 meaningful products/locations per applicable pack, with stable IDs and non-deliverable addresses/emails.
2. Make each pack’s scenario visible as a short guided walkthrough tied to real source references, expected documents, monthly checkpoints, ageing, and stock movements.
3. Add at least one partial settlement, one linked credit/return, one supplier-side settlement, one cross-year open item, and one correction where the business scenario warrants it.
4. For stock packs, implement or explicitly stage receipts, issues, returns, counts, and moving weighted-average valuation through the existing inventory service. Do not post stock twice from a manual schedule and an operational event.
5. Make the Pharmacy sample’s non-medicinal boundary unavoidable in the chooser, pack header, and guide. Apply the same capability boundary treatment to restaurant, manufacturing, jewelry, and workshop samples.
6. Add fixture-level checks that compare monthly/yearly report totals, document ageing, stock quantities, stock values, and GRNI to independent expected data. Keep posted opening AR/AP reconciled to the opening journal exactly once.

### P2 — breadth and refinement

1. Add alternative bounded overlays for matrix segments not represented by the eleven chosen companies, prioritising Pakistan services, retail, distribution, and workshop workflows after review.
2. Add country-neutral display dimensions such as cash/bank, credit, inventory, service, and location without turning country research into tax or statutory logic.
3. Add optional non-posting reference records for route stops, tables/KOT, job cards, customer property, lot notes, and material conversions where the runtime does not yet support those operations.
4. Measure pack size, provisioning latency, concurrent loading, retry behavior, and practice-record allowance before hosted publication.
5. Review scenario names and chooser copy with the product owner and one operator from each represented business type. Record accessibility and observed usability separately from automated test results.

## Files and boundaries

- This report was created during the initial read-only audit and is now updated to reflect the subsequent local implementation work. The current candidate changed runtime PHP, fixtures, tests, release documentation and ignored local evidence; no migration or schema change was added.
- The unrelated deletion of `Claude outputs/ERPNEXT-REVIEW.md` and other pre-existing working-tree changes were preserved.
- The existing deletion of `Claude outputs/ERPNEXT-REVIEW.md` and all pre-existing working-tree changes were preserved.
- No secrets or real customer data were read or exposed.
- External activity was limited to read-only retrieval of cited public documentation pages. No provider calls, messages, payments, webhooks, deployments, or production changes were made.
