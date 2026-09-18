# Designer font and logo review

Reviewed 14 September 2026. This is a local typography study and asset review, not the completed report/POS redesign. The working application still uses Inter throughout. The user's latest explicit instruction is to **keep Inter for money, amounts and other financial figures**.

## Recommended roles

| Role | Font and starting treatment | Decision |
|---|---|---|
| Page and section headings | Manrope 600–700; 24–36px for ordinary screens | Suitable candidate from the designer; avoid oversized headings in the register |
| Ordinary prose and guidance | Poppins 400; start at 16px/24–26px | Usable, subject to actual dense-screen reading checks; do not compress to tiny text to make it fit |
| Amounts, prices, totals, percentages, quantities and numeric report columns | Inter 400–650 with tabular lining numerals | User requirement; apply to entry fields and POS as well as displayed reports |
| Dense financial tables | Keep numerical content Inter; test readable labels at 14–16px | Preserve useful density and decimal alignment rather than applying spacious marketing typography everywhere |

The specimen [index.html](index.html) demonstrates these roles with original sample content. Its two-year figures are a subset of the separate [retail arithmetic fixture](../../accounting/examples/retail-statements.md). It does not imply that the application implements those statement mappings.

## File and license checks

The official Google Fonts files were downloaded for inspection, with source URLs and SHA-256 hashes recorded in [font-audit.json](font-audit.json). The review uses locally converted WOFF2 containers; this conversion changes packaging, not the glyph design. Licenses are retained under [fonts](fonts/).

- **Manrope 4.504:** variable weight 200–800; the inspected font includes tabular numerals. The Google Fonts source is under SIL OFL 1.1. [Official source/license](https://github.com/google/fonts/blob/main/ofl/manrope/OFL.txt).
- **Poppins Regular 4.004:** inspected digits have unequal advance widths and no `tnum` feature was present. It is suitable for prose, while the user's Inter decision avoids this limitation in financial columns. It is also under SIL OFL 1.1. [Official source/license](https://github.com/google/fonts/blob/main/ofl/poppins/OFL.txt).
- **Existing Inter:** the actual bundled `InterVariable.woff2` includes `tnum` and the slashed-zero feature. Its existing license is copied alongside the review assets. Numeric alignment still requires the CSS feature to be enabled.
- Neither inspected Manrope nor Poppins file contains Arabic alef or Urdu yeh barree. They do not constitute an Urdu/Arabic font solution. A separately reviewed companion font and right-to-left layout/shaping tests are needed when those languages are implemented.

The OFL permits bundling and embedding with its conditions, including preservation of the copyright/license notices. We do not need to load Google Fonts from visitors' browsers: this study makes no third-party page requests. Only the needed families/weights/subsets should be shipped in the eventual product, with a measured loading budget. The designer reports a licensing/access limitation for Portland; no Portland font file or license was supplied or independently verified here.

## Initial supplied logo package

The user's ZIP contained **six JPEG files only**, each **500 × 500, RGB, opaque**. No SVG, transparent PNG, PDF master, font file or separate usage/license document was in the archive. They are preserved unmodified with their original ZIP names, sizes and hashes in the [manifest](../brand/designer-2026-09-14/manifest.json).

| Preview | Recommended role / observed limitation |
|---|---|
| [03](../brand/designer-2026-09-14/logo-preview-03.jpeg) | Best horizontal lockup for a light header; substantial baked canvas needs a proper production export |
| [04](../brand/designer-2026-09-14/logo-preview-04.jpeg) | Standalone symbol; request a simplified small-size version and vector export |
| [06](../brand/designer-2026-09-14/logo-preview-06.jpeg) | Stacked presentation for marketing or larger placements |
| [01](../brand/designer-2026-09-14/logo-preview-01.jpeg), [02](../brand/designer-2026-09-14/logo-preview-02.jpeg), [05](../brand/designer-2026-09-14/logo-preview-05.jpeg) | Light/reversed artwork is barely visible on its baked pale JPEG background. Placing the file on navy will not remove that background. Need genuinely transparent/vector reversed exports. |

The designer's [layout reference](../brand/designer-2026-09-14/layout-reference.png) has a useful quiet left rail, readable selected state, clear heading hierarchy and restrained dividers. Adopt that rhythm for owner/onboarding screens. Collapse first-run greetings/checklists after setup; report and cashier workspaces need their own efficient density. The reference is an image, not evidence of implemented behavior or accessibility compliance.

The later [Drive delivery](../brand/designer-2026-09-14/originals/README.md) supplies **12 SVGs and 12 genuinely transparent PNGs**, including light, dark and monochrome variants. This resolves the earlier missing-export request. Each SVG still embeds a bitmap symbol; the wordmarks are paths. A true vector book/P, agreed gradient stops and minimum-size guidance remain designer deliverables. The user's subsequent cropping permission is recorded in the [prepared asset package](../brand/designer-2026-09-14/prepared/README.md), which preserves the originals.

The user subsequently compared our existing horizontal logo favourably with the supplied wordmark. The lead agrees: retain the stronger **PHP**, lighter **Ledger** and balanced spacing of the current application mark. The prepared designer variants are available alternatives, not an automatic replacement of that preferred main identity.

## Browser review

The actual specimen loaded Manrope 700, Poppins 400 and Inter 600 successfully. Computed styles confirmed Manrope headings, Poppins body, and Inter numeric cells/inputs with tabular lining figures. Chrome captures are [desktop](desktop.png), [mobile](mobile.png) and [mobile statement](mobile-statement.png). At the checked 1440px and 390px viewports there was no horizontal page overflow; the mobile statement fit its available width. Separate in-app-browser DOM checks also covered 768px and 320px without page overflow.

The pairing works for readable onboarding/owner screens. Use Poppins at 16px for ordinary prose and test dense labels at 14px or larger; avoid making the cashier screen fit by shrinking text. Keep Inter for money, quantities, percentages and financial-entry fields. This is a font study, not a completed report/POS redesign. Exact 200% browser zoom and observed user task completion were not verified.

## Review limits

An independent agent inspected the supplied layout, the six JPEGs, and the later Drive originals. Font metadata and official licenses were inspected directly. The typography study uses sample values and editable demonstration fields only; it neither saves nor posts anything. This does not change the earlier accounting/POS publication hold, establish final body-font approval, or demonstrate observed user task success.
