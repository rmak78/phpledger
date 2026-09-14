# Install PHP Ledger {{VERSION}}

Source revision: `{{SOURCE_COMMIT}}`. This guide installs the new preview into an **empty, dedicated database**. For any existing database, first read [UPGRADE.md](UPGRADE.md). Never run SQL dumps from the historical application against this database.

## 1. Prepare the host

Arrange the following with your hosting administrator:

| Requirement | Supported package profile |
|---|---|
| PHP | 8.5.x for both command-line and web requests. PHP 8.6 and other branches are outside this profile. |
| Extensions | BCMath, PDO, PDO MySQL, mbstring and working PHP sessions; standard JSON support must be available. |
| Database | MySQL 8.4, InnoDB and `utf8mb4_0900_ai_ci`. MariaDB is not validated for this package. |
| Web server | HTTPS with a valid certificate; document root and front-controller fallback configured as below. |
| Operator access | A terminal for preflight, migrations and initial-user creation. |
| Session storage | A private writable PHP session directory, usable by the web PHP process. Match CLI and web configuration when checking it. |

Use a dedicated database account, never MySQL root in application configuration. Installation requires permission to create/alter the package's tables, indexes and triggers and to write migration receipts. Have the database administrator provision these privileges, then restrict the normal runtime account after validating the required workflows. This preview does not prescribe an independently verified minimal grant set for every customer host.

Set `PL_ENV=production` in the server's PHP environment for web and CLI processes. Leave the hosted-demo mode disabled. The public demo's reset scheduler and credentials are not part of this installation. Do not copy a development environment into customer hosting.

## 2. Unpack and configure privately

Verify the downloaded archive against its published checksum, then unpack it in a new private application directory. Preserve this structure:

```text
phpledger-{{VERSION}}/
  vendor/
  resources/
  www/phpledger/
    includes/
    install/
    public/       <-- only web document root
    templates/
```

Production dependencies are supplied in `vendor/`; Composer is not required for this installation. Keep application code read-only to the web process. Only PHP's private session storage needs runtime write access in this preview; do not make the whole application writable.

Copy `www/phpledger/includes/config.local.example.php` to `www/phpledger/includes/config.local.php`, keeping it outside `public/`. Edit the copy with your database details:

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

Use a dedicated hostname with the application mounted at `/`. Set its document root to the absolute path ending in `www/phpledger/public`. The package root, `includes`, `install`, `templates`, `resources` and `vendor` must not be exposed by aliases or static-file rules.

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

## 4. Check and initialize

Run these commands from the unpacked package root, using the same PHP 8.5 configuration and private database settings as the web process:

```sh
php www/phpledger/install/preflight.php
php www/phpledger/install/migrate.php
php www/phpledger/install/preflight.php
```

Run each command only after the previous command succeeds. Preflight checks prerequisites and database/migration state; it does not create tables or accounts. An empty database should be ready for migrations. After migration, preflight should report the schema as current. Retain the command results privately, without credentials.

Keep **all five** migrations `001` through `005`. The runner tracks checksums and safely skips matching applied versions on a repeated run. If it reports an interrupted migration, missing version or checksum mismatch, stop and follow [UPGRADE.md](UPGRADE.md); never erase a receipt to force a retry.

## 5. Create the initial user

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

Open the HTTPS hostname and sign in. Confirm that refresh and navigation retain the session. Create a clearly isolated sample company, record a small synthetic receipt or expense, open its journal, and locate its effect in the reports. Verify a linked reversal in that sample. Check that private file paths cannot be downloaded and that HTTPS/session cookies are configured correctly.

`/health` checks database connectivity only; it does not prove that migrations, users or accounting workflows are ready. Run the journey above as well as the CLI checks. Delete no real records to perform acceptance checks.

For a real business, choose the appropriate start date, fiscal year and base currency deliberately. Existing-business onboarding remains blocked pending opening-balance and unpaid-document reconciliation; historical cutover/import tools are not included. Do not bypass that gate by misclassifying an existing business as new.

## Country suggestion and operation

The country hint may send the visitor's public IP address from the server to `https://api.country.is/` once per browser session. The request is bounded; manual selection remains available when it fails or outbound access is blocked. It does not establish tax rules or choose the business's accounting policy. If outbound requests are disallowed, enforce that through hosting egress controls; no API key is needed.

Forwarded IP headers are accepted only from explicitly configured trusted proxies (`PL_TRUSTED_PROXY_IPS`); have the host overwrite incoming forwarded headers before enabling that setting. Keep it unset for direct hosting. Follow [UPGRADE.md](UPGRADE.md) to establish and rehearse private backups before a pilot.
