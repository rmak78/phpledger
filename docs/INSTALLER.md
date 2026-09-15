# Browser installer plan

Status: planned, not implemented. Owner requested a WordPress-style installer on 15 September 2026. The evening strategy decision supersedes its former placement: design may proceed alongside read access, while packaging distribution and the updater form the named adoption milestone immediately after AP. Coordinate the browser wizard there. The installer-creates-a-customer-website idea is parked. Existing CLI installation remains available.

## Intended experience

An operator downloads the complete release ZIP, unpacks it and points the HTTPS hostname at `www/phpledger/public`. On a validated host, the normal fresh-install journey should then work in the browser without Composer or terminal commands. The wizard cannot install PHP/extensions, create DNS/certificates or grant database privileges; it explains missing prerequisites with hosting-panel guidance.

| Step | User experience and result |
|---|---|
| 1. Welcome and host checks | Explain the supported package profile, confirm explicit installation ownership/enablement, and check PHP/extensions, HTTPS, private sessions, routing, configuration access and database requirements. Show actionable failures. |
| 2. Database connection | Enter a dedicated empty database's host, port, name and credentials. Test connectivity, MySQL version/collation and required privileges. Reject an existing/unrecognized database. Explain how to create the database/user in a hosting panel. |
| 3. Install | Show the target and package version for review, then run the existing versioned migrations through a guarded installer service. Display progress and safe error messages. Preserve complete filenames, checksums, locks and migration receipts. |
| 4. Owner account | Create the initial sign-in account through the existing auth service. Use its current password rules; this is not a new global administrator role. |
| 5. First business | Continue into existing onboarding: new business, reviewed existing-business opening/cutover, or a separate synthetic sample. Reuse currency, date, account-template and membership rules. Optional modules retain their existing defaults. |
| 6. Finish | Verify schema/current identity, write the private installation completion state, disable installation access and open the application. Give backup and next-step guidance. Do not post a transaction into a real company as a test. |

## Architecture and boundaries

- Extend the shared bootstrap/configuration lifecycle to show a safe unconfigured state before attempting a normal database connection. Reuse the existing MeekroDB configuration and services; do not introduce another framework, router, auth stack or database abstraction.
- Refactor the existing CLI-only migration implementation into a shared internal service with separately guarded CLI and one-time installer entry points. Do not simply remove its CLI guard or expose migration scripts directly under the document root.
- Keep configuration in the current `includes/config.local.php` location outside `public/`, or retain operator-provided environment settings. Write configuration atomically with restrictive permissions when explicitly writable; otherwise provide a protected one-time download/copy fallback for the operator. Never make the entire application writable.
- Require explicit one-time installer enablement and proof of control through the hosting environment, such as a temporary private setup key. A publicly reachable empty installation must not let an arbitrary visitor claim the first account. Use CSRF, secure sessions, no-store responses, bounded attempts and one installation lock. Detailed credentials/errors stay out of URLs, logs and diagnostics.
- Establish and test the fresh-install schema privileges and the narrower normal runtime privileges. Support a temporary schema identity and a dedicated runtime identity using the same MeekroDB layer; retain only runtime credentials after installation. Some shared hosts require the owner to provision these in the control panel. The exact portable grant profile needs validation before claiming support.
- Refuse overwriting private configuration, existing users or unknown database objects. Repeated requests must not create duplicate accounts or migrations. Reuse matching applied receipts; an interrupted `applying` migration stops for operator review because MySQL DDL is not one rollbackable transaction.
- Fresh installation only in version 1. Existing installations show their status and the current upgrade guidance. Automatic updates, schema rollback, hosting-account provisioning, email delivery, payments, tax activation and historical import execution are separate capabilities.
- Disable browser installation entirely in the hosted demo and after successful installation. CLI recovery remains an operator action; deleting one lock file must not allow takeover of an existing installation.

## Supported profile and acceptance

Start with the current package profile: PHP 8.2+ (8.3 recommended); BCMath, PDO/PDO MySQL, mbstring, JSON and sessions; MySQL 8.4/InnoDB with the required collation; HTTPS and the exact public document root. The [hosting/runtime record](strategy/HOSTING-PHP-COMPATIBILITY.md) records the compatible lock and tested environments. MariaDB and arbitrary shared-hosting configurations are not implicitly supported.

Verify fresh installation on Apache/PHP-FPM and Nginx/PHP-FPM hosting profiles where available, including a representative hosting-panel setup. Test missing extensions, wrong credentials, insufficient grants, wrong/nonempty database, unwritable configuration, interrupted migration, concurrent installation, CSRF, ownership failure, duplicate submission and access after completion. Confirm no private files can be downloaded. Compare CLI and browser-installed schemas/receipts and run existing scoped posting, report, permission and recovery checks against synthetic data.

Check the six-step experience on desktop, tablet and mobile. Have a person unfamiliar with the repository complete installation from the release ZIP and document where assistance was required. Technical test success does not establish easy installation on all hosts.

## Delivery sequence and changes

1. Record the host/grant/ownership contract and screen flow while API/MCP reads are in progress.
2. Complete read access, AR and AP in that order.
3. Deliver the distribution/updater adoption milestone, including the shared installer service and browser journey; test failure/recovery boundaries and validate supported hosts.
4. Under release authorization, publish an evaluation package with updated INSTALL/UPGRADE instructions, README, Wiki, website and installation evidence.
5. Continue regional tax/e-invoicing connectors (Pakistan FBR is one planned connector), inventory, shop POS and e-commerce/storefront; controlled API/MCP commands follow e-commerce.

Expected implementation areas: shared bootstrap/config handling, existing install/preflight/migrate/create-admin services, a guarded installer controller/view, configuration persistence and release docs. No new accounting schema or migration content is proposed for the wizard itself; installation executes the existing migration chain. An additional installation-state schema is not assumed.

References: [current package installation](../resources/release/INSTALL.md), [upgrade contract](../resources/release/UPGRADE.md), [architecture](ARCHITECTURE.md), [module roadmap](MODULE-ROADMAP.md), and [WordPress's installation flow](https://developer.wordpress.org/advanced-administration/before-install/howto-install/). The WordPress experience informs usability, not PHP Ledger's financial or permission rules. No Google Drive reference was required. This plan makes no production, DNS, schema or provider change.
