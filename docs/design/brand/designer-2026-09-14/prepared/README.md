# Prepared designer assets

The user authorized cropping/modification on 14 September 2026. These derivatives reframe the supplied SVG files with sensible clear space, preserving every drawing element, wordmark path, colour and embedded image byte. Symbol variants receive a square canvas. The [manifest](manifest.json) records each source hash and crop; [prepare-assets.mjs](../prepare-assets.mjs) reproduces them using Node without raster editing. The [original archive](../originals/README.md) remains unchanged.

Open [the gallery](index.html) to review the 12 light, dark, black and white variants. These remain raster-backed SVGs, not newly vectorized masters. Cropping does not improve source resolution. The original lettering and its palette differences remain visible for designer review.

## Preferred main identity

The user subsequently compared the existing application header artwork favourably with the supplied horizontal lockup. The lead recommends retaining that version: bold **PHP**, lighter **Ledger**, balanced vertical alignment and the book/P symbol. Its existing source is [the application horizontal asset](../../../../../www/phpledger/public/assets/brand/phpledger-horizontal.png). These prepared designer lockups are available alternatives and do not automatically replace that preferred wordmark.

The built-in image editor was tried with the supplied `20.png` to obtain a tightly cropped transparent horizontal asset. It changed the lettering and introduced an unwanted background, so its output was rejected and is not part of this package or the public website. The prompt asked for an exact crop, preserved shape/gradient/lettering and genuine transparency. The successful preparation instead edits the existing SVG canvas attributes only, preserving its contents.

## Validation boundary

The preparation command verifies all 12 source SVG hashes and confirms that every byte after the root SVG tag remains unchanged. The earlier original-file audit screened the SVG content and all 24 original hashes. Matching PNG alpha bounds guide cropping, with an eight-percent clear-space margin; final SVG browser checks are recorded separately. No font, account data, route, schema, or live site is changed by generating these assets.

## Independent browser receipt — 14 September 2026

Opened the local gallery at `http://127.0.0.1:18431/` in a separate Chrome tab. At the default 1440×675 CSS-pixel viewport, all **12/12 SVG images loaded**, and the document had no horizontal overflow. The [full gallery screenshot](gallery.png) is 1425×1675 pixels and includes every variant and the footer.

Visual inspection confirms that all twelve cropped symbols/lockups retain their complete visible artwork: top page strokes, the book/P outline, and every wordmark letter remain inside their canvases with clear space. The colour and white reverse variants are readable on navy; the colour and black variants are readable on white. The gallery reports no captured browser warnings or errors. This validates framing at the displayed sizes, not a change to the preferred application identity.

The symbols displayed at 170×170 CSS pixels; stacked marks were about 170px tall, and horizontal lockups about 335px wide. A 32px symbol specimen is not present in this gallery, so small-icon legibility remains unverified. No claim of full vector geometry, production accessibility or favicon readiness follows from these screenshots. All 24 original file hashes were rechecked after the browser review and remained unchanged.

Files added/updated by this review: this receipt and `gallery.png`. Browser activity was limited to the local gallery; no Drive/external calls, application writes, migrations, schema changes or live changes occurred. Raw secrets exposed: no.
