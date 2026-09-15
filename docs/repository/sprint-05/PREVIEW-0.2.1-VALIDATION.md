# 0.2.1-preview validation

The owner approved a combined read-integration and richer-demo preview. The full existing accounting core remains included: server-side company/book permissions, accounts, receipt/expense/general-journal drafts, balanced posting, linked reversals, opening cutover with an unpaid-document register, period controls, bank CSV reconciliation, reports, running balances and CSV exports.

## Completed local checks

- PHP 8.2.33 and PHP 8.3.33: 142 tests each, zero failures; 109 PHP files linted; PHPStan clean.
- Four pinned packs: 74 sources and 36 exact monthly checkpoints each; three editable practice drafts, closed 2024–2025 and open 2026.
- Package-builder checks: six tests passed. Explicit inventory includes fixtures, guide assets, migrations and versioned integration recipes.
- Existing local browser and observed scheduled-reset receipts are retained in this directory. They are local evidence, not live-release evidence.

## Publication gates

Exact ZIP installation/upgrade checks, restoration, native Codex HTTP/STDIO, deployed OAuth/browser checks, public download hashes and a live scheduled-reset observation are being completed. Publication remains pending until the release receipt records those results.

Untested Claude, ChatGPT, n8n, OpenClaw, Hermes Agent, Open WebUI and the actual `llm.bixisoft.com` client remain pending individually. The owner-approved verified-client preview does not equate protocol support or an OpenAPI fallback with application compatibility.

Migrations: `011_read_connections` adds seven tables; `012_demo_history_periods` replaces two guard triggers. The complete schema has 34 tables, 35 guards and 13 migration receipts. Stop web traffic and the scheduler during trigger DDL; rollback requires the matching source/database/configuration backup.

Accounting review and observed real-business usability remain separate open gates. No customer data or raw credentials belong in these receipts.
