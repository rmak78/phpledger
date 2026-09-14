# PHP Ledger working brand

The user supplied the book-shaped **P** logo on 2026-09-14 and requested all six interface concepts in its colors. The mark gives PHP Ledger a recognizable connection to a ledger while keeping a simple initial. Its navy, blue, and violet are a suitable working identity for the next design comparison.

The brand direction is adopted for these concept revisions. On 2026-09-14 the user delegated the layout choice to the lead after an independent review; direction 6, Review Console, is selected for refinement. The images remain concepts rather than implemented screens. See [Design](DESIGN.md) for the decision and remaining usability gates.

## Source asset

- [Original supplied logo](design/brand/logo-reference.png), preserved without modification.
- Source file: `codex-clipboard-96a614aa-d54e-4b89-b44b-f06390a93037.png`.
- Dimensions: 1254 × 1254 pixels; an opaque raster with substantial light-background space around the mark and wordmark.
- SHA-256 of both the supplied file and the repository copy: `7DB2A298EB7C2663FBA6DF4130E6943321DDB5DD5B3A96E418D7B13ED9087A6F`.

The supplied image is the visual reference for the six concept revisions. It is not a finished vector master, a transparent production icon, or an established typeface specification.

The designer subsequently supplied a [second logo reference](design/brand/logo-designer-reference.png), exact main colors `#0C2052` and `#424242`, and the font names "Portland" and "Poppins". These exact colors supersede our sampled navy/charcoal below for implementation. The second image and wordmark choices are being reviewed, not silently accepted as final production assets. See [designer feedback](design/brand/DESIGNER-FEEDBACK.md). Revisions 1–5 preserve the first supplied lockup and existing typography; selected revision 6 also references the later image, exact main colors, and Inter interface direction. None is an exact font-rendering proof.

## Palette

### Latest asset preparation and main-logo preference

The designer's [Drive originals](design/brand/designer-2026-09-14/originals/README.md) are preserved as supplied: 12 SVGs and 12 transparent PNGs, with provenance and file checks. The SVG wordmarks use paths, but the book/P symbols are embedded bitmaps. The user authorized cropping/modification, and [12 prepared SVG canvases](design/brand/designer-2026-09-14/prepared/README.md) remove excess margins while preserving the original drawing data.

After seeing these files, the user compared our [existing horizontal mark](../www/phpledger/public/assets/brand/phpledger-horizontal.png) favourably with the supplied lockup. The lead recommends retaining the existing main mark: **bold PHP, lighter Ledger, balanced vertical alignment and readable spacing**. Header framing can improve its apparent size without redrawing its lettering. Use the book/P identity for compact placements; a true vector master and optically checked small icon are still needed. The prepared supplied variants remain alternatives and do not automatically supersede this preference.

### Colour authority

Raster concepts 1–5 use the initial approximate palette below; selected concept 6 requests the subsequent exact main colors. The designer's main colors are the implementation authority: **navy `#0C2052` and charcoal `#424242`**. Blue, violet, selection tint, and muted text remain proposed interface extensions; the designer has not specified the visible logo gradient stops. Generated pixels must not be sampled back into production CSS as an authoritative token set.

| Token | Value | Intended use |
|---|---|---|
| Navy, initial concept approximation | `#152356` | Replaced by designer-specified `#0C2052` for implementation |
| Blue | `#4656E8` | Primary action, links, active indicator, focus outline |
| Violet | `#6250EA` | Restrained secondary brand accent; part of the logo gradient |
| Charcoal, initial concept approximation | `#393B3D` | Replaced by designer-specified `#424242` for implementation |
| Off-white | `#F7F7F7` | Application canvas and reference background |
| White | `#FFFFFF` | Forms, tables, document surfaces, text on solid primary actions |
| Selection tint | `#EEF0FF` | Selected row or navigation background |
| Muted text | `#596174` | Secondary descriptions on white or other verified light surfaces |

Keep blue and violet concentrated in the identity, selected states, and meaningful actions. Use a solid blue primary button. The logo may retain its blue-to-violet shading; avoid spreading decorative gradients over tables, forms, or large background areas.

## Contrast checks

These ratios were calculated from the proposed solid sRGB values using relative luminance and `(lighter + 0.05) / (darker + 0.05)`. They are rounded to two decimal places. Each listed pair exceeds the 4.5:1 normal-text contrast target; this does not establish accessibility of a generated image or a future implementation.

| Foreground | Background | Contrast |
|---|---|---|
| Navy | White | 14.93:1 |
| Charcoal | Off-white | 10.50:1 |
| White | Blue | 5.59:1 |
| White | Violet | 5.45:1 |
| Blue | White | 5.59:1 |
| Blue | Off-white | 5.22:1 |
| Navy | Selection tint | 13.18:1 |
| Blue | Selection tint | 4.94:1 |
| Muted text | White | 6.20:1 |

