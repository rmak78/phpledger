<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/www/phpledger/includes/functions/web_functions.php';

test('workflow recovery carries report context through reads writes and demo base paths without accepting redirect URLs', function (): void {
    $context=['id'=>'12','as_of'=>'2026-09-30','from'=>'2026-09-01','page'=>'2','per_page'=>'50','q'=>'Client & September','sort'=>'date','dir'=>'asc',
        'return_report'=>'profit-loss','return_from'=>'2026-09-01','return_to'=>'2026-09-30','return_preset'=>'custom'];
    $expected=pl_web_account_return(['return_account'=>$context]);
    $savedGet=$_GET; $savedPost=$_POST; $savedMethod=$_SERVER['REQUEST_METHOD']??null; $savedBase=getenv('PL_BASE_PATH');
    try {
        $_GET=['return_account'=>$context]; $_POST=['id'=>'7']; $_SERVER['REQUEST_METHOD']='POST'; putenv('PL_BASE_PATH=/demo');
        foreach (['/transactions/edit','/transactions/detail','/general-journals/detail','/journals/detail','/ar','/ap','/purchasing'] as $path) {
            $url=pl_workflow_url($path,['id'=>7]);
            assert_same('/demo'.$path,parse_url($url,PHP_URL_PATH));
            parse_str((string)parse_url($url,PHP_URL_QUERY),$query);
            assert_same($expected,pl_web_account_return($query));
            assert_same('7',$query['id']);
        }
        $_POST['return_account']='https://example.invalid';
        assert_throws(fn()=>pl_workflow_url('/transactions/detail'),DomainException::class);
        foreach (['https://example.invalid','//example.invalid','/logout','/demo/transactions/detail','/transactions/detail?next=outside'] as $path) { assert_throws(fn()=>pl_workflow_url($path,[],[]),DomainException::class); }
        assert_same('/demo/ar?id=7',pl_workflow_url('/ar',['id'=>7],['return_to'=>'https://example.invalid']));
    } finally {
        $_GET=$savedGet; $_POST=$savedPost;
        if ($savedMethod===null) { unset($_SERVER['REQUEST_METHOD']); } else { $_SERVER['REQUEST_METHOD']=$savedMethod; }
        $savedBase===false?putenv('PL_BASE_PATH'):putenv('PL_BASE_PATH='.$savedBase);
    }
});

test('workflow recovery identifies rejected editor fields without coercing money or changing submitted values', function (): void {
    $input=['date'=>'2026-02-31','amount'=>'1,234.00','counterparty'=>'','money_account_id'=>'','category_account_id'=>'','memo'=>'Keep my note'];
    $before=$input; $errors=pl_web_editor_errors($input,'transaction');
    assert_same(['date','counterparty','amount','money_account_id','category_account_id'],array_keys($errors));
    assert_same($before,$input);
    assert_same([],pl_web_editor_errors(['date'=>'2026-09-18','amount'=>'0.0001','counterparty'=>'Sample party','money_account_id'=>'1','category_account_id'=>'2'],'transaction'));
    $journal=['date'=>'2026-09-18','description'=>'Preserved journal','lines'=>[['account_id'=>'1','debit'=>'5','credit'=>'5'],['account_id'=>'','debit'=>'2.12345','credit'=>'']]];
    $errors=pl_web_editor_errors($journal,'journal');
    assert_true(isset($errors['lines.0.debit'],$errors['lines.1.account_id'],$errors['lines.1.debit']));
    $commercial=['date'=>'2026-09-18','due_date'=>'2026-09-17','party_id'=>'1','currency'=>'USD','lines'=>[['description'=>'Work','quantity'=>'1','unit_price'=>'0.0001']]];
    assert_same(['due_date'],array_keys(pl_web_editor_errors($commercial,'ar')));
    $commercial['lines'][0]=['description'=>'','product_id'=>'2','quantity'=>'1','unit_price'=>'0.0001'];
    assert_same([],pl_web_editor_errors($commercial,'purchase'),'Purchase descriptions remain optional, matching the service.');
    $commercial['lines'][0]['description']=str_repeat('x',301);
    assert_same(['lines.0.description'],array_keys(pl_web_editor_errors($commercial,'purchase')));
});

