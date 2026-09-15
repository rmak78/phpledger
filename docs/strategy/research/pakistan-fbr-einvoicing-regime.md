# Research: FBR digital invoicing, POS integration and tax obligations

Research date: 15 September 2026. Supporting evidence for [Pakistan market](../PAKISTAN-MARKET.md) §3. Primary sources (FBR/PRAL PDFs, KPMG/EY alerts, major press) are used where available; vendor blogs are cited only for procedural colour and flagged. Anything not confirmed from a primary or Big-4/major-press source is marked UNVERIFIED. This is research, not legal advice, and not a PHP Ledger capability claim.

## 1. Digital invoicing mandate for sales-tax registered persons

**Legal basis.** Chapter XIV-BB of the Sales Tax Rules 2006 was replaced by SRO 69(I)/2025 (29 Jan 2025), creating a single "licensing, issuance of electronic invoices and integration" chapter, Rules 150Q–150XM (KPMG brief). Rule 150Q lets the Board notify classes of registered persons who must integrate; Rules 150R/150S require every taxable supply to be transmitted in real time to FBR's computerised system and to receive a unique FBR invoice number and QR code.

**Notification timeline (all sales-tax registered persons):**

| Instrument | Date | Effect |
|---|---|---|
| SRO 709(I)/2025 | 22 Apr 2025 | Extended mandate from FMCG to all corporate (1 Jun 2025) and non-corporate (1 Jul 2025) registered persons (EY, KPMG US) |
| Extension | ~30 Jun / 18 Jul 2025 | Pushed to 1 Jul / 1 Aug 2025 (VATupdate) |
| SRO 1413(I)/2025 | 1 Aug 2025 | Turnover-tiered phases: >Rs1bn/public companies/importers 1 Sep 2025; Rs100m–1bn and individuals/AOPs >Rs100m 1 Oct; companies <Rs100m 1 Nov; all others 1 Dec 2025 (KPMG US, Tribune) |
| **SRO 1852(I)/2025** | late Sep 2025 | **Current operative schedule.** Registration → sandbox testing → live issuance: >Rs1bn/public/importers register 15 Oct, test 25 Oct, live 1 Nov 2025; Rs100m–1bn live 15 Nov 2025; companies ≤Rs100m live 1 Dec 2025; individuals/AOPs >Rs100m live 1 Nov 2025; **all other registered persons integrate by 10 Dec, live by 31 Dec 2025** (TaxationPk; Thomson Reuters/Pagero; Business Recorder 24 Jun 2026 confirms importer dates) |
| STGO 01/2026 | ~Apr 2026 | Restates that all sales-tax registered persons must issue digital invoices via FBR-licensed integrators; may use more than one integrator; invoices may be cancelled/edited only inside FBR's system within **72 hours** (thereafter Commissioner approval); PRAL manual adds a cap of 10% of monthly sales on edits/cancellations (VATupdate, Mettis, PRAL manual v1.6) |
| **Finance Act 2026** | effective 1 Jul 2026 | Commissioner may **suspend/blacklist** a registered person for non-integration alone (s.21); input-tax restrictions may be tied to e-invoicing compliance; e-adjustment mechanism for debit/credit notes; new definition of "licensed integrator" (s.2(15A)); "integrated enterprise" now means integration "with the Board's computerised system through a licensed integrator" (KPMG Finance Act 2026 brief; KPMG US July 2026; VATupdate 27 Aug 2026) |

**Who is legally required today:** every person registered for federal sales tax (goods), regardless of size or legal form — the last phase (31 Dec 2025) has passed. No turnover floor exempts a registered person. Unregistered persons (most small shops, service-only businesses) are not covered by the Sales Tax Act mandate. Enforcement in 2026 has focused on importers (from 1 Jul 2026: penalties, suspension, loss of green-channel; Business Recorder 24 Jun 2026). Blog claims of a "July 2026 final deadline" and "45,000 integrated / Rs 2.3bn penalties" are vendor marketing — UNVERIFIED.

**Penalties:** s.33 Sales Tax Act — Rs 500k first default, then Rs 1m, Rs 2m, Rs 3m at 15-day intervals; sealing under s.14AB; invoices not issued through the system are invalid for input-tax purposes (FBR FAQ; Finance Act 2026 adds suspension).

**Implication:** any sales-tax-registered SME cannot legally use an invoicing module that does not post each invoice to FBR in real time and print the FBR number + QR — table stakes, not a feature.

