# Staged delivery: 0.1.5 through 0.2.1

## Current publication: combined 0.2.1-preview

The owner approved combining Releases B and C with the complete existing core into **0.2.1-preview**, published on 15 September 2026 UTC (16 September PKT). Native Codex HTTP and STDIO are verified; other clients remain explicitly partial or pending. All four multi-year businesses and the three illustrated guides shipped with the package, website and demo. See [publication](PREVIEW-0.2.1-PUBLICATION.json), [acceptance](PREVIEW-0.2.1-ACCEPTANCE.json) and [validation](PREVIEW-0.2.1-VALIDATION.md). The [current roadmap](../../ROADMAP.md) records the subsequent feature sequence, PHP 8.2 minimum/8.3 deployment and country-neutral direction. Entries below retain the earlier plans and evidence; they do not supersede the current release.

## Original staged plan and receipts

Approved implementation plan, 15 September 2026. This receipt separates implemented and verified behavior from pending release gates.

| Release | Scope | Gate |
|---|---|---|
| A, 0.1.5-preview | Contributor PR #65, POS fallback layout, website information/SEO | Accessibility/checkout, exact package installation, website/demo live proof |
| B, 0.2.0-preview | Scoped read API/MCP, OAuth/connections, server-side tables | Financial/access parity, restore/upgrade, all named clients tested |
| C, 0.2.1-preview | Four original 2024–2025 sample histories, open 2026 practice, walkthroughs | Reconciled fixtures, visitor isolation, observed scheduled reset |

Keep API/MCP reads → controlled commands → installer → optional AR/AP as the build order. No domain move or upgrade to the application hosted at llm.bixisoft.com is included.

## Release A local evidence

PR #65 at `54fb005bc959375e3a2c4970e0f1901fda0fb3be` was fetched and applied with its original contributor authorship. The maintainer restores the newline and repairs a discovered fallback layout issue: product buttons took the full card height, clipping the following quantity input. Flex layout gives both content and input room.

- `composer check`: 90 PHP files linted, PHPStan passed, sample validator passed, 121 tests / zero failures on PHP 8.5.10 and MySQL 8.4.
- POS sample HTTP suite: 29 checks including explicit review/confirmation, forged totals, CSRF, insufficient tender, retained cart, exact journal effect and duplicate receipt identity.
- Responsive browser receipt: `output/playwright/release-a/receipt.json`; desktop 1440, tablet 768, mobile 390; JavaScript on/off; product-specific names, all fallback inputs inside card bounds, keyboard continuation, read-only review and product/website layouts.
- Website build/check includes About, Privacy, demo-use terms, six matching visible/structured FAQs, sitemap/RSS and `max-image-preview:large`. Existing noindex is preserved.
- Local Nginx config test/reload passed. Permissions Policy denies unused camera, microphone, location, payment and USB access. HSTS is host-only and emitted only on HTTPS.
- Dependency audit: no known vulnerability advisories in the installed baseline. The pre-existing container carries stale Composer project metadata; its generic exact-pin warning does not override the repository's explicit dependency-pin policy.

The privacy notice was grounded in `regional_functions.php`, `security_functions.php`, `auth_functions.php`, `DEMO.md`, Apache/PHP/Nginx configuration and a read-only check of the actual hosting log destinations. Hosting log/backup retention is operator-managed, with no invented fixed deletion guarantee. No Google Drive document is required.

Publication and exact-package installation are pending until their receipts are appended below. The local checks do not establish accounting acceptance, WCAG conformance or observed participant success.

## Expanded integration gate

Native MCP acceptance must cover Codex, Claude, ChatGPT, n8n, OpenClaw, Nous Research Hermes Agent, Open WebUI and the actual client at llm.bixisoft.com. Record versions, explicit transport/authentication settings, tool discovery, four reports, pagination/source reads, browser parity, denied cross-company/writes, revocation/expiry and demo reset. OpenAPI fallback and protocol harnesses do not count as native-client acceptance. Any inaccessible client remains an open gate.

## Publication ledger

No live change is established by this document's initial local entry. Record the exact release/package hash, source commit, migration receipts, live routes and reset/client results after execution.

### Release A publication verified

