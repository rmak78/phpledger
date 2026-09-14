# Website composition comparison

Three homepage directions prepared on 2026-09-14 for the phpledger.com replacement. The website's goal is to help visitors explore PHP Ledger and begin a pilot conversation. These are generated visual proposals, not working pages or release evidence. The user selected **1, Field Notes**, and its implementation is in progress under `www/website/public`. Final live publication is separately authorized and remains the lead's responsibility after verification.

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
