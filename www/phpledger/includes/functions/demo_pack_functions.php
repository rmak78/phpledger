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

/** A separate zero-balance choice; the four pinned historical packs remain unchanged. */
function pl_demo_starter_playground(?string $startDate = null): array
{
    $startDate ??= gmdate('Y') . '-01-01';
    if (!preg_match('/^[0-9]{4}-01-01$/D', $startDate)) { throw new DomainException('The starter playground needs a January practice-year start.'); }
    $pack = [
        'id' => 'accounting-starter', 'version' => '1.0.0', 'kind' => 'starter_playground',
        'name' => 'Accounting starter playground', 'business' => 'Invoices, bills, purchasing and stock',
        'start_date' => $startDate, 'demo_only' => true, 'source_count' => 0,
        'notice' => 'Start with zero balances and no stock. The example tax is a manually configured synthetic 5 percent rate, not a country tax rule.',
        'accounts' => [
            ['code' => '1300', 'name' => 'Stock on hand', 'type' => 'asset'],
            ['code' => '1350', 'name' => 'Synthetic input tax', 'type' => 'asset'],
            ['code' => '2100', 'name' => 'Goods received awaiting bills', 'type' => 'liability'],
            ['code' => '2150', 'name' => 'Synthetic output tax', 'type' => 'liability'],
            ['code' => '5100', 'name' => 'Cost of goods sold', 'type' => 'expense'],
            ['code' => '5200', 'name' => 'Purchase variance and rounding', 'type' => 'expense'],
        ],
        'party_name' => 'Sample customer and supplier',
        'products' => [
            ['sku' => 'SAMPLE-GOODS', 'name' => 'Sample goods', 'kind' => 'stock', 'base_unit' => 'each', 'selling_price' => '25.0000'],
            ['sku' => 'SAMPLE-SERVICE', 'name' => 'Sample service', 'kind' => 'nonstock', 'base_unit' => 'service', 'selling_price' => '50.0000'],
        ],
        'tax' => ['code' => 'DEMO5', 'name' => 'Synthetic example 5 percent', 'percentage' => '5'],
    ];
    $pack['digest'] = hash('sha256', json_encode($pack, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    return $pack;
}

function pl_demo_sample_choices(): array
{
    return ['accounting-starter' => pl_demo_starter_playground()] + pl_demo_pack_catalog();
}

function pl_demo_sample(string $id): array
{
    return $id === 'accounting-starter' ? pl_demo_starter_playground() : pl_demo_pack($id);
}

/** Provision only a new empty sample through normal scoped setup and module services. */
function pl_seed_demo_starter_playground(int $actorId, int $companyId, int $bookId): void
{
    pl_demo_require_setup_action();
    pl_ledger_transaction(function () use ($actorId, $companyId, $bookId): void {
        pl_require_company_access($actorId, $companyId, true);
        pl_ledger_book($companyId, $bookId, true);
        $company = pl_company_context($actorId, $companyId);
        $pack = pl_demo_starter_playground();
        if (!$company['is_sample'] || $company['setup_status'] !== 'ready' || $company['start_date'] !== $pack['start_date']
            || $company['fiscal_year_end'] !== '12-31'
            || DB::queryFirstField('SELECT id FROM pl_journals WHERE company_id=%i AND book_id=%i LIMIT 1', $companyId, $bookId)
            || DB::queryFirstField('SELECT id FROM pl_documents WHERE company_id=%i AND book_id=%i LIMIT 1', $companyId, $bookId)
            || DB::queryFirstField('SELECT id FROM pl_general_drafts WHERE company_id=%i AND book_id=%i LIMIT 1', $companyId, $bookId)
            || DB::queryFirstField('SELECT id FROM pl_ar_documents WHERE company_id=%i AND book_id=%i LIMIT 1', $companyId, $bookId)
            || DB::queryFirstField('SELECT id FROM pl_products WHERE company_id=%i AND book_id=%i LIMIT 1', $companyId, $bookId)
            || DB::queryFirstField('SELECT user_id FROM pl_demo_visitors WHERE company_id=%i LIMIT 1', $companyId)) {
            throw new DomainException('The starter playground requires a new empty unassigned sample. Existing books cannot be replaced.');
        }
        $mapping = array_column($company['accounts'], 'id', 'code');
        $reason = 'Synthetic zero-balance starter playground, prepared before visitor assignment.';
        foreach ($pack['accounts'] as $definition) {
            $account = pl_save_account($actorId, $companyId, $bookId, $definition + [
                'is_active' => true, 'reason' => $reason, 'creation_key' => 'starter-account:' . $definition['code'],
            ]);
            $mapping[$definition['code']] = $account['id'];
        }
        foreach (['inventory', 'purchasing'] as $module) {
            $manifest = pl_module_registry()[$module];
            pl_set_company_module($actorId, $companyId, $module, true, 0, $manifest['digest'], $reason, 'starter-module:' . $module);
        }
        foreach (['1100', '2000'] as $code) {
            pl_activate_open_item_account($actorId, $companyId, $bookId, $mapping[$code], $reason);
        }
        $party = pl_save_party($actorId, $companyId, $bookId, [
            'legal_name' => $pack['party_name'], 'entity_type' => 'private_company', 'country_code' => 'ZZ',
            'is_customer' => true, 'is_vendor' => true, 'currency' => $company['currency'],
            'ar_account_id' => $mapping['1100'], 'ap_account_id' => $mapping['2000'],
            'notes' => 'Entirely fictional practice party. Country ZZ denotes this synthetic example.',
            'reason' => $reason, 'request_key' => 'starter-party',
        ]);
        $products = [];
        foreach ($pack['products'] as $definition) {
            $product = pl_save_inventory_product($actorId, $companyId, $bookId, $definition + [
                'is_active' => true, 'inventory_account_id' => $definition['kind'] === 'stock' ? $mapping['1300'] : null,
                'cogs_account_id' => $definition['kind'] === 'stock' ? $mapping['5100'] : null,
                'sales_account_id' => $mapping['4000'], 'purchase_account_id' => $mapping['5000'],
                'reason' => $reason, 'idempotency_key' => 'starter-product:' . $definition['sku'],
            ]);
            $products[$definition['kind']] = $product['id'];
        }
        $tax = pl_create_tax_code($actorId, $companyId, $bookId, [
            'code' => $pack['tax']['code'], 'name' => $pack['tax']['name'], 'treatment' => 'standard',
            'sales_account_id' => $mapping['2150'], 'purchase_account_id' => $mapping['1350'],
            'reason' => $reason, 'idempotency_key' => 'starter-tax-code',
        ]);
        pl_enter_tax_rate($actorId, $companyId, $bookId, [
            'tax_code_id' => $tax['id'], 'effective_from' => $pack['start_date'], 'percentage' => $pack['tax']['percentage'],
            'reason' => $reason, 'idempotency_key' => 'starter-tax-rate',
        ]);
        $snapshot = json_decode((string) DB::queryFirstField('SELECT snapshot FROM pl_template_installations WHERE company_id=%i AND book_id=%i FOR UPDATE', $companyId, $bookId), true, 512, JSON_THROW_ON_ERROR);
        $snapshot['sample_pack'] = ['id' => $pack['id'], 'version' => $pack['version'], 'digest' => $pack['digest'],
            'date' => $pack['start_date'], 'currency' => $company['currency'], 'party_id' => $party['id'],
            'product_ids' => $products, 'tax_code_id' => $tax['id']];
        DB::update('pl_template_installations', ['snapshot' => json_encode($snapshot, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)], 'company_id=%i AND book_id=%i', $companyId, $bookId);
        if ((int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE company_id=%i AND book_id=%i', $companyId, $bookId) !== 0
            || pl_trial_balance($actorId, $companyId, $bookId)['total_debit'] !== '0.0000') {
            throw new RuntimeException('The starter playground must begin without posted or opening balances.');
        }
    });
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
    if (!is_array($snapshot)) { return null; }
    if (($snapshot['id'] ?? '') === 'accounting-starter') {
        $pack = pl_demo_starter_playground($snapshot['date']);
    } else {
        if (!isset(pl_demo_pack_catalog()[$snapshot['id'] ?? ''])) { return null; }
        $pack = pl_demo_pack($snapshot['id']);
    }
    if ($snapshot['version'] !== $pack['version'] || !hash_equals($pack['digest'], $snapshot['digest'])) {
        throw new DomainException('This sample guide has changed since your company was created. Start a fresh sample to use the current guide.');
    }
    return $pack;
}
