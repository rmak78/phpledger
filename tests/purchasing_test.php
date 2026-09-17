<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/www/phpledger/includes/functions/web_functions.php';

test('purchase order list pages exact totals and derives receipt progress without changing order state',function(): void {
    $f=purchasing_fixture(); $args=[$f['actor_id'],$f['company_id'],$f['book_id']]; $first=null;
    for ($i=0;$i<26;$i++) {
        $row=pl_save_purchase_order(...array_merge($args,[['party_id'=>$f['party_id'],'date'=>'2026-01-05','currency'=>'USD','reference'=>'Paged order '.$i,'creation_key'=>bin2hex(random_bytes(16)),
            'lines'=>[['product_id'=>$f['product_id'],'description'=>'Synthetic goods','quantity'=>'3','unit_price'=>(string)($i+1)]]]]));
        $first??=$row;
    }
    $run=fn(array $q):array=>pl_list_query(...array_merge($args,['purchasing',$q]));
    $firstPage=$run(['sort'=>'amount','dir'=>'asc']); $last=$run(['sort'=>'amount','dir'=>'asc','page'=>'999']);
    assert_same(26,$firstPage['total']); assert_same(25,count($firstPage['orders'])); assert_same(2,$last['page']); assert_same('78.0000',$last['orders'][0]['total']);
    $order=pl_confirm_purchase_order(...array_merge($args,[$first['id'],$first['revision'],bin2hex(random_bytes(16))]));
    assert_same(1,$run(['status'=>'ordered'])['total']);
    pl_receive_purchase_order(...array_merge($args,[$order['id'],purchasing_receipt_input($f,$order,'1')]));
    $partial=$run(['status'=>'partial']); assert_same(1,$partial['total']); assert_same('confirmed',$partial['orders'][0]['status']); assert_same('partial',pl_purchase_order_progress($partial['orders'][0]));
    pl_receive_purchase_order(...array_merge($args,[$order['id'],purchasing_receipt_input($f,$order,'2')]));
    assert_same(1,$run(['status'=>'received'])['total']); assert_same(0,$run(['status'=>'partial'])['total']);
    assert_same(25,$run(['status'=>'draft'])['total']); assert_same(1,$run(['q'=>$order['number']])['total']); assert_same(0,$run(['q'=>'%'])['total']);
    foreach ([['sort'=>'amount DESC'],['status'=>'posted'],['dir'=>'invalid']] as $bad) { assert_throws(fn()=>$run($bad),DomainException::class); }
    $other=ledger_fixture(); assert_throws(fn()=>pl_page_purchase_orders($other['actor_id'],$f['company_id'],$f['book_id'],[]),DomainException::class);
});

function purchasing_fixture(): array
{
    $f = inventory_fixture();
    $manifest = pl_module_registry()['purchasing'];
    pl_set_company_module($f['actor_id'], $f['company_id'], 'purchasing', true, 0, $manifest['digest'], 'Synthetic purchasing module', bin2hex(random_bytes(16)));
    $party = pl_save_party($f['actor_id'], $f['company_id'], $f['book_id'], ['legal_name' => 'Synthetic purchasing vendor', 'entity_type' => 'private_company', 'country_code' => 'GB', 'is_customer' => true, 'is_vendor' => true, 'currency' => 'USD', 'request_key' => bin2hex(random_bytes(16)), 'reason' => 'Synthetic purchasing fixture']);
    return $f + ['party_id' => $party['id']];
}

function purchasing_order(array $f, string $quantity = '10', string $price = '10', string $currency = 'USD'): array
{
    $draft = pl_save_purchase_order($f['actor_id'], $f['company_id'], $f['book_id'], ['party_id' => $f['party_id'], 'date' => '2026-01-05', 'currency' => $currency, 'reference' => 'Synthetic purchase order', 'creation_key' => bin2hex(random_bytes(16)), 'lines' => [['product_id' => $f['product_id'], 'description' => 'Synthetic goods', 'quantity' => $quantity, 'unit_price' => $price]]]);
    return pl_confirm_purchase_order($f['actor_id'], $f['company_id'], $f['book_id'], $draft['id'], $draft['revision'], bin2hex(random_bytes(16)));
}

function purchasing_receipt_input(array $f, array $order, string $quantity, string $date = '2026-01-06'): array
{
    return ['date' => $date, 'grni_account_id' => $f['grni_account_id'], 'idempotency_key' => bin2hex(random_bytes(16)), 'lines' => [['order_line_id' => $order['lines'][0]['id'], 'quantity' => $quantity]]];
}

