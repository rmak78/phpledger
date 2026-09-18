# Live website and repository presentation check

Date: 14 September 2026. Independent Chrome check after the deployment workstream confirmed the HTTPS cutover. The website, published Wiki, README and working demo were inspected through their actual public URLs. No blocking presentation or navigation defect was found in the checks below. This is a bounded browser check, not acceptance of the unfinished report/POS redesign or a stable-release audit.

## Public website

Verified [https://phpledger.com/](https://phpledger.com/) shows **A day's work. Clearer books.**, the preferred bold-PHP/lighter-Ledger horizontal logo, the Kolkata workshop/shop hero, and the café and woodworking photographs. The local Inter font loaded. All three photographic images had loaded image data in the live DOM. The page identifies itself as in development and presents the imports, scanning, regional accounting and ERP work as future plans.

The first navigation briefly showed the prior cached page; an ordinary reload returned the new site at the same canonical HTTPS URL. The demo entry likewise needed one reload in the existing Chrome profile. Subsequent checks used the new pages. This observation does not establish a remaining origin/cache defect.

| Viewport tested | Observed result | Evidence |
|---|---|---|
| 1440 × 1000 | Header, preferred logo and split hero fit. Document client width and scroll width both 1425px. | [Desktop hero](desktop-hero.png) |
| 768 × 1024 | Menu replaces the full navigation. No horizontal overflow: client/scroll width 753px. | [Tablet hero](tablet-hero.png) |
| 390 × 844 | Logo and menu fit, hero stacks, report tabs use two columns. No horizontal overflow: client/scroll width 375px. | [Mobile hero](mobile-hero.png) |
| 320 × 844 | Header menu ends at x=291 within the 305px content width. Documentation and installation FAQ also remain inside that width. | [Narrow hero](narrow-320-hero.png) |

## Product interactions

- Owner report tabs: ArrowRight changed At a glance to Profit & loss; End selected Cash forecast. The selected state, visible panel and keyboard focus agreed.
- The accounting disclosure opened. ArrowRight changed Record expense to Review entry; End selected See report; Home returned to Record expense. The owner tab retained its separate selection.
- Enlarging the P&L screenshot loaded its 1440px source and focused Close. Escape closed the dialog and restored focus to the exact opening button. [Desktop dialog](desktop-profit-dialog.png).
- On mobile, Balance sheet selected and enlarged correctly. The modal fit the viewport. Its image region was internally scrollable (335px client area, 850px content); ArrowRight moved that region and focused it. Escape closed the dialog and restored its opener. [Mobile dialog](mobile-report-dialog.png).
- Tablet menu opened and Escape closed it with focus restored to Menu. Mobile Product navigation closed the menu and reached `#features`.
- The installation FAQ expanded and displayed the forthcoming package, legacy default-branch boundary and temporary demo guidance.

## Contact and documentation paths

The initial launch check verified the requested email and contact paths without placing a call or sending a message. The owner subsequently removed the phone from publication and shortened the location to **Innovista Chenab**; those changes are tracked in the publication receipt. The original supporting partners were BixiTech, BixiSoft and Agency75; the owner's later request adds verified linked partner logos and BrownBag.

The website's Documentation link opened the [published Wiki home](https://github.com/phpledger/phpledger/wiki). The exact [Getting Started page](https://github.com/phpledger/phpledger/wiki/Getting-Started) also loaded, showing the live preview announcement and the first installable package still being prepared. This was checked from actual browser responses, not search-engine cached content. The browser displayed 10 Wiki pages and the published sidebar/footer.

## Demo destination

[The public demo](https://phpledger.com/demo/) resolved to the working application in the existing sample browser session. From Transactions, Reports opened `/demo/reports` with the preferred prefixed logo and the owner overview. It displayed the sample company's PKR 875 cash, PKR 1,000 income, PKR 125 expenses and PKR 875 profit, along with the public-demo isolation/destructive-action notice and the reset time in Asia/Karachi. At 1440px the document client/scroll widths were both 1425px. [Live reports capture](demo-reports.png).

No financial record was edited or posted. This browser pass does not independently prove session isolation, destructive-action enforcement or the scheduled reset; those are owned by the deployment workstream's separate HTTP/reset checks.

## Published README at smaller widths

The [public repository README](https://github.com/phpledger/phpledger#readme), at commit `250b27d`, showed the preferred logo, development badge, owner overview, honest first-package note and Wiki links. The transaction and cash-POS sections were initially collapsed. Opening the POS section loaded its real preview image without widening the page, and it was collapsed again afterward.

GitHub had a pre-existing 90% browser zoom. Requested 768px and 390px browser surfaces therefore reported approximately **853px and 433px CSS viewports**, with document client/scroll widths of **836/836px** and **416/416px**. These are tablet/mobile checks at those actual logical widths, not claims of exact 768px/390px CSS coverage. A normal zoom-reset shortcut did not change the measured zoom. The temporary viewport override was reset afterward.

All five README images loaded. The logo and owner image stayed within the article: approximately 475.6px wide on tablet and 351.6px on mobile. The full-width image links support closer inspection; financial screenshot text naturally becomes small at mobile README width. [Tablet capture](readme-tablet.png) · [Mobile capture](readme-mobile.png).

## Scope and remaining checks

Only this local evidence folder was written in this pass. No application, website source, GitHub content, hosting configuration or remote file was edited. Migrations: no. Schema changes: no. Raw secrets exposed: no. Google Drive documents read: none. External/live calls: yes, read-only browser navigation to PHP Ledger and GitHub. Financial writes, account actions and messages: none.

No source-code change required lint or application tests. Saved PNGs were checked as readable image files and this report's local links were validated. This pass did not repeat the full pilot form/mail-client flow, disabled-JavaScript behavior, screen-reader testing, 200% zoom, reduced-motion emulation, Lighthouse timings or observed usability sessions. No WCAG conformance, complete browser support or product-readiness claim is made. Social-preview upload verification was performed by the lead, not this independent pass.
