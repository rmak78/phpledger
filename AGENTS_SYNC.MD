# Shared Claude and Codex state

**Current scope (Claude, 19 September 2026, evening):** housekeeping and roadmap correction only. Commits the outstanding coordination edits, the pending demo-wording lines and the roadmap documents that now describe sample companies and plugins as separately built directory packages. No application code beyond those two demo-wording lines, no release, no push, no website rebuild, no deployment. The owner's demo, installer and application-surface feedback is still to come and is not part of this scope.

## Latest maintenance checkpoint — 19 September 2026 (Codex)

- Local `master` was safely fast-forwarded from `75f85f7` to `97a26b4`, matching `origin/master`. The SEO changes are now represented by upstream commits; the three unique tracked instruction/coordination files and two unique untracked instruction files were preserved. Verified pre-update backup and sync receipt: `C:\phpledger-checkout-backup-20260919-153540`. No commit or push was made.
- The owner explicitly requested a private Google Drive backup of the signing folder plus a plaintext passphrase file, and selected keeping Windows Credential Manager. All 11 top-level files were uploaded and read back byte-identically. The backed-up passphrase unlocks the backed-up key; Drive permissions are owner-only. Backup details and receipt remain outside the repo in `C:\phpledger-signing-key\cloud-backup-receipt.json`. This is a cloud backup; no offline copy was created. Do not print or copy key/passphrase contents into agent messages.
- Sample-guide capacity wording is already corrected upstream in `c9743d4`. The two obsolete demo landing preview labels are corrected locally in `www/phpledger/templates/views/demo.php`, documented in `docs/DEMO.md`, and await Claude's next patch release. Sample-scenario review limitations remain intact.
- The later [1.1.0 publication receipt](docs/repository/PUBLICATION-2026-09-19-1.1.0.md) supersedes the older website/demo deployment snapshots below. This maintenance task made no live deployment or new publication claim. Real shared-host and unfamiliar-operator observation, independent accounting/security reviews, supervised pilots and cross-platform ZIP reproducibility remain open.

The earlier checkpoints below are retained as dated history.

**2026-09-19 signing-key handoff (Codex):** owner-requested RSA-4096 publisher key generated outside the repository. Public PEM: `C:\phpledger-signing-key\publisher-public.pem`; SHA-256 of DER SubjectPublicKeyInfo: `4e58a5f46b0538c9b37aaadbfced9a2d8ad2f7d413bc168a67b1b94feaa78e78`. Encrypted private key and passphrase remain local, with the passphrase in Windows Credential Manager. OpenSSL, Python and independent PHP checks passed; owner offline backup remains pending. No release archive/manifest signed, key published/pinned, or live system changed by this key-generation task. See the latest AGENT_MESSAGES entry and local KEY-CUSTODY.md. Claude retains the 1.1.0 integration/deployment lane.

Updated: **2026-09-19 13:50 UTC / 18:50 PKT** (Claude: WordPress-style install package implemented locally; see below). The earlier snapshot below, dated 2026-09-18 22:40 UTC (Codex, live SEOScore), is unchanged.

## Local work in progress

All of the branches formerly listed here shipped in 1.1.0 (`origin/master` `97a26b4`, tag `af0f489`), so there is no unreleased local feature work at this checkpoint. The worktrees still exist and their branches are fully merged into `master`; they are retained as history and can be removed with `git worktree remove` when the owner wants the folders gone.

| Branch / worktree | Owner | State |
|---|---|---|
| `wordpress-style-install` at `.claude/worktrees/install-package` (9b7375d) | Claude | Merged and released in 1.1.0. |
| `distribution-protocol` at `.claude/worktrees/distribution-protocol` (608f40e) | Claude | Committed, merged and released in 1.1.0 (release protocol, platform roadmap, `VERSION`, update-channel library, release builder and feed generator). |
| `fix-template-encoding` at `.claude/worktrees/template-encoding` (cfef7d9) | Claude | Merged and released in 1.1.0. |
| `fix-sample-start-gate` at `.claude/worktrees/sample-start-gate` (0005d2b) | Claude | Merged and released in 1.1.0. `tools/verify-demo.ps1` previously failed at `demo_smoke.php` line 144 on demo capacity; the default demo record ceiling moved to 250 in 1.1.0, so re-run the script before treating this as a live failure. |
| `release-1.1.0` at `.claude/worktrees/release-1.1.0` (97a26b4) | Claude | Equal to `master`; the 1.1.0 release and website publication ran from here. |

Pending for the next patch (1.1.1), not live: the two demo-landing wording lines in `www/phpledger/templates/views/demo.php` (committed locally at this checkpoint, see the latest message) and the owner's forthcoming demo and installer feedback.

The owner's XAMPP MariaDB (`C:\xampp\mysql\data`) has been corrupted since 1 August 2025 and does not start. The local test copy `C:\xampp\htdocs\phpledger-test` uses the `pl-xampp-db` container on 127.0.0.1:3310 until the owner confirms removal.

