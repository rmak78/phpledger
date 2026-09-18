# Codex prompt: raise phpledger.com to the maximum honest score on SEO, AEO and GEO

Measured with seoscore.tools (260+ checks, three categories) on 15 September 2026 against the live site.
Paste everything below the line into Codex at the repository root.

---

You are working in the PHP Ledger repository (`C:\phpledger`). This task changes **only the marketing website source** under `www/website/` plus the website's Nginx configuration at `docker/website.conf`. **Do not touch the accounting application** (`www/phpledger/`), `resources/`, `tools/` at repo root, or anything under `docs/` except where this prompt names a file explicitly.

Read these before changing anything:

- `www/website/README.md` — build, routing, publication and QA contract
- `www/website/build.mjs` — the generator: JSON front matter, `GRAPH_PARTS`, `PAGE_KEYS`, JSON-LD builders
- `www/website/check.mjs` — the static QA gate; its header comment lists every rule and whether it errors or warns
- `www/website/design-qa.md` — the current QA record
- `AGENTS.md` and `CONTRIBUTING.md` — repository conventions
- `docs/strategy/DISTRIBUTION-PLAN.md` — the "alternative to" comparison pages are already an owner priority; the comparison work below is the first instalment of that

Build and validate with:

```sh
node www/website/build.mjs --check
```

Commit source (`www/website/src/**`) and generated output (`www/website/public/**`) together. Do not hand-edit generated pages. Do not push. Do not deploy — publication is separately authorised.

---

## 1. Where the site stands today

seoscore.tools scores each page independently. Grades: 90-100 A+, 80-89 A, 70-79 B, 60-69 C, under 60 D/F.

| Page | SEO (68 checks) | AEO (50 checks) | GEO (55 checks) |
|---|---|---|---|
| `/` | **94** A+ | **71** B | **70** B |
| `/product/` | 93 A+ | 71 B | 69 C |
| `/point-of-sale/` | 92 A+ | 62 C | 63 C |
| `/download/` | 93 A+ | 75 B | 73 B |
| `/support/` | 91 A+ | 73 B | 60 C |
| `/roadmap/` | 90 A+ | 69 C | 67 C |
| `/about/` | 92 A+ | 58 D | 60 C |
| `/guides/` | 90 A+ | 56 D | 55 D |
| `/news/` | 90 A+ | 62 C | 61 C |

**The B grade is not an SEO problem.** Classic SEO is already A+ everywhere. The whole deficit is in AEO (Answer Engine Optimization — can an AI assistant extract and cite this page) and GEO (Generative Engine Optimization — will AI search feature it). The scanner classified the site as `saas`, confidence 55.

Targets for this task: **SEO 98-100, AEO 92+, GEO 88+ on every indexable page.** Section 7 lists the checks that will remain failed on purpose and why; do not chase those.

---

## 2. Hard constraints — read these before writing a single line

These are non-negotiable and several of them directly contradict the scanner's own advice.

1. **Never invent evidence.** The scanner asks for "reviews, certifications", "expert quotes", "original data" and "case studies". PHP Ledger is a development preview with no users to quote and no certifications. You may use only: figures that exist in `www/website/src/site.json` or the release manifest, statements the maintainer is on record as making, cited public sources with a URL, and the sample worked examples already published under `/guides/`. **No fabricated testimonials, no invented statistics, no made-up quotes, no fictional adoption numbers.**
2. **`check.mjs` errors on `aggregateRating`, `review` and interaction counts in JSON-LD.** Do not add them under any circumstance.
3. **CSP is `default-src 'self'`.** No inline `<style>`, no `style=` attributes, no inline `<script>` except JSON-LD, no `on*=` handlers, no external stylesheets, scripts, images or preloads, no `@import`, no `url(http`, `url(//` or `url(data:` in CSS. `check.mjs` errors on every one of these.
4. **No client-side network or storage.** `check.mjs` errors on `fetch(`, `XMLHttpRequest`, `localStorage`, `sessionStorage`, `sendBeacon` in any public JS. No analytics, no third-party fonts, no embeds.
5. **Page byte budgets** are declared per page as `budgetEager` / `budgetTotal` in front matter and enforced as errors. Prose is cheap; new images are not. If a change genuinely needs more budget, raise the number in front matter in the same commit and say so in the summary.
6. **Banned words** (warning): `empower`, `seamless`, `streamlin*`, `robust`, `effortless`, `unlock`, `elevat*`. **Forbidden strings** (error): phone numbers, the old address, "licensing review", "being prepared", "no download", "PLACEHOLDER", "lorem".
7. **Truthful scope.** Nothing in this task ships a feature. Keep every capability claim bounded to what 0.2.1-preview actually does. Keep "technical checks passed" distinct from accounting review. Every page showing `/assets/screens/` must keep its sample-data label (`check.mjs` errors otherwise).
8. **Meta description must stay 50-160 characters** (error) and **canonical must equal `baseUrl + path`** (error) and **exactly one `<h1>` per page** (error).
9. **Do not change any published URL.** See §6.1.
10. Where this prompt marks something **OWNER DECISION**, do not apply it. Implement everything else, then list those items in the final summary for the owner to rule on.

---

## 3. Site-wide infrastructure

These fix checks that currently fail on **all nine pages at once**. Do this group first — it is the largest single score movement for the least content work.

### 3.1 `docker/website.conf` — add `X-Frame-Options`

