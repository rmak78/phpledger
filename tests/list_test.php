<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/www/phpledger/includes/functions/web_functions.php';
require_once dirname(__DIR__) . '/www/phpledger/templates/partials/ui/components.php';

test('list request rejects untrusted order identifiers malformed paging and array query input', function (): void {
    foreach (['transactions','general-journals','account','bank'] as $screen) {
        assert_throws(fn()=>pl_table_order(['sort'=>'date','direction'=>'DESC; SELECT 1'],$screen),DomainException::class);
        assert_throws(fn()=>pl_table_order(['sort'=>'default','direction'=>'asc'],$screen),DomainException::class);
    }
    assert_same('d.amount DESC, d.id ASC',pl_table_order(['sort'=>'amount','direction'=>'desc'],'transactions'));
    assert_same('r.line_number',pl_table_order([],'bank'));
    foreach ([['sort'=>'date DESC; DROP TABLE pl_users'], ['dir'=>'desc NULLS LAST'], ['page'=>'0'], ['page'=>'100001'], ['per_page'=>'250'], ['q'=>['x']], ['sort'=>'money_in']] as $input) {
        assert_throws(fn() => pl_list_filters($input, 'transactions'), DomainException::class);
    }
    $filters = pl_list_filters(['page'=>'2','per_page'=>'50','q'=>'  alpha & beta  ','sort'=>'amount','dir'=>'asc','status'=>'posted'], 'transactions');
    assert_same(2, $filters['page']); assert_same(50, $filters['per_page']); assert_same('alpha & beta', $filters['q']);
    ob_start(); pl_ui_pagination('/transactions', $filters, 2, 3); $html = (string)ob_get_clean();
    assert_true(str_contains($html, 'q=alpha+%26+beta') && str_contains($html, 'status=posted') && str_contains($html, 'sort=amount') && str_contains($html, 'per_page=50'));
    assert_true(str_contains($html, 'page=1') && str_contains($html, 'page=3'));
});

test('server lists paginate without overlaps preserve filters and enforce company scope', function (): void {
    $f = ledger_fixture();
    for ($i=0; $i<26; $i++) {
        pl_save_document($f['actor_id'], $f['company_id'], $f['book_id'], [
            'kind'=>'expense','date'=>'2026-09-14','amount'=>'10',
            'money_account_id'=>$f['accounts']['1000'],'category_account_id'=>$f['accounts']['5000'],
            'counterparty'=>'List fixture','reference'=>'ROW-'.$i,'memo'=>'Synthetic paging', 'creation_key'=>bin2hex(random_bytes(16)),
        ]);
    }
    $first=pl_list_query($f['actor_id'],$f['company_id'],$f['book_id'],'transactions',['per_page'=>'25','q'=>'List fixture']);
    $last=pl_list_query($f['actor_id'],$f['company_id'],$f['book_id'],'transactions',['per_page'=>'25','q'=>'List fixture','page'=>'100000']);
    assert_same(26,$first['total']); assert_same(25,count($first['documents'])); assert_same(2,$last['page']); assert_same(1,count($last['documents']));
    assert_same([],array_values(array_intersect(array_column($first['documents'],'id'),array_column($last['documents'],'id'))));
    $empty=pl_list_query($f['actor_id'],$f['company_id'],$f['book_id'],'transactions',['q'=>'not found','page'=>'3']);
    assert_same(1,$empty['page']); assert_same(0,$empty['total']);
    $other=ledger_fixture();
    assert_throws(fn()=>pl_list_query($other['actor_id'],$f['company_id'],$f['book_id'],'transactions',[]),DomainException::class);
});

test('journal status and text filters use the effective source and reconcile page totals', function (): void {
    $f=ledger_fixture();
    $draft=pl_save_general_draft($f['actor_id'],$f['company_id'],$f['book_id'],[
        'date'=>'2026-09-14','reference'=>'SEARCH-JOURNAL','description'=>'Filter journal','creation_key'=>bin2hex(random_bytes(16)),
        'lines'=>[['account_id'=>$f['accounts']['1000'],'debit'=>'12','credit'=>'0','description'=>'Debit'],['account_id'=>$f['accounts']['3000'],'debit'=>'0','credit'=>'12','description'=>'Credit']],
    ]);
    $run=static fn(string $status): array=>pl_list_query($f['actor_id'],$f['company_id'],$f['book_id'],'general-journals',['q'=>'SEARCH-JOURNAL','status'=>$status]);
    assert_same(1,$run('draft')['total']); assert_same(0,$run('posted')['total']);
    pl_post_general_draft($f['actor_id'],$f['company_id'],$f['book_id'],$draft['id'],$draft['revision']);
    assert_same(0,$run('draft')['total']); assert_same(1,$run('posted')['total']);
});

test('account report return context keeps original dates and rejects arbitrary destinations', function (): void {
    $filters=pl_list_filters(['return_report'=>'profit-loss','from'=>'2026-01-01','as_of'=>'2026-09-17','return_preset'=>'year'],'account');
    assert_same('2026-01-01',$filters['return_from']); assert_same('2026-09-17',$filters['return_to']);
    $next=pl_list_filters($filters+['from'=>'2026-09-01','as_of'=>'2026-09-18'],'account');
    assert_same('2026-01-01',$next['return_from']); assert_same('2026-09-17',$next['return_to']);
    foreach ([['return_report'=>'https://example.invalid'],['return_report'=>['profit-loss']],['return_report'=>'profit-loss','return_to'=>[]],['return_report'=>'profit-loss','return_from'=>'2026-09-20','return_to'=>'2026-09-01'],['return_report'=>'profit-loss','return_preset'=>'unknown']] as $input) {
        assert_throws(fn()=>pl_list_filters($input,'account'),DomainException::class);
    }
});

test('bank summary counts every row independently of paged or searched statement views', function (): void {
    $f=ledger_fixture(); $rows=[];
    for ($i=1;$i<=26;$i++) { $rows[]=bank_fixture_row('PAGE-'.$i,'1'); }
    $statement=bank_fixture_import($f,bank_fixture_input($f,$rows,'26'));
    $summary=pl_bank_reconciliation_summary($f['actor_id'],$f['company_id'],$f['book_id'],(int)$statement['id']);
    assert_same(26,$summary['row_count']); assert_same(26,$summary['unmatched_count']);
    $page=pl_list_query($f['actor_id'],$f['company_id'],$f['book_id'],'bank',['statement_id'=>$statement['id'],'page'=>'2']);
    assert_same(1,count($page['rows']));
    $journal=pl_post_journal($f['actor_id'],$f['company_id'],$f['book_id'],ledger_payload($f,'1'));
    $candidates=pl_bank_candidates($f['actor_id'],$f['company_id'],$f['book_id'],(int)$statement['id'],(int)$statement['rows'][0]['id']);
    $matched=pl_bank_match_row($f['actor_id'],$f['company_id'],$f['book_id'],(int)$statement['id'],(int)$statement['rows'][0]['id'],(int)$candidates[0]['id'],1);
    $summary=pl_bank_reconciliation_summary($f['actor_id'],$f['company_id'],$f['book_id'],(int)$statement['id']);
    assert_same(26,$summary['row_count']); assert_same(25,$summary['unmatched_count']);
    assert_throws(fn()=>pl_bank_cancel_statement($f['actor_id'],$f['company_id'],$f['book_id'],(int)$statement['id'],(int)$matched['revision'],'Synthetic correction','paged-cancel'),DomainException::class);
});
