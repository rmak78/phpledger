# AR/AP — Parties, Contacts, Vetting and CRM Linkage

_Approved by the owner, 15 September 2026. Implementation reference for the AR/AP milestone (decision register A5). Supersedes nothing; extends the AR/AP milestone with the party/contact master-data layer, an optional vetting workflow, and the A75 CRM connector contract._

**For implementers:** §2 is a prerequisite for both AR/AP and the FBR digital-invoicing client — build it first. §3 is optional per company and off by default. §5 describes an integration contract, not a module inside the core.

This document folds in the usable parts of a reviewed commercial PHP "CRM + Accounting + Billing" script. The parts deliberately excluded are listed in §4 and should not be reintroduced without validated demand (decision register A4).

---

## 1. Scope folded into AR/AP

None of this becomes a CRM module.

| Feature | Milestone | Notes |
|---|---|---|
| Party + contact master data | AR/AP, **first** | Prerequisite for AR/AP and FBR DI. See §2. |
| Invoice create/send/track, outstanding balances | AR | Core. |
| Recurring invoices | AR, after DI client | Each issue is a live IRN submission; queue with retry. |
| Payment reminders and confirmations | AR | Email plus WhatsApp-link handoff (no WhatsApp API dependency in core). |
| Credit limits and payment terms | AR | Enforcement rules in §3.4. |
| Vendor bills, due-date tracking, payment runs | AP | |
| Party notes, activity trail, tags, attachments | AR/AP | Annotations on the party record only. |
| Transactional email: SMTP config, templates, message log | AR/AP | Queue-driven, no daemon. Message log attaches to the document as delivery evidence. |
| Role-based access, scoped by company/book | Core | The buyer is an accountant holding many clients (A2). |
| Dashboard: equity/capital headline plus AR/AP aging | Core reporting | A3. |
| One-click database backup | Distribution (B2) | Streamed; written outside the web root. |
| Quick-entry invoicing UX, phone-width counter path | AR | B6. |
| Payment provider adapter | AR, after DI | Defaults: bank transfer/IBFT, Raast, JazzCash, Easypaisa, 1Link. Stripe/PayPal as export-segment adapters only. |
| Branding customisation (logo, colours, company identity) | Core | Removal of attribution and the source offer stays under the commercial licence, not a settings toggle (C1). |

**Rejected from the source material:** its data model. That script treats the invoice as the primary record with accounting derived from it — the Tier-3 trap described in the competitive landscape, where invoicing tools hit a wall at GL, trial balance and year-end. In PHPLedger the journal is primary and an invoice is a document that produces one. Also rejected: "tested with millions of random transactions" as a requirement (volume proves nothing about a double-entry system; test correctness under concurrent posting, period locking, rounding, FX and reversal semantics instead) and "optimised for performance" as a claim (our version is the measurable hosting floor: PHP 8.2, shared hosting, no Docker, page budget on 1 GB RAM).

---

## 2. Party and contact model

The central correction to the reviewed script: it models a *contact*. We model a **party** — the legal entity we transact with — with **contacts** (people) hanging off it.

A party carries role flags: customer, vendor, or both. There are never separate customer and vendor tables. In Pakistan the same party being both is routine, and splitting them makes netting, aging and statements wrong from day one.

**Why this is core and not CRM:** FBR digital invoicing requires the buyer's NTN or CNIC, registration type and province on every invoice. Withholding rates depend on the counterparty's filer status. Party data is a *posting input*, not a convenience. Bad party data means rejected IRNs and incorrect WHT deductions.

### 2.1 Party record

| Group | Fields |
|---|---|
| Identity | Legal name, trading name, entity type (sole trader / AOP / Pvt Ltd / public / NPO / government), Urdu name for RTL display |
| Tax | NTN, CNIC (sole traders and individuals), STRN, provincial registration(s) and authority (PRA / SRB / KPRA / BRA / ICT), registered vs unregistered status, province of supply |
| Tax behaviour | Filer status plus ATL check date, WHT applicability, exemption certificate reference and **expiry date**, default tax treatment |
| Financial | Default currency, payment terms, credit limit, AR and AP control account overrides, opening balance |
| Banking | Bank accounts (title, bank, IBAN/account number) under the change control in §3.5 |
| Addresses | Registered, billing and shipping held separately — province drives services tax jurisdiction |
| Relationship | Role flags, status (§3.1), assigned owner, tags, notes, attachments, activity trail |

