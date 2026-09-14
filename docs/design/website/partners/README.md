# Company marks and contact update

Prepared 14 September 2026. The owner requested removal of the telephone number, the exact short address **Innovista Chenab**, and linked official company logos. The contact-only update was handed to the deployment workstream first and reported live in release `website-20260914-175932` at 17:59:40 UTC. The four-logo batch was then prepared separately so it did not delay that change.

The final footer includes **BixiTech, BixiSoft, BrownBag and Agency75**. The user identified the company family. No additional portfolio/client names or unverified product brands were included. The deployment workstream published the final static batch as `website-20260914-180337`. A subsequent live Chrome check verified the four local logo assets and exact outbound destinations at `https://phpledger.com/#sister-heading`, with no telephone link and the exact short address.

## Sources and artwork

The public [source record](../../../../www/website/public/assets/partners/SOURCES.md) lists the exact official pages and artwork URLs. Original PNGs remain in this folder; [sources.json](sources.json) records their hashes. The current official BrownBag site and company LinkedIn page confirmed `brownbag.pk`; its mark was visually compared with the same brown/green .pk identity in BixiSoft's wider rendition. The wider source is used for better footer legibility.

The [derivative manifest](../../../../www/website/public/assets/partners/manifest.json) records the four locally served WebPs, totaling **36,626 bytes**. Only proportional resizing and compression were applied, preserving the logo artwork, color and aspect ratio. No image generation, recoloring, reconstructed wordmark or external page-load media was used. These are company trademarks, not newly licensed open-source logo assets.

## Validation

- Official HTTPS pages and exact source assets were read successfully. No messages, forms or account changes were made.
- All five retained original files and four derivatives passed SHA-256, dimension and readable-image checks.
- Static HTML, local asset paths, anchors and CSS checks passed with zero errors. JavaScript was unchanged. The existing conservative file budget is 595,242 bytes; these four additional footer logos are lazy-loaded and add 36,626 bytes when reached. This is a file budget, not a network-timing measurement.
- Browser checks at 1440px, 768px and 320px showed no horizontal page overflow. All four marks loaded and linked to their intended sites; the mobile strip uses two columns. Tab navigation reached the next company link with a visible 3px focus outline.
- The DOM contained no telephone link and the address text was exactly Innovista Chenab. The public HTML/JS/Markdown scan found no removed phone or old detailed address remnants.
- [Desktop capture](qa/desktop.png), [tablet capture](qa/tablet.png) and [320px mobile capture](qa/mobile-320.png) preserve the local review. The final mobile image was explicitly positioned at the footer before capture.
- [Live mobile confirmation](qa/live-mobile-390.png): all four public logo images loaded, the versioned partner stylesheet applied, and the 390px viewport had matching 375px client/scroll widths. The phone was absent and location was exactly Innovista Chenab. This check was read-only; the deployment agent performed the live change.

Public files changed: `index.html`, `credits.html`, `assets/site.css` and `assets/partners/`. Documentation/source evidence changed only in this design folder and the public asset source record. No route, workflow, application, migration or schema changed. Raw secrets exposed: no. Google Drive reads: none. External calls: read-only research and official asset downloads. Live publication belongs to the deployment workstream.

## Final heading refinement

The owner subsequently requested **Companies that support our open-source initiative.** instead of a company-family heading. The visible heading, credits explanation and public source record were updated together and handed to deployment as a final copy-only patch. No logo, layout, runtime or link changed. Earlier captures document the preceding heading; they are not proof of the later wording. Static checks and whitespace checks passed after this refinement.
