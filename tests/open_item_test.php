<?php
declare(strict_types=1);

function open_item_fixture(bool $payable = false): array
{
    $f = ledger_fixture('PKR');
    $party = pl_save_party($f['actor_id'], $f['company_id'], $f['book_id'], ['legal_name' => 'Synthetic settlement party', 'entity_type' => 'private_company', 'country_code' => 'GB', 'is_customer' => true, 'is_vendor' => true, 'currency' => 'USD', 'request_key' => bin2hex(random_bytes(16)), 'reason' => 'Synthetic foundation fixture']);
    $account = (int) DB::queryFirstField('SELECT id FROM pl_accounts WHERE book_id = %i AND role = %s', $f['book_id'], $payable ? 'payables' : 'receivables');
    pl_activate_open_item_account($f['actor_id'], $f['company_id'], $f['book_id'], $account, 'Synthetic unused control activation');
    return $f + ['party_id' => $party['id'], 'control_account_id' => $account, 'payable' => $payable];
}

function open_item_recognition_input(array $f, string $amount = '2000', string $rate = '281'): array
{
    return ['party_id' => $f['party_id'], 'control_account_id' => $f['control_account_id'], 'offset_account_id' => $f['accounts'][$f['payable'] ? '5000' : '4000'], 'currency' => 'USD', 'amount_fc' => $amount, 'date' => '2026-01-05', 'rate' => $rate, 'source_reference' => bin2hex(random_bytes(16)), 'description' => 'Synthetic recognition, no invoice document', 'idempotency_key' => bin2hex(random_bytes(16))];
}

function open_item_settlement_input(array $f, int $itemId, string $amount = '2000', string $rate = '279.50'): array
{
    return ['item_id' => $itemId, 'bank_account_id' => $f['accounts']['1000'], 'gain_account_id' => $f['accounts']['4000'], 'loss_account_id' => $f['accounts']['5000'], 'amount_fc' => $amount, 'date' => '2026-02-20', 'actual_rate' => $rate, 'description' => 'Synthetic bank settlement', 'idempotency_key' => bin2hex(random_bytes(16))];
}

test('realised FX worked example closes both currencies and reverses allocation with the journal', function (): void {
    $f = open_item_fixture(); $input = open_item_recognition_input($f);
    $r = pl_open_item_recognize($f['actor_id'], $f['company_id'], $f['book_id'], $input);
    assert_same($r, pl_open_item_recognize($f['actor_id'], $f['company_id'], $f['book_id'], $input));
    $settlement = open_item_settlement_input($f, $r['item_id']);
    $s = pl_settle_open_item($f['actor_id'], $f['company_id'], $f['book_id'], $settlement);
    assert_same('562000.0000', $s['allocated_base']); assert_same('559000.0000', $s['settlement_base']);
    assert_same('279.500000000000', $s['settlement_rate']); assert_same('actual', $s['settlement_rate_type']);
    $j = pl_get_journal($f['actor_id'], $f['company_id'], $f['book_id'], $s['journal_id']);
    assert_same('562000.0000', $j['lines'][0]['credit']); assert_same('559000.0000', $j['lines'][1]['debit']); assert_same('3000.0000', $j['lines'][2]['debit']);
    $state = pl_get_open_item($f['actor_id'], $f['company_id'], $f['book_id'], $r['item_id']);
    assert_same('0.0000', $state['remaining_fc']); assert_same('0.0000', $state['remaining_base']);
    assert_same($s, pl_settle_open_item($f['actor_id'], $f['company_id'], $f['book_id'], $settlement));
    assert_throws(fn() => pl_reverse_journal($f['actor_id'], $f['company_id'], $f['book_id'], $r['journal_id'], gmdate('Y-m-d'), bin2hex(random_bytes(16)), 'Recognition has allocated money'), DomainException::class, 'allocations');
    pl_reverse_journal($f['actor_id'], $f['company_id'], $f['book_id'], $s['journal_id'], gmdate('Y-m-d'), bin2hex(random_bytes(16)), 'Explicit settlement correction');
    $state = pl_get_open_item($f['actor_id'], $f['company_id'], $f['book_id'], $r['item_id']);
    assert_same('2000.0000', $state['remaining_fc']); assert_same('562000.0000', $state['remaining_base']);
    // A retry is a durable command receipt, not a second settlement after reversal.
    assert_same($s, pl_settle_open_item($f['actor_id'], $f['company_id'], $f['book_id'], $settlement));
});