The later designer-specified values were calculated separately: `#0C2052` on white is **15.62:1**, `#424242` on `#F7F7F7` is **9.38:1**, and `#0C2052` on `#EEF0FF` is **13.80:1**. These are solid-color calculations, not a test of small logo details, generated image pixels, or a working UI.

## Selected interface typography

### Latest designer review and numeric-font decision

On 14 September 2026 the designer proposed **Manrope for headings and Poppins for body text**, and the user explicitly required **Inter for money and amounts**. Preserve Inter across monetary entry/display, POS prices/change, totals and numeric report columns. The candidate pairing and supplied six JPEG logo previews have a [file/license review and local typography specimen](design/typography-review/README.md). The designer's quiet sidebar/spacing reference informs the next owner-screen design; it is not a reason to make cashier layouts oversized. Manrope/Poppins are usable candidates, while the application currently remains Inter throughout pending the actual design integration. This later requirement refines the earlier all-Inter decision recorded below.

The user subsequently asked to prioritize screen readability and client appeal over the initial font suggestions. **Select Inter for the application interface and website body text.** Use one readable family across navigation, forms, tables, and headings. Keep the logo lettering a separate identity decision. The designer named Portland and Poppins but did not identify their wordmark assignments, exact weights, or the Portland publisher/file; do not infer those facts from the raster.

