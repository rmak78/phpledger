<?php
declare(strict_types=1);

test('goods receipt preview shares exact stock and GRNI amounts with posting and remains read-only', function (): void {
    $f=purchasing_fixture(); $order=purchasing_order($f,'10','1.2345');
    $input=purchasing_receipt_input($f,$order,'3');
    $plan=pl_preview_purchase_receipt($f['actor_id'],$f['company_id'],$f['book_id'],$order['id'],$input);
    assert_same('3.7035',$plan['total_base']); assert_same('7.0000',$plan['lines'][0]['remaining_after']);
    assert_same(0,(int)DB::queryFirstField('SELECT COUNT(*) FROM pl_purchase_receipts WHERE book_id=%i',$f['book_id']));
    assert_same(0,(int)DB::queryFirstField('SELECT COUNT(*) FROM pl_inventory_movements WHERE book_id=%i',$f['book_id']));
    assert_same(0,(int)DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id=%i',$f['book_id']));
    $hash=hash('sha256',json_encode($plan,JSON_THROW_ON_ERROR));
    $posted=pl_confirm_purchase_receipt($f['actor_id'],$f['company_id'],$f['book_id'],$order['id'],$input,$hash);
    assert_same($plan['total_base'],$posted['lines'][0]['amount_base']);
    assert_true($posted==pl_confirm_purchase_receipt($f['actor_id'],$f['company_id'],$f['book_id'],$order['id'],$input,$hash));
    $journal=pl_get_journal($f['actor_id'],$f['company_id'],$f['book_id'],$posted['lines'][0]['journal_id']);
    assert_same('3.7035',$journal['lines'][0]['debit']); assert_same('3.7035',$journal['lines'][1]['credit']);
    assert_same('0.0000',pl_purchase_received_unbilled($f['actor_id'],$f['company_id'],$f['book_id'])['accounts'][0]['difference']);
});

test('goods receipt confirmation rejects changed remaining quantities and preview retains scope guards', function (): void {
    $f=purchasing_fixture(); $order=purchasing_order($f); $input=purchasing_receipt_input($f,$order,'2');
    $plan=pl_preview_purchase_receipt($f['actor_id'],$f['company_id'],$f['book_id'],$order['id'],$input);
    pl_receive_purchase_order($f['actor_id'],$f['company_id'],$f['book_id'],$order['id'],purchasing_receipt_input($f,$order,'1'));
    assert_throws(fn()=>pl_confirm_purchase_receipt($f['actor_id'],$f['company_id'],$f['book_id'],$order['id'],$input,hash('sha256',json_encode($plan,JSON_THROW_ON_ERROR))),DomainException::class,'Update the preview');
    $bad=$input; $bad['lines'][0]['quantity']='10';
    assert_throws(fn()=>pl_preview_purchase_receipt($f['actor_id'],$f['company_id'],$f['book_id'],$order['id'],$bad),DomainException::class);
    $bad=$input; $bad['lines'][]=$bad['lines'][0];
    assert_throws(fn()=>pl_preview_purchase_receipt($f['actor_id'],$f['company_id'],$f['book_id'],$order['id'],$bad),DomainException::class,'each order line once');
    $other=ledger_fixture();
    assert_throws(fn()=>pl_preview_purchase_receipt($other['actor_id'],$f['company_id'],$f['book_id'],$order['id'],$input),DomainException::class);
    assert_same(1,(int)DB::queryFirstField('SELECT COUNT(*) FROM pl_purchase_receipts WHERE book_id=%i',$f['book_id']));
});

test('stock count preview writes nothing and confirmation uses the exact reviewed journal effect', function (): void {
    $f=inventory_fixture();
    pl_inventory_receive($f['actor_id'],$f['company_id'],$f['book_id'],inventory_move_input($f,'3','10'));
    $input=inventory_move_input($f,'0','0','2026-01-06')+['counted_quantity'=>'2','expected_quantity'=>'3','unit_cost'=>'0'];
    $plan=pl_preview_inventory_count($f['actor_id'],$f['company_id'],$f['book_id'],$input);
    assert_same('-1.0000',$plan['effect']['quantity_delta']); assert_same('-3.3333',$plan['effect']['value_delta']);
    assert_same(1,(int)DB::queryFirstField('SELECT COUNT(*) FROM pl_inventory_movements WHERE book_id=%i',$f['book_id']));
    assert_same(1,(int)DB::queryFirstField('SELECT COUNT(*) FROM pl_journals WHERE book_id=%i',$f['book_id']));
    $hash=hash('sha256',json_encode($plan,JSON_THROW_ON_ERROR));
    assert_throws(fn()=>pl_confirm_inventory_count($f['actor_id'],$f['company_id'],$f['book_id'],$input,''),DomainException::class,'Update the preview');
    $posted=pl_confirm_inventory_count($f['actor_id'],$f['company_id'],$f['book_id'],$input,$hash);
    assert_same($plan['effect']['value_delta'],$posted['value_delta']);
    assert_true($posted==pl_confirm_inventory_count($f['actor_id'],$f['company_id'],$f['book_id'],$input,$hash));
    $journal=pl_get_journal($f['actor_id'],$f['company_id'],$f['book_id'],$posted['journal_id']);
    foreach ($plan['journal']['lines'] as $index=>$line) {
        assert_same(bcadd($line['debit'],'0',4),$journal['lines'][$index]['debit']);
        assert_same(bcadd($line['credit'],'0',4),$journal['lines'][$index]['credit']);
    }
    assert_same('0.0000',pl_inventory_valuation($f['actor_id'],$f['company_id'],$f['book_id'])['accounts'][0]['difference']);
});

test('count preview binds carrying value and retains count increase and period validation', function (): void {
    $f=inventory_fixture();
    pl_inventory_receive($f['actor_id'],$f['company_id'],$f['book_id'],inventory_move_input($f));
    $input=inventory_move_input($f,'0','0','2026-01-07')+['counted_quantity'=>'11','expected_quantity'=>'10','unit_cost'=>'12'];
    $plan=pl_preview_inventory_count($f['actor_id'],$f['company_id'],$f['book_id'],$input);
    assert_same('12.0000',$plan['effect']['value_delta']);
    $bad=$input; unset($bad['unit_cost']);
    assert_throws(fn()=>pl_preview_inventory_count($f['actor_id'],$f['company_id'],$f['book_id'],$bad),DomainException::class,'positive unit cost');
    $adjust=inventory_move_input($f,'0','5','2026-01-06')+['expected_quantity'=>'10','expected_value_base'=>'100'];
    pl_inventory_value_adjustment($f['actor_id'],$f['company_id'],$f['book_id'],$adjust);
    assert_throws(fn()=>pl_confirm_inventory_count($f['actor_id'],$f['company_id'],$f['book_id'],$input,hash('sha256',json_encode($plan,JSON_THROW_ON_ERROR))),DomainException::class,'Update the preview');
    assert_same('10.0000',pl_inventory_balance($f['actor_id'],$f['company_id'],$f['book_id'],$f['product_id'])['quantity']);
    DB::update('pl_periods',['status'=>'closed'],'id=%i',$f['period_id']);
    assert_throws(fn()=>pl_preview_inventory_count($f['actor_id'],$f['company_id'],$f['book_id'],$input),DomainException::class,'open accounting period');
});
