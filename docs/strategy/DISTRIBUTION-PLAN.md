# Distribution plan

Drafted 15 September 2026 from the [decision register](DECISION-REGISTER.md) (B2, A1, A2, C7, C8) and the [Pakistan market](PAKISTAN-MARKET.md) findings. Distribution is treated as a product milestone with owners and gates, not as marketing. Channel names and mechanics were checked on the date above; re-verify submission processes before acting. Nothing here is a claim that any channel is live.

## Principles

1. **Install counts are a distribution outcome.** Akaunting's reach came from being one click away inside hosting panels; FrontAccounting's decline tracks its absence from them.
2. **The person who installs is not the person who uses it.** For the target tier the installer is an accountant, a software house or a hosting reseller (see A2). Every channel below is chosen for that person.
3. **Do not distribute a half product.** Broad listing before AR/AP and the FBR client exist would create a first impression of "ledger with no receivables" and a support load with no revenue. Cheap, developer-facing channels go first; mass channels wait for the trader-complete release.
4. **No phone-home for licensing, ever.** The only outbound contact is an opt-in, anonymous update check that the administrator can disable; its payload is documented. This is the line between us and Akaunting, and it is also how installs get counted.
5. **Every channel needs an upgrade path, not just an install path.** The in-app updater (signed release manifest, pre-upgrade backup, migration check, rollback) is the retention feature and ships before the mass channels open.

## Channels

| Tier | Channel | Why | What it requires from us | Gate |
|---|---|---|---|---|
| 1 — Hosting panels | **Softaculous** (cPanel, DirectAdmin, Plesk, CyberPanel, ~thousands of hosts) | The single largest source of PHP-app installs on shared hosting; AGPL qualifies for the free library. Softaculous builds the official package on request; a custom package (info.xml, install.xml, install.php, upgrade.php, zipped release) can be given to individual hosts immediately. | A release zip with a non-interactive install (config from parameters, database from parameters, admin from parameters); an upgrade script; PHP 8.2 floor; a demo URL and screenshots. | Trader-complete release (AR, AP, FBR client). |
| 1 | **Installatron** | Second panel installer; strong on Plesk and DirectAdmin. | Same package discipline as Softaculous; application submission through Installatron. | Same. |
| 1 | **Pakistani cPanel hosts** as direct partners | The panel installer is generic; a local host preloading the custom package and naming PHP Ledger in its plans is the reseller relationship in miniature. Five hosts cover most of the Pakistani shared-hosting market. | The custom Softaculous package plus a one-page partner offer (co-marketing, first-line support boundary). | Same; owner-led outreach through existing networks. |
| 2 — Self-host ecosystem | **Docker Hub image + compose** (official `phpledger/phpledger`) | Baseline for every other Tier-2 channel; what a software house or agency actually runs. | Multi-arch image, health check, documented env, tagged per release; compose with MySQL 8.4. | Now (the repo already ships compose files). |
| 2 | **Managed open-source hosts: Elestio, PikaPods, Hossted** | They host open-source apps for a monthly fee and share revenue with the project (PikaPods and Elestio both pay maintainers). Zero operations for us, and it gives non-technical accountants a "hosted PHP Ledger" without us running a SaaS. | The Docker image; a listing request; a maintainer account for the revenue share. | Docker image plus first stable tag. |
| 2 | **App platforms: Cloudron, YunoHost, CapRover, Coolify, Umbrel, Unraid Community Apps, TrueNAS apps, Portainer templates** | Each is a self-hoster community with its own catalogue; AGPL is accepted by all. | A package manifest per platform (most are thin wrappers around the Docker image); YunoHost and Cloudron need their own packaging and a maintainer. | After Docker image; low effort each, do in a batch. |
| 3 — Registries and releases | **GitHub Releases** (exists) | Already the canonical download. | Signed checksums; release notes that state scope honestly (existing policy). | Now. |
| 3 | **Packagist** (`composer create-project phpledger/phpledger`) | How PHP developers and software houses expect to obtain a PHP application. | `composer.json` metadata, a `create-project` layout, tagged releases. | Now. |
| 3 | **Windows desktop bundle** (portable PHP 8.2 + MariaDB + PHP Ledger, one installer, runs on localhost) | The accountant's office computer is the only computer in the chain (12% household ownership). A Windows bundle is the cheapest "desktop app" and satisfies the owner's desktop intent without native code; the same bundle becomes the LAN server for a shop. | Build script, signed installer, start/stop tray, backup to a folder, update through the in-app updater. | Trader-complete release. |
| 4 — Discovery | **awesome-selfhosted, selfh.st, OpenAlternative, AlternativeTo, SourceForge mirror, Product Hunt, r/selfhosted, Show HN** | Where "open source accounting" searches land; SourceForge specifically because FrontAccounting's users live there. | Listings need a stable release, screenshots, a demo, and honest capability wording. | Trader-complete release; one launch week, not a trickle. |
| 4 | **"Alternative to" pages on phpledger.com** | Search intent is comparative: Akaunting alternative, QuickBooks Pakistan, Peachtree replacement, FBR digital invoicing software. | Pages that state the comparison truthfully, including what we lack. | Owner's SEO workstream (already high priority). |
| 5 — Pakistan channel | **Tax consultants and accounting firms** (ICAP, ICMAP, Pakistan Tax Bar members) | They operate the books for most registered SMEs and are the buyer of the consultant edition. | Multi-company installation guide; Annex-A/C/I exports; a short "for practitioners" page; consultant edition pricing when declared. | AR/AP plus FBR client. |
| 5 | **Training institutes teaching Peachtree/QuickBooks/Tally** (IPATS, Mirchawala, Careervision and similar) | Their graduates become the accountants who choose software. Free course material with PHP Ledger replaces a decade-old pirated Peachtree in the curriculum. | A teaching pack: sample company, exercises, certificate of completion. | Trader-complete release. |
| 5 | **Software houses currently selling white-labelled PHP ERPs** | Same hosting and price niche; they could ship PHP Ledger under a partner badge instead of a script they cannot maintain. | Partner programme: implementation training, badge, lead sharing, boundaries on branding (trademark). | After first five real installations (C9); partner programme drafted then. |
| 5 | **P@SHA and the owner's existing networks** | Warm introductions to software houses and hosts. | Nothing new; use the current outreach. | Now. |
| 6 — Retention | **In-app updater** | Every channel above becomes a dead install without upgrades; the updater is also the install counter. | Signed release manifest; pre-upgrade backup; migration checks (exist); rollback; opt-in anonymous version ping. | Before Tier 1 opens. |