test('AP settlement handles gain loss and zero difference with the correct signs', function (): void {
    foreach (['279.5' => ['income', '3000.0000'], '282' => ['expense', '2000.0000'], '281' => [null, '0.0000']] as $rate => [$type, $amount]) {
        $f = open_item_fixture(true); $r = pl_open_item_recognize($f['actor_id'], $f['company_id'], $f['book_id'], open_item_recognition_input($f));
        $s = pl_settle_open_item($f['actor_id'], $f['company_id'], $f['book_id'], open_item_settlement_input($f, $r['item_id'], '2000', (string) $rate));
        $j = pl_get_journal($f['actor_id'], $f['company_id'], $f['book_id'], $s['journal_id']);
        assert_same('562000.0000', $j['lines'][0]['debit']);
        if ($type === null) { assert_same(2, count($j['lines'])); }
        else { assert_same($amount, $j['lines'][2][$type === 'income' ? 'credit' : 'debit']); }
        assert_same('0.0000', pl_get_open_item($f['actor_id'], $f['company_id'], $f['book_id'], $r['item_id'])['remaining_base']);
    }
});

test('partial settlements allocate the exact final carrying residual without changing recognition rate', function (): void {
    $f = open_item_fixture(); $r = pl_open_item_recognize($f['actor_id'], $f['company_id'], $f['book_id'], open_item_recognition_input($f, '3', '0.333333333333'));
    $released = [];
    for ($i = 0; $i < 3; $i++) {
        $s = pl_settle_open_item($f['actor_id'], $f['company_id'], $f['book_id'], open_item_settlement_input($f, $r['item_id'], '1', '0.4'));
        $released[] = $s['allocated_base'];
        assert_same('0.333333333333', pl_get_journal($f['actor_id'], $f['company_id'], $f['book_id'], $s['journal_id'])['lines'][0]['rate']);
    }
    assert_same(['0.3333', '0.3334', '0.3333'], $released);
    $state = pl_get_open_item($f['actor_id'], $f['company_id'], $f['book_id'], $r['item_id']);
    assert_same('0.0000', $state['remaining_fc']); assert_same('0.0000', $state['remaining_base']);
});

test('unused-account activation and tracked-control funnel cannot be bypassed', function (): void {
    $f = open_item_fixture(); $payload = ledger_payload($f); $payload['currency'] = 'PKR'; $payload['lines'][0]['account_id'] = $f['control_account_id'];
    assert_throws(fn() => pl_post_journal($f['actor_id'], $f['company_id'], $f['book_id'], $payload), DomainException::class, 'open-item');
    $other = ledger_fixture(); $control = (int) DB::queryFirstField("SELECT id FROM pl_accounts WHERE book_id = %i AND role = 'receivables'", $other['book_id']);
    $old = ledger_payload($other); $old['lines'][0]['account_id'] = $control;
    pl_post_journal($other['actor_id'], $other['company_id'], $other['book_id'], $old);
    assert_throws(fn() => pl_activate_open_item_account($other['actor_id'], $other['company_id'], $other['book_id'], $control, 'Not actually unused'), DomainException::class, 'unused');
    assert_throws(fn() => pl_open_item_recognize($f['actor_id'], $f['company_id'], $f['book_id'], array_replace(open_item_recognition_input($f), ['party_id' => 99999999])), DomainException::class);
});

