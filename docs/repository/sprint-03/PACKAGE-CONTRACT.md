# First-package contract

Work item: [#56 — Build and verify the first installable preview package](https://github.com/phpledger/phpledger/issues/56).

**Status: local inventory and implementation contract only.** No archive, source tag, licence decision, installer change or candidate validation run is produced by this document. `0.1.0-preview` remains a proposed name. The [machine-readable candidate inventory](package-candidate.json) records **78 existing files / 1,392,413 bytes**, their exact paths and SHA-256 hashes, plus the required outputs that do not exist yet. It must be refreshed deliberately when the candidate changes; it is not a release manifest.

## 1. Package shape and source baseline

Propose one customer PHP runtime archive, rooted at `phpledger-0.1.0-preview/`, containing the new application, its required resources and build-generated production Composer dependencies. Preserve the repository-relative runtime layout: `includes/bootstrap.php` derives `PL_ROOT` three levels above itself and loads `vendor/autoload.php` there. Setup, POS and regional services also resolve their resources relative to that root. Flattening `www/phpledger` would break these contracts.

The observed checkout is branch `revival/foundation` at **`fe528eb52a8be277f3b2806022d23a817c01bda8`**, with the new foundation untracked. None of the 78 candidate files exists in that HEAD tree. A plain archive of HEAD would therefore capture the historical application, not this runtime. Recent public README/assets/Wiki publication is a separate change stream and does not establish a clean committed runtime revision in this checkout.

Before a distributable build, select and review a clean source revision containing the exact foundation and lockfile. Record its commit, candidate version and migration checksums. Keep local working-tree hashes as inventory evidence only; do not describe them as a reproducible Git release. No commit or branch manipulation is performed here.

## 2. Exact input and output policy

`copy_existing_files` in [package-candidate.json](package-candidate.json) is the exact current allowlist. The grouped paths below explain it; a builder must not automatically expand a directory and accept newly appearing files.

| Existing inputs | Purpose |
|---|---|
| `composer.json`, `composer.lock` | Runtime/dependency constraints and pinned dependency resolution. |
| `www/phpledger/includes/bootstrap.php`, `config.local.example.php` | Existing bootstrap and empty private-configuration example. Never include an actual `config.local.php`. |
| Listed files under `www/phpledger/includes/functions/` | Shared accounting, access, setup, POS, report, regional, demo-guard and web helpers. |
| `www/phpledger/install/migrate.php`, `create-admin.php` | Existing CLI migration and initial-user entry points. |
| All five listed migrations, `001_foundation.php` through `005_demo_period_guard.php` | Preserve the complete checksum-verified migration chain, including demo tables/guards. Do not remove applied versions because the customer package is not a public demo. |
| Listed `www/phpledger/templates/` PHP files | Current layouts, partials and screens, including shared demo-view dependencies. |
| `www/phpledger/public/index.php` and listed public CSS/JS, logo, Inter font and icons/notices | The only web-accessible application tree and its current local assets. |
| `resources/coa/core-starter-1.0.0.json` | Versioned preliminary account template used by setup. |
| `resources/core-samples/core-accounting-1.0.0.json` | Explicit opt-in synthetic sample; never automatically applied to a real company. |
| `resources/core/pos-catalog.json` | The illustrative cash-POS catalog used by the server. |
| `resources/locale/country-defaults-cldr48.json`, `UNICODE-LICENSE.txt`, `README.md` | Runtime regional metadata with its upstream notice and provenance. |

The candidate needs these **new build/documentation outputs before distribution**: clean production `vendor/`; candidate-specific `README.md`, `INSTALL.md`, `UPGRADE.md` and `RELEASE-NOTES.md`; the owner-approved `LICENSE`; reviewed `THIRD-PARTY-NOTICES.md`; and a complete `PACKAGE-MANIFEST.json`. Their absence is an explicit release gap, not permission to invent content or copy unresolved notices. The final manifest must include generated dependency files, source/version identity and archive checksums, not just the 78 source inputs.

Exclude every source path not explicitly accepted, including:

- Historical root `*.php`, `assets/`, `includes/`, `modules/`, `install/` and `example-pages/`.
- `.git/`, `.cache/`, `output/`, local `vendor/`, all environment files, actual private configuration, storage/uploads, database dumps, logs and host-specific deployment material.
- `www/website/`, broad `docs/`, Wiki/design/research galleries, `.github/`, `tests/`, `tools/`, `phpstan.neon`, `AGENTS.md` and contributor/editor metadata.
- `resources/coa/research-index.json` and `resources/sample-data/`; those research/industry fixtures are not runtime dependencies of the current setup/POS/region services.
- Current `Dockerfile`, `compose.yaml`, `compose.demo.yaml`, `.dockerignore` and `docker/` from the customer PHP archive. They remain source-side development/demo references, not an already supported production installation profile.
- The repository's current `README.md`: generate a package quickstart matching the actual artifact instead of shipping marketing/default-branch status as installation guidance.

Input local `vendor/` is excluded while newly generated production `vendor/` is a required output. Reject symlinks/reparse points, path escapes, private files and unknown additions during inventory/build. Do not assemble a package by archiving the working directory wholesale.

## 3. Runtime and dependency requirements

The actual [Composer manifest](../../../composer.json) requires PHP `~8.5.0`, BCMath, PDO, PDO MySQL and mbstring. The [bootstrap](../../../www/phpledger/includes/bootstrap.php) independently rejects PHP below 8.5 and PHP 8.6 or later. MySQL 8.4/InnoDB is the current target; migration SQL uses `utf8mb4_0900_ai_ci`, constraints and triggers. Another database or PHP branch must not be advertised as supported without implementation and validation.

The lockfile contains one production package, `sergeytsalkov/meekrodb` **v3.1.5**, with a declared LGPL-3.0 notice. PHPStan **2.2.14** is development-only. That dependency notice is not the project licence decision. Generate vendor in a clean controlled PHP 8.5 build environment from the lockfile using production-only installation, disabled plugins/scripts as appropriate, optimized autoloading and platform checks; never copy the developer machine's vendor tree. Preserve required dependency notices. Record the Composer version and resolved build-image identity.

The current [Dockerfile](../../../Dockerfile) starts from `php:8.5.10-apache-bookworm`, copies Composer from the mutable `composer:2` tag, runs Composer without `--no-dev`, then copies its build context. Its [Compose file](../../../compose.yaml) mounts development/test trees, supplies a local environment, includes a marketing website service and disposable test services, and defaults to loopback HTTP. It is useful validation infrastructure but is not the proposed customer runtime package. A future Docker customer artifact needs its own reviewed production profile; do not relabel this development Compose file.

Customer requirements must include CLI access for the current installation commands, working PHP sessions with private writable session storage, an HTTPS web server, and front-controller fallback to `www/phpledger/public/index.php`. The [Apache reference](../../../docker/apache.conf) demonstrates that fallback at its container path; installation guidance must adapt the path and HTTPS setup to the tested host. No application `storage/` directory currently exists or is referenced for uploads, so do not create a blanket writable application tree as a substitute for finding actual writable requirements.

## 4. Configuration and installer reuse

Reuse these existing interfaces:

| Interface | Current behavior | Packaging implication |
|---|---|---|
| [Shared bootstrap](../../../www/phpledger/includes/bootstrap.php) | Reads `PL_DB_*` environment values, then an optional private PHP configuration override; requires a nonempty database password and connects with UTC/UTF-8 settings. | Document one real supported configuration path. The application does not parse `.env`; that file currently feeds Compose only. |
| [Migration command](../../../www/phpledger/install/migrate.php) | CLI-only; serializes with a database lock, tracks applying/applied receipts and SHA-256 checksums, rejects missing/altered/interrupted migrations, and replays applied versions safely. | Keep the runner and five migration bytes unchanged. Provide prerequisite/privilege checks and clear interrupted-DDL recovery instructions around it. |
| [Initial-user command](../../../www/phpledger/install/create-admin.php) | CLI-only; accepts password through stdin or a private environment variable, never a password argument. Creates an active user who becomes a company owner after company creation. | Reuse this command; do not introduce a second authentication system. The same email is protected by database uniqueness; another valid CLI invocation can create another user. This is not a first-run-only web installer or a global instance-admin role. |
| [Health route](../../../www/phpledger/public/index.php) | Checks bootstrap/database connectivity with `SELECT 1`. | A healthy response is not proof of completed schema, valid migration receipts, an initial user, or working business setup. Add those to package acceptance. |

Installation checks should run before creating the initial user and explain missing extensions, dependency autoload, configuration, database access, schema state and session-write failures without exposing secrets. The migration entry point currently loads bootstrap before its guarded migration call; a consolidated, safe preflight/error experience remains work to implement. Do not claim that the existing CLI checks are already the proposed complete installer.

Use production mode and HTTPS. The front controller derives secure cookies from `PL_ENV` plus the narrowly scoped local-demo exception; the `PL_SESSION_SECURE` value present in the development Compose file is not the control used by that code. Document `PL_BASE_PATH` only with a verified router/proxy configuration. Trust forwarded client addresses only from explicitly configured proxies that overwrite client-supplied headers.

The current optional country suggestion uses a bounded server HTTPS request and falls back to manual selection. Record that data flow and test failure/no-egress behavior; do not turn it into an undocumented installation dependency or perform live requests during package checks unnecessarily.

Separate installation/migration privileges from normal runtime privileges. The public demo has a specially verified restricted account, but that alone is not a reviewed privilege contract for every normal customer workflow. Test and document the minimal normal runtime grants and temporary DDL/trigger grants needed for installation and upgrade. Do not ship root credentials or the demo reset service. Demo guard code/migrations remain included because current services reference them; hourly reset scripts/configuration remain excluded.

## 5. Installation, upgrade and recovery acceptance matrix

Existing source-side tools are references to reuse, not evidence that this unbuilt artifact has passed. All candidate rows below are **pending**.

| Check | Candidate acceptance | Existing starting point / gap |
|---|---|---|
| Inventory and source | Exact clean revision, allowlisted files, dependency/notices inventory, deterministic paths/order/metadata, no private state, manifest hashes match. | This document and JSON inventory exist; no package builder or clean runtime revision exists yet. |
| Production dependencies | Fresh production vendor from lockfile on supported PHP; platform checks pass; no PHPStan/dev-only payload. | Current Docker build installs development dependencies too. |
| Clean installation | Unpack the actual archive into a fresh supported environment; configure private access; create schema and first user; sign in and set up a business. | Reuse CLI migration/user setup and the existing browser journey; no artifact installation has run. |
| Failure and re-entry | Missing prerequisite/config/privilege yields a clear safe error; replay does not mutate receipts; repeated user setup cannot duplicate the same email; no public unauthenticated user-creation route. | Existing service guards help, but there is no consolidated installer/preflight. |
| Router and privacy | Correct document root, HTTPS/session behavior, intended base path, unavailable private paths and source files, working HTML routes and health failure states. | Apache/public front controller and scoped HTTP checks exist; package-host configuration must be verified. |
| Accounting and POS | Company isolation; draft/post/source/report/reversal; exact cash/change; duplicate/conflict/concurrent checkout; closed-period/late-failure rollback. | Existing tests and HTTP scripts exercise source development services. Run against unpacked candidate bytes, not another checkout. |
| Supported upgrade | Baseline 001 → current chain retains account IDs/custom names, posted headers/lines and review-required openings; replay and checksum/error paths pass. | [verify-upgrade.php](../../../tools/verify-upgrade.php) implements a disposable baseline proof. Supported package-to-package versions do not exist yet. |
| Backup restoration | Isolated restoration preserves definitions/data, current migration receipts, guards, scoped source links and balanced journals; operator recovery steps are reproducible. | [verify-backup-restore.ps1](../../../tools/verify-backup-restore.ps1) currently targets only disposable `db_test`, expects five receipts and nine triggers. It is not a customer backup CLI. |
| Upgrade failure and rollback | Interruptions preserve an actionable applying receipt; restore known-good database plus compatible code from backup; do not claim SQL DDL rollback. | Migration runner rejects interrupted receipts. Customer maintenance/backup/recovery orchestration remains to be specified and tested. |
| Final acceptance | Syntax/static/dependency checks, current-scope accounting and usability review, release notes/known limits, public artifact checksum and independent quickstart installation. | [#59](https://github.com/phpledger/phpledger/issues/59) coordinates this evidence; no tag/download is created here. |

A validation harness must genuinely load the unpacked candidate. Existing test/tool files resolve paths relative to their parent, so running them from a different source checkout would test that checkout instead. Attach only a controlled, non-shipping test harness around an unpacked candidate and verify that all original candidate bytes remain unchanged. Run source static analysis with its development tooling separately from production dependency packaging; do not tell customers to run absent test scripts from a minimal archive.

The current GitHub workflow builds and tests the source tree and runs disposable restoration. It does not build a release archive, install that artifact independently, prove every supported package upgrade, or publish a release. Those steps need explicit implementation.

## 6. Publication-transfer inventories are not a package contract

Read-only inspection found an initial local publication manifest with **111 files**, including 26 website files and a demo deployment profile, and a later runtime-overlay manifest with **95 paths**. The overlay includes research/industry sample files not referenced by the current runtime. These were operational transfer snapshots; neither supplies the clean source revision, customer configuration, production vendor or package acceptance required above.

The deployment archives/manifests and hosted system were not modified. The 78-file contract uses actual runtime references to distinguish required resources from website/demo tooling and research. It does not assert that the hosted demo is an installable package.

## 7. Next concrete implementation

Implement a package-inventory checker under the existing `tools/` convention, consuming the explicit manifest. Its first mode should be read-only: report source-revision mismatch, changed/missing/extra inputs, path escapes/symlinks and required ungenerated outputs; never silently archive the dirty checkout. Test those rejection cases with synthetic fixtures.

Then, from a reviewed clean runtime revision, add clean staging/production-vendor generation, candidate operator documents and deterministic archive creation with a full output manifest. Prove installation and recovery from that artifact. Publication remains gated by [#55 licensing/provenance](https://github.com/phpledger/phpledger/issues/55), the agreed supported scope and [#59 acceptance](https://github.com/phpledger/phpledger/issues/59). No licence choice, source commit or archive build is authorized or performed by this documentation subtask.

## Inspection receipt

Read: current repository instructions/README/architecture/roadmap; Composer manifest/lock; Dockerfile, development Compose and Docker ignore/config examples; bootstrap, migration runner and all five migration paths; initial-user, session/authentication, routing, setup/POS/regional resource references; public/template file inventories; source tests, upgrade/restore helpers and CI workflow; the two existing local publication-transfer manifests. No private configuration, actual environment file, customer data, Google Drive document or credential store was read for this task.

Validation for this document is limited to local file inventory, hashes, dependency metadata, source-reference checks and documentation/JSON checks. No PHP/JavaScript code was changed, no candidate was installed or tested, and no migration/schema, GitHub or live-system change occurred. Raw secrets exposed: no. External calls made: no.

The local contract check passed: all 78 input size/hash pairs, all five migration entries, 13 local documentation links, four external URL shapes, JSON parsing and whitespace checks. No excluded private/research/test/deployment path appeared in the candidate allowlist. These checks do not satisfy the pending artifact installation, upgrade or restoration rows.
