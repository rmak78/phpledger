# Sprint 03 GitHub milestone and issues

## Current delivery

[v0.1.1-preview](https://github.com/phpledger/phpledger/releases/tag/v0.1.1-preview) is published; see the [package validation receipt](PREVIEW-0.1.1-VALIDATION.md). The published POS update implements product click-to-add and separate cash confirmation, with [its own validation receipt](POS-CHECKOUT-VALIDATION.md). Representative cashier sessions remain pending; package and hosted-demo publication are verified.

## Historical milestone creation receipt

The following records the initial publication of the milestone and issue bodies; its open-issue counts and proposed package status are historical.

Published and read back from GitHub: **[Sprint 03: First installable preview](https://github.com/phpledger/phpledger/milestone/4)**, milestone **4**, with five open issues. All five are unassigned; the milestone has no due date. `0.1.0-preview` remains a proposed candidate name, not a created tag or released package.

The [manifest](manifest.json) records the live issue/milestone URLs and IDs, dependencies, body hashes and verification result. The [milestone description](milestone.md) and local issue bodies match their published content:

| Key | Published issue | Body | Dependency |
|---|---|---|---|
| licence | [#55 Record the release licence decision and shipped-asset provenance](https://github.com/phpledger/phpledger/issues/55) | [01](01-licence-and-provenance.md) | Owner decision gates package publication. |
| package | [#56 Build and verify the first installable preview package](https://github.com/phpledger/phpledger/issues/56) | [02](02-installable-package.md) | Can prepare in parallel; publication depends on #55. |
| reporting | [#57 Implement a reviewed Pakistan profile and reproducible reports](https://github.com/phpledger/phpledger/issues/57) | [03](03-pakistan-profile-and-reports.md) | Reviewer-approved profile and rules precede affected behavior. |
| pos | [#58 Refine POS into a deliberate cart-to-cash checkout journey](https://github.com/phpledger/phpledger/issues/58) | [04](04-pos-checkout-experience.md) | Preserve current accounting and confirmation guarantees. |
| acceptance | [#59 Validate and publish the first scoped release candidate](https://github.com/phpledger/phpledger/issues/59) | [05](05-candidate-acceptance-and-release.md) | Release evidence from #55, #56, #57 and #58. |

The first package retains a bounded import feasibility/cutover decision. It does not commit a full importer where AR/AP/opening-document dependencies are unavailable. The original two-year statement fixture supplies reviewable outcomes; it is not installed sample data or a tax calculation today. AI Scan document and broad ERP remain later roadmap work.

Exact-title checks found no matching milestone or issues before creation. The authorized publication created one milestone and five issues, then updated the five issue bodies from files to link the resolved dependencies. Read-back checks confirmed every title/body, milestone membership, open state and lack of assignees; the milestone description matched its source and its due date remained unset.

Legacy issues #3, #5, #53 and #54 remain unchanged: before/after snapshots matched their title, state, body hash, milestone, labels, assignees, comment count and update timestamp. No existing milestone was changed. This work made GitHub issue/milestone changes only; it did not commit the main checkout, change the Wiki, create a release/tag, modify the application/schema or deploy a site.