Fails site-wide: `seo:x_frame_options` ("Missing X-Frame-Options — clickjacking risk").

CSP already sets `frame-ancestors 'none'`, but the scanner checks for the header by name. Add:

```
add_header X-Frame-Options "DENY" always;
```

**Nginx does not inherit `add_header` into a `location` that sets its own.** The file already repeats the security block in five places — the `server` block and the four `location` blocks (`\.(css|js|woff2)$`, `\.(webp|png|jpg|jpeg|svg|ico)$`, the robots/sitemap/llms/feed block, and `location /`). Add the line to **every one of them**. Note in the commit summary that the live configuration needs the same change at the next authorised publication.

### 3.2 `src/static/site.webmanifest` — new file

Fails site-wide: `seo:web_manifest`.

Icons already exist at `/assets/brand/icon-192.png`, `/assets/brand/icon-512.png`, `/assets/brand/apple-touch-icon.png`. Create the manifest with `name`, `short_name` ("PHP Ledger"), `description` (use `site.entity`), `start_url: "/"`, `scope: "/"`, `display: "standalone"`, `background_color` and `theme_color: "#0c2052"` (match the existing `theme-color` meta), `lang: "en-GB"`, and the two PNG icons with `sizes`, `type` and `purpose: "any"`.

Add to `src/partials/head.html`:

```html
<link rel="manifest" href="/site.webmanifest">
```

`check.mjs` already allows `manifest` in `LOCAL_ONLY_LINK_RELS`. Add a `location = /site.webmanifest { default_type application/manifest+json; }` block to `docker/website.conf` alongside the existing `location = /llms.txt` rule, and add `site.webmanifest` to the short-cache location regex.

### 3.3 `src/partials/head.html` — hreflang and resource hints

Fails site-wide: `seo:hreflang`, `seo:resource_hints`.

**hreflang.** The site is English-only, so emit a self-referencing pair. In `build.mjs`, extend the head context so the partial can render:

```html
<link rel="alternate" hreflang="en" href="{{canonical}}">
<link rel="alternate" hreflang="x-default" href="{{canonical}}">
```

This is valid and honest for a single-language site. When Urdu and Arabic arrive (see `docs/MODULE-ROADMAP.md`) this becomes the real hook.

**Resource hints.** The site loads nothing cross-origin, but it *links* to `github.com` from the header, footer, download and about pages. Add:

```html
<link rel="dns-prefetch" href="https://github.com">
```

site-wide, and on `/download/` additionally `<link rel="preconnect" href="https://github.com" crossorigin>` via that page's `headExtra`. `preconnect` and `dns-prefetch` are not in `check.mjs`'s `LOCAL_ONLY_LINK_RELS`, so an external origin is permitted for them — verify this holds after your change rather than assuming.

### 3.4 `src/site.json` — entity identity

Fails site-wide: `aeo:aeo_sameas_links` ("No sameAs links in schema"), `geo:geo_knowledge_graph` ("Only 1 authoritative sameAs link — add Wikipedia, LinkedIn, or Wikidata").

The `social` block is currently all empty strings and `twitterHandle` is empty. Populate `Organization.sameAs` in `build.mjs` from **real, resolving URLs only**:

