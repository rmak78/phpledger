# Staged delivery: 0.1.5 through 0.2.1

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
- POS synthetic HTTP suite: 29 checks including explicit review/confirmation, forged totals, CSRF, insufficient tender, retained cart, exact journal effect and duplicate receipt identity.
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
