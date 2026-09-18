# Website design and publication QA

## 1.0.0 product site rebuild — 18 September 2026

Status: implemented and checked locally; not published. The owner publishes; the `/support` 301 rules in `docker/website.conf` ship with it. Application source, release archives and the demo were not changed.

| Check | Result |
|---|---|
| `node www/website/build.mjs --check` | 70 pages, zero errors, zero warnings |
| Rendered review at 1440 px | 19 routes (home, product, point of sale, download, pricing, community, roadmap, news index and 1.0.0 article, about, credits, the learn, guides, compare, for and self-hosting hubs, 404, the Akaunting comparison and the trial-balance lesson): no console errors, no failed requests, no horizontal overflow |
| Rendered review at 390 px | Home, product, download, pricing, point of sale, roadmap, lesson and community: no console errors, no overflow |
| Title and description lengths | All 70 pages: title 60 characters or fewer, description 50–160 |
| Wording | "synthetic" no longer appears in page sources (203 replacements across 56 pages); captions and labels say "fictional sample business" or "sample data" |
| Nginx configuration | `docker/website.conf` with the `/support` and `/support/` 301s passed `nginx -t` in a throwaway `nginx:alpine` container |
| Interactive checks (puppeteer, 20 checks) | Mobile menu open/Escape/focus return and no overflow; capture dialog from the keyboard, title and note, Escape and focus return; skip link; enquiry form validation, copy, error clearing and `#enquiry-pilot` preselect; reduced motion disables smooth scrolling; product page requests stay on the local origin; no console errors |
| Contact details | No email address, `mailto:` link or personal LinkedIn profile anywhere in the output (owner instruction); contact runs through GitHub Discussions and the security policy |

Defects found in the renders and fixed before this record: roadmap phase labels rendered as cards over their headings; the release card's definition list inherited article `dt` margins; the second enquiry-form column inherited the stacked-field margin; the draft preview rendered in monospace; the generated contents list landed inside "Key takeaways" callouts and outside the container on `/credits/`; the `.page-404` layout rule leaked into the header and footer; heading links were underlined; the hero phone capture was too tall on phones (hidden under 640 px); the point-of-sale phone capture is now cropped with a fade; FAQ blocks on about, news and roadmap were restructured to the shared accordion markup; home pillars and feature blocks became `h3` under their group headings so every H2 section carries 50 words or more.

Not done in this pass: interactive browser checks of the menu, capture dialog and enquiry form after the restyle (the JavaScript is unchanged); keyboard and reduced-motion re-verification; a live seoscore.tools run, which needs the published site. Full-page renders of `/product/` exceed Chrome's 16,384-pixel capture limit and show a stitched repeat in the screenshot only.
## Local 0.6.0 UI information and screenshot refresh — 18 September 2026

Status: implemented and checked locally; not published. Application source, versioned application/media archives, deployment settings and historical release articles were not changed.

Updated source pages: Home, Product, Point of sale, Download, About, Roadmap, News, the new 0.6.0 release article, daily cash, monthly closing and quarterly/yearly review. Shared `site.json` capabilities, limitations, product screenshot and default social artwork were updated; generated HTML, RSS, sitemap and AI discovery documents were rebuilt from source.

The screenshot library contains 18 fresh captures from the existing local 0.6.0 runtime (`127.0.0.1:18219`) and 36 WebP derivatives. Fresh Willow Corner Shop and Cedar Studio sample companies were provisioned through the application's sample chooser in the isolated local test runtime. One synthetic cash sale of USD 5.75, with USD 10.00 tender and USD 4.25 change, supplied the POS cart/review/receipt sequence. No real customer records or external providers were used. This does create local synthetic records; it does not change schema or production.

`public/assets/screens/v0.6.0-preview/manifest.json` records sample names, routes, captions, alt text, dimensions and SHA-256 hashes. Captures were visually inspected. Their pixels were only resized and encoded for the website; no generated interface or substituted accounting figures were used. Historical screenshot paths remain unchanged. Shared social artwork uses the new Home capture; dated article artwork remains historical.

