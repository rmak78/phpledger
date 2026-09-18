# Discovery workbook

No user interviews, accounting sign-offs, or usability outcomes are recorded yet. This document supplies a repeatable research plan and decision record; it does not invent participants, commitments, or results.

## Research sessions

Recruit SME owners separately from accountants/bookkeepers; aim for five initial participants per group when testing the core journeys. Start with two accounting reviewers and three prospective pilot organizations as validation targets, not claimed recruits. Record consent and task observations without copying customer financial data into the repository.

Ask participants to describe a recent bookkeeping day, their current tool and export formats, setup difficulties, the first report they need, common corrections, bank reconciliation, and how an accountant reviews the work. Ask what they would need to move a business mid-year and which records must be available on day one.

Use synthetic tasks: create a simple company; choose an account template; record an expense/receipt; find its report effect; correct a mistaken draft; reverse a posted entry; explain an import error; compare opening AR/AP to unpaid documents. Begin each task without coaching. Record time, success, confusion, help needed, and whether the person can explain the outcome.

For design comparison, use the same content and tasks across all candidates. Counterbalance presentation order and ask participants to explain choices using concrete work, not just color preference. Record the owner's selection separately from observed task performance.

## Accounting acceptance examples

Amounts below are synthetic examples in one base currency; no tax treatment is assumed.

| Scenario | Expected accounting outcome |
|---|---|
| Owner contributes 1,000 into bank | Bank debit 1,000; equity credit 1,000; equal trial-balance totals |
| Pay office expense 125 from bank | Expense debit 125; bank credit 125; bank balance now 875 |
| Reverse that expense in an open period | Linked reversal debits bank 125 and credits expense 125; original remains readable |
| Attempt an unbalanced journal | Reject the entire submission; no partial journal or source record |
| Resubmit the same source/key/payload | One durable journal reference; no duplicate economic effect |
| Reuse a duplicate key with changed amount | Explicit conflict; original journal unchanged |
| Force a failure after header creation | Transaction rolls back; report totals unchanged |
| Post while the period closes | One serialized result; no successful post into a closed period |
| Use another company's account or book | Deny on the server; no access or write across company boundaries |
| Carry AR 500 into a cutover with unpaid invoices 300 and 200 | AR opening total remains 500, not 1,000; invoice detail reconciles to control balance |
| Import bank row twice or encounter two plausible matches | Flag duplicate/ambiguity; require an explicit valid choice; no automatic reconciliation |

Reviewers must extend these examples to fiscal-year boundaries, document dates around cutover, permitted rounding, account types, report presentation, closing behavior, credits/partial payments, and correction policy before the associated feature ships.

## Decisions requiring evidence

| Decision | Owner/evidence | Current default or boundary |
|---|---|---|
| First pilot segment and business template | Product owner plus user research | Country-neutral SME accounting; no industry-specific default asserted |
| Visual direction | Product owner plus observed tasks | Three candidates pending selection |
| Company versus multiple reporting books | Product owner plus accounting reviewers | One complete book per company; no alternative-book flags yet |
| Multi-book differences and reconciliation | Accounting reviewers; worked legitimate reporting examples | Explainable, auditable adjustments; no opaque overwrites |
| Historical source formats and cutover policy | Pilot exports and accounting review | CSV/XLSX starter imports; detailed history later |
| Currency precision and accounting/report rules | Accounting reviewers and supported currencies | Exact amounts; validate the supported precision explicitly |
| Project license and contributor provenance | Rights-holder/license review | No license chosen or retroactively applied by automation |
| Funding recipient and platform | Product owner plus eligibility review | Jurisdiction undecided; no payment collection |
| Supported hosting and service commitments | Technical install tests plus support capacity | Customer-owned hosting; document only tested compatibility |
| Website implementation and HTTPS status | Current source/hosting inspection when authorized | Local preparation; live relaunch remains separate |

## Record each completed session

Keep a dated, de-identified record containing participant role, tasks attempted, timing/outcome, observed issues, changes proposed, and reviewer decision. Record failed tasks as failures. Maintain findings in this document or linked work items rather than creating a separate research system. Do not claim the five-minute/ten-minute targets passed without timed observations.
