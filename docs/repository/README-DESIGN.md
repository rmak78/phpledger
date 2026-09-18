# README visual package

Prepared 14 September 2026 for the repository README and linked Wiki work. This package contains the user's preferred existing horizontal logo and genuine local development captures. It does not establish that the revived application is on the default branch, publicly deployed, redesigned to the user's satisfaction or ready for live bookkeeping. The lead owns those publication checks and the README/Wiki copy.

## Recommended hierarchy

1. Center the preferred logo at approximately 420px wide, with a short plain-language purpose below it: **Clear, traceable accounting for small businesses.**
2. Show the compact development-preview badge and one concise explanation of the restart/default-branch boundary. Keep any runtime badges factual; do not add an unverified license, build or release badge.
3. Offer a short row of useful links: Getting started, Wiki, Roadmap, Contributing and Support. The lead should use the actual published Wiki destinations.
4. Lead product evidence with the full-width owner overview. The first screen communicates why an owner would use the application.
5. Place the expense/journal and cash-POS screens in separate expandable sections with readable captions. Their full-width images can then be inspected without making the README's opening excessively long.
6. Keep installation, current limitations and contribution guidance as normal accessible text. Photos, feature collages and more dashboard thumbnails would dilute the product evidence here.

Use native repository-relative images, not external image hosting or third-party status services. Avoid a row of three small screenshots: financial text becomes unreadable at GitHub's content width. Link each screenshot to its full image.

## Ready assets and captions

| Asset | Dimensions | Bytes | Suggested caption / alt text |
|---|---|---:|---|
| [Preferred horizontal logo](assets/phpledger-logo.webp) | 640 × 213 | 5,720 | PHP Ledger |
| [Development badge](assets/development-preview.svg) | 215 × 26 | 515 | Status: development preview |
| [Owner overview](assets/owner-overview-preview.webp) | 1425 × 1188 | 63,500 | Local development preview with sample books: cash, income, expenses and profit, with links to the reports behind them. |
| [Expense and journal](assets/expense-to-journal-preview.webp) | 1425 × 1106 | 68,930 | Local development preview: a posted USD 125 sample expense and its balanced journal, shown together with the source record. |
| [Cash POS](assets/cash-pos-preview.webp) | 1425 × 1188 | 74,268 | Early cash-POS preview with illustrative products and an unposted basket. Stock, tax, card processing and credit sales are not implemented in this screen. |

The four WebPs and badge total **212,933 bytes**. These are file sizes, not a network performance measurement. The logo's white canvas is retained exactly; GitHub's simple image rendering should use the intact asset. The application uses separate CSS framing, which is not baked into this copy.

The owner screen shows USD 1,000 income, USD 125 expense and USD 875 cash/profit. The POS basket shows a notebook and pen totaling USD 5.75, with USD 10 tender and USD 4.25 change. That screenshot was captured without posting the basket. All business names, people, transactions and figures are sample. The screenshots precede the latest narrow header-framing and country-selector changes; do not describe them as pixel-exact captures of the latest checkout. Reports/POS remain under design review.

Example logo block for the root README:

```html
<p align="center">
  <img src="docs/repository/assets/phpledger-logo.webp" width="420" alt="PHP Ledger">
</p>
<p align="center">Clear, traceable accounting for small businesses.</p>
<p align="center">
  <img src="docs/repository/assets/development-preview.svg" width="215" height="26" alt="Status: development preview">
</p>
```

Keep the preview caption outside each image so it remains readable to assistive technology and when images do not load. The package does not prescribe a new application layout or fonts.

## Provenance and rights

The four image copies were compared byte-for-byte with their sources; all matched. [manifest.json](assets/manifest.json) records exact sources, dimensions, byte counts and SHA-256 digests. No raster modification, retouching, recoloring, logo replacement or newly generated product image was performed in this package.

- Logo source: [existing website WebP](../../www/website/public/assets/brand/phpledger-horizontal.webp), derived earlier from the [preferred application PNG](../../www/phpledger/public/assets/brand/phpledger-horizontal.png). The bold PHP/lighter Ledger identity was retained by the user. [Brand guidance](../BRAND.md) records the interim identity and remaining master-artwork/provenance work; this package does not invent a separate license for the mark.
- Screenshot sources: [website capture provenance](../design/website/SOURCES.md) and unchanged original PNGs under [product-screens](../design/product-screens). The earlier local WebP derivatives are copied intact here. These are PHP Ledger's sample local screens, not seller or competitor images.
- Visible interface typography is Inter; the existing [font license](../../www/phpledger/public/assets/fonts/LICENSE.txt) remains in the repository. UI icons retain the [Tabler source and MIT license](../../www/website/public/assets/icons/SOURCE.txt). No font or icon binaries were duplicated into this package.
- The compact status badge is project-authored native SVG with system-font text and the existing navy/blue palette. It makes no build, security, jurisdiction or release certification claim.
- No photographs, CodeCanyon/Shopify/Square/Odoo imagery, outside customer data or new external media were added. Existing photograph licenses do not need to be repackaged because no photos are included.

## Optional social-preview candidate

[social-preview-candidate.svg](assets/social-preview-candidate.svg) is a self-contained **1280 × 640** native SVG composition. It embeds the identical preferred logo and owner-overview WebP bytes, places them on the existing navy palette and labels the content **Development preview** and **Sample data**. Text is native SVG using a system sans-serif fallback; this is not a new brand typography decision.

The upload-ready [PNG render](assets/social-preview-candidate.png) is **1280 × 640**, **66,922 bytes**, captured directly from Chrome at the exact native viewport. Its SHA-256 digest is `08ee3aac12948ff51ac78f360835c1f4fca9b1038abab46d040d10db625b969f`. Visual inspection confirmed the whole composition, readable logo and purpose, intact product evidence and both preview labels, without browser chrome, clipping or scrollbars. No post-capture raster editing or resizing was performed.

The asset is an optional publication candidate. It has not been set as the GitHub social preview. The original logo and screenshot are embedded intact, with only native SVG layout framing around the logo's white margin. The source SVG contains no remote references or scripts. The lead owns the final GitHub upload and rendering check.

## Validation and delivery boundary

Image dimensions, source-copy equality, SHA-256 digests, local links, JSON and SVG XML were checked. Every selected source image and the final Chrome-rendered social PNG were visually inspected. Chrome reported a 1280 × 640 viewport and SVG bounds; the saved PNG was independently checked as 1280 × 640 and below 1 MB. The lead still needs to check the actual GitHub README, social image and any Wiki rendering after publication.

Files in this workstream are limited to `docs/repository/assets` and this design note. No application, marketing website, root README, Wiki, route, schema or migration was edited. No secrets were exposed, Google Drive files read, external media fetched, account settings changed or publication performed by this workstream.

## Publication follow-up — 14 September 2026

The lead subsequently published the selected README media in main-repository commit `250b27d`, uploaded the PNG through GitHub Settings, and verified the custom social preview by both the visible saved artwork and API read-back. The README was inspected on GitHub at desktop and smaller widths; the Wiki has ten pages with sidebar/footer navigation. The earlier candidate-only statements above describe the asset-preparation handoff, not the current publication status. See [the publication receipt](PUBLICATION-2026-09-14.md) and [live visual QA](../design/website/qa/live-20260914/README.md).