Inter's official documentation describes its small-text optical design, tabular numbers, and optional character-disambiguation features. These support our choice for a financial interface; they are not proof that every user will prefer it. Source reviewed 2026-09-14: [Inter's official site and features](https://rsms.me/inter/). The independent [designer review](design/brand/DESIGNER-FEEDBACK.md) records font-file checks and the limits of Poppins for financial columns.

| Use | Starting specification |
|---|---|
| Body and form values | Inter Regular 400, 16px with 24px line height |
| Navigation, labels, buttons | Inter Medium 500, 14–16px with at least 20px line height |
| Dense desktop tables | Inter Regular 400, 14px/20px; offer comfortable row density and preserve zoom |
| Section headings and totals | Inter Semibold 600, 18–20px headings; totals retain the surrounding numerical scale |
| Page heading | Inter Semibold 600, 24–28px with a clear task title |
| Amounts and financial columns | Inter with verified tabular lining figures, right alignment, consistent precision, visible currency context |

These are starting component tokens, subject to rendered testing. Avoid thin type, excessive uppercase/letter spacing, light gray body text, or artificially condensed numerals. Use `font-variant-numeric: lining-nums tabular-nums` for money after verifying the selected font file supports the features; optional slashed zero and disambiguated I/l belong primarily in references and codes. Keep fractional ligatures off for accounting values. Use font optical sizing where supported, retain browser text scaling, and ensure forms remain usable at zoom.

Self-host the chosen WOFF2 files with their license, pin the release, load only required styles/scripts, and use a system fallback while they load. Do not add a third-party font request on page load. Language support must be verified for each released locale; a font choice does not imply Urdu, Arabic, or Devanagari support. Pair any required script font through a reviewed locale style rather than changing the whole product's visual identity. The current static concept images are not proof of exact font loading, glyph coverage, or browser rendering.

Use navy on the selection tint for selected-row text and white on solid blue for the main action. Keep a visible focus outline with separation from the control. Check the actual adjacent colors, disabled states, hover states, icons, borders, and any future gradients in the rendered interface. A logo color is not automatically an accessible control color on every background.

## Practical use in the six concepts

- Preserve each concept's layout and task emphasis so the comparison measures structure within one coherent brand.
- Put a compact mark and the `PHP Ledger` wordmark in the navigation or page header. Keep the book/P construction, page strokes, and proportions recognizable.
- Prefer a light ground behind the supplied lockup. If a concept has navy navigation, provide a small light brand area; do not assume the dark wordmark will remain readable directly on navy.
- Keep the mark subordinate to the work. Avoid a giant logo, a decorative greeting hero, or repeated brand panels that consume transaction space.
- Retain clear draft, posted, reversed, warning, and error labels. Status needs text or an icon as well as color; do not use blue/violet branding as the only accounting-state signal.
- Use ordinary interface typography with aligned figures and readable tables. The raster wordmark does not establish a body-font choice.
- Keep layouts and labels believable. Owner setup can be calm and guided; accountant screens can be compact. Both need clear totals, source references, actions, and a recoverable validation state.

Generated revisions should use this source asset as an image reference. They are visual interpretations and must not become the production logo master. Before implementation, prepare properly reviewed scalable assets and check the mark at small sizes; the thin page details may require a carefully simplified small-icon variant.

## Proposed business branding

The recommended future policy is a shared PHP Ledger interface with optional business branding. This recommendation follows the user's question about customer logos, color choices, and PHP Ledger marketing; **the final policy is not approved and the feature is not implemented**. The six current concepts show the default PHP Ledger identity, including its header branding. No customer logo has been supplied for this comparison, and they do not demonstrate a working business-branding mode. Moving PHP Ledger to a subtle footer applies to the proposed future mode after a business supplies its own identity; it does not change the meaning of these six default-brand concepts.

The default navy/blue/violet identity should remain recognizable in a new installation, the sample experience, the public website, and product demonstrations. Within a business's workspace, its logo and name can lead the header, with a small PHP Ledger mark or wordmark in the application footer. Keep that PHP Ledger mark in its original colors. The customer's accent must not recolor or redraw it.

| Surface | Recommended ownership |
|---|---|
| Website, public demo, and uncustomized installation | PHP Ledger identity remains prominent, with sample/company context clearly labeled |
| Customized business workspace | Business logo and name in the header; restrained approved accent; subtle PHP Ledger identity in the application footer |
| Customer-facing invoices, receipts, quotations, and statements | Business logo, company details, and document accent; no prominent software identity competing with the business |
| Navigation, forms, tables, typography, and accounting-state signals | One consistent PHP Ledger design system across businesses |

Document customization is a future capability, not a claim that these document workflows currently exist. The application footer recommendation does not impose a PHP Ledger marketing footer on customer-facing documents. Any document attribution policy remains a separate product/license decision.

Customer-document branding has a practical precedent: QuickBooks Online supports business logos on invoices, estimates, and sales receipts, with placement and size controls. This supports the document-customization part of the proposal; it does not prove that whole-application theming or any particular PHP Ledger logo position will improve marketing results. The hybrid policy is our recommendation, not a measured conversion claim. Source reviewed 2026-09-14: [Intuit: Add, customize, or remove logos on sales forms](https://quickbooks.intuit.com/learn-support/en-us/help-article/customize-forms/add-customize-remove-logos-sales-forms/L2PH9EBoP_US_en_US).

### Wizard and settings path

1. Complete essential company setup using the default brand. Uploading a logo or choosing colors must remain optional and must not delay the five-minute setup target or the first bookkeeping task.
2. Offer a short, skippable "Make it yours" step after core setup, with the same controls available later in company settings.
3. Let the user upload a business logo and choose a controlled accent. Colors extracted from the logo are suggestions only; display them for review and never apply them automatically.
4. Preview the header, a selected navigation item, a primary action, and a sample customer document together. Show the business name and company context so the affected company is clear.
5. Check text, controls, focus, hover, and selected-state contrast against their actual surfaces. Offer an accessible adjustment when a requested color fails. Manual color changes remain possible within the contrast rules; an override must not bypass the accessibility requirement.
6. Save only after explicit confirmation. Provide a clear way to return that company's appearance to the PHP Ledger defaults without changing its accounting data or another company's settings.

Typography, spacing, component layout, navigation behavior, and semantic colors for errors, warnings, success, drafts, posted entries, and reversals stay controlled by the shared design system. Branding must not change financial-state meaning, rely on color alone, or create an arbitrary theme builder. Customer choice is a validated accent and approved identity assets, not custom CSS, HTML, or scripts.

### Permissions and implementation boundaries

Branding belongs to the company, not the signed-in person's global profile. Only the company owner or an explicitly authorized business administrator should be able to change it. The current foundation defines owner, accountant, and viewer roles; it has no separate administrator role. Implement owner-only changes unless a later permission decision defines an authorized administrator or equivalent capability. Viewer access must never permit branding writes. All reads, previews, asset retrieval, and writes must enforce company scope on the server.

Use the existing bootstrap, permissions, upload conventions when established, CSRF protection, and contextual output escaping. Do not introduce an alternative settings/authentication stack. Future upload handling must validate actual file content, allowed format, size, and decoded dimensions; use safe filenames and storage; prevent executable or active content; and deliver assets with appropriate response headers. Exact supported formats and limits require an implementation decision. Do not accept arbitrary remote logo URLs or arbitrary CSS as a shortcut.

The preview must use local assets and sample data without calling external services. Do not fetch providers on page load, upload logos to a color-extraction service, or use customer records to generate a branding preview. Any extracted palette can be calculated locally and requires the user's confirmation before saving.

Before this mode is released, test company isolation, permissions, invalid uploads, confirmation and reset behavior, logo proportions, actual contrast, keyboard use, responsive headers, and printed/exported document readability. These are future acceptance checks; the current static images and token calculations do not satisfy them.

## Evidence and remaining work

The supplied image was visually inspected, source pixels were sampled without editing the image, the copy was checked byte-for-byte through matching SHA-256 hashes, and the proposed solid color contrast ratios were calculated locally. No runtime code, routes, database schema, or migrations changed for this brand record. No production system or external service was changed.

Read for context: `AGENTS.md`, `README.md`, `docs/ARCHITECTURE.md`, `docs/ROADMAP.md`, and `docs/DESIGN.md`. The proposed business-branding section also references the public Intuit documentation linked above. No Google Drive document was read. External calls were public, read-only reference lookups; no account settings were accessed or changed. Markdown and image files require no PHP or JavaScript lint. Browser, keyboard, responsive, small-icon, and observed usability checks remain for the selected implementation.
