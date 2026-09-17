<?php
declare(strict_types=1);

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
