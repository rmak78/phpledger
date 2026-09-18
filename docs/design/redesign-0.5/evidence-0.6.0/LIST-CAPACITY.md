# Local list capacity evidence

Run on the isolated Compose MySQL 8.4 test database, 18 September 2026.
All records are synthetic. The normal source services and central posting funnel
created 5,000 receipt drafts and 5,000 posted general journals (10,000 journal
lines). No historical dumps or direct posted-row inserts were used.

`tools/verify-list-capacity.php` records first, last and searched page calls, their
bounded SELECT timings and EXPLAIN JSON. Each page returns at most 25 records.
Account closing balance remains exactly USD 5,000.0000 under display filtering.

| List | Before: first / last page | After: first / last / search |
| --- | --- | --- |
| Receipts and expenses | 40,519.94 / 42,865.42 ms | 40.06 / 42.77 / 45.25 ms |
| General journals | 58.82 / 56.25 ms | 79.50 / 110.42 / 114.34 ms |
| Account statement | 70.14 / 50.47 ms | 59.35 / 63.76 / 92.95 ms |
| Bank reconciliation | 3.90 / 3.82 ms | 3.03 / 3.18 / 3.50 ms |

The receipt view joins its enum kind to the posting identity's ASCII source type.
Before migration 031, the optimizer used only company/book for that join, with
an estimated 5,000 identity rows examined for each of 5,000 sources. The new
`ix_posting_source_lookup` puts source ID before the type comparison; the same
join now uses an estimated one row. Existing uniqueness and immutable history
are unchanged. No forced index hint or financial result cache was introduced.

The source scans are indexed by book/statement; journal, revision and match
joins use their existing keys. The account statement intentionally materializes
its scoped running-balance window before display sorting, search and pagination.
Its derived-table scan does not scan other books or redefine balances.

Reconciliation preserves the existing 500-row maximum per statement. Its 5,000
rows are spread across ten statements on separate bank accounts; measured pages
are within one 500-row statement. This does not claim a supported 5,000-row single
statement. The final searched cases return 111 receipt/journal matches, one exact
journal-number account match, and 11 bank matches. The earlier account/bank search
cases in the baseline JSON were empty; compare their first/last cases instead.

These are individual local measurements, not production latency or a concurrency
benchmark. Raw plans and timings are in `list-capacity-before.json` and
`list-capacity-after.json`. The fixture remains only in the disposable test DB.

Fresh install passed with 32 migration files and a balanced central posting.
The existing 0.5 verifier passed preservation of posted journal/accounts/setup,
full-read connection defaults and migration replay. Complete historical AR/AP,
inventory and currency upgrade verification is a separate pending release gate.
