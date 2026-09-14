<?php
declare(strict_types=1);

function pos_input(): array
{
    return ['checkout_key' => bin2hex(random_bytes(24)), 'catalog_digest' => pl_pos_catalog()['digest'],
        'date' => '2026-09-14', 'items' => [['sku' => 'NOTE-A5', 'quantity' => '2'], ['sku' => 'PEN-BLUE', 'quantity' => '3']],
        'cash_received' => '20.00'];
}

test('all added base currencies survive sample setup POS posting and reconciled owner reports', function (): void {
    $f = ledger_fixture();
    foreach (['MYR', 'BDT', 'LKR', 'NPR', 'SGD'] as $currency) {
        $setup = setup_input('sample');
        $setup['currency'] = $currency;
        $key = bin2hex(random_bytes(16));
        $company = pl_setup_company($f['actor_id'], $setup, $key);
        assert_same($currency, $company['currency']);
        $snapshot = json_decode(DB::queryFirstField('SELECT snapshot FROM pl_template_installations WHERE company_id = %i', $company['id']), true, 512, JSON_THROW_ON_ERROR);
        assert_same($currency, $snapshot['sample_pack']['currency']);
        $sale = pl_checkout_pos($f['actor_id'], $company['id'], $company['book_id'], pos_input());
        assert_same($currency, $sale['currency']);
        assert_same($currency, $sale['document']['journal_preview']['currency']);
        assert_same($currency, $sale['document']['journal']['currency']);
        assert_same('12.7500', $sale['total']);
        assert_same('7.2500', $sale['change_due']);
        $profit = pl_profit_loss($f['actor_id'], $company['id'], $company['book_id'], '2026-09-14', '2026-09-14');
        $balance = pl_balance_sheet($f['actor_id'], $company['id'], $company['book_id'], '2026-09-14');
        $trial = pl_trial_balance($f['actor_id'], $company['id'], $company['book_id'], '2026-09-14');
        foreach ([$profit, $balance] as $report) { assert_same($currency, $report['currency']); }
        assert_same('1012.7500', $trial['total_debit']);
        assert_same('887.7500', $profit['net_profit']);
        assert_same('887.7500', $balance['total_assets']);
        assert_same('887.7500', pl_cash_balance($f['actor_id'], $company['id'], $company['book_id'], '2026-09-14'));
        assert_true($balance['balanced'] && $trial['balanced']);
        $changed = $setup; $changed['currency'] = 'USD';
        assert_throws(fn () => pl_setup_company($f['actor_id'], $changed, $key), DomainException::class, 'different business');
        assert_same($currency, pl_company_context($f['actor_id'], $company['id'])['currency']);
    }
    $unsupported = setup_input('sample'); $unsupported['currency'] = 'CAD';
    assert_throws(fn () => pl_setup_company($f['actor_id'], $unsupported, bin2hex(random_bytes(16))), DomainException::class, 'supported base currencies');
});

test('POS uses exact server prices and atomically links cash receipt journal and immutable lines', function (): void {
    $f = ledger_fixture();
    $input = pos_input();
    $sale = pl_checkout_pos($f['actor_id'], $f['company_id'], $f['book_id'], $input);
    assert_same('12.7500', $sale['total']);
    assert_same('20.0000', $sale['cash_received']);
    assert_same('7.2500', $sale['change_due']);
    assert_same('4.5000', $sale['items'][0]['unit_price']);
    assert_same('9.0000', $sale['items'][0]['line_total']);
    assert_same('posted', $sale['document']['status']);
    assert_same('receipt', $sale['document']['kind']);
    assert_same('document:' . $sale['document_id'], $sale['document']['journal']['source_reference']);
    assert_same('12.7500', pl_trial_balance($f['actor_id'], $f['company_id'], $f['book_id'])['total_debit']);
    assert_same('12.7500', pl_account_activity($f['actor_id'], $f['company_id'], $f['book_id'], $f['accounts']['1000'])['balance']);
    assert_same('-12.7500', pl_account_activity($f['actor_id'], $f['company_id'], $f['book_id'], $f['accounts']['4000'])['balance']);
    assert_throws(fn () => DB::update('pl_pos_sales', ['total' => '1.0000'], 'document_id = %i', $sale['document_id']), MeekroDBException::class, 'Posted POS snapshots are immutable');
    assert_throws(fn () => DB::delete('pl_pos_sales', 'document_id = %i', $sale['document_id']), MeekroDBException::class, 'Posted POS snapshots cannot be deleted');
    assert_same($sale['items'], pl_get_pos_receipt($f['actor_id'], $f['company_id'], $f['book_id'], $sale['document_id'])['items']);
});

