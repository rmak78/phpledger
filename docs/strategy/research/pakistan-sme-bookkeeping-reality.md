# Research: what Pakistani shopkeepers, traders and micro-businesses actually use to keep books

Research date: 15 September 2026. Supporting evidence for [Pakistan market](../PAKISTAN-MARKET.md) §1 and §4. Play Store figures are as shown on the listing on that date (Google shows bands; AppBrain estimates noted where available). Anything not confirmed by a primary or reputable secondary source is marked UNVERIFIED.

## 1. Digital khata / udhaar apps

| App | Owner | Play downloads | Rating (reviews) | Last update | Alive in 2026? | Pricing | Beyond plain khata |
|---|---|---|---|---|---|---|---|
| Udhaar Book → "Rupin (formerly Udhaar Book)" | Toko Lab Inc., Karachi ($6M seed 2021) | 5M+ (site claims 5.1–5.6M businesses) | 4.3 (41.8K) | 29 Aug 2026 | Yes. Rebranded to Rupin in 2026 (Play title, social handles, web.rupin.pk). No formal announcement found — rebrand date UNVERIFIED | Free. Monetises via 2% easyload / 4% voucher commissions, wallet, bill pay | Invoice Book (sales-tax invoices, logo, QR), Sale POS, Stock Book, Cash Book, Staff Book (attendance/payroll), purchases/vendors, wallet, web app |
| CreditBook | CreditBook Technologies / CreditBook Financial Services (Pvt) Ltd ($11M Series A Dec 2021) | 5M+ (AppBrain: 5.2M cumulative, ~130/day) | 4.1 (25.2K) | 6 Jul 2026 | Yes, but pivoted to lending: SECP NBFC licence, digital-lending whitelist, "Tijara" lending module in-app | Free | CashBook, StockBook, BillBook, wallet, Tijara loans; Urdu/Sindhi/Pashto/Punjabi |
| DigiKhata | Digi Technologies Pte Ltd (Singapore) / Digi Khata SMC Pvt Ltd (Faisalabad) ($2M seed) | 5M+ | 4.4 (210K — most reviews by far) | 2 Sep 2026 | Yes | Free with ads + IAP. **DigiKhata Pro: Rs 200 upfront + Rs 200/month — only 100+ installs, 3.6★, last updated Aug 2024 (the paid tier flopped)** | Party ledger, Staff Book, Stock Book, Cash/Expense Book, Bill Book, bank reconciliation, multi-user, DigiPOS (NFC tap-to-pay), DigiQR, HBL MfB merchant lending, 1Bill |
| Easy Khata | Bazaar Technologies | 5M+ (AppBrain: 7.6M cumulative, ~1,800/day) | 4.0 (33.5K) | 30 Jul 2026 (v7.0.16 on 12 Sep 2026 per AppBrain) | Yes — still shipping, but still a pure ledger: no invoicing, inventory or POS | Free, no IAP | Cloud backup, WhatsApp/SMS reminders, PDF reports, English/Urdu/Sindhi. Reviews complain of sync loss and slow support |
| Dukan.pk | Dukan Pte Ltd (Monis Rahman) | 500K+ | 4.3 (3.86K) | 11 Sep 2026 | Yes (cut 25% of staff 2022) | Free | Online store, inventory with variants, khata, printed bills, COD/card/Easypaisa/JazzCash/QisstPay, courier integrations. Users ask for a desktop portal — mobile-only is painful for bulk work |
| KhataBook (India) | Khatabook Inc, Bengaluru | Not in Pakistani rankings | – | – | Alive in India; built for INR; not localised for Pakistan. Pakistani use UNVERIFIED, likely marginal | Free basic; paid premium/SMS in India | – |
| mKhata, Hisaab (Retailo) | – | – | – | – | "Minimal traction despite funding" (Data Darbar, Nov 2022). Dead/dormant — UNVERIFIED | – | – |
| Mobikhata | – | – | – | – | New 2026 entrant, "100% free, no ads". Traction UNVERIFIED | Free | – |

**Engagement reality:** Data Darbar (Nov 2022) counted 17.8M combined downloads for the big four but only ~740.5K combined monthly active users in Oct 2022 — roughly 4% of installs. Every khata app has since bolted on invoicing, stock, staff/payroll, POS and payments because plain khata does not monetise; subscription models were "untested locally" and DigiKhata Pro is the only hard test found — it failed.

## 2. B2B commerce / retail-tech that bundled bookkeeping

