# First stable release: reviewer and pilot brief

Prepared 18 September 2026. Local coordination brief; no advertisement or invitation has been sent. The owner reports that their accountant and lawyer have agreed to help. Their precise review scope and dates still need to be recorded. An independent application-security reviewer and pilot businesses are not yet confirmed.

The working target is 18 December 2026, within the provisional 11 December 2026–8 January 2027 window. These are engineering estimates. The [roadmap](../ROADMAP.md#current-delivery-contract-first-stable-10) contains the acceptance gates; this brief does not relax them.

## Ready-to-post volunteer advert

**Help review PHP Ledger before its first stable release — volunteer reviewers and small-business pilots wanted**

PHP Ledger is an open-source, self-hosted accounting application for small businesses. We are preparing its first stable release and looking for practical, independent feedback before recommending it for production use.

The planned first release focuses on English-language bookkeeping: receipts and expenses, journals, invoices and bills, payments and credits, bank reconciliation, financial reports, and optional basic purchasing and inventory. Reliable browser installation, updates, backups and failed-update recovery are also part of the release requirements. Regional tax certification and e-invoicing are outside this first release.

We would welcome:

- **An independent application-security reviewer** experienced in PHP/MySQL, web authentication and authorization, multi-company isolation, secure software updates and backup/recovery. Please include a relevant example of your work and your availability for a scoped review and a follow-up retest.
- **Accountants or experienced bookkeepers** to review supported accounting scenarios and challenge reconciliation, rounding, corrections, stock valuation and month-end results. Our existing accountant will also participate.
- **Two or three small businesses** willing to join a supervised pilot after the accounting and security reviews. We particularly need one service business and one business with simple stock/purchasing. Each pilot will run for at least 30 consecutive days and include a real month-end close, with an owner or bookkeeper available for weekly feedback.
- **People unfamiliar with the software** to try installation and everyday tasks and report where instructions or screens are confusing. Phone and keyboard users are welcome.

This is a volunteer collaboration. Reviewers should expect to document findings and revisit material fixes; pilot participants should keep their existing bookkeeping process available during evaluation. A preview is not yet a production-readiness claim. No purchase is required, and public acknowledgement is optional.

We will provide a test package, source access, synthetic scenarios and an agreed review scope. Initial review does not require customer data or production credentials.

Interested? Send a private message with your role, relevant experience, time zone, availability, and the area you would like to review. For a business pilot, add your business type, approximate monthly transaction volume, and whether you use inventory. Please do not send passwords, customer records or financial documents with your initial response.

Project: https://github.com/rmak78/phpledger

## What each participant is needed for

| Participant | Work and evidence needed | Planning allowance |
|---|---|---|
| Owner's accountant | Review opening/cutover reconciliation, AR/AP allocations and credits, corrections, configured tax rounding, weighted-average stock/returns, control accounts, reports and supported close/equity treatment. Record expected results, actual results, discrepancies, supported boundaries and a dated decision on the exact reviewed version. | Start with 2–3 focused sessions; reserve roughly 8–16 hours plus time to retest findings. Agree the actual scope and time with the reviewer. |
| Independent security reviewer | Review company/book isolation, server-side permissions, sessions/CSRF, installation takeover, operator authority, signed packages, upload/download validation, private backups, interrupted updates and the independent recovery interface. Provide reproducible findings, severity, remediation/retest status and explicit scope exclusions. | Reserve a scoped initial review, provisionally 20–40 hours plus retesting; the reviewer must estimate effort after seeing the application. This is not a promised audit duration or certification. |
| Owner's lawyer | Review the product's public claims, support/service wording, licensing and privacy materials within their expertise and jurisdiction; document questions and any specialist referral needed. | Agree a separate scope directly. This does not substitute for technical security or accounting review. |
| Core-only pilot | Use supported receipts/expenses, journals, AR/AP, bank reconciliation and reports; reconcile opening balances and complete the supported month-end workflow. Keep discrepancy and assistance logs. | At least 30 consecutive days, an actual month-end close and a short weekly review. |
| Basic stock/purchasing pilot | Exercise purchases, receipts, bills, returns, stock movements/valuation and control-account reconciliation alongside core bookkeeping, within the documented basic-stock limits. | Same minimum period and close; named owner/bookkeeper available throughout. |
| Optional third pilot | A second independent operator or different transaction-volume profile within the same supported scope. No new product family is introduced to accommodate it. | Same minimum period and close. |
| Unfamiliar installers/users | Install from the complete ZIP after hosting-panel preparation; complete assigned everyday tasks without developer guidance where possible. Record assistance, errors and completion. Include desktop/phone, keyboard and zoom observations. | Initial 45–90-minute observed sessions, with focused retests. |

## What engineering supplies

- A versioned package and checksum, a bounded supported-scope statement, installation/upgrade instructions, known limitations and the relevant source revision.
- Synthetic accounting cases with expected balances and source links. The accountant confirms the expected accounting treatment; passing automated tests does not supply that decision.
- An isolated security-review installation with company/operator roles, documented trust boundaries, update signing/recovery design and reproducible failure scenarios. Access details are shared privately with named reviewers.
- A findings register: reviewer, version/environment, scenario, expected/actual result, severity, evidence, owner, resolution and retest decision. Material unresolved findings block the affected release gate.
- Pilot setup assistance, approved opening balances, an agreed hosting/backup plan, weekly reviews and reconciliation evidence. Real-customer pilots start only after the required accounting and independent security review.

## Information the owner needs to provide now

1. Accountant's name or preferred identifier, relevant qualification/experience, available review dates and the accounting cases they will cover.
2. Lawyer's agreed scope and available dates; confirm separately whether an independent technical security reviewer has been identified.
3. Security review route: a named reviewer, a volunteer recruitment effort, or an agreed budget for a professional review if a qualified volunteer is unavailable. Do not substitute a general developer or lawyer without the required security experience.
4. Pilot candidates: business type, responsible bookkeeper, approximate monthly transaction count, currency, inventory use, proposed host and month-end date. Detailed customer data is not needed at recruitment.
5. Which public profile/contact will receive advert replies. The copy above uses private replies so it can be posted without publishing an email address.

## Scheduling and acceptance

Book review sessions during development, then reserve a retest after the installer and updater are stable. Start the pilot clock only after review blockers are resolved and the pilot version and opening balances are accepted. Reserve at least 30 consecutive pilot days with an actual month-end close, followed by the required exact-artifact RC acceptance period of at least 14 consecutive days. Material changes restart the affected acceptance period.

An uninterrupted November pilot with a November month-end close could support the December working target if the earlier gates are complete and RC acceptance then succeeds. This is a planning example, not a booked schedule. Missing reviewer availability, recovery qualification, pilot reconciliation or RC evidence moves the release date.

Do not enter a real person or business as confirmed until they accept a defined role and dates. No external message, advertisement, payment or production deployment is authorized merely by preparing this brief.
