# PHP Ledger marketing website

The deployable static document root is `www/website/public`. It is separate from the accounting application and never loads its bootstrap or serves the repository root.

## Current local website refresh: 0.6.0-preview

The 18 September 2026 refresh updates the homepage, product and POS walkthroughs, download illustration, three reporting guides, news index and a new `/news/0-6-0-preview/` article. Shared capability/limitation metadata and social previews describe the rebuilt workspace. This website refresh is local and has not been published.

Current screenshots live under `public/assets/screens/v0.6.0-preview/`. Its `manifest.json` records routes, sample identity, captions, alt text, dimensions and SHA-256 hashes for 18 actual application captures and their responsive WebP derivatives. They were captured from the local 0.6.0 runtime with fresh Willow Corner Shop and Cedar Studio samples; image pixels were not rewritten. The POS sequence uses a local sample USD 5.75 sale. The three Cedar Studio walkthroughs retain their worked figures with new interface captures. Historical release pages and their original screenshots remain intact.

Desktop/tablet are the application design targets. Mobile refinement, dark mode, full visual/accessibility acceptance, field-error consistency and nested report/source/action return behavior remain unfinished. Website layout checks do not close those application acceptance gates. No application release archive, version, migration or published media kit changes as part of this refresh. See `design-qa.md` for local checks and publication boundaries.

## Historical accounting starter 0.4.0 publication

The 0.4.0 website presented core AR/AP, optional Purchasing/Inventory, manual inclusive/exclusive tax, and five demo choices (one focused playground plus four existing multi-year histories). Its release article remains at `/news/0-4-0-preview/`. The approved website design and long-form library are preserved; dated release articles retain their original scope and screenshots.

