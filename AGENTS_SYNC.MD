# Shared Claude and Codex state

Updated: **2026-09-18 22:25 UTC / 19 September 2026 03:25 PKT** (Claude: repository cleanup for the owner's fresh start).

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
| Deployment | **Completed**, `website-1-0-0-20260918-210112` |
| Completion | 2026-09-18 21:02:58 UTC / 19 September 2026 02:02:58 PKT |
| Intended source | `master` at `86a1eef35fd497c7f5f708a47af227657fd4a2ee`, including product-site rebuild `ac8fca3` |
| Selected checkout | `C:\phpledger\.cache\website-redesign-live`, detached and pinned; retain it as this deployment's source snapshot |
| Design identity | H1: **Complete double-entry accounting that runs on your own PHP hosting**; desktop/phone hero captures, `/pricing/` and `/community/` |
| Static inventory | **295/295 files verified**; 70 HTML outputs; fresh build matched the previously prepared design archive |
| Publisher | `.cache/publish-product-website-1.0.0.py`, configured for that source |
| Recovery backup | `/var/www/phpledger/data/backups/website-1-0-0-20260918-210112` |
| Receipt | [live-1.0.0-product-publication.json](docs/design/website/qa/live-1.0.0-product-publication.json) |

**Source trap:** the main checkout remains on `record-demo-cutover`, whose website is an older design also labelled 1.0.0. Do not deploy its `www/website/public` when asked for the new design. The older same-version site was initially published during this task; the selected master design above was then deployed and verified. Identify a requested design by its source commit, headline/screenshots and routes, not the release number alone. The old `.claude/worktrees/festive-elgamal-8a1d61` directory is empty.

The website changes the static root and the exact `/support` and `/support/` redirect pair, both `301 /pricing/`. The active vhost is `/etc/nginx/fastpanel2-sites/phpledger/phpledger.com.conf`; the older `fastpanel2-available` copy can be stale. Preserve the demo proxy, OAuth locations, TLS/ACME and existing security/noindex headers. Treat published server release directories as immutable; use a new reviewed source snapshot and release identifier for future edits.

## Demo and authentication

The demo is now **`core-1.0.0-sample-20260919-r2`**, completed at **2026-09-18T21:20:15Z**. It retains the official 1.0.0 package/source `1415ceb61cab2fe27be3947d611a16f4035e54c7` as its baseline, with a separately recorded **33-file sample-wording overlay** and unchanged runtime image. The original package manifest is preserved for provenance; use `DEPLOYMENT-OVERLAY.json` for the changed runtime hashes instead of expecting the unmodified ZIP hashes to describe every current file. The guarded sample-only reset refreshed temporary visitors and reapplied the same 32 existing migrations; no new migration or schema definition was introduced. Its database container/volume and the website/demo proxy were preserved. Fresh starter and historical sample guides plus reports passed. See [wording receipt](docs/repository/DEMO-1.0.0-SAMPLE-WORDING-2026-09-19.json). The one-shot older demo scripts require their recorded baseline and must not be replayed.

Start fresh inspection with `python tools/hosting-status.py`. The product publisher reuses `.cache/publish-website-1.0.0.py` for the saved **Windows Credential Manager** identity and pinned SSH key. No password prompt is needed. Do not request, print or reset passwords; do not bypass host-key checks. A release-baseline mismatch means the deployment plan is stale, not that the saved credentials failed. The base publisher's default static source is the older main checkout, so use the product wrapper for the design above.

## Handoff and remaining work

- **Owner scope correction, 21:18 UTC:** Claude is the requested owner for the complete account-numbering, collapsible-report and Pakistan pharmacy sample-company feature work. Codex owns only website/demo deployment and the deployment handoff. This is a requested file-based handoff, not confirmation that Claude has started.
- **Feature status: pending; no feature implementation was made or deployed by Codex.** Scope for Claude: padded `X-XXX-XXXXX-XX` account codes and conversion of existing account numbers using their current groups; named parent/subaccount hierarchy such as `1-000-00000-00 Assets` → `1-001-00000-00 Cash` → `1-001-00002-00 Petty Cash` → `1-001-00002-01 Qasim` / `1-001-00002-02 Office`; collapsible Trial balance, Profit & loss and Balance sheet; and a Pakistan pharmacy sample company using an Odoo chart-of-accounts reference with fictional data. Claude must inspect the latest messages and actual WIP before continuing. Do not include unfinished feature changes in deployment.
- **Feature stop receipt:** both feature subtasks stopped before implementation. The account/report helpers, report templates and partials, `public/assets/app.js`, `public/assets/app.css` and `resources/ui/ext/reports.css` have no scoped changes; no feature patch, CSS rebuild or feature test was produced. Research and proposed interfaces are recorded in the messages. The owner explicitly requested no full retest.
- Codex completed the live website correction, demo wording deployment, repository receipts and owner handoff. No concurrent live deployment is needed. Claude acknowledgement remains pending.
- Browser checks passed ten routes at **1440, 768 and 390 px**. At **320 px**, the header causes the layout width to reach **376 px**. This is a known follow-up for Claude's next relevant website task; do not describe 320 px as passed. Preserve the pinned deployed source snapshot when preparing a fix.
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