## Sequence

| Phase | When | Channels opened | Why this order |
|---|---|---|---|
| 0 — Developer channels | Now, alongside AR/AP work | Docker Hub image and compose; Packagist; GitHub Releases with checksums; Elestio/PikaPods/Hossted listing requests; Portainer/CapRover/Coolify manifests | Cheap, no support load, reaches the software houses and agencies who are the realistic early installers. Managed hosts start paying revenue share as soon as anyone deploys. |
| 1 — Trader-complete release | When AR, AP and the FBR digital-invoicing client pass their gates (the register's Layer 1) | In-app updater; Softaculous request and custom package; Installatron; Pakistani host partners; Windows bundle; Cloudron/YunoHost packages; directory listings and one launch week; practitioner page | Mass channels open only for a product a trader can run a business on. |
| 2 — Channel programme | After five real businesses and one accountant sign-off (C9) | Partner programme for software houses; training-institute pack; consultant edition (when declared) | Partners need a proven product and a support boundary before they will carry it. |
| 3 — Regional | With each Middle East adapter (UAE, KSA, Oman) | Same channels, plus regional hosts and Arabic listings | Follows the tax-adapter sequence in the register (A7). |

## Measurement

Installs cannot be counted from the software itself without a phone-home, so the count is assembled from channels that report:

- Opt-in anonymous update-check pings (version, PHP version, country from IP at the edge, nothing else) — the primary "active installs" number once the updater ships.
- Docker Hub pulls; Packagist installs; GitHub release downloads (already available).
- Softaculous publishes per-script install counts; Installatron reports to the vendor.
- Managed-host deployments (Elestio, PikaPods report to maintainers).
- Partner-reported installations under the partner programme.

Targets are set by the owner; the register's 90-day target (five real businesses, one accountant sign-off) precedes any install target. A suggested first install target after Phase 1: 100 active installs reporting through the update check within six months of the trader-complete release, with at least 20 in Pakistan.

## Costs and owners

| Item | Owner | Cost basis |
|---|---|---|
| Docker image, Packagist, release signing, manifests | Technical lead | Development time only |
| Softaculous/Installatron packages and submissions | Technical lead | Development time; no listing fee for open-source scripts (verify at submission) |
| Windows bundle | Technical lead | Development time plus a code-signing certificate |
| Managed-host listings | Owner | Free; revenue share inbound |
| Host partnerships, P@SHA outreach, training institutes | Owner | Time; co-marketing collateral |
| Partner programme | Owner + technical lead | Training material, badge, support boundary; priced after the first five installations |
| Directory listings, launch week | Owner (SEO workstream) | Time |

## Open checks before Phase 1

- Softaculous official inclusion process and turnaround (custom packages are documented; official library inclusion is by request).
- Whether Pakistani hosts run Softaculous or Installatron (or both) and which panel versions.
- Code-signing certificate for the Windows bundle.
- Revenue-share terms at Elestio and PikaPods at the time of listing.

## Sources

- Softaculous custom package documentation — https://www.softaculous.com/docs/developers/making-custom-package/
- Softaculous script requirements — https://www.softaculous.com/docs/admin/scripts-requirements/
- Softaculous Akaunting listing (precedent for an accounting app in the library) — https://www.softaculous.com/apps/erp/Akaunting
- Invoice Ninja auto-installer list (Softaculous, Cloudron, Elestio, Umbrel, Installatron, Hossted, Coolify) — https://www.invoiceninja.org/auto-installers/
- Elestio managed Akaunting — https://elest.io/open-source/akaunting
- PikaPods and alternatives — https://expresstech.io/8-pikapods-alternatives-in-2026-self-host-without-a-vps/
- Pakistan market evidence — [PAKISTAN-MARKET.md](PAKISTAN-MARKET.md) and [research/pakistan-sme-bookkeeping-reality.md](research/pakistan-sme-bookkeeping-reality.md)
