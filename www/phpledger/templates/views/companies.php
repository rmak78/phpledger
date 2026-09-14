<?php declare(strict_types=1); ?>
<section class="page-wrap" aria-labelledby="companies-title">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Your workspace</p>
            <h1 id="companies-title">Your businesses</h1>
            <p class="muted">Choose the books you want to work with, or set up a separate business.</p>
        </div>
        <a class="button primary" href="<?= pl_e(pl_url('/onboarding')) ?>">Set up a business</a>
    </div>
    <?php if ($companies === []): ?>
        <div class="panel">
            <h2>Start with your business or try a sample</h2>
            <p>Create a new set of books, prepare an existing business for its opening review, or explore a separate sample company.</p>
            <p class="muted">The sample uses fictional transactions and will not be added to your real business.</p>
            <div class="actions">
                <a class="button primary" href="<?= pl_e(pl_url('/onboarding')) ?>">Choose how to start</a>
                <a class="button secondary" href="<?= pl_e(pl_url('/help')) ?>">Read the getting-started guide</a>
            </div>
        </div>
    <?php else: ?>
        <div class="form-grid">
            <?php foreach ($companies as $business): ?>
                <article class="panel" aria-labelledby="company-<?= pl_e((string) $business['id']) ?>">
                    <div class="actions">
                        <span class="badge"><?= $business['is_sample'] ? 'Sample company' : 'Business' ?></span>
                        <span class="badge"><?= pl_e(ucfirst((string) $business['role'])) ?></span>
                    </div>
                    <h2 id="company-<?= pl_e((string) $business['id']) ?>"><?= pl_e((string) $business['name']) ?></h2>
                    <p class="muted"><?= pl_e((string) $business['book_name']) ?> · <?= pl_e((string) $business['currency']) ?></p>
                    <?php if ($business['setup_status'] === 'opening_required'): ?>
                        <p class="alert">Opening balances need reconciliation. Posting is unavailable until that work is complete; historical imports are coming in a later milestone.</p>
                    <?php elseif ($business['setup_status'] === 'review_required'): ?>
                        <p class="alert">Review your existing accounts and opening balances before recording more transactions.</p>
                    <?php elseif ($business['is_sample']): ?>
                        <p class="muted">Fictional records for exploring the accounting journey.</p>
                    <?php else: ?>
                        <p class="muted">Ready for receipt and expense entry.</p>
                    <?php endif; ?>
                    <form action="<?= pl_e(pl_url('/company/select')) ?>" method="post">
                        <?= pl_csrf_field() ?>
                        <?= pl_scope_fields($business) ?>
                        <button class="button secondary" type="submit" aria-label="<?= pl_e('Open ' . $business['name']) ?>">Open books</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
        <p class="muted">Sample companies have separate books. Always check the business name before entering or posting a transaction.</p>
    <?php endif; ?>
</section>
