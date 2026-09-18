# Designer originals from Google Drive

Retrieved 14 September 2026 from the user-supplied [PHP Ledger logo folder](https://drive.google.com/drive/folders/1bXlUSxVOsOFHXTLqdXhe3CrPCQqJwqG2). This directory preserves the original bytes; it is a reference archive, not the application's public asset directory. See the [updated brand review](../REVIEW.md).

## Provenance and checks

- Read the folder metadata and its [PHP SVG](https://drive.google.com/drive/folders/1V4Dz25jVo71opzCleBuCnPgbUh0syhJp) and [PHP PNG](https://drive.google.com/drive/folders/1NK0E6nvrwFkrPJbnb4_5pUgLLZSO5Kmo) subfolders through the Google Drive connector. Each contained 12 files, named 12–23 with the corresponding extension.
- The connector's raw fetch for `14.svg` returned a streamed reference without an available local materializer. The 24 originals were therefore downloaded through Drive's anonymous original-file download route using the already observed file IDs. No credentials, edits, sharing changes or deletions were involved. Successful download does not establish a redistribution licence.
- All **24 original files / 1,986,550 bytes** match the listed sizes. The [manifest](manifest.json) records each original name, source file URL/ID, relative path, timestamps, MIME type, size and SHA-256 hash. The metadata's sharing visibility was unavailable; anonymous download access was verified separately.
- SVG XML was inspected before any possibility of rendering; no SVG script was executed. The [audit](audit.json) records dimensions, elements, embedded PNGs, font/text dependencies and the bounded static security screen.
- All PNGs are **500×500 RGBA** with actual transparency. Original EXIF/XMP export metadata remains intact. No GPS IFD tag was present in the inspected EXIF headers; this is not a comprehensive assertion about every metadata field.

## File selection

Each row links the unchanged matching SVG and PNG. “Light” means the mark is intended to sit on a light surface; white variants need a dark surface.

| Number | Files | Construction/colour | Intended role and surface |
|---|---|---|---|
| 12 | [SVG](svg/12.svg) · [PNG](png/12.png) | White book, blue/violet P | Compact symbol on dark backgrounds; preferred colour reverse symbol. |
| 13 | [SVG](svg/13.svg) · [PNG](png/13.png) | Navy book, blue/violet P | Compact symbol on light backgrounds; matches preview 04. |
| 14 | [SVG](svg/14.svg) · [PNG](png/14.png) | Black symbol | One-colour symbol on light backgrounds. |
| 15 | [SVG](svg/15.svg) · [PNG](png/15.png) | White symbol | One-colour symbol on dark backgrounds. |
| 16 | [SVG](svg/16.svg) · [PNG](png/16.png) | White book/wordmark, blue/violet P | Colour stacked lockup on dark backgrounds. |
| 17 | [SVG](svg/17.svg) · [PNG](png/17.png) | White stacked lockup | One-colour stacked lockup on dark backgrounds. |
| 18 | [SVG](svg/18.svg) · [PNG](png/18.png) | Black stacked lockup | One-colour stacked lockup on light backgrounds. |
| 19 | [SVG](svg/19.svg) · [PNG](png/19.png) | Navy/blue/violet mark; dark wordmark | Colour stacked lockup on light backgrounds; matches preview 06. |
| 20 | [SVG](svg/20.svg) · [PNG](png/20.png) | Navy/blue/violet mark; dark wordmark | Preferred horizontal header on light backgrounds; matches preview 03. |
| 21 | [SVG](svg/21.svg) · [PNG](png/21.png) | Black horizontal lockup | One-colour horizontal lockup on light backgrounds. |
| 22 | [SVG](svg/22.svg) · [PNG](png/22.png) | White horizontal lockup | One-colour horizontal lockup on dark backgrounds. |
| 23 | [SVG](svg/23.svg) · [PNG](png/23.png) | White book/wordmark, blue/violet P | Preferred colour horizontal header on dark backgrounds. |

The PNG colour variants were visually inspected. The all-black variants were additionally classified from their opaque RGB pixels and content bounds because the image viewer displayed transparency on black. The SVG classification is grounded in XML structure and matching asset naming; full SVG browser rendering was not performed by this audit.

## Important limits

Every SVG declares width/height `500` and viewBox `0 0 375 374.999991`. **Every SVG embeds two PNGs: the book/P image and its mask.** Files 12–15 have no vector drawing of the mark (zero paths for 12, only a clipping path for 13–15). Files 16–23 add nine vector wordmark paths and a clipping path. There are no live `<text>` elements or external fonts. The wordmark can scale as paths; the symbol remains resolution-limited.

The screen found no scripts, event attributes, `foreignObject`, external resource references, suspicious CSS imports/font loads, DTDs or entities. Do not treat this bounded inspection as a universal SVG sanitization guarantee.

All originals have generous square canvases. Use separate, reviewed derivatives for an application header, icon or marketing placement. Cropping does not restore vector geometry or improve source detail. Preserve these originals and their hashes when preparing derivatives. The user authorized cropping/modification after this review began; no pixel or SVG edits were made to these originals.

The book/P identity is consistent with the supplied previews. Final masters still need a consistent palette: light stacked `19.svg` uses outlined wordmark fills `#0c2052` / `#151414`, while horizontal `20.svg` uses `#122252` / `#151414`. The supplied brief names `#0c2052` / `#424242`. Exact gradient stops and a genuinely vector book/P should come from the designer's master rather than being guessed from raster pixels.

## Verification receipt

Original hashes and sizes were checked for all 24 files. All 12 SVGs parsed as XML after DTD/entity rejection; all 12 PNGs decoded and their dimensions/alpha/content bounds were inspected. The documentation/JSON files are additions to the local review archive; no application asset replacement, migration, schema change, GitHub write, Drive write or live deployment was performed by this audit. Remote read-only calls: yes. Raw secrets exposed: no.
