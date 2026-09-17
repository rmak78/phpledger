<?php
declare(strict_types=1);

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
