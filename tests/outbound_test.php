<?php
declare(strict_types=1);

require_once __DIR__.'/party_outbound_race.php';

function outbound_fixture(): array
{
    $f=ledger_fixture();
    $f['event_id']=pl_ledger_transaction(fn()=>pl_enqueue_outbound_event($f['company_id'],$f['book_id'],'party.created','party',1,1,'synthetic-event'));
    return $f;
}

function outbound_handler(array $f,callable $handler): array
{
    return ['synthetic'=>['company_id'=>$f['company_id'],'book_id'=>$f['book_id'],'handler'=>$handler]];
}

test('outbound content is transactional immutable and content-bound',function():void{
    $f=outbound_fixture();
    assert_throws(fn()=>pl_enqueue_outbound_event($f['company_id'],$f['book_id'],'party.created','party',1,1,'outside'),LogicException::class);
    assert_same($f['event_id'],pl_ledger_transaction(fn()=>pl_enqueue_outbound_event($f['company_id'],$f['book_id'],'party.created','party',1,1,'synthetic-event')));
    assert_throws(fn()=>pl_ledger_transaction(fn()=>pl_enqueue_outbound_event($f['company_id'],$f['book_id'],'party.created','party',1,2,'synthetic-event')),DomainException::class);
    assert_throws(fn()=>DB::delete('pl_outbound_events','id=%i',$f['event_id']));
    assert_same(0,pl_dispatch_outbound_events([])['claimed']);
    assert_same(0,(int)DB::queryFirstField('SELECT COUNT(*) FROM pl_outbound_deliveries WHERE event_id=%i',$f['event_id']));
});

test('scoped fake outbound dispatch acknowledges once outside transaction',function():void{
    $f=outbound_fixture();$other=outbound_fixture();$seen=[];
    $registry=outbound_handler($f,function(array $event)use(&$seen,$f):bool{assert_same(0,DB::transactionDepth());assert_same($f['book_id'],(int)$event['book_id']);$seen[]=$event['event_key'];return true;});
    assert_same(1,pl_dispatch_outbound_events($registry)['succeeded']);
    assert_same(0,pl_dispatch_outbound_events($registry)['claimed']);assert_same(['synthetic-event'],$seen);
    assert_same(0,(int)DB::queryFirstField('SELECT COUNT(*) FROM pl_outbound_deliveries WHERE event_id=%i',$other['event_id']));
    assert_throws(fn()=>DB::query("UPDATE pl_outbound_attempts SET outcome='dead' WHERE delivery_id IN (SELECT id FROM pl_outbound_deliveries WHERE event_id=%i)",$f['event_id']));
});

test('outbound failure retries with capped backoff and eight-attempt terminal state',function():void{
    $f=outbound_fixture();$registry=outbound_handler($f,static function():bool{throw new RuntimeException('Synthetic private response must not be logged');});
    assert_same(1,pl_dispatch_outbound_events($registry)['retried']);
    $d=DB::queryFirstRow('SELECT * FROM pl_outbound_deliveries WHERE event_id=%i',$f['event_id']);
    assert_same('ready',$d['status']);assert_same(1,(int)$d['attempts']);
    assert_true(strtotime($d['available_at'])>=time()+55);assert_same(0,pl_dispatch_outbound_events($registry)['claimed']);
    DB::query("UPDATE pl_outbound_deliveries SET attempts=7,available_at=DATE_SUB(UTC_TIMESTAMP(),INTERVAL 1 SECOND) WHERE id=%i",$d['id']);
    assert_same(1,pl_dispatch_outbound_events($registry)['dead']);
    assert_same('dead',DB::queryFirstField('SELECT status FROM pl_outbound_deliveries WHERE id=%i',$d['id']));
    assert_same(0,pl_dispatch_outbound_events($registry)['claimed']);
});

