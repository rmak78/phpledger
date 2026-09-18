# Website design decisions

## Published accounting starter website - 16 September 2026

The current website presents **0.4.0-preview**, with core AR/AP, optional Purchasing/Inventory and manually configured inclusive/exclusive tax. The [release article](https://phpledger.com/news/0-4-0-preview/) is live. Current capability assertions are synchronized throughout the existing long-form library; the seven earlier release articles retain historical scope. The approved homepage headline, typography, six-item navigation and visual style are unchanged.

The static release `website-redesign-20260915-233156` was published at **04:32 PKT on 16 September** (23:32 UTC on 15 September). All 165 files matched archive, host and public hashes; demo identity and headers were preserved. [The live receipt](qa/release040-live-publication.json) records these checks. The application and media ZIP were published separately on [GitHub](https://github.com/phpledger/phpledger/releases/tag/v0.4.0-preview), and their public downloads matched the release artifacts.

Five demo choices means one focused Accounting starter playground plus four existing multi-year teaching histories. No eleven-company expansion or new country account catalogues are claimed. The [content register](CONTENT-REGISTER.md) covers 64 source pages and 65 HTML outputs. The [launch kit](ACCOUNTING-STARTER-LAUNCH-KIT.md), [facts/FAQ/experiments](ACCOUNTING-STARTER-FACTS-AND-DEMO.md) and [actual screenshot assets](accounting-starter-media/README.md) provide the completed marketing handoff. No social post, email or press submission was sent.

## Historical design selection and publication context

The dated records below preserve earlier release facts. They establish the selected visual direction, not the current package version or starter capability boundary.

## Current selected direction — 15 September 2026

The owner's current selection is **A, Workbench, with B's photo treatment** from the [A/B design canvas](https://claude.ai/artifact/9KEqRSg3TL14tZSZVscuaF). The canvas contains home-page views at 1440 and 390 pixels and a download-page view for each direction. This decision supersedes the earlier Field Notes website selection preserved below.

The approved homepage headline is, verbatim:

> Double-entry accounting that runs on your own PHP and MySQL hosting

Use A's light, centred introduction, primary demo/download actions, large framed product capture and paper footer. Use B's full-width photographic story sections to connect the product to everyday business scenes. Website typography is locally served **Manrope for headings, Poppins for body copy and Inter for figures**. This website decision does not change the application's approved Review Console/Inter design.

The current [homepage source](../../../www/website/src/pages/home.html) already contains the exact headline and selected layout treatment. Use original licensed photographs and actual sample application captures; subjects are illustrative and are not presented as customers or endorsers. The task's fresh public check identifies [**v0.1.2-preview**](https://github.com/phpledger/phpledger/releases/tag/v0.1.2-preview), published 15 September 2026, as the latest release and **0.1.2-preview** on the live demo. Its downloaded ZIP was checked against its published checksum and GitHub digest; included installation, upgrade and release documents were read to ground current package copy. Existing screenshots retain their actual **0.1.0 development-preview** provenance and sample-data labels. The original 0.1.0 news post remains historical. Opening cutover, historical imports, bank reconciliation, AR/AP and tax are outside the verified package; local accounting additions are not presented as shipped capabilities.

Basic on-site SEO is implemented: page metadata and canonical links, social-preview metadata, structured data, robots, sitemap, llms and RSS. Google/Bing verification and indexing submissions, IndexNow setup, social handles/tokens and additional page-specific OG artwork remain pending. The campaign is separately tracked.

### SEO and campaign handoff status

The original Claude plan retains a separate Part 2 for search discovery and launch-week marketing. The website publication completed the on-site baseline. A fresh 15 September public audit verified GitHub's homepage, accounting-core description, planned topics and enabled Discussions; labels, issue cleanup and a pinned discussion still need their own receipts. The earlier claim that `tools/marketing-snapshot.ps1` was missing was incorrect: it is tracked and is now refreshed with public SEO checks and opt-in private traffic. The referenced private launch kit was not located, so a current [SEO and campaign execution kit](SEO-CAMPAIGN-EXECUTION.md) now supplies the high-priority queue and 0.1.3 release-gated copy. GSC/Bing account/DNS ownership remains unknown rather than proven absent. IndexNow preparation/submission, directory and social execution must be recorded individually; proposed budgets and traffic ranges are not reported results.

The owner explicitly prioritized publication first, then API/MCP reads, commands and optional AR/AP, with SEO discovery and marketing preparation in parallel. Google/Bing may crawl demo responses to observe their `noindex` directive; other configured bots remain excluded from temporary demo crawling. This intentional policy is documented in robots comments and the public audit.

The owner explicitly requested that the updated plan and website be **published live** on 15 September 2026. That request supersedes the plan's earlier owner-only publication restriction for this website. Publish the reviewed static site and required website routing/header configuration while preserving the separately hosted `/demo/`. Advertising, social posts, messages, external account setup and the later launch campaign remain separate pending work.

**Publication status:** published at [phpledger.com](https://phpledger.com/) on **15 September 2026, 08:08 UTC (13:08 PKT)** as `website-redesign-20260915-080700`. All 101 static files passed publication verification; all nine pages passed fresh browser checks at 1440, 768, 390 and 320 CSS pixels. Download and roadmap FAQs are complete. Demo entry/health stayed available with the same containers; the planned noindex header was added. See the [website QA report](../../../www/website/design-qa.md#live-publication-15-september-2026) and [publication receipt](qa/live-20260915-publication.json). The reference canvas was read through the browser; local `.dc.html`/PNG exports have not been verified.

## Historical composition comparison — 14 September 2026

Three homepage directions were prepared on 2026-09-14 for the phpledger.com replacement. The website's goal was to help visitors explore PHP Ledger and begin a pilot conversation. These were generated visual proposals, not working pages or release evidence. The user selected **1, Field Notes** at that time. The candidates, original recommendation and refinements below are retained as historical design context; the current A/B selection above is authoritative.

## Final candidates

The first three generated structures were displayed in this order. Candidate 2 was subsequently reissued with corrected product copy; use its final file below, not the superseded initial image.

| Candidate | Final visual | Structure | Trade-off |
|---|---|---|---|
| 1 | [Field Notes](01-field-notes.png) | Large real workplace photograph beside an introduction; full-width product walkthrough; supporting photo strip; navy pilot invitation | Best balance of human context and product readability. Keep the proof screenshot prominent and make the step control work. |
| 2 | [The Working Ledger](02-working-ledger.png) | Editorial margin rail; three-photo introduction; vertical annotated walkthrough; quieter pilot spread | Most unusual grid, but narrower copy and screenshot space require more responsive care. The generated headline overlaps its image edge slightly and the Kolkata caption sits under the wrong part of the photo group. Both must be corrected if selected. |
| 3 | [Open for Business](03-open-for-business.png) | Strong navy headline band; wide workplace photograph; photo/product chapter; contrasting invitation | Strongest visual opening, but the large hero pushes product evidence further down and the second photograph reduces screenshot width. |

**Recommendation: candidate 1.** It gives the real business scene a distinctive role while making the product story large enough to inspect. Its mobile reading order is straightforward: introduction, photograph, walkthrough, pilot invitation. This is design judgment, not measured conversion or usability evidence.

## Required implementation refinements

- Reproduce the selected layout using the original real photographs and actual application captures. Never extract reconstructed people or UI pixels from a generated composition as production assets.
- Use exact navy `#0C2052`, charcoal `#424242`, solid action blue `#4656E8`, and locally served licensed Inter. Generated pixels and typography are not exact token or font-rendering evidence.
- Present a user-driven three-step expense → entry → report walkthrough with an accessible enlarged view. Only show working screens after the sprint journey is implemented and captured. The attached Review Console reference remains a labelled design preview until then.
- Keep present capabilities separate from planned historical imports, localization, multicurrency/foreign-exchange accounting, multi-book support, tax adapters and ERP modules. Language, tax or FX coverage is not implied by the photography or currency display.
- Explain the pilot invitation and provide a deliberate email-draft action to `rmak78@gmail.com`, a copy fallback, and the approved contact details. Do not claim delivery, submit messages automatically, collect payments, or add tracking.
- Implement meaningful navigation and retain existing useful anchors. Add the full roadmap/support/contribution/contact content below the concise visual opening without turning it into repetitive feature cards.
- Use photo credits and illustrative-photo wording. Only the blue shop photograph has a verified Kolkata location; no place or customer affiliation is inferred for the other scenes.
- Verify desktop/tablet/mobile, keyboard, zoom, reduced motion, readable contrast, image loading/layout stability, and the agreed page-weight targets. Screenshot concepts satisfy none of those checks.

## Assets and evidence

- [Photography source and license record](SOURCES.md): three actual commercial-use stock photographs, source links, unchanged downloads, dimensions and hashes.
- [Complete prompts](PROMPTS.md): independent built-in calls and the second composition's correction.
- [Approved application direction](../../DESIGN.md) and [brand specifications](../../BRAND.md).

The first image is 912 × 1725 px, the final second 1024 × 1536 px, and the third 920 × 1710 px. The prompts requested a 1440-pixel-wide scrollable concept; the generator returned these smaller proportional canvases. They were not stretched or resampled to pretend the requested resolution was met. Implementation must use actual responsive dimensions and readable content rather than scale the bitmap into a page.

Visual inspection confirmed the three intended structures, supplied branding, recognizable source-photo scenes, and corrected pre-release copy. Candidate 2 retains the small layout/caption issues explicitly noted above. Image dimensions and local links were checked. No website code, routes, schema, migrations or production configuration changed in this visual phase. No Google Drive documents were read. External calls were source/license research, photo downloads and image generation; no messages, payments or publication occurred.
