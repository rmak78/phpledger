# PHP Ledger marketing website

The deployable static document root is `www/website/public`. It is separate from the accounting application and never loads its bootstrap or serves the repository root.

## Source and build

The completed website-redesign work uses a dependency-free Node.js 18+ build:

```sh
node www/website/build.mjs --check
```

Edit `src/pages/*.html` (JSON front matter followed by page content), `src/partials/*.html`, `src/css/*.css`, `src/js/site.js` and `src/site.json`; then rebuild. The generator writes public HTML, versioned CSS/JS links, sitemap and the release RSS feed. `src/static` supplies the old credits redirect, robots and llms files. Commit source and generated output together. Do not hand-edit generated pages.

The public output needs no Node.js, PHP, database, analytics or external page-load requests. It uses locally served Manrope, Poppins and Inter fonts, the supplied brand, licensed photographs and actual synthetic application captures. Existing originals remain under `docs/design`. See [asset provenance](../../docs/design/website/SOURCES.md).

`tools/prepare-images.py` is the existing optional Pillow-based asset preparation tool; the prepared derivatives are already present. It is not part of normal page builds. The image manifests record source paths, dimensions, bytes and hashes.

## Local preview and routing

The existing website Compose service serves `public` at [http://127.0.0.1:18201/](http://127.0.0.1:18201/). A plain static server also works for directory pages, although Nginx is required to validate the configured HTTP redirects and custom 404 status.

| Path | Content |
|---|---|
| `/` | Product introduction, real screen captures, reports and start paths |
| `/product/` | Company setup, receipt/expense posting, corrections and reports |
| `/point-of-sale/` | Preview cash-sale journey and supported boundaries |
| `/download/` | Verified 0.1.3-preview assets, checksum, installation requirements and FAQ |
| `/support/` | Setup/training/troubleshooting information and local email-draft form |
| `/roadmap/` | Opening balances/cutover → period administration → bank reconciliation, then later gates |
| `/news/` | Release index and RSS link |
| `/news/0-1-0-preview/` | Versioned release announcement |
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

The approved **A, Workbench, with B's photo treatment** redesign was published to **https://phpledger.com/** on **15 September 2026 at 08:08 UTC (13:08 PKT)** as `website-redesign-20260915-080700`. The homepage uses the exact approved headline: “Double-entry accounting that runs on your own PHP and MySQL hosting”. Current download copy uses the independently verified 0.1.2-preview package; existing 0.1.0 screenshots and news remain explicitly historical.

Publication used the existing static-release hosting lane, with a private backup of the prior website/configuration and an atomic Nginx configuration switch. All 101 public files were verified; canonical redirects, custom 404/private-path rejection, security/cache headers and demo entry/health passed. The demo gained its planned `X-Robots-Tag: noindex, nofollow` header; its containers, proxy target, application and database were preserved. No Git push, application deployment or migration was needed. See [current publication QA](design-qa.md#live-publication-15-september-2026) and the [machine-readable receipt](../../docs/design/website/qa/live-20260915-publication.json).

Future website releases still require explicit publication authorization. The current source tree contains unrelated local accounting work: deploy only the reviewed static document root. Google/Bing verification/submissions, IndexNow, social handles and the wider campaign remain separate pending work. Shared social-preview artwork is present; additional per-page variants remain an editorial follow-up.
