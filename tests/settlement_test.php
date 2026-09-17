<?php
declare(strict_types=1);

test('payment confirmation binds financial review and permits the applicable FX account choice', function (): void {
    $f=settlement_fixture(false,true); $input=settlement_input($f)+['actual_rate'=>'282'];
    $plan=pl_preview_settlement($f['actor_id'],$f['company_id'],$f['book_id'],$input);
    $hash=pl_settlement_review_hash($input,$plan);
    assert_throws(fn()=>pl_confirm_settlement($f['actor_id'],$f['company_id'],$f['book_id'],$input,''),DomainException::class,'Update the preview');
    $changed=$input; $changed['description']='Changed reference';
    assert_throws(fn()=>pl_confirm_settlement($f['actor_id'],$f['company_id'],$f['book_id'],$changed,$hash),DomainException::class,'Update the preview');
    $input['gain_account_id']=$f['accounts']['4000'];
    $posted=pl_confirm_settlement($f['actor_id'],$f['company_id'],$f['book_id'],$input,$hash);
    assert_same($posted,pl_confirm_settlement($f['actor_id'],$f['company_id'],$f['book_id'],$input,$hash));
});

test('another payment invalidates reviewed balances even when the allocation still fits', function (): void {
    $f=settlement_fixture(); $input=settlement_input($f); $input['amount_fc']='2';
    foreach ($input['allocations'] as &$row) { $row['amount_fc']='1'; } unset($row);
    $hash=pl_settlement_review_hash($input,pl_preview_settlement($f['actor_id'],$f['company_id'],$f['book_id'],$input));
    $other=$input; $other['idempotency_key']=bin2hex(random_bytes(16));
    pl_settle_open_items($f['actor_id'],$f['company_id'],$f['book_id'],$other);
    assert_throws(fn()=>pl_confirm_settlement($f['actor_id'],$f['company_id'],$f['book_id'],$input,$hash),DomainException::class,'Update the preview');
    assert_same(1,(int)DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id=%i AND source_type=%s',$f['book_id'],'open_item_batch_settlement'));
});

function settlement_fixture(bool $payable=false, bool $foreign=false): array
{
    $f=open_item_fixture($payable); $items=[];
    foreach (['280','282'] as $rate) {
        $input=open_item_recognition_input($f,'10',$foreign?$rate:'1');
        if (!$foreign) { $input['currency']='PKR'; }
        $items[]=pl_open_item_recognize($f['actor_id'],$f['company_id'],$f['book_id'],$input)['item_id'];
    }
    return $f+['items'=>$items];
}

function settlement_input(array $f): array
{
    return ['party_id'=>$f['party_id'],'direction'=>$f['payable']?'payable':'receivable','date'=>'2026-03-01',
        'bank_account_id'=>$f['accounts']['1000'],'amount_fc'=>'20','description'=>'Synthetic multi-document payment',
        'idempotency_key'=>bin2hex(random_bytes(16)),'allocations'=>array_map(static fn(int $id): array=>['item_id'=>$id,'amount_fc'=>'10'],$f['items'])];
}

test('multi-item domestic settlement previews without writes and posts one bank line with retry and reversal', function (): void {
    foreach ([false,true] as $payable) {
        $f=settlement_fixture($payable); $input=settlement_input($f);
        $preview=pl_preview_settlement($f['actor_id'],$f['company_id'],$f['book_id'],$input);
        assert_same(null,$preview['fx_kind']); assert_same('20.0000',$preview['settlement_base']);
        assert_same(2,(int)DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id=%i',$f['book_id']));
        $posted=pl_settle_open_items($f['actor_id'],$f['company_id'],$f['book_id'],$input);
        $journal=pl_get_journal($f['actor_id'],$f['company_id'],$f['book_id'],$posted['journal_id']);
        assert_same(3,count($journal['lines']));
        assert_same(1,count(array_filter($journal['lines'],static fn(array $line): bool=>(int)$line['account_id']===$f['accounts']['1000'])));
        foreach ($f['items'] as $id) { assert_same('0.0000',pl_get_open_item($f['actor_id'],$f['company_id'],$f['book_id'],$id)['remaining_fc']); }
        $input['allocations']=array_reverse($input['allocations']);
        assert_same($posted,pl_settle_open_items($f['actor_id'],$f['company_id'],$f['book_id'],$input));
        pl_reverse_journal($f['actor_id'],$f['company_id'],$f['book_id'],$posted['journal_id'],gmdate('Y-m-d'),bin2hex(random_bytes(16)),'Synthetic whole-payment reversal');
        foreach ($f['items'] as $id) { assert_same('10.0000',pl_get_open_item($f['actor_id'],$f['company_id'],$f['book_id'],$id)['remaining_fc']); }
        assert_same($posted,pl_settle_open_items($f['actor_id'],$f['company_id'],$f['book_id'],$input));
    }
});

test('multi-item invalid allocation rejects the whole payment and never guesses a remainder', function (): void {
    $f=settlement_fixture(); $input=settlement_input($f); $other=settlement_fixture();
    $bad=$input; $bad['allocations'][1]['amount_fc']='11'; $bad['amount_fc']='21';
    $duplicate=$input; $duplicate['allocations'][1]=$duplicate['allocations'][0];
    $cross=$input; $cross['allocations'][1]['item_id']=$other['items'][0];
    foreach ([$bad,$duplicate,$cross,array_replace($input,['amount_fc'=>'20.0001']),array_replace($input,['party_id'=>$other['party_id']])] as $invalid) {
        assert_throws(fn()=>pl_settle_open_items($f['actor_id'],$f['company_id'],$f['book_id'],$invalid),DomainException::class);
    }
    assert_same(2,(int)DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id=%i',$f['book_id']));
    foreach ($f['items'] as $id) { assert_same('10.0000',pl_get_open_item($f['actor_id'],$f['company_id'],$f['book_id'],$id)['remaining_fc']); }
    assert_throws(fn()=>pl_settle_open_items($other['actor_id'],$f['company_id'],$f['book_id'],$input),DomainException::class);
});

test('foreign batch netting to zero needs no FX account and a difference requires only its applicable side', function (): void {
    foreach ([false,true] as $payable) {
        foreach (['281','282'] as $rate) {
            $f=settlement_fixture($payable,true); $input=settlement_input($f)+['actual_rate'=>$rate];
            $plan=pl_preview_settlement($f['actor_id'],$f['company_id'],$f['book_id'],$input);
            assert_same($rate==='281'?null:($payable?'loss':'gain'),$plan['fx_kind']);
            if ($rate==='282') {
                assert_throws(fn()=>pl_settle_open_items($f['actor_id'],$f['company_id'],$f['book_id'],$input),DomainException::class,'calculated exchange difference');
                $input[$payable?'loss_account_id':'gain_account_id']=$f['accounts'][$payable?'5000':'4000'];
            }
            $posted=pl_settle_open_items($f['actor_id'],$f['company_id'],$f['book_id'],$input);
            assert_same('5620.0000',$posted['allocated_base']);
            assert_same($rate==='281'?'5620.0000':'5640.0000',$posted['settlement_base']);
            assert_true(pl_trial_balance($f['actor_id'],$f['company_id'],$f['book_id'])['balanced']);
        }
    }
});

test('concurrent identical multi-item requests settle once', function (): void {
    $f=settlement_fixture(); $input=settlement_input($f);
    $job=['mode'=>'open_items_settle','fixture'=>$f,'settlement_input'=>$input];
    $results=ledger_race([$job,$job]);
    assert_true((int)$results[0]['id'] > 0);
    assert_true((int)$results[1]['id'] > 0);
    assert_same($results[0]['id'],$results[1]['id']);
    assert_same(1,(int)DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id=%i AND source_type=%s',$f['book_id'],'open_item_batch_settlement'));
});

test('partial batch allocations keep exact residuals and a closed period rolls back the whole payment', function (): void {
    $f=settlement_fixture(); $input=settlement_input($f); $input['amount_fc']='2.4690';
    foreach ($input['allocations'] as &$row) { $row['amount_fc']='1.2345'; } unset($row);
    $posted=pl_settle_open_items($f['actor_id'],$f['company_id'],$f['book_id'],$input);
    foreach ($f['items'] as $id) { assert_same('8.7655',pl_get_open_item($f['actor_id'],$f['company_id'],$f['book_id'],$id)['remaining_fc']); }
    $input['idempotency_key']=bin2hex(random_bytes(16));
    DB::update('pl_periods',['status'=>'closed'],'id=%i',$f['period_id']);
    assert_throws(fn()=>pl_settle_open_items($f['actor_id'],$f['company_id'],$f['book_id'],$input),DomainException::class,'open accounting period');
    assert_same(1,(int)DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id=%i AND source_type=%s',$f['book_id'],'open_item_batch_settlement'));
    foreach ($f['items'] as $id) { assert_same('8.7655',pl_get_open_item($f['actor_id'],$f['company_id'],$f['book_id'],$id)['remaining_fc']); }
    $journal=pl_get_journal($f['actor_id'],$f['company_id'],$f['book_id'],$posted['journal_id']);
    foreach ($journal['lines'] as &$line) { $line['account_id']=(int)$line['account_id']; } unset($line);
    assert_throws(fn()=>pl_post_journal($f['actor_id'],$f['company_id'],$f['book_id'],['date'=>'2026-03-01','currency'=>'PKR','source_type'=>'open_item_batch_settlement','source_reference'=>'open-item-batch:'.str_repeat('a',64),'idempotency_key'=>'forged-batch','description'=>'Must use settlement service','lines'=>$journal['lines']]),DomainException::class,'authoritative open-item');
});