| Company | What it was | Bookkeeping/POS angle | Status Sept 2026 |
|---|---|---|---|
| Bazaar Technologies | B2B marketplace + Easy Khata; $100M+ raised | Easy Khata; Keenu POS acquiring after acquisition | Alive. Shut pharma & mobile verticals, laid off ~600 field staff (early 2024); shifted to direct-to-consumer grocery; acquired Keenu (EMI, POS acquiring, gateway, wallet) July 2025 |
| Retailo | B2B marketplace PK/KSA, ~$60M raised | Hisaab khata app (dead) | Shut distribution ops Oct 2023; pivoted to SaaS for distributors (listed as an Odoo partner). Customer numbers UNVERIFIED |
| Jugnu | B2B marketplace, $22.5M Series A | – | Marketplace shut July 2023; no live product found |
| Dastgyr | B2B marketplace, $37M Series A (2022) | – | Laid off ~85% of staff (Jan 2024). No 2025–26 news; status UNVERIFIED, presumed dormant |
| Tajir | YC-backed kiryana supplier, $17M Series A (2021) | – | Crunchbase "active", 1–10 employees, last news 2024. 2026 status UNVERIFIED |
| Salesflo (Retailistan) | Distributor sales-force / DMS app | Order booking for distributor reps, not retailer books | Alive: 100K+ installs, 4.4 (1.93K), updated 17 Aug 2026 |
| Keenu | Acquirer/gateway/wallet, EMI licence Feb 2025 | POS terminals, gateway, merchant wallets | Acquired by Bazaar July 2025 |

Net: of the 2021-vintage B2B wave, only Bazaar (now commerce + payments) and the pure-software players (Salesflo, Dukan) are clearly alive. None shipped a real accounting product for retailers; the ledger apps were customer-acquisition tools.

## 3. Payments / fintech touching SME bookkeeping

| Product | Bookkeeping-relevant features | Notes |
|---|---|---|
| JazzCash Business (10M+, 4.6, 104K reviews, updated 22 Jun 2026) | QR (static & amount-specific), payment requests / invoices with tracking, account & tax statements, supplier payments, salary disbursement, refunds, history | No khata, inventory or sales ledger |
| Easypaisa merchant (Easy Merchant app, portal, QR, gateway) | QR acceptance, gateway, merchant portal | Site returned 403; ledger/invoicing UNVERIFIED |
| SadaPay SadaBiz (2024) | Invoicing — only for freelancers billing foreign clients | Not a domestic SME ledger |
| NayaPay business accounts | Business accounts/cards | Ledger/invoicing UNVERIFIED |
| Raast P2M (SBP) | QR acceptance; government reimburses merchants 0.5% or Rs 100 per P2M transaction | Q3 FY26: 55.9M P2M transactions vs 664M P2P. Merchants accepting digital payments rose from ~500K (Jun 2025) to 2.03M (Jun 2026) against est. 4–5M retail outlets; centralised merchant database announced 3 Sep 2026 |
| Keenu / PayFast / Safepay | Gateways / POS acquiring | Invoicing/payment-link features UNVERIFIED |
| Card POS | ~196K terminals at 159K merchants (FY25) | Small against 4–5M outlets |

None of the wallets or gateways provides a ledger, inventory or tax-ready books. All provide a transaction statement — which is what a trader hands to the accountant.

## 4. The status quo: paper, Excel, pirated desktop software, the munshi

No survey was found that quantifies the paper / Excel / software split for Pakistani SMEs. Every figure below is indirect; proportions UNVERIFIED.