test('POS canonical retries return one receipt and changed checkout content conflicts', function (): void {
    $f = ledger_fixture(); $input = pos_input();
    $sale = pl_checkout_pos($f['actor_id'], $f['company_id'], $f['book_id'], $input);
    $retry = $input; $retry['items'] = array_reverse($retry['items']); $retry['cash_received'] = '20.0000';
    assert_same($sale['document_id'], pl_checkout_pos($f['actor_id'], $f['company_id'], $f['book_id'], $retry)['document_id']);
    $retry['items'][0]['quantity'] = '4';
    assert_throws(fn () => pl_checkout_pos($f['actor_id'], $f['company_id'], $f['book_id'], $retry), DomainException::class, 'different sale');
    assert_same(1, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_pos_sales WHERE book_id = %i', $f['book_id']));
    assert_same(1, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id = %i', $f['book_id']));
});

test('POS rejects forged prices quantities stale catalog and insufficient cash without writes', function (): void {
    $f = ledger_fixture(); $input = pos_input();
    $badInputs = [];
    $bad = $input; $bad['total'] = '0.01'; $badInputs[] = $bad;
    $bad = $input; $bad['items'][0]['unit_price'] = '0.01'; $badInputs[] = $bad;
    foreach (['-1', '1.5', '100', '01'] as $quantity) { $bad = $input; $bad['items'][0]['quantity'] = $quantity; $badInputs[] = $bad; }
    $bad = $input; $bad['items'] = []; $badInputs[] = $bad;
    $bad = $input; $bad['items'] = [['sku' => 'NOTE-A5', 'quantity' => '99'], ['sku' => 'PEN-BLUE', 'quantity' => '99'], ['sku' => 'TEA-80', 'quantity' => '99']]; $badInputs[] = $bad;
    $bad = $input; $bad['items'][1]['sku'] = 'NOTE-A5'; $badInputs[] = $bad;
    $bad = $input; $bad['items'][0]['sku'] = 'UNKNOWN'; $badInputs[] = $bad;
    $bad = $input; $bad['cash_received'] = '12.7499'; $badInputs[] = $bad;
    $bad = $input; $bad['catalog_digest'] = str_repeat('0', 64); $badInputs[] = $bad;
    foreach ($badInputs as $bad) { assert_throws(fn () => pl_checkout_pos($f['actor_id'], $f['company_id'], $f['book_id'], $bad), DomainException::class); }
    assert_same(0, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_documents WHERE book_id = %i', $f['book_id']));
    assert_same(0, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id = %i', $f['book_id']));
});

test('POS enforces reader writer company book and opening-readiness boundaries', function (): void {
    $f = ledger_fixture(); $other = ledger_fixture(); $input = pos_input();
    $sale = pl_checkout_pos($f['actor_id'], $f['company_id'], $f['book_id'], $input);
    assert_throws(fn () => pl_get_pos_receipt($other['actor_id'], $f['company_id'], $f['book_id'], $sale['document_id']), DomainException::class);
    assert_throws(fn () => pl_checkout_pos($f['actor_id'], $f['company_id'], $other['book_id'], pos_input()), DomainException::class);
    DB::insert('pl_company_members', ['company_id' => $f['company_id'], 'user_id' => $other['actor_id'], 'role' => 'viewer']);
    assert_same($sale['document_id'], pl_get_pos_receipt($other['actor_id'], $f['company_id'], $f['book_id'], $sale['document_id'])['document_id']);
    assert_throws(fn () => pl_checkout_pos($other['actor_id'], $f['company_id'], $f['book_id'], $input), DomainException::class);
    DB::update('pl_companies', ['setup_status' => 'opening_required'], 'id = %i', $other['company_id']);
    assert_throws(fn () => pl_checkout_pos($other['actor_id'], $other['company_id'], $other['book_id'], pos_input()), DomainException::class, 'Opening balances');
});

test('POS closed-period failure rolls back its newly created source', function (): void {
    $f = ledger_fixture();
    DB::update('pl_periods', ['status' => 'closed'], 'id = %i', $f['period_id']);
    assert_throws(fn () => pl_checkout_pos($f['actor_id'], $f['company_id'], $f['book_id'], pos_input()), DomainException::class, 'open accounting period');
    assert_same(0, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_documents WHERE book_id = %i', $f['book_id']));
    assert_same(0, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_pos_sales WHERE book_id = %i', $f['book_id']));
});

test('POS snapshot failure rolls back receipt journal and lines together', function (): void {
    $f = ledger_fixture();
    DB::query("CREATE TRIGGER pl_test_pos_failure BEFORE INSERT ON pl_pos_sales FOR EACH ROW BEGIN IF NEW.book_id = " . $f['book_id'] . " THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Synthetic POS snapshot failure'; END IF; END");
    try {
        assert_throws(fn () => pl_checkout_pos($f['actor_id'], $f['company_id'], $f['book_id'], pos_input()), MeekroDBException::class, 'Synthetic POS snapshot failure');
        foreach (['pl_documents', 'pl_journals', 'pl_journal_lines', 'pl_pos_sales'] as $table) {
            assert_same(0, (int) DB::queryFirstField('SELECT COUNT(*) FROM %b WHERE book_id = %i', $table, $f['book_id']));
        }
    } finally { DB::query('DROP TRIGGER pl_test_pos_failure'); }
});

test('POS concurrent duplicate checkouts create one receipt and journal', function (): void {
    $f = ledger_fixture();
    $job = ['mode' => 'pos_checkout', 'fixture' => $f, 'pos_input' => pos_input()];
    $results = ledger_race([$job, $job]);
    assert_same($results[0]['id'], $results[1]['id']);
    assert_same(1, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_pos_sales WHERE book_id = %i', $f['book_id']));
    assert_same(1, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_documents WHERE book_id = %i', $f['book_id']));
    assert_same(1, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id = %i', $f['book_id']));
});

test('POS linked reversal preserves price snapshots while reversing the report effect', function (): void {
    $f = ledger_fixture(); $sale = pl_checkout_pos($f['actor_id'], $f['company_id'], $f['book_id'], pos_input());
    pl_reverse_document($f['actor_id'], $f['company_id'], $f['book_id'], $sale['document_id'], '2026-09-14', 'Synthetic shop correction');
    $preserved = pl_get_pos_receipt($f['actor_id'], $f['company_id'], $f['book_id'], $sale['document_id']);
    assert_same('reversed', $preserved['document']['status']);
    assert_same($sale['items'], $preserved['items']);
    assert_same('0.0000', pl_trial_balance($f['actor_id'], $f['company_id'], $f['book_id'])['total_debit']);
});
