# Current licence decision - 15 September 2026

The owner selected **AGPL-3.0-or-later** for the project-owned development line from the adoption commit forward. The accounting core remains open source; a separate commercial licence is available. Published 0.1.x previews retain their MIT grant, archives and receipts. [Licensing policy](LICENSING-POLICY.md), [scope](../LICENSE-SCOPE.md) and [CLA](../CLA.md) record the current terms. Earlier statements below are historical evidence and are superseded where they discuss the choice for new modern code.

The owner considers an enduring open-source core non-negotiable. The strategy review identifies competitors' BSL/source-available direction as an opportunity for clear open-source terms; that is positioning rationale, not a new feature or an independently verified market-size claim. AGPL source-sharing and commercial sublicensing were selected together. The CLA supplies explicit individual/entity rights for AGPL distribution, dual-licensing customers and future declared commercial modules while contributors retain copyright.

The copyright holder can offer new terms for its own work. Existing MIT permissions remain available to their recipients; adding an AGPL grant does not revoke them. The current branch includes the explicit contributor-change revert `a157e52` and owner-authored accessible-label replacement `916aebb`, with public history preserved. This is the owner's recorded basis for the modern transition. It does not replace the separate rights-chain review for Sutlej-era material, independent legal review of the CLA/commercial agreement, or third-party licence obligations. No outside contribution is asserted to have signed a retroactive CLA.

The AGPL text was obtained without modification from [SPDX's AGPL-3.0-or-later text](https://github.com/spdx/license-list-data/blob/main/text/AGPL-3.0-or-later.txt) after the GNU site timed out. SHA-256: `d8a6cc31abc16b6748c7a21f21611f5a1ec33f67d22ca23d7da1c19b95496bee`. [GNU AGPL](https://www.gnu.org/licenses/agpl-3.0.html) remains the canonical licence reference.

The requested [CLA Assistant action](https://github.com/contributor-assistant/github-action) is pinned to v2.6.1 commit `ca4a40a7d1004f18d9960b404b97e5f30a505a08`. Its repository was archived on 23 March 2026. Workflow configuration is local; no signature, GitHub enforcement, unprotected signature branch or live run has been established by this change. No PR code is checked out by the privileged workflow. The next authorised publication must create the branch, verify the document URL and a real signature/recheck, then enforce the check before accepting contributions.

---

> **Current decision, 14 September 2026:** MIT is approved for new project-owned code and documentation; see [LICENSE](../LICENSE), [scope](../LICENSE-SCOPE.md) and [the recorded decision](repository/sprint-03/LICENSE-DECISION.md). The historical audit below is retained as evidence and its pending-choice language is superseded for the modern code. Historical paths are retained only in Git history; their provenance remains separate.

# License and provenance review

Status: **open; no new project license has been selected or applied.** The product direction is that software and modules remain open source. The inspected historical repository does not contain a project-level license grant sufficient to treat that selection as settled. Its README's invitation to use the application is historical wording, not the chosen license for the revival.

This is a source-file inventory and review checklist, not a legal opinion. Preserve existing notices and resolve rights/provenance before public contribution or funding launch.

## Observed inventory

| Component or evidence | Observed notice/provenance | Required next step |
|---|---|---|
| Original project README | Names Sutlej Solutions and invites use; no identified standard project license | Confirm original ownership and intended license grant |
| Git history | Contributions include Rana Mansoor Akbar Khan and `waseem238`; this is not a complete rights inventory | Review contributors and provenance of reused project code |
| `includes/classes/meekrodb.2.2.class.php` and `meekrodb.2.3.class.php` | Sergey Tsalkov copyright; LGPL version 3 or later notice | Keep legacy notices; review the separately pinned current Composer dependency and its own license |
| `includes/classes/html_table.class.php` | Sharon Paine copyright 2001â€“2014; MIT notice | Preserve applicable notice if reused |
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
