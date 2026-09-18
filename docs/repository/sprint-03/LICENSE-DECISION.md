# First-package licence decision

Work item: [#55 — Licence and provenance](https://github.com/phpledger/phpledger/issues/55). Related: [package contract](PACKAGE-CONTRACT.md) and [exact candidate inventory](package-candidate.json).

**Decision pending: MIT or GNU AGPLv3 for PHP Ledger's new project-owned code.** Both are viable with the inspected dependencies when their separate terms and notices are preserved. No project licence has been selected, no `LICENSE` has been created, and this audit does not certify ownership. Package engineering can continue while the owner makes this one policy choice.

## Reviewed scope

The inventory contains **78 existing files / 1,392,413 bytes**. All 78 hashes still matched at this audit on 14 September 2026. It covers the new application, Composer metadata, three original starter/sample/catalog resources, Inter, Tabler icons, the current horizontal logo and CLDR-derived regional data. Production `vendor/` is a required build output, not a copy of the developer's dependency folder.

The local checkout remains on the legacy commit `fe528eb52a8be277f3b2806022d23a817c01bda8`; these 78 files are not in that commit. The first archive needs a reviewed source revision and final file manifest. The hosted preview and public README/Wiki do not establish a versioned source release.

Marketing photographs, partner logos, designer galleries, Manrope/Poppins review fonts, research reports, industry sample packs and historical root code are outside this customer-runtime candidate. Their existence elsewhere in the repository must not expand its licence scope automatically.

## Verified third-party inventory and notices

| Shipped component | Verified source and terms | Package requirement |
|---|---|---|
| MeekroDB **v3.1.5** | Composer lock pins `98a400845cffd6cafbbec38eae1a61b0e695a446`. Package metadata says `LGPL-3.0`; `db.class.php` explicitly permits version 3 or later. `orm.class.php` has no equivalent individual header. Use v3 obligations as the conservative baseline, preserving both existing statements. [Pinned upstream licence](https://raw.githubusercontent.com/SergeyTsalkov/meekrodb/98a400845cffd6cafbbec38eae1a61b0e695a446/LICENSE). | Retain the library's source, copyright headers and LGPL text; add the accompanying **GPLv3 text** and prominent MeekroDB notice. Keep the library replaceable. Do not relabel it MIT or remove its notice if the application uses AGPL. |
| Composer-generated runtime loader | Local `vendor/composer/LICENSE` is MIT, copyright Nils Adermann and Jordi Boggiano. [Composer upstream notice](https://raw.githubusercontent.com/composer/composer/2.8.11/LICENSE). | Retain the generated `vendor/composer/LICENSE`; record the actual Composer version used by the clean build and check its resulting notice. This reference version is not a claim that the current build used 2.8.11. |
| **Inter v4.1** `InterVariable.woff2` | The 352,240-byte font and its licence both match the official `rsms/inter` v4.1 files exactly. SIL OFL 1.1, copyright 2016 The Inter Project Authors. [Pinned notice](https://raw.githubusercontent.com/rsms/inter/v4.1/LICENSE.txt). | Preserve `public/assets/fonts/LICENSE.txt`. Keep the font under OFL; neither project option relicenses it. The current candidate contains the unmodified upstream WOFF2, not the website's Latin subset. |
| **21 Tabler outline SVGs** | Each SVG and the licence match commit `55f87a73f45cf1d9eaf16d7da705065483a9e4f9`. MIT, copyright 2020–2026 Paweł Kuna. [Pinned notice](https://raw.githubusercontent.com/tabler/tabler-icons/55f87a73f45cf1d9eaf16d7da705065483a9e4f9/LICENSE). | Keep `public/assets/icons/LICENSE.txt` and `SOURCE.txt` with the icons. Add their source/version to the package notices. |
| **Unicode CLDR 48.0.0** regional subset | `resources/locale/country-defaults-cldr48.json` records the three source JSON URLs and hashes. All three source hashes and the bundled licence were verified against upstream. Unicode License v3, copyright 2015–2024 Unicode, Inc. [Pinned notice](https://raw.githubusercontent.com/unicode-org/cldr-json/48.0.0/LICENSE). | Retain `resources/locale/UNICODE-LICENSE.txt`, its README and derivation/source metadata. Do not imply Unicode endorses PHP Ledger. |

The lockfile has **one production dependency**: MeekroDB. PHPStan 2.2.14 is MIT but development-only; it must not be in the production vendor payload. PHP, MySQL, Apache, operating-system packages and Docker images are prerequisites/build infrastructure for this PHP-source archive, not bundled binaries. A future container distribution needs its own complete image inventory and notices.

OFL permits bundling the font with software while requiring its copyright and licence to remain available; restrictions include standalone font sales, reserved names when applicable, and relicensing the font itself. These do not require PHP Ledger application code to use OFL. [Official OFL terms](https://openfontlicense.org/open-font-license-official-text/).

## MeekroDB combination requirements

The existing bootstrap loads Composer's autoloader and calls the MeekroDB interface; it does not paste the library into application functions. PHP source and vendor files remain separately inspectable and replaceable.

For this arrangement, preserve LGPL §4's notice, licence-copy and modification/recombination conditions. The inspected upstream `LICENSE` contains **only the LGPL supplement**; the final archive must also carry the GPLv3 text that it incorporates. Provide PHP Ledger source and installation material in a usable form and avoid restrictions that prohibit debugging a modified library. If the application displays copyright notices during execution, the corresponding MeekroDB notice and licence reference must be included. These are package acceptance checks, not completed UI changes. [LGPLv3 §4](https://opensource.org/license/lgpl-3-0), [GPLv3 text](https://opensource.org/license/gpl-3.0).

MIT application code can coexist with this LGPL library under those conditions. AGPLv3 is also a viable application option: preserve the library's terms, and review any future combined-work changes against LGPL permissions and the GPLv3/AGPLv3 combination provisions. This finding covers the inspected dependency arrangement, not arbitrary future plugins or proprietary integrations.

## Project code, data and logo provenance

The new bootstrap, route entry points and installer load the new application's helpers and Composer dependencies. They do not load the historical root application. A bounded lexical screen compared **44 candidate PHP/CSS/JavaScript files** with **681 tracked historical PHP/CSS/JavaScript/SQL files**: no identical files and no matching whitespace-normalized blocks of 12 nonblank lines with at least 240 characters were found. This is a useful reuse screen, **not proof of independent authorship or copyright clearance**.

The [legacy review](../../LICENSE_REVIEW.md) records unresolved historical licensing. A fresh author-name inventory contains more names than its initial summary; author aliases are not a rights-holder register. Do not apply a new licence retroactively to all historical contributions or require every historical contributor's permission merely because history is retained. Any actual copied/adapted protected material needs its own provenance resolution. Existing accounting vocabulary and general workflow concepts are research inputs; the source review does not establish the extent of every influence. Local BixiSoft/Agency75 conventions likewise do not transfer that project's code or rights by implication.

The six-account starter, Cedar Trading sample and six-product POS catalog are marked preliminary, sample or fictional in their files. No imported customer records, accounting-standard text or commercial script dataset was found in these three candidate resources. Record them with the new project material rather than implying that a country template or third-party chart was licensed for redistribution.

The preferred horizontal logo is a **user-supplied 2172 × 724 image**. Its supplied PNG and the 678,094-byte application PNG have different encodings, but decoded RGBA pixels match exactly. Application-file SHA-256: `859a749124e98c121b096cc37e24ab70d095225fe4b4da8b0554eca50051c6f4`. The user explicitly authorized use, cropping and modification. That authorization supports the requested use; this audit does not invent a designer assignment or claim exclusive ownership. The alternative designer SVG/PNG package uses a different wordmark and is not the candidate's source. See [brand record](../../BRAND.md).

Keep logo provenance and any trademark/endorsement policy distinct from the software licence. Selecting MIT or AGPL for code does not by itself settle every underlying artwork right or authorize misleading endorsement. No additional formal-assignment approval is imposed here; if broader standalone logo redistribution terms are needed, document them accurately rather than treating the image as an unspecified third-party open-source asset.

## Two project options

| Decision | MIT | GNU AGPLv3 |
|---|---|---|
| Main effect | Broad reuse, redistribution and commercial integration with the notice retained. | Copyleft for covered derivatives, including a source-offer obligation for modified versions used through a network. |
| Downstream closed forks | Allowed for the project's MIT code; bundled dependencies still retain their own obligations. | Covered modifications cannot be distributed as closed replacements; modified network versions must offer their corresponding source to their remote users. |
| Support business | Paid setup, support and hosting are compatible. | Paid setup, support and hosting are compatible; AGPL is not a ban on commercial use. |
| Fit with project direction | Fits free access and broad adoption, but does not require third parties to keep their modifications open. | Fits a stronger expectation that users of modified hosted versions can obtain those changes. |
| Release work | Add the approved MIT notice and accurate copyright attribution; preserve all separate dependency notices. | Choose an explicit SPDX form (`AGPL-3.0-only` or `AGPL-3.0-or-later`), include the licence, and provide the exact corresponding source and appropriate notices. |

MIT's permissions and notice condition are set out in the [OSI MIT text](https://opensource.org/license/mit). AGPL §§4–6 and 13 define distribution/source obligations and permit paid support. Network source goes to users of the modified covered program; this is not a demand to publish customer accounting records or secrets. Separate independent works need their own assessment rather than being automatically relicensed because they communicate with PHP Ledger. [AGPLv3 text and §13](https://opensource.org/license/agpl-3.0).

**Owner decision to record:** choose MIT for maximum reuse flexibility, or AGPLv3 for source-sharing obligations on covered modifications, including hosted ones. Neither option has been applied. The record should identify the authorised copyright attribution and scope without asserting ownership over historical or third-party material.

## Release acceptance after the choice

1. Record the selected project licence and scope; use it consistently in release documentation and Composer metadata. Preserve third-party terms and keep excluded historical material outside the package.
2. Generate the clean production vendor tree. Add `THIRD-PARTY-NOTICES.md`, both LGPLv3 and GPLv3 texts for MeekroDB, and all existing font/icon/Unicode/Composer notices. Check the final archive, not just source paths.
3. If AGPL is selected, make the exact source of the released/running version available, with necessary installation/build material and legal/source navigation. A link to the legacy default branch is insufficient. Keep credentials and customer data excluded.
4. Record the candidate revision, build tools, dependency versions and final hashes. Provide contribution guidance under the selected inbound licence; do not silently adopt a CLA, copyright transfer or DCO policy.
5. Recheck any new package file or dependency against this inventory before [#59 release acceptance](https://github.com/phpledger/phpledger/issues/59). Build preparation may continue; do not label an unlicensed candidate as an approved open-source release.

## Audit receipt

Read local licence/legacy/brand/package records, the 78-file inventory, bootstrap/install/runtime references, Composer manifest/lock/vendor notices, font/icon notices and sources, regional provenance, sample resources and historical source inventory. No private configuration, customer data, credential store or Google Drive document was read for this audit.

Checks completed: **78/78 candidate hashes; 28/28 exact upstream file comparisons** (21 icons, their licence, Unicode licence, three MeekroDB files, Inter font and licence); **4/4 CLDR source/licence hashes**; full decoded-logo pixel equality; and the bounded historical reuse screen described above. The Unicode licence is included in both comparison groups, so these are not 32 unique upstream files. GNU-hosted pages timed out; the published FSF licence texts on the Open Source Initiative site and pinned upstream licence files were read instead.

Only this Markdown decision document was created. No application code, migration/schema, licence file, dependency, GitHub issue or live system was changed. External calls: read-only official/upstream sources. Raw secrets exposed: no. Runtime tests and browser checks were not rerun for this documentation-only task. Final package generation, notice assembly and the owner's licence decision remain pending.

## Owner decision — 14 September 2026

**MIT approved.** The owner selected MIT for the new project-owned PHP Ledger code and documentation. This decision supersedes the pending-choice language in the historical audit above. The [root licence](../../../LICENSE) records the grant; [licence scope](../../../LICENSE-SCOPE.md) preserves separate historical, dependency, font, icon, data and company-mark terms.

The historical application is preserved under `legacy/` in the published source layout, while the modern application is `www/phpledger`. MIT does not retroactively relicense historical contributions or third-party material. MeekroDB's LGPL/GPL texts and the other required notices remain package requirements.

The first installable package is still pending its actual artifact, notice verification and release acceptance. This documentation update does not create a release tag, publish an archive or establish a new runtime test result. Publication is handled separately by the release lead.
