# License and provenance review

Status: **open; no new project license has been selected or applied.** The product direction is that software and modules remain open source. The inspected historical repository does not contain a project-level license grant sufficient to treat that selection as settled. Its README's invitation to use the application is historical wording, not the chosen license for the revival.

This is a source-file inventory and review checklist, not a legal opinion. Preserve existing notices and resolve rights/provenance before public contribution or funding launch.

## Observed inventory

| Component or evidence | Observed notice/provenance | Required next step |
|---|---|---|
| Original project README | Names Sutlej Solutions and invites use; no identified standard project license | Confirm original ownership and intended license grant |
| Git history | Contributions include Rana Mansoor Akbar Khan and `waseem238`; this is not a complete rights inventory | Review contributors and provenance of reused project code |
| `includes/classes/meekrodb.2.2.class.php` and `meekrodb.2.3.class.php` | Sergey Tsalkov copyright; LGPL version 3 or later notice | Keep legacy notices; review the separately pinned current Composer dependency and its own license |
| `includes/classes/html_table.class.php` | Sharon Paine copyright 2001–2014; MIT notice | Preserve applicable notice if reused |
| `includes/classes/JSON.php` | Services_JSON author/copyright headers; BSD license reference and embedded terms | Preserve embedded terms if reused; do not assume legacy JSON code is needed on modern PHP |
| `assets/plugins/ckeditor/LICENSE.md` | CKSource copyright; GPL 2+, LGPL 2.1+, or MPL 1.1 terms stated in the bundled file | Review actual bundled version and any distribution choice if reused |
| CKEditor WSC/SCAYT plugins | Separate `LICENSE.md` files exist | Read their terms separately before reuse |
| jsTree, idleTimer, bootstrap-switch, cropper | Separate license files exist in those plugin directories | Inventory exact versions, terms, and required notices before reuse |
| Remaining bundled assets, templates, fonts, images | Full attribution/version inventory not yet completed | Identify source/license per retained asset; exclude unresolved assets from new distribution |

Finding a vendor license does not establish the license of the overall application. Public GitHub visibility and authorship in commit history alone do not resolve every reuse right.

## Review procedure

1. Identify rights holders for original application code and confirm the license decision with the product owner; record the selected SPDX identifier, scope, rationale, and permissions.
2. Inventory only dependencies/assets actually shipped by the new runtime, including exact locked versions and their licenses. Keep the broader legacy repository review separate from the new application bill of materials.
3. Prefer fresh implementation and maintained packages where legacy code is unnecessary. Preserve required notices for anything retained; do not strip copyrights or relicense third-party components by assumption.
4. Decide contribution terms and repository notices consistent with the selected project license. Add the approved license text and attribution materials only after this review.
5. Include license/dependency review in release checks and document unresolved items. Until resolution, do not assert that the revival already has an approved open-source license or is ready for unrestricted redistribution.

No license file or legal acceptance is created as part of the documentation milestone. Platform and legal eligibility checks require current sources and the user's actual receiving/ownership arrangements when undertaken.