### 2.2 Contact record

Person-level, belonging to a party: name, designation, phone numbers with a WhatsApp flag, email, language preference, role (billing / receiving / authorised signatory / owner), primary flag, notes. Optional photo — late, low priority, and file-upload surface area on shared hosting.

### 2.3 Data hygiene

- Deduplication on NTN, CNIC and phone at entry, with a merge tool.
- NTN and STRN format validation at input, not at posting time.
- Import from CSV and from the shapes accountants actually hold: Excel client lists and FBR return annexures.

---

## 3. Customer and vendor vetting

Vetting is **optional per company and off by default**. A sole trader entering a walk-in sale must not hit a gate; an accountant onboarding a Pvt Ltd's vendor list wants one. Same data model, one switch.

Budget this as a distinct task worth roughly 15–20% on top of the party module. Treated as a footnote, it gets built as a status dropdown and delivers nothing.

### 3.1 Status lifecycle

`Draft → Under review → Approved → On hold → Blacklisted`, plus `Archived`. Every transition records who, when and why, and is immutable in the audit trail.

### 3.2 Checks

Each check is a record carrying evidence, outcome, date, expiry and checker.

**Both sides**
- NTN / STRN / CNIC captured and format-valid
- ATL / filer status verified, with check date and re-check schedule
- Registration documents attached (incorporation, NTN certificate, partnership deed for an AOP)
- Duplicate check cleared

**Customer-specific**
- Credit limit set and approved; payment terms agreed
- Trade or bank reference (free text plus attachment)
- Prior aging history, once there is any

**Vendor-specific**
- Bank account title matches legal name
- WHT category determined; exemption certificate attached if claimed
- Related-party / conflict declaration
- Internal do-not-pay flag (manual list; no external screening service in core)

### 3.3 Verification providers

Pluggable, following the tax adapter pattern (B4):

1. **Manual** — the checker marks verified and attaches evidence. Always available, works offline.
2. **ATL file import** — FBR publishes the Active Taxpayer List; a periodic import refreshes filer status in bulk. This is the realistic path for self-hosted installs.
3. **API** — via a licensed integrator or hosted relay. Commercial/hosted tier, later.

_Verify before build: current ATL publication cadence and file format, and whether bulk NTN verification remains available without integrator status._

### 3.4 Enforcement

Configurable per company and per check: **off / warn / block**.

- Posting a sale to an unapproved or blacklisted customer
- Exceeding a credit limit, or invoicing a customer overdue beyond N days
- Posting a bill or payment to an unapproved vendor
- Transacting against an expired exemption certificate
- Missing NTN/CNIC when the document is destined for FBR DI

Blocks are overridable by an authorised role with a recorded reason — never a silent bypass.

### 3.5 Vendor bank change control

Changing a vendor's bank details requires a second approver. Old value, new value and approver are written to the audit trail.

Invoice-redirect fraud is the most common loss an SMB actually suffers, and no competitor in this tier handles it. It is cheap to build, easy to demonstrate, and the kind of control that gets an accountant to sign off.

### 3.6 Filer status is the piece to get right

If PHPLedger holds filer status per party and applies the correct withholding rate automatically, that is a locally-specific correctness feature no open-source competitor has, and it gives the ATL import a purpose beyond vetting. Treat it as a first-class posting input, not a label on a profile page.

---

## 4. Explicitly out of scope

Sales pipeline, leads, opportunities, forecasting, task scheduling, and general-purpose mail sending.

These are a different product with a different buyer — the same test applied to the website builder and payroll in A4. An accountant does not buy a pipeline; a salesperson does not buy a trial balance. They are reachable through the API/MCP layer by anyone who wants a real CRM (§5). Revisit only on validated demand.

---

## 5. A75 CRM linkage

Agency75 runs a PHP CRM (LeadOps Command Center, legacy BixiCRM) that is the system of record for prospect data and already enforces human approval for every field change. That safety contract and the vetting gate in §3 are the same idea, which is what makes this linkage safe: the sync is suggestion-based in both directions, never a mirror.

