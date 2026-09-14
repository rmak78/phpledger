<?php declare(strict_types=1); ?>
<section class="page-wrap" aria-labelledby="error-title">
    <div class="panel">
        <p class="eyebrow">Unable to complete this action</p>
        <h1 id="error-title"><?= pl_e((string) $title) ?></h1>
        <div class="alert" role="alert" tabindex="-1" data-form-error><p><?= pl_e((string) $message) ?></p></div>
        <p>Check that you are working in the intended business. If your access changed, ask its owner to review your permissions.</p>
        <div class="actions"><a class="button primary" href="<?= pl_e(pl_url('/companies')) ?>">Return to your businesses</a><a class="button secondary" href="<?= pl_e(pl_url('/help')) ?>">Getting-started help</a></div>
    </div>
</section>
