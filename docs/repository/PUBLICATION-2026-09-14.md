# PHP Ledger publication — 14 September 2026

The owner explicitly requested replacement of the existing website, an updated demonstration, the new README/logo, GitHub Wiki publication, and the start of the next release sprint. These instructions superseded the earlier publication hold. This is a **development-preview launch**, not a stable accounting release or an installable package release.

## Published destinations

| Destination | Result |
|---|---|
| [Website](https://phpledger.com/) | Field Notes replacement, real illustrative workplace photography, actual synthetic product screens, walkthrough, partners and contact paths |
| [Demo](https://phpledger.com/demo/) | Isolated visitor books, receipts/expenses, reports, cash POS and ten selectable base currencies |
| [Repository](https://github.com/rmak78/phpledger) | Branded README, current product screens, feature boundaries and documentation links |
| [Wiki](https://github.com/rmak78/phpledger/wiki) | Ten pages, sidebar/footer, product and installation guidance, accounting/POS boundaries, regional decisions and complete future path |
| [Sprint 03](https://github.com/rmak78/phpledger/milestone/4) | First installable preview; five issues covering licence/provenance, packaging, Pakistan reports, POS experience and release acceptance |

## Repository evidence

- Main repository commit [`250b27d`](https://github.com/rmak78/phpledger/commit/250b27d) published the README, six image/badge assets and the twelve Wiki source files. The original application/history and default branch `master` were preserved. A separate clean checkout was used; the unfinished local foundation was not staged wholesale.
- Wiki commits `5454d1b` and `7d1dc84` created the Home page and remaining pages/navigation. Commit `9f5ac1a` updated the live-preview status and linked Sprint 03. Subsequent documentation commits may add publication evidence without changing the runtime.
- GitHub Settings visibly displayed the new 1280×640 sharing image. API read-back confirmed `usesCustomOpenGraphImage: true`. It uses the preferred logo and an actual synthetic development screenshot.
- The public README was inspected after push. The logo, heading, real screenshot, feature table, collapsible screenshot sections and development status rendered. Wiki Home showed ten pages, correct table links and sidebar/footer navigation.
- Sprint 03 is milestone 4. Issues [#55](https://github.com/rmak78/phpledger/issues/55)–[#59](https://github.com/rmak78/phpledger/issues/59) define acceptance and dependencies; no release date or team assignment was invented.

## Deployment and checks

The new website and application were assembled from an explicit 111-file source inventory. A private backup of the previous site, virtual-host configuration and isolated demo settings was taken before replacement. A new versioned release was built from locked Composer dependencies. The old site/release remain available for rollback.

Only the PHP Ledger virtual host was replaced. It now serves the standalone static website and proxies `/demo/` to the isolated application on loopback. HTTP redirects to HTTPS. ACME challenge handling was preserved. The demo database/container and network were retained; the web image and separately credentialed reset scheduler were updated. No unrelated hosted site, customer database or legacy accounting database was migrated.

- Nginx configuration validation and reload passed. One immediate probe during the worker transition returned 404; a fresh probe returned 200. Persistent website and demo checks passed afterward.
- Fresh external reads returned website HTTP 200 with title **PHP Ledger — A day's work. Clearer books.**, the Wiki links, and no previous enterprise-ready headline. `/demo/health` returned HTTP 200 with `working-accounting-preview`.
- Hosted synthetic HTTP checks passed **68 checks, zero failures**: restricted demo baseline, secure scoped cookies, CSRF and access restrictions, and the five added currency journeys through cash sale, balanced reporting, repeated confirmation and retained currency. A first label assertion used a wrongly encoded dash in an ignored test copy; correcting that test encoding resolved it without an application change.
- Additional hosted routing, headers and content checks passed **71 checks, zero failures**, including canonical redirects, preserved ACME handling, private-path rejection and the hashes of all reviewed website pages/assets.
- The isolated hosted schema contained **15 tables, nine triggers and five matching migration receipts**. All ten supported currencies were present. The web user and reset scheduler use distinct restricted credentials.
- Existing local foundation checks recorded **55 PHP files linted without failure, zero static-analysis errors and 56 passing integration tests**, plus sample validation and upgrade/restoration evidence. Those results are local evidence; the hosted checks above were run separately.
- Hosted PHP was **8.5.10**. All required production extensions were present; **39 deployed PHP files linted without errors**, and the dependency audit returned no advisories. Composer's ordinary validation passed with warnings for the unset project licence and intentional exact MeekroDB pin; strict validation returned 1 for those metadata warnings. No licence field was guessed to silence them.

Live browser QA found no blocking issue. Desktop, tablet, mobile and narrow layouts, walkthrough tab groups, keyboard image closing/focus restoration, the mobile menu, contact links and the exact published Wiki destinations passed. The README logo/screens fit the smaller tested layouts and the POS disclosure opened. The existing GitHub browser zoom was 90%; its measured CSS widths were 853 and 433 pixels, as recorded in the [live QA report](../design/website/qa/live-20260914/README.md). A normal reload cleared old site/demo content in the existing browser profile.

The lead independently entered a fresh PKR sample and followed **reports → balance sheet → cash account activity → posted expense**, confirming 875 in the bank, the 1,000 receipt and 125 expense, and the linked balanced entry. This created only disposable synthetic sample data.

The actual **18:00 UTC / 11:00 PM Pakistan** reset completed without manual triggering. The generation changed, all previous synthetic visitors/companies/documents/journals were cleared, and the next reset advanced to 19:00 UTC. The reset observer passed three checks: old-session expiry, a fresh GBP sample and restored 875 opening sample balance. The lead's pre-reset browser session also returned safely to the sample start screen. Health temporarily returned 503 during rebuilding and was ready by the 18:00:17 observer; the website stayed available. The same demo database container and unrelated host MySQL process remained running. No customer database was targeted.

The current demo uses a bounded global maintenance lock. One busy 503 was seen during simultaneous QA, outside the scheduled reset; a retry succeeded. The 68 scripted journeys passed, but they are not a concurrency/load certification. Improved availability under concurrent visitors remains release work.

## Contact update after launch

The owner subsequently instructed that the phone must not be published and the location should be only **Innovista Chenab**. Main-repository commit `1e516c1` and Wiki commit `3eb2309` removed the phone from current pages; the static website correction went live at 17:59:40 UTC. A fresh response confirmed no telephone link/number and no former office/floor details. Existing Git history was preserved. The newly requested linked partner logos are being verified against official company sources before their separate static publication.

## Release limits and change record

The modern source package is still unreleased; `0.1.0-preview` is the proposed first candidate. Project licence/provenance, independent accounting review, representative usability sessions, refined reports/POS and package installation/recovery acceptance remain open. The preview does not claim tax compliance, inventory/COGS, AR/AP, historical import, offline checkout or payment processing.

Changed content includes README, repository artwork, Wiki source/pages, sprint issue records, website documentation links/FAQ, and publication/status records. The live website, demo image/scheduler and PHP Ledger virtual host were changed. No new migration or schema definition was authored for this publication pass; the existing five-migration demo schema was verified and disposable demo data refreshed by the guarded reset process. No raw secrets were exposed.

References used: current repository code/docs, approved plans and brand assets, existing image/font provenance records, GitHub pages/API and current server checks. No Google Drive documents were read or edited during publication. Designer asset files had been read in the earlier asset review. External/live calls: **yes**. Live repository, Wiki, website and demo changed: **yes**. Messages, funding collection and purchases: **none**.