test('settlement rejects conflicts closed periods over-allocation and failed writes roll back', function (): void {
    $f = open_item_fixture(); $r = pl_open_item_recognize($f['actor_id'], $f['company_id'], $f['book_id'], open_item_recognition_input($f));
    $input = open_item_settlement_input($f, $r['item_id'], '2001');
    assert_throws(fn() => pl_settle_open_item($f['actor_id'], $f['company_id'], $f['book_id'], $input), DomainException::class, 'over-allocated');
    $input['amount_fc'] = '1000';
    $before = (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id = %i', $f['book_id']);
    assert_throws(function () use ($f, $input): void { pl_ledger_transaction(function () use ($f, $input): void { pl_settle_open_item($f['actor_id'], $f['company_id'], $f['book_id'], $input); throw new DomainException('Synthetic outer failure'); }); });
    assert_same($before, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id = %i', $f['book_id']));
    assert_same('2000.0000', pl_get_open_item($f['actor_id'], $f['company_id'], $f['book_id'], $r['item_id'])['remaining_fc']);
    DB::update('pl_periods', ['status' => 'closed'], 'id = %i', $f['period_id']);
    assert_throws(fn() => pl_settle_open_item($f['actor_id'], $f['company_id'], $f['book_id'], $input), DomainException::class, 'open accounting period');
    DB::update('pl_periods', ['status' => 'open'], 'id = %i', $f['period_id']);
    pl_settle_open_item($f['actor_id'], $f['company_id'], $f['book_id'], $input);
    $input['actual_rate'] = '300';
    assert_throws(fn() => pl_settle_open_item($f['actor_id'], $f['company_id'], $f['book_id'], $input), DomainException::class, 'different content');
    assert_throws(fn() => DB::delete('pl_open_item_entries', 'item_id = %i', $r['item_id']), Throwable::class, 'immutable');
});

test('settlement races serialize duplicate commands and prevent overlapping allocation', function (): void {
    $f = open_item_fixture(); $r = pl_open_item_recognize($f['actor_id'], $f['company_id'], $f['book_id'], open_item_recognition_input($f));
    $input = open_item_settlement_input($f, $r['item_id'], '1000');
    $job = ['mode' => 'open_item_settle', 'fixture' => $f, 'settlement_input' => $input];
    $same = ledger_race([$job, $job]); assert_same($same[0]['id'], $same[1]['id']);
    $one = $job; $one['allow_domain_failure'] = true; $one['settlement_input']['idempotency_key'] = bin2hex(random_bytes(16));
    $two = $one; $two['settlement_input']['idempotency_key'] = bin2hex(random_bytes(16));
    $race = ledger_race([$one, $two]);
    assert_same(1, count(array_filter($race, static fn (array $row): bool => $row['id'] > 0)));
    assert_same('0.0000', pl_get_open_item($f['actor_id'], $f['company_id'], $f['book_id'], $r['item_id'])['remaining_fc']);
});

test('foreign-bank payments and out-of-order activity are rejected without base-only residuals', function (): void {
    $f = open_item_fixture(true); $r = pl_open_item_recognize($f['actor_id'], $f['company_id'], $f['book_id'], open_item_recognition_input($f));
    DB::update('pl_accounts', ['currency' => 'USD'], 'id = %i', $f['accounts']['1000']);
    assert_throws(fn() => pl_settle_open_item($f['actor_id'], $f['company_id'], $f['book_id'], open_item_settlement_input($f, $r['item_id'])), DomainException::class, 'carrying-value');
    DB::update('pl_accounts', ['currency' => null], 'id = %i', $f['accounts']['1000']);
    $input = open_item_settlement_input($f, $r['item_id']);
    $s = pl_settle_open_item($f['actor_id'], $f['company_id'], $f['book_id'], $input);
    pl_reverse_journal($f['actor_id'], $f['company_id'], $f['book_id'], $s['journal_id'], gmdate('Y-m-d'), bin2hex(random_bytes(16)), 'Explicit reversal before later new allocation');
    $input['idempotency_key'] = bin2hex(random_bytes(16));
    assert_throws(fn() => pl_settle_open_item($f['actor_id'], $f['company_id'], $f['book_id'], $input), DomainException::class, 'latest open-item activity');
    assert_same('2000.0000', pl_get_open_item($f['actor_id'], $f['company_id'], $f['book_id'], $r['item_id'])['remaining_fc']);
});

test('recognition reversal exact retry returns its original receipt after the item closes', function (): void {
    $f = open_item_fixture();
    $recognized = pl_open_item_recognize($f['actor_id'], $f['company_id'], $f['book_id'], open_item_recognition_input($f));
    $key = bin2hex(random_bytes(16)); $date = gmdate('Y-m-d'); $reason = 'Synthetic recognition cancellation';
    $reversal = pl_reverse_journal($f['actor_id'], $f['company_id'], $f['book_id'], $recognized['journal_id'], $date, $key, $reason);
    assert_same('0.0000', pl_get_open_item($f['actor_id'], $f['company_id'], $f['book_id'], $recognized['item_id'])['remaining_fc']);
    assert_same($reversal, pl_reverse_journal($f['actor_id'], $f['company_id'], $f['book_id'], $recognized['journal_id'], $date, $key, $reason));
    assert_throws(fn() => pl_reverse_journal($f['actor_id'], $f['company_id'], $f['book_id'], $recognized['journal_id'], $date, $key, 'Changed cancellation reason'), DomainException::class);
    assert_same(2, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_open_item_entries WHERE item_id=%i', $recognized['item_id']));
});

test('open-item activation sees postings committed after the caller snapshot', function (): void {
    $f = ledger_fixture();
    $control = (int) DB::queryFirstField("SELECT id FROM pl_accounts WHERE book_id=%i AND role='receivables'", $f['book_id']);
    $payload = ledger_payload($f); $payload['lines'][0]['account_id'] = $control;
    DB::startTransaction();
    try {
        assert_same(0, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journal_lines WHERE account_id=%i', $control));
        $posted = ledger_race([['mode'=>'post', 'fixture'=>$f, 'payload'=>$payload]]);
        assert_true($posted[0]['id'] > 0);
        // Confirm this connection still has the older consistent-read snapshot.
        assert_same(0, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journal_lines WHERE account_id=%i', $control));
        assert_throws(fn() => pl_activate_open_item_account($f['actor_id'], $f['company_id'], $f['book_id'], $control, 'Synthetic stale snapshot activation'), DomainException::class, 'unused');
    } finally { DB::rollback(); }
    assert_same(0, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_open_item_accounts WHERE account_id=%i', $control));
});
