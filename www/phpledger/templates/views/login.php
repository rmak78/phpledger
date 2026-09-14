<?php declare(strict_types=1); ?>
<section class="standalone" aria-labelledby="login-title">
    <p class="eyebrow">PHP Ledger</p>
    <h1 id="login-title">Welcome back</h1>
    <p class="muted">Sign in to work with your business books.</p>
    <?php if ($form['message'] !== ''): ?>
        <div class="alert" role="alert" tabindex="-1" id="login-error" data-form-error>
            <h2>We could not sign you in</h2>
            <p><?= pl_e((string) $form['message']) ?></p>
        </div>
    <?php endif; ?>
    <form action="<?= pl_e(pl_url('/login')) ?>" method="post" class="form-grid">
        <?= pl_csrf_field() ?>
        <div class="field full-width">
            <label for="login-email">Email address</label>
            <input id="login-email" name="email" type="email" autocomplete="username" required maxlength="254" value="<?= pl_e(pl_web_text($form['input'], 'email')) ?>">
        </div>
        <div class="field full-width">
            <label for="login-password">Password</label>
            <input id="login-password" name="password" type="password" autocomplete="current-password" required>
        </div>
        <button class="button primary full-width" type="submit">Sign in</button>
    </form>
    <p class="muted">Your administrator provides your account. If you need access or a password reset, contact the person who manages this installation.</p>
</section>