- GitHub release: [v0.1.5-preview](https://github.com/phpledger/phpledger/releases/tag/v0.1.5-preview). PR #65 is merged; contributor authorship is preserved in Git history.
- Package and live app source: `ad6d618c5c9c9b64afc8315515f1a994a6e9a1f0`. Downloaded ZIP: 1,326,966 bytes; SHA-256 `2d8e60816f5c03a1d1e540beb48b4139b4e231c75c57b13acb3011e5eb23bd5b`, matching its downloaded checksum.
- Exact package fresh installation and actual 0.1.4-to-0.1.5 upgrade passed. Backup restoration passed: 27 table definitions/data checksums, 17,409 sample rows, 35 guards and 11 receipts.
- Live demo `core-0.1.5-preview-ad6d618c5c9c` changed only `pos.css` and `pos.php`; source-switch guards verified preserved schema, receipts, generation, session files, DB container/volume and app image. The first attempt encountered the scheduled reset before any cutover action and safely stopped; the post-reset retry passed.
- Website `website-redesign-20260915-140159` published at 14:02:57 UTC. All 108 files matched local/archive/host hashes. CSP, canonical redirects, private-path rejection and demo noindex passed. Host-only HSTS (`max-age=31536000`, no includeSubDomains/preload) and Permissions Policy verified over public HTTPS.
- README/master and Wiki commit `c381423` published with release information. RSS has four entries; sitemap has 15 public URLs. IndexNow returned HTTP 200 for nine priority URLs at 14:04 UTC. Acceptance by IndexNow is not proof of indexing.
- Live sample no-JavaScript POS checked at 1440/768/390; all six named quantity fields fit their cards. A 9.00 sample sale with 10.00 illustrative cash produced 1.00 change, a linked journal and account closing 884.00 (875.00 sample + 9.00 sale). No real payment was collected.
- Google/Bing property access, sitemap submission through owned consoles and observed indexing remain open. Computer Use inventory failed with a missing app-server executable, so no authenticated console was accessible. No owned project social handles are configured and none were invented or posted to.

Release A required no new migration or schema change. The authorized live application/website/GitHub/Wiki changes above were made; no raw secret was exposed and no Google Drive document was read. Professional accounting/security review and observed participant usability remain wider preview gates.

## Release B local candidate, 15 September 2026

The existing application now has eleven scoped read API/MCP operations, personal-token and OAuth connection management, Streamable HTTP and a standalone PHP STDIO bridge. It uses the existing users, explicit router, MeekroDB and financial services. `011_read_connections` adds seven tables; the local schema has 34 tables, 35 existing accounting guards and twelve receipts. No B source, migration, OAuth key or configuration has been published to production.

- `composer check`: **105 PHP files linted, zero failures; PHPStan zero errors; existing sample validation and eight invalid-fixture checks passed; 137 tests, zero failures** on PHP 8.5.10 / MySQL 8.4.9.
- Focused connection suite: 31 tests, zero failures (fifteen shared ledger tests and sixteen integration tests). Every advertised operation is exercised. Coverage includes exact decimal/browser report parity, canonical balances, scope/write denial, S256/exact redirect/resource, authentic JWT claim substitution, refresh rotation/replay/concurrent redemption, standard access/refresh revocation, client expiry/CIMD renewal and reused-ID demo generation denial.
- The actual standalone PHP bridge ran against the isolated application HTTP server and passed 2025-11-25 handshake/discovery/read, 2026-07-28 per-request read, unsupported SQL-tool denial and reconnect after revocation. This is bridge/protocol evidence, not Codex/Hermes application acceptance.
- Responsive browser harness: **43 checks, 15 route/width layouts, zero JavaScript errors**, plus consent checks at 1440/768/390. Connections displays a token once, real OAuth consent/loopback callback/token exchange reads a matching report, UI revocation returns a useful HTTP 401, and all four tables retain no-JavaScript views. Bank pages 25/50/100, search and source review work. The final rerun also verified the read-only private-volume mount after recreating only the local web service.
- Query profiling used EXPLAIN ANALYZE on all four list services before adding any financial index. It exposed repeated source reads: the general list dropped from 53 queries to 5, account movement reads from 39 to 9 by formatting selected rows and batching scoped source IDs. The [query receipt](INTEGRATION-QUERY-PROFILE.json) records the small sample workload and plans. No financial indexes were added; these single-run timings make no production capacity claim.
- Restricted demo smoke passed against a separate `phpledger_demo` in the disposable **db_test** service. API reads, MCP session creation/expiry and connection revocation work with SELECT/INSERT/UPDATE only. The existing real reset CLI replaced the generation and removed visitors twice. The **scheduled hourly reset observation remains open**; forced reset tests do not close it.
- Backup restoration passed for **34 table definitions/data checksums, 32,711 sample rows, 35 guards and twelve receipts**, with balanced journals and scoped source links. Only the generated restoration database was removed. Exact candidate-package fresh installation and 0.1.4 upgrade subsequently passed; see the package receipt below.
- Composer audit found no vulnerability advisories. Changed JavaScript and embedded n8n summary code passed `node --check`; example JSON/TOML parsing and disabled/no-credential workflow checks passed. Vendored DataTables 3.0.4 has its upstream license, explicit source URLs and SHA-256 records. Native n8n import/execution is still open.

Versioned recipes and the per-client matrix are in [Integrations](../../INTEGRATIONS.md). Codex CLI 0.154.0 is installed; no actual Codex connection has passed yet. A fresh public check of `llm.bixisoft.com/props` returned `b8790-be76dd0bb`, separately from Open WebUI. The user approved opening fresh Chrome windows; the official recovery helper opened one, but the existing browser connection still returned `failed to start codex app-server: The system cannot find the path specified. (os error 3)`. The prior windows were retained. Actual authenticated client and search-console UI gates remain open.

References: repository README/architecture/roadmap/design, official SDK/League source, OAuth RFCs, DataTables and the named client documentation. No Google Drive document was required/read. Raw secrets exposed: **no**. Local migrations/schema changed: **yes**. This candidate made public documentation/metadata/download reads, Docker dependency calls and local sample HTTP/DB calls; it made **no new production changes**. Release A's separately recorded publication remains live. No messages, real payments or advertising were sent.

### Release B exact package verification

Candidate source `1541e27bac5dc1ad61af8c779e9785256895faef` produced `phpledger-0.2.0-preview.zip` (2,999,107 bytes), SHA-256 `5f6c64aa6365a7c9b80386ccdccc89b1d5561edd70c38b20d53a37e591036293`. The clean source worktree and production-only dependencies were packaged together. This artifact is local and unpublished.

The extracted ZIP passed a fresh installation with twelve receipts and an upgrade from the actual 0.1.4 package. All eleven prior migration receipts and its trial balance were preserved; only `011_read_connections` applied. The upgraded database has 34 tables, 35 guards and twelve receipts. The standalone key setup created private key files outside the package; the scoped API trial balance matched 1,000.0000 debit and OpenAPI exposed eleven read operations. The [sanitized package receipt](INTEGRATION-PACKAGE-VALIDATION.json) records these checks. Test databases were isolated and removed after verification. Named-client acceptance and hosted OAuth/proxy validation remain open.