| Check | Result |
|---|---|
| `node www/website/build.mjs --check` | 67 HTML pages, zero errors, zero warnings |
| `node www/website/tools/aeo-geo-audit.mjs` | Passed, exit 0 |
| Main browser matrix | Nine routes at 1440, 768, 390 and 320 pixels: 36 HTTP/layout/image checks, no errors, overflow or broken images |
| Follow-up browser checks | About, Roadmap and the final release article at the same four widths: 12 additional HTTP/layout checks passed |
| Screenshot dialogs | 32 opening/image-target/Escape/focus-return checks passed |
| Navigation and fallback | Mobile menu/Escape/focus passed; product content and download link passed without JavaScript |
| Network boundary | No external page requests observed in the main browser matrix |
| Guide figures | Cedar daily opening 40,825 + receipt 2,700 = closing 43,525; December income 3,000 − expenses 2,990 = profit 10; closing assets 49,015 = liabilities 4,150 + equity 44,865 verified in the application |
| Quarter/year examples | Q1 2,580; Q2 2,580; Q3 2,535; Q4 1,730; 2025 total 9,425; 2024 total 10,440 verified against fresh sample reports |
| `git diff --check` | Passed |

Browser scripts, full-page captures and reports are under `output/playwright/website-060-*`; the main structured receipt is `docs/design/website/qa/website-060-refresh-browser.json`. The existing local website service is available at `http://127.0.0.1:18201/`. The local static server's `/demo/` path does not proxy the separate application.

No PHP or application JavaScript changed, so financial/access suites, fresh-install/upgrade tests and PHP lint were not rerun. Current screenshot and website checks do not establish application accessibility certification, mobile readiness, accounting sign-off or production readiness. No Google Drive references were required. No migrations/schema changes, raw-secret exposure, external/live calls, push, deployment or campaign occurred. Publication requires a separate explicit instruction.

## Completed content library and publication — 16 September 2026

The owner authorized takeover, completion, review and publication of the shared website work. The earlier 53-page stub pass is superseded by this completed release: 63 source pages, 64 HTML outputs, 62 sitemap URLs, seven release-feed items and 162 static files. The [content register](../../docs/design/website/CONTENT-REGISTER.md) records each page and the remaining professional-review boundaries.

| Check | Observed result |
|---|---|
| Build/static check | 64 HTML pages; zero errors and zero warnings |
| Syntax and content guard | Build, checker and AEO audit JavaScript passed; main-content metrics exclude shared footer/navigation |
| Generator regression checks | 21 isolated cases passed: two positive controls and 19 expected invalid-metadata rejections |
| Browser layout | 62 indexable routes × 1440, 768, 390 and 320 pixels = 248 checks; images loaded and no script errors |
| Public browser | Nine routes at four widths: 36 checks passed after publication |
| Final corrections | 20 targeted route/width checks passed after correcting the home grid span and punctuation encoding; no mojibake markers remain in page sources |
| Interactions | 10 checks passed: mobile menu/Escape, FAQ, capture dialog/Escape, support validation, local draft, no HTTP submission and no-JavaScript fallback |
| Nginx | Local syntax/reload and hosted syntax/reload passed; public archive bytes, HTTPS manifest MIME, discovery routes and existing demo behavior verified |
| Publication | `website-redesign-20260915-211542`, 02:17 PKT on 16 September; all 162 static files matched over public HTTP(S), with private prior-site/config backup |

Screenshots were inspected separately from automated overflow checks. Visual inspection caught the homepage scope table occupying one grid column and corrupted punctuation in four pages; both were fixed before publication. Full-page screenshots can be downscaled by viewers and do not establish pixel-level typography or observed usability. The OS email-send path, actual business users, exact browser zoom, screen-reader sessions, Core Web Vitals, qualified accounting/legal acceptance and installed competitor benchmarks were not tested. No email, payment, search submission, social account or campaign was created.

The site remains an independent static release; its publication preserved demo container identity, proxy/OAuth locations and demo noindex. The separate 0.3.0 app release did run migrations, as recorded in [the combined release evidence](../../docs/repository/sprint-05/PREVIEW-0.3.0-PUBLICATION.json). See [the website receipt](../../docs/design/website/qa/live-20260916-content-publication.json).

## Live publication: 15 September 2026