## 2. Tier-1 retailer POS regime

- **Definition (s.2(43A) Sales Tax Act, unchanged in 2026):** a retailer meeting any of: part of a national/international chain; located in an air-conditioned mall/plaza (kiosks excluded); cumulative electricity bill >Rs 1.2m in preceding 12 months; wholesaler-cum-retailer of bulk consumer goods; has acquired card-payment POS equipment; 236G/236H withholding above FBR-notified threshold; any other class FBR notifies.
- **Obligation:** s.3(9A) — integrate every outlet with FBR's system for real-time reporting; each invoice carries an FBR number and QR. Finance Act 2026 collapsed the separate "approved fiscal electronic device and software" vocabulary into "Board's computerised system through a licensed integrator" — the Tier-1 POS regime now sits inside the licensed-integrator/digital-invoicing framework (KPMG FA2026 brief).
- **Consequences of non-integration:** 60% of input tax disallowed for the period (s.8B(6)); s.33 penalties; sealing/utility disconnection.
- **Incentives:** integrated Tier-1 retailers get 0.25% minimum tax under s.113; 10% tax credit on integration hardware/software spend (s.64D as amended by FA2026).
- Draft SRO 288(I)/2026 would extend a POS-style income-tax integration to services/retail even without sales-tax registration (see §4).

**Implication:** a retail POS front-end for Tier-1 shops needs offline-capable real-time posting, FBR number/QR per receipt and outlet-level registration — larger scope than B2B invoicing; a later phase for an SMB product.

## 3. Licensed integrators: who, how, cost, and whether software can go direct

**Current FBR list (fetched Sep 2026): 8 entities** — Haball (Pvt) Ltd, WEBDNAWORKS (Pvt) Ltd, EY Ford Rhodes, PRAL, OpenPort Pakistan, TMR Consulting, NatureTech, Dynamic Resources. (Original four notified Apr 2025 — Dawn, Business Recorder.)

**Becoming one (Rules 150XH–150XO, FBR Chapter XIV PDF):** application to the Board; company incorporated in Pakistan; **paid-up capital ≥ Rs 10 million**; registration certificate from **PASHA or ICAP**; three years' audited accounts; list of projects in last three years; documented ERP-integration capacity; NTN, directors' CNICs, never-blacklisted undertaking. Licence **valid 5 years**, non-transferable, no sub-contracting; post-deployment maintenance and troubleshooting obligations; s.33 penalties and revocation for breach. **Application fee / bank guarantee: not found — UNVERIFIED.** Fee cap on what integrators may charge taxpayers: "Rs 10 per invoice or Rs 1 million" annually (Tribune Aug 2025; Business Recorder Aug 2025). PRAL is exempt from the licensing conditions and **must provide integration free on demand** plus free downloadable software (Rule 150XF; Business Recorder 30 Jan 2025; FBR FAQ).

**Technical approach (PRAL "Technical Specification for DI API" v1.12, 24 Jul 2025; PRAL DI User Manual v1.6, updated 16 Apr 2026):**
- REST/JSON over HTTPS. Sandbox: `https://gw.fbr.gov.pk/di_data/v1/di/postinvoicedata_sb` and `validateinvoicedata_sb`; production: same paths without `_sb`. Reference GETs under `gw.fbr.gov.pk/pdi/v1/` (provinces, document types, HS codes, UoM, transaction types, SRO schedules, rates).
- Auth: bearer token issued by PRAL, 5-year validity, obtained by the registered person in **IRIS → Digital Invoicing** after selecting integrator and business nature/sector; IP whitelisting; sandbox token first, **production token auto-issued once all required scenario test invoices (SN001–SN028) pass**.
- Payload: header (invoiceType Sale Invoice/Debit Note, invoiceDate, seller/buyer NTN-CNIC, names, provinces, addresses, buyerRegistrationType, invoiceRefNo, scenarioId in sandbox) + items (hsCode, description, rate, uoM, quantity, valueSalesExcludingST, salesTaxApplicable, further/extra tax, sroScheduleNo, discount, saleType). Response: statusCode "00" + FBR invoice number, or item-level error codes.
- Print: FBR DI logo + QR (v2.0, 25×25, 1"×1" per API spec; rules PDF says 7×7 mm) on every invoice.