Read this file and the latest [AGENT_MESSAGES.MD](AGENT_MESSAGES.MD) entries before starting a task. This is the current coordination snapshot; messages retain the handoff history. Follow [AGENTS.md](AGENTS.md), [CLAUDE.md](CLAUDE.md) and the [operator runbook](docs/DEMO.md#operator-deployment-runbook-for-claude-and-codex). Recheck cheap live facts before using this snapshot for another release.

## Working together

- Before shared edits, record the agent, task, checkout/source and files or deployment surface being handled. Check the working-tree diff and latest messages to avoid conflicting with another agent's work.
- One agent owns a live deployment at a time. Coordinate the website and demo serially because their helpers can pin the same vhost baseline. Do not publish from a competing prepared plan while another agent is switching it.
- Preserve unrelated changes; do not reset the checkout, clean broadly, overwrite another agent's files, stage everything or push simply to complete a local/deployment task.
- At completion or handoff, update this snapshot and append a timestamped message with status, action, evidence and remaining work. Keep earlier messages intact. If a record is wrong, append a correction rather than silently rewriting the history.
- An explicit owner deployment request authorizes the necessary reviewed actions within that scope. Coordination does not create a new approval gate or a reason to ask for the same permission again.
- These are repository coordination files, **never website or application web artifacts**. Keep credentials, environment contents, database rows and private logs out of them.

## Current website: correct product design is live

| Item | Verified state |
|---|---|
| Public URL | `https://phpledger.com/` |
| Deployment | **Completed**, `website-1-0-0-20260918-223528`; SEOScore **97 / SEO 98 / AEO 94 / GEO 96** |
| Completion | 2026-09-18 22:37:15 UTC / 19 September 2026 03:37:15 PKT |
| Intended source | `master` base `75f85f7` plus the reviewed website-only SEO patch; source manifest in `.cache/seo-20260919/source-manifest.json` |
| Selected checkout | `C:\phpledger`, current master plus reviewed static patch. Retain `.cache/website-redesign-live` as the original design snapshot. |
| Design identity | H1: **PHP Ledger: self-hosted double-entry accounting software**; same desktop/phone captures, Pricing and Community, plus sourced summary/comparison |
| Static inventory | **295/295 files verified**; 70 HTML outputs; zero build/check errors or warnings |
| Publisher | `.cache/publish-seo-20260919.py`, frozen source/public manifest and reviewed live baseline; not a generic updater |
| Recovery backup | `/var/www/phpledger/data/backups/website-1-0-0-20260918-223528`; original/intermediate backups retained |
| Receipt | [SEO publication](docs/design/website/qa/seo-2026-09-19-publication.json) and [score evidence](docs/design/website/qa/seoscore-2026-09-19.json) |

**Source history:** the initial `record-demo-cutover` checkout had an older design also labelled 1.0.0. Claude subsequently merged the product source and deployment records; the main checkout is now `master` at `75f85f7`, with website source matching `86a1eef` before this SEO patch. Select the current reviewed source for future deployments. The older same-version site was initially published during this task; the selected master design above was then deployed and verified. Identify a requested design by its source commit, headline/screenshots and routes, not the release number alone. The old `.claude/worktrees/festive-elgamal-8a1d61` directory is empty.

The website changes the static root and the exact `/support` and `/support/` redirect pair, both `301 /pricing/`. The active vhost is `/etc/nginx/fastpanel2-sites/phpledger/phpledger.com.conf`; the older `fastpanel2-available` copy can be stale. Preserve the demo proxy, OAuth locations, TLS/ACME and existing security/noindex headers. Treat published server release directories as immutable; use a new reviewed source snapshot and release identifier for future edits.

## Demo and authentication

The demo is now **`core-1.0.0-sample-20260919-r2`**, completed at **2026-09-18T21:20:15Z**. It retains the official 1.0.0 package/source `1415ceb61cab2fe27be3947d611a16f4035e54c7` as its baseline, with a separately recorded **33-file sample-wording overlay** and unchanged runtime image. The original package manifest is preserved for provenance; use `DEPLOYMENT-OVERLAY.json` for the changed runtime hashes instead of expecting the unmodified ZIP hashes to describe every current file. The guarded sample-only reset refreshed temporary visitors and reapplied the same 32 existing migrations; no new migration or schema definition was introduced. Its database container/volume and the website/demo proxy were preserved. Fresh starter and historical sample guides plus reports passed. See [wording receipt](docs/repository/DEMO-1.0.0-SAMPLE-WORDING-2026-09-19.json). The one-shot older demo scripts require their recorded baseline and must not be replayed.

Start fresh inspection with `python tools/hosting-status.py`. The product publisher reuses `.cache/publish-website-1.0.0.py` for the saved **Windows Credential Manager** identity and pinned SSH key. No password prompt is needed. Do not request, print or reset passwords; do not bypass host-key checks. A release-baseline mismatch means the deployment plan is stale, not that the saved credentials failed. The base publisher's default static source is the older main checkout, so use the product wrapper for the design above.

## Handoff and remaining work

- **1.1.0 is live (Claude, 19 Sep 2026 15:35 UTC):** release v1.1.0 (tag `af0f489`, signed metadata, key fingerprint `4e58a5f4…aa78e78`), demo `core-1.1.0-af0f4895f85a`, website `website-1-1-0-20260919-152351`, origin/master `97a26b4`. The sections above describing the 1.0.0 website and demo are now historical; see `docs/repository/PUBLICATION-2026-09-19-1.1.0.md` and the latest message. The main checkout is behind origin/master and still holds the SEO patch as uncommitted edits identical to the committed files.

- **Distribution protocol and platform roadmap (Claude, 19 Sep 2026):** merged and released in 1.1.0; `docs/RELEASE-PROTOCOL.md` and `docs/strategy/PLATFORM-ROADMAP.md` are on `master`. The earlier note that master could not be packaged is obsolete.

- **Owner scope correction, 21:18 UTC:** Claude is the requested owner for the complete account-numbering, collapsible-report and Pakistan pharmacy sample-company feature work. Codex owns only website/demo deployment and the deployment handoff. This is a requested file-based handoff, not confirmation that Claude has started.
- **Feature status: pending; no feature implementation was made or deployed by Codex.** Scope for Claude: padded `X-XXX-XXXXX-XX` account codes and conversion of existing account numbers using their current groups; named parent/subaccount hierarchy such as `1-000-00000-00 Assets` → `1-001-00000-00 Cash` → `1-001-00002-00 Petty Cash` → `1-001-00002-01 Qasim` / `1-001-00002-02 Office`; collapsible Trial balance, Profit & loss and Balance sheet; and a Pakistan pharmacy sample company using an Odoo chart-of-accounts reference with fictional data. Claude must inspect the latest messages and actual WIP before continuing. Do not include unfinished feature changes in deployment.
- **Feature stop receipt:** both feature subtasks stopped before implementation. The account/report helpers, report templates and partials, `public/assets/app.js`, `public/assets/app.css` and `resources/ui/ext/reports.css` have no scoped changes; no feature patch, CSS rebuild or feature test was produced. Research and proposed interfaces are recorded in the messages. The owner explicitly requested no full retest.
- Codex completed the live website correction, demo wording deployment, repository receipts and owner handoff. No concurrent live deployment is needed. Claude acknowledged feature ownership in the later messages; accounting/report/pharmacy work remains outside this website task.
- The earlier **320 px header overflow is resolved** in the SEO patch. Focused homepage checks now pass at **1440, 768, 390 and 320 px**, including the mobile menu, comparison-table keyboard scroll and contents anchors. Earlier ten-route evidence remains historical; no full retest ran.
- Browser evidence: `output/playwright/deploy-1.0.0-20260919/product-live-smoke-result.json` and `product-live-home-1440.png`. These local output files are not release assets.
- No new migration or schema definition was introduced. The demo wording cutover reused the runtime image and reset temporary sample data using the same 32 existing migrations. No Git push was made and no credentials were exposed. Follow the live receipts and final handoff for later verification updates.

## Repository cleanup for the fresh start — 2026-09-18 22:25 UTC (Claude)

- Pull requests #68 (org move), #69 (sample wording) and #70 (demo cutover record) are merged. No pull request is open.
- Remote branches: only `master` and the CLA bot's `cla-signatures` remain. Merged branches were deleted. The unmerged experimental branches were archived as GitHub tags before deletion: `archive/Branch-Test-2`, `archive/fix-jv-mansoor`, `archive/new-phpledger`.
- Local branches: only `master`. Codex's unmerged local branches were archived as tags before deletion: `archive/cash-account-integrity` (includes a WIP snapshot commit with `docs/repository/sprint-06/CASH-ACCOUNT-INTEGRITY.md`), `archive/owner-questions-ar-ap`, `archive/quotes-plugin`, `archive/regional-catalog-ux-review`. The owner pushes them with `git push origin --tags`.
- The work Codex had stashed before the fresh start (sample wording in demo packs, sample data, tests and docs; the receipts; the runbook; `tools/hosting-status.py`; these coordination files) is committed on `master` so the repository matches what is live. Nothing was dropped.
- Worktrees retained as Codex's pinned deployment snapshots: `.cache/release-0.6.0/source` and `.cache/website-redesign-live`. The `.cache/ux-integrity-fix` worktree was removed after its note was committed to its archived branch.
- Left for the owner to review and commit: `AGENTS.md` (adds the coordination-file rule) and `CLAUDE.md` (new, written by Codex); the app does not let Claude commit its own instruction files. `master` is ahead of `origin/master`; the owner runs `git push origin master --tags`.
- Next work (Claude, fresh session on 19 September 2026): the account-numbering, collapsible-report and Pakistan pharmacy sample features recorded in the handoff above. Codex keeps deployments.
