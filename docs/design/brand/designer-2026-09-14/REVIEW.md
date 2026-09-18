# Independent logo, layout and typography review

Reviewed 14 September 2026 against the user's supplied interface reference, the six unaltered designer JPEGs in this directory, and the subsequently supplied [24 Drive originals](originals/README.md). This is visual/asset feedback, not a completed browser accessibility test. **Inter remains required for money amounts.**

## Layout direction

The supplied reference offers a useful left navigation rail, a distinct active item, generous but orderly spacing, readable main actions, and restrained dividers. Carry that structure into the next implementation. Keep the company identity/context visible before users enter financial data.

The large greeting and Getting started steps suit a first successful transaction. Reduce or dismiss them after onboarding so recurring tasks, transaction lists and actual statements gain the space. The journal breakdown should remain available without being forced into every owner's first view. On a posted confirmation screen, use past-tense status such as “Posted journal entry”; a preview must be labeled separately.

The image alone cannot establish responsive behavior, keyboard order, exact typeface rendering, or contrast. Check those in the working specimen at normal text sizes and browser zoom before adoption. Small pale labels must remain readable; adding whitespace does not compensate for weak text contrast.

## Proposed typography

**Manrope headings with Poppins body is a viable combination to compare in the real specimen.** Manrope can provide a restrained heading hierarchy; Poppins has a more geometric, friendly texture. Its wider-looking shapes may require more space in dense labels/tables, so test wrapping and scan speed at 14–16px rather than choosing from a large heading alone. Avoid light weights for operational text.

Keep **Inter for amounts, totals, debit/credit columns, and other aligned financial numerals**. Apply tabular lining figures and right alignment consistently; include currency context and correct signs/precision. Extending Inter to the related numeric cells makes the financial grid more consistent without changing the proposed body-font decision. Do not mix typefaces within a single money amount.

The locally inspected font audit records Manrope **4.504** with a `tnum` feature and Poppins Regular **4.004** without that feature; Poppins' default digit widths vary. That is a property of the specific checked files, not a universal claim about every future font version. Enabling a CSS tabular-number property cannot create missing tabular glyphs. The explicit Inter decision avoids relying on Poppins for aligned financial columns.

The checked files lack the tested Arabic alef and Urdu yeh barree characters. Do not claim Arabic/Urdu coverage from this English specimen; a future locale needs a reviewed companion font and shaping/RTL tests. Keep body-font adoption pending the specimen review; this assessment changes no application font files or stylesheet.

## Earlier JPEG package

The table below records the initial six-file package. The later Drive delivery resolves the missing transparent-PNG issue; it does not change the original JPEGs.

| File | Best intended role | Readiness finding |
|---|---|---|
| [03 horizontal](logo-preview-03.jpeg) | Expanded navigation/header and horizontal branding | Preferred lockup for a normal application header. Needs a tightly framed scalable/transparent export; the large square canvas would waste header space. |
| [04 symbol](logo-preview-04.jpeg) | Compact navigation, app icon, favicon family | Recognizable book/P construction. Thin page strokes need an optically simplified small-size variant and checks at 16, 20, 24 and 32px. |
| [06 stacked](logo-preview-06.jpeg) | Marketing, cover or larger identity placement | Useful where vertical space is intentional; too tall to be the default dense workspace header. |
| [01 white horizontal](logo-preview-01.jpeg), [02 white symbol](logo-preview-02.jpeg), [05 white stacked](logo-preview-05.jpeg) | Intended reverse/dark-surface variants | White details and lettering nearly disappear against the baked light background. The opaque JPEG cannot reveal a navy surface behind them. The later Drive PNGs supply actual transparency. |

The manifest confirms all six files are **500×500 RGB JPEGs**. They are useful design previews, not transparent production masters. Keep them unchanged as supplied. The gradient book/P idea is worth retaining; asset preparation is the outstanding issue, not a need to invent a different mark.

## Drive originals: revised asset readiness

