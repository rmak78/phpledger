<?php
declare(strict_types=1);
// Run seed against an archived v0.5.0-preview tree, then check against this tree.
// Both phases require the disposable local root account; never accepts a database name.
if (PHP_SAPI!=='cli' || getenv('PL_ENV')!=='test' || getenv('PL_DB_HOST')!=='db_test' || getenv('PL_DB_NAME')!=='phpledger_test' || getenv('PL_DB_USER')!=='root') {
    fwrite(STDERR,"Historical preview verification requires the disposable db_test root account.\n"); exit(2);
}
$mode=$argv[1]??''; $baseline='/tmp/phpledger-preview-05'; $receipt='/tmp/phpledger-preview-upgrade.json';
if (!in_array($mode,['seed','check'],true)) { throw new DomainException('Choose seed or check.'); }
$root=$mode==='seed'?$baseline:dirname(__DIR__);
require $root.'/www/phpledger/includes/bootstrap.php';
require $root.'/www/phpledger/install/migrate.php';
if (DB::$host!=='db_test' || DB::$dbName!=='phpledger_test' || DB::$user!=='root') { throw new RuntimeException('Effective configuration is not the disposable test service.'); }
function preview_assert(bool $condition,string $message): void { if (!$condition) { throw new RuntimeException($message); } }
/** Published fixture functions are loaded from the archived release, not this checkout. */
function preview_fixture(string $name,mixed ...$args): array {
    if (!in_array($name,['purchasing_fixture','purchasing_order','purchasing_receipt_input','purchasing_bill_input','ar_ap_payment','ar_ap_input'],true) || !function_exists($name)) { throw new RuntimeException('Published fixture builder is unavailable.'); }
    $result=call_user_func_array($name,$args);
    if (!is_array($result)) { throw new RuntimeException('Unexpected published fixture result.'); }
    return $result;
}
function preview_rows(string $table,array $columns): string {
    $rows=DB::query('SELECT * FROM %b',$table); $keys=array_fill_keys($columns,true);
    foreach ($rows as &$row) { $row=array_intersect_key($row,$keys); ksort($row); } unset($row);
    usort($rows,static fn(array $a,array $b):int=>strcmp(json_encode($a,JSON_THROW_ON_ERROR),json_encode($b,JSON_THROW_ON_ERROR)));
    return hash('sha256',json_encode($rows,JSON_THROW_ON_ERROR));
}
function preview_reports(array $f): array {
    $args=[$f['actor_id'],$f['company_id'],$f['book_id']];
    $ar=pl_ar_ap_open_items(...array_merge($args,['receivable','2026-12-31']));
    $ap=pl_ar_ap_open_items(...array_merge($args,['payable','2026-12-31']));
    $tb=pl_trial_balance(...$args); $pl=pl_profit_loss(...array_merge($args,['2026-01-01','2026-12-31']));
    $stock=pl_inventory_valuation(...array_merge($args,['2026-12-31']));
    return ['trial'=>$tb,'profit'=>$pl['net_profit'],'ar'=>$ar['total_base'],'ap'=>$ap['total_base'],'ar_reconciled'=>$ar['reconciled'],'ap_reconciled'=>$ap['reconciled'],'stock'=>$stock];
}
if ($mode==='seed') {
    preview_assert(!is_file($receipt),'A pending upgrade receipt already exists; inspect it before repeating.');
    $database='phpledger_preview_verify_'.bin2hex(random_bytes(12));
    preview_assert((int)DB::queryFirstField('SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME=%s',$database)===0,'Refusing an existing schema.');
    DB::query('CREATE DATABASE %b CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci',$database); DB::useDB($database);
    try {
        $migration=pl_migrate(); preview_assert(end($migration['applied'])==='028_ar_ap_upgrade_completion','Seed must use the published migration chain through 028.');
        // Load only the published fixture builders; test closures are intentionally not run.
        function test(string $name,callable $body): void {}
        foreach (['ledger','ar_ap','inventory','purchasing'] as $suite) { require $root.'/tests/'.$suite.'_test.php'; }
        $f=preview_fixture('purchasing_fixture'); $args=[$f['actor_id'],$f['company_id'],$f['book_id']];
        $order=preview_fixture('purchasing_order',$f,'10','10');
        $receiptInput=preview_fixture('purchasing_receipt_input',$f,$order,'6');
        $received=pl_receive_purchase_order(...array_merge($args,[$order['id'],$receiptInput]));
        $bill=pl_bill_purchase_receipts(...array_merge($args,[preview_fixture('purchasing_bill_input',$f,$received,'4')]));
        pl_settle_ar_document(...array_merge($args,[$bill['bill_document_id'],preview_fixture('ar_ap_payment',$f,'10','2026-01-10')]));
        $invoiceInput=preview_fixture('ar_ap_input',$f,'invoice','25'); $invoiceInput['date']='2026-01-11'; $invoiceInput['lines'][0]['product_id']=$f['product_id']; $invoiceInput['lines'][0]['quantity']='2';
        $draft=pl_save_ar_document(...array_merge($args,[$invoiceInput]));
        $invoice=pl_post_ar_document(...array_merge($args,[$draft['id'],$draft['revision']]));
        $paymentInput=preview_fixture('ar_ap_payment',$f,'10','2026-01-12'); $payment=pl_settle_ar_document(...array_merge($args,[$invoice['id'],$paymentInput]));
        $creditInput=preview_fixture('ar_ap_input',$f,'customer_credit','25'); $creditInput['date']='2026-01-13'; $creditInput['original_document_id']=$invoice['id']; $creditInput['lines'][0]['product_id']=$f['product_id'];
        $credit=pl_save_ar_document(...array_merge($args,[$creditInput])); pl_post_ar_document(...array_merge($args,[$credit['id'],$credit['revision']]));
        pl_currency_rate_enter(...array_merge($args,[['from_currency'=>'EUR','to_currency'=>'USD','rate_date'=>'2026-01-14','rate'=>'1.234567890123','source'=>'manual','note'=>'Sample published FX evidence','idempotency_key'=>'preview-fx']]));
        $fxInput=preview_fixture('ar_ap_input',$f,'invoice','100'); $fxInput['currency']='EUR'; $fxInput['date']='2026-01-14';
        $fxDraft=pl_save_ar_document(...array_merge($args,[$fxInput])); $fx=pl_post_ar_document(...array_merge($args,[$fxDraft['id'],$fxDraft['revision']]));
        $fxPayment=preview_fixture('ar_ap_payment',$f,'25','2026-01-15'); $fxPayment['manual_rate']='1.25'; $fxPayment['manual_rate_reason']='Sample settlement difference';
        pl_settle_ar_document(...array_merge($args,[$fx['id'],$fxPayment]));
        // Receipt readers include subsequently billed quantities; compare the final pre-upgrade view.
        $received=pl_receive_purchase_order(...array_merge($args,[$order['id'],$receiptInput]));
        $reports=preview_reports($f); preview_assert($reports['ar_reconciled'] && $reports['ap_reconciled'] && $reports['trial']['balanced'],'Historical fixture must reconcile before upgrade.');
        $tables=[];
        foreach (DB::queryFirstColumn('SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA=%s AND TABLE_TYPE=%s',$database,'BASE TABLE') as $table) {
            $columns=DB::queryFirstColumn('SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=%s AND TABLE_NAME=%s ORDER BY ORDINAL_POSITION',$database,$table);
            $tables[$table]=['columns'=>$columns,'hash'=>preview_rows($table,$columns)];
        }
        file_put_contents($receipt,json_encode(compact('database','f','tables','reports','invoice','paymentInput','payment','order','receiptInput','received'),JSON_THROW_ON_ERROR));
        echo 'Published 0.5 fixtures created: AR/AP, partial settlements, stock invoice/credit, partial goods receipt/billing and realised FX; '.count($tables)." table snapshots.\n";
    } catch (Throwable $error) { DB::useDB('phpledger_test'); DB::query('DROP DATABASE %b',$database); throw $error; }
    exit;
}
preview_assert(is_file($receipt),'Seed receipt is required.');
$data=json_decode((string)file_get_contents($receipt),true,512,JSON_THROW_ON_ERROR); $database=$data['database'];
preview_assert(is_string($database) && preg_match('/^phpledger_preview_verify_[a-f0-9]{24}$/D',$database)===1,'Unexpected verification schema.');
DB::useDB($database);
try {
    $migration=pl_migrate(); preview_assert($migration['applied']===['029_connection_access','030_cost_of_sales','031_posting_source_lookup'],'Review the expected preview migration chain.');
    foreach ($data['tables'] as $table=>$snapshot) {
        if ($table==='pl_schema_migrations') { continue; }
        preview_assert(preview_rows($table,$snapshot['columns'])===$snapshot['hash'],'Historical rows changed in '.$table);
    }
    preview_assert(preview_reports($data['f'])===$data['reports'],'Historical reports or reconciliation changed.');
    $args=[$data['f']['actor_id'],$data['f']['company_id'],$data['f']['book_id']];
    preview_assert(pl_settle_ar_document(...array_merge($args,[$data['invoice']['id'],$data['paymentInput']]))===$data['payment'],'Published settlement retry changed.');
    preview_assert(pl_receive_purchase_order(...array_merge($args,[$data['order']['id'],$data['receiptInput']]))===$data['received'],'Published goods receipt retry changed.');
    foreach ($data['tables'] as $table=>$snapshot) {
        if ($table==='pl_schema_migrations') { continue; }
        preview_assert(preview_rows($table,$snapshot['columns'])===$snapshot['hash'],'A historical retry changed rows in '.$table);
    }
    $new=pl_save_ar_document(...array_merge($args,[['kind'=>'invoice','party_id'=>$data['f']['party_id'],'date'=>'2026-02-01','due_date'=>'2026-03-01','currency'=>'USD','reference'=>'Sample post-upgrade invoice','creation_key'=>'preview-post-upgrade',
        'lines'=>[['description'=>'Sample post-upgrade stock sale','product_id'=>$data['f']['product_id'],'account_id'=>$data['f']['accounts']['4000'],'quantity'=>'1','unit_price'=>'25']]]]));
    pl_post_ar_document(...array_merge($args,[$new['id'],$new['revision']]));
    $after=preview_reports($data['f']);
    preview_assert($after['trial']['balanced'] && $after['ar_reconciled'] && $after['ap_reconciled'],'New posting after upgrade does not reconcile.');
    foreach ($after['stock']['accounts'] as $account) { preview_assert($account['difference']==='0.0000','Post-upgrade inventory does not reconcile.'); }
    preview_assert(pl_migrate()['applied']===[],'Migration replay is not empty.');
    echo 'PASS actual v0.5.0-preview data upgrade: unchanged historical table projections, exact reports, AR/AP and inventory reconciliation, settlement/receipt retry, new stock posting and migration replay.'.PHP_EOL;
} finally {
    DB::useDB('phpledger_test'); DB::query('DROP DATABASE %b',$database); unlink($receipt);
}
