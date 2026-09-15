# Licensing policy

Owner decision: 15 September 2026. Applies to the current development line from the AGPL adoption commit forward. See [the authoritative decision register, section D](strategy/DECISION-REGISTER.md#d-decisions-taken-by-the-owner-15-september-2026-evening).

## The accounting core remains open source

PHP Ledger's project-owned accounting core and documentation use **AGPL-3.0-or-later**. The core remains open source. The current module line, including the POS showcase, uses the same AGPL terms. Published **0.1.x previews remain under the MIT grant they shipped with**, including their bundled project-owned modules. Their archives, checksums, release receipts and existing recipient rights are unchanged.

The [full AGPL text](../LICENSE) governs the open-source grant. It allows commercial use and requires its applicable source-sharing and notice obligations to be met. For a modified version used interactively over a network, section 13 requires an offer of corresponding source to those users. The code licence grants no trademark rights. [Licence scope](../LICENSE-SCOPE.md) preserves third-party and historical terms.

Self-hosting for your own business is always free under the open-source licence. The software must never require a licence key or contact a licensing server. Free use does not remove the obligations of the licence; paid hosting, setup, training and support are optional services.

## A commercial licence is available

The project offers a separate commercial licence on request to parties that want permission to use the core outside AGPL terms. Examples include embedding the core in a closed product or operating a modified hosted version without making those changes available under the AGPL source-sharing requirement. A commercial agreement must define the covered code, permissions and support separately; no prices or automatic entitlement are stated here.

Commercial licensing concerns project-owned code and rights actually held by the licensor. It does not waive independent dependency licences, asset permissions or trademark rights. Existing open-source grants cannot be withdrawn through a later commercial offer. New contributions require the [CLA](../CLA.md), which permits both AGPL distribution and commercial sublicensing while leaving copyright with the contributor.

## Future commercial modules are declared in advance

No new module is declared commercial by this policy. A future commercial module must be named and its licence stated before release. An already published open-source module will not be converted into a closed module by withdrawing its existing grant.

Candidates for later declaration are:

- Native desktop and Android clients, planned paid add-ons using the shared API/MCP contracts.
- An informal khata add-on restricted to a visibly **unposted sub-ledger**, with formalisation through normal accounting services; the owner reserves the product decision.
- A hosted FBR relay service or module.
- A multi-company consultant edition.

These are candidates, not released products or a committed price list. Shop POS and e-commerce/storefront are expected commercial drivers, but no proprietary licence is declared for them here. The existing POS showcase remains part of the AGPL development line.

This policy supersedes earlier statements that “modules are not a paid feature tier”, that all future modules necessarily share one licence, or that commercial add-ons are outside the product direction. Those earlier statements remain historical where preserved in release receipts.

## Contribution and publication controls

The owner-approved PR #65 revert (`a157e52`) and owner-authored accessible-label replacement (`916aebb`) are present in the current branch. Git history and the published 0.1.5 attribution remain intact. This records the scope of the transition; it does not settle the separate historical Sutlej-era chain of rights.

The CLA text and pinned workflow are configured locally. Enabling them on GitHub, creating the signature branch, observing a real signature/recheck and requiring the check before merge belong to the authorised publication/setup step. No signature is fabricated or backdated.

Before the next release, rebuild its package with the new licence and dependency notices, and update README, Wiki, website and demo together. The already built, unpublished 0.2.0 candidate predates this decision and is installation evidence only; it must not be presented as the next approved release artifact.

References: [GNU AGPL version 3](https://www.gnu.org/licenses/agpl-3.0.html), [CLA Assistant action](https://github.com/contributor-assistant/github-action), [licence review](LICENSE_REVIEW.md). The action repository was archived on 23 March 2026; the requested existing action is pinned, and its hosted operation remains to be verified.
