<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title><?= pl_e($title) ?> · PHP Ledger</title>
    <link rel="preload" href="<?= pl_e(pl_url('/assets/fonts/InterVariable.woff2')) ?>" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?= pl_e(pl_url('/assets/app.css', ['v' => '0.1.2'])) ?>">
    <link rel="stylesheet" href="<?= pl_e(pl_url('/assets/core.css', ['v' => '0.1.2'])) ?>">
    <?php if ($view === 'general-editor'): ?><script src="<?= pl_e(pl_url('/assets/core-journal.js', ['v' => '0.1.2'])) ?>" defer></script><?php endif; ?>
    <?php if ($view === 'pos'): ?><link rel="stylesheet" href="<?= pl_e(pl_url('/assets/pos.css')) ?>"><script src="<?= pl_e(pl_url('/assets/pos.js')) ?>" defer></script><?php endif; ?>
    <script src="<?= pl_e(pl_url('/assets/app.js')) ?>" defer></script>
</head>
<body class="view-<?= pl_e($view) ?>">
<a class="skip-link" href="#main">Skip to content</a>
<header class="app-header">
    <a class="brand" href="<?= pl_e(pl_url($user ? '/companies' : '/login')) ?>" aria-label="PHP Ledger home"><span class="brand-frame"><img src="<?= pl_e(pl_url('/assets/brand/phpledger-horizontal.png')) ?>" alt="PHP Ledger" width="2172" height="724"></span></a>
    <?php if ($user): ?>
    <nav class="primary-nav" aria-label="Main navigation">
        <?php if ($company): ?>
        <a href="<?= pl_e(pl_url('/transactions')) ?>" <?= in_array($view, ['transactions', 'editor'], true) ? 'aria-current="page"' : '' ?>><?= pl_icon('list') ?><span>Transactions</span></a>
        <a href="<?= pl_e(pl_url('/general-journals')) ?>" <?= in_array($view, ['general-journals', 'general-editor', 'general-detail'], true) ? 'aria-current="page"' : '' ?>><?= pl_icon('book') ?><span>Journals</span></a>
        <a href="<?= pl_e(pl_url('/accounts')) ?>" <?= $view === 'accounts' ? 'aria-current="page"' : '' ?>><?= pl_icon('list') ?><span>Accounts</span></a>
        <a href="<?= pl_e(pl_url('/pos')) ?>" <?= $view === 'pos' ? 'aria-current="page"' : '' ?>><?= pl_icon('receipt') ?><span>Point of sale</span></a>
        <a href="<?= pl_e(pl_url('/reports')) ?>" <?= in_array($view, ['reports', 'balance-sheet', 'profit-loss', 'cash-forecast', 'trial-balance', 'account', 'journal'], true) ? 'aria-current="page"' : '' ?>><?= pl_icon('book') ?><span>Reports</span></a>
        <?php else: ?><a href="<?= pl_e(pl_url('/companies')) ?>" <?= $view === 'companies' ? 'aria-current="page"' : '' ?>>Your businesses</a><?php endif; ?>
        <a href="<?= pl_e(pl_url('/help')) ?>" <?= $view === 'help' ? 'aria-current="page"' : '' ?>>Help</a>
    </nav>
    <details class="user-menu"><summary><span class="avatar"><?= pl_e(mb_strtoupper(mb_substr($user['display_name'], 0, 1))) ?></span><span class="user-name"><?= pl_e($user['display_name']) ?></span><?= pl_icon('chevron-down') ?></summary>
        <div class="user-menu-panel"><?php if (!pl_demo_enabled()): ?><p class="muted"><?= pl_e($user['email']) ?></p><a href="<?= pl_e(pl_url('/companies')) ?>">Switch business</a><?php else: ?><p class="muted">Your own temporary sample books</p><?php endif; ?><form action="<?= pl_e(pl_url('/logout')) ?>" method="post"><?= pl_csrf_field() ?><button type="submit" class="text-button"><?= pl_icon('logout') ?> <?= pl_demo_enabled() ? 'Leave demo' : 'Sign out' ?></button></form></div>
    </details>
    <?php else: ?><span class="header-note">Your business. Clearly accounted for.</span><?php endif; ?>
</header>
<?php if (pl_demo_enabled() && $view !== 'error'): $demoState = DB::queryFirstRow('SELECT next_reset_at FROM pl_demo_state WHERE id = 1'); ?>
<div class="demo-banner"><span><strong>Public demo</strong> · Separate synthetic data for each visitor. Destructive actions are disabled.</span><span>Resets <time data-local-time datetime="<?= pl_e(str_replace(' ', 'T', $demoState['next_reset_at']) . 'Z') ?>"><?= pl_e($demoState['next_reset_at']) ?> UTC</time></span></div>
<?php endif; ?>
<?php if ($company): ?>
<div class="company-bar"><a href="<?= pl_e(pl_url('/companies')) ?>" class="company-switch"><?= pl_icon('building') ?><strong><?= pl_e($company['name']) ?></strong><?= pl_icon('chevron-down') ?></a>
    <?php if ($company['is_sample']): ?><span class="badge sample">Sample company</span><?php endif; ?>
    <span class="context-item"><?= pl_e($company['book_name']) ?></span><span class="context-item"><?= pl_e($company['currency']) ?></span>
    <span class="company-role"><?= pl_e(ucfirst($company['role'])) ?></span>
</div>
<?php if ($company['setup_status'] !== 'ready'): ?>
<div class="readiness-banner"><?= pl_icon('info-circle') ?><div><strong><?= $company['setup_status'] === 'opening_required' ? 'Opening balances required.' : 'Review your existing setup.' ?></strong> <?= $company['setup_status'] === 'opening_required' ? 'Opening balances and unpaid documents must be reconciled before recording or posting transactions. Historical cutover follows in a later release.' : 'Confirm account mappings and opening balances before posting new documents.' ?> <?php if ($company['setup_status'] === 'review_required' && pl_can_write($company)): ?><a href="<?= pl_e(pl_url('/setup/review')) ?>">Review setup</a><?php endif; ?></div></div>
<?php endif; ?>
<?php endif; ?>
<?php if ($notice): ?><div class="notice" role="status"><?= pl_icon('check') ?><span><?= pl_e($notice) ?></span><button type="button" class="icon-button" data-dismiss aria-label="Dismiss notification"><?= pl_icon('x') ?></button></div><?php endif; ?>
<main id="main" tabindex="-1">
<?php if ($view === 'login'): ?>
<div class="signin-layout"><aside class="signin-story"><p class="eyebrow">Your business, clearly accounted for</p><h2>A day's work.<br>A clearer picture.</h2><p>Keep the everyday details connected to the bigger picture.</p><div class="signin-journey"><div><?= pl_icon('receipt') ?><span><strong>Capture the details</strong>Receipts and expenses, in one place.</span></div><div><?= pl_icon('book') ?><span><strong>Follow every entry</strong>From source document to balanced books.</span></div><div><?= pl_icon('file-text') ?><span><strong>Understand your business</strong>Readable reports with a path to the numbers.</span></div></div><p class="signin-footnote">PHP Ledger · Built for owners and bookkeepers</p></aside><div class="signin-form"><?php require __DIR__ . '/views/login.php'; ?></div></div>
<?php else: require __DIR__ . '/views/' . $view . '.php'; endif; ?>
</main>
<footer class="app-footer"><span>PHP Ledger · Development preview</span><span>English · <span data-timezone>UTC</span> <span class="muted">event times</span></span></footer>
</body>
</html>
