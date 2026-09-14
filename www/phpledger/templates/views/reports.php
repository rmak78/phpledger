<?php
declare(strict_types=1);
$hasOverview = isset($overview) && is_array($overview);
?>
<div class="page-wrap owner-reports">
    <div class="page-heading owner-heading">
        <div><p class="eyebrow">Your business at a glance</p><h1>A clearer picture.</h1><p class="muted"><?= pl_e($company['name']) ?> · <?= pl_e($company['book_name']) ?></p></div>
        <div class="owner-heading-actions">
            <?php if ($hasOverview): ?><span class="as-of-label">Through <?= pl_e(pl_date_label($overview['as_of'])) ?></span><?php endif; ?>
            <?php if (pl_can_write($company)): ?><a class="button primary" href="<?= pl_e(pl_url('/transactions/new')) ?>"><?= pl_icon('plus') ?> New transaction</a><?php endif; ?>
        </div>
    </div>
    <?php if ($hasOverview):
        $chartMax = max(abs((float) $overview['income']), abs((float) $overview['expenses']), 1);
        $incomeWidth = max(0, min(360, (int) round(abs((float) $overview['income']) / $chartMax * 360)));
        $expenseWidth = max(0, min(360, (int) round(abs((float) $overview['expenses']) / $chartMax * 360)));
        // Rounded widths are presentation only; displayed amounts retain fixed-precision formatting.
    ?>
    <section class="owner-overview" aria-label="Posted business balances">
        <div class="cash-highlight"><div class="cash-label"><?= pl_icon('building') ?><span>Cash &amp; bank</span><span class="actual-label">Posted balance</span></div><p class="hero-amount"><span><?= pl_e($company['currency']) ?></span><?= pl_e(pl_money($overview['cash'])) ?></p><p>Balance across your recorded cash and bank accounts.</p><a href="<?= pl_e(pl_url('/reports/balance-sheet')) ?>">See your financial position <?= pl_icon('arrow-right') ?></a></div>
        <div class="performance-highlight"><div class="performance-heading"><h2>Money earned. Money spent.</h2><span><?= pl_e(pl_date_label($overview['period_from'])) ?> – <?= pl_e(pl_date_label($overview['as_of'])) ?></span></div><div class="performance-numbers"><div><span class="metric-label"><i class="metric-dot income-dot" aria-hidden="true"></i>Income</span><strong><?= pl_e(pl_money($overview['income'])) ?></strong></div><div><span class="metric-label"><i class="metric-dot expense-dot" aria-hidden="true"></i>Expenses</span><strong><?= pl_e(pl_money($overview['expenses'])) ?></strong></div><div><span class="metric-label"><?= bccomp($overview['profit'], '0', 4) < 0 ? 'Net loss' : 'Net profit' ?></span><strong><?= pl_e(pl_money($overview['profit'])) ?></strong></div></div><div class="performance-chart"><svg viewBox="0 0 360 54" role="img" aria-label="Relative magnitude of posted income and expenses. Exact amounts appear above."><rect x="0" y="3" width="360" height="17" rx="6" class="chart-track"/><rect x="0" y="3" width="<?= $incomeWidth ?>" height="17" rx="6" class="chart-income"/><rect x="0" y="33" width="360" height="17" rx="6" class="chart-track"/><rect x="0" y="33" width="<?= $expenseWidth ?>" height="17" rx="6" class="chart-expense"/></svg><a href="<?= pl_e(pl_url('/reports/profit-loss')) ?>">Explore profit &amp; loss <?= pl_icon('arrow-right') ?></a></div><p class="small muted">Amounts in <?= pl_e($company['currency']) ?> · Posted entries only</p></div>
    </section>
    <?php endif; ?>
    <div class="report-section-title"><div><p class="eyebrow">Look a little closer</p><h2>The answers behind the numbers.</h2></div><p>Start with the question you want to answer.</p></div>
    <div class="report-directory">
        <a href="<?= pl_e(pl_url('/reports/balance-sheet')) ?>"><span class="report-symbol"><?= pl_icon('building') ?></span><span class="report-type">Financial position</span><h2>What do I own<br> and owe?</h2><p>Assets, liabilities and the equity behind your business.</p><span class="report-link">Balance sheet <?= pl_icon('arrow-right') ?></span></a>
        <a href="<?= pl_e(pl_url('/reports/profit-loss')) ?>"><span class="report-symbol"><?= pl_icon('file-text') ?></span><span class="report-type">Business performance</span><h2>What am I<br> earning?</h2><p>Income and expenses, with the profit left over for a period.</p><span class="report-link">Profit &amp; loss <?= pl_icon('arrow-right') ?></span></a>
        <a class="forecast-directory" href="<?= pl_e(pl_url('/reports/cash-forecast')) ?>"><span class="report-symbol"><?= pl_icon('arrow-right') ?></span><span class="report-type">Planning scenario</span><h2>What could<br> come next?</h2><p>Explore cash needs using your own expected money in and out.</p><span class="report-link">Cash forecast <?= pl_icon('arrow-right') ?></span></a>
    </div>
    <div class="accountant-report"><span class="accountant-icon"><?= pl_icon('book') ?></span><div><h3>Every number has a story.</h3><p>Review debit and credit balances, open account activity, and follow posted entries to their source.</p></div><a href="<?= pl_e(pl_url('/reports/trial-balance')) ?>" class="button secondary">Open trial balance <?= pl_icon('arrow-right') ?></a></div>
    <p class="report-scope-note"><?= pl_icon('info-circle') ?><span>Actual reports exclude drafts. Cash forecasts are scenarios based on your assumptions. Country-neutral statements still need your accountant's review for statutory reporting.</span></p>
</div>
