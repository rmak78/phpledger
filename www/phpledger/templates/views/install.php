<?php declare(strict_types=1);
$headings = ['locked' => 'Installation is locked', 'key' => 'Install PHP Ledger', 'database' => 'Connect your database',
    'review' => 'Review your installation', 'migrating' => 'Preparing your database', 'configuration' => 'Save private configuration', 'account' => 'Create your sign-in account'];
$field = static fn (string $name, string $default = ''): string => is_string($_POST[$name] ?? null) ? $_POST[$name] : $default;
$csrf = session_status() === PHP_SESSION_ACTIVE ? pl_csrf_token() : '';
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="color-scheme" content="light">
<title><?= pl_e($headings[$view]) ?> · PHP Ledger</title><link rel="stylesheet" href="<?= pl_e(pl_url('/assets/app.css')) ?>">
<?php if ($view === 'migrating' && $error === ''): ?><script src="<?= pl_e(pl_url('/assets/install.js')) ?>" defer></script><?php endif; ?>
</head><body><main class="auth-shell"><div class="auth-card">
<img class="auth-logo" src="<?= pl_e(pl_url('/assets/brand/phpledger-horizontal.png')) ?>" alt="PHP Ledger" width="2172" height="724">
<section class="auth-panel flex flex-col gap-4"><p class="eyebrow">Private installation · <?= pl_e(pl_app_version()) ?></p><h1 class="page-title"><?= pl_e($headings[$view]) ?></h1>
<?php if ($error !== ''): ?><div class="alert alert-danger" role="alert" tabindex="-1"><p><?= pl_e($error) ?></p></div><?php endif; ?>
<?php if ($view === 'locked'): ?>
<p>Browser setup is disabled until the hosting operator enables it with a private setup key. Completed installations remain locked.</p>
<p>For an existing installation, sign in normally or follow its upgrade instructions.</p><a class="btn btn-primary" href="<?= pl_e(pl_url('/login')) ?>">Go to sign in</a>
<?php elseif ($view === 'key'): ?>
<p>Enter the setup key from your private installation directory. This proves that you control this hosting installation. Never place the key in a public folder or share it in a URL.</p>
<form class="flex flex-col gap-3" method="post" action="<?= pl_e(pl_url('/install')) ?>"><input type="hidden" name="csrf_token" value="<?= pl_e($csrf) ?>"><input type="hidden" name="action" value="unlock">
<label class="field-label" for="setup-key">Private setup key</label><input class="input" id="setup-key" name="setup_key" type="password" required autocomplete="off" maxlength="256">
<div class="panel-actions"><button class="btn btn-primary" type="submit">Unlock setup</button></div></form>
<?php elseif ($view === 'database'): ?>
<p>Create an empty, dedicated MySQL 8.4 database in your hosting panel. The installation account must create tables, indexes, views and triggers. No terminal or Composer command is needed.</p>
<form class="flex flex-col gap-3" method="post" action="<?= pl_e(pl_url('/install')) ?>"><input type="hidden" name="csrf_token" value="<?= pl_e($csrf) ?>"><input type="hidden" name="action" value="database">
<label class="field-label" for="public-url">Public application URL</label><input class="input" id="public-url" name="public_url" type="url" value="<?= pl_e($field('public_url', (string) (getenv('PL_PUBLIC_URL') ?: ''))) ?>" placeholder="https://books.example.com" required maxlength="480" autocomplete="url"><p>Use the HTTPS address visitors will use for this installation. Connections and OAuth discovery use this exact address.</p>
<label class="field-label" for="db-host">Database host</label><input class="input" id="db-host" name="host" value="<?= pl_e($field('host', 'localhost')) ?>" required maxlength="253" autocomplete="off">
<label class="field-label" for="db-port">Port</label><input class="input" id="db-port" name="port" value="<?= pl_e($field('port', '3306')) ?>" required inputmode="numeric" pattern="[0-9]+">
<label class="field-label" for="db-name">Database name</label><input class="input" id="db-name" name="database" value="<?= pl_e($field('database')) ?>" required maxlength="64" autocomplete="off">
<label class="field-label" for="db-user">Installation database user</label><input class="input" id="db-user" name="user" value="<?= pl_e($field('user')) ?>" required maxlength="128" autocomplete="off">
<label class="field-label" for="db-password">Database password</label><input class="input" id="db-password" name="password" type="password" required autocomplete="new-password" maxlength="1024">
<details class="text-sm"><summary>Use a separate everyday database account</summary>
<p>Where the host supports separate privileges, enter a restricted runtime account. Keep the migration account available: the database views and triggers retain their definer.</p>
<label><input name="separate_runtime" type="checkbox" value="1"<?= $field('separate_runtime') === '1' ? ' checked' : '' ?>> Use the runtime account below</label>
<label class="field-label" for="runtime-user">Runtime database user</label><input class="input" id="runtime-user" name="runtime_user" value="<?= pl_e($field('runtime_user')) ?>" maxlength="128" autocomplete="off">
<label class="field-label" for="runtime-password">Runtime database password</label><input class="input" id="runtime-password" name="runtime_password" type="password" autocomplete="new-password" maxlength="1024"></details>
<div class="panel-actions"><button class="btn btn-primary" type="submit">Check database</button></div></form>
<?php elseif ($view === 'review' || $view === 'migrating'): ?>
<?php if ($view === 'review'): ?><p>Install <?= pl_e(pl_app_version()) ?> into <strong><?= pl_e((string) ($_SESSION['install_database']['database'] ?? '')) ?></strong> on <?= pl_e((string) ($_SESSION['install_database']['host'] ?? '')) ?>. This creates the application schema in the empty database. No company or financial transaction will be posted.</p>
<?php else: ?><p role="status"><?= (int) ($schema['applied'] ?? 0) ?> migrations complete; <?= (int) ($schema['pending'] ?? 0) ?> remain. Your browser continues one migration at a time. Keep this tab open.</p><p>If the connection stops, reopen setup with the same key and database credentials. A partially applied migration requires operator review; setup will not blindly replay it.</p><?php endif; ?>
<form class="flex flex-col gap-3" method="post" action="<?= pl_e(pl_url('/install')) ?>"<?= $view === 'migrating' && $error === '' ? ' data-install-continue' : '' ?>><input type="hidden" name="csrf_token" value="<?= pl_e($csrf) ?>"><input type="hidden" name="action" value="migrate">
<button class="btn btn-primary" type="submit"><?= $view === 'review' ? 'Install database' : 'Continue installation' ?></button></form>
<noscript><p>JavaScript is off. Select Continue installation until all migrations are complete.</p></noscript>
<?php elseif ($view === 'configuration'): ?>
<p>The schema is ready. Save the normal database configuration and public URL privately, outside the public directory. This step also creates private OAuth signing and encryption keys for Connections, preserving any matching existing keys. Temporary installation credentials will be removed from the setup session when installation finishes.</p>
<form class="flex flex-col gap-3" method="post" action="<?= pl_e(pl_url('/install')) ?>"><input type="hidden" name="csrf_token" value="<?= pl_e($csrf) ?>"><label class="field-label" for="confirm-public-url">Public application URL</label><input class="input" id="confirm-public-url" name="public_url" type="url" value="<?= pl_e($field('public_url', (string) ($_SESSION['install_runtime']['public_url'] ?? getenv('PL_PUBLIC_URL') ?: ''))) ?>" placeholder="https://books.example.com" required maxlength="480"><button class="btn btn-primary" name="action" value="save_config" type="submit">Save private configuration</button></form>
<details class="text-sm"><summary>My host does not allow PHP to write this file</summary><p>Download the private configuration and upload it with your hosting panel as <code>www/phpledger/includes/config.local.php</code>, outside <code>public/</code>. Restrict its permissions and remove the downloaded copy from shared devices. Then refresh this page. Existing files are never overwritten by setup.</p>
<form class="flex flex-col gap-3" method="post" action="<?= pl_e(pl_url('/install')) ?>"><input type="hidden" name="csrf_token" value="<?= pl_e($csrf) ?>"><button class="btn btn-secondary" name="action" value="download_config" type="submit">Download private configuration</button></form></details>
<?php elseif ($view === 'account'): ?>
<p>Create your initial sign-in account. Next you will set up your first business using the existing onboarding flow. Browser installation then closes permanently.</p>
<p>Your private installation directory also holds the separate <code>operator.key</code> for maintenance. Keep it with your private backups; it is not your sign-in password.</p>
<form class="flex flex-col gap-3" method="post" action="<?= pl_e(pl_url('/install')) ?>"><input type="hidden" name="csrf_token" value="<?= pl_e($csrf) ?>"><input type="hidden" name="action" value="finish">
<label class="field-label" for="owner-name">Your name</label><input class="input" id="owner-name" name="name" value="<?= pl_e($field('name')) ?>" required maxlength="120" autocomplete="name">
<label class="field-label" for="owner-email">Email address</label><input class="input" id="owner-email" name="email" type="email" value="<?= pl_e($field('email', (string) ($state['owner_email'] ?? ''))) ?>" required maxlength="254" autocomplete="username">
<label class="field-label" for="owner-password">Password</label><input class="input" id="owner-password" name="password" type="password" required minlength="12" maxlength="72" autocomplete="new-password" aria-describedby="password-hint"><p id="password-hint">Use 12–72 bytes. For a simple choice, use at least 12 ASCII characters.</p>
<div class="panel-actions"><button class="btn btn-primary" type="submit">Finish and create your business</button></div></form>
<?php endif; ?>
</section></div></main></body></html>
