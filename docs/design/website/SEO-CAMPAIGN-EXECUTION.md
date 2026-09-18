# SEO discovery and preview campaign execution

Publication update: 0.1.3 and the 0.1.4 layout correction are live. The website release `website-redesign-20260915-123255` includes the corrected crawler summary, download and new 0.1.4 article. IndexNow returned HTTP 202 for the initial ten URLs, then HTTP 200 for six changed 0.1.4 URLs; receipt does not establish indexing. Public release/news/RSS publication and discovery submission have started; social/ad execution and Google/Bing account verification remain pending. The 0.1.3 feature copy below remains a feature announcement; any new post should link to the current 0.1.4 download.


**Priority: high, parallel to product development. Updated 15 September 2026.**

The owner requested that completed work go live first and that discovery and marketing start in parallel. Product implementation keeps its approved order: **API/MCP read access → controlled commands → optional AR → optional AP**. This kit does not move subledgers ahead of that order.

**Campaign target: 0.1.3-preview.** The release lead owns its publication and live verification. The baseline below was captured while **0.1.2-preview** remained the verified public package. Use the version-specific copy only after the 0.1.3 release, checksum, website, Wiki and demo have matching publication receipts. “Draft ready”, “submitted”, “published” and “indexed” are different states.

## What was found and started

The original Claude plan is retained privately at `C:/Users/dearm/.claude/plans/https-phpledger-com-we-need-a-structured-tarjan.md`. Its website work and Part 2 campaign are separate tracks. Its old dates and 0.1.0 promotional copy are superseded by this execution kit; historical release announcements and screen captures keep their actual provenance. Private budgets, traffic targets and account material stay outside the public repository.

**Correction to the earlier handoff:** `tools/marketing-snapshot.ps1` already existed and was tracked at commit `91e5c9a`. It was not missing. Its default still targeted 0.1.0 and its first implementation always read private GitHub traffic. The refreshed script follows the configured website release unless a tag is supplied, reads public discovery surfaces by default, and preserves private traffic collection behind `-IncludePrivateTraffic`.

### Direct public baseline — 15 September 2026, 11:56 UTC

| Surface | Fresh evidence | Meaning / follow-up |
|---|---|---|
| Marketing pages | All **nine** sitemap URLs returned 200. Each had a title, description, exactly one H1, matching canonical, OG image URL and parseable JSON-LD. | Basic discoverability is working. This is not a rich-result eligibility or indexing receipt. |
| Homepage | Approved “Double-entry accounting that runs on your own PHP and MySQL hosting” H1 and current self-hosted-accounting title. | A search-tool cached copy still showed the old site; direct HTTP was used for the baseline. Cached search excerpts do not establish current server content. |
| Crawler files | `/robots.txt`, `/sitemap.xml`, `/llms.txt` and `/news/feed.xml` returned 200. Sitemap and RSS XML parsed. A unique missing public URL returned 404. | Recheck after publication, especially release/news URLs. |
| Demo | Read-only `/demo/health` returned 200 with `X-Robots-Tag: noindex, nofollow`. Demo URLs are absent from the sitemap. | No demo visitor was started by this audit. Release QA must also inspect the actual demo entry and application responses. |
| GitHub | Correct project homepage, accounting-core description, all 20 planned topics, and Discussions enabled. | These settings are already present. Do not rerun the old setup checklist as if none existed. Labels, moderation, pinned discussion and private vulnerability-reporting settings need their own account-side evidence. |
| Package | Public `v0.1.2-preview` prerelease with named ZIP/checksum assets; published 15 September at 00:13:40 UTC. | The baseline script checks metadata/asset presence; the release lead separately verifies actual package bytes and deployment. |
| Google/Bing verification | No verification meta tags in any of the nine pages; source verification fields were empty. | **Account/DNS verification unknown**, not proven absent. Check the existing owner's property before creating another one. |
| IndexNow | Source key was empty. | Key publication and URL submission pending; no submission was sent. |
| Social channels | Website social URLs and handle fields were empty. | Do not invent handles, `sameAs` identities, or scheduled posts. |

Ignored machine-readable evidence: `.cache/marketing/raw/20260915T115625996Z-7ade3083/public-seo.json`, plus public GitHub read responses in the same directory. No adoption numbers or raw metrics are published here.

