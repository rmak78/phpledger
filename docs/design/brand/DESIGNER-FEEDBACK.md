# Revised logo: review and designer feedback

Reviewed 2026-09-14. **Verdict: retain the book/P concept and refine the delivery.** It is a relevant, recognizable starting point for PHP Ledger. A replacement concept is unnecessary. The current submission needs a more intentional wordmark, working small-size variants, and complete production specifications before final acceptance.

This is a review recommendation, not approval of the finished identity or permission to contact the designer. No message has been sent.

## What was supplied and what is verified

- [Revised designer reference](logo-designer-reference.png): unchanged copy of `codex-clipboard-9bec3785-7699-4595-86b8-eccf2c792d1a.png`, 500 × 500 pixels.
- Source/copy SHA-256: `883a05e346fc6359a65f90abf5c2bd810ae5705b5723a5331db28759cd441eab`.
- Designer-stated main colors: **navy `#0C2052` and charcoal `#424242`**. These exact supplied values supersede approximate raster sampling for those two roles; they do not specify the visible blue/violet gradient.
- Designer-stated font names: **“portland” and “poppins.”** The intended assignment, exact Portland family, foundry, weight, version, and license are unverified. The raster alone does not prove which font files made the wordmark.

## Design assessment

The book pages and P silhouette communicate an accounting product without a separate explanatory symbol. Keep their relationship. The dark spine gives the mark a useful anchor, while the blue/violet area adds recognition. Do not force the entire application to use gradients merely because the mark has shading.

The current wordmark reads as **PHPLedger**. The widely spaced PHP and tighter Ledger create an uneven rhythm; the color change separates the words more than the spacing does. Ask for deliberate spacing between PHP and Ledger, a balanced weight relationship, and optical alignment with the mark. Preserve the product spelling **PHP Ledger** in ordinary text. The final spacing is a designer refinement, not a numerical kerning value inferred from this raster.

| Use | Assessment and required refinement |
|---|---|
| App header | The supplied stacked composition consumes too much vertical space for a compact application header. Supply a horizontal mark + wordmark lockup and show it at its intended header size. |
| Favicon and collapsed navigation | The mark can work independently, but the thin page gaps and three spine strokes may merge when reduced. Supply a small-size variant and demonstrate 16, 24, and 32 px. These sizes have not yet been rendered or tested. |
| Subtle product footer | Supply a compact horizontal or wordmark-only version so PHP Ledger remains legible when a business's own logo leads the workspace. |
| Monochrome and dark surfaces | A flat one-color and reversed version are missing. They must retain the P/book distinction without depending on the gradient or gray shading. |
| Documents and invoices | Use a clean vector version with restrained placement and a black-only proof. Customer-facing documents should principally carry the business's identity under the proposed branding policy; the PHP Ledger logo must not compete with it. That policy is still proposed. |

The raster has wide outer margins and no transparent background. It is a review image, not an app-ready master. Some dark wordmark pixels are close to `#141414`, rather than the stated `#424242`; sampling a supplied raster cannot establish the intended vector color. Ask the designer to make the master and palette sheet agree.

## Colors and typography

Both supplied main colors work well for readable text on light backgrounds. Locally calculated solid-sRGB contrast is **15.62:1** for navy on white and **10.05:1** for charcoal on white; against `#F7F7F7`, the ratios are **14.58:1** and **9.38:1**. These checks apply to the exact solid pairs, not every pixel of the logo or every future UI state.

The palette is incomplete for product use. Request the exact blue/violet accent values, gradient stops and direction, and a flat-color substitute for the mark. The interface also needs defined action, hover, focus, selected, disabled, and semantic state colors. Those are part of the product design system; they should not be guessed from the two dark main colors or change when a business picks an accent.

Poppins is a credible brand/headings choice. Google's published description identifies a geometric sans-serif with Latin and Devanagari designs; that is a family description, not a finding that it is optimal for dense accounting tables. [Google Fonts: Poppins description](https://raw.githubusercontent.com/google/fonts/main/ofl/poppins/DESCRIPTION.en_us.html).

