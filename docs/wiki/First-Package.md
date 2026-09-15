# The first installable package

The [0.1.0-preview foundation evaluation package](https://github.com/rmak78/phpledger/releases/tag/v0.1.0-preview) is available. It is an installable checkpoint, not completion of [Sprint 03](https://github.com/rmak78/phpledger/milestone/4). The reporting, POS, accounting-review and usability gates below remain open for a supported pilot package; no date is promised.

Modern source is in `www/phpledger`; the historical application remains under `legacy/`. The owner selected [MIT](https://github.com/rmak78/phpledger/blob/master/LICENSE) for new project-owned code and documentation on 14 September 2026. [Separate dependency, asset and historical terms](https://github.com/rmak78/phpledger/blob/master/LICENSE-SCOPE.md) remain in force. Source development follows the [development guide](https://github.com/rmak78/phpledger/blob/master/docs/DEVELOPMENT.md).

## 1. Reviewable accounting and reports

Start with a narrowly defined Pakistan entity/profile, selected with a qualified accounting reviewer. Turn applicable guidance into a traceable rule register, account classifications and versioned report mappings. Recognition, measurement and period-end behavior must support the statements being shown.

**Acceptance:** a documented profile and its exclusions; reviewed mappings; an original two-period fixture reconciled through the actual services; source drilldowns; explicit missing-data states; and accounting review of the supported scope. A template alone does not establish compliance. Reports needing unimplemented subledger or inventory data must remain clearly unavailable or preliminary.

## 2. A deliberate, compact POS journey

Refine catalog navigation, cart editing, sale review and a separate cash-confirmation step. Preserve clear pending/posted states and fast keyboard/touch operation. Keep the package's sale scope explicit; this is not an automatic expansion into payment processing, inventory or tax.

**Acceptance:** representative users can complete and correct a sample sale without accidental checkout; keyboard edits cannot post it; errors retain the cart; repeated confirmation produces one receipt/journal; receipt totals and cash/change reconcile; desktop, tablet and mobile checks pass.

## 3. A package people can actually install and recover

Prepare a versioned archive and exact supported-environment guide, a first-admin installation journey, migration/upgrade instructions and backup restoration. Assemble and verify the approved MIT grant and all required third-party notices in the actual package; the licence decision does not replace artifact validation.

**Acceptance:** clean installation from the published artifact in a fresh supported environment; verified initial sign-in/business setup; upgrade of the supported prior schema without lost records; restored backup reconciliation; dependency/security checks; documented limits and known issues; and an actual download linked from [[Getting started|Getting-Started]].

## 4. Make the import cutover decision concrete

Assess whether a bounded starter import fits this package after the accounting and packaging gates. Its first deliverable is a reviewed field/template contract, cutover example and reconciliation rules. Implementation scope depends on the required AR/AP and opening-document services being ready.

**Acceptance if included:** preview and mapping before confirmation; row errors and duplicate checks; exact totals; explicit authorized confirmation; repeat-safe application; opening balances and unpaid documents reconcile without double counting. If those dependencies are incomplete, publish the decision and keep the importer unavailable rather than ship a misleading upload button.

## Release boundary

The foundation evaluation archive does not satisfy the supported-pilot gates above. Complete the agreed reporting and POS scope, record qualified accounting and observed usability review, then assess a supported pilot release. Each package has its own exact source revision, checksums, validation receipt and explicit limits.

Full ERP, broad country compliance and AI **Scan document** are not commitments for this sprint. Scanning remains a later roadmap item with no next-sprint promise.

[[Current status|Home]] · [[Full roadmap|Roadmap]] · [[Contribute|Contributing-and-Support]]
