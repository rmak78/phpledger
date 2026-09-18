<?php
declare(strict_types=1);

test('correction preview is read only and reviewed replacement is atomic and retry safe',function():void {
    foreach (['invoice','bill'] as $kind) {
        $f=ar_ap_fixture(); $args=[$f['actor_id'],$f['company_id'],$f['book_id']];
        $input=ar_ap_input($f,$kind); $draft=pl_save_ar_document(...array_merge($args,[$input]));
        $posted=pl_post_ar_document(...array_merge($args,[$draft['id'],1]));
        $input['date']=gmdate('Y-m-d'); $input['due_date']=$input['date']; $input['lines'][0]['unit_price']='1200.1234';
        $preview=pl_preview_ar_correction(...array_merge($args,[$posted['id'],$input,1,'Reviewed correction']));
        assert_same(1,(int)DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id=%i',$f['book_id']));
        assert_same(1,pl_get_ar_document(...array_merge($args,[$posted['id']]))['revision']);
        assert_same('1200.1234',$preview['document']['total']);
        $hash=hash('sha256',json_encode($preview,JSON_THROW_ON_ERROR)); $key=bin2hex(random_bytes(16));
        $changed=$input; $changed['lines'][0]['unit_price']='1300';
        assert_throws(fn()=>pl_correct_ar_document(...array_merge($args,[$posted['id'],$changed,1,$key,'Reviewed correction',null,null,$hash])),DomainException::class,'Update the preview');
        assert_same(1,(int)DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id=%i',$f['book_id']));
        $call=array_merge($args,[$posted['id'],$input,1,$key,'Reviewed correction',null,null,$hash]);
        $corrected=pl_correct_ar_document(...$call); assert_same($corrected,pl_correct_ar_document(...$call));
        assert_same(2,$corrected['revision']); assert_same(3,(int)DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id=%i',$f['book_id']));
        $journal=pl_get_journal(...array_merge($args,[$corrected['journal_id']]));
        assert_same(array_column($preview['lines'],'debit'),array_column($journal['lines'],'debit'));
        assert_same(array_column($preview['lines'],'credit'),array_column($journal['lines'],'credit'));
        assert_throws(fn()=>pl_preview_ar_correction(...array_merge($args,[$posted['id'],$input,1,'Stale revision'])),DomainException::class);
    }
});

test('stock correction previews restore the old issue before costing replacement and credit returns',function():void {
    $f=inventory_fixture(); $args=[$f['actor_id'],$f['company_id'],$f['book_id']];
    $party=pl_save_party(...array_merge($args,[['legal_name'=>'Synthetic correction buyer','entity_type'=>'private_company','country_code'=>'GB','is_customer'=>true,'is_vendor'=>true,'currency'=>'USD','request_key'=>bin2hex(random_bytes(16)),'reason'=>'Correction fixture']]));
    $f['party_id']=$party['id'];
    pl_inventory_receive(...array_merge($args,[inventory_move_input($f,'3','10')]));
    $input=ar_ap_input($f,'invoice','20'); $input['date']='2026-01-06'; $input['lines'][0]['quantity']='3'; $input['lines'][0]['product_id']=$f['product_id'];
    $draft=pl_save_ar_document(...array_merge($args,[$input])); $posted=pl_post_ar_document(...array_merge($args,[$draft['id'],1]));
    $input['date']=gmdate('Y-m-d'); $input['due_date']=$input['date']; $input['lines'][0]['unit_price']='21';
    $preview=pl_preview_ar_correction(...array_merge($args,[$posted['id'],$input,1,'Correct full stock issue']));
    assert_same('10.0000',$preview['stock_reversal'][0]['value_delta']); assert_same('-10.0000',$preview['stock'][0]['value_delta']);
    assert_same('0.0000',pl_inventory_balance(...array_merge($args,[$f['product_id']]))['quantity']);
    $posted=pl_correct_ar_document(...array_merge($args,[$posted['id'],$input,1,bin2hex(random_bytes(16)),'Correct full stock issue',null,null,hash('sha256',json_encode($preview,JSON_THROW_ON_ERROR))]));
    $credit=ar_ap_input($f,'customer_credit','21'); $credit['date']=$input['date']; $credit['due_date']=$input['date']; $credit['original_document_id']=$posted['id']; $credit['lines'][0]['product_id']=$f['product_id']; $credit['lines'][0]['quantity']='3';
    $draft=pl_save_ar_document(...array_merge($args,[$credit])); $credited=pl_post_ar_document(...array_merge($args,[$draft['id'],1]));
    $credit['lines'][0]['quantity']='2';
    $plan=pl_preview_ar_correction(...array_merge($args,[$credited['id'],$credit,1,'Correct returned quantity']));
    assert_same('-10.0000',$plan['stock_reversal'][0]['value_delta']); assert_same('6.6667',$plan['stock'][0]['value_delta']);
    assert_same('3.0000',pl_inventory_balance(...array_merge($args,[$f['product_id']]))['quantity']);
    $corrected=pl_correct_ar_document(...array_merge($args,[$credited['id'],$credit,1,bin2hex(random_bytes(16)),'Correct returned quantity',null,null,hash('sha256',json_encode($plan,JSON_THROW_ON_ERROR))]));
    assert_same(2,$corrected['revision']); assert_same('2.0000',pl_inventory_balance(...array_merge($args,[$f['product_id']]))['quantity']);
    assert_same('6.6667',pl_inventory_balance(...array_merge($args,[$f['product_id']]))['value_base']);
});

test('correction preview rejects allocations closed periods and foreign access without writing',function():void {
    $f=ar_ap_fixture(); $args=[$f['actor_id'],$f['company_id'],$f['book_id']]; $input=ar_ap_input($f);
    $draft=pl_save_ar_document(...array_merge($args,[$input])); $posted=pl_post_ar_document(...array_merge($args,[$draft['id'],1]));
    $input['date']=gmdate('Y-m-d'); $input['due_date']=$input['date'];
    $other=ar_ap_fixture();
    assert_throws(fn()=>pl_preview_ar_correction($other['actor_id'],$f['company_id'],$f['book_id'],$posted['id'],$input,1,'Wrong actor'),DomainException::class);
    DB::update('pl_periods',['status'=>'closed'],'book_id=%i',$f['book_id']);
    assert_throws(fn()=>pl_preview_ar_correction(...array_merge($args,[$posted['id'],$input,1,'Closed period'])),DomainException::class,'open accounting periods');
    DB::update('pl_periods',['status'=>'open'],'book_id=%i',$f['book_id']);
    pl_settle_ar_document(...array_merge($args,[$posted['id'],ar_ap_payment($f,'100','2026-01-06')]));
    assert_throws(fn()=>pl_preview_ar_correction(...array_merge($args,[$posted['id'],$input,1,'Allocated original'])),DomainException::class,'allocations');
    assert_same(1,pl_get_ar_document(...array_merge($args,[$posted['id']]))['revision']);
});

test('foreign supplier credit correction restores its carrying allocation for review',function():void {
    $f=ar_ap_fixture(); $args=[$f['actor_id'],$f['company_id'],$f['book_id']];
    $input=ar_ap_input($f,'bill','100'); $input['currency']='EUR';
    $draft=pl_save_ar_document(...array_merge($args,[$input])); $bill=pl_post_ar_document(...array_merge($args,[$draft['id'],1,null,'2']));
    $credit=ar_ap_input($f,'supplier_credit','100'); $credit['currency']='EUR'; $credit['original_document_id']=$bill['id'];
    $draft=pl_save_ar_document(...array_merge($args,[$credit])); $posted=pl_post_ar_document(...array_merge($args,[$draft['id'],1]));
    $credit['date']=gmdate('Y-m-d'); $credit['due_date']=$credit['date']; $credit['lines'][0]['unit_price']='50';
    $plan=pl_preview_ar_correction(...array_merge($args,[$posted['id'],$credit,1,'Correct foreign credit']));
    assert_same('100.0000',$plan['lines'][0]['debit']); assert_same('200.0000',$plan['reversal']['lines'][0]['credit']);
    pl_correct_ar_document(...array_merge($args,[$posted['id'],$credit,1,bin2hex(random_bytes(16)),'Correct foreign credit',null,null,hash('sha256',json_encode($plan,JSON_THROW_ON_ERROR))]));
    $after=pl_get_ar_document(...array_merge($args,[$bill['id']])); assert_same('50.0000',$after['outstanding_fc']); assert_same('100.0000',$after['outstanding_base']);
});

test('stock document previews consume exact sequential issue and historical return residuals', function (): void {
    $f=inventory_fixture();
    $party=pl_save_party($f['actor_id'],$f['company_id'],$f['book_id'],['legal_name'=>'Synthetic stock preview customer','entity_type'=>'private_company','country_code'=>'GB','is_customer'=>true,'is_vendor'=>true,'currency'=>'USD','request_key'=>bin2hex(random_bytes(16)),'reason'=>'Synthetic stock preview']);
    $f['party_id']=$party['id'];
    pl_inventory_receive($f['actor_id'],$f['company_id'],$f['book_id'],inventory_move_input($f,'3','10'));
    $input=ar_ap_input($f,'invoice','20'); $input['date']='2026-01-06'; $input['lines'][0]['product_id']=$f['product_id'];
    $input['lines'][]=$input['lines'][0]; $input['lines'][1]['quantity']='2';
    $plan=pl_preview_ar_document($f['actor_id'],$f['company_id'],$f['book_id'],$input);
    assert_same('-3.3333',$plan['stock'][0]['value_delta']); assert_same('-6.6667',$plan['stock'][1]['value_delta']);
    assert_same('3.0000',pl_inventory_balance($f['actor_id'],$f['company_id'],$f['book_id'],$f['product_id'])['quantity']);
    $posted=pl_save_and_post_ar_document($f['actor_id'],$f['company_id'],$f['book_id'],$input,hash('sha256',json_encode($plan,JSON_THROW_ON_ERROR)));
    $issues=DB::query('SELECT value_delta FROM pl_inventory_movements WHERE source_journal_id=%i ORDER BY id',$posted['journal_id']);
    assert_same(array_column($plan['stock'],'value_delta'),array_column($issues,'value_delta'));
    $credit=ar_ap_input($f,'customer_credit','20'); $credit['date']='2026-01-07'; $credit['original_document_id']=$posted['id'];
    $credit['lines'][0]['product_id']=$f['product_id']; $credit['lines'][0]['quantity']='2';
    $review=pl_preview_ar_document($f['actor_id'],$f['company_id'],$f['book_id'],$credit);
    assert_same('3.3333',$review['stock'][0]['value_delta']); assert_same('3.3334',$review['stock'][1]['value_delta']);
    $returned=pl_save_and_post_ar_document($f['actor_id'],$f['company_id'],$f['book_id'],$credit,hash('sha256',json_encode($review,JSON_THROW_ON_ERROR)));
    $returns=DB::query('SELECT value_delta FROM pl_inventory_movements WHERE source_journal_id=%i ORDER BY id',$returned['journal_id']);
    assert_same(array_column($review['stock'],'value_delta'),array_column($returns,'value_delta'));
    assert_same('0.0000',pl_inventory_valuation($f['actor_id'],$f['company_id'],$f['book_id'])['accounts'][0]['difference']);
});

test('stock editor confirmation rejects a changed cost basis without saving a draft', function (): void {
    $f=inventory_fixture();
    $party=pl_save_party($f['actor_id'],$f['company_id'],$f['book_id'],['legal_name'=>'Synthetic stale stock customer','entity_type'=>'private_company','country_code'=>'GB','is_customer'=>true,'is_vendor'=>false,'currency'=>'USD','request_key'=>bin2hex(random_bytes(16)),'reason'=>'Synthetic stale stock preview']);
    $f['party_id']=$party['id'];
    pl_inventory_receive($f['actor_id'],$f['company_id'],$f['book_id'],inventory_move_input($f,'3','10'));
    $input=ar_ap_input($f,'invoice','20'); $input['date']='2026-01-06'; $input['lines'][0]['product_id']=$f['product_id'];
    $plan=pl_preview_ar_document($f['actor_id'],$f['company_id'],$f['book_id'],$input);
    pl_inventory_receive($f['actor_id'],$f['company_id'],$f['book_id'],inventory_move_input($f,'1','20'));
    assert_throws(fn()=>pl_save_and_post_ar_document($f['actor_id'],$f['company_id'],$f['book_id'],$input,hash('sha256',json_encode($plan,JSON_THROW_ON_ERROR))),DomainException::class,'Update the preview');
    assert_same(0,(int)DB::queryFirstField('SELECT COUNT(*) FROM pl_ar_documents WHERE book_id=%i',$f['book_id']));
    assert_same('4.0000',pl_inventory_balance($f['actor_id'],$f['company_id'],$f['book_id'],$f['product_id'])['quantity']);
});

test('invoice and bill editor preview writes nothing and shares exact financial lines with posting', function (): void {
    foreach (['invoice','bill'] as $kind) {
        $f=ar_ap_fixture(); $input=ar_ap_input($f,$kind,'12.3456');
        $plan=pl_preview_ar_document($f['actor_id'],$f['company_id'],$f['book_id'],$input);
        foreach (['pl_ar_documents','pl_open_items','pl_open_item_accounts','pl_journals'] as $table) {
            assert_same(0,(int)DB::queryFirstField('SELECT COUNT(*) FROM %b WHERE book_id=%i',$table,$f['book_id']));
        }
        $hash=hash('sha256',json_encode($plan,JSON_THROW_ON_ERROR));
        $posted=pl_save_and_post_ar_document($f['actor_id'],$f['company_id'],$f['book_id'],$input,$hash);
        assert_same($posted,pl_save_and_post_ar_document($f['actor_id'],$f['company_id'],$f['book_id'],$input,''));
        $journal=pl_get_journal($f['actor_id'],$f['company_id'],$f['book_id'],$posted['journal_id']);
        foreach ($plan['lines'] as $index=>$line) {
            foreach (['debit','credit','amount_fc','amount_base','rate'] as $field) { assert_same($line[$field],$journal['lines'][$index][$field]); }
        }
    }
});

test('credit editor preview retains original tax residual and carrying value', function (): void {
    $f=ar_ap_fixture(); $invoice=pl_save_ar_document($f['actor_id'],$f['company_id'],$f['book_id'],ar_ap_input($f,'invoice','100'));
    $invoice=pl_post_ar_document($f['actor_id'],$f['company_id'],$f['book_id'],$invoice['id'],$invoice['revision']);
    $input=ar_ap_input($f,'customer_credit','25'); $input['original_document_id']=$invoice['id']; $input['lines'][0]['original_line_number']=1;
    $plan=pl_preview_ar_document($f['actor_id'],$f['company_id'],$f['book_id'],$input);
    assert_same('25.0000',$plan['lines'][0]['credit']);
    $credit=pl_save_and_post_ar_document($f['actor_id'],$f['company_id'],$f['book_id'],$input,hash('sha256',json_encode($plan,JSON_THROW_ON_ERROR)));
    assert_same('75.0000',pl_get_ar_document($f['actor_id'],$f['company_id'],$f['book_id'],$invoice['id'])['outstanding_fc']);
    assert_same('25.0000',$credit['total']);
});

test('editor rejects unreviewed changes and closed-period posting rolls back draft and control activation', function (): void {
    $f=ar_ap_fixture(); $input=ar_ap_input($f);
    $plan=pl_preview_ar_document($f['actor_id'],$f['company_id'],$f['book_id'],$input); $hash=hash('sha256',json_encode($plan,JSON_THROW_ON_ERROR));
    $changed=$input; $changed['lines'][0]['unit_price']='200';
    assert_throws(fn()=>pl_save_and_post_ar_document($f['actor_id'],$f['company_id'],$f['book_id'],$changed,$hash),DomainException::class,'Update the preview');
    DB::update('pl_periods',['status'=>'closed'],'id=%i',$f['period_id']);
    assert_throws(fn()=>pl_save_and_post_ar_document($f['actor_id'],$f['company_id'],$f['book_id'],$input,$hash),DomainException::class,'open accounting period');
    foreach (['pl_ar_documents','pl_open_items','pl_open_item_accounts','pl_journals'] as $table) {
        assert_same(0,(int)DB::queryFirstField('SELECT COUNT(*) FROM %b WHERE book_id=%i',$table,$f['book_id']));
    }
});
