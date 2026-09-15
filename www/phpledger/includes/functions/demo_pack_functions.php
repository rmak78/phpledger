<?php
declare(strict_types=1);

/** Only these original bundled pack identities are selectable, never a request path. */
function pl_demo_pack_catalog(): array
{
    $catalog = json_decode((string) file_get_contents(PL_ROOT . '/resources/demo-packs/catalog.json'), true, 32, JSON_THROW_ON_ERROR);
    $result = [];
    foreach ($catalog as $pack) {
        if (!in_array($pack['id'], ['service-agency', 'retail-shop', 'seasonal-business', 'distributor'], true)
            || $pack['version'] !== '1.0.0' || $pack['file'] !== $pack['id'] . '-1.0.0.json'
            || !preg_match('/^[a-f0-9]{64}$/D', $pack['sha256'])) {
            throw new RuntimeException('The bundled sample catalog is invalid.');
        }
        $result[$pack['id']] = $pack;
    }
    if (count($result) !== 4) { throw new RuntimeException('The bundled sample catalog is incomplete.'); }
    return $result;
}

function pl_demo_pack(string $id): array
{
    $entry = pl_demo_pack_catalog()[$id] ?? null;
    if ($entry === null) { throw new DomainException('Choose one of the four bundled sample companies.'); }
    $raw = file_get_contents(PL_ROOT . '/resources/demo-packs/' . $entry['file']);
    if ($raw === false) { throw new RuntimeException('The selected sample is unavailable.'); }
    // Git may check out text with CRLF; the pinned fixture bytes use LF.
    $raw = str_replace("\r\n", "\n", $raw);
    if (!hash_equals($entry['sha256'], hash('sha256', $raw))) { throw new RuntimeException('The selected sample changed; restore its pinned fixture.'); }
    $pack = json_decode($raw, true, 64, JSON_THROW_ON_ERROR);
    if ($pack['id'] !== $id || $pack['version'] !== $entry['version'] || $pack['demo_only'] !== true
        || $pack['start_date'] !== '2024-01-01' || count($pack['events']) + count($pack['drafts']) !== 74
        || count($pack['checkpoints']) !== 36) { throw new RuntimeException('The selected sample has an invalid contract.'); }
    $pack['digest'] = $entry['sha256'];
    return $pack;
}

/** Reconcile authored Decimal checkpoints through the same reports used by the UI/API. */
function pl_demo_pack_reconcile(int $actorId, int $companyId, int $bookId, array $pack): void
{
    foreach ($pack['checkpoints'] as $checkpoint) {
        $trial = pl_trial_balance($actorId, $companyId, $bookId, $checkpoint['to']);
        $actual = array_column($trial['accounts'], 'balance', 'code');
        $expected = $checkpoint['balances'];
        ksort($actual); ksort($expected);
        $profit = pl_profit_loss($actorId, $companyId, $bookId, $checkpoint['from'], $checkpoint['to']);
        $balance = pl_balance_sheet($actorId, $companyId, $bookId, $checkpoint['to']);
        if (!$trial['balanced'] || $actual !== $expected || !$balance['balanced']
            || $profit['total_income'] !== $checkpoint['income'] || $profit['total_expenses'] !== $checkpoint['expenses']
            || $profit['net_profit'] !== $checkpoint['profit']) {
            throw new RuntimeException('The sample did not reconcile at ' . $checkpoint['to'] . '; setup was rolled back.');
        }
    }
}

