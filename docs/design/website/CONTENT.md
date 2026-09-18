# Website copy and interaction contract

## Wording rule — 18 September 2026

- Never write "synthetic" on the site. The first capture on a page carries "Screens show a fictional sample business, Willow Corner Shop." and captions say "Sample business"; elsewhere write "sample data", "sample records" or "fictional business". The older labels "Product design preview · Synthetic data" and "Development preview · Synthetic company data" below are superseded.
- Version, date, size, checksum and URLs come from `src/site.json`; never type them into a page.
- No prices, no phone number, no personal email address, no personal LinkedIn profile, no invented counts, testimonials or delivery dates. Contact runs through GitHub Discussions (the enquiry form prepares a message for a new discussion) and, for private security reports, SECURITY.md; the organization's no-reply address is not a contact route.
- Headings are one idea each; no eyebrows, chapter numbers or two-fragment "X. Y." headings. Banned words: empower, seamless, streamline, robust, effortless, unlock, elevate.
## Current redesign completion: 15 September 2026

The website-redesign homepage and shared static generator have product, cash POS, download, support, roadmap, news and credits destinations. The owner selected **A, Workbench, with B's photo treatment**, and the homepage headline is exactly **Double-entry accounting that runs on your own PHP and MySQL hosting**. The site was published on **15 September 2026 at 08:08 UTC (13:08 PKT)** as `website-redesign-20260915-080700`; 101 static files and 36 live route/width browser checks passed. See the [publication receipt](qa/live-20260915-publication.json) and [website QA](../../../www/website/design-qa.md#live-publication-15-september-2026).

The task's fresh public check identifies [**v0.1.2-preview**](https://github.com/rmak78/phpledger/releases/tag/v0.1.2-preview), published 15 September 2026, as the latest published release and **0.1.2-preview** on the live demo. The downloaded package's SHA-256 matches its published checksum and GitHub digest; included INSTALL, UPGRADE and RELEASE-NOTES were read. Release-backed additions include account statements, audited account administration and general journals, with six migrations and the improved cash POS retained from 0.1.1. Existing product captures show the **0.1.0 development preview with synthetic sample data** and retain that explicit provenance; the original 0.1.0 news announcement is historical. Opening cutover, historical imports, bank reconciliation, AR/AP and tax remain outside that package. The roadmap must not present local opening balances/cutover → period administration → bank reconciliation work as contents of the published ZIP.

Basic on-site SEO includes page titles/descriptions, canonical links, OG/Twitter metadata, structured data, robots, sitemap, llms and RSS. Search-console verification/submissions, IndexNow setup, social handles/tokens, additional page-specific OG variants and campaign execution remain pending. Website publication does not establish completion of those external or editorial actions.

The support enquiry prepares a reviewable email draft or copy on the visitor's device. It has no submission endpoint, storage, automatic messaging or payment. All product images use synthetic records; photograph subjects are illustrative. New source, asset and responsive/keyboard checks are recorded in [website QA](../../../www/website/design-qa.md). The earlier composition contract below is preserved as historical context.

Prepared for the three homepage compositions on 2026-09-14. The chosen composition determines layout; this record keeps the offer and interaction truth consistent. Final published capability wording must be checked against the sprint's actual validation results.

## Opening

Status: **In development · Pilot conversations open**

Headline: **A day's work. Clearer books.**

Introduction: Accounting for owners, accountants and bookkeepers. Explore what we're building and help shape the next release.

Primary action: **Explore the product** — scrolls to the user-controlled product walkthrough.

Secondary action: **Join the pilot** — opens the pilot invitation section, with no implied reservation or automatic submission.

Supporting points: Clear daily tasks. Traceable entries. Your own hosting.

## Product walkthrough

**Follow one expense. See the whole story.**

From a saved expense to a balanced entry and a report you can trace.

Three deliberate controls switch between **Record expense**, **Review entry**, and **See report**. Use actual captures of the same verified synthetic transaction once the working sprint is available, with a visible sample-data label and readable enlarged view. The captured amount, currency, source reference and report date must agree. No autoplay, fake animation of financial posting, or simulated public demo sign-in is required.

Before real captures are supplied, any displayed approved concept must explicitly say **Product design preview · Synthetic data**. Once replaced, use **Development preview · Synthetic company data**. Neither label means a stable release or a hosted accounting service is available.

## Current work and future path

**A practical start, with room to grow.**

We are building the first connected accounting journey: company setup, a simple expense or receipt, a balanced journal and a report that leads back to its source. We are reviewing it with owners and accountants before a stable release.

Use three clear roadmap groups:

- **This milestone:** the connected core accounting journey and local validation. List its final tested capabilities only after the root's sprint evidence is available.
- **Next:** historical-data imports with preview and reconciliation, then broader accounting workflows and supported pilots.
- **Later:** reviewed business/country account templates, localization, multicurrency/foreign-exchange accounting, multiple books, inventory, POS and industry workflows. No country tax or regulatory coverage is promised by this list.

Do not add dates, adoption counts, certification, guaranteed performance, funding totals, or claims of released modules.

## Support and contribution

**Own your software. Choose the help you need.**

The product direction is open-source software and modules, with optional paid help for installation, training and troubleshooting on customer-owned hosting. Support packages, supported environments and response expectations are being defined. The project's license and provenance review remain in progress; do not claim a selected license or unrestricted redistribution.

Actions: **Ask about support** leads to the approved email contact; **View the repository** leads to `https://github.com/rmak78/phpledger`. Keep release/setup statements synchronized with verified repository guidance, and do not link to unpushed files as though they exist publicly.

**Help build the next release.** Invite experience reports, accounting review and contributions through the repository and pilot conversation. Funding is milestone-based preparation; no donation button, payment collection, funded amount or reward promise is included.

## Pilot invitation

**Help shape the next release.**

Tell us how you keep your books today and what you need from the next release. We are preparing a small supported pilot; fit and availability will be discussed individually.

The draft composer asks for role, business type, country and a short bookkeeping challenge. Do not request account credentials, financial files, invoices or other sensitive records. Use form labels, sensible autocomplete where relevant and recoverable field errors. No browser storage, analytics, email provider API, database capture or automatic sending is introduced.

Primary action: **Open email draft**. Secondary action: **Copy message**. Explain that the visitor reviews and sends the email in their own email application. If clipboard access fails, leave the message available as selectable text. Never show **Sent**, **Submitted** or **You're registered** merely because a draft opened or text was copied.

Destination: `rmak78@gmail.com`. Subject: `PHP Ledger pilot interest`.

## Contact and partners

Email: `rmak78@gmail.com`

Phone: `[public phone removed]`, with `tel:[public phone removed]`.

Postal address: BixiSoft Office, M5 First Floor, Innovista Chenab Arcade Plaza, Sector C, DHA Multan, Punjab 60000, Pakistan.

Supporting partners: **BixiTech · BixiSoft · Agency75**. Use exact names in plain text unless an authorized official logo is supplied. Do not invent partner websites, certifications, customer relationships or shared legal ownership.

Photo caption: **Illustrative photography. Not customer endorsements.** Include photographer/source credits and the Pexels license link in the local credits material.

## Useful existing anchors

Retain `#features` for the product story, `#quick-start` for setup context, `#docs` for contributor/documentation guidance, `#enterprise` for support and `#faq` for the FAQ. Also use readable anchors for roadmap, contribution and pilot contact. A section can carry an alias anchor without adding another page or an unnecessary menu item.

The website is self-contained static content. Only user-activated ordinary outbound links, phone links and email drafts leave it. No accounting credentials, database bootstrap, session handling or application storage belongs in its public directory.
