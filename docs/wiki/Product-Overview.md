# A workspace for the business and its books

PHP Ledger aims to make the first useful accounting task straightforward while preserving the detail accountants need. An owner should understand what changed; an accountant should be able to inspect the source, accounts and journal behind that change.

## Who it is for

| Person | What the experience should make easier |
|---|---|
| Business owner | Record daily activity, understand cash and expenses, and see a clear next action. |
| Accountant or bookkeeper | Enter and review transactions efficiently, investigate balances, reconcile records and close periods. |
| Shop operator | Build a sale quickly, confirm it deliberately and find its receipt and accounting effect. |

## The first useful journey

1. Explore an isolated sample or create a business.
2. Enter the business name, base currency, accounting start date and fiscal year.
3. Review the accounts and open a statement to inspect its balances.
4. Save a receipt, expense or general journal as a draft and correct any errors.
5. Post it and inspect its balanced journal.
6. Find the report effect and return to the source transaction.

1.0.0 implements that core journey, including opening conversion and bank reconciliation. A business must not be treated as ready simply because its name and currency have been entered.

## Experience standards

Clear draft/posted states, recoverable errors, keyboard-friendly entry, sensible defaults and source links matter more than a dashboard full of decorative numbers. The design should work on practical mobile workflows while retaining efficient desktop tables. Financial figures use Inter with aligned numerals.

The initial goals are simple-company setup within five minutes and a first useful transaction/report outcome within ten minutes, after login. These are usability targets to measure with representative users, not results already achieved. Server installation and historical migration are measured separately.

## What comes next

The product follows a [[core-first module roadmap|Module-Roadmap]]: 1.0.0 ships the required core, AR/AP, optional Purchasing/Inventory, opening conversion, bank reconciliation, read API/MCP access, browser installation and signed automatic updates. See [[current package|First-Package]] for the full 1.0.0 scope. Industry research covers restaurants, clubs, pharmacies, traders, distributors, shops and workshops; sample scenarios and disabled [[tax candidates|Tax-Research]] are not installed industry modules.

[[Getting started|Getting-Started]] · [[Accounting and reports|Accounting-and-Reports]] · [[POS showcase|POS-Showcase]]