function purchasing_bill_input(array $f, array $receipt, string $quantity, string $price = '10'): array
{
    return ['party_id' => $f['party_id'], 'date' => '2026-01-09', 'due_date' => '2026-02-09', 'currency' => 'USD', 'grni_account_id' => $f['grni_account_id'], 'reference' => 'Synthetic supplier bill', 'idempotency_key' => bin2hex(random_bytes(16)), 'lines' => [['receipt_line_id' => $receipt['lines'][0]['id'], 'quantity' => $quantity, 'unit_price' => $price]]];
}

test('purchasing partial order receipts and later AP bills reconcile independent inventory and payable ledgers', function (): void {
    $f = purchasing_fixture(); $order = purchasing_order($f);
    assert_same(0, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id=%i', $f['book_id']));
    $input = purchasing_receipt_input($f, $order, '6');
    $first = pl_receive_purchase_order($f['actor_id'], $f['company_id'], $f['book_id'], $order['id'], $input);
    assert_true($first == pl_receive_purchase_order($f['actor_id'], $f['company_id'], $f['book_id'], $order['id'], $input));
    $second = pl_receive_purchase_order($f['actor_id'], $f['company_id'], $f['book_id'], $order['id'], purchasing_receipt_input($f, $order, '4', '2026-01-07'));
    assert_same('100.0000', pl_inventory_valuation($f['actor_id'], $f['company_id'], $f['book_id'], '2026-01-07')['total_value_base']);
    assert_same('100.0000', pl_purchase_received_unbilled($f['actor_id'], $f['company_id'], $f['book_id'], '2026-01-07')['accounts'][0]['unbilled_base']);
    $billInput = purchasing_bill_input($f, $first, '6');
    $billInput['lines'][] = ['receipt_line_id' => $second['lines'][0]['id'], 'quantity' => '4', 'unit_price' => '10'];
    $billed = pl_bill_purchase_receipts($f['actor_id'], $f['company_id'], $f['book_id'], $billInput);
    assert_true($billed == pl_bill_purchase_receipts($f['actor_id'], $f['company_id'], $f['book_id'], $billInput));
    $clearing = pl_purchase_received_unbilled($f['actor_id'], $f['company_id'], $f['book_id'], '2026-01-09');
    assert_same('0.0000', $clearing['accounts'][0]['unbilled_base']); assert_same('0.0000', $clearing['accounts'][0]['difference']);
    assert_same('100.0000', pl_ar_ap_open_items($f['actor_id'], $f['company_id'], $f['book_id'], 'payable', '2026-01-09')['total_base']);
    pl_settle_ar_document($f['actor_id'], $f['company_id'], $f['book_id'], $billed['bill_document_id'], ar_ap_payment($f, '40', '2026-01-10'));
    assert_same('60.0000', pl_ar_ap_open_items($f['actor_id'], $f['company_id'], $f['book_id'], 'payable', '2026-01-10')['total_base']);
    assert_same('100.0000', pl_purchase_received_unbilled($f['actor_id'], $f['company_id'], $f['book_id'], '2026-01-07')['accounts'][0]['unbilled_base']);
});

test('purchasing rejects excess receipt duplicate billing and cross-company references atomically', function (): void {
    $f = purchasing_fixture(); $order = purchasing_order($f);
    assert_throws(fn() => pl_receive_purchase_order($f['actor_id'], $f['company_id'], $f['book_id'], $order['id'], purchasing_receipt_input($f, $order, '11')), DomainException::class, 'exceeds');
    assert_same(0, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_purchase_receipts WHERE book_id=%i', $f['book_id']));
    $receipt = pl_receive_purchase_order($f['actor_id'], $f['company_id'], $f['book_id'], $order['id'], purchasing_receipt_input($f, $order, '10'));
    $input = purchasing_bill_input($f, $receipt, '10');
    pl_bill_purchase_receipts($f['actor_id'], $f['company_id'], $f['book_id'], $input);
    $input['idempotency_key'] = bin2hex(random_bytes(16));
    assert_throws(fn() => pl_bill_purchase_receipts($f['actor_id'], $f['company_id'], $f['book_id'], $input), DomainException::class, 'exceeds');
    assert_same(1, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_ar_documents WHERE book_id=%i', $f['book_id']));
    $other = purchasing_fixture();
    assert_throws(fn() => pl_get_purchase_order($other['actor_id'], $other['company_id'], $other['book_id'], $order['id']), DomainException::class, 'available');
    assert_throws(fn() => DB::update('pl_purchase_receipt_lines', ['quantity' => '11'], 'id=%i', $receipt['lines'][0]['id']), Throwable::class, 'immutable');
});

test('purchase price variance needs review and preserves receipt cost while clearing GRNI', function (): void {
    $f = purchasing_fixture(); $order = purchasing_order($f);
    $receipt = pl_receive_purchase_order($f['actor_id'], $f['company_id'], $f['book_id'], $order['id'], purchasing_receipt_input($f, $order, '10'));
    $input = purchasing_bill_input($f, $receipt, '10', '12');
    assert_throws(fn() => pl_bill_purchase_receipts($f['actor_id'], $f['company_id'], $f['book_id'], $input), DomainException::class, 'review');
    assert_same(0, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_ar_documents WHERE book_id=%i', $f['book_id']));
    $input['variance_confirmed'] = true; $input['variance_account_id'] = $f['variance_account_id'];
    $bill = pl_bill_purchase_receipts($f['actor_id'], $f['company_id'], $f['book_id'], $input);
    assert_same('120.0000', pl_ar_ap_open_items($f['actor_id'], $f['company_id'], $f['book_id'], 'payable', '2026-01-09')['total_base']);
    assert_same('100.0000', pl_inventory_valuation($f['actor_id'], $f['company_id'], $f['book_id'], '2026-01-09')['total_value_base']);
    assert_same('0.0000', pl_purchase_received_unbilled($f['actor_id'], $f['company_id'], $f['book_id'], '2026-01-09')['accounts'][0]['difference']);
    $return = ['receipt_line_id' => $receipt['lines'][0]['id'], 'match_id' => $bill['match_ids'][0], 'quantity' => '2', 'date' => '2026-01-10', 'reason' => 'Synthetic damaged goods', 'idempotency_key' => bin2hex(random_bytes(16))];
    $result = pl_return_purchase_receipt($f['actor_id'], $f['company_id'], $f['book_id'], $return);
    assert_true($result == pl_return_purchase_receipt($f['actor_id'], $f['company_id'], $f['book_id'], $return));
    assert_same('96.0000', pl_ar_ap_open_items($f['actor_id'], $f['company_id'], $f['book_id'], 'payable', '2026-01-10')['total_base']);
    assert_same('80.0000', pl_inventory_valuation($f['actor_id'], $f['company_id'], $f['book_id'], '2026-01-10')['total_value_base']);
    assert_same('0.0000', pl_purchase_received_unbilled($f['actor_id'], $f['company_id'], $f['book_id'], '2026-01-10')['accounts'][0]['difference']);
    $document = pl_get_ar_document($f['actor_id'], $f['company_id'], $f['book_id'], $bill['bill_document_id']);
    assert_throws(fn() => pl_reverse_journal($f['actor_id'], $f['company_id'], $f['book_id'], $document['journal_id'], gmdate('Y-m-d'), bin2hex(random_bytes(16)), 'Detached reversal'), DomainException::class);
});

test('unbilled purchase returns reduce stock and clearing without creating a payable', function (): void {
    $f = purchasing_fixture(); $order = purchasing_order($f);
    $receipt = pl_receive_purchase_order($f['actor_id'], $f['company_id'], $f['book_id'], $order['id'], purchasing_receipt_input($f, $order, '10'));
    pl_return_purchase_receipt($f['actor_id'], $f['company_id'], $f['book_id'], ['receipt_line_id' => $receipt['lines'][0]['id'], 'quantity' => '3', 'date' => '2026-01-07', 'reason' => 'Synthetic unbilled damage', 'idempotency_key' => bin2hex(random_bytes(16))]);
    $report = pl_purchase_received_unbilled($f['actor_id'], $f['company_id'], $f['book_id'], '2026-01-07');
    assert_same('70.0000', $report['accounts'][0]['unbilled_base']); assert_same('0.0000', $report['accounts'][0]['difference']);
    assert_same(0, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_ar_documents WHERE book_id=%i', $f['book_id']));
    $bill = pl_bill_purchase_receipts($f['actor_id'], $f['company_id'], $f['book_id'], purchasing_bill_input($f, $receipt, '7'));
    assert_same('0.0000', pl_purchase_received_unbilled($f['actor_id'], $f['company_id'], $f['book_id'], '2026-01-09')['accounts'][0]['difference']);
    assert_true($bill['bill_document_id'] > 0);
});

test('purchasing cancellation and module disable preserve receipts while blocking new receipts', function (): void {
    $f = purchasing_fixture(); $order = purchasing_order($f);
    $receipt = pl_receive_purchase_order($f['actor_id'], $f['company_id'], $f['book_id'], $order['id'], purchasing_receipt_input($f, $order, '6'));
    pl_cancel_purchase_order($f['actor_id'], $f['company_id'], $f['book_id'], $order['id'], $order['revision'], 'Cancel remaining four units', bin2hex(random_bytes(16)));
    assert_throws(fn() => pl_receive_purchase_order($f['actor_id'], $f['company_id'], $f['book_id'], $order['id'], purchasing_receipt_input($f, $order, '4')), DomainException::class, 'confirmed');
    $manifest = pl_module_registry()['purchasing'];
    pl_set_company_module($f['actor_id'], $f['company_id'], 'purchasing', false, 1, $manifest['digest'], 'Hide unused purchasing', bin2hex(random_bytes(16)));
    assert_same($receipt['lines'][0]['id'], (int) pl_purchasing_receipts($f['actor_id'], $f['company_id'], $f['book_id'])[0]['id']);
    assert_throws(fn() => pl_bill_purchase_receipts($f['actor_id'], $f['company_id'], $f['book_id'], purchasing_bill_input($f, $receipt, '6')), DomainException::class, 'disabled');
});

test('mixed fractional bills and unbilled returns consume actual remaining basis with reviewed residual variance', function (): void {
    $f = purchasing_fixture(); $order = purchasing_order($f, '6', '1', 'EUR');
    $input = purchasing_receipt_input($f, $order, '6'); $input['rate'] = '0.166666666667';
    $receipt = pl_receive_purchase_order($f['actor_id'], $f['company_id'], $f['book_id'], $order['id'], $input);
    assert_same('1.0000', $receipt['lines'][0]['amount_base']);
    foreach ([['2026-01-09', '2026-01-10'], ['2026-01-11', '2026-01-12']] as [$billDate, $returnDate]) {
        $bill = purchasing_bill_input($f, $receipt, '1', '1'); $bill['currency'] = 'EUR'; $bill['rate'] = '0.166666666667'; $bill['date'] = $billDate;
        $bill['variance_confirmed'] = true; $bill['variance_account_id'] = $f['variance_account_id'];
        pl_bill_purchase_receipts($f['actor_id'], $f['company_id'], $f['book_id'], $bill);
        $returned = ['receipt_line_id' => $receipt['lines'][0]['id'], 'quantity' => '1', 'date' => $returnDate, 'reason' => 'Synthetic fractional return', 'idempotency_key' => bin2hex(random_bytes(16))];
        if ($returnDate === '2026-01-12') {
            assert_throws(fn() => pl_return_purchase_receipt($f['actor_id'], $f['company_id'], $f['book_id'], $returned), DomainException::class, 'variance');
            $returned['variance_confirmed'] = true; $returned['variance_account_id'] = $f['variance_account_id'];
        }
        $result = pl_return_purchase_receipt($f['actor_id'], $f['company_id'], $f['book_id'], $returned);
        if ($returnDate === '2026-01-12') { assert_true($result['variance_journal_id'] !== null); }
    }
    $return = ['receipt_line_id' => $receipt['lines'][0]['id'], 'quantity' => '2', 'date' => '2026-01-13', 'reason' => 'Final fractional return', 'idempotency_key' => bin2hex(random_bytes(16))];
    pl_return_purchase_receipt($f['actor_id'], $f['company_id'], $f['book_id'], $return);
    $report = pl_purchase_received_unbilled($f['actor_id'], $f['company_id'], $f['book_id'], '2026-01-13');
    assert_same('0.0000', $report['accounts'][0]['unbilled_base']); assert_same('0.0000', $report['accounts'][0]['difference']);
    assert_same('0.0000', $report['lines'][0]['quantity']);
});

test('concurrent purchasing receipt and bill allocations cannot overreceive or overbill', function (): void {
    $f = purchasing_fixture(); $order = purchasing_order($f);
    $input = purchasing_receipt_input($f, $order, '6');
    $job = ['mode' => 'purchase_receive', 'fixture' => $f, 'order_id' => $order['id'], 'receipt_input' => $input, 'allow_domain_failure' => true];
    $other = $job; $other['receipt_input']['idempotency_key'] = bin2hex(random_bytes(16));
    $results = ledger_race([$job, $other]);
    assert_same(1, count(array_filter($results, static fn(array $r): bool => $r['id'] > 0)), 'Exactly one six-unit receipt must fit in a ten-unit order.');
    assert_same('6.0000', pl_get_purchase_order($f['actor_id'], $f['company_id'], $f['book_id'], $order['id'])['lines'][0]['received_quantity']);
    $line = pl_purchasing_receipts($f['actor_id'], $f['company_id'], $f['book_id'])[0];
    $bill = purchasing_bill_input($f, ['lines' => [['id' => (int) $line['id']]]], '4');
    $job = ['mode' => 'purchase_bill', 'fixture' => $f, 'bill_input' => $bill, 'allow_domain_failure' => true];
    $other = $job; $other['bill_input']['idempotency_key'] = bin2hex(random_bytes(16));
    $results = ledger_race([$job, $other]);
    assert_same(1, count(array_filter($results, static fn(array $r): bool => $r['id'] > 0)), 'Exactly one four-unit bill must fit against six received units.');
    assert_same('2.0000', pl_purchasing_receipts($f['actor_id'], $f['company_id'], $f['book_id'])[0]['unbilled_quantity']);
    assert_same('0.0000', pl_purchase_received_unbilled($f['actor_id'], $f['company_id'], $f['book_id'], '2026-01-09')['accounts'][0]['difference']);
});

test('purchasing stale caller snapshots see committed receipt quantities before the next receipt', function (): void {
    $f = purchasing_fixture(); $order = purchasing_order($f);
    DB::startTransaction();
    try {
        DB::query('SELECT id FROM pl_purchase_receipt_lines WHERE book_id=%i', $f['book_id']);
        ledger_race([['mode' => 'purchase_receive', 'fixture' => $f, 'order_id' => $order['id'], 'receipt_input' => purchasing_receipt_input($f, $order, '6')]]);
        assert_throws(fn() => pl_receive_purchase_order($f['actor_id'], $f['company_id'], $f['book_id'], $order['id'], purchasing_receipt_input($f, $order, '6')), DomainException::class, 'exceeds');
        DB::rollback();
    } catch (Throwable $error) { if (DB::transactionDepth() > 0) { DB::rollback(); } throw $error; }
});

test('inclusive supplier bills and returns keep net receipt matching separate from input tax', function (): void {
    $f = purchasing_fixture();
    foreach (['tax_out' => ['2150', 'liability'], 'tax_in' => ['1350', 'asset']] as $name => [$code, $type]) {
        $account = pl_save_account($f['actor_id'], $f['company_id'], $f['book_id'], ['code' => $code, 'name' => 'Synthetic purchasing ' . $name, 'type' => $type, 'role' => null, 'is_active' => true, 'reason' => 'Synthetic tax accounts', 'creation_key' => bin2hex(random_bytes(16))]);
        $f[$name] = (int) $account['id'];
    }
    $tax = pl_create_tax_code($f['actor_id'], $f['company_id'], $f['book_id'], ['code' => 'SYNTHETIC', 'name' => 'Synthetic purchasing tax', 'treatment' => 'standard', 'sales_account_id' => $f['tax_out'], 'purchase_account_id' => $f['tax_in'], 'reason' => 'Synthetic test', 'idempotency_key' => bin2hex(random_bytes(16))]);
    pl_enter_tax_rate($f['actor_id'], $f['company_id'], $f['book_id'], ['tax_code_id' => $tax['id'], 'effective_from' => '2026-01-01', 'percentage' => '10', 'reason' => 'Synthetic first rate', 'idempotency_key' => bin2hex(random_bytes(16))]);
    pl_set_tax_price_mode($f['actor_id'], $f['company_id'], $f['book_id'], 'inclusive', 0, 'Supplier prices include tax', bin2hex(random_bytes(16)));
    $order = purchasing_order($f); $receipt = pl_receive_purchase_order($f['actor_id'], $f['company_id'], $f['book_id'], $order['id'], purchasing_receipt_input($f, $order, '10'));
    $input = purchasing_bill_input($f, $receipt, '10', '11'); $input['lines'][0]['tax_code_id'] = $tax['id'];
    $preview = pl_preview_purchase_bill($f['actor_id'], $f['company_id'], $f['book_id'], $input);
    assert_same('inclusive', $preview['price_mode']); assert_same('100.0000', $preview['net_fc']); assert_same('10.0000', $preview['tax_fc']); assert_same('110.0000', $preview['total_fc']); assert_same('0.0000', $preview['variance_base']);
    $bill = pl_bill_purchase_receipts($f['actor_id'], $f['company_id'], $f['book_id'], $input + ['expected_hash' => $preview['payload_hash']]);
    assert_same('110.0000', pl_ar_ap_open_items($f['actor_id'], $f['company_id'], $f['book_id'], 'payable', '2026-01-09')['total_base']);
    assert_same('0.0000', pl_purchase_received_unbilled($f['actor_id'], $f['company_id'], $f['book_id'], '2026-01-09')['accounts'][0]['difference']);
    pl_enter_tax_rate($f['actor_id'], $f['company_id'], $f['book_id'], ['tax_code_id' => $tax['id'], 'effective_from' => '2026-01-10', 'percentage' => '20', 'reason' => 'Later rate preserves original credit basis', 'idempotency_key' => bin2hex(random_bytes(16))]);
    pl_set_tax_price_mode($f['actor_id'], $f['company_id'], $f['book_id'], 'exclusive', 1, 'Changed default preserves posted source', bin2hex(random_bytes(16)));
    $returned = pl_return_purchase_receipt($f['actor_id'], $f['company_id'], $f['book_id'], ['receipt_line_id' => $receipt['lines'][0]['id'], 'match_id' => $bill['match_ids'][0], 'quantity' => '2', 'date' => '2026-01-10', 'reason' => 'Synthetic taxed return', 'idempotency_key' => bin2hex(random_bytes(16))]);
    $credit = pl_get_ar_document($f['actor_id'], $f['company_id'], $f['book_id'], $returned['credit_document_id']);
    assert_same('inclusive', $credit['price_mode']); assert_same('20.0000', $credit['subtotal']); assert_same('2.0000', $credit['tax_total']); assert_same('22.0000', $credit['total']);
    assert_same('88.0000', pl_ar_ap_open_items($f['actor_id'], $f['company_id'], $f['book_id'], 'payable', '2026-01-10')['total_base']);
    assert_same('80.0000', pl_inventory_valuation($f['actor_id'], $f['company_id'], $f['book_id'], '2026-01-10')['total_value_base']);
    assert_same('0.0000', pl_purchase_received_unbilled($f['actor_id'], $f['company_id'], $f['book_id'], '2026-01-10')['accounts'][0]['difference']);
});

test('supplier bill confirmation rejects a stale receipt preview and closed receipt periods roll back stock', function (): void {
    $f = purchasing_fixture(); $order = purchasing_order($f);
    DB::update('pl_periods', ['status' => 'closed'], 'id=%i', $f['period_id']);
    assert_throws(fn() => pl_receive_purchase_order($f['actor_id'], $f['company_id'], $f['book_id'], $order['id'], purchasing_receipt_input($f, $order, '10')), DomainException::class, 'open accounting period');
    assert_same(0, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_purchase_receipts WHERE book_id=%i', $f['book_id']));
    assert_same(0, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_inventory_movements WHERE book_id=%i', $f['book_id']));
    DB::update('pl_periods', ['status' => 'open'], 'id=%i', $f['period_id']);
    $receipt = pl_receive_purchase_order($f['actor_id'], $f['company_id'], $f['book_id'], $order['id'], purchasing_receipt_input($f, $order, '10'));
    $input = purchasing_bill_input($f, $receipt, '5');
    $preview = pl_preview_purchase_bill($f['actor_id'], $f['company_id'], $f['book_id'], $input);
    pl_bill_purchase_receipts($f['actor_id'], $f['company_id'], $f['book_id'], purchasing_bill_input($f, $receipt, '2'));
    assert_throws(fn() => pl_bill_purchase_receipts($f['actor_id'], $f['company_id'], $f['book_id'], $input + ['expected_hash' => $preview['payload_hash']]), DomainException::class, 'review changed');
    assert_same(1, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_ar_documents WHERE book_id=%i', $f['book_id']));
});