### 5.1 Principle: two systems of record, split by field

Neither system owns the party outright. Ownership is per field group, and the boundary is the transaction.

| Owned by A75 CRM | Owned by PHPLedger |
|---|---|
| Lead and prospect records, pipeline, opportunities | Party existence once it transacts |
| Activities, communications, enrichment evidence | NTN / CNIC / STRN, provincial registration |
| Company-contact graph, directory import provenance, tags | Tax profile, filer status, WHT category, exemption certificates |
| Photos, logos, social and web presence | Credit limit, payment terms, control accounts |
| Marketing consent | Vetting status, bank accounts, balances and aging |

A CRM company is a *prospect*. It becomes a ledger party only on conversion — first quote, first invoice, first bill. Nothing else crosses.

### 5.2 Direction of flow

**CRM → Ledger, on conversion only.** A converted company arrives as a **Draft** party and enters `Under review` (§3.1). It cannot post until vetted.

This matters concretely: the CRM holds roughly 15.8k contacts sourced from WhatsApp groups and trade-body directory imports, with large gaps in email, country and company linkage. That data is *enriched*, not *verified*. The vetting gate is exactly the filter that keeps it out of a statutory ledger. Mirroring the CRM into PHPLedger would destroy the property that makes PHPLedger worth an accountant's signature.

**Ledger → CRM, continuous, as suggestions.** The more valuable direction and the one usually skipped: current AR balance, days overdue, credit hold status, lifetime revenue, last invoice date and last payment date pushed onto the CRM company record. A salesperson who can see that a client is 90 days overdue before pitching an upsell is the whole argument for linking the two systems.

**Neither direction writes silently.** Ledger→CRM lands in the CRM's existing `field_suggestions` / review queue — the same path the enrichment worker uses, honouring the same no-auto-accept contract. CRM→Ledger lands in vetting. A sync job must never be the route by which a tax identity or a credit limit changes without approval, or §3 becomes decorative.

### 5.3 Identity and matching

A mapping table, never name matching:

```
party_external_refs (party_id, system, external_id, last_synced_at, payload_hash)
```

Match priority: external id → NTN/CNIC → verified email/phone → **manual review queue**. No auto-merge below the NTN tier. The CRM already has `entity_duplicate_reviews`; cross-system duplicates route there rather than growing a second review UI.

### 5.4 Transport

Over the public API in both directions. No shared database user and no cross-schema queries — the two systems may sit on different servers, and a self-hosted PHPLedger user will point the connector at *their* CRM, not Bixisoft's.

PHPLedger needs one new core capability for this: **outbound events** (`party.created`, `party.updated`, `invoice.issued`, `invoice.paid`, `party.overdue`) delivered by a cron-driven queue with retry and idempotency keys. No synchronous HTTP in the request path — the hosting floor is shared cPanel. That queue is worth building regardless; the desktop and Android clients will need it too.

Auth: scoped API tokens per integration, per company/book.

The connector is a **generic party-source contract** with A75 as the first implementation, so HubSpot, Zoho or a CSV bridge drop into the same slot. An open-source ledger that syncs only with the vendor's own CRM reads badly; a connector interface with a reference implementation reads as architecture.

### 5.5 Licensing boundary

The connector talks to the core over HTTP. A separate process exchanging JSON with an AGPL service is the clean side of the line; lifting PHPLedger code into BixiCRM is not, and would pull AGPL obligations onto the CRM.

If the connector is to be a declared commercial module later (C1), it must live in its own repository from the first commit. Retrofitting that separation is painful.

### 5.6 Why this helps the roadmap

- It removes the last argument for CRM features in core (§4): the answer to "where is my pipeline?" becomes a connector, not a module.
- It is the first real consumer of the API/MCP layer already in flight, which is how we find out whether that contract is any good.
- Agency75's own books running on PHPLedger with live CRM linkage is one of the five real installations in the 90-day KPI (C9), on a client we control.

---

## 6. Open items

- ATL publication cadence, file format, and whether bulk NTN verification is available without integrator status (§3.3).
- Whether the A75 connector is a declared commercial module — gates the repository decision in §5.5, which must be made before the first commit.
- Dimension model (project / cost centre / branch) must be settled before tags ship, so tags do not become a substitute for a missing chart of accounts.