test('outbound leases recover and old worker cannot acknowledge replacement',function():void{
    $f=outbound_fixture();
    DB::insert('pl_outbound_deliveries',['event_id'=>$f['event_id'],'company_id'=>$f['company_id'],'book_id'=>$f['book_id'],'consumer'=>'synthetic']);
    $first=pl_outbound_claim('synthetic',$f['company_id'],$f['book_id']);
    assert_same(null,pl_outbound_claim('synthetic',$f['company_id'],$f['book_id']));
    DB::query('UPDATE pl_outbound_deliveries SET leased_until=DATE_SUB(UTC_TIMESTAMP(),INTERVAL 1 SECOND) WHERE id=%i',$first['id']);
    $replacement=pl_outbound_claim('synthetic',$f['company_id'],$f['book_id']);
    assert_true($replacement['token']!==$first['token']);assert_same(2,$replacement['attempt']);
    assert_same(false,pl_outbound_acknowledge($first['id'],$first['token'],true));
    assert_same(true,pl_outbound_acknowledge($replacement['id'],$replacement['token'],true));
    assert_same($first['event']['event_key'],$replacement['event']['event_key']);
});

test('separate outbound workers claim disjoint events and cannot claim a live lease twice',function():void{
    $f=outbound_fixture();
    $otherId=pl_ledger_transaction(fn()=>pl_enqueue_outbound_event($f['company_id'],$f['book_id'],'party.updated','party',1,2,'synthetic-second-event'));
    foreach([$f['event_id'],$otherId] as $eventId){DB::insert('pl_outbound_deliveries',['event_id'=>$eventId,'company_id'=>$f['company_id'],'book_id'=>$f['book_id'],'consumer'=>'synthetic']);}
    $job=['mode'=>'claim','fixture'=>$f,'retry_empty_claim'=>true];$results=pl_foundation_race([$job,$job]);
    foreach($results as $result){assert_same(true,$result['ok']);assert_true(is_array($result['value']),'A worker did not claim either available event within the retry deadline.');assert_same(1,$result['value']['attempt']);}
    assert_true($results[0]['value']['id']!==$results[1]['value']['id']);
    $ids=array_map(static fn(array $r):int=>(int)$r['value']['event']['id'],$results);sort($ids);$expected=[$f['event_id'],$otherId];sort($expected);assert_same($expected,$ids);
    assert_same(null,pl_outbound_claim('synthetic',$f['company_id'],$f['book_id']));
    foreach($results as $result){assert_same(true,pl_outbound_acknowledge($result['value']['id'],$result['value']['token'],true));}
    assert_same(2,(int)DB::queryFirstField("SELECT COUNT(*) FROM pl_outbound_deliveries WHERE book_id=%i AND status='succeeded'",$f['book_id']));
});

test('fake receiver deduplicates delivered event after lost acknowledgement and expired lease',function():void{
    $f=outbound_fixture();
    DB::insert('pl_outbound_deliveries',['event_id'=>$f['event_id'],'company_id'=>$f['company_id'],'book_id'=>$f['book_id'],'consumer'=>'synthetic']);
    $seen=[];$calls=0;$applied=0;
    $receiver=function(array $event)use(&$seen,&$calls,&$applied):bool{
        ++$calls;$identity=$event['company_id'].':'.$event['book_id'].':'.$event['event_key'];
        if(!isset($seen[$identity])){$seen[$identity]=true;++$applied;}return true;
    };
    $original=pl_outbound_claim('synthetic',$f['company_id'],$f['book_id']);
    assert_same(true,$receiver($original['event']));
    // Simulate a process exiting after receiver success but before queue acknowledgement.
    DB::query('UPDATE pl_outbound_deliveries SET leased_until=DATE_SUB(UTC_TIMESTAMP(),INTERVAL 1 SECOND) WHERE id=%i',$original['id']);
    $result=pl_dispatch_outbound_events(outbound_handler($f,$receiver));
    assert_same(1,$result['succeeded']);assert_same(2,$calls);assert_same(1,$applied);assert_same(1,count($seen));
    assert_same(false,pl_outbound_acknowledge($original['id'],$original['token'],true));
    assert_same(2,(int)DB::queryFirstField('SELECT attempts FROM pl_outbound_deliveries WHERE id=%i',$original['id']));
});
