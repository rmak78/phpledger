<?php declare(strict_types=1); ?>
<div class="regional-hint">
    <div class="section-heading"><h3>Your regional starting point</h3><span class="badge">Suggested</span></div>
    <dl><div><dt>Country</dt><dd><?= pl_e($regional['country_name'] ?? 'Choose your own settings') ?></dd></div><div><dt>Regional locale</dt><dd><?= pl_e($regional['locale'] ?? 'Browser default') ?></dd></div><div><dt>Local currency</dt><dd><?= pl_e($regional['country_currency'] ?? (implode(', ', $regional['country_currencies'] ?? []) ?: 'Not detected')) ?></dd></div><div><dt>Your timezone</dt><dd data-timezone>UTC</dd></div></dl>
    <p class="small muted"><?php if (($regional['status'] ?? '') === 'detected'): ?>Country is an approximate IP-based suggestion. It can be wrong when using a VPN.<?php else: ?>Country detection is unavailable for this connection. Continue with your preferred currency below.<?php endif; ?> The interface is English. You confirm the currency before creating these books.</p>
    <p class="small muted">One country lookup per session uses <a href="https://country.is/" target="_blank" rel="noopener noreferrer">Country</a>; its result is kept in your session. Your device supplies its timezone.</p>
</div>
