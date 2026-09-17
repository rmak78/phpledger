<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/www/phpledger/includes/functions/web_functions.php';

test('editor return state preserves validated filters and rejects malformed orders', function (): void {
    $filters = ['page'=>2,'per_page'=>50,'q'=>'Synthetic preview','sort'=>'amount','dir'=>'asc','status'=>'draft','kind'=>'expense','from'=>'2026-09-01','to'=>'2026-09-30'];
    assert_same($filters, pl_return_list_filters(['return_filters'=>$filters], 'transactions'));
    assert_same($filters, pl_return_list_filters(['return_filters'=>$filters + ['redirect'=>'https://example.invalid']], 'transactions'));
    foreach (['invalid', ['sort'=>'amount DESC'], ['q'=>[]], ['page'=>[]], ['dir'=>[]]] as $invalid) {
        assert_throws(fn () => pl_return_list_filters(['return_filters'=>$invalid], 'transactions'), DomainException::class);
    }
});

test('transaction editor preview is read only and matches the posted service payload in both directions', function (): void {
    foreach (['receipt','expense'] as $kind) {
        $f=ledger_fixture();
        $input=['kind'=>$kind,'date'=>'2026-09-17','amount'=>'12.3401','money_account_id'=>$f['accounts']['1000'],'category_account_id'=>$f['accounts'][$kind==='receipt'?'4000':'5000'],'counterparty'=>'Synthetic preview party','reference'=>'Preview proof','memo'=>'Exact service comparison','creation_key'=>bin2hex(random_bytes(16))];
        $preview=pl_preview_document($f['actor_id'],$f['company_id'],$f['book_id'],$input);
        assert_same(0,(int)DB::queryFirstField('SELECT COUNT(*) FROM pl_documents WHERE book_id=%i',$f['book_id']));
        assert_same(0,(int)DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id=%i',$f['book_id']));
        $draft=pl_save_document($f['actor_id'],$f['company_id'],$f['book_id'],$input);
        $posted=pl_post_document($f['actor_id'],$f['company_id'],$f['book_id'],$draft['id'],$draft['revision']);
        foreach ($preview['lines'] as $line) {
            $actual=array_values(array_filter($posted['journal']['lines'],static fn(array $row): bool=>(int)$row['account_id']===$line['account_id']))[0];
            assert_same($line['debit'],$actual['debit']); assert_same($line['credit'],$actual['credit']);
        }
        $other=ledger_fixture();
        assert_throws(fn()=>pl_preview_document($other['actor_id'],$f['company_id'],$f['book_id'],$input),DomainException::class);
        $input['category_account_id']=$other['accounts'][$kind==='receipt'?'4000':'5000'];
        assert_throws(fn()=>pl_preview_document($f['actor_id'],$f['company_id'],$f['book_id'],$input),DomainException::class);
    }
});

test('server journal line controls retain incomplete values and enforce the line limit', function (): void {
    $input = ['description'=>'Keep this unfinished entry', 'date'=>'', 'lines'=>[['account_id'=>'','debit'=>'12.3','description'=>'Unfinished']], 'editor_action'=>'add_line'];
    $result = pl_web_journal_line_action($input);
    assert_same(2, count($result['lines']));
    assert_same($input['lines'][0], $result['lines'][0]);
    assert_same($input['description'], $result['description']);
    $result['remove_line']='0';
    assert_same([[]], pl_web_journal_line_action($result)['lines']);
    assert_throws(fn () => pl_web_journal_line_action(['lines'=>array_fill(0,100,[]),'editor_action'=>'add_line']), DomainException::class);
    assert_throws(fn () => pl_web_journal_line_action(['lines'=>[[]],'remove_line'=>'-1']), DomainException::class);
    assert_throws(fn () => pl_web_journal_line_action(['lines'=>['invalid'],'editor_action'=>'add_line']), DomainException::class);
});

test('editor posting saves and posts once and rolls back rejected unbalanced work', function (): void {
    $f = ledger_fixture(); $input = core_general_input($f);
    $broken = $input; $broken['lines'][1]['credit']='1';
    assert_throws(fn () => pl_save_and_post_general_draft($f['actor_id'],$f['company_id'],$f['book_id'],$broken), DomainException::class);
    assert_same(0, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_general_drafts WHERE book_id=%i',$f['book_id']));
    assert_same(0, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id=%i',$f['book_id']));
    $posted = pl_save_and_post_general_draft($f['actor_id'],$f['company_id'],$f['book_id'],$input);
    assert_same('posted',$posted['status']);
    assert_same($posted['journal_id'],pl_save_and_post_general_draft($f['actor_id'],$f['company_id'],$f['book_id'],$input)['journal_id']);
    assert_same(1, (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id=%i',$f['book_id']));
    $other=ledger_fixture();
    assert_throws(fn () => pl_save_and_post_general_draft($other['actor_id'],$f['company_id'],$f['book_id'],$input), DomainException::class);
});

test('rejected editor posting preserves the previously saved draft and revision', function (): void {
    $f=ledger_fixture(); $input=core_general_input($f);
    $draft=pl_save_general_draft($f['actor_id'],$f['company_id'],$f['book_id'],$input);
    $input['description']='Unsaved change'; $input['lines'][1]['credit']='1';
    assert_throws(fn () => pl_save_and_post_general_draft($f['actor_id'],$f['company_id'],$f['book_id'],$input,$draft['id'],$draft['revision']), DomainException::class);
    $after=pl_get_general_draft($f['actor_id'],$f['company_id'],$f['book_id'],$draft['id']);
    assert_same($draft['description'],$after['description']);
    assert_same($draft['revision'],$after['revision']);
    assert_same(null,$after['journal_id']);
});
