# Website and application presentation QA

Date: 2026-09-14. Local development baseline; publication is held while the user's report/POS critique is researched. Earlier composition selection does not establish acceptance of the current product screens.

## Source and scope

The later narrow logo-framing change preserves the existing source assets and uses the CSS viewport documented in [provenance](../../docs/design/website/SOURCES.md#header-framing-14-september-2026). The full mark is visible in application, website and typography previews at desktop 1440px and mobile 390px, with no horizontal page overflow. The website at 320px has a 175px frame and its menu ends at x=291 within the 305px content width. Credits were additionally inspected at desktop. Local `GET /demo/` returned 200 with the unchanged prefixed logo URL and versioned prefixed stylesheet. PHP layout lint, CSS parsing, website static checks and whitespace checks passed. Updated conservative file budget: 592,323 bytes. No source artwork, app font, workflow or publication changed in this pass.

The selected website reference is [1, Field Notes](../../docs/design/website/01-field-notes.png). Its split introduction/real workplace photo, editorial numbering, broad product evidence and navy pilot invitation were implemented with original photos and real application captures. The user's later scope makes owner reports and cash POS the leading product evidence, with the expense/journal/trial-balance journey available as a bookkeeper disclosure.

The application retained the Review Console structure and existing routes/forms. The visual polish raises normal reading text to 14–16px, gives transaction list/detail their own readable panels, improves the company/navigation controls, introduces an owner overview with actual balances, styles balance sheet/P&L/forecast, and gives sign-in a deliberate two-column introduction. No alternative frontend framework or financial write path was added.

## Checked before the owner/POS expansion

| Check | Observed result / evidence |
|---|---|
| Desktop 1440 × 1000 | Correct typeface, real assets and readable hierarchy; no horizontal page overflow. [Final hero](../../docs/design/website/qa/desktop-final.png) |
| Tablet 768 × 1024 | Navigation collapses correctly; split hero and three principles fit. [Capture](../../docs/design/website/qa/tablet-768.png) |
| Mobile 390 × 844 | Single-column hero, working menu and walkthrough; no page overflow. [Hero](../../docs/design/website/qa/mobile-390-hero.png) |
| Narrow 320 × 760 | Initial menu clipping was corrected by narrowing the header identity and adjusting the menu spacing. Document width 305 within 320 viewport. [Recheck](../../docs/design/website/qa/mobile-320-final.png) |
| Credits at 320px | Header wraps and text remains readable, width 305 within 320. [Capture](../../docs/design/website/qa/credits-320.png) |
| Keyboard report tabs | Right arrow changes selection and the visible report. Modal focuses Close; Escape closes and restores the opener. [Report](../../docs/design/website/qa/report-dialog-desktop.png) |
| Mobile enlarged image | Fits viewport, internally scrolls a larger readable image. A keyboard-focusable image region and explicit scroll instructions were added. [Capture before instruction addition](../../docs/design/website/qa/mobile-dialog.png) |
| Pilot validation and correction | Empty submission identifies all four required fields; entered synthetic values are retained and an exact reviewable message is copied. No message was sent. [Pilot](../../docs/design/website/qa/pilot-desktop.png) |
| JavaScript | `node --check www/website/public/assets/site.js` passed, including the grouped-tab update. |
| Markup/assets/styles | Local structure, duplicate IDs, anchors, image alt/asset paths and CSS parser checks passed before expansion. Final asset check is recorded separately below. |

The initial same-input comparison is [here](../../docs/design/website/qa/comparison-desktop.png). It identified the first implementation's desktop headline as too quiet; the subsequent [final hero](../../docs/design/website/qa/desktop-final.png) increases it to approximately 88px at 1440 while keeping smaller breakpoint sizes. Original versus implemented product pixels intentionally differ: working captures replace generated UI.

## Application audit and remediation

The live local application was inspected before edits, without financial writes. These are current-session captures, not recalled judgments:

1. [Sign-in before](../../docs/design/website/qa/app-polish/01-login-before.png): a narrow form floated in unstructured space. Added an intentional brand introduction and a bounded form surface, preserving account fields and behavior.
2. [Transactions before](../../docs/design/website/qa/app-polish/02-transactions-before.png): small row/detail text and weak section hierarchy made the working flow feel raw. Increased readable sizes, separated list/detail surfaces, strengthened amount/status emphasis and retained responsive list/detail behavior.
3. [Reports before](../../docs/design/website/qa/app-polish/03-reports-before.png): new report links were visibly unstyled. Replaced the directory with actual cash and performance values, a truthful magnitude comparison, meaningful report questions and a clearly separate planning option.

`layout.php` and `reports.php` passed PHP syntax checks using the running `web` service. The stylesheet parsed without errors. The owner overview uses existing controller-supplied report values; only chart widths use rounded display arithmetic. Financial strings remain fixed-precision and escaped. The demo error view skips the demo-state database query, and the opening-balance notice now correctly says recording and posting remain unavailable.

## Final integration status and limits

### Documentation links: 14 September 2026

Added the Wiki and Getting Started guide in the existing documentation area and footer, plus an installation FAQ distinguishing the forthcoming new package from the legacy default branch and temporary evaluation demo. The main navigation is unchanged. Local Chrome checks at 1440px, 768px and 320px found no horizontal overflow (content widths 1425, 753 and 305px); the new FAQ expanded correctly. [Desktop capture](../../docs/design/website/qa/documentation/desktop.png) and [320px mobile capture](../../docs/design/website/qa/documentation/mobile-320.png) record the changed area. The five outbound links use the two exact Wiki paths and match the prepared local Wiki pages. Actual GitHub destination availability remains the lead's publication check. Static markup, local assets, anchors and CSS report zero errors; the conservative file budget is now **593,330 bytes**, with unchanged **7,259-byte** JavaScript. No font, CSS, form handling or application behavior changed in this update.

Chrome access recovered. Actual owner overview, P&L, balance sheet, cash scenario and POS baseline captures were inspected and saved under `docs/design/product-screens/04-*` through `08-*`. Their compressed website derivatives now resolve, with correct intrinsic dimensions and originals preserved. Static markup/asset/anchor/CSS checks report zero errors, with a conservative 591,936-byte file budget and 7,259 bytes of JavaScript. This includes the first owner screen and POS screen plus both lazy secondary photographs; it is not a browser timing measurement. Expanded tabs/disclosure still need their final responsive acceptance pass after the redesign decision.

The POS audit found current basket/Pay reachability problems on tablet/mobile and a possible implicit Enter submission. [POS research](../../docs/design/POS_RESEARCH.md) records the screenshots, official comparison and narrow authorized correction. In isolated company 5, Enter in search, quantity and tender did not submit; deliberate Enter on Record cash sale saved source 15 / journal 11. The Core accounting sample was not modified. JavaScript syntax and PHP view lint passed; the parallel HTTP workstream reports 21/0. JavaScript-disabled browser operation remains a runtime check.

Full-page capture timed out once, so viewport screenshots were retained. Valid mailto handoff to the operating-system email application, disabled-clipboard failure behavior, runtime reduced-motion emulation, exact browser zoom at 200%, screen-reader testing, timing-based performance and observed user sessions were not completed by this workstream. Code contains the reduced-motion and clipboard fallback paths; that is not runtime proof. No claim of WCAG compliance or measured usability success is made.

Migrations: no. Schema changes: no. Raw real secrets/customer records exposed: no. External calls in this workstream: read-only research, licensed photo downloads, image generation and a local build dependency download. Live changes by this workstream: no. Google Drive documents read: none.
