# PHP Ledger marketing website

The deployable static document root is `www/website/public`. It does not load the accounting bootstrap or expose the repository root. It uses local HTML, CSS, JavaScript, Inter, licensed photographs and captures of the working development application. No build step, database, analytics or third-party page-load requests are required.

The user selected [Field Notes](../../docs/design/website/01-field-notes.png). [Asset provenance](../../docs/design/website/SOURCES.md), [content decisions](../../docs/design/website/CONTENT.md) and [visual QA](design-qa.md) accompany the implementation.

**Current status:** development preview prepared for the user's authorized website/demo launch. The lead owns publication and the final live checks; this note does not claim they have completed. All referenced screenshots resolve locally, but reports and POS still have documented design work ahead. See the [POS research](../../docs/design/POS_RESEARCH.md).

## Local preview

The repository's `website` Compose service serves this document root on `http://127.0.0.1:18201/`. A plain static file server can also serve the same folder. The temporary design preview used port 18420. These previews do not themselves establish live demo behavior.

## Public paths and interactions

- `/` or `/index.html`: product overview, owner report tabs, cash POS chapter, expandable accounting walkthrough, roadmap, support, contribution information, FAQs and pilot interest.
- `/credits.html`: photography, font, icon and interim identity provenance.
- `/demo/`: points to the separately maintained isolated PHP demo. The deployment must route this prefix correctly. Never map it to the legacy repository code.
- Anchor paths include `#features`, `#point-of-sale`, `#accounting-details`, `#roadmap`, `#enterprise`, `#quick-start`, `#contribute`, `#docs`, `#faq` and `#pilot`.
- The existing `#docs` area and footer link to the [project Wiki](https://github.com/rmak78/phpledger/wiki) and [Getting Started](https://github.com/rmak78/phpledger/wiki/Getting-Started). The installation FAQ explains that the first new installable package is being prepared, the default branch remains legacy and the public demo is for evaluation. The lead must publish and verify those Wiki destinations alongside the website.

Each report/walkthrough tab group has its own arrow-key/Home/End navigation. Product captures open in a native modal with keyboard focus return and a scrollable image area. The mobile menu closes after navigation or Escape. Reduced-motion CSS disables animation and smooth scrolling.

The pilot form prepares a mailto draft for `rmak78@gmail.com` or copies a reviewable message. No message is automatically sent, no registration is completed and no visitor data is stored by this page. Copy failure exposes a selectable preview. JavaScript-disabled visitors receive a direct email link.

## Publication contract

Publish only `public` and retain the local relative asset paths. The lead owns the authorized hosting backup, upload, HTTPS, demo routing and final public checks. No deployment action was performed by the website workstream.

Screens and copy describe development capabilities. Forecasts are user-entered scenarios; current POS handles sample catalog cash sales. Tax, stock/COGS, card processing, credit sales, invoice/bill-based receivables/payables and historical imports are not represented as complete. License/provenance review remains pending; the website does not assert an unverified software license.

Page-weight figures in the QA record are explicit file budgets, not Lighthouse/Core Web Vitals results. Preserve image dimensions, responsive hero sources and lazy loading when replacing captures.
