<?php
declare(strict_types=1);

function opening_conversion_fixture(): array
{
    $f = opening_fixture(); $input = opening_input();
    $input['unpaid_documents'][0]['outstanding'] = '100';
    $input['unpaid_documents'][] = ['kind' => 'receivable', 'account_code' => '1100', 'party' => 'Second synthetic customer', 'reference' => 'INV-02', 'document_date' => '2026-08-20', 'due_date' => '2026-10-10', 'outstanding' => '200'];
    $cutover = opening_confirm($f, opening_preview($f, $input));
    $party = pl_save_party($f['actor_id'], $f['company_id'], $f['book_id'], ['legal_name' => 'Synthetic mapped party', 'entity_type' => 'private_company', 'country_code' => 'GB', 'is_customer' => true, 'is_vendor' => true, 'currency' => 'USD', 'request_key' => bin2hex(random_bytes(16)), 'reason' => 'Synthetic explicit opening mapping']);
    $mappings = [];
    foreach (DB::query('SELECT id FROM pl_opening_documents WHERE cutover_id=%i ORDER BY id', $cutover['id']) as $document) { $mappings[] = ['opening_document_id' => (int) $document['id'], 'party_id' => $party['id']]; }
    return $f + ['cutover_id' => (int) $cutover['id'], 'mappings' => $mappings];
}

test('reviewed opening debt conversion allocates shared GL basis without posting twice', function (): void {
    $f = opening_conversion_fixture();
    $before = pl_trial_balance($f['actor_id'], $f['company_id'], $f['book_id']);
    $preview = pl_preview_opening_conversion($f['actor_id'], $f['company_id'], $f['book_id'], $f['cutover_id'], $f['mappings']);
    assert_same(2, count($preview['accounts'])); assert_same(3, count($preview['documents']));
    assert_same(0, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_open_items WHERE book_id=%i', $f['book_id']));
    $key = bin2hex(random_bytes(16));
    $result = pl_confirm_opening_conversion($f['actor_id'], $f['company_id'], $f['book_id'], $f['cutover_id'], $f['mappings'], $preview['payload_hash'], true, $key, 'Reviewed source mapping');
    assert_true($result == pl_confirm_opening_conversion($f['actor_id'], $f['company_id'], $f['book_id'], $f['cutover_id'], $f['mappings'], $preview['payload_hash'], true, $key, 'Reviewed source mapping'));
    assert_same($before, pl_trial_balance($f['actor_id'], $f['company_id'], $f['book_id']));
    assert_same(1, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id=%i', $f['book_id']));
    $ar = pl_ar_ap_open_items($f['actor_id'], $f['company_id'], $f['book_id'], 'receivable', '2026-09-16');
    assert_same('300.0000', $ar['total_base']); assert_same('100.0000', $ar['overdue_base']); assert_same('0.0000', $ar['difference_base']);
    $itemId = $result['items'][0]['item_id'];
    $state = pl_get_open_item($f['actor_id'], $f['company_id'], $f['book_id'], $itemId);
    assert_same('100.0000', $state['remaining_base']); assert_same('100.0000', $state['recognition']['amount_base']);
    pl_settle_open_item($f['actor_id'], $f['company_id'], $f['book_id'], ['item_id' => $itemId, 'bank_account_id' => $f['accounts']['1000'], 'gain_account_id' => $f['accounts']['4000'], 'loss_account_id' => $f['accounts']['5000'], 'amount_fc' => '40', 'date' => '2026-09-17', 'description' => 'Collect reviewed opening debt', 'idempotency_key' => bin2hex(random_bytes(16))]);
    $ar = pl_ar_ap_open_items($f['actor_id'], $f['company_id'], $f['book_id'], 'receivable', '2026-09-17');
    assert_same('260.0000', $ar['total_base']); assert_same('0.0000', $ar['difference_base']);
    assert_throws(fn() => pl_reverse_journal($f['actor_id'], $f['company_id'], $f['book_id'], $result['journal_id'], gmdate('Y-m-d'), bin2hex(random_bytes(16)), 'Do not erase opening basis'), DomainException::class);
    assert_throws(fn() => DB::update('pl_open_item_entries', ['allocated_amount_base' => '101'], 'item_id=%i AND opening_document_id IS NOT NULL', $itemId), Throwable::class, 'immutable');
});

test('opening conversion requires complete explicit mapping fresh digest and owner confirmation', function (): void {
    $f = opening_conversion_fixture();
    assert_throws(fn() => pl_preview_opening_conversion($f['actor_id'], $f['company_id'], $f['book_id'], $f['cutover_id'], array_slice($f['mappings'], 1)), DomainException::class, 'every');
    $bad = $f['mappings']; $bad[0]['party_id'] = 99999999;
    assert_throws(fn() => pl_preview_opening_conversion($f['actor_id'], $f['company_id'], $f['book_id'], $f['cutover_id'], $bad), DomainException::class, 'role');
    $preview = pl_preview_opening_conversion($f['actor_id'], $f['company_id'], $f['book_id'], $f['cutover_id'], $f['mappings']);
    assert_throws(fn() => pl_confirm_opening_conversion($f['actor_id'], $f['company_id'], $f['book_id'], $f['cutover_id'], $f['mappings'], $preview['payload_hash'], false, bin2hex(random_bytes(16)), 'Unconfirmed'), DomainException::class, 'Confirm');
    assert_throws(fn() => pl_confirm_opening_conversion($f['actor_id'], $f['company_id'], $f['book_id'], $f['cutover_id'], $f['mappings'], str_repeat('0', 64), true, bin2hex(random_bytes(16)), 'Stale review'), DomainException::class, 'changed');
    assert_same(0, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_open_items WHERE book_id=%i', $f['book_id']));
    $extra = ledger_payload($f); $extra['date'] = '2026-09-02'; $extra['lines'][0]['account_id'] = $f['accounts']['1100'];
    pl_post_journal($f['actor_id'], $f['company_id'], $f['book_id'], $extra);
    assert_throws(fn() => pl_preview_opening_conversion($f['actor_id'], $f['company_id'], $f['book_id'], $f['cutover_id'], $f['mappings']), DomainException::class, 'activity');
});

test('concurrent opening conversion retries allocate shared basis once and preserve the existing journal', function (): void {
    $f = opening_conversion_fixture();
    $preview = pl_preview_opening_conversion($f['actor_id'], $f['company_id'], $f['book_id'], $f['cutover_id'], $f['mappings']);
    $job = ['mode' => 'opening_convert', 'fixture' => $f, 'cutover_id' => $f['cutover_id'], 'mappings' => $f['mappings'], 'expected_hash' => $preview['payload_hash'], 'key' => bin2hex(random_bytes(16)), 'reason' => 'Synthetic concurrent review'];
    $results = ledger_race([$job, $job]);
    assert_same($results[0]['id'], $results[1]['id']);
    assert_same(3, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_open_items WHERE book_id=%i', $f['book_id']));
    assert_same(1, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_opening_conversions WHERE book_id=%i', $f['book_id']));
    assert_same(1, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id=%i', $f['book_id']));
});
