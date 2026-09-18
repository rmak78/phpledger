# Competitive landscape

Research recorded 15 September 2026. Part of the [strategy reference](README.md); decisions drawn from it are in the [decision register](DECISION-REGISTER.md). Star counts, licences and versions are point-in-time observations from the sources listed at the end; re-verify before quoting any of it publicly. Nothing here is a claim about PHP Ledger's own completed capability — see the [README](../../README.md) and the [module roadmap](../MODULE-ROADMAP.md) for that.

## Why this document exists

The project's stated direction is open-source, self-hosted double-entry accounting for SMEs on customer-owned hosting, funded by defined installation, training and support services. That direction is only defensible if we know which projects already occupy it and where they are weak. This document records the observed field and the gaps it leaves.

## Tier 1 — direct competitors (self-hosted SMB accounting)

| Project | Stack | Licence | Observed position |
|---|---|---|---|
| Akaunting | Laravel, Vue, PHP 8.1+ | **BSL — not an OSI-approved open-source licence** | ~10.1k GitHub stars; the best-known name in the category. Core capability sits behind a paid App Store, and self-hosting users have publicly reported paywalls affecting access to their own installations. The clearest positioning gap available to an MIT-licensed project. |
| Bigcapital | Node/TypeScript, React, Docker, pnpm + Lerna monorepo | AGPL-3.0 | ~3.9k stars, 521 forks, ~5,075 commits. Genuine double-entry, active development, paid cloud tier. The closest functional competitor, but its Docker/Node deployment does not target modest PHP hosting. |
| FrontAccounting | Legacy PHP (project docs still recommend PHP 5.6 / 7.x) | GPL-3.0 | Real double-entry with long SME use; GitHub mirror carries ~112 stars with the primary project on SourceForge. Occupies our exact hosting niche and is ageing out of it. |
| LedgerSMB | Perl, PostgreSQL | GPL | Strong accounting correctness, small community, a stack with limited contributor supply. |
| webzash, OSPOS and similar | PHP | Various | Largely unmaintained or shaped as retail point of sale rather than accounting. |

## Tier 2 — ERPs that absorb accounting

- **Odoo Community** — very large install base and mindshare. The Community/Enterprise split repeatedly moves accounting capability into the paid edition, and deployment assumes meaningful server resources.
- **ERPNext / Frappe** — complete double-entry, notable adoption across South Asia and the Gulf, active development. Practical deployment expects roughly 4 GB RAM and competent operations; it is not a modest VPS target.
- **Dolibarr** — PHP, actively maintained, installs on ordinary shared hosting. Double-entry accounting was added over an existing ERP data model. The nearest project to us on hosting philosophy.

## Tier 3 — invoicing tools that drift toward accounting

Invoice Ninja (Laravel, the largest of these), InvoicePlane, Crater and SolidInvoice. They serve businesses that only need to bill customers, and reach their limit at general ledger, trial balance and period close. Their users are a plausible source of early adopters for an accounting-first product, but only once receivables exist.

## Tier 4 — personal ledgers

Firefly III, ezBookkeeping (~5.6k stars, MIT), Maybe, and the plain-text tools Beancount, hledger and Ledger. These are personal finance rather than business accounting. They compete for search visibility under the phrase "open source accounting" without competing for the same user.

## Tier 5 — the commercial products SMEs actually evaluate

QuickBooks, Xero, Wave, Zoho Books, Manager.io and FreshBooks. Few small businesses self-host by preference. The observed reasons for choosing self-hosted software are recurring cost, data control, and tax or reporting requirements that the commercial products do not serve in a given jurisdiction.

## Gaps this field leaves

1. **Licence clarity.** Akaunting's move to the Business Source Licence left no widely known, genuinely open-source PHP accounting application. Our MIT position, with all modules open and no paid feature tier, is a real and stated difference.
2. **Hosting floor.** Bigcapital expects Docker, ERPNext expects memory and operations skill, and Odoo effectively expects an implementation partner. The lowest practical hosting requirement is unoccupied since FrontAccounting stopped keeping pace.
3. **Jurisdiction coverage.** Every project in Tier 1 and Tier 2 treats country tax and e-invoicing as a downstream concern or a partner responsibility. A reviewed adapter for a specific regime is a differentiator that the incumbents are unlikely to contest.
4. **Accounting correctness.** Immutable posted entries, linked reversals, reviewed periods and a traceable audit path are what allow a qualified accountant to accept a system. Most Tier 3 projects do not attempt this. It is the area where our existing core is already ahead of its category.

## Risks this field also demonstrates

Self-hosted SMB accounting has a long record of abandoned projects, low direct monetisation, and slow adoption relative to effort. Feature breadth is not a durable advantage against Odoo or ERPNext, both of which have far greater capacity. Distribution — package availability, one-click installers and container images — determines install counts at least as much as capability does, and is not currently a scheduled milestone.

## Sources

- Self-hosted accounting category listing — https://openalternative.co/categories/accounting-software/self-hosted
- Akaunting repository and licence file — https://github.com/akaunting/akaunting
- Community discussion of Akaunting licence and free-tier changes — https://itsfoss.community/t/akaunting-accounting-software-no-longer-free-or-open-source/11385
- Bigcapital repository — https://github.com/bigcapitalhq/bigcapital
- FrontAccounting mirror repository — https://github.com/FrontAccountingERP/FA
- Dolibarr double-entry accounting overview — https://www.dolibarr.org/presentation-double-entry-accounting.php