- **Paper is the default.** Express Tribune (Dec 2025): "Kiryana shops tally credit in worn ledgers"; 95% of establishments employ fewer than 10 workers; the modal retailer is a 1–3 person shop. A Pakistani bookkeeping firm describes the baseline as "handwritten ledgers or basic spreadsheets".
- **Khata apps replaced the credit notebook, not the books.** 5M+ installs each but ~4% MAU (2022); feature set is receivables, cash-in/out, reminders. No double entry, trial balance, or tax-return output.
- **Desktop accounting = Peachtree / QuickBooks / Tally, learned in vocational institutes.** Every "computerised accounting" diploma found teaches exactly this trio. Sage retired the Peachtree name in 2013, so any "Peachtree" in use in 2026 is a decade-old copy — strong circumstantial evidence of unlicensed use, but piracy share UNVERIFIED.
- **Local incumbents for the tier above:** Candela RMS, LedgerMax, Splendid Accounts, plus dozens of custom software-house ERPs whose pitch is that Peachtree/QuickBooks/Tally "are not localised for Pakistan's tax law".
- **The munshi / tax consultant is the real user of the books.** A trader in the tax net outsources it. Published fees (Kamboh Associates, 2026): NTN Rs 2,000; business income-tax return Rs 5,000/yr; STRN Rs 3,000; monthly sales-tax return Rs 3,000/mo. Karandaaz (2023): SMEs say "the burden of tax compliance is a bigger hurdle than the actual tax paid".
- **Traders actively resist documentation.** ~400K of 3.5M+ traders file returns; ~41K persons actually pay sales tax (Friday Times, 2019 data). Tajir Dost registered ~70–75K shopkeepers by Mar 2025. FBR Tier-1 POS integration: 9,834 retailers (Mar 2025) → 17,337 (end FY26). PIDE (2022): retailers fear their sales tax returns will jump because of underreporting. 19 July 2025: Karachi and Lahore traders shut down against Finance Act 2025-26 — arrest powers (s.37A/B), disallowance of cash transactions above Rs 200,000, mandatory digital invoicing and e-Bilty.
- **Digital invoicing is now the compliance forcing function** for the registered minority, not the kiryana.

## 5. SME digitisation statistics

| Metric | Figure | Source |
|---|---|---|
| Number of SMEs | ~5.2M (Karandaaz 2023); ">5M, extrapolated from the 2005 census" (ILO/SMEDA 2024) | see sources |
| SECP-registered companies | ~199K | ILO/SMEDA 2024 |
| AOPs filing income-tax returns | ~64K (FBR 2018) | Karandaaz 2023 |
| Traders filing returns | ~400K of 3.5M+; ~41K paying sales tax | Friday Times (2019 data) |
| Tajir Dost registrations | 70–75K (Mar 2025) | Business Recorder |
| FBR POS-integrated Tier-1 retailers | 17,337 (end FY26) | LHR Times / FBR |
| Merchants accepting digital payments | 2.03M (Jun 2026) of est. 4–5M outlets | TechX / government |
| Card POS terminals | ~196K at 159K merchants (FY25) | SBP via TechJuice |
| Formal firms (5+ staff) with bank account / bank loan | 92.4% / 2.1% | World Bank Enterprise Survey 2022 |
| Households with computer/laptop/tablet | 12% (urban 19%, rural 7%, Islamabad 40%) | PSLM 2019-20 (latest found) |
| Households with mobile / internet | 96% / 70% | HIES 2024-25 |
| Smartphone usage | 71.6%; 28% of mobile users still on 2G | Economic Survey 2025-26 |
| Internet users | 117M (45.6%); 194M mobile connections | DataReportal Digital 2026 |
| Shared cPanel hosting | Rs 1,000–10,000/yr; VPS from Rs 2,200/mo | Host24 |
| Share of SMEs with hosting / a VPS | No data exists. UNVERIFIED; almost certainly <1% outside software firms, e-commerce sellers, agencies | inference |

Read together: the trader has a phone, a JazzCash/Easypaisa merchant account, a WhatsApp group with his supplier, and a notebook. The only computer in the chain sits with the accountant.

## 6. Pricing sensitivity

| Product | Price | Evidence of willingness to pay |
|---|---|---|
| All khata apps | Rs 0 | Zero direct revenue; monetised via easyload commissions (2–4%), lending, payments |
| DigiKhata Pro | Rs 200 + Rs 200/mo | 100+ installs after 2+ years — effectively nil |
| Cloud POS | Rs 2,000–5,000/mo; on-prem from Rs 40,000 one-time; basic desktop POS Rs 15–30K one-time | Vendor-published bands |
| Granet Pro POS | Rs 2,999 / 5,999 / 9,999 per branch/mo + Rs 50,000 setup + Rs 2,000/mo FBR module | Restaurant/retail chains |
| LedgerMax | Rs 6,600/mo, unlimited users | Registered SMEs |
| Splendid Accounts | $18/mo (1 user) ≈ Rs 5,000 | Priced in USD — top slice |
| Tax consultant | Rs 3,000/mo for monthly ST return; Rs 5,000/yr business return | **The benchmark:** a compliant small trader already pays ~Rs 36–60K/yr to a human for compliance and nothing for software |

Willingness to pay is bimodal: Rs 0 for the 4–5M informal shops; Rs 3,000–10,000/month tolerated only once a business is sales-tax registered and must produce FBR-integrated invoices — and even then the money often goes to the consultant, not software.

## Implications for a self-hosted open-source PHP accounting system aimed at Pakistani SMEs

