# Install PHP Ledger {{VERSION}}

> Minimum PHP 8.2; PHP 8.3 is recommended for deployment. Use a current security patch and run preflight with the same PHP version/extensions as web requests. The package includes compatible production dependencies; do not bypass Composer platform checks.

Source revision: `{{SOURCE_COMMIT}}`. This guide installs the new preview into an **empty, dedicated database**. For any existing database, first read [UPGRADE.md](UPGRADE.md). Never run SQL dumps from the historical application against this database.

## 1. Prepare the host

Arrange the following with your hosting administrator:

| Requirement | Supported package profile |
|---|---|
| PHP | 8.2 or newer for command-line and web requests; 8.3 recommended. The tested matrix is 8.2/8.3/8.4; other branches require validation. |
| Extensions | BCMath, PDO, PDO MySQL, mbstring, curl, OpenSSL, fileinfo and working PHP sessions; standard JSON support must be available. |
| Database | MySQL 8.4, InnoDB and `utf8mb4_0900_ai_ci`. MariaDB is not validated for this package. |
| Web server | HTTPS with a valid certificate; document root and front-controller fallback configured as below. |
| Operator access | Hosting-panel access for the guarded browser installer; terminal access remains available for CLI setup and expert recovery. |
| Session storage | A private writable PHP session directory, usable by the web PHP process. Match CLI and web configuration when checking it. |

Use a dedicated database account, never MySQL root in application configuration. Installation requires permission to create/alter the package's tables, indexes, triggers and views and to write migration receipts. Have the database administrator provision these privileges, then restrict the normal runtime account after validating the required workflows. This preview does not prescribe an independently verified minimal grant set for every customer host. The two effective-source views use SQL SECURITY DEFINER with the account that runs migration 016. Use a stable dedicated migration account, preserve its access to the underlying tables, and verify the normal runtime account can select the views. Do not delete the view-definer account after setup; review view and trigger definers when rehearsing a restore.

Set `PL_ENV=production` in the server's PHP environment for web and CLI processes. Leave the hosted-demo mode disabled. The public demo's reset scheduler and credentials are not part of this installation. Do not copy a development environment into customer hosting.

For the included read integrations, the browser wizard records the explicit public URL and provisions private OAuth keys. CLI operators follow [docs/INTEGRATIONS.md](docs/INTEGRATIONS.md) to set the exact HTTPS `PL_PUBLIC_URL` and initialize keys with `php tools/setup-oauth.php`. Both paths require preserving the keys in backups and configuring the documented proxy/discovery/origin headers. Connections remain unavailable until the URL is configured; OAuth also requires its private keys. The browser application continues to use its existing users and company permissions. Serve only the public directory; keys, configuration and CLI tools must remain private.

## 2. Unpack and configure privately

### Browser setup in the local installer candidate

The published 0.6.0-preview uses the CLI steps below. The next installer candidate adds `/install`; it is local implementation until a later release publication receipt records acceptance.

After unpacking the complete package and setting HTTPS/public-root routing, create a private writable `www/phpledger/storage/installation` directory and place a strong random setup key (at least 32 characters, no whitespace) in `setup.key`. Use a password manager or hosting-panel generator. Do not expose this directory through the web server. Hosts may instead configure `PL_INSTALL_DIRECTORY` and `PL_SETUP_KEY`. Visit `/install`, enter the key, and supply a dedicated empty MySQL 8.4 database/account created through the hosting panel. Never use an existing business database or MySQL root.

The wizard checks prerequisites, applies the unchanged migration chain, creates the initial account and continues to business onboarding. JavaScript is optional. Private configuration is written atomically or offered as a protected download for placement using the hosting panel. Preserve the separately generated private `operator.key` for future installation-wide update/recovery access. Setup locks after completion and cannot be reopened by deleting a marker from an installed database. Unfamiliar-user acceptance and general shared-host qualification remain pending.

Automatic updates require PHP ZIP, a pinned publisher public key, private backup space, supported schema privileges and a writable update layout; ordinary application runtime requirements alone do not establish automatic recovery support. The current updater uses the configured schema-owning identity and rejects a narrower runtime identity without recovery privileges; temporary separate update credentials are not implemented. Follow [UPGRADE.md](UPGRADE.md) before enabling that capability.

