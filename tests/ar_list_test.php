<?php declare(strict_types=1);
require_once dirname(__DIR__).'/www/phpledger/includes/functions/web_functions.php';

test('customer and supplier lists page and filter the effective immutable revision',function(): void {
    $f=ar_ap_fixture(); $first=null;
    for ($i=0;$i<26;$i++) {
        $draft=pl_save_ar_document($f['actor_id'],$f['company_id'],$f['book_id'],ar_ap_input($f,'invoice',(string)($i+1)));
        $first??=$draft;
    }
    $run=fn(array $filters): array=>pl_list_query($f['actor_id'],$f['company_id'],$f['book_id'],'ar',$filters);
    $page=$run(['sort'=>'amount','dir'=>'asc']); $last=$run(['sort'=>'amount','dir'=>'asc','page'=>'999']);
    assert_same(26,$page['total']); assert_same(25,count($page['documents'])); assert_same(2,$last['page']); assert_same(1,count($last['documents']));
    assert_same([],array_values(array_intersect(array_column($page['documents'],'id'),array_column($last['documents'],'id'))));
    assert_same('1.0000',$page['documents'][0]['total']); assert_same('26.0000',$last['documents'][0]['total']);
    $posted=pl_post_ar_document($f['actor_id'],$f['company_id'],$f['book_id'],$first['id'],$first['revision']);
    $input=ar_ap_input($f,'invoice','75'); $input['date']='2026-02-05'; $input['due_date']='2026-03-05';
    $corrected=pl_correct_ar_document($f['actor_id'],$f['company_id'],$f['book_id'],$posted['id'],$input,$posted['revision'],bin2hex(random_bytes(16)),'Synthetic list correction','2026-01-05');
    $current=$run(['from'=>'2026-02-01','sort'=>'amount','dir'=>'desc','status'=>'unpaid']);
    assert_same(1,$current['total']); assert_same('75.0000',$current['documents'][0]['total']); assert_same(2,$current['documents'][0]['revision']);
    assert_same(1,$run(['q'=>$corrected['number']])['total']); assert_same(0,$run(['q'=>'%'])['total']);
    pl_settle_ar_document($f['actor_id'],$f['company_id'],$f['book_id'],$corrected['id'],ar_ap_payment($f,'75','2026-02-06'));
    assert_same(1,$run(['status'=>'paid'])['total']); assert_same(0,$run(['status'=>'unpaid'])['total']);
    assert_same(25,$run(['status'=>'draft'])['total']); assert_same(0,pl_list_query($f['actor_id'],$f['company_id'],$f['book_id'],'ap',[])['total']);
    foreach ([['sort'=>'current_total DESC'],['dir'=>'invalid'],['status'=>'applied'],['from'=>'2026-03-01','to'=>'2026-01-01']] as $bad) { assert_throws(fn()=>$run($bad),DomainException::class); }
    $other=ar_ap_fixture(); assert_throws(fn()=>pl_list_query($other['actor_id'],$f['company_id'],$f['book_id'],'ar',[]),DomainException::class);
});