For ledger tables, the exact font build matters. The official Google Fonts **Poppins Regular** file inspected during this review has proportional default digits: at a 100 px font size, `1` advances 32 px, `2` 57 px, and `0` 63 px. Its GSUB feature list contains no `tnum` feature. Therefore, setting `font-variant-numeric: tabular-nums` cannot be relied upon to produce equal-width digits with this inspected build. Keep Poppins available for branding and headings; pair financial tables with a font whose lining, tabular digits have been verified, or obtain and test a suitable font build. Right-align amounts and use consistent decimal precision regardless. [Inspected Poppins Regular source](https://github.com/google/fonts/blob/main/ofl/poppins/Poppins-Regular.ttf).

The inspected font SHA-256 is `7e65201e9b79159e2300267cc885e16c8dcef2424cdfa09a29bfb0980a94a7ba`. This evidence is specific to that file, not a claim about every version or weight. No browser rendering or designer-supplied font was tested. Compare actual labels and tables at normal working sizes before fixing the UI font. Include currency symbols, decimal separators, negative amounts, invoice references, and ambiguous characters such as `0/O` and `1/I/l` in the proof. Later Urdu/Arabic support needs a separately verified script/fallback choice; Latin/Devanagari coverage alone does not settle it.

Google Fonts lists Poppins under the Open Font License, and the distributed file includes SIL OFL 1.1 terms. Obtain the designer's actual source files and retain their applicable notices when distributing fonts. This does not establish anything about the unidentified Portland font or the logo's provenance. [Poppins metadata](https://github.com/google/fonts/blob/main/ofl/poppins/METADATA.pb), [distributed font license](https://raw.githubusercontent.com/google/fonts/main/ofl/poppins/OFL.txt).

## Recommended application font: Inter

The user subsequently prioritized screen readability and an attractive client experience over retaining the suggested font names. **Use Inter as the application-font recommendation; review the logo lettering separately.** Its clean interface character suits dense records and forms while allowing the book/P mark and color palette to carry the distinctive identity. This is a reasoned choice for PHP Ledger, not a claim that one font is universally best or proven to increase customer conversion. No font has been integrated into the application yet.

Inter's official documentation describes its text optical-size design and supplies tabular-number and character-disambiguation features. These address practical accounting needs more directly than the inspected Poppins Regular build. [Inter: design and OpenType features](https://rsms.me/inter/).

| Role | Recommended weight and treatment |
|---|---|
| Body text, descriptions, inputs, table rows | Inter Regular **400**; normal tracking; readable line height |
| Form labels, navigation, buttons, table headings | Inter Medium **500** |
| Page/section headings, document totals, key balances | Inter Semibold **600**; use emphasis selectively |
| Amounts, quantities, percentages, numeric comparison columns | Tabular lining figures; right alignment and consistent decimal precision |
| Short supporting metadata | Keep sufficient contrast and readable size; do not use thin/light weights to imply secondary importance |

Start body/form text at 16 px and dense table text at 14 px, then validate in the selected layout at real device sizes and browser zoom. These are starting values, not a waiver of readability testing. Keep letter spacing normal for financial data. Evaluate the disambiguation or slashed-zero feature for account codes and references rather than imposing unusual glyphs on every label.

### Verified font evidence

The official download page provided **Inter 4.1**. The inspected `InterVariable.ttf` reports a default text optical size of 14 with a 14–32 range, and default weight 400 with a 100–900 range. Its GSUB table includes **`tnum`**, **`zero`**, and **`ss02`**. [Official Inter download](https://rsms.me/inter/download/).

The inspected character map includes `$`, `£`, `€`, `₹`, `₨`, the mathematical minus, parentheses, percent, comma, and period. Representative Latin, Greek, and Cyrillic characters were present; the tested Devanagari `अ`, Arabic `ا`, and Urdu/Persian `ی` characters were absent. This is a codepoint check, not an exhaustive language audit. Use a separately tested script-appropriate font and shaping strategy for later Urdu/Arabic/Devanagari interfaces rather than relying on accidental system fallbacks.

Inter source SHA-256: `4989b125924991b90d05b2d16e0e388c48f7d5bb8b30539bbf9c755278d0ccaf`. The file was downloaded only into ignored local review storage. The local measurement library lacks the shaping support required to apply `tnum`, so the feature's presence was inspected directly in the font tables; equal-width rendered output has **not** yet been browser-tested. The official Inter documentation describes that behavior, but the application must still verify the bundled file and CSS together.

Use locally served, version-pinned webfont files with their license notice when implementing the choice; loading a branding preview must not call a font provider. Test the actual regular/medium/semibold rendering, tabular figures, supported currency characters, font-loading fallback, and printed/exported documents before declaring typography complete.

## Ready-to-send designer feedback

The book-shaped P is a good direction for PHP Ledger. Please keep the concept and refine it for product use before we finalize it:

1. Refine the wordmark to read clearly as **PHP Ledger**, with an intentional gap and more balanced spacing/weight between PHP and Ledger. Please show the current version alongside the refinement.
2. Provide horizontal, stacked, icon-only, one-color, and reversed versions. Show the icon at 16, 24, and 32 px and the horizontal version in a compact application header. Simplify the smallest version if the page details disappear.
3. Confirm `#0C2052` and `#424242` in the source artwork. Please also specify the exact blue/violet colors, gradient stops/direction, and flat-color alternative; they are visible in the mark but missing from the supplied palette.
4. We plan to use **Inter for the application**: Regular 400 for body/tables, Medium 500 for controls/labels, and Semibold 600 for headings/totals, with tabular figures for amounts. Please keep logo lettering a separate decision and identify the exact **Portland** family/source, weight, and license, plus the font/weight used for each wordmark part. The UI does not need to inherit the logo font.
5. Deliver clean SVG and vector PDF masters, transparent PNG exports, and favicon assets. Include a live-type editable source plus an outlined wordmark export, clear-space guidance, and minimum sizes. Please avoid embedding the current large background margins into the app assets.
6. Include one light-header, one dark-header, and one black-only document proof so we can confirm legibility in the places the identity will actually appear.

This is a refinement request, not a request for a new logo concept.

## Review record

Changed files: this feedback document and the unchanged `logo-designer-reference.png` copy. Temporary Poppins and Inter font evidence was downloaded into ignored `.cache/brand-review`; neither was installed or added as an application dependency. The supplied image was visually inspected and sampled without editing it. File identity, exact palette contrast, one Poppins file's digit metrics/features, and Inter's axes/features/selected character coverage were checked. Local document links and whitespace were validated.

References read: repository instructions and design/architecture/roadmap context, both supplied logo images, and the official Poppins and Inter sources linked above. No Google Drive document was read. External calls: yes, public read-only font references/downloads. Messages sent: none. Migrations: no. Schema changed: no. Raw secrets exposed: no. Live/production changed: no. PHP/JavaScript lint and application tests were not applicable to these image/document additions. Small-size rendering, browser/device checks, monochrome printing, designer-font identification, designer-font licensing/provenance confirmation, and observed usability remain pending.
