# Restricted public demo operations

## Website-only publication: 15 September 2026

The approved multi-page website was published at **08:08 UTC (13:08 PKT)** as `website-redesign-20260915-080700`. Its static root was switched independently of the demo. Existing **core-0.1.2-preview-da5ff133e645** demo containers, upstream proxy target, application files and database were preserved. No demo reset or migration was run.

The website SEO plan required `X-Robots-Tag: noindex, nofollow` on `/demo/`; this header was absent in the fresh preflight and was added to the existing proxy location. Existing inherited Alt-Svc/nosniff headers were repeated to preserve Nginx behavior, and upstream CSP/referrer headers remained unchanged. Fresh `/demo/` and `/demo/health` checks returned 200 with the new noindex header; the container identities matched before/after publication. Public sample creation/posting/reset journeys were not rerun by this static release.

The marketing site now uses directory routes, canonical flat-page redirects, a custom 404 and reviewed security/cache headers. Previous website/configuration are backed up under `/var/www/phpledger/data/backups/website-redesign-20260915-080700`. See [current website QA](../www/website/design-qa.md#live-publication-15-september-2026) and [publication receipt](design/website/qa/live-20260915-publication.json). The earlier operational receipts below retain their historical scope.

The user authorized and reconfirmed publication on **14 September 2026**. The [marketing website](https://phpledger.com/) and [restricted public demo](https://phpledger.com/demo/) are now live over HTTPS. The actual **18:00 UTC automatic reset** was observed: old sample data cleared, prior sessions expired, and a clean sample could be started. This remains an accounting/POS preview; the requested reporting/POS improvements and first installable package are separate work. See [validation](VALIDATION.md#hosted-website-and-restricted-demo-publication), [sprint scope](SPRINT-02.md), [architecture](ARCHITECTURE.md), and the [POS limits](POS.md).

## Release layout and exposure

The deployed application release is `/var/www/phpledger/data/releases/sprint-02-20260914-174642`. The website has an independent static release, **`website-20260914-180808`**, including the contact correction and supporting-company logos/heading; static updates do not replace the demo runtime. Store deployment secrets separately in `/var/www/phpledger/data/private/demo.env`, readable only by the deployment operator. Neither release root nor the private directory is a web document root. Fresh backups preserve the previous website, site-specific Nginx configuration and private environment without publishing their contents.

| Surface | Current deployment contract |
|---|---|
| Marketing website | Static document root `<release>/www/website/public`; never serve the repository or release root |
| Public demo | HTTPS `/demo` reverse-proxies to `127.0.0.1:18202`, retaining the `/demo` path prefix |
| Application container | Apache serves only `www/phpledger/public`, with the `/demo/` alias and fallback in `docker/demo-apache.conf` |
| Demo database | Dedicated Compose MySQL service and named volume; no host database port is published |
| Project name | `phpledger-demo` in `compose.demo.yaml`; keep this identity stable between releases so the intended volume/network are used |
| Private state | Deployment environment file, restricted credentials, and any operator backups stay outside public roots and Git |

`compose.demo.yaml` mounts the release's application and resources read-only. The image supplies the pinned Composer dependencies and PHP runtime. Keep a published release directory immutable: a rebuilt image alone does not replace these mounted source files. Never mount legacy root code or a customer's database into the demo.

## Environment and database identities

The following is an **illustrative private environment file**, with unusable placeholder secrets. Provision three independent random secrets privately. The database initialization script requires the web/reset passwords to be lowercase hexadecimal strings of at least 48 characters; use equally strong independent material for the root password. Do not copy development/test fixture passwords.

```dotenv
PL_DEMO_RELEASE=sprint-02-20260914-174642
PL_DEMO_PORT=18202
PL_DEMO_ROOT_PASSWORD=<independent-random-root-secret>
PL_DEMO_WEB_PASSWORD=<at-least-48-random-lowercase-hex-characters>
PL_DEMO_RESET_PASSWORD=<different-at-least-48-random-lowercase-hex-characters>
PL_DEMO_LOCAL_HTTP=0
PL_DEMO_DOCKER_SUBNET=10.204.82.0/24
PL_DEMO_TRUSTED_PROXY_IPS=10.204.82.1
```

Verify that the subnet does not overlap an existing host network and that the trusted address is the actual nearest controlled proxy hop. The subnet/gateway above match the current Compose defaults; they are not universal hosting values. An environment file is not a secret manager: restrict its permissions, keep it out of backups shared publicly, and never paste resolved `docker compose config`, environment output, or credential-bearing diagnostics into a public receipt.

The Compose file sets `PL_ENV=demo`, `PL_BASE_PATH=/demo`, and the literal database `phpledger_demo` for the web service. Demo mode rejects a different database name, and normal application mode rejects this demo database. `PL_DEMO_LOCAL_HTTP=0` is required for the public deployment; the guarded loopback-only override exists solely for local HTTP testing. Public session cookies require HTTPS.

`docker/demo-db-init.sh` creates these identities on a fresh MySQL volume:

- `ledger_demo_web`: only `SELECT`, `INSERT`, and `UPDATE` on `phpledger_demo.*`. It has no schema-changing, delete, trigger-management, administrative, or grant-option privileges. The application checks current grants and refuses a web connection with forbidden privileges. Existing accounting/source/POS triggers also reject posted changes; service permissions restrict allowed operations further.
- `ledger_demo_reset`: `ALL PRIVILEGES` on **only** `phpledger_demo.*`, so its CLI maintenance process can drop/recreate that synthetic database and migrations. This is not a global administrator or a grant to other databases. Only `demo-reset` and `demo-scheduler` receive this credential and `PL_DEMO_RESET_MODE=1`.
- MySQL root: bootstrap/administrative identity confined to the database container's setup and health checks; never the application web account.

The init script runs only for a fresh MySQL volume. Changing a password in the environment file does not rotate an already-created database user's password. Coordinate any rotation of the database identity and consuming services explicitly; do not erase the volume to work around a credential mismatch.

## Visitor isolation, expiry, and capacity

`POST /demo/start` requires CSRF and creates a synthetic user and a private sample company for that visitor. Repeated entry with the same valid session reuses its sample. Company/book permissions and the current generation are checked by server services; another visitor's IDs cannot grant access. Visitors can explore reports, edit sample drafts, post/reverse entries, and use the bounded cash POS. Setup, company switching, user administration, fiscal-period changes, and destructive user operations are blocked.

The entry selector includes the original USD/EUR/GBP/PKR/INR choices plus **Malaysia — MYR, Bangladesh — BDT, Sri Lanka — LKR, Nepal — NPR and Singapore — SGD**. Country hints and manual choices use one shared server-validated currency list. A new visitor's selected base currency carries through sample transactions, POS receipts/journals and reports. The sample's numerical amounts remain illustrative, without exchange conversion or local price/tax/reporting rules. Starting again in the same valid session preserves the existing company's currency even if another supported currency is submitted. No migration or reset is needed to add these choices.

Current defaults allow **100 visitors per generation** and **100 source documents per company**, including its starter sample documents. The helper clamps optional settings to 1–1,000 visitors and 10–500 documents, but the current Compose file does not forward `PL_DEMO_MAX_VISITORS` or `PL_DEMO_MAX_DOCUMENTS`; adding those names to the private environment file alone will not change runtime limits. Any future tuning must explicitly wire the settings, preserve guard tests, and follow load evidence. Capacity is a ceiling, not a claim of supported concurrent throughput.

A visitor's session stores the current generation. The next reset invalidates all previous visitors, even if numeric IDs are reused later. An expired generation rejects new work until the reset completes; it does not keep accepting writes into expired data. The UI must show that records are synthetic, temporary, and refreshed hourly. Never enter real financial/customer information or treat a printed sample receipt as a production transaction.

Every demo request and reset uses the same database advisory maintenance lock. Requests fail with a recoverable busy/refresh response when it is held, while reset waits up to 30 seconds. This deliberately serializes the bounded showcase; it is not evidence of a production concurrency design or a multi-node demo deployment.

## Hourly UTC reset

`tools/demo-scheduler.sh` runs the guarded reset on startup, then sleeps until the next UTC hour. `tools/demo-reset.php` skips a valid generation whose reset is not yet due. A successful reset records a new random generation and sets the next boundary to the following UTC hour. On failure, the scheduler logs a generic error and retries after 30 seconds. Container restart policy is `unless-stopped`.

The reset entry point requires CLI, `PL_ENV=demo`, `PL_DB_NAME=phpledger_demo`, and scheduler-only reset mode. Before replacing an existing nonempty database, it requires the demo marker, a valid generation, exclusively sample companies, and exclusively visitor users. It uses the **literal** database name in destructive SQL. It never substitutes an arbitrary environment-derived database name. Missing markers or unexpected records stop the reset for inspection.

`--now` forces an otherwise valid isolated demo generation to refresh before its due time, invalidating existing sessions. It does not bypass configuration, marker, scope, or maintenance-lock checks. Only the operator runs it through the maintenance service. Do not expose reset as a browser route, give the web service reset credentials, or run this tool against development/test/customer databases.

## Reverse proxy and country hint

The public HTTPS proxy must retain `/demo` when forwarding to the loopback listener; redirect `/demo` to `/demo/` consistently if the host needs it. Forward the verified host and scheme, and overwrite client forwarding headers rather than appending untrusted browser values. A single trusted host proxy can set `X-Forwarded-For` from its own observed client address. Configure `PL_DEMO_TRUSTED_PROXY_IPS` to the exact controlled hop seen by the container. If there are multiple proxy layers, validate their trust chain explicitly before enabling forwarded country hints.

The application accepts forwarded addresses only when `REMOTE_ADDR` exactly matches a configured trusted proxy, then chooses the nearest untrusted hop in a valid bounded chain. Private/reserved/local addresses skip the external lookup. A public address may be sent once per browser session to country.is for a country hint; success, failure, and local-network skips are cached. The response only suggests a supported base currency and never overrides confirmed business settings. See [regional provenance and behavior](../resources/locale/README.md). This is not currency conversion or tax/language coverage.

Application country-hint code does not retain the raw address/provider response in its session cache. Proxy/Apache access logs are a separate operational surface and may still record request addresses; configure access, retention, and public notice deliberately. The published Nginx location overwrites forwarding headers and the trusted Docker gateway was verified as `10.204.82.1`. A Pakistan suggestion was observed during live entry; provider accuracy across countries is not established by that observation.

## Deployment and rollback pattern

The lead owns the authorized release. These steps describe the current Compose contract; run only against the prepared isolated deployment after checking the concrete paths and private environment file.

1. Preserve the current website/proxy configuration and its recoverable release pointer. Keep the previous release and any needed isolated-demo recovery evidence outside the public root. Confirm the new files, license-status wording, image provenance, checks, and private-file exclusions before upload.
2. Put the verified release in its versioned directory and the secrets in the separate restricted environment file. Inspect the unresolved Compose source and variable names without printing resolved secrets. Verify the loopback port and network range are available.
3. Build and start the database, wait for its healthy state, then initialize the guarded demo generation through the maintenance service. Start the web and hourly scheduler only with the intended release tag and private environment file. Example commands from the release directory:

   ```sh
   docker compose --env-file /var/www/phpledger/data/private/demo.env -f compose.demo.yaml build demo-web demo-reset
   docker compose --env-file /var/www/phpledger/data/private/demo.env -f compose.demo.yaml up -d demo-db
   docker compose --env-file /var/www/phpledger/data/private/demo.env -f compose.demo.yaml --profile maintenance run --rm demo-reset --now
   docker compose --env-file /var/www/phpledger/data/private/demo.env -f compose.demo.yaml up -d demo-web demo-scheduler
   ```

4. Check the loopback `/demo/health`, sample entry, restrictive grants, sample generation, and scheduler state. Switch the static website document root and HTTPS `/demo` proxy only after the local deployment checks succeed. Do not expose the raw loopback port publicly or proxy the release root.
5. Verify public HTTPS, secure cookies, `/demo` links/forms/assets, two independent visitor sessions, POS posting/receipt/report links, rejected destructive operations, and recoverable busy/expired states. Verify an actual scheduled UTC-hour generation replacement and that prior sessions expire. Record the release identifier, checked URLs, commands/results, remaining gates, and rollback location without credentials.

For rollback, restore the previous website/proxy configuration first if the new public surface fails. Stop the new demo web/scheduler if isolation or reset safety is uncertain; leave the database volume intact for inspection. Restart a known compatible prior release using its matching source mounts/image/environment. Check schema compatibility before pairing old code with the new database; when incompatible, keep the demo unavailable until its matching isolated state is restored or safely rebuilt through the guarded reset. Never use `down -v`, a blanket database drop, or altered migration receipts as a shortcut. A website-only rollback can leave the demo temporarily unavailable rather than expose an unverified combination.

## Evidence and remaining release gates

Local evidence includes the expanded 56-test backend suite, original-foundation-to-005 upgrade proof, 15-table/9-trigger restoration check, restricted-user demo/reset smoke, and the latest 21-check POS HTTP journey with explicit-confirmation rejection. The loopback-only `python tests/demo-http-smoke.py --currencies` check passed 68 checks: the existing 18 demo checks plus ten per added currency covering selection, sample startup, POS/receipt, repeat checkout, reports, unchanged currency on re-entry and logout. It creates six independent synthetic visitors, makes no database reset, and leaves them until the ordinary hourly refresh. Omit `--currencies` for the original 18-check journey. Exact snapshot counts and follow-up fixes are in [Validation](VALIDATION.md).

The hosted counterpart passed **68 HTTP checks**, including secure scoped cookies and the five new currency journeys. Another **71 routing/asset checks** verified the initial published bytes, canonical HTTPS, the retained ACME exception and private-path rejection. All **39 deployed application PHP files** passed lint, platform requirements passed, and the locked production dependency audit reported no advisories. Composer metadata remains valid with warnings for the unset project license and the intentionally exact dependency pin; strict metadata validation therefore does not pass yet.

At the actual 18:00 UTC boundary the scheduler rebuilt only `phpledger_demo`. Empty synthetic tables and a temporary maintenance response were observed during rebuilding; by 18:00:17 the prior browser session had returned to entry and started a clean GBP sample. The generation changed, the next reset became 19:00 UTC, the scheduler did not restart, and the existing host MySQL process and demo database container were unchanged. The three session/reset assertions passed. The temporary maintenance interval and a separate busy response during concurrent QA are expected behavior of the current serialized demo, not throughput validation.

Public browser evidence is recorded in the [independent launch check](design/website/qa/live-20260914/README.md). These checks do not establish production throughput, monitored disaster recovery, regulatory/accounting approval, receipt-printer compatibility, accessibility conformance or observed usability targets. The project license/provenance decision still gates the first downloadable package. See the [hosted publication receipt](VALIDATION.md#hosted-website-and-restricted-demo-publication) for exact deployment scope and follow-up static changes.