The supplied [Drive folder](https://drive.google.com/drive/folders/1bXlUSxVOsOFHXTLqdXhe3CrPCQqJwqG2) contains **12 SVGs and 12 PNGs**, numbered 12–23 in each format. All 24 were retrieved unchanged, matched to the connector's file sizes, and recorded with SHA-256 hashes in the [manifest](originals/manifest.json). The [asset inventory](originals/README.md) maps every file to its role and intended surface.

**The PNGs are genuinely transparent; the SVGs are not fully vector masters.** Each PNG is 500×500 RGBA with an alpha range of 0–255. Every SVG contains two embedded PNGs (image plus mask). The icon-only files contain no drawing paths beyond a clipping path where present. The lockups contain nine outlined wordmark paths, but their book/P mark is still a bitmap. There are no live text elements or external font dependencies. Renaming or cropping the SVG will not make the mark resolution-independent.

Static XML inspection found no scripts, event handlers, `foreignObject`, external resource references, font loads, DTDs or entities. This is a bounded source inspection, not a general-purpose SVG sanitizer or evidence of production rendering at every size. See the [audit](originals/audit.json). Original PNG export metadata was retained with the original bytes; no GPS IFD tag was present in the inspected EXIF headers.

Recommended source selection:

| Use | Light surface | Dark surface | Reason |
|---|---|---|---|
| Expanded application header | [20 horizontal PNG](originals/png/20.png), [SVG](originals/svg/20.svg) | [23 horizontal PNG](originals/png/23.png), [SVG](originals/svg/23.svg) | Matches the earlier horizontal direction 03; use a tightly framed derivative. |
| Compact navigation/app symbol | [13 symbol PNG](originals/png/13.png), [SVG](originals/svg/13.svg) | [12 symbol PNG](originals/png/12.png), [SVG](originals/svg/12.svg) | Matches the earlier symbol direction 04; keeps the book/P identity. |
| Larger marketing placement | [19 stacked PNG](originals/png/19.png), [SVG](originals/svg/19.svg) | [16 stacked PNG](originals/png/16.png), [SVG](originals/svg/16.svg) | Matches the earlier stacked direction 06; reserve the vertical space for larger branding. |

Black and white one-colour versions are also supplied: symbols 14/15, stacked lockups 18/17, and horizontal lockups 21/22. The earlier request for monochrome and transparent variants is therefore fulfilled. Their symbol remains raster-backed too.

The 500px canvases include substantial empty margins: the light horizontal mark occupies about 385×115px, and the light symbol about 187×220px. Cropping is useful for predictable header sizing, but must not stretch the mark or remove deliberate clear space. The user has now authorized asset cropping/modification; this audit preserves the originals and leaves production derivatives to the implementation workstream. Inspect the page strokes, edge fringes and wordmark gap at actual use sizes. A favicon still needs optical checks at 16, 20, 24 and 32px.

The supplied files also differ slightly from the two-colour brief. The outlined wordmark in `19.svg` uses `#0c2052` and `#151414`; `20.svg` uses `#122252` and `#151414`. The brief specifies navy `#0c2052` and charcoal `#424242`. Keep these supplied originals intact, then deliberately standardize the final master and confirm the gradient stops. Do not infer the intended gradient from compressed-looking raster pixels.

The remaining designer deliverable is a **true path-based book/P master**, with the gradient defined as vector fills, plus agreed palette, clear space, minimum sizes and a simplified small icon. The logo concept can stay. This review changes no application logo assets, fonts, routes or schema.

## Suggested designer message

The layout direction works well: please keep the clear left navigation, selected state and calm spacing. We are comparing Manrope headings with Poppins body in a real screen, while keeping Inter for all financial amounts and totals. Thank you for the SVG and transparent PNG package; the transparent and one-colour versions are now available. We will keep the book/P identity. The SVGs still embed a bitmap for the symbol, so please supply the book/P itself as clean vector paths with vector gradients. Please also standardize the navy and charcoal against the agreed palette, provide the exact gradient stops, and include clear-space/minimum-size guidance plus a simplified favicon. We can prepare tightly cropped application exports from the supplied originals.
