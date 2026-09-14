<?php
declare(strict_types=1);

test('owner statements reconcile posted receipt expense and dated reversal without including drafts', function (): void {
    $f = ledger_fixture();
    $receipt = pl_save_document($f['actor_id'], $f['company_id'], $f['book_id'], document_input($f, 'receipt', '1000'));
    pl_post_document($f['actor_id'], $f['company_id'], $f['book_id'], $receipt['id'], 1);
    $expense = pl_save_document($f['actor_id'], $f['company_id'], $f['book_id'], document_input($f));
    pl_post_document($f['actor_id'], $f['company_id'], $f['book_id'], $expense['id'], 1);
    pl_save_document($f['actor_id'], $f['company_id'], $f['book_id'], document_input($f, 'expense', '999'));
    $income = pl_profit_loss($f['actor_id'], $f['company_id'], $f['book_id'], '2026-09-01', '2026-09-14');
    assert_same('1000.0000', $income['total_income']);
    assert_same('125.0000', $income['total_expenses']);
    assert_same('875.0000', $income['net_profit']);
    $balance = pl_balance_sheet($f['actor_id'], $f['company_id'], $f['book_id'], '2026-09-14');
    assert_same('875.0000', $balance['total_assets']);
    assert_same('0.0000', $balance['recorded_equity']);
    assert_same('875.0000', $balance['earned_profit']);
    assert_same('875.0000', $balance['total_equity']);
    assert_true($balance['balanced']);
    assert_same('875.0000', pl_cash_balance($f['actor_id'], $f['company_id'], $f['book_id'], '2026-09-14'));
    pl_reverse_document($f['actor_id'], $f['company_id'], $f['book_id'], $expense['id'], '2026-09-15', 'Synthetic report correction');
    assert_same('875.0000', pl_balance_sheet($f['actor_id'], $f['company_id'], $f['book_id'], '2026-09-14')['earned_profit']);
    assert_same('1000.0000', pl_balance_sheet($f['actor_id'], $f['company_id'], $f['book_id'], '2026-09-15')['total_assets']);
    assert_same('-125.0000', pl_profit_loss($f['actor_id'], $f['company_id'], $f['book_id'], '2026-09-15', '2026-09-15')['total_expenses']);
    $activity = pl_account_activity($f['actor_id'], $f['company_id'], $f['book_id'], $f['accounts']['5000'], '2026-09-15', 1, '2026-09-15');
    assert_same('2026-09-15', $activity['from']);
    assert_same(1, $activity['total']);
    assert_same('-125.0000', $activity['balance']);
    assert_same('0.0000', $activity['debit_movement']);
    assert_same('125.0000', $activity['credit_movement']);
    assert_throws(fn () => pl_account_activity($f['actor_id'], $f['company_id'], $f['book_id'], $f['accounts']['5000'], '2026-09-14', 1, '2026-09-15'), DomainException::class);
});

test('owner statements include liabilities recorded capital and retained earnings exactly once', function (): void {
    $f = ledger_fixture();
    $payload = ledger_payload($f, '500');
    $payload['lines'][1]['account_id'] = $f['accounts']['3000'];
    pl_post_journal($f['actor_id'], $f['company_id'], $f['book_id'], $payload);
    $payload = ledger_payload($f, '200');
    $payload['lines'][1]['account_id'] = $f['accounts']['2000'];
    pl_post_journal($f['actor_id'], $f['company_id'], $f['book_id'], $payload);
    pl_post_journal($f['actor_id'], $f['company_id'], $f['book_id'], ledger_payload($f, '100'));
    $statement = pl_balance_sheet($f['actor_id'], $f['company_id'], $f['book_id'], '2026-09-14');
    assert_same('800.0000', $statement['total_assets']);
    assert_same('200.0000', $statement['total_liabilities']);
    assert_same('500.0000', $statement['recorded_equity']);
    assert_same('100.0000', $statement['earned_profit']);
    assert_same('600.0000', $statement['total_equity']);
    assert_same('800.0000', $statement['total_liabilities_equity']);
    assert_true($statement['balanced']);
    DB::update('pl_accounts', ['is_active' => 0], 'id = %i', $f['accounts']['1000']);
    assert_same('800.0000', pl_cash_balance($f['actor_id'], $f['company_id'], $f['book_id'], '2026-09-14'));
});

test('owner reports provide zero states valid date boundaries and scoped read permissions', function (): void {
    $f = ledger_fixture();
    $other = ledger_fixture();
    assert_same('0.0000', pl_profit_loss($f['actor_id'], $f['company_id'], $f['book_id'], '2026-01-01', '2026-09-14')['net_profit']);
    assert_true(pl_balance_sheet($f['actor_id'], $f['company_id'], $f['book_id'], '2025-01-01')['balanced']);
    assert_same('0.0000', pl_cash_balance($f['actor_id'], $f['company_id'], $f['book_id'], '2026-09-14'));
    assert_throws(fn () => pl_profit_loss($f['actor_id'], $f['company_id'], $f['book_id'], '2026-09-15', '2026-09-14'), DomainException::class);
    assert_throws(fn () => pl_balance_sheet($f['actor_id'], $f['company_id'], $f['book_id'], '2026-02-30'), DomainException::class);
    assert_throws(fn () => pl_balance_sheet($other['actor_id'], $f['company_id'], $f['book_id'], '2026-09-14'), DomainException::class);
    assert_throws(fn () => pl_profit_loss($f['actor_id'], $f['company_id'], $other['book_id'], '2026-01-01', '2026-09-14'), DomainException::class);
    assert_throws(fn () => pl_cash_balance($other['actor_id'], $f['company_id'], $f['book_id'], '2026-09-14'), DomainException::class);
    DB::insert('pl_company_members', ['company_id' => $f['company_id'], 'user_id' => $other['actor_id'], 'role' => 'viewer']);
    assert_true(pl_balance_sheet($other['actor_id'], $f['company_id'], $f['book_id'], '2026-09-14')['balanced']);
});

test('cash scenarios use exact signed balances and disclose the first shortfall without writes', function (): void {
    $projection = pl_cash_forecast('875', '100', '200', 12);
    assert_same('875.0000', $projection['opening']);
    assert_same('775.0000', $projection['rows'][0]['closing']);
    assert_same('-325.0000', $projection['closing']);
    assert_same(9, $projection['first_negative_week']);
    assert_same(0, pl_cash_forecast('-0.1000', '0.1000', '0.2000', 1)['first_negative_week']);
    assert_same('-0.2000', pl_cash_forecast('-0.1000', '0.1000', '0.2000', 1)['closing']);
    assert_same('0.3000', pl_cash_forecast('0.1000', '0.2000', '0', 1)['closing']);
    assert_same(null, pl_cash_forecast('0', '0', '0', 12)['first_negative_week']);
    assert_throws(fn () => pl_cash_forecast('0', '-1', '0'), DomainException::class);
    assert_throws(fn () => pl_cash_forecast('0', '0', '0', 53), DomainException::class);
    assert_throws(fn () => pl_cash_forecast('0', '0', '0', 0), DomainException::class);
    assert_throws(fn () => pl_cash_forecast('NaN', '0', '0'), DomainException::class);
});
