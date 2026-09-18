# Browser installer

Status: published in **1.0.0** (18 September 2026) as part of the consolidated 0.6.1/0.7/0.8 stable release; general shared-host qualification and unfamiliar-operator acceptance remain open post-release commitments. The [stable release checklist](ROADMAP.md#current-delivery-contract-first-stable-10) now controls sequencing. The original WordPress-style request remains: hosting-panel preparation, then browser setup without Composer, Node or shell. Existing CLI installation remains available. The installer-created customer website remains parked. Independent review and unfamiliar-operator/host acceptance remain separate gates.

## Local implementation contract

`GET/POST /install` runs before the configured application bootstrap. It requires HTTPS except explicit local/test loopback use, and is disabled for the hosted demo. The host provisions a random setup key of at least 32 characters in private `www/phpledger/storage/installation/setup.key`, or `PL_SETUP_KEY`. `PL_INSTALL_DIRECTORY` may point to a private directory outside the application; `PL_INSTALL_CONFIG_PATH` optionally selects a private configuration path. Neither may resolve into the public document root. Setup credentials never belong in a URL.

The browser verifies the key, checks the dedicated empty database, runs the original migration chain one migration per request, provisions private OAuth keys, creates the first account and hands off to existing business onboarding. Automatic progress has a manual no-JavaScript continuation. State and database identity protect retry behavior. Unknown/nonempty databases and incomplete/altered migration receipts stop for review. Writable private configuration is published atomically; otherwise an authenticated no-store download allows hosting-panel placement. Installation completion disables setup; deleting a flag does not authorize takeover of existing users.

A separate private `operator.key` authorizes installation-wide maintenance. It is not a company-owner permission and must be kept by the hosting operator. Application updates use the independent `/maintenance.php` entry point and their own signed-package, backup and recovery gates. PHP ZIP is an additional prerequisite for that update capability. Local automated evidence belongs in [Validation](VALIDATION.md); no live provider/hosting mutation is implied.

## Intended experience

An operator downloads the complete release ZIP, unpacks it and points the HTTPS hostname at `www/phpledger/public`. On a validated host, the normal fresh-install journey should then work in the browser without Composer or terminal commands. The wizard cannot install PHP/extensions, create DNS/certificates or grant database privileges; it explains missing prerequisites with hosting-panel guidance.

| Step | User experience and result |
|---|---|
| 1. Welcome and host checks | Explain the supported package profile, confirm explicit installation ownership/enablement, and check PHP/extensions, HTTPS, private sessions, routing, configuration access and database requirements. Show actionable failures. |
| 2. Database connection | Enter a dedicated empty database's host, port, name and credentials. Test connectivity, MySQL version/collation and required privileges. Reject an existing/unrecognized database. Explain how to create the database/user in a hosting panel. |
| 3. Install | Show the target and package version for review, then run the existing versioned migrations through a guarded installer service. Display progress and safe error messages. Preserve complete filenames, checksums, locks and migration receipts. |
| 4. Owner account | Create the initial sign-in account through the existing auth service. Use its current password rules; this is not a new global administrator role. |
| 5. First business | Continue into existing onboarding: new business, reviewed existing-business opening/cutover, or a separate sample. Reuse currency, date, account-template and membership rules. Optional modules retain their existing defaults. |
| 6. Finish | Verify schema/current identity, write the private installation completion state, disable installation access and open the application. Give backup and next-step guidance. Do not post a transaction into a real company as a test. |

## Architecture and boundaries

- Extend the shared bootstrap/configuration lifecycle to show a safe unconfigured state before attempting a normal database connection. Reuse the existing MeekroDB configuration and services; do not introduce another framework, router, auth stack or database abstraction.
- Refactor the existing CLI-only migration implementation into a shared internal service with separately guarded CLI and one-time installer entry points. Do not simply remove its CLI guard or expose migration scripts directly under the document root.
- Keep configuration in the current `includes/config.local.php` location outside `public/`, or retain operator-provided environment settings. Write configuration atomically with restrictive permissions when explicitly writable; otherwise provide a protected one-time download/copy fallback for the operator. Never make the entire application writable.
- Require explicit one-time installer enablement and proof of control through the hosting environment, such as a temporary private setup key. A publicly reachable empty installation must not let an arbitrary visitor claim the first account. Use CSRF, secure sessions, no-store responses, bounded attempts and one installation lock. Detailed credentials/errors stay out of URLs, logs and diagnostics.
- The current candidate retains a dedicated identity scoped to the application database, with installation/recovery DDL privileges. Separate temporary installation and narrower runtime identities remain a qualification gap: the updater currently rejects insufficient grants or a different view/trigger definer before mutation. Do not describe this candidate as supporting automatic recovery with every existing restricted runtime account. A portable split-credential contract and representative hosting-panel evidence remain required before that claim.
- Refuse overwriting private configuration, existing users or unknown database objects. Repeated requests must not create duplicate accounts or migrations. Reuse matching applied receipts; an interrupted `applying` migration stops for operator review because MySQL DDL is not one rollbackable transaction.
- Fresh installation only in version 1. Existing installations show their status and the current upgrade guidance. Automatic updates, schema rollback, hosting-account provisioning, email delivery, payments, tax activation and historical import execution are separate capabilities.
- Disable browser installation entirely in the hosted demo and after successful installation. CLI recovery remains an operator action; deleting one lock file must not allow takeover of an existing installation.

## Supported profile and acceptance

Start with the current package profile: PHP 8.2+ (8.3 recommended); BCMath, PDO/PDO MySQL, mbstring, JSON and sessions; MySQL 8.4/InnoDB with the required collation; HTTPS and the exact public document root. The [hosting/runtime record](strategy/HOSTING-PHP-COMPATIBILITY.md) records the compatible lock and tested environments. MariaDB and arbitrary shared-hosting configurations are not implicitly supported.

Verify fresh installation on Apache/PHP-FPM and Nginx/PHP-FPM hosting profiles where available, including a representative hosting-panel setup. Test missing extensions, wrong credentials, insufficient grants, wrong/nonempty database, unwritable configuration, interrupted migration, concurrent installation, CSRF, ownership failure, duplicate submission and access after completion. Confirm no private files can be downloaded. Compare CLI and browser-installed schemas/receipts and run existing scoped posting, report, permission and recovery checks against sample data.

Check the six-step experience on desktop, tablet and mobile. Have a person unfamiliar with the repository complete installation from the release ZIP and document where assistance was required. Technical test success does not establish easy installation on all hosts.

## Delivery sequence and changes

1. Close 0.6.1 workflow gaps while preparing browser setup and review evidence; read access and AR/AP are already in the published baseline.
2. Accept 0.7 browser setup against the complete ZIP with unfamiliar operators and explicit host requirements.
3. Accept 0.8 automatic backup/update/recovery; test failure/recovery boundaries and validate supported hosts. Distribution-channel publication follows stable in 1.1.
4. Under release authorization, publish an evaluation package with updated INSTALL/UPGRADE instructions, README, Wiki, website and installation evidence.
5. Continue regional tax/e-invoicing connectors (Pakistan FBR is one planned connector), inventory, shop POS and e-commerce/storefront; controlled API/MCP commands follow e-commerce.

Expected implementation areas: shared bootstrap/config handling, existing install/preflight/migrate/create-admin services, a guarded installer controller/view, configuration persistence and release docs. No new accounting schema or migration content is proposed for the wizard itself; installation executes the existing migration chain. An additional installation-state schema is not assumed.

References: [current package installation](../resources/release/INSTALL.md), [upgrade contract](../resources/release/UPGRADE.md), [architecture](ARCHITECTURE.md), [module roadmap](MODULE-ROADMAP.md), and [WordPress's installation flow](https://developer.wordpress.org/advanced-administration/before-install/howto-install/). The WordPress experience informs usability, not PHP Ledger's financial or permission rules. No Google Drive reference was required. This plan makes no production, DNS, schema or provider change.
