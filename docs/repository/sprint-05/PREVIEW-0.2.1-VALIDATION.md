# 0.2.1-preview validation

The combined preview includes the full existing accounting core, scoped read API/MCP and four reconciled businesses with reporting guides. PHP 8.2 is the minimum, PHP 8.3 is recommended and the hosted demo runs PHP 8.3.33. The product is country-neutral; Pakistan FBR is one planned regional connector.

## Final package and source

- Source: `a8ee38a37616cc245102404bd47b6afea9373a23`.
- ZIP: **3,054,692 bytes**; SHA-256 `65eca3c57b084e185affb93a4167fa36cc141e94d5fd6f9e6c1243844ee696d1`.
- The exact ZIP passed fresh installation and upgrades from 0.1.4, 0.1.5 and 0.1.6 on PHP 8.2.33 and 8.3.33. Existing totals and prior migration receipts were preserved.
- [Final-source CI](https://github.com/phpledger/phpledger/actions/runs/35009834133): 142 tests per PHP 8.2.33, 8.3.33 and 8.4.25, zero failures. PHP lint, static analysis, dependency audit and backup restoration passed on all three versions.

## Accounting and samples

Company/book permissions, chart management, receipt/expense/general-journal drafts, balanced posting, linked reversals, opening cutover with reconciled unpaid-document evidence, period controls, bank CSV reconciliation, reports, running balances and CSV exports remain included.

The four versioned packs contain 74 sources and 36 exact monthly checkpoints each, with three editable drafts, closed 2024–2025 and open 2026. Live browser checks covered all four packs at 1440, 768 and 390 pixels, visitor isolation, source denial and a no-JavaScript guide. The public website has 23 checked pages, zero errors/warnings, and three illustrated reporting guides.

## Live connections and reset

Codex CLI 0.154.0 completed remote HTTP and STDIO discovery, explicit scope selection, all four reports, exact browser/API parity, pagination and source lookup. Cross-company requests were denied. Revocation, hourly expiry/reset and fresh-generation reads passed. Two-user browser OAuth consent, scope isolation, refresh rotation and replay rejection also passed. Public discovery aliases, CORS preflight, denied origins, authentication challenges, unsupported writes and OpenAPI were verified.

The actual `llm.bixisoft.com` llama.cpp UI, build `b8790-be76dd0bb`, discovered 11 tools and read the reports, pagination and source details. Scope denial and expired credentials were observed. Its model incorrectly labelled the expected denial as a failed check. Dedicated revocation and exhaustive per-field parity remain pending for that client; Claude, ChatGPT, n8n, OpenClaw, Hermes Agent and Open WebUI remain individually unverified. See [the client matrix](../../INTEGRATIONS.md).

The **19:00 UTC scheduled reset completed at 19:00:13 on 15 September 2026**. The generation changed, old native credentials were denied, the old browser returned to sample entry, and fresh browser/native connections worked. The initial state collector encountered the maintenance lock at the boundary; the immediate follow-up state, scheduler receipt and independent native expiry checks establish the transition. No forced reset was used.

## Rollout and recovery

Migration 011 adds seven connection tables; 012 replaces two demo-period guards. The final schema has 34 tables, 35 guards and 13 receipts. Web and scheduler were stopped during DDL. The actual frozen server backup was restored to an isolated verification database before cutover. A first deployment-command failure restored the matching database/source/configuration before reopening; the corrected command then passed. Final transport fixes used the same tested PHP image and unchanged migrations with a new immutable source mount.

Native testing found and corrected two integration defects: the bridge had converted empty JSON objects into arrays, and a busy bootstrap lost approved CORS headers. Regression coverage now preserves capability objects, retains retry headers and checks bounded demo lock admission.

Full machine-readable results are in [the acceptance receipt](PREVIEW-0.2.1-ACCEPTANCE.json). The [publication receipt](PREVIEW-0.2.1-PUBLICATION.json) records the published GitHub release, verified public download, all 117 website files, Wiki commit and live migration receipts. The release was published at 19:05 UTC on 15 September 2026 (00:05 PKT on 16 September). IndexNow accepted nine updated URLs; this does not establish indexing. Financial commands, full AR/AP, payroll, inventory/COGS and tax connectors remain future work. Independent accounting review and real-business usability remain open gates.

Migrations: yes. Schema changed: yes. Raw secrets exposed: no. External/live calls: yes. Live demo changed: yes. No Google Drive documents were required. No external messages or payments were sent.