1. **The trader does not have hosting and will not get any.** 12% of households own a computer; no data exists on SME hosting because the number is negligible. "Runs on a simple VPS" describes the deployment capability of a software house, an accounting firm, or a hosting reseller — not the shopkeeper. Design the install path for a third party who hosts it for many clients.
2. **The end user is phone-first; the operator of the books is desktop-first.** Every product that reached 5M installs is an Android app with WhatsApp reminders and Urdu UI; Dukan's users beg for a desktop portal for bulk work. A PHP web app must work as a mobile web app for counter entry and as a desktop app for whoever closes the month.
3. **The buyer is the accountant / tax consultant, not the trader.** A system a consultant can run for 20–50 clients (multi-company, per-client access, FBR-ready outputs) has a real buyer; one aimed at the individual shop competes with five free, well-funded apps.
4. **Free is the price for anything the khata apps do.** The edge has to be what those apps structurally will not do: real double-entry, audit trail, multi-company, tax returns, data ownership, no dependence on a startup that may pivot or be acquired.
5. **Pakistan-specific tax is both the moat and the burden.** A system that is not FBR-integration-ready will be rejected by exactly the segment that can pay; building and maintaining that integration is a compliance project, not just code.
6. **Assume the counter is offline and the data is in Urdu.** 28% of mobile users on 2G; the khata apps all advertise offline entry and PDF reports. A PHP server app needs an offline-tolerant front end (queued entries) and RTL/Urdu (plus Sindhi/Pashto) from day one.
7. **Payments are the on-ramp, not accounting.** 2.03M merchants have a QR; JazzCash Business has statements and payment requests but no ledger. Importing JazzCash/Easypaisa/Raast/bank statements and reconciling them — what the accountant does by hand — is the path in.
8. **The graveyard is real.** Jugnu, Retailo's marketplace and Hisaab, Dastgyr, mKhata are gone or dormant; CreditBook survives as a lender; Bazaar survives by buying a payments company. Retailer-facing software in Pakistan has never sustained a business on software revenue alone. Sustainability must assume services (hosting, FBR integration, consultant training), not licences.

## Sources

Khata apps: https://play.google.com/store/apps/details?id=com.oscarudhaarapp&hl=en · https://udhaar.pk/ · https://web.rupin.pk/ · https://www.thenews.com.pk/print/907115-startup-udhaar-book-raises-6-million-seed-funding · https://play.google.com/store/apps/details?id=com.creditbookpk.creditbook · https://www.appbrain.com/app/creditbook-digital-khata/com.creditbookpk.creditbook · https://www.creditbook.pk/licensing · https://www.dawn.com/news/1664321 · https://restofworld.org/2024/3-minutes-with-hasib-malik-creditbook/ · https://play.google.com/store/apps/details?id=com.androidapp.digikhata&gl=PK · https://play.google.com/store/apps/details?id=com.androidapp.digikhata.pro&hl=en · https://digikhata.pk/ · https://play.google.com/store/apps/details?id=com.tech.bazaar.easykhata&hl=en&gl=US · https://www.appbrain.com/app/easy-khata-apka-digital-khata/com.tech.bazaar.easykhata · https://www.menabytes.com/bazaar-easy-khata/ · https://play.google.com/store/apps/details?id=pk.dukan&hl=en · https://profit.pakistantoday.com.pk/2022/07/17/pakistans-ecommerce-startup-dukan-pk-lays-off-25pc-workforce-as-it-focuses-on-profitability/ · https://insights.datadarbar.io/the-impressive-growth-trajectory-of-pakistani-khata-apps-and-what-lies-ahead-for-them/ · https://mobikhata.com/blog/best-khata-app-in-pakistan-khatabook-alternatives.html · https://inc42.com/buzz/khatabook-fires-over-40-employees-restructuring-exercise/

