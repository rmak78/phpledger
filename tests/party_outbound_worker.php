<?php
declare(strict_types=1);

if(PHP_SAPI!=='cli'||getenv('PL_ENV')!=='test'||getenv('PL_DB_NAME')!=='phpledger_test'){exit(2);}
try{
    require dirname(__DIR__).'/www/phpledger/includes/bootstrap.php';
    $job=json_decode((string)file_get_contents($argv[1]??''),true,512,JSON_THROW_ON_ERROR);
    $deadline=microtime(true)+10;
    while(!is_file($job['barrier'])){if(microtime(true)>$deadline){throw new RuntimeException('Barrier timed out.');}usleep(5000);}
    $f=$job['fixture'];
    try{
        $value=match($job['mode']){
            'party'=>pl_save_party($f['actor_id'],$f['company_id'],$f['book_id'],$job['input']),
            'contact'=>pl_save_contact($f['actor_id'],$f['company_id'],$f['book_id'],$job['party_id'],$job['input']),
            'claim'=>pl_outbound_claim('synthetic',$f['company_id'],$f['book_id']),
            default=>throw new LogicException('Unknown foundation worker operation.'),
        };
        // SKIP LOCKED may transiently skip an ordered scan's candidates, including the
        // second available event. Retry only in the eventual-disjoint race scenario.
        if($job['mode']==='claim' && ($job['retry_empty_claim']??false)===true){
            $claimDeadline=microtime(true)+3;
            while($value===null && microtime(true)<$claimDeadline){
                usleep(10000);
                $value=pl_outbound_claim('synthetic',$f['company_id'],$f['book_id']);
            }
        }
        echo json_encode(['ok'=>true,'value'=>$value],JSON_THROW_ON_ERROR);
    }catch(DomainException $error){echo json_encode(['ok'=>false,'error'=>$error->getMessage()],JSON_THROW_ON_ERROR);}
}catch(Throwable){fwrite(STDERR,"Foundation test worker failed.\n");exit(1);}
