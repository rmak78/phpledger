<?php
declare(strict_types=1);

test('Home reuses posted cash and draft totals without posting or crossing company scope', function (): void {
    $f = ledger_fixture();
    $draft = pl_save_document($f['actor_id'], $f['company_id'], $f['book_id'], document_input($f, 'receipt', '23.4567'));
    $before = (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE company_id=%i', $f['company_id']);
    $home = pl_home_overview($f['actor_id'], $f['company_id'], $f['book_id'], '2026-09-17');
    assert_same('0.0000', $home['cash']);
    assert_same(1, $home['drafts']['total']);
    assert_same('23.4567', $home['drafts']['total_amount']);
    assert_same($draft['id'], $home['recent'][0]['id']);
    assert_same($before, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE company_id=%i', $f['company_id']));
    pl_post_document($f['actor_id'], $f['company_id'], $f['book_id'], $draft['id'], $draft['revision']);
    $home = pl_home_overview($f['actor_id'], $f['company_id'], $f['book_id'], '2026-09-17');
    assert_same('23.4567', $home['cash']);
    assert_same(0, $home['drafts']['total']);
    assert_same(pl_ar_ap_open_items($f['actor_id'], $f['company_id'], $f['book_id'], 'receivable', '2026-09-17'), $home['receivables']);
    $other = ledger_fixture();
    assert_throws(fn () => pl_home_overview($other['actor_id'], $f['company_id'], $f['book_id'], '2026-09-17'), DomainException::class);
    assert_throws(fn () => pl_home_overview($f['actor_id'], $f['company_id'], $other['book_id'], '2026-09-17'), DomainException::class);
});

test('Home bank attention agrees with reconciliation and excludes cancelled statements', function (): void {
    $f = ledger_fixture();
    $input = bank_fixture_input($f, [bank_fixture_row('HOME-1')], '12.34');
    $statement = bank_fixture_import($f, $input);
    $summary = pl_bank_reconciliation_summary($f['actor_id'], $f['company_id'], $f['book_id'], $statement['id']);
    assert_same($summary['unmatched_count'], pl_bank_pending_review_count($f['actor_id'], $f['company_id'], $f['book_id']));
    assert_same(1, pl_home_overview($f['actor_id'], $f['company_id'], $f['book_id'], '2026-09-17')['bank_lines']);
    pl_bank_cancel_statement($f['actor_id'], $f['company_id'], $f['book_id'], $statement['id'], $statement['revision'], 'Synthetic cancelled statement', bin2hex(random_bytes(16)));
    assert_same(0, pl_bank_pending_review_count($f['actor_id'], $f['company_id'], $f['book_id']));
});
