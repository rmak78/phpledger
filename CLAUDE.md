# PHP Ledger instructions for Claude

Read [AGENTS.md](AGENTS.md) first; it is the shared repository instruction source. Before each task, read [AGENTS_SYNC.MD](AGENTS_SYNC.MD) and the latest entries in [AGENT_MESSAGES.MD](AGENT_MESSAGES.MD), record your intended scope/ownership before shared work, and append the result and evidence at handoff. Then read `README.md`, `docs/ARCHITECTURE.md`, `docs/ROADMAP.md` and the documentation relevant to the requested work. Preserve unrelated changes and inspect the active checkout before acting. The coordination files stay in the repository and must never be copied into web artifacts.

For an owner-requested deployment, use the [operator deployment runbook](docs/DEMO.md#operator-deployment-runbook-for-claude-and-codex) and [website build/publication guide](www/website/README.md). Start with this read-only command from the repository root:

```powershell
python tools/hosting-status.py
```

Before building or selecting a deployment artifact, inspect the relevant branches, worktrees and recent website history, then identify the requested design by its source commit, homepage/screenshots and route set. A matching version number is insufficient: `record-demo-cutover` contains an older 1.0.0 website, while `master` at `86a1eef` includes the product-site rebuild `ac8fca3`. That rebuilt site is staged in `.cache/website-redesign-live`, has the headline “Complete double-entry accounting that runs on your own PHP hosting”, and includes `/pricing/` and `/community/`. Use the reviewed publisher configured for that source; do not publish whichever document root happens to be in the active checkout.

The configured Windows operator already has a Windows Credential Manager identity and a pinned SSH host key. Use the credential-safe helper described in the runbook. Do not ask the owner to paste a password, print credentials, reset credentials, or fall back to an interactive SSH password prompt. A release-baseline assertion failure is a deployment-state mismatch, not an authentication failure.

An explicit request to deploy the website and/or demo authorizes the necessary reviewed deployment steps for those named surfaces; do not ask for the same permission again. Inspect and prepare the concrete change, retain recovery evidence, perform the authorized cutover and verify public behavior. An ordinary design/code approval does not authorize publication. Keep unrelated domains, customer databases, messages, campaigns, payments and Git publication outside the requested deployment scope.

The versioned `.cache` demo scripts are historical, single-use release tools with strict baseline checks. Do not blindly replay them or remove assertions to make them pass. Follow the runbook for current-source, package, backup, migration and rollback requirements; report verified live results separately from local checks.