**Published successfully at 08:08 UTC (13:08 PKT)** to [phpledger.com](https://phpledger.com/), static release `website-redesign-20260915-080700`. This current receipt supersedes the local-only publication status in the historical sections below.

The owner's selected **A, Workbench, with B's photo treatment** is live, with the exact homepage H1 **Double-entry accounting that runs on your own PHP and MySQL hosting**. Existing source/layout and licensed images were retained. Before publishing, current package copy was reconciled to verified public **0.1.2-preview**, five download and three roadmap FAQs were added, and a malformed support-select option was repaired so Installation assistance is selectable. Historical 0.1.0 captures and news retain their provenance.

### Fresh evidence

| Check | Executed result |
|---|---|
| Website build and static checker | 10 generated pages plus historical credits redirect stub; **zero errors and warnings**. Metadata, internal links/assets, structured data and byte budgets passed. |
| Source syntax | Node syntax passed for build/check tools and source/generated site JavaScript. Publication helper Python syntax passed. No PHP file changed. |
| Candidate server configuration | Nginx syntax passed; **16 local route/hash/header/redirect checks passed**, zero failures. |
| Publication integrity | All **101 static files** passed archive and extracted-host hash verification. Public file bytes/status matched; credits.html correctly served its 301 instead of stub bytes. |
| Live routes and responsive layout | `/`, `/product/`, `/point-of-sale/`, `/download/`, `/support/`, `/roadmap/`, `/news/`, `/news/0-1-0-preview/`, `/credits/`: **36 checks** at actual **1440, 768, 390 and 320 CSS pixels**, no horizontal overflow or broken loaded images. |
| Browser console | No errors or warnings in the live route checks. |
| Visual inspection | Fresh live desktop/mobile homepage, mobile download and desktop photo/product-story views inspected. A light product opening and full-width photographs are present. |
| Interactions, local browser | Menu open/Escape/focus, enlarged image open/Escape/opener focus, pilot preselection, required-field errors/focus, invalid email/retained values, installation topic selection and FAQ expansion passed. No enquiry was sent. |
| HTTP behavior | HTTP/www canonical redirects, `/download` directory redirect, `/index.html`, `/credits.html`, crawler files, RSS, custom 404 and private-path rejection passed. Static HTML has CSP and short cache policy; versioned resources have their reviewed cache policy. |
| Existing demo | `/demo/` and `/demo/health` remained HTTP 200. Container identities and prior response headers were preserved. Added the planned `X-Robots-Tag: noindex, nofollow`; no demo runtime/database/reset action. |
| Published download | Independently downloaded 0.1.2 ZIP: **1,276,132 bytes**; SHA-256 **8bce5df6be15ad261e13719be2bc199b49011748ec073843d76b087b47b2e495** matches SHA file and GitHub digest. Read its INSTALL, UPGRADE and RELEASE-NOTES. |

Machine-readable evidence: [publication and browser receipt](../../docs/design/website/qa/live-20260915-publication.json). Private preparation and local candidate checks are retained in `.cache/website-redesign-publication/website-redesign-20260915-080700/`. The earlier website/configuration backup is `/var/www/phpledger/data/backups/website-redesign-20260915-080700`; previous static release `website-20260915-001433` remains available. The ignored existing-hosting helper supports `python .cache/publish-website-redesign.py --rollback website-redesign-20260915-080700`, with a current-configuration hash guard.

### Files and boundaries

Current source edits: `src/site.json`; home, product, download, support, roadmap, news and historical-news page sources; `src/static/llms.txt`; corresponding generated HTML/llms and static-check receipt. Published output includes the complete existing redesign's 101 files. Documentation updates: supplied Claude plan, website design/content records, website README/QA, repository roadmap, demo operations and publication receipt. The ignored publication helper is local operator tooling. No commit or push was made; unrelated dirty accounting files were preserved.

References read: supplied Claude plan and A/B canvas through the browser; repository AGENTS/README/architecture/roadmap; design, content, photo provenance and deployment records; current GitHub release and packaged guides. **Google Drive documents: none.**

**Migrations: no. Schema changed: no. Raw secrets exposed: no. External/live calls: yes (reference/release reads, authorized static publication and verification). Live/production changed: yes (static website and its Nginx configuration, including the demo noindex header). Application/database changed: no.**

The OS mail application/send path, real clipboard write, exact 200% browser zoom, screen-reader sessions, performance/Core Web Vitals, participant usability and independent accounting/accessibility review were not tested. Accounting/fresh-install regressions were not rerun because this release contains only the static website and routing/header changes. Search-console verification/submissions, IndexNow, social channels/campaign and further page-specific social artwork remain pending; publication does not establish search indexing or marketing results.

## Historical local redesign completion: 15 September 2026

This section supersedes the older website baseline below. The existing Claude changes were preserved and completed on `website-redesign`; all work was local.

## Completed scope

- Added product, POS, download, support, roadmap, news and versioned 0.1.0-preview release pages to the existing generator.
- Connected the shared navigation/footer and generated nine sitemap URLs and a one-item RSS feed. Missing navigation destinations now fail the static checker.
- Repaired credits styling and completed photograph/font provenance. Preserved all supplied original images and branding.
- Corrected the Nginx homepage redirect loop, narrow-screen grid overflow, screenshot frame sizing and actual image dimensions.
- Completed the email-draft support flow: required/email validation, retained input, pilot deep-link selection including same-page hash changes, selected-copy fallback and a no-JavaScript email path.
- Preserved legacy homepage fragments and native image-dialog keyboard behavior.

## Executed validation

| Check | Result |
|---|---|
| `node www/website/build.mjs --check` | 10 generated pages + one old credits redirect stub; 0 errors and 0 warnings. |
| Browser routes at 1440, 768 and 320 CSS pixels | All nine content pages returned 200; 27 width/route checks found no horizontal overflow or missing images. |
| Page requests and scripts | No page JavaScript errors or external page-load requests during the route checks. The deliberate missing-page request produced the expected browser 404 network entry. |
| Redirects and metadata files | `/index.html` and `/credits.html` returned 301 to the correct local destinations. Robots, sitemap, llms and RSS returned 200 with security headers. |
| Missing URL | HTTP 404 with the custom “This page does not exist” heading. |
| Keyboard and form interactions | 14 checks passed: mobile menu/Escape/focus, required errors/focus, email correction, draft copy, fallback selection, image dialog/viewport/focus return, reduced motion, no-JS navigation/email/mobile fit. |
| Clipboard scope | Success path used a browser-local mock to verify exact draft text; unavailable API exercised the real selected-preview fallback. No user's clipboard was overwritten. |
| Nginx | `docker compose exec -T website nginx -t` passed; local-only reload applied the reviewed static config. |
| Asset and source checks | Local references, metadata, structured-data parsing, CSS resource rules and page budgets passed the build checker. |
| Source syntax | Node syntax checks passed for build/check tools, source and generated website JavaScript and both browser QA scripts. Python AST parsing passed for the inherited image-preparation tool. |
| Repeatability | A repeated build produced identical SHA-256 values for all 101 public files. |

Results: [route/HTTP receipt](../../docs/design/website/qa/redesign-browser-checks.json), [interaction receipt](../../docs/design/website/qa/redesign-interaction-checks.json), [static report](../../docs/design/website/qa/static-checks.json).

Inspected screenshots: [desktop home](../../docs/design/website/qa/redesign-home-1440.png), [tablet product](../../docs/design/website/qa/redesign-product-768.png), [320px support](../../docs/design/website/qa/redesign-support-320.png), [320px credits](../../docs/design/website/qa/redesign-credits-320.png), [support form/fallback](../../docs/design/website/qa/redesign-support-form-320.png).

Repeat browser checks with a dedicated session:

```sh
npx --no-install --package @playwright/cli playwright-cli -s=phpledger-website open http://127.0.0.1:18201/
npx --no-install --package @playwright/cli playwright-cli -s=phpledger-website run-code --filename www/website/tools/browser-smoke.cjs
npx --no-install --package @playwright/cli playwright-cli -s=phpledger-website run-code --filename www/website/tools/browser-interactions.cjs
```

On Windows use `npx.cmd`. The Chromium route check disables browser caching so existing short-lived HTML caching cannot mix earlier and current builds. Inspect the returned `errors` and `failed` arrays as well as the CLI exit code.

## Limits and boundaries

The OS email-client launch/send path, a real OS clipboard write, exact 200% zoom, screen-reader sessions, timed performance/Core Web Vitals, independent accessibility/accounting review and observed user sessions were not run. Desktop/tablet/mobile screenshots and automated checks do not establish those outcomes.

External URLs and the published ZIP digest match the local release validation receipt and supplied configuration; they were not re-downloaded or independently live-verified in this completion work. Public `/demo/` is a separate routing/application contract, not served or revalidated by this static service.

References read: repository AGENTS.md, README.md, ARCHITECTURE.md, ROADMAP.md, DESIGN.md, website composition/content/source records, package INSTALL.md template and the local PREVIEW-0.1.0-VALIDATION.md release receipt. Google Drive documents: none.

Changed files: website source pages/CSS/JavaScript, checker, browser QA tools and generated public HTML/CSS/JavaScript/sitemap/RSS; website README/QA, source/content records and QA receipts/screenshots; `docker/website.conf`. Existing Claude image assets/source originals were retained.

Migrations: no. Schema changed: no. Raw secrets exposed: no. External/live calls made by this completion work: no. Live/production changed: no. Only the loopback static website service was reloaded.

---

## Historical website and application presentation QA
# Website and application presentation QA

Date: 2026-09-14. Local development baseline; publication is held while the user's report/POS critique is researched. Earlier composition selection does not establish acceptance of the current product screens.

## Source and scope

The later narrow logo-framing change preserves the existing source assets and uses the CSS viewport documented in [provenance](../../docs/design/website/SOURCES.md#header-framing-14-september-2026). The full mark is visible in application, website and typography previews at desktop 1440px and mobile 390px, with no horizontal page overflow. The website at 320px has a 175px frame and its menu ends at x=291 within the 305px content width. Credits were additionally inspected at desktop. Local `GET /demo/` returned 200 with the unchanged prefixed logo URL and versioned prefixed stylesheet. PHP layout lint, CSS parsing, website static checks and whitespace checks passed. Updated conservative file budget: 592,323 bytes. No source artwork, app font, workflow or publication changed in this pass.

The selected website reference is [1, Field Notes](../../docs/design/website/01-field-notes.png). Its split introduction/real workplace photo, editorial numbering, broad product evidence and navy pilot invitation were implemented with original photos and real application captures. The user's later scope makes owner reports and cash POS the leading product evidence, with the expense/journal/trial-balance journey available as a bookkeeper disclosure.

The application retained the Review Console structure and existing routes/forms. The visual polish raises normal reading text to 14–16px, gives transaction list/detail their own readable panels, improves the company/navigation controls, introduces an owner overview with actual balances, styles balance sheet/P&L/forecast, and gives sign-in a deliberate two-column introduction. No alternative frontend framework or financial write path was added.

## Checked before the owner/POS expansion

| Check | Observed result / evidence |
|---|---|
| Desktop 1440 × 1000 | Correct typeface, real assets and readable hierarchy; no horizontal page overflow. [Final hero](../../docs/design/website/qa/desktop-final.png) |
| Tablet 768 × 1024 | Navigation collapses correctly; split hero and three principles fit. [Capture](../../docs/design/website/qa/tablet-768.png) |
| Mobile 390 × 844 | Single-column hero, working menu and walkthrough; no page overflow. [Hero](../../docs/design/website/qa/mobile-390-hero.png) |
| Narrow 320 × 760 | Initial menu clipping was corrected by narrowing the header identity and adjusting the menu spacing. Document width 305 within 320 viewport. [Recheck](../../docs/design/website/qa/mobile-320-final.png) |
| Credits at 320px | Header wraps and text remains readable, width 305 within 320. [Capture](../../docs/design/website/qa/credits-320.png) |
| Keyboard report tabs | Right arrow changes selection and the visible report. Modal focuses Close; Escape closes and restores the opener. [Report](../../docs/design/website/qa/report-dialog-desktop.png) |
| Mobile enlarged image | Fits viewport, internally scrolls a larger readable image. A keyboard-focusable image region and explicit scroll instructions were added. [Capture before instruction addition](../../docs/design/website/qa/mobile-dialog.png) |
| Pilot validation and correction | Empty submission identifies all four required fields; entered synthetic values are retained and an exact reviewable message is copied. No message was sent. [Pilot](../../docs/design/website/qa/pilot-desktop.png) |
| JavaScript | `node --check www/website/public/assets/site.js` passed, including the grouped-tab update. |
| Markup/assets/styles | Local structure, duplicate IDs, anchors, image alt/asset paths and CSS parser checks passed before expansion. Final asset check is recorded separately below. |

The initial same-input comparison is [here](../../docs/design/website/qa/comparison-desktop.png). It identified the first implementation's desktop headline as too quiet; the subsequent [final hero](../../docs/design/website/qa/desktop-final.png) increases it to approximately 88px at 1440 while keeping smaller breakpoint sizes. Original versus implemented product pixels intentionally differ: working captures replace generated UI.

## Application audit and remediation

The live local application was inspected before edits, without financial writes. These are current-session captures, not recalled judgments:

1. [Sign-in before](../../docs/design/website/qa/app-polish/01-login-before.png): a narrow form floated in unstructured space. Added an intentional brand introduction and a bounded form surface, preserving account fields and behavior.
2. [Transactions before](../../docs/design/website/qa/app-polish/02-transactions-before.png): small row/detail text and weak section hierarchy made the working flow feel raw. Increased readable sizes, separated list/detail surfaces, strengthened amount/status emphasis and retained responsive list/detail behavior.
3. [Reports before](../../docs/design/website/qa/app-polish/03-reports-before.png): new report links were visibly unstyled. Replaced the directory with actual cash and performance values, a truthful magnitude comparison, meaningful report questions and a clearly separate planning option.

`layout.php` and `reports.php` passed PHP syntax checks using the running `web` service. The stylesheet parsed without errors. The owner overview uses existing controller-supplied report values; only chart widths use rounded display arithmetic. Financial strings remain fixed-precision and escaped. The demo error view skips the demo-state database query, and the opening-balance notice now correctly says recording and posting remain unavailable.

## Final integration status and limits

### Documentation links: 14 September 2026

Added the Wiki and Getting Started guide in the existing documentation area and footer, plus an installation FAQ distinguishing the forthcoming new package from the legacy default branch and temporary evaluation demo. The main navigation is unchanged. Local Chrome checks at 1440px, 768px and 320px found no horizontal overflow (content widths 1425, 753 and 305px); the new FAQ expanded correctly. [Desktop capture](../../docs/design/website/qa/documentation/desktop.png) and [320px mobile capture](../../docs/design/website/qa/documentation/mobile-320.png) record the changed area. The five outbound links use the two exact Wiki paths and match the prepared local Wiki pages. Actual GitHub destination availability remains the lead's publication check. Static markup, local assets, anchors and CSS report zero errors; the conservative file budget is now **593,330 bytes**, with unchanged **7,259-byte** JavaScript. No font, CSS, form handling or application behavior changed in this update.

Chrome access recovered. Actual owner overview, P&L, balance sheet, cash scenario and POS baseline captures were inspected and saved under `docs/design/product-screens/04-*` through `08-*`. Their compressed website derivatives now resolve, with correct intrinsic dimensions and originals preserved. Static markup/asset/anchor/CSS checks report zero errors, with a conservative 591,936-byte file budget and 7,259 bytes of JavaScript. This includes the first owner screen and POS screen plus both lazy secondary photographs; it is not a browser timing measurement. Expanded tabs/disclosure still need their final responsive acceptance pass after the redesign decision.

The POS audit found current basket/Pay reachability problems on tablet/mobile and a possible implicit Enter submission. [POS research](../../docs/design/POS_RESEARCH.md) records the screenshots, official comparison and narrow authorized correction. In isolated company 5, Enter in search, quantity and tender did not submit; deliberate Enter on Record cash sale saved source 15 / journal 11. The Core accounting sample was not modified. JavaScript syntax and PHP view lint passed; the parallel HTTP workstream reports 21/0. JavaScript-disabled browser operation remains a runtime check.

Full-page capture timed out once, so viewport screenshots were retained. Valid mailto handoff to the operating-system email application, disabled-clipboard failure behavior, runtime reduced-motion emulation, exact browser zoom at 200%, screen-reader testing, timing-based performance and observed user sessions were not completed by this workstream. Code contains the reduced-motion and clipboard fallback paths; that is not runtime proof. No claim of WCAG compliance or measured usability success is made.

Migrations: no. Schema changes: no. Raw real secrets/customer records exposed: no. External calls in this workstream: read-only research, licensed photo downloads, image generation and a local build dependency download. Live changes by this workstream: no. Google Drive documents read: none.