- `https://github.com/phpledger/phpledger` (already present)
- `https://github.com/phpledger/phpledger/wiki`
- `https://pk.linkedin.com/in/rmak78` (currently only on the `founder` node — promote it to the Organization graph too, correctly attributed as the maintainer's profile)
- Any Packagist, Docker Hub or AlternativeTo listing **that already exists**. If it does not exist yet, leave the key empty and list it in the final summary as a follow-up. Do not invent URLs.

**OWNER DECISION:** a Wikidata item and a LinkedIn company page for the project would satisfy `geo_knowledge_graph` outright. Do not create them; flag them.

### 3.5 `build.mjs` — new JSON-LD graph parts

Fails site-wide or near-site-wide: `geo:geo_schema_richness` ("Only 2 Schema types"), `geo:geo_author_schema`, `aeo:aeo_author`, `seo:author_byline`, `geo:geo_author_credentials`, `aeo:aeo_date`, `geo:geo_freshness`, `geo:geo_breadcrumb` (home), `aeo:aeo_speakable`, `geo:geo_howto_schema`.

`GRAPH_PARTS` is currently `['organization', 'website', 'software', 'breadcrumb', 'faq', 'article']`. Add three, and update `PAGE_KEYS` and the front-matter validation in the same edit so a bad page still fails the build loudly:

**`webpage`** — emit for every page, automatically, without needing front matter. Include `@type: "WebPage"`, `@id: canonical + "#webpage"`, `url`, `name` (page title), `description`, `inLanguage: "en"`, `isPartOf` → the `#website` node, `primaryImageOfPage` where the page has an `ogImage`, `datePublished` and `dateModified` from front matter `lastmod`, `author` and `publisher` → the Person and Organization nodes, `breadcrumb` → the BreadcrumbList node where one exists, and a `speakable` `SpeakableSpecification` with `cssSelector` pointing at the page's summary block (§4.1) and its H1.

**`person`** — a single `Person` node, `@id: baseUrl + "/#maintainer"`, built from `site.founder`: `name`, `url`, `sameAs`, plus `jobTitle`, `worksFor` → an Organization reference, and `knowsAbout` listing the real subject areas (double-entry bookkeeping, PHP application architecture, self-hosted software). Reference it as `author` from `webpage` and `article`. This is what closes `geo_author_schema`, `aeo_author` and `geo_author_credentials`.

**`howto`** — for `/download/` only. Front-matter shape `"howto": {"name": ..., "totalTime": "PT20M", "supply": [...], "tool": [...], "step": [{"name": ..., "text": ..., "url": "#anchor"}]}`. Google deprecated HowTo rich results in 2023, so this buys nothing in classic search — it is here because the scanner reads it as AI context and because the install steps are genuinely a procedure. Keep the steps identical to `resources/release/INSTALL.md`; if they diverge, fix the page, not the installer.

**Breadcrumb on the homepage.** `build.mjs`'s `breadcrumb()` currently skips the crumb for `path === '/'`, which leaves the homepage with no `BreadcrumbList` at all. Emit a single-item list (`position: 1`, name "Home", item `baseUrl + "/"`) when the page is the root, and add `"breadcrumb"` to the homepage's `jsonld` array.

### 3.6 `src/partials/footer.html` — visible byline and review date

Fails site-wide: `seo:author_byline` ("No author byline — add 'By [Name]'"), `aeo:aeo_date` ("No date information"), `geo:geo_time_element` ("No `<time>` elements"), `geo:geo_freshness`.

Schema alone does not satisfy these — the scanner looks for visible text and a real `<time>` element. Add a small block to the footer, rendered on every page:

```html
<p class="footer-meta">Maintained by <a href="/about/" rel="author">Rana Mansoor Akbar Khan</a>, PHP Ledger project maintainer.
Last reviewed <time datetime="2026-09-15">15 September 2026</time>.</p>
```

Drive the date from the page's `lastmod` front matter, not a hard-coded string, and **make `lastmod` required for every non-404 page** in `build.mjs` validation. `src/pages/home.html`, `product.html`, `point-of-sale.html`, `download.html`, `support.html`, `roadmap.html`, `news.html` and the news items currently lack it or need it refreshed — set each to the date you actually edit the page.

Style `.footer-meta` in `src/css/19-footer.css`. No inline styles.

### 3.7 `src/static/llms.txt` — it is stale, and that is a real defect

The published `llms.txt` describes **0.1.4-preview** while `site.json` declares **0.2.1-preview**. AI crawlers read this file preferentially, so it is currently feeding them a version-old picture of the product.

Rewrite it from `site.json` and the current page set. It must state: the 0.2.1 version and date, the scoped read API/MCP connections, the four sample businesses and reporting walkthroughs, the reconciliation and period-close capabilities, and — unchanged in spirit — the honest "not yet" list (customer/vendor subledgers, inventory and cost of sales, tax, foreign-currency posting, offline use, card payments, document scanning). Add the `/guides/` pages to the Product section; they are missing. Keep the demo-is-sample warning.

**Better still: generate it.** Add an `llms.txt` emitter to `build.mjs` driven by `site.json` and page front matter, so it cannot drift again. Keep `src/static/llms.txt` only if you cannot generate it cleanly; if you do generate it, delete the static copy and say so.

### 3.8 AI-discovery files — new

Fails site-wide: `geo:geo_ai_discovery` ("No AI-discovery files (.well-known/ai.txt, /ai/summary.json, /ai/faq.json)").

These are an emerging, not a settled, convention — the check is low-weight. They are cheap and they cost nothing in truthfulness, so generate all three from existing data in `build.mjs`:

- `/.well-known/ai.txt` — plain text: project identity, licence position (AGPL-3.0-or-later core, commercial licence available, 0.1.x previews remain MIT), crawl posture matching `robots.txt`, contact email, and a pointer to `/llms.txt`.
- `/ai/summary.json` — JSON: name, description (`site.entity`), version, releaseDate, licence, requirements, repository, demo URL and its sample/reset warning, capability list and explicit "not yet" list.
- `/ai/faq.json` — JSON: the union of every page's `faq` front-matter array, each entry carrying its source page URL.

Add a `location ~ ^/(\.well-known/ai\.txt|ai/.*\.json)$` short-cache block to `docker/website.conf` with the full security-header set repeated, and note that `location ~ /\. { deny all; }` currently blocks `/.well-known/` — you must add an explicit `location ^~ /.well-known/` allow **before** it, or the file 403s. Verify with the local Compose service before claiming it works.

### 3.9 `robots.txt` — IndexNow

Fails site-wide but **info severity only**: `seo:indexnow`.

The key file already exists at `/240b73d781977f499ab7c75318b9a14f.txt` and the key is in `site.json`. Add a commented `IndexNow` line to `src/static/robots.txt` naming the key file URL, and add an IndexNow submission step to the publication runbook in `www/website/README.md`. Do not build a submission client — the site is static and Google does not use IndexNow.

---

## 4. Content patterns to apply to every page

This is where AEO and GEO are actually won. Apply all of these to all nine indexable pages plus `/privacy/`, `/terms/`, `/credits/` and the three guide pages and five news items, adjusting depth to the page.

### 4.1 A "Key takeaways" block at the top of every page

Closes: `aeo:aeo_summary`, `geo:geo_summary`, `aeo:aeo_direct_answers`, `aeo:aeo_answer_box`, `aeo:aeo_snippet_ready`, `geo:geo_passage_citability` ("0% citation-ready passages"), `geo:geo_featured_snippet`.

Directly after the H1 and lead, add a bounded summary: a short H2 ("In short" or "Key takeaways"), then 3-5 `<li>` items. **Each bullet must stand alone out of context** — an AI assistant will lift one bullet and cite it with no surrounding page. So: "PHP Ledger 0.2.1-preview posts receipts, expenses and cash sales as balanced double-entry journals on your own PHP 8.2+ and MySQL 8.4 hosting", not "It does this on your own server."

Add the block's selector to the `speakable` `cssSelector` in §3.5. Add a `.summary` rule set to `src/css/13-chapter.css` or a new `21-summary.css`.

### 4.2 A one-sentence answer immediately after every H2 and H3

Closes: `aeo:aeo_answer_box` ("add 2+ short summaries, 40-200 chars, right after H2/H3"), `aeo:aeo_direct_answers`, `geo:geo_paragraph_length` ("53% optimal paragraphs — aim for 20-80 words").

Every H2 and H3 gets a 40-200 character answer paragraph as its first child, before any figure, list or table. Then the body follows. This is a mechanical, checkable rule — apply it everywhere.

### 4.3 Question-shaped headings covering all six question types

Closes: `geo:geo_paa_format` ("Few question headings"), `aeo:aeo_question_headings`, `geo:geo_comprehensiveness` ("Only 2/6 question types — add What, Why, How headings"), `geo:geo_hierarchy_depth` ("Flat heading structure — add H3/H4"), `geo:geo_subtopic_depth`.

Across each page's heading set, cover **What, Why, How, When, Who and Which**. Not every heading becomes a question — the approved chapter headlines on the homepage stay as they are — but add H3 subheads underneath them in question form: "What happens when you post an expense?", "Why does a correction create a reversal instead of an edit?", "Which currencies can a company use?", "Who is this preview for?", "When should you not use PHP Ledger yet?", "How do you install it on shared hosting?"

Every page currently has a flat H1 → H2 structure. Introduce a real H2 → H3 (→ H4 where warranted) hierarchy.

### 4.4 Sections of 120-180 words, opening with a definition

Closes: `seo:section_depth` ("5 of 11 H2 sections have < 50 words"), `aeo:aeo_section_depth` ("Avg 25 words/section"), `geo:geo_content_depth`, `geo:geo_passage_citability` ("write self-contained 120-180 word sections that open with a definition/answer"), `geo:geo_definitions` ("add 'X is...' patterns"), `aeo:aeo_segmentation`.

No H2 section under 50 words. Target 120-180 words per section, opening with an "X is ..." sentence. Merge sections that cannot honestly carry that weight rather than padding them.

### 4.5 Definition lists and key-value pairs

Closes: `aeo:aeo_definition_lists` ("No `<dl>`"), `aeo:aeo_key_value` ("Few key-value patterns"), `geo:geo_definitions`.

Add real `<dl>` blocks:

- **`/product/`** — a glossary: double-entry, journal, posting, draft, linked reversal, trial balance, profit and loss, balance sheet, base currency, period close, bank reconciliation. Definitions must match how the application actually behaves; cross-check against `docs/accounting/` before writing them.
- **`/download/`** — a package specification list: Version, Released, Package size (3.05 MB / 3,054,692 bytes), SHA-256, Licence, PHP, MySQL, Transport, Terminal access. Every value comes from `site.json` `release` and `requirements` — template them, do not retype them.
- **`/point-of-sale/`** — what the cash sale does and does not touch, as term/definition pairs.
- **Homepage** — a short "At a glance" `<dl>` in the preview section: Version, Licence, Requirements, Demo, Source.

Add `dl`, `dt`, `dd` styling to `src/css/14-facts.css`. None exists yet.

### 4.6 Tables, and both list types

Closes: `aeo:aeo_tables` ("No data tables"), `geo:geo_comparison` ("No comparison content — add vs. sections"), `geo:geo_list_variety` ("Only one list type — use both `<ul>` and `<ol>`"), `geo:geo_format_variety` ("Only 1/6 formatting types — use bold, lists, tables, quotes, code").

`src/css/15-table.css` already exists. Add:

- **Homepage and `/roadmap/`** — "In 0.2.1-preview / Not yet" as a real `<table>` with a caption, not only the current two `<ul>` columns. Keep the lists as well; the scanner wants both formats.
- **`/download/`** — a requirements table: component, minimum, recommended, note.
- **`/product/`** — a report table: report, what it answers, what it reads, where it drills to.
- **A comparison table.** This is the highest-value GEO item on the site and it feeds the "alternative to" pages already prioritised in `docs/strategy/DISTRIBUTION-PLAN.md`. Build one honest comparison of self-hosted open-source accounting options along axes PHP Ledger can be judged on without disparaging anyone: licence, hosting model, stack, data location, phone-home/licence-key behaviour, AR/AP availability, inventory, current maturity. **PHP Ledger must lose several rows** — it has no AR/AP, no inventory and no tax; say so in the table. Cite every competitor claim with a link to that project's own documentation and add a "verified on 15 September 2026" note. If you cannot verify a cell from a primary source, write "not verified" rather than guessing. **OWNER DECISION on placement:** propose it as a new `/compare/` page and as a section on `/product/`; implement it on `/product/` only and leave the standalone page for the owner.
- Use `<ol>` wherever order genuinely matters (install steps, the transaction lifecycle draft → post → report, the roadmap sequence) and `<ul>` elsewhere.

### 4.7 Code blocks

Closes: `aeo:aeo_code_blocks` ("No code blocks — add `<code>`/`<pre>` for technical content"). Fails on eight of nine pages.

The audience installs software on a VPS; code blocks are the natural register and their absence is a genuine gap.

- **`/download/`** — checksum verification (`sha256sum`, `certutil -hashfile`), unzip, the `docker compose` one-liner, and the minimum `php -v` / MySQL version check.
- **`/product/`** — the scoped read API / MCP connection shape from the 0.2.1 release, as a short illustrative request and response. Keep it consistent with the shipped client matrix; do not invent endpoints.
- **`/support/`** — the exact diagnostic output to include in a support email (PHP version, MySQL version, error log excerpt).
- **Inline `<code>`** for `PHP 8.2`, `MySQL 8.4`, file names and paths across all pages.

Add `pre`/`code` styling to the CSS. No syntax-highlighting library — CSP forbids external scripts and it is not worth self-hosting one.

### 4.8 Collapsible FAQs on the pages that lack them

Closes: `aeo:aeo_faq`, `aeo:aeo_collapsible` ("No `<details>`/`<summary>`"), `aeo:aeo_qa_blocks`, `geo:geo_faq_schema`.

`/product/`, `/download/` and `/roadmap/` already do this correctly — front-matter `faq` array plus `<details>`/`<summary>` markup, mirrored by `build.mjs` into `FAQPage` JSON-LD. **Copy that exact pattern** to `/`, `/point-of-sale/`, `/about/`, `/guides/`, `/support/` and `/news/`.

The homepage already has `id="faq"` on the facts block but no Q&A content there — replace or supplement it with a real FAQ section carrying that id so existing `#faq` links keep working (`check.mjs` errors on broken anchors).

Five to eight questions per page, in the visitor's own words. Homepage candidates, all answerable truthfully today: *Is PHP Ledger really free?* / *What do I need to run it?* / *Does it work on shared cPanel hosting?* / *Does it send my data anywhere?* / *Can it do invoicing and receivables yet?* / *Is it ready for real books?* / *How is it different from a hosted accounting service?* / *What happens to my data if the project stops?*

Answer the awkward ones honestly. "Is it ready for real books?" gets a real answer about preview status, not a deflection.

### 4.9 Numbers, and where each one comes from

Closes: `geo:geo_statistics_density` ("Only 1 data point"), `geo:geo_numerical_density` ("Few data points with units"), `geo:geo_original` ("No original data signals"), `aeo:aeo_data_attribution` ("Statistics lack source references").

Verifiable figures that already exist and are currently not on the page: PHP 8.2+ / 8.3 recommended, MySQL 8.4, package 3.05 MB (3,054,692 bytes), the full SHA-256, 10 base currencies, hourly demo reset, four sample businesses, the count of report types, the count of guide walkthroughs, the release date, the AGPL/MIT split by version.

Attribute each one in text — "3.05 MB, from the published release manifest", "SHA-256 `65eca3c…`, published with the 0.2.1-preview package" — and link to the GitHub release where one exists.

**`geo_original` asks for "our research shows…" signals.** The only honest original data the project has is the sample worked examples under `/guides/` and the sample company figures. Present those as what they are: "a worked sample example, reconciled and published so you can check the arithmetic yourself" with a link. **Do not manufacture usage or adoption statistics.**

### 4.10 Voice: second person, first person, transitions

Closes: `seo:first_person` ("Only 0 first-person pronouns — add personal experience (E-E-A-T)"), `aeo:aeo_pronoun_density` ("Pronoun density: 0.9% — add more 'you/your'"), `aeo:aeo_conversational` ("Formal tone"), `seo:transition_words` ("1% sentences use transition words — aim for 20%+"), `seo:readability_score` (`/roadmap/`: "Readability: 22 (difficult)").

The current copy is deliberately spare and impersonal. Loosen it without turning it into marketing noise:

- Address the reader as **you** throughout. Target 2.5-3% pronoun density.
- Use **I** where the maintainer is genuinely speaking — `/about/`, `/support/`, `/news/` items. A first-person maintainer's note is the strongest E-E-A-T signal this project can honestly produce. Two or three sentences per page, not a blog voice.
- Add transition words (however, because, so, in practice, once, until, instead, which means) to roughly one sentence in five.
- `/roadmap/` reads at 22 on the readability scale. Split its long sentences, cut nested clauses, and move the jargon into the `/product/` glossary and link to it.
- `check.mjs` warns on two-fragment headings (`^[^.!?]{2,40}\.\s+[^.!?]{2,40}\.$`) and the banned-word list. Re-read §2.6 before editing copy.

### 4.11 Multi-perspective and a conclusion on every page

Closes: `geo:geo_multi_perspective` ("Single-perspective content — add 'on the other hand...'"), `geo:geo_conclusion` ("No conclusion — add recommendations or a verdict"), `geo:geo_trust` (partially).

This one suits the project's existing culture exactly. Add to each substantive page a short, plainly-titled section — **"When PHP Ledger is not the right choice"** — naming the cases where a visitor should use something else today: you need receivables and payables now; you need inventory and cost of sales; you need statutory tax filing; you cannot run PHP and MySQL hosting; you need audited, accountant-reviewed statements. Then a closing recommendation section: who should try the demo, who should download, who should wait for a later release and subscribe to `/news/`.

Being the source that says "not us, for this" is exactly what generative engines cite. It is also already how this project writes.

### 4.12 Quotes and trust signals

Closes: `geo:geo_expert_quotes`, `geo:geo_trust` ("Only 1/5 trust signals").

**Quotes.** Only two honest sources exist. Use `<blockquote cite="URL">` with visible attribution for both:

1. **The maintainer**, speaking on the record about a design decision — on `/about/` and in news items. Attribute by name and role, linked to `/about/`.
2. **Cited public statements from primary sources**, where they bear on the page's subject and the URL resolves. `docs/strategy/ERPNEXT-REVIEW.md` records Frappe's own published statement that ERPNext is overwhelming for small business; if and only if you can verify the source URL from that document, quote it with attribution and link. If the URL does not resolve, drop it.

**Invented quotes are a hard fail on this task.** If you cannot attribute it, do not write it.

**Trust signals.** Honest ones the site is not currently surfacing: the AGPL-3.0-or-later core licence with a link to `LICENSE`; the SHA-256 checksum next to the download; `SECURITY.md` and a visible security-contact line; `CODE_OF_CONDUCT.md`; the no-phone-home, no-licence-key guarantee stated plainly; the public repository and issue tracker; the privacy page's "no analytics, no third-party requests, no visitor data stored" statement, which is unusually strong and is currently buried. Surface these as a compact trust strip near the footer. **No badges implying certification or audit the project does not hold.**

### 4.13 Table of contents

Closes: `aeo:aeo_toc` ("No Table of Contents — add jump links").

Generate one in `build.mjs` from each page's H2 set (and H3 where the page is long), rendered as a nav list of anchor links under the Key takeaways block, on pages with four or more H2s. Every H2 needs a stable `id` — most already have one from `aria-labelledby`. `check.mjs` errors on duplicate ids and unresolved anchors, so generate ids deterministically and slug-collide-safely. Skip the TOC on `/404.html` and short legal pages.

### 4.14 Brand naming discipline

Closes: `aeo:aeo_entity_consistency` ("Brand name used inconsistently (3 partial vs 2 full)" / "mentioned only 1 time").

Write **PHP Ledger** in full on first mention in every section, and at least three times per page. Never "phpledger" in prose (the lowercase form stays only in URLs, package names and `alternateName`). Never "the Ledger" or "the app" where the brand name would do.

### 4.15 Figures, captions and responsive images

Closes: `geo:geo_image_captions` ("No `<figure>`/`<figcaption>`" — fails on `/download/`, `/support/`, `/roadmap/`, `/about/`, `/guides/`, `/news/`), `seo:img_responsive` ("Only 0% responsive images — add srcset" — fails on `/point-of-sale/`, `/download/`, `/support/`, `/roadmap/`, `/about/`, `/guides/`, `/news/`).

The homepage does both correctly. Bring the rest up to the same standard:

- Wrap every content image in `<figure>` with a descriptive `<figcaption>`. The caption describes what the screen shows and what the reader should notice in it — it is not a repeat of the `alt` text.
- Add `srcset` and `sizes` to every capture. Use `www/website/tools/prepare-images.py` to produce the 720w derivatives where they do not already exist, and update the image manifests (source path, dimensions, bytes, hash) as that tool does. `check.mjs` errors when `srcset` is present without `sizes`, and on missing `alt`, `width` or `height`.
- Keep `loading="lazy"` on everything after the first two images, and `fetchpriority="high"` only on the LCP image.
- Watch `budgetEager` / `budgetTotal` — adding derivatives can push a page over. Raise the budget in front matter deliberately if needed.

---

## 5. Page-by-page work

Everything in §4 applies everywhere. These are the additional, page-specific failures.

### 5.1 `/` — home (AEO 71, GEO 70)

- 652 words across 11 sections, five of them under 50 words. Take it to **1,000-1,300 words** by deepening the existing chapters, not by adding new ones.
- Add the FAQ section (§4.8) at `id="faq"`.
- Add `lastmod` and `"breadcrumb"` to the `jsonld` array.
- `geo:geo_intent_clarity` / `seo:title_h1_alignment` — see §6.2.

### 5.2 `/product/` (AEO 71, GEO 69)

Strongest page already; six FAQs and `faq` schema in place.

- Add the glossary `<dl>` (§4.5), the report table and the comparison table (§4.6), and the API/MCP code block (§4.7).
- `geo:geo_hierarchy_depth` — flat H2s; add H3 subheads under each feature block.
- `seo:keyword_in_url` — see §6.1.

### 5.3 `/point-of-sale/` (AEO 62, GEO 63) — second-weakest page

- `geo:geo_keyword_stuffing` — **"preview" at 4.4% density, 11 occurrences.** Rewrite using "0.2.1", "the current release", "this evaluation build", "the published package". Do the same sweep on `/news/`, which trips the same check.
- Thin sections (`aeo:aeo_section_depth`, avg 25 words). Expand each to 120-180 words.
- Add FAQ, `<details>`, `<dl>`, responsive images and figcaptions.
- `geo:geo_definitions` — define the POS terms: tender, change, basket, catalogue, linked journal.

### 5.4 `/download/` (AEO 75, GEO 73) — best page; take it to A+

- Add the `howto` JSON-LD and numbered `<ol>` install steps (§3.5, §4.6) — this also closes `geo:geo_structured_answers`.
- Add the specification `<dl>` and the requirements table.
- Add the verification code blocks.
- `geo:geo_case_study` — link the `/guides/` walkthroughs as worked examples of what you get after installing.
- Add `preconnect` to github.com via `headExtra`.

### 5.5 `/support/` (AEO 73, **GEO 60**) — biggest GEO gap relative to its AEO

- `geo:geo_expert_quotes`, `geo:geo_industry_terms`, `geo:geo_definitions`, `geo:geo_featured_snippet`, `geo:geo_schema_richness` all fail. Add a maintainer's note in first person (§4.10), define the support vocabulary (installation assistance vs training vs troubleshooting vs pilot), and add the diagnostics code block.
- Add a FAQ covering what support costs, what is in scope, response expectations, and what the form does and does not send. The no-data-sent behaviour is a trust asset — state it on the page, not only on `/privacy/`.
- `aeo:aeo_lists`, `geo:geo_list_variety` — add an `<ol>` of "what to send in your first email".
- Keep the existing no-JavaScript fallback and mailto behaviour intact; `check.mjs` restricts `mailto:` to `site.email` only.

### 5.6 `/roadmap/` (AEO 69, GEO 67)

- `seo:desc_length` — description under 120 characters. Rewrite to 140-158 (`check.mjs` hard limit is 160).
- `seo:readability_score` **22 — the worst on the site.** See §4.10.
- `geo:geo_definitions`, `geo:geo_featured_snippet` — open each roadmap stage with "X is …".
- Add the "In 0.2.1 / Not yet" table and an `<ol>` for the delivery sequence.

### 5.7 `/about/` (**AEO 58**, GEO 60) — weakest AEO on the site

- `seo:title_length` — "About the PHP Ledger project" is 28 characters; aim 30-60. Try "About PHP Ledger and the maintainer behind it" and align the H1 (§6.2).
- `aeo:aeo_segmentation` — only 2 content sections. Split into: what PHP Ledger is, who maintains it and their background, why it exists, how it is funded and licensed, what it deliberately does not do, how to get involved.
- `aeo:aeo_direct_answers`, `aeo:aeo_answer_box`, `aeo:aeo_section_depth` — apply §4.1, §4.2, §4.4.
- This is the page that carries `Person` credentials. Real background only: role, the Bixisoft context, what the maintainer has actually built. No invented credentials, awards or certifications.
- Add a FAQ and `<details>` blocks.

### 5.8 `/guides/` (**AEO 56, GEO 55**) — worst page on the site, fix it first

The index page is 256 words and 54% of its DOM is nav, header and footer.

- `seo:word_count` ("Only 256 words — thin content"), `geo:geo_content_length` ("AI systems prefer 300+"), `geo:geo_boilerplate` ("~54% nav/footer/header"). Take it to **700-900 words**: for each of the three walkthroughs, a self-contained 120-180 word section saying what question it answers, which figures it reconciles, and who should read it.
- `aeo:aeo_snippet_ready` ("First paragraph too short") — open with a 40-60 word self-contained paragraph.
- `seo:desc_length` — under 120 characters; rewrite to 140-158.
- Add an `ItemList` JSON-LD node listing the three guides in order (this also helps `geo_schema_richness`). Add it as a `GRAPH_PART` if you introduce it.
- Add FAQ, `<details>`, question headings, a comparison table of the three walkthroughs (period covered, entries, reports exercised), and figcaptions with `srcset`.
- `seo:keyword_in_url` — see §6.1.

### 5.9 `/news/` (AEO 62, GEO 61)

- `seo:h1_length` — 15 characters; aim 20-70. "Release news and project updates" rather than the current short form, and keep the title distinct from it (`check.mjs` errors on duplicate titles).
- `seo:desc_length` — under 120 characters.
- `geo:geo_keyword_stuffing` — same "preview" sweep as §5.3.
- `aeo:aeo_segmentation` — group the release list by version series with H2s, and add a 120-180 word "what changed across 0.1.x to 0.2.1" summary above the list.
- Each news item: confirm `article` JSON-LD carries `datePublished`, `dateModified` and the `Person` author reference, and that a visible `<time datetime="…">` appears in the body.
- Keep `/news/feed.xml` regenerating correctly.

---

## 6. Two things you must not decide yourself

### 6.1 URL slugs — do not change them

`seo:keyword_in_url` fails on `/product/`, `/support/`, `/guides/` and `/news/` ("URL lacks title keywords"). Renaming them to something like `/accounting-features/` would satisfy the check and would also break every published link, the sitemap, the canonical set, `llms.txt`, the wiki, the README and the 15 September publication receipt, in exchange for a low-weight check.

**Leave every URL exactly as it is.** Instead, align each page's title and H1 toward the words already in its slug. Record the trade-off in the final summary as an owner decision, with the redirect work it would need if the owner ever wants it.

### 6.2 The homepage H1 — OWNER DECISION

`seo:title_h1_alignment` fails on every page, and on the homepage it reports **14% word overlap**:

- Title: *PHP Ledger: open-source, self-hosted accounting software*
- H1: *Double-entry accounting that runs on your own PHP and MySQL hosting*

`www/website/README.md` records that this H1 is **the exact approved headline** from the "A, Workbench" redesign published on 15 September 2026. Do not change it.

Change the **title** instead, keeping the primary keyword and pulling in the H1's terms — for example: *Self-hosted double-entry accounting on your own PHP and MySQL hosting | PHP Ledger* (58 characters). Apply the same treatment on the other eight pages: rewrite the title toward the H1, never the reverse, and keep every title unique and 30-60 characters (`check.mjs` errors on duplicate titles).

Present the before/after title table for all pages in the final summary. **Apply the non-homepage titles; leave the homepage title change staged and flagged** — it is the approved headline's counterpart and the owner should sign it off.

---

## 7. Checks that will stay red, deliberately

State these in the final summary so nobody chases them later.

| Check | Why it stays failed |
|---|---|
| `geo_trust` (partial) | The scanner wants "reviews, certifications". The project has neither. §4.12 raises it as far as honest signals allow; the rest would require fabrication. |
| `aeo_video` | No video exists. External hosting is impossible under `default-src 'self'`; a self-hosted clip would blow the page budgets. Deferrable to a later release with its own budget decision. |
| `geo_case_study` (partial) | The sample `/guides/` walkthroughs are the only honest examples. No customer case studies exist. |
| `geo_original` (partial) | Only the sample worked examples qualify. No usage or adoption data exists and none will be invented. |
| `aeo_speakable`, `indexnow`, `geo_howto_schema`, `geo_faq_schema` | Implemented anyway because they are cheap and truthful, but note in the summary that Google retired FAQ rich results in May 2026 and deprecated HowTo rich results in 2023 — these are AI-context signals now, not search features. |
| `keyword_in_url` | See §6.1. Owner decision. |

Target after this work: **SEO 98-100, AEO 92-97, GEO 88-94** on the indexable pages. If a page lands short, say which checks still fail and why rather than adjusting copy to game a check.

---

## 8. Verification — required before you report done

1. `node www/website/build.mjs --check` passes with **zero errors**. Report the warning count before and after; it must not increase.
2. Every page's `budgetEager` / `budgetTotal` still passes. List any budget you raised and why.
3. Serve `www/website/public` on the local Compose service at `http://127.0.0.1:18201/` and run the existing Playwright checks: `www/website/tools/browser-smoke.cjs` and `www/website/tools/browser-interactions.cjs`. The mobile menu, Escape handling, capture dialog focus return, reduced-motion behaviour and the no-JavaScript support fallback must all still work. New `<details>` elements must be keyboard-operable and must not trap focus.
4. Validate every emitted JSON-LD graph. Confirm no `aggregateRating`, `review` or interaction count appears anywhere.
5. Confirm `/.well-known/ai.txt`, `/ai/summary.json`, `/ai/faq.json` and `/site.webmanifest` all return 200 with the right content type through the **Nginx** config, not just a plain static server — the `location ~ /\.` deny rule is the trap here.
6. Confirm `X-Frame-Options: DENY` appears on HTML, CSS/JS, images, and the robots/sitemap/llms paths — all five `add_header` sites.
7. **Write a local auditor**: `www/website/tools/aeo-geo-audit.mjs`, dependency-free Node, run over `www/website/public`. It reports per page: word count, boilerplate ratio, H2/H3 counts and depth, sections under 50 words, average words per section, count of answer paragraphs (40-200 chars) directly after a heading, question-heading count by type (what/why/how/when/who/which), pronoun density, transition-word ratio, `<dl>`/`<table>`/`<ol>`/`<ul>`/`<blockquote>`/`<pre>` presence, `<figure>`/`<figcaption>` count, `<time>` count, `srcset` coverage, brand-mention count, top term density (to catch keyword stuffing), and the JSON-LD `@type` set. It exits non-zero on the thresholds this prompt sets. This is how the site stays at its score after the next content edit; `check.mjs` cannot see any of it.
8. Optionally re-scan the live pages after publication — the scanner's own endpoint accepts `POST https://seoscore.tools/api/scan` with `{"url": "..."}` and returns the full per-check JSON. It only reflects the deployed site, so it verifies nothing before publication; the auditor in step 7 is what gates this work.

---

## 9. Commits

Group them so each is reviewable on its own. Do not push.

1. **Infrastructure** — `docker/website.conf` headers, `site.webmanifest`, hreflang, resource hints, `site.json` sameAs.
2. **Build system** — new `GRAPH_PARTS` (`webpage`, `person`, `howto`, `itemlist`), homepage breadcrumb, required `lastmod`, generated TOC, generated `llms.txt`, AI-discovery files, footer byline and `<time>`.
3. **CSS** — `<dl>`, `<pre>`/`<code>`, summary block, TOC, trust strip, footer meta. No inline styles.
4. **Content, weakest first** — `/guides/`, `/about/`, `/point-of-sale/`, `/news/`, `/roadmap/`, `/support/`, `/product/`, `/download/`, `/`.
5. **Titles and descriptions** — the §6.2 alignment pass, homepage title staged separately and flagged.
6. **Tooling and QA** — `aeo-geo-audit.mjs`, updated `www/website/README.md` (new files, the IndexNow step, the auditor command), updated `www/website/design-qa.md` with the before/after scores and the QA record.

For each commit, write a short summary and list every file changed.

## 10. Final summary to produce

- Before/after table of the per-page auditor metrics.
- The before/after title and description table for all pages.
- Every owner decision held back: the homepage title, the standalone `/compare/` page, URL slugs, Wikidata/LinkedIn company entity, video.
- Every check left red on purpose, with the reason.
- Every file changed, grouped by commit.
- Anything in this prompt you could not do without breaking §2, and what you did instead.