### Intentional crawler policy

Googlebot and Bingbot may fetch the demo so they can see its **noindex** response. The general and named AI-crawler group excludes `/demo/` from crawling; the marketing site remains allowed. Keep the robots comment consistent with those different rules.

An initial audit compared the implementation to the older “block every bot from the demo” wording and flagged the Google/Bing group. After checking the actual indexing goal, the release lead chose to retain Google/Bing access plus explicit noindex and correct the wording. The audit now tests that intentional policy. Google explains that a blocked crawler cannot see noindex, and that specific user-agent groups are not merged with the wildcard group. Neither robots configuration nor this HTTP audit proves search-result removal. [Google noindex guidance](https://developers.google.com/search/docs/crawling-indexing/block-indexing), [robots group precedence](https://developers.google.com/crawling/docs/robots-txt/robots-txt-spec).

## High-priority execution queue

| Priority | Action | Completion receipt |
|---|---|---|
| P0 | Publish and verify the release, website, documentation and demo as one delivery. Add a separate 0.1.3 news article; preserve the original 0.1.0 article/captures. | Matching release/package/checksum/version links and fresh demo checks, recorded by the release lead. |
| P0 | Rerun the public snapshot for `v0.1.3-preview` after that delivery. | Successful timestamped run with current sitemap/news and matching release assets. |
| P1 | Confirm existing GSC/Bing property access; complete ownership verification if needed. | Property, method, actor and completion time recorded privately; no token copied into a report. |
| P1 | Submit the canonical sitemap; inspect the homepage, product, download and new release article. | Submission receipt and each URL's actual inspection status. |
| P1 | Publish an IndexNow verification key through the existing site builder and submit changed canonical marketing URLs. | Public key-file verification plus submission response/time/URL list. A 200 or 202 is a submission outcome, not indexing proof. |
| P1 | Verify owned social handles and prepare the first release post with the actual current capture. | Account/handle, exact approved copy/asset, actual published URL and timestamp. |
| P2 | Submit the directory/editorial kit where current rules accept development previews. | One receipt per destination; distinguish submitted from accepted/published. |
| P2 | Review early user feedback and search coverage alongside the next product sprint. | Reproducible feedback issues, indexing findings and channel decisions. |

This workstream has started with the live public audit, corrected handoff, executable snapshot and current campaign copy. Search-console/account actions, submissions, social messages and advertising have **not** been executed by this workstream. Account access and channel identities are concrete prerequisites; proposed advertising terms remain private and need an actual account-side campaign configuration before spending.

## Search discovery runbook

### Google Search Console

1. Open the existing property, if any, for `https://phpledger.com/`. Confirm which account owns it before creating a duplicate.
2. A URL-prefix property can use the existing builder's `verification.google` field or the exact provided verification file in `www/website/src/static/`. A domain property requires DNS verification. Preserve existing owners' verification material. Verification files/tags must remain publicly available after acceptance. [Google ownership methods](https://support.google.com/webmasters/answer/9008080).
3. Build and publish through the normal website lane. Confirm the exact file/tag is available without authentication, then complete verification in the owning account.
4. Submit `https://phpledger.com/sitemap.xml` through the Sitemaps report. Inspect `/`, `/product/`, `/download/` and the new release article; request indexing where the account offers that action. Keep sitemap dates tied to meaningful page changes. Sitemap submission helps discovery but does not guarantee crawling or indexing. [Google sitemap guidance](https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap).
5. Record coverage/canonical/noindex findings rather than promising every URL will be indexed by a fixed date. Search appearance and title refresh can lag the live site.

### Bing Webmaster Tools

Use the current owner account. Import only this verified GSC property if the owner chooses that connection; Bing requests access to Search Console for the import and periodic validation. Alternatively verify directly using the supported file, tag or DNS method. The existing `verification.bing` field supports the meta-tag route. Submit the canonical sitemap, inspect the priority URLs and record actual results. [Bing verification and GSC import](https://www2.bing.com/webmasters/help/add-and-verify-site-12184f8b).

### IndexNow

The existing website generator supports `site.indexNowKey` and emits the matching root text file. Create one valid key for this domain, publish it through that existing mechanism, and confirm an unauthenticated GET returns the exact expected content before submission. Use only changed public canonical URLs on `phpledger.com`; exclude `/demo/`, private routes and GitHub URLs.

Prepare a JSON payload with `host`, `key`, `keyLocation` and `urlList`, then submit once through an IndexNow endpoint. Retain the response and handle validation/rate-limit errors before retrying. The snapshot script deliberately contains no submission command. [Official IndexNow protocol and response codes](https://www.indexnow.org/documentation).

### AI-search and identity

Keep the same factual entity description, official URL, repository and verified organization links across the site and documentation. Keep `llms.txt` version/scope current. The file is an additional readable reference; it is not evidence that an AI service has indexed, cited or recommended the product. Add social identities to structured data only after their ownership and URLs are verified. Additional page-specific OG artwork is a useful editorial follow-up, not a substitute for functional pages and accurate claims.

## Campaign message and links

**Entity sentence:** PHP Ledger is open-source, self-hosted double-entry accounting software for small businesses, built on PHP 8.5 and MySQL 8.4 and currently in development preview.

**Short bio:** Self-hosted accounting on PHP and MySQL. Trace entries, review balances and explore the sample demo. Development preview.

**0.1.3 scope for release copy:** core accounting and CSV reports; account statements with opening, running and closing balances; journal review and cumulative debit/credit totals; opening CSV cutover with an unpaid-document reconciliation register; period administration; bank CSV reconciliation; optional bundled POS enablement with retained receipt history.

**Keep these boundaries visible where relevant:** customer/vendor operational subledgers and invoice/bill settlement are future AR/AP work. An unpaid-document cutover register is not those modules. Country-neutral reports are available for review; no independent accountant sign-off or regional compliance is claimed. API/MCP reads come next, then controlled commands. Tax, inventory/COGS, FX posting, offline checkout and card payments are not part of this preview.

| Use | Destination |
|---|---|
| General discovery | `https://phpledger.com/` |
| Feature walkthrough | `https://phpledger.com/product/` |
| Evaluation download | `https://phpledger.com/download/` |
| Sample hands-on demo | `https://phpledger.com/demo/` |
| Version-specific release | `https://github.com/phpledger/phpledger/releases/tag/v0.1.3-preview` — publish first |
| Technical conversation | `https://github.com/phpledger/phpledger/discussions` |

Social links may use `utm_source=linkedin|x|facebook|instagram|youtube`, `utm_medium=social`, `utm_campaign=preview-0-1-3`, and a descriptive `utm_content` such as `expense-to-journal`. Use clean canonical URLs for directory entries and HN. UTM parameters are labels, not an installed analytics system; no client tracking or conversion attribution is claimed.

## Ready-to-use copy deck

All version-specific text below is a **draft for the verified 0.1.3 publication**, not a record of a sent post. Use the correct owned account voice and leave out any claim whose release receipt is still pending.

### LinkedIn: release and product sequence

**Post 1 — release**

> PHP Ledger 0.1.3-preview is available for evaluation. It runs on your own PHP and MySQL hosting, with a core accounting workflow you can trace from source to journal to report.
>
> This preview brings together opening cutover, period controls, bank reconciliation and CSV reports. Account statements show opening, running and closing balances; journal review shows cumulative debits and credits. Optional POS can be enabled per company while existing receipts remain readable after disablement.
>
> Try the sample demo or download the package: https://phpledger.com/download/
>
> We would like feedback on the bookkeeping tasks you can finish and the screens that need clearer explanations. It remains a development preview; accountant review and jurisdiction-specific reporting support are still open work.

**Post 2 — accountant/bookkeeper review**

> A useful account statement should make its arithmetic easy to follow: opening balance, each movement, running balance and closing balance, with a link back to the entry.
>
> That is the review path in the PHP Ledger preview. Opening balances, bank reconciliation and reports use the same company and book scope. Corrections retain their original entry through a linked reversal.
>
> If you keep or review books, try a sample example and tell us where the evidence or presentation is missing: https://phpledger.com/product/
>
> These are country-neutral reports available for review. Customer/vendor subledgers and reviewed regional reporting remain later work.

**Post 3 — optional modules**

> The accounting core should remain usable when optional features are disabled. PHP Ledger now has company-level module controls for its bundled cash POS showcase.
>
> An owner enables it deliberately. Disabling it stops new checkout while preserving existing receipts and accounting history. The showcase does not calculate tax, manage stock or process cards.
>
> Explore the current workflow: https://phpledger.com/point-of-sale/

**Post 4 — developer contribution**

> PHP Ledger uses small typed PHP functions, MySQL transactions and one central posting service. The next milestone is scoped API/MCP read access, followed by controlled commands and then optional receivables/payables.
>
> We are looking for concrete feedback on installation, access isolation, report reconciliation and the clarity of the UI. Please use sample examples and sanitized logs.
>
> Source and contributor guidance: https://github.com/phpledger/phpledger

### X: short post and five-part thread

**Short post:** PHP Ledger 0.1.3-preview: self-hosted accounting on PHP/MySQL, with account running balances, opening cutover, period controls, bank reconciliation and CSV reports. Explore sample books: https://phpledger.com/ Development preview.

1. PHP Ledger is a self-hosted accounting project built on PHP and MySQL. The 0.1.3 development preview is ready to explore with sample books: https://phpledger.com/
2. Follow an account from its opening balance through each movement to the closing balance. Source links help explain the numbers; journal review shows cumulative debits and credits.
3. Opening cutover, period administration, bank reconciliation and CSV reports now sit alongside the central posting and linked-reversal workflow.
4. Optional cash POS is enabled per company. Existing receipts remain readable after disablement. Tax, inventory/COGS, credit sales and card processing are outside this showcase.
5. Next: API/MCP reads, controlled commands, then AR/AP. Accountant and user review remain open. Try it and share a concrete workflow problem: https://github.com/phpledger/phpledger/discussions

### Facebook

> We have published a new PHP Ledger development preview for people who want to keep their accounting software on their own hosting.
>
> Try the sample books, follow an account's running balance, review the journal behind an entry, and explore the reports. The preview also includes opening cutover, period controls and bank reconciliation.
>
> Use the fictional demo data while evaluating it. Customer/vendor ledgers and regional reporting review remain future work.
>
> Start here: https://phpledger.com/product/

### Instagram carousel: four 1080 × 1350 panels

Use actual release captures, the supplied logo and the existing brand colors. Keep “Development preview · Sample data” readable on every capture. These dimensions are production instructions, not a claim that new artwork has been generated.

1. **Follow every movement** — Account statement capture with opening, running and closing balances.
2. **See the entry behind it** — Journal capture with cumulative debit/credit totals and source link.
3. **Reconcile the period** — Bank reconciliation/report capture with its actual state visible.
4. **Try the sample books** — Product/demo address and a concise development-preview label.

**Caption:** Explore PHP Ledger's accounting preview on your own PHP and MySQL hosting. Follow account movements, review journals and reconcile the period with sample books. API/MCP access is next; customer/vendor subledgers follow later. Product and demo: phpledger.com. #OpenSource #SelfHosted #Accounting #PHP

### YouTube: approximately 60-second narration

> This is PHP Ledger, a self-hosted accounting development preview built on PHP and MySQL. These books contain fictional data.
>
> Start with an account statement. The opening balance, each movement and the running balance explain how we reach the closing figure. Open a movement to see its source and the balanced journal. Cumulative debit and credit totals make the entry easier to inspect.
>
> Opening cutover, period controls and bank reconciliation help connect the start and end of a bookkeeping period. Reports can be exported as CSV.
>
> The optional cash POS showcase shares the posting service. It does not manage stock, calculate tax or process cards.
>
> Try the sample demo or evaluation download at phpledger.com. API and MCP read access come next. Accountant review and customer/vendor subledgers remain further work.

**Shot order:** 0–8s identity/preview label; 8–23s account statement and source; 23–36s journal and reconciliation; 36–46s report export; 46–54s optional POS boundary; 54–60s demo/download. Capture the deployed version after release; keep identifiers sample and leave the actual version label visible. Add captions and a transcript.

**Description:** PHP Ledger 0.1.3-preview walkthrough using fictional books. Download, requirements and limitations: https://phpledger.com/download/ · Demo: https://phpledger.com/demo/ · Source: https://github.com/phpledger/phpledger. This is a development preview, with country-neutral reporting and later API/MCP and AR/AP work.

## Community and directory kit

**Directory title:** PHP Ledger

**Short description:** Self-hosted double-entry accounting on PHP and MySQL, with traceable journals, account statements, bank reconciliation and CSV reports. Development preview.

**Long description:** PHP Ledger is an open-source accounting project for customer-owned PHP 8.5 and MySQL 8.4 hosting. The preview includes company/book scope, receipts and expenses, general journals, linked reversals, account statements, opening cutover, period controls, bank CSV reconciliation and core reports. A bundled optional cash POS showcase shares the posting service. New project-owned code is MIT licensed; dependencies keep their own terms. A temporary sample demo and evaluation package are available. API/MCP access, operational AR/AP, tax and inventory remain future work. Regional accountant review is not complete.

| Destination | Execution path and current state |
|---|---|
| AlternativeTo | The [add/edit entry route](https://alternativeto.net/manage-item/) currently redirects to sign-in. Search for an existing PHP Ledger entry before adding one; confirm the correct repository because the name has historical collisions. No submission made. |
| selfh.st | The current [submission form](https://selfh.st/submit/) offers newsletter and app-directory choices. Provide the live demo, installable package and preview scope. No submission made. |
| PHP Weekly | Use the publication's current [official site](https://www.phpweekly.com/) to confirm its editorial submission contact. The old guessed `/submit-news.html` route was not verified; do not send to an invented address. |
| SaaSHub, LibHunt, OpenSourceAlternative.to, php[architect] | Retained as candidate channels from the original plan. Verify current submission forms, existing listings, development-preview eligibility and any charge before action. No account/form inspection or acceptance is claimed here. |
| awesome-selfhosted | Retain as a later candidate and check its [current data-repository contribution rules](https://github.com/awesome-selfhosted/awesome-selfhosted-data) before proposing an entry. The original plan's January 2027 date is a reminder to recheck, not a guaranteed eligibility date. |

**Editorial pitch draft:** I maintain PHP Ledger, an open-source accounting preview on PHP 8.5 and MySQL 8.4. Its current release adds reviewed opening-cutover workflows, bank reconciliation and company-level optional-module controls to the core journal/report path. A sample demo and installable evaluation package are available at https://phpledger.com/. API/MCP reads are next; AR/AP, tax and inventory remain future work. If development-stage tools fit your publication, the release notes and source are linked from the download page.

**Show HN title draft:** Show HN: PHP Ledger, self-hosted accounting on PHP and MySQL

**First-comment draft:** I maintain PHP Ledger. I wanted a small accounting core whose entries can be followed from source to journal to report, with optional modules layered on it. The current preview supports sample evaluation with an installable package and a no-registration demo. The newest work covers running balances, opening cutover, period controls, bank reconciliation and module lifecycle. It is not a regional compliance package, and tax, inventory and operational AR/AP are still ahead. I would value feedback on installation, the traceability of the numbers and the workflows that feel incomplete.

Check whether a prior Show HN exists and whether the current work meets the [Show HN guidelines](https://news.ycombinator.com/showhn.html): a usable project, maker available for discussion, no coordinated upvotes, and no routine point-release announcement presented as a new project. Verify demo capacity before choosing a time; publication popularity and uptime are not guaranteed.

**Reddit/community draft:** I maintain PHP Ledger, a PHP/MySQL accounting development preview. I am sharing it for feedback on [installation and code structure / self-hosting and backups / contributing to an accounting core]. It has a sample demo and downloadable package. The current scope includes journals, running account balances, opening cutover, bank reconciliation and CSV reports. API/MCP reads are next; customer/vendor subledgers, tax and stock remain future work. Source and limitations: https://github.com/phpledger/phpledger. What would make this evaluation more useful in your workflow?

Adapt the bracketed topic to each community and check its rules, promotion frequency and megathread requirements on the actual day. Do not cross-post identical text on a timer. No posts or moderator messages were sent.

**Technical article outline:** why one central posting service; trace a sample expense through a journal and statement; fixed-precision amounts and linked reversals; opening/cutover and period reconciliation; optional-module disablement with retained history; what API/MCP reads must preserve; evaluation instructions and explicit remaining gates. Publish one original article first; follow the destination's canonical/cross-post policy before reuse.

## Launch sequence and measurement

Use **Day 0 = verified 0.1.3 publication**. These are sequencing recommendations, not scheduled tasks or promised publication dates.

| Window, Pakistan time | Focus |
|---|---|
| Day 0 | Finish publication receipts; rerun public discovery checks; record baseline; complete owned search prerequisites as access permits. |
| Day 1, 10:00–12:00 | Release post on verified owned channels; respond to questions with actual scope and links. |
| Day 2, 10:00–18:00 | Accountant/bookkeeper walkthrough; technical article draft; inspect search-console processing. |
| Day 3 | One fitting directory/editorial submission per destination, with receipts; review any unexpected load or errors. |
| Day 4 | Captioned video and optional-module walkthrough. Consider HN/community posting only with rules checked and the maintainer available. |
| Day 5 | Developer/contributor post and concrete feedback triage. |
| Days 6–7 | Review search coverage, failed installs, reproducible reports and substantive conversations; decide what to continue. |

Paid creative can reuse two verified sample screen stories: account-to-journal for developers and account/report review for bookkeepers. Prepare the actual post/ad identity, destination, placement-compatible assets and account-side terms first. No budget, spend, guaranteed clicks, fabricated adoption, endorsements or tax-compliance claims belong in this public kit. No paid campaign has been activated.

### Read-only snapshot commands

```powershell
# Public checks; current target comes from www/website/src/site.json.
./tools/marketing-snapshot.ps1

# Explicit release comparison after publication.
./tools/marketing-snapshot.ps1 -ReleaseTag v0.1.3-preview

# Optional maintainer-only read of GitHub traffic; ignored private output.
./tools/marketing-snapshot.ps1 -IncludePrivateTraffic
```

The script uses read-only GitHub REST/GraphQL queries and public HTTP GETs. It saves aggregate metrics under ignored `.cache/marketing`, timestamps each run uniquely, preserves the old CSV and writes the revised schema to `snapshots-v2.csv`. It checks a bounded same-host sitemap, page metadata, crawler policy, demo health noindex, feed and 404 behavior. It follows configured release metadata rather than treating GitHub's stable-only “latest” endpoint as the latest prerelease. Absent or failed sources remain blank with a nonzero exit; omitted private traffic is intentionally blank. A missing previous-day traffic bucket is unknown, not zero. Issue search caps are visible. [GitHub traffic endpoint requirements](https://docs.github.com/en/rest/metrics/traffic?apiVersion=2022-11-28).

Compare snapshots at comparable UTC times. GitHub rolling-window unique counts cannot be added to obtain unique campaign visitors. Release asset downloads are not verified successful installations. Social clicks, UTM arrivals, demo starts and qualified conversations are different measures. This script installs no analytics, reads no hosting access logs and creates no automated schedule. Keep private metric exports and any later anonymized server-log analysis outside the public repository.

## Execution receipt and limits

- **Files changed:** this kit and `tools/marketing-snapshot.ps1`.
- **Validation:** Windows PowerShell syntax passed; five focused parser probes passed (specific robots group, demo exclusion, homepage allow, wildcard group, HTML entity decoding). Public snapshot rerun against 0.1.2 returned exit 0: nine sitemap pages, no findings under the intentional crawler policy.
- **External reads:** public phpledger.com marketing URLs/crawler files/health/404; GitHub public repository, release, discussion-count and issue-number queries; the official Google, Bing, IndexNow, GitHub, HN and publication/directory reference pages linked above. Search-engine cached copies were not used as current deployment proof.
- **Not performed:** private traffic read, search-console login/verification/submission, IndexNow submission, account edits, directory submissions, messages, posts, artwork generation, ad activation, spend or new analytics.
- **Google Drive documents:** none required or read. Local repository instructions, architecture, roadmap, website design/handoff/build files and the original Claude plan were read.
- **Migrations:** no. **Schema changed:** no. **Raw secrets exposed:** no. **External/live calls:** yes, read-only. **Live/production changed by this workstream:** no; release publication is owned and reported separately by the release lead.
- **Remaining checks:** actual search-console ownership/indexing, current social identities and account access, after-release snapshot, release-specific media, destination-specific submission rules, actual publication/submission receipts and observed campaign performance.
