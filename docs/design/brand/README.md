# PHP Ledger: six branded layouts

Created 2026-09-14 from the six existing concepts and the supplied book/P identity. These are static image explorations, not working product screens. Original images remain unchanged in the parent directory.

## Lead decision

**Choose 6, Review Console, for the main application.** The user explicitly delegated this choice after asking for an independent review. Both the lead and reviewing agent favor its persistent transaction list beside selected-record details, deliberate company/book context, and professional working density. Direction 5 is the runner-up for long document editing. The full [six-layout review](REVIEW.md) records each candidate's advantages and limitations.

The ranking is a visual judgment. The images do not prove keyboard operation, responsive behavior, accounting correctness, or faster task completion. The [selection record](../../DESIGN.md) lists required refinements and the observed usability gates before release.

## Complete branded set

| Preserved number | Image | Main role |
|---|---|---|
| 1 | [Guided Start](01-guided-start.png) | Business onboarding |
| 2 | [Ledger Desk](02-ledger-desk.png) | First everyday expense |
| 3 | [Calm Overview](03-calm-overview.png) | First-success confirmation |
| 4 | [Transaction Workbench](04-transaction-workbench.png) | Table with inline editing |
| 5 | [Accounting Records](05-accounting-records.png) | Full document editor |
| **6** | **[Review Console](06-review-console.png)** | **Selected main direction: list and record detail** |

Images were generated independently and displayed in this order, preserving the original numbering. Their exact generation text and attached source paths are recorded in [Prompts](PROMPTS.md). The preserved original six source images are linked in [Design](../../DESIGN.md).

## Logo, colors, and typography

The [first supplied logo](logo-reference.png) grounds the brand pass. The designer later supplied a [revised reference](logo-designer-reference.png), exact navy `#0C2052` and charcoal `#424242`, and the names Portland and Poppins. Our verdict is to retain the book/P concept and refine the wordmark, small-icon versions, and export package; see [designer feedback](DESIGNER-FEEDBACK.md).

The first five revisions retain their source typography and initial sampled palette (`#152356` navy and `#393B3D` charcoal) to preserve the layout comparison. The selected sixth revision incorporates the later exact navy/charcoal direction and Inter-oriented interface typography. Blue/violet accents remain proposed extensions, pending the designer's exact gradient specification. Raster images are visual interpretations, not exact font files or production CSS tokens.

The user asked to prioritize screen readability and client appeal. **Inter is selected for application text**, with regular body/table text, medium controls, semibold headings, and verified tabular figures for financial columns. Logo lettering is a separate decision. [Brand guidance](../../BRAND.md) records exact tokens, contrast calculations, typography roles, and remaining font/rendering checks.

The images show the default PHP Ledger identity. The proposed future business mode allows a business logo and controlled accessible accent, with a subtle PHP Ledger footer. It is optional after essential onboarding; it is not an implemented theme or upload setting. Customer-facing documents should primarily express the business's identity. Preserve one shared layout, typography, and clear accounting states.

## Review and delivery limits

The lead visually inspected all six generated images for recognizable logo, content, readable placement, and major layout drift. The independent reviewer additionally inspected branded 1–4. Local checks passed for all six PNGs: 1–3 are 1487 × 1058 and 4–6 are 1586 × 992, matching their respective source dimensions. Both logo copies match their supplied source SHA-256 hashes. Eight Markdown files passed 76 local-link checks, trailing-whitespace checks, and code-fence pairing checks. Generated images still approximate colors, fonts, shading, and incidental attachment metadata; implementation must use reviewed vector assets and actual design tokens.

Known workflow issues in the original layouts remain documented in [Design](../../DESIGN.md) and the [independent review](REVIEW.md): direction 4 needs an authoritative editable amount and correct internet category; direction 5 must avoid conflicting totals; direction 6 needs fewer header/status repetitions, clear inspection versus editing, and a deliberate mobile path. Direction 3's blue Posted indicator retains its text but must not set the semantic color system for the product. No click, save, import, or posting occurs in these images.

Current changes are local images and documentation. No application code, routes, migrations, or schema changed in this design pass; no secrets were exposed and no production system changed. External calls were image generation and read-only design/font research. No Google Drive documents were read. PHP/JavaScript lint and application tests are not applicable to these files; browser, keyboard, responsive, font-loading, print, and observed usability checks remain for implementation.