/** New isolated company only; every source uses existing account/document/journal/period services. */
function pl_seed_demo_pack(int $actorId, int $companyId, int $bookId, string $id): void
{
    pl_demo_require_setup_action();
    $pack = pl_demo_pack($id);
    pl_ledger_transaction(function () use ($actorId, $companyId, $bookId, $pack): void {
        pl_require_company_access($actorId, $companyId, true);
        pl_ledger_book($companyId, $bookId, true);
        $company = pl_company_context($actorId, $companyId);
        if (!$company['is_sample'] || $company['setup_status'] !== 'ready' || $company['start_date'] !== $pack['start_date']
            || $company['fiscal_year_end'] !== '12-31'
            || DB::queryFirstField('SELECT id FROM pl_journals WHERE book_id = %i LIMIT 1', $bookId)
            || DB::queryFirstField('SELECT id FROM pl_documents WHERE book_id = %i LIMIT 1', $bookId)
            || DB::queryFirstField('SELECT id FROM pl_general_drafts WHERE book_id = %i LIMIT 1', $bookId)
            || DB::queryFirstField('SELECT user_id FROM pl_demo_visitors WHERE company_id = %i LIMIT 1', $companyId)) {
            throw new DomainException('A historical sample requires a new, empty, unassigned sample company starting in 2024. Existing books cannot be replaced.');
        }
        if (pl_demo_enabled() && min(500, max(10, (int) (getenv('PL_DEMO_MAX_DOCUMENTS') ?: 100))) < $pack['source_count'] + 20) {
            throw new PlDemoUnavailable('The sample needs room for its history and at least twenty practice records. Ask the demo operator to check its capacity setting.');
        }
        $prefix = 'sample:' . $pack['id'] . ':' . $pack['version'] . ':';
        $mapping = array_column($company['accounts'], 'id', 'code');
        foreach ($pack['accounts'] as $definition) {
            $account = pl_save_account($actorId, $companyId, $bookId, $definition + [
                'is_active' => true, 'reason' => 'Original synthetic sample chart and manual support schedules.',
                'creation_key' => $prefix . 'account:' . $definition['code'],
            ]);
            $mapping[$definition['code']] = $account['id'];
        }
        for ($month = 1; $month <= 12; $month++) {
            $start = sprintf('2025-%02d-01', $month);
            pl_create_period($actorId, $companyId, $bookId, ['start_date' => $start,
                'end_date' => (new DateTimeImmutable($start))->format('Y-m-t'),
                'reason' => 'Historical sample month, prepared before visitor assignment.', 'request_key' => $prefix . 'period:' . $month]);
        }
        pl_create_period($actorId, $companyId, $bookId, ['start_date' => '2026-01-01', 'end_date' => '2026-12-31',
            'reason' => 'Open practice year for synthetic visitor actions.', 'request_key' => $prefix . 'practice']);
        foreach ($pack['events'] as $event) {
            if ($event['kind'] === 'receipt') {
                $source = pl_save_document($actorId, $companyId, $bookId, [
                    'kind' => 'receipt', 'date' => $event['date'], 'amount' => $event['amount'],
                    'money_account_id' => $mapping[$event['money_code']], 'category_account_id' => $mapping[$event['category_code']],
                    'counterparty' => $event['counterparty'], 'reference' => $event['reference'], 'memo' => $event['description'],
                    'creation_key' => $prefix . $event['key'],
                ]);
                pl_post_document($actorId, $companyId, $bookId, $source['id'], $source['revision']);
            } else {
                $lines = array_map(static fn (array $row): array => ['account_id' => $mapping[$row['code']],
                    'debit' => $row['debit'], 'credit' => $row['credit'], 'description' => $row['description']], $event['lines']);
                $source = pl_save_general_draft($actorId, $companyId, $bookId, ['date' => $event['date'],
                    'reference' => $event['reference'], 'description' => $event['description'], 'lines' => $lines,
                    'creation_key' => $prefix . $event['key']]);
                pl_post_general_draft($actorId, $companyId, $bookId, $source['id'], $source['revision']);
                if ($event['reverse']) { pl_reverse_general_draft($actorId, $companyId, $bookId, $source['id'], $event['date'], 'Original sample correction: reverse wrong-cost; replacement is correct-cost on 2025-08-21.'); }
            }
        }
        foreach ($pack['drafts'] as $event) {
            pl_save_document($actorId, $companyId, $bookId, $event + ['money_account_id' => $mapping[$event['money_code'] ?? '1000'],
                'category_account_id' => $mapping[$event['kind'] === 'receipt' ? '4000' : '5000'], 'creation_key' => $prefix . $event['key']]);
        }
        pl_demo_pack_reconcile($actorId, $companyId, $bookId, $pack);
        foreach (pl_list_periods($actorId, $companyId, $bookId) as $period) {
            if ($period['end_date'] <= $pack['history_end']) {
                pl_change_period_status($actorId, $companyId, $bookId, (int) $period['id'], 'closed', (int) $period['revision'],
                    'Synthetic history reconciled to pinned monthly checkpoints. Closure prevents backdated posting; it does not approve statutory statements.', $prefix . 'close:' . $period['start_date']);
            }
        }
        $snapshot = json_decode((string) DB::queryFirstField('SELECT snapshot FROM pl_template_installations WHERE company_id = %i FOR UPDATE', $companyId), true, 512, JSON_THROW_ON_ERROR);
        $snapshot['sample_pack'] = ['id' => $pack['id'], 'version' => $pack['version'], 'digest' => $pack['digest'],
            'date' => $pack['start_date'], 'currency' => $company['currency'], 'checkpoints' => 36];
        DB::update('pl_template_installations', ['snapshot' => json_encode($snapshot, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)], 'company_id = %i', $companyId);
        $manifest = pl_module_registry()['pos-showcase'];
        pl_set_company_module($actorId, $companyId, 'pos-showcase', true, 0, $manifest['digest'], 'Explicit isolated sample includes the cash POS showcase; it does not deduct stock.', 'sample-pos');
    });
}

/** Resolve a guide only from this authorized company's pinned installation snapshot. */
function pl_company_demo_pack(int $actorId, int $companyId, int $bookId): ?array
{
    pl_require_company_access($actorId, $companyId);
    pl_ledger_book($companyId, $bookId);
    $raw = DB::queryFirstField('SELECT t.snapshot FROM pl_template_installations t JOIN pl_companies c ON c.id = t.company_id WHERE t.company_id = %i AND t.book_id = %i AND c.is_sample = 1', $companyId, $bookId);
    if (!$raw) { return null; }
    $snapshot = json_decode((string) $raw, true, 512, JSON_THROW_ON_ERROR)['sample_pack'] ?? null;
    if (!is_array($snapshot) || !isset(pl_demo_pack_catalog()[$snapshot['id'] ?? ''])) { return null; }
    $pack = pl_demo_pack($snapshot['id']);
    if ($snapshot['version'] !== $pack['version'] || !hash_equals($pack['digest'], $snapshot['digest'])) {
        throw new DomainException('This sample guide has changed since your company was created. Start a fresh sample to use the current guide.');
    }
    return $pack;
}