test('workflow recovery retains ageing dates through document and journal actions and rejects foreign destinations', function (): void {
    $source=['direction'=>'payable','as_of'=>'2026-08-31'];
    foreach (['/ap','/journals/detail','/purchasing'] as $path) {
        $url=pl_workflow_url($path,['id'=>10],['return_ageing'=>$source+['url'=>'https://example.invalid']]);
        parse_str((string)parse_url($url,PHP_URL_QUERY),$query);
        assert_same($source,$query['return_ageing']);
        assert_true(!str_contains($url,'example.invalid'));
    }
    assert_same(['direction'=>'receivable','as_of'=>'2026-09-30'],pl_web_ageing_return(['return_report'=>'ageing','as_of'=>'2026-09-30'],'/ar'));
    foreach (['https://example.invalid',['direction'=>'other','as_of'=>'2026-09-30'],['direction'=>'receivable','as_of'=>'invalid']] as $input) { assert_throws(fn()=>pl_web_ageing_return(['return_ageing'=>$input]),DomainException::class); }
});

test('workflow recovery renders linked field errors escaped values and unavailable choices without substitution', function (): void {
    require_once dirname(__DIR__).'/www/phpledger/templates/partials/ui/components.php';
    require_once dirname(__DIR__).'/www/phpledger/includes/functions/starter_web_functions.php';
    require_once dirname(__DIR__).'/www/phpledger/templates/partials/ui/commercial-lines.php';
    $partialLines=pl_starter_lines(['lines'=>[[],['unit_price'=>'12.3401'],['account_id'=>'12']]]);
    assert_same(2,count($partialLines),'Partially entered price/account rows must reach validation instead of disappearing.');
    assert_same('12.3401',$partialLines[0]['unit_price']);
    assert_same(12,$partialLines[1]['account_id']);
    ob_start();
    pl_ui_error_summary(['party_id'=>'Choose a supplier.','lines.0.quantity'=>'Enter a positive quantity.'],['party_id'=>['purchase-party','Supplier'],'lines.0.quantity'=>['commercial-0-quantity','Quantity, line 1']]);
    pl_starter_select('Supplier','party_id',[1=>'Current supplier'],'999',error:'Choose a supplier.',id:'purchase-party');
    pl_ui_commercial_lines([['description'=>'<script>bad()</script>','quantity'=>'wrong','unit_price'=>'4','product_id'=>'999']],['product_id'=>[1=>'Current product']],false,true,['lines.0.quantity'=>'Enter a positive quantity.']);
    $html=(string)ob_get_clean();
    assert_true(str_contains($html,'href="#purchase-party"'));
    assert_true(str_contains($html,'value="999" selected>Unavailable selection'));
    assert_true(str_contains($html,'id="purchase-party" name="party_id" aria-invalid="true" aria-describedby="purchase-party-error"'));
    assert_true(str_contains($html,'id="commercial-0-quantity" aria-invalid="true" aria-describedby="commercial-0-quantity-error"'));
    assert_true(str_contains($html,'value="wrong"'));
    assert_true(str_contains($html,'&lt;script&gt;bad()&lt;/script&gt;'));
    assert_true(!str_contains($html,'<script>bad()'));
});

test('editor return state preserves validated filters and rejects malformed orders', function (): void {
    $filters = ['page'=>2,'per_page'=>50,'q'=>'Sample preview','sort'=>'amount','dir'=>'asc','status'=>'draft','kind'=>'expense','from'=>'2026-09-01','to'=>'2026-09-30'];
    assert_same($filters, pl_return_list_filters(['return_filters'=>$filters], 'transactions'));
    assert_same($filters, pl_return_list_filters(['return_filters'=>$filters + ['redirect'=>'https://example.invalid']], 'transactions'));
    foreach (['invalid', ['sort'=>'amount DESC'], ['q'=>[]], ['page'=>[]], ['dir'=>[]]] as $invalid) {
        assert_throws(fn () => pl_return_list_filters(['return_filters'=>$invalid], 'transactions'), DomainException::class);
    }
});

test('transaction editor preview is read only and matches the posted service payload in both directions', function (): void {
    foreach (['receipt','expense'] as $kind) {
        $f=ledger_fixture();
        $input=['kind'=>$kind,'date'=>'2026-09-17','amount'=>'12.3401','money_account_id'=>$f['accounts']['1000'],'category_account_id'=>$f['accounts'][$kind==='receipt'?'4000':'5000'],'counterparty'=>'Sample preview party','reference'=>'Preview proof','memo'=>'Exact service comparison','creation_key'=>bin2hex(random_bytes(16))];
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
