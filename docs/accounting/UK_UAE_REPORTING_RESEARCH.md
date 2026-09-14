# UK and UAE reporting research

Research date: 14 September 2026. User priority: **Pakistan first, then UK and UAE**. This is an implementation reference, not an activated country package or an accounting compliance opinion. The user requires the accounting system to follow the applicable rules, with ICMA Pakistan, ICAP and ACCA guidance informing the work. [Pakistan research](PAKISTAN_REPORTING_RESEARCH.md) owns the first-country detail.

## The selection is an accounting decision

A country selector cannot safely select one universal financial statement format. Capture legal form, reporting purpose, entity eligibility, financial-year dates, adopted framework and edition, and approval of the relevant accounting policies. Currency, browser locale and IP location are presentation hints only. They must not select accounting rules.

ACCA's published material explains the IFRS statement terminology and offers separate UK model-account sets for different frameworks. It does not establish a single international “ACCA format.” The official model sets include FRS 102, Section 1A, FRS 105 and separate LLP/charity examples. Access requires a member request; no request was sent and the model files were not obtained. [ACCA terminology](https://www.accaglobal.com/middle-east/en/student/exam-support-resources/fundamentals-exams-study-resources/f7/technical-articles/rd1.html), [ACCA model-account availability](https://www.accaglobal.com/uk/en/member/sectors/smp/creating-tomorrows-practice-today/the-practice-of-tomorrow-today/practice-management/useful-resources/model-accounts.html).

## United Kingdom

| Candidate profile | Intended selection | Product consequence |
|---|---|---|
| UK FRS 102, Section 1A | Eligible small entities electing this regime | A defined small-entity statement and disclosure pack, not merely fewer accounts in the general template |
| UK FRS 105 | Eligible micro-entities choosing the micro-entity regime | Its own recognition, measurement and presentation rules; do not treat it as a cosmetic FRS 102 theme |
| UK FRS 102, full | Entities applying this framework outside the reduced small-entity presentation | Full applicable statements and disclosures |

