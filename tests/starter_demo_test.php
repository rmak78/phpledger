<?php
declare(strict_types=1);

function starter_demo_fixture(): array
{
    $actor = pl_create_user('starter-demo-' . bin2hex(random_bytes(8)) . '@example.invalid', 'Synthetic starter visitor', bin2hex(random_bytes(24)));
    $input = ['name' => 'Synthetic zero-balance playground', 'currency' => 'USD', 'start_date' => gmdate('Y') . '-01-01',
        'fiscal_year_end' => '12-31', 'start_mode' => 'sample', 'sample_pack' => 'accounting-starter',
        'template_digest' => pl_starter_template()['digest']];
    $key = 'starter-demo:' . bin2hex(random_bytes(8));
    return ['actor' => $actor, 'input' => $input, 'key' => $key, 'company' => pl_setup_company($actor, $input, $key)];
}

test('starter playground is a separate explicit choice and retains the eleven pinned histories', function (): void {
    assert_same(['service-agency', 'retail-shop', 'seasonal-business', 'distributor', 'trader', 'restaurant', 'membership-club', 'pharmacy', 'jewelry-studio', 'light-manufacturing', 'service-workshop'], array_keys(pl_demo_pack_catalog()));
    assert_same(12, count(pl_demo_sample_choices()));
    foreach (pl_demo_pack_catalog() as $id => $entry) {
        $pack = pl_demo_pack($id);
        assert_same($entry['sha256'], $pack['digest']);
        assert_true(is_array($pack['source_material']) && is_string($pack['source_material']['runtime_note']));
        assert_true(is_array($pack['scenario']) && $pack['scenario']['id'] !== '');
        if ($entry['status'] === 'preview_only') {
            $research = $pack['source_material']['research_evidence'];
            assert_true($research !== null, $id . ' has no operational research evidence');
            assert_true(in_array($pack['source_material']['research_status'], ['available', 'generated_candidate'], true), $id . ' has an invalid research status');
            assert_true(count($research['operational_event_contract']) > 0, $id . ' has no operational event contract');
        }
    }
    $starter = pl_demo_sample('accounting-starter');
    assert_same('starter_playground', $starter['kind']);
    assert_same(gmdate('Y') . '-01-01', $starter['start_date']);
    assert_same(0, $starter['source_count']);
    assert_throws(fn () => pl_demo_sample('../accounting-starter'), DomainException::class);
});

test('starter playground provisions zero balances current open year and scoped usable module mappings once', function (): void {
    $f = starter_demo_fixture(); $actor = $f['actor']; $company = $f['company']; $id = $company['id']; $book = $company['book_id'];
    assert_same($id, pl_setup_company($actor, $f['input'], $f['key'])['id']);
    assert_true($company['is_sample']);
    assert_same('ready', $company['setup_status']);
    assert_same('accounting-starter', pl_company_demo_pack($actor, $id, $book)['id']);
    assert_same('0.0000', pl_trial_balance($actor, $id, $book)['total_debit']);
    foreach (['pl_journals', 'pl_documents', 'pl_general_drafts', 'pl_ar_documents', 'pl_open_items', 'pl_inventory_movements', 'pl_purchase_orders'] as $table) {
        assert_same(0, (int) DB::queryFirstField('SELECT COUNT(*) FROM %b WHERE company_id=%i AND book_id=%i', $table, $id, $book));
    }
    $periods = pl_list_periods($actor, $id, $book);
    assert_same(1, count($periods)); assert_same('open', $periods[0]['status']);
    assert_same(gmdate('Y') . '-01-01', $periods[0]['start_date']); assert_same(gmdate('Y') . '-12-31', $periods[0]['end_date']);
    foreach (['ar', 'ap', 'inventory', 'purchasing'] as $module) { assert_true(pl_module_available($actor, $id, $book, $module)); }
    assert_true(!pl_module_state($id, 'pos-showcase')['enabled']);
    assert_same(2, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_open_item_accounts WHERE company_id=%i AND book_id=%i', $id, $book));
    $products = pl_list_inventory_products($actor, $id, $book);
    assert_same(2, count($products));
    $stock = array_values(array_filter($products, static fn(array $p): bool => $p['kind'] === 'stock'))[0];
    assert_same('0.0000', pl_inventory_balance($actor, $id, $book, $stock['id'])['quantity']);
    assert_same('0.0000', pl_inventory_valuation($actor, $id, $book)['total_value_base']);
    $party = DB::queryFirstRow('SELECT * FROM pl_parties WHERE company_id=%i', $id);
    assert_same(1, (int) $party['is_customer']); assert_same(1, (int) $party['is_vendor']);
    assert_same(1, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_tax_codes WHERE company_id=%i AND book_id=%i AND code=%s', $id, $book, 'DEMO5'));
    assert_throws(fn () => pl_seed_demo_starter_playground($actor, $id, $book), DomainException::class, 'empty');
});

test('starter playground rejects foreign guides ordinary books and invalid practice dates without replacing records', function (): void {
    $starter = starter_demo_fixture(); $other = ledger_fixture();
    assert_throws(fn () => pl_company_demo_pack($other['actor_id'], $starter['company']['id'], $starter['company']['book_id']), DomainException::class);
    assert_throws(fn () => pl_seed_demo_starter_playground($other['actor_id'], $other['company_id'], $other['book_id']), DomainException::class, 'empty');
    $before = (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_companies');
    assert_throws(fn () => pl_setup_company($starter['actor'], array_replace($starter['input'], ['start_date' => gmdate('Y') . '-02-01']), 'starter-invalid-date'), DomainException::class, 'current January');
    assert_same($before, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_companies'));
});