Verify the downloaded archive against its published checksum, then unpack it in a new private application directory. Preserve this structure:

```text
phpledger-{{VERSION}}/
  vendor/
  resources/
  tools/          <-- private operator tools; never web-accessible
  www/phpledger/
    includes/
    install/
    public/       <-- only web document root
    templates/
```

Production dependencies are supplied in `vendor/`; Composer is not required for this installation. The manual, read-only deployment profile keeps code read-only while providing private session and OAuth storage. Browser setup also requires private installation state and either a writable private configuration destination or hosting-panel placement of its protected configuration download. Automatic updating additionally requires write access to the managed release destinations. Grant only the access needed by the selected profile; never expose private storage through the public document root.

For the alternate CLI setup path, copy `www/phpledger/includes/config.local.example.php` to `www/phpledger/includes/config.local.php`, keeping it outside `public/`, then edit the copy with your database details. Browser wizard users let the wizard create this configuration instead:

```php
<?php
declare(strict_types=1);
return [
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'phpledger',
    'user' => 'phpledger',
    'password' => '', // Set your unique database password here.
];
```

A nonempty password is required. Restrict this file to the operator and PHP process; exclude it from public downloads and source control. The equivalent environment keys are `PL_DB_HOST`, `PL_DB_PORT`, `PL_DB_NAME`, `PL_DB_USER` and `PL_DB_PASSWORD`; values in `config.local.php` override those keys. The application does **not** read a `.env` file itself.

## 3. Configure HTTPS and routing

Use a dedicated hostname with the application mounted at `/`. Set its document root to the absolute path ending in `www/phpledger/public`. The package root, `includes`, `install`, `templates`, `resources`, `tools` and `vendor` must not be exposed by aliases or static-file rules.

For Apache 2.4, the following fragment belongs inside your hosting administrator's HTTPS virtual host. Replace the example absolute path with your unpacked directory; this fragment does not configure certificates or the PHP handler:

```apache
DocumentRoot /srv/phpledger-{{VERSION}}/www/phpledger/public
<Directory /srv/phpledger-{{VERSION}}/www/phpledger/public>
    Options -Indexes
    AllowOverride None
    Require all granted
    FallbackResource /index.php
</Directory>
```