The FRC identifies FRS 102 as the framework for entities not applying adopted IFRS, FRS 101 or FRS 105. The 2024 periodic-review amendments principally apply from accounting periods beginning 1 January 2026. FRS 105 is required when an eligible entity chooses the micro-entity regime; eligibility does not require that choice. Later amendments must be read with each base edition. [FRC FRS 102](https://www.frc.org.uk/library/standards-codes-policy/accounting-and-reporting/uk-accounting-standards/frs-102/), [FRC FRS 105](https://www.frc.org.uk/library/standards-codes-policy/accounting-and-reporting/uk-accounting-standards/frs-105/).

**Where to find the formats:** FRS 102 Section 1A, Appendices A and B explain the statutory, abridged and adapted alternatives for balance sheet and profit-and-loss presentation; Appendix C sets out UK small-entity disclosure requirements. These are distinct choices, with conditions. Our initial UK target should be a full, unabridged small-company preparation view, with the applicable framework and disclosures confirmed by a UK reviewer. Do not automatically use a shortened filing view for the owner's or accountant's financial statements. [Official FRS 102, September 2024, pages 26–30](https://www.frc.org.uk/documents/7668/FRS_102_September_2024_tmKYWO6.pdf).

The original statutory instrument contains the familiar UK vertical balance-sheet format and profit-and-loss alternatives. It is useful as a visual reference for headings and hierarchy, **but this PDF is the original 2008 instrument, not a consolidated current-law source**. The current Schedule 1 HTML request returned HTTP 429 in this research pass. Read the revised legislation and applicable FRC amendments before a UK package is activated. [Original SI 2008/409, Schedule 1](https://www.legislation.gov.uk/uksi/2008/409/pdfs/uksi_20080409_en.pdf), [current Schedule 1](https://www.legislation.gov.uk/uksi/2008/409/schedule/1).

The 2025 company-size changes mean old threshold figures in a base PDF cannot be used uncritically. Eligibility includes more than a revenue number, and preparation and public filing have different requirements. Store the eligibility evidence and effective rules rather than guessing from an industry choice. [GOV.UK annual accounts guidance](https://www.gov.uk/annual-accounts/microentities-small-and-dormant-companies), [Companies House preparation and filing guidance](https://www.gov.uk/government/publications/life-of-a-company-annual-requirements/life-of-a-company-part-1-accounts).

## United Arab Emirates

For Corporate Tax purposes, Ministerial Decision 114 of 2023 specifies IFRS, with IFRS for SMEs permitted where the person's revenue does not exceed AED 50 million. It also provides a cash-basis route in specified circumstances, including revenue not exceeding AED 3 million. These are scoped tax rules, not permission to classify every UAE business as a cash-basis SME. [Official decision, Articles 2 and 4](https://mof.gov.ae/wp-content/uploads/2023/05/Ministerial-Decision-No.-114-of-2023-on-the-Accounting-Standards-and-Methods-for-Corporate-Tax-Purposes.pdf).

The FTA guide explains that financial statements supply accounting income and tax adjustments then determine taxable income. Its Section 3 distinguishes IFRS and IFRS-for-SMEs eligibility; Section 4 discusses accounting methods. A UAE financial statement profile, a tax computation and a VAT/tax-invoice profile must therefore remain separately identified. The current cash-only product preview does not implement these UAE accounting methods or tax computations. [FTA Accounting Standards guide, Sections 3–4](https://tax.gov.ae/Datafolder/Files/Guides/CT/Accounting%20Standards%20Guide%20-%2006%2011%202023.pdf).

Proposed UAE profiles should start with eligible ordinary non-financial single entities. Free-zone, tax-group, regulated-sector, consolidation and specific filing obligations need separate validation. A company may have obligations beyond the Corporate Tax framework discussed here; this research has not established every emirate, registrar or regulator's filing requirements.

## Standards change without rewriting history

IFRS 18 is effective internationally for annual periods beginning on or after 1 January 2027, with early application permitted. The 2025 IFRS for SMEs edition has the same international effective date. These are separate frameworks; the SMEs standard does not automatically inherit every IFRS 18 change. Local adoption, early adoption and the entity's actual reporting period must be checked before changing a profile. [IFRS 18](https://www.ifrs.org/issued-standards/list-of-standards/ifrs-18-presentation-and-disclosure-in-financial-statements/), [IFRS for SMEs](https://www.ifrs.org/issued-standards/ifrs-for-smes/), [IASB treatment of amendments in the SMEs review](https://www.ifrs.org/content/dam/ifrs/publications/ifrs-for-smes/english/2025/treatment-of-amendments.pdf).

The product should retain a versioned reporting profile and mapping snapshot for each approved report. Upgrading a country package must not silently relabel or reclassify a previously approved period. Recognition and measurement changes may require controlled accounting adjustments, not just a report template update. This is a proposed implementation requirement; the current schema has no country reporting profile.

## Proposed report workspace

This is original PHP Ledger product design, informed by the sources above:

1. **Report identity:** legal entity, statement title, period/as-of date, framework/version, currency and units, draft/review status.
2. **Comparable statement:** one vertical table with description, note reference, current period and comparative period. Management-only variance columns are optional and clearly separate from formal statement presentation.
3. **Useful structure:** meaningful account groups and subtotals, current/non-current distinctions where required, and mapped expense presentation. Gross profit is shown only when cost of sales is actually supported and complete.
4. **Evidence:** expand a line to its mapped accounts, then to scoped ledger activity and source documents. A drilldown must preserve the report's dates and mapping version.
5. **Completeness:** distinguish zero from unavailable, unmapped or incomplete opening data. Do not display an unsupported disclosure as a reassuring zero.
6. **Consistent output:** print/PDF/spreadsheet views use the same values, scope, signs and rounding. Disclosure and approval requirements remain visible.

An owner dashboard can summarize reviewed figures, but it is not the statement itself. A cash forecast is an assumption-driven planning report; it cannot replace a historical statement of cash flows. The initial complete country package must include every statement/disclosure required for its chosen framework and entity, rather than promising the same checklist for all regimes.

## Evidence limits and next gate

Read: official FRC standards and amendment pages, original UK statutory-format PDF, GOV.UK/Companies House guidance, ACCA public guidance/model availability, UAE Ministry of Finance decision, FTA guide and IFRS Foundation pages. No Google Drive documents or member-only ACCA model files were read. No outreach, registration, filing or payment was made.

This research changes documentation only. No framework is activated and no new migrations are introduced. Before implementation approval of a country package: confirm eligibility rules and current editions, map required recognition/measurement policies, create original reconciled examples, validate every line and disclosure with a qualified local reviewer, then test the coded report and exported output against those examples.