B2B / retail-tech: https://profit.pakistantoday.com.pk/2024/02/12/b2b-startups-shut-shop-or-iscale-down-bazaar-too-struggles-to-tide-over-current-crisis · https://profit.pakistantoday.com.pk/2024/01/18/cheetay-mulling-shut-downg-dastgyr-and-others-look-towards-layoffs-as-startups-go-through-another-purge · https://insights.datadarbar.io/pivot-or-shutdown-the-case-of-jugnu/ · https://profit.pakistantoday.com.pk/2024/10/14/can-retailos-saas-gamble-pay-off/ · https://www.odoo.com/customers/retailo-pakistan-3966923 · https://profit.pakistantoday.com.pk/2025/07/10/bazaar-technologies-aims-for-profitability-acquires-keenu-to-expand-e-commerce-fintech/ · https://www.menabytes.com/bazaar-keenu/ · https://propakistani.pk/2025/07/09/bazaar-acquires-keenu-to-become-pakistans-leading-e-commerce-provider/ · https://propakistani.pk/2025/02/26/keenu-secures-emi-commercial-license-set-to-launch-merchant-wallets/ · https://www.crunchbase.com/organization/tajir · https://profit.pakistantoday.com.pk/2021/06/02/lahore-based-b2b-ecommerce-marketplace-tajir-raises-17mn-in-series-a-round/ · https://www.dawn.com/news/1694793 · https://play.google.com/store/apps/details?id=com.retailistan.salesflo&hl=en_IN

Payments / fintech: https://play.google.com/store/apps/details?id=com.ibm.jazzcashmerchant&hl=en_US · https://easypaisa.com.pk/business-solutions/ · https://sadapay.pk/blogs/sadabiz-streamlines-invoicing-for-pakistani-freelancers-with-industry-first-apple-pay-integration · https://www.sbp.org.pk/assets/documents/press-release/Pr-25-Jun-2026.pdf · https://www.techjuice.pk/pakistans-digital-payments-surge-as-sbp-releases-fy25-payment-systems-review/ · https://techx.pk/pakistan-centralized-merchant-database-cashless-economy/ · https://www.brecorder.com/news/40276991/digital-payments-sbp-launches-raast-p2m-service

Status quo, tax, informality: https://tribune.com.pk/story/2584434/informal-retail-economys-blind-spot · https://thefridaytimes.com/25-May-2024/the-retail-sector-s-dollar-15-billion-tax-potential · https://www.brecorder.com/news/40354403 · https://www.lhrtimes.com/2026/08/31/fbrs-pos-network-crosses-17300-retailers-as-integration-jumps-31/ · https://pide.org.pk/research/fbrs-pos-integration-digitalisation-of-business-transactions-and-associated-challenges/ · https://www.dawn.com/news/1925345 · https://www.brecorder.com/news/40414134 · https://kpmg.com/us/en/taxnewsflash/news/2025/08/pakistan-compliance-deadlines-e-invoicing.html · https://www.karandaaz.com.pk/news-and-media/press-releases/karandaaz-launches-report-state-business-taxation-pakistan-challenges-smes · https://karandaaz.com.pk/blog/unlocking-micro-small-medium-enterprise-msme-potential-pakistan/ · https://www.ilo.org/sites/default/files/2024-05/ILO_Pakistan_SMEDA%20mapping%20informality%20enterprises_WEB_FINAL_v2_0.pdf · https://espanol.enterprisesurveys.org/content/dam/enterprisesurveys/documents/country/Pakistan-2022.pdf · https://kambohassociates.com/pricing · https://www.klapakistan.com/bookkeeping-software-in-pakistan-which-one-is-best-for-your-business/ · https://softwares.com.pk/best-accounting-software-in-pakistan · https://hisaab.pk/list-of-customized-accounting-software-in-pakistan/ · https://pakcollege.edu.pk/course/peachtree-course-in-karachi/ · https://www.viit.pk/computerized-accounting · https://microtechinstitute.com/product/peachtree-sage-diploma-course/

Digitisation stats: https://www.dawn.com/news/1637075 · https://independent-pakistan.com/news/pakistans-internet-users-surge-57-percent-household-access-hits-70-percent-survey-shows/ · https://www.phoneworld.com.pk/pakistan-smartphone-usage-71-percent-economic-survey-2026/ · https://datareportal.com/reports/digital-2026-pakistan · https://pta.gov.pk/category/pta-releases-annual-report-2024%E2%80%9325-1374704410-2026-01-01 · https://www.host24.com.pk/

Pricing: https://oneclickpos.pk/pos-software-features-updates/pos-software-pricing-in-pakistan-2025/ · https://www.granetpro.com/pricing.html · https://www.ledgermax.pk/pricing · https://softwarefinder.com/accounting-software/splendid-accounts · https://www.capterra.com/p/97672/Candela-RMS/

Gaps not closed: no primary survey quantifies paper vs Excel vs software share; no source directly measures pirated Peachtree/Tally use; Easypaisa's business site blocked fetching; Dastgyr, Tajir and Retailo-SaaS have no verifiable 2025–26 operating status; the Udhaar→Rupin rebrand has no press announcement found.