The PHP handler must execute `index.php`. Redirect HTTP to HTTPS. Other servers need the equivalent existing-file handling and fallback to `index.php`; arbitrary subdirectory/reverse-proxy layouts require separate verification. Do not use `PL_ENV=local` to work around an HTTPS or cookie problem. Apache documents [FallbackResource](https://httpd.apache.org/docs/2.4/mod/mod_dir.html#fallbackresource) for this routing pattern.

## 4. Alternate CLI path: check and initialize

Browser wizard users complete these checks and migrations in `/install`; after successful setup, continue to section 6. The commands here and in section 5 are for operators choosing CLI setup.

Run these commands from the unpacked package root, using the same PHP 8.2+ configuration and private database settings as the web process:

```sh
php www/phpledger/install/preflight.php
php www/phpledger/install/migrate.php
php www/phpledger/install/preflight.php
```

Run each command only after the previous command succeeds. Preflight checks prerequisites and database/migration state; it does not create tables or accounts. An empty database should be ready for migrations. After migration, preflight should report the schema as current. Retain the command results privately, without credentials.

Keep **every supplied migration**, including earlier applied versions. The runner tracks checksums and safely skips matching applied versions on a repeated run. If it reports an interrupted migration, missing version or checksum mismatch, stop and follow [UPGRADE.md](UPGRADE.md); never erase a receipt to force a retry.

## 5. Alternate CLI path: create the initial user

Choose a unique password between **12 and 72 bytes**. The command accepts it through stdin, not a password argument. This Bash example prompts without echoing the password or putting it in shell history; turn off shell tracing before using it:

```bash
set +x
IFS= read -r -s -p 'Initial user password: ' PL_INITIAL_PASSWORD
printf '\n'
printf '%s\n' "$PL_INITIAL_PASSWORD" | php www/phpledger/install/create-admin.php --email='owner@example.com' --name='Owner' --password-stdin
unset PL_INITIAL_PASSWORD
```

Replace the example email and name. On another shell, use its secure input mechanism to supply stdin; do not put the password in command arguments. The command creates a sign-in account. That person becomes the owner of a company they create; this is not a separate global administrator role. Repeating it with the same email does not create a duplicate user. Keep terminal access restricted because it can create additional users.

## 6. Verify the first journey

Open the HTTPS hostname and sign in. Confirm that refresh and navigation retain the session. In a clearly isolated sample company, record a small synthetic receipt or expense and follow its journal into the reports. Open an account statement and check its opening, period and closing balances. Add an account, review a balanced general-journal draft, post it and verify a linked reversal with its own date and reason. Confirm that the chart and journals retain their history. Check that private file paths cannot be downloaded and that HTTPS/session cookies are configured correctly.

`/health` checks database connectivity only; it does not prove that migrations, users or accounting workflows are ready. Run the journey above after the browser wizard or CLI checks succeed. Delete no real records to perform acceptance checks.

For a real business, choose the appropriate start date, fiscal year and functional currency deliberately. Functional currency is immutable after creation; a future change requires a separately reviewed new-book migration, not a settings edit. Existing-business onboarding remains blocked until an authorised owner previews and confirms its opening trial balance and reconciled unpaid-document register at `/opening-balances`. Cutover is the close of the accounting start date; ordinary transactions start afterward. Use the exact CSV columns shown on screen, or enter account balances manually. Do not bypass that gate by misclassifying an existing business as new. Invoice collection/bill settlement and detailed historical journals remain outside this cutover workflow.

Use `/periods` to create nonoverlapping date ranges and close them with a recorded reason; only the owner can reopen. Use `/bank-reconciliation` for strict statement CSV preview/import, explicit journal-line matching and confirmed reconciliation. The first bank baseline requires all earlier entries cleared and a matching ledger opening. These local workflows do not connect to a bank or make payments.

The files in `resources/tax/` are disabled, unreviewed research candidates. They are not loaded into company settings or POS calculations. Optional `php tools/validate-tax-catalog.php --self-test` checks their structure without database access or activation; it does not verify tax law or approve a tax profile. See [RELEASE-NOTES.md](RELEASE-NOTES.md).

## Foundation operator tools

The rate CLI accepts a private JSON input file with `from_currency`, `to_currency`, `rate_date`, a decimal-string `rate`, `source`, `note` and `idempotency_key`. `rate_type` defaults to `spot`; `actual` is also supported. A correction supplies the latest row's integer `supersedes_id` and a new request key. Rates are appended, never overwritten. The operator must identify an active actor with write access to the selected company/book:

```sh
php tools/currency-rates.php ACTOR_ID COMPANY_ID BOOK_ID /private/rate-input.json
```

Keep the input outside the web root. This records a supplied rate without contacting a provider. Reusing a key with identical content returns its prior result; changed content is rejected.

Party/contact, invoice/bill, settlement, credit and correction browser workflows use the shared internal accounting services. No public financial write API is enabled. Existing aggregate opening debts and stock require the implemented reviewed opening conversion: explicitly map parties/products and reconcile to the original opening journal without posting it again. Outgoing settlement from a foreign-currency bank remains blocked until bank carrying-value realization is implemented. Accounting entries record payments; they do not send money.

The outbound dispatcher has no installed delivery adapter. Do not schedule `tools/dispatch-outbound-events.php` expecting email, webhooks or CRM synchronization. Configure and validate a separately authorized adapter before enabling external delivery; the package neither installs cron nor enables a connector.

## Country suggestion and operation

The country hint may send the visitor's public IP address from the server to `https://api.country.is/` once per browser session. The request is bounded; manual selection remains available when it fails or outbound access is blocked. It does not establish tax rules or choose the business's accounting policy. If outbound requests are disallowed, enforce that through hosting egress controls; no API key is needed.

Forwarded IP headers are accepted only from explicitly configured trusted proxies (`PL_TRUSTED_PROXY_IPS`); have the host overwrite incoming forwarded headers before enabling that setting. Keep it unset for direct hosting. Follow [UPGRADE.md](UPGRADE.md) to establish and rehearse private backups before a pilot.
