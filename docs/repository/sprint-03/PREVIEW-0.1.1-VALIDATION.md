# 0.1.1-preview artifact and publication validation

Validated 15 September 2026 (Asia/Karachi). This is an evaluation prerelease, not a supported production-retail or statutory-accounting release.

## Published artifacts

- [Release and downloads](https://github.com/rmak78/phpledger/releases/tag/v0.1.1-preview).
- Runtime source: `0258a844d83b0ee225b2fc24cabae35ee6bfc9dc`.
- ZIP: `phpledger-0.1.1-preview.zip`, **1,214,419 bytes**.
- SHA-256: `56bdaafd6cb0981d28f36e0a214397543fa1a66c61527878f6549d7dbb51eede`.
- 106 manifest entries plus the manifest; two independent builds produced identical bytes. Both public assets were downloaded again and matched. All package file hashes remained unchanged after acceptance.
- Fresh production Composer vendor from the unchanged lockfile: MeekroDB 3.1.5. Dependency advisory audit and PHP platform checks passed.

## Executed acceptance

| Check | Result |
|---|---|
| Actual unpacked archive integration | 67 tests, zero failures |
| Actual archive HTTP | 26 core + 29 POS + 14 recovery checks, zero failures |
| Fresh installation | Separate empty database: five pending migrations, five applied, current checksums, initial user created |
| 0.1.0-preview to 0.1.1-preview | Old archive installed and passed 26 HTTP checks before replacement; new archive preflight/replay left all 15 table checksums unchanged |
| Backup restoration | 15 definitions/data checksums, 1,066 rows, nine triggers, five migration receipts, scoped source links and balanced journals verified |
| Package builder guards | Five tests passed |
| Syntax/static/CI | 59 PHP files, JavaScript and Python syntax passed; PHPStan no errors; [source CI](https://github.com/rmak78/phpledger/actions/runs/34896577861) passed |
| Browser | Product click/add, cart +/-/remove, separate cash review, recovery, keyboard, reduced motion, no-JavaScript fallback, desktop/tablet/mobile and 720px reflow checked |
| Print preview | Receipt PDF rendered and inspected: 14.75 total, 20.00 cash, 5.25 change; complete rows |

Package acceptance used its unpacked read-only files, production vendor and a separate nonshipping harness in the isolated `phpledger-release-011` Compose project. Source development, live demo and customer databases were not used for package tests. Recovery HTTP testing injected a lost-response state only into its own synthetic session; this is not a real network interruption test.

## Hosted verification

- Demo source release: `pos-0.1.1-preview-0258a844d83b`, matching the packaged runtime commit.
- Protected database/environment/session backups retained. The demo database container and volume were preserved; no live migrations ran. The exact existing PHP image was reused after checking unchanged runtime/dependency inputs.
- A pre-cutover SGD visitor retained the same company/book and session. A 4.50 sale with 10.00 cash produced 5.50 change; duplicate confirmation returned the same receipt.
- **78 public HTTP checks passed**, including demo entry, server restrictions, reports, MYR/BDT/LKR/NPR/SGD selections, staged cash checkout and duplicate behavior.
- Secure scoped cookie configuration, restricted database grants, five matching migrations, nine immutable/demo triggers and the separately credentialed hourly scheduler were verified. The next reset was scheduled for 22:00 UTC; no forced reset was performed for deployment.
- Website release `website-20260914-211150` verified at `2026-09-14T21:11:58.874432+00:00`. Its exact homepage hash matched and links 0.1.1-preview. Nginx configuration test/reload passed; the demo proxy stayed unchanged.
- Full postal address remains **Innovista Chenab, Arcade Plaza, Sector C, DHA Multan, Punjab 60000, Pakistan**; LinkedIn and the supporting-company section remain present. No public phone link was introduced.

## Limits and change record

Qualified accounting review, representative cashier sessions, native browser 200% zoom, physical receipt/till hardware and real network-failure simulation remain open. Native zoom shortcuts had no effect in headless Chrome; reflow is reported separately. A recovery state is session-bound: if the session expires, inspect posted transactions before starting a replacement sale. No inventory/COGS, tax, actual payment capture, AR/AP, historical import, offline or country-compliance capability is added.

The source commit changes POS routes/services/templates/CSS/JavaScript, regression tests, README screenshot, release/operator docs, Wiki drafts and local-artifact ignore rules. A later documentation-only commit records publication and updates the public website download link; it does not change packaged runtime bytes. Historical validation receipts are retained.

References: local AGENTS, README, architecture/roadmap, POS, release contract and deployment records; Playwright skill and actual hosted configuration. No Google Drive documents read. New migration/schema files: **no**. Live schema changes: **no**. Raw secrets exposed: **no**. External calls: **yes**, GitHub, dependency sources and authorized hosting/public-demo checks. Live changes: **yes**, repository/release, demo, static website and Wiki publication. Local acceptance created only isolated disposable schemas.
