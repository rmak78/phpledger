<?php declare(strict_types=1); ?>
<section class="flex flex-col gap-4 py-5" aria-labelledby="error-title">
    <div class="rounded-panel border border-border bg-surface p-4">
        <p class="eyebrow">Unable to complete this action</p>
        <?php pl_ui_page_header((string) $title, '', null, 'error-title'); ?>
        <div class="alert alert-danger" role="alert" tabindex="-1" data-form-error><p><?= pl_e((string) $message) ?></p></div>
        <p>Check that you are working in the intended business. If your access changed, ask its owner to review your permissions.</p>
        <div class="panel-actions" data-fold="primary action"><a class="btn btn-primary" href="<?= pl_e(pl_url('/companies')) ?>">Return to your businesses</a><a class="btn btn-secondary" href="<?= pl_e(pl_url('/help')) ?>">Getting-started help</a></div>
    </div>
</section>