**Can an open-source self-hosted product integrate directly?** The rules and STGO 01/2026 say integration must be "through a licensed integrator", and the FBR FAQ says direct integration is not permitted. However, the PRAL route is functionally direct: the taxpayer selects PRAL (free) in IRIS, PRAL whitelists their IP and issues tokens, and the taxpayer's own software calls the FBR gateway. Vendors describe exactly this. **The software vendor does not need a licence; the taxpayer needs PRAL (or another integrator) as integrator of record, and the software needs to speak the DI API.** Whether PRAL will whitelist arbitrary VPS IPs of thousands of small self-hosters at scale is a practical, not legal, question — UNVERIFIED. Business Recorder (Aug 2025) reported PRAL does not whitelist IPs for other integrators' clients, implying integrators run their own gateways.

**Implication:** build a first-class FBR DI API client (sandbox scenario runner, token storage, HS/UoM reference sync, QR/logo print, 72-hour edit window, debit/credit notes) and document the IRIS-PRAL enrolment; no licence needed. Consider a pluggable adapter so users on a private integrator can route through it.

## 4. Returns, withholding, provincial services tax

**Federal sales tax (goods):** monthly STR-7 on IRIS: Annex-A purchases (auto-populated from suppliers' Annex-C), Annex-B imports, Annex-C sales (now auto-populated from digital invoices — reconciliation to DI data is the pain point), Annex-D exports, Annex-F stock, Annex-H refunds, Annex-I debit/credit notes. Deadlines: Annex-C/A upload by 10th, payment by 15th, return by 18th of the following month. Input tax capped at 90% of output (s.8B). Sales tax withholding by withholding agents (Eleventh Schedule) — SRO 69 added "sales tax withheld at source" as a mandatory e-invoice field. Finance Act 2026 requires records/statements in electronically readable format (CSV/XLSX/XML/JSON; PDFs and scans excluded).

**Income-tax withholding:** the SME as withholding agent (s.153 goods/services/contracts, s.149 salaries, s.155 rent) must deduct, deposit and file periodic withholding statements (s.165) in IRIS. Finance Act 2025 (1 Jul 2025): 50% disallowance of expenses attributable to any single cash sale >Rs 200,000 (s.24), 10% disallowance of payments to persons without NTN (s.21), payments only via banking/digital channels. Finance Act 2026 adds a penalty equal to excess withholding credit claimed (s.168).

**Provincial sales tax on services:** four authorities (PRA, SRB, KPRA, BRA), each with own registration, monthly return, rates and POS/e-invoice systems for restaurants, salons, hotels, etc. FBR's draft SRO 288(I)/2026 (18 Feb 2026) would pull these service businesses into an FBR income-tax e-invoicing integration; provinces formally opposed it in March 2026 as unconstitutional duplication. KPMG and Thomson Reuters say it is still draft; a Sept 2026 vendor blog treats it as in force — finalisation UNVERIFIED.

**Implication:** a Pakistani chart of accounts must carry federal ST output/input with 90% cap logic, ST and IT withholding sub-ledgers, and per-province services-tax ledgers; export Annex-A/C/I and withholding statements as IRIS-compatible spreadsheets. Provincial POS integrations are four more APIs — defer.

## 5. Income tax for small traders / sole proprietors

- **Filing:** an individual with business income files an annual return plus mandatory wealth statement (s.116) on IRIS; due 30 Sep 2026 for individuals/AOPs (companies 31 Dec). New return-form SRO 1495(I)/2026 issued ~4 Sep 2026. Non-filers face s.114C "ineligible person" restrictions since Finance Act 2025.
- **Tajir Dost (Apr 2024):** failed; IMF agreed in Mar 2025 to drop it, with FBR pointing instead to Rs 400bn collected via 236G/236H withholding on distributors/retailers.
- **Successor — "Special Procedure for Small Shopkeepers, Tax Year 2026" (notified Jul 2026):** eligibility: income only from a single retail shop, turnover threshold reported variously as ~Rs 20m and Rs 200m — UNVERIFIED; not Tier-1; minimum Rs 25,000 tax with return; simplified return in Urdu/regional languages; exemption from s.153 withholding, s.113 minimum tax, audit, **and from POS/digital invoicing**; "Green Plate" shop marker; penalties Rs 10k/25k/50k (ProPakistani 28 Jul 2026).
- **Demand driver:** the scheme still requires declaring sales/purchases/expenses/assets and expects bank deposits to match declared income; with cash-sale disallowances, 114C restrictions and 236G/H withholding, small traders now need at minimum simple books.

**Implication:** the volume market (unregistered shopkeepers) needs a simple cash-book/stock/expense ledger with Urdu UI that outputs the simplified return numbers — not e-invoicing.

## 6. FBR-recognised software and free government tools

- **No FBR "approved accounting software" list exists.** FBR only licenses integrators. "FBR-approved"/"PRAL-approved" in vendor marketing means sandbox scenarios passed or an integrator partnership.
- **Free options:** IRIS Digital Invoicing portal offers a manual invoice-generation mode for low-volume taxpayers alongside API mode (PRAL manual v1.6); Rule 150XF obliges PRAL to provide free integration and free downloadable software — no evidence of a widely used shipped PRAL desktop/POS app (UNVERIFIED); FBR "Tax Asaan" app is for returns/verification, not invoicing.

## Barriers and opportunities

- **Barrier: two-tier market.** Registered SMEs must e-invoice; the far larger unregistered base is explicitly exempted. One product cannot serve both without two different UX modes.
- **Barrier: integrator-of-record gatekeeping.** Taxpayer must enrol via IRIS and get PRAL to whitelist an IP and issue tokens; self-hosted deployments on dynamic or shared IPs, and PRAL's responsiveness at scale, are unproven.
- **Barrier: moving target.** Six notifications in 18 months, a 72-hour edit rule, a Finance Act 2026 suspension power, a contested draft for services, four provincial systems. Compliance content needs a maintained rules pack separate from core code.
- **Barrier: return-form churn and IRIS-only filing.** No public return-filing API; the product can only prepare annex spreadsheets/JSON for upload.
- **Opportunity: no licence needed to ship an FBR DI client.** Public JSON API, sandboxed, stable since v1.12 (Jul 2025). An open-source, well-tested PHP client would be the first of its kind.
- **Opportunity: Annex-C/DI reconciliation and 90% cap logic** are recurring monthly pain that cheap local tools handle badly.
- **Opportunity: enforcement pressure is real and rising** (suspension power from 1 Jul 2026, importer crackdown, cash-sale disallowances, 114C). Demand for compliant, cheap, on-prem tooling is created by law.
- **Opportunity: Urdu-first "small shopkeeper" ledger** aligned to the Rs 25k fixed-tax procedure and wealth statement has no obvious open-source incumbent.

## Sources

- FBR SRO 69(I)/2025 — https://download1.fbr.gov.pk/Docs/202541712407495sro69(I)2025.pdf
- FBR Chapter XIV rules — https://download1.fbr.gov.pk/Docs/2025571554338577ChapterXIV.pdf
- FBR licensed integrators — https://www.fbr.gov.pk/list-of-license-interprator/173967/173971
- FBR DI FAQs — https://fbr.gov.pk/faqs/173967/173969
- FBR DI user manual page — https://www.fbr.gov.pk/di-technical-assistance/173967/174202
- PRAL DI API spec v1.12 — https://download1.fbr.gov.pk/Docs/20257301172130815TechnicalDocumentationforDIAPIV1.12.pdf
- PRAL DI user manual v1.6 (mirror) — https://conseric.pk/wp-content/uploads/2026/05/fbr-digital-invocing-user-manual-pral-version-1.6.pdf
- FBR Tier-1 STGO — https://fbr.gov.pk/sales-tax-general-order-tier-1/163085/173442
- FBR Finance Bill 2026-27 — https://fbr.gov.pk/Budget2026-27/FinanceBill.html
- KPMG Integration Rules brief (Feb 2025) — https://assets.kpmg.com/content/dam/kpmg/pk/pdf/2025/02/A-Brief-on-Integration-Rules.pdf
- KPMG Finance Act 2026 brief — https://assets.kpmg.com/content/dam/kpmgsites/pk/pdf/2026/07/A%20Brief%20of%20Finance%20Act%202026.pdf.coredownload.inline.pdf
- KPMG US Finance Act 2026 — https://kpmg.com/us/en/taxnewsflash/news/2026/07/pakistan-tax-customs-measures-finance-act-2026.html
- KPMG US SRO 1413 deadlines — https://kpmg.com/us/en/taxnewsflash/news/2025/08/pakistan-compliance-deadlines-e-invoicing.html
- KPMG US draft e-invoicing rules (Mar 2026) — https://kpmg.com/us/en/taxnewsflash/news/2026/03/pakistan-draft-e-invoicing-fmv-property.html
- KPMG Pakistan tax alerts SRO 288 — https://kpmg.com/pk/en/home/insights/2026/01/tax-alert.html · https://kpmg.com/pk/en/insights/2026/03/tax-alert.html
- EY (Apr 2025) — https://www.ey.com/en_gl/technical/tax-alerts/pakistan-extends-scope-of-electronic-invoicing-to-corporate-and-noncorporate-registered-persons
- Express Tribune (Aug 2025) — https://tribune.com.pk/story/2559223/trader-e-invoice-deadline-extended · (Feb 2026) — https://tribune.com.pk/story/2593493/fbr-unveils-draft-amendments-to-income-tax-rules-mandates-pos-integration-for-businesses
- Business Recorder — https://www.brecorder.com/news/40345367/registered-persons-retailers-pral-to-provide-free-integration-services · https://www.brecorder.com/news/40357172/digital-invoicing-fbr-directs-retailers-to-approach-4-approved-licensed-integrators · https://www.brecorder.com/news/40377164/digital-invoicing-system-registered-taxpayers-adopting-cautious-approach-fbr · https://www.brecorder.com/news/40407975 · https://www.brecorder.com/news/40411087/pras-oppose-fbrs-sro-288i2026 · https://www.brecorder.com/news/40427085/digital- · https://www.brecorder.com/news/40423907 · https://www.brecorder.com/news/40437831
- Dawn (Apr 2025) — https://www.dawn.com/news/1903690/fbr-notifies-four-firms-to-register-retailers
- Profit — https://profit.pakistantoday.com.pk/2026/02/19/fbr-directs-integration-of-e-invoicing-with-income-tax-system-for-specified-businesses/ · https://profit.pakistantoday.com.pk/2025/03/15/imf-agrees-to-scrap-tajir-dost-scheme-as-fbr-surpasses-tax-collection-target-report
- ProPakistani — https://propakistani.pk/2026/07/28/fbr-rolls-out-new-fixed-tax-scheme-for-small-shopkeepers/ · https://propakistani.pk/2026/02/19/fbr-mandates-e-invoicing-integration-for-clubs-hospitals-retailers-online-sellers-and-schools/
- TaxationPk SRO 1852 — https://news.taxationpk.com/fbr-extendes-date-for-electronic-invoicing-integration/
- Thomson Reuters/Pagero Pakistan updates — https://europe.thomsonreuters.com/compliance/regulatory-updates/pakistan
- VATupdate — https://www.vatupdate.com/2025/07/18/pakistan-extends-e-invoicing-integration-deadline-again-for-corporate-and-non-corporate-entities/ · https://www.vatupdate.com/2026/04/11/pakistan-clarifies-integration-and-amendment-rules-for-mandatory-e-invoicing/ · https://www.vatupdate.com/2026/08/27/pakistan-finance-act-2026-expands-sales-tax-e-invoicing-enforcement-and-ev-relief/
- Mettis 72-hour cap — https://mettisglobal.news/FBR-imposes-72hour-cap-on-Einvoice-edits-59355
- SRB POS — https://www.srb.gos.pk/srb/point-of-sale-pos/
- RM Tahir on Finance Act 2025 disallowances — https://www.rmtahir.com/articles/navigating-the-new-tax-landscape-understanding-disallowance-provisions-under-finance-act-2025
- TaxBuddy Umair Tier-1 (Jun 2026) — https://www.taxbuddyumair.com/articles/tier-1-retailer-pos-integration/
- PakTaxCalc STR-7 annexes — https://paktaxcalc.com/blog/sales-tax-return-filing-pakistan.html
- Filing.pk return guide — https://filing.pk/blog/how-to-file-income-tax-return-pakistan-2026
- Vendor blogs (procedural colour only, UNVERIFIED): https://www.switchertechno.com/how-to-register-fbr-digital-invoicing/ · https://www.switchertechno.com/fbr-digital-invoicing-cost-pakistan/ · https://www.switchertechno.com/finance-act-2026-fbr-sales-tax-registration-suspension/ · https://www.switchertechno.com/pra-pos-integration-punjab/ · https://difbr.pk/blog/fbr-electronic-invoicing-integrator-license-guide