Published as `website-redesign-20260915-233156` at **04:32 PKT on 16 September 2026** (23:32 UTC on 15 September). All 165 static files matched archive, host and public hashes. Demo identity, entry/health, noindex and existing headers were preserved. See [the static publication receipt](../../docs/design/website/qa/release040-live-publication.json). The separate application and media archives are published on [GitHub](https://github.com/phpledger/phpledger/releases/tag/v0.4.0-preview), with verified public downloads. The site builds 64 source pages into 65 HTML outputs and 63 sitemap URLs.

The [marketing handoff](../../docs/design/website/ACCOUNTING-STARTER-LAUNCH-KIT.md) contains final copy, evidence, actual sample screenshots and a downloadable media ZIP. No email or social post is sent by the static site or this publication.

## Source and build

The completed website-redesign work uses a dependency-free Node.js 18+ build:

```sh
node www/website/build.mjs --check
```

Edit `src/pages/*.html` (JSON front matter followed by page content), `src/partials/*.html`, `src/css/*.css`, `src/js/site.js` and `src/site.json`; then rebuild. The generator writes public HTML, versioned CSS/JS links, sitemap and the release RSS feed. `src/static` supplies the manifest, credits redirect and robots policy. Release facts and page metadata generate `llms.txt`, `/.well-known/ai.txt`, `/ai/summary.json` and `/ai/faq.json`; the former static discovery copies were removed. Valid `lastmod` is required on every page. WebPage/Person/breadcrumb data is emitted consistently, while explicit HowTo/ItemList metadata is validated. Generated contents use stable, collision-safe heading anchors. Commit source and generated output together. Do not hand-edit generated pages.

The public output needs no Node.js, PHP, database, analytics or third-party page assets. GitHub DNS/preconnect hints can establish a connection before you follow a download link. It uses locally served Manrope, Poppins and Inter fonts, the supplied brand, licensed photographs and actual sample application captures. Existing originals remain under `docs/design`. See [asset provenance](../../docs/design/website/SOURCES.md).

`tools/prepare-images.py` is the existing optional Pillow-based asset preparation tool; the prepared derivatives are already present. It is not part of normal page builds. The image manifests record source paths, dimensions, bytes and hashes.

## Local preview and routing

The existing website Compose service serves `public` at [http://127.0.0.1:18201/](http://127.0.0.1:18201/). A plain static server also works for directory pages, although Nginx is required to validate the configured HTTP redirects and custom 404 status.

| Path | Content |
|---|---|
| `/` | Product introduction, real screen captures, reports and start paths |
| `/product/` | Core invoices/bills/payments, optional purchasing/stock, manual tax and retained bookkeeping |
| `/point-of-sale/` | Preview cash-sale journey and supported boundaries |
| `/download/` | 0.6.0-preview assets, checksum, installation requirements and FAQ |
| `/about/` | Project, maintainer and current scope |
| `/privacy/` | Sessions, country hints, sample reset and operational logs |
| `/terms/` | Sample demo use and preview/license boundaries |
| `/support/` | Setup/training/troubleshooting information and local email-draft form |
| `/roadmap/` | Opening balances/cutover → period administration → bank reconciliation, then later gates |
| `/news/` | Release index and RSS link |
| `/news/0-6-0-preview/` | Current interface release, workflow changes and acceptance limits |
| `/news/0-4-0-preview/` | Historical starter release and fictional experiments |
| `/news/0-1-0-preview/` | Historical versioned release announcement |
| `/credits/` | Photography, fonts, icons and company-mark provenance |
| `/404.html` | Internal custom error page used for missing URLs |

Nginx redirects `/credits.html` to `/credits/` and explicit `/index.html` to `/`. The latter tests the original request URI so the server's internal directory-index handling cannot loop. HTML has short caching; CSS/JS URLs use content hashes. Clear the local browser cache or disable it in QA after rebuilding.

Existing homepage fragments `#features`, `#point-of-sale`, `#accounting-details`, `#roadmap`, `#enterprise`, `#quick-start`, `#contribute`, `#docs`, `#faq` and `#pilot` remain valid. Support uses `#enquiry` and `#enquiry-pilot`.

`/demo/` remains an external deployment routing contract for the separately maintained isolated PHP demo. The local static service does not proxy it. Never point it at legacy code. The redesign QA does not claim to revalidate or deploy the public demo.

## Interaction and privacy

The mobile menu supports Escape and retains usable navigation with JavaScript disabled. Screen captures open in a native keyboard-accessible dialog; Escape returns focus to the opener. Reduced-motion preferences disable smooth scrolling.

The support form validates required entries and email shape, preserves correction values, and prepares a mailto draft or copies an exact reviewable message. It stores no visitor data and sends no message. Clipboard failure exposes a selected text preview. JavaScript-disabled visitors receive a direct email link and no active form.

## Validation and publication

Run the build checker before publication. It fails on missing navigation destinations, invalid local links/anchors, malformed metadata, missing assets, unsafe inline/external page resources, JavaScript syntax errors and page-budget overruns.

Reproducible local browser checks are in `tools/browser-smoke.cjs` and `tools/browser-interactions.cjs`, run with Playwright CLI's `run-code --filename` against the local website service. They are QA tools, excluded from the public document root. Read [the current QA record](design-qa.md) for commands, results and limitations.

The content guard is `node www/website/tools/aeo-geo-audit.mjs`. It reports page word counts, heading depth, figure/caption parity and brand mentions; it is a lightweight editorial regression check, not a search-ranking guarantee. The content inventory is [CONTENT-REGISTER.md](../../docs/design/website/CONTENT-REGISTER.md).

The approved **A, Workbench, with B's photo treatment** redesign was published to **https://phpledger.com/** on **15 September 2026 at 08:08 UTC (13:08 PKT)** as `website-redesign-20260915-080700`. The homepage uses the exact approved headline: “Double-entry accounting that runs on your own PHP and MySQL hosting”. At that publication, download copy used the independently verified 0.1.2-preview package; existing 0.1.0 screenshots and news remain explicitly historical.

Publication used the existing static-release hosting lane, with a private backup of the prior website/configuration and an atomic Nginx configuration switch. All 101 public files were verified; canonical redirects, custom 404/private-path rejection, security/cache headers and demo entry/health passed. The demo gained its planned `X-Robots-Tag: noindex, nofollow` header; its containers, proxy target, application and database were preserved. No Git push, application deployment or migration was needed. See [current publication QA](design-qa.md#live-publication-15-september-2026) and the [machine-readable receipt](../../docs/design/website/qa/live-20260915-publication.json).

Future website releases still require explicit publication authorization. The current source tree contains unrelated local accounting work: deploy only the reviewed static document root. Google/Bing verification/submissions, IndexNow, social handles and the wider campaign remain separate pending work. Shared social-preview artwork is present; additional per-page variants remain an editorial follow-up.

The 0.1.5 work adds six product FAQs and permits large image previews on indexable pages. The local Nginx example emits host-only HSTS on HTTPS and denies unused browser permissions; live configuration has a separate verified publication receipt. See [staged delivery](../../docs/repository/sprint-05/DELIVERY-0.1.5-0.2.1.md).

## Content library and publication — 16 September 2026

The owner authorized takeover, review and publication of the other website thread. The earlier completed collection had 63 source pages, 62 sitemap URLs and 64 HTML outputs including the redirect. It adds the glossary, ten lessons, six comparisons, six hosting guides, four research articles, audience pages and five additional walkthroughs. The [content register](../../docs/design/website/CONTENT-REGISTER.md) records page-level review and remaining professional gates.

Run `node www/website/build.mjs --check` and `node www/website/tools/aeo-geo-audit.mjs`. The latter reports main-content metrics, excluding shared footer/navigation. Browser QA must use a fresh context or cache-busting URL because local HTML uses short caching; verify content-hashed CSS URLs after rebuilding. Check desktop, tablet and mobile layouts, including grid children, long tables and code blocks.

Publish only the reviewed static archive through the existing immutable-release lane, with private prior-site/configuration backup, unchanged demo proxy and OAuth locations, Nginx validation, public byte/hash checks and rollback. A new manifest MIME location belongs in the HTTPS static server; HTTP remains a canonical redirect. Preserve ACME handling and demo noindex. After an explicitly authorized publication, IndexNow submission may be performed separately using the key file listed in robots.txt; no submission client, account creation or campaign is part of this release.
