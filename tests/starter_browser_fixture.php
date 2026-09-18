<?php
declare(strict_types=1);

// Explicit sample fixture for local browser acceptance; refuses any other database.
if (getenv('PL_ENV')!=='test' || getenv('PL_DB_NAME')!=='phpledger_test') { fwrite(STDERR,"Use the isolated test database.\n"); exit(2); }
require_once dirname(__DIR__).'/www/phpledger/includes/bootstrap.php';
$suffix=bin2hex(random_bytes(5));
$email='starter-browser-'.$suffix.'@example.test';
$actor=pl_create_user($email,'Sample Starter Owner','Sample-browser-only-2026!');
$f=pl_create_company($actor,'Sample Starter '.$suffix,'USD','2026-01-01');
$company=$f['company_id']; $book=$f['book_id'];
$extra=[];
foreach (['inventory'=>['1300','asset'],'grni'=>['2100','liability'],'tax_in'=>['1350','asset'],'tax_out'=>['2150','liability'],'variance'=>['5200','expense']] as $name=>[$code,$type]) {
    $extra[$name]=pl_save_account($actor,$company,$book,['code'=>$code,'name'=>ucwords(str_replace('_',' ',$name)),'type'=>$type,'role'=>null,'is_active'=>true,'reason'=>'Sample browser fixture','creation_key'=>'account-'.$name])['id'];
}
foreach (['inventory','purchasing','pos-showcase'] as $id) { $m=pl_module_registry()[$id]; pl_set_company_module($actor,$company,$id,true,0,$m['digest'],'Sample browser fixture','module-'.$id); }
$party=pl_save_party($actor,$company,$book,['legal_name'=>'Sample Customer and Supplier','entity_type'=>'private_company','country_code'=>'GB','is_customer'=>true,'is_vendor'=>true,'currency'=>'USD','reason'=>'Sample browser fixture','request_key'=>'party']);
$product=pl_save_inventory_product($actor,$company,$book,['sku'=>'DEMO-ITEM','name'=>'Sample Widget','kind'=>'stock','base_unit'=>'each','selling_price'=>'25','is_active'=>true,
    'inventory_account_id'=>$extra['inventory'],'cogs_account_id'=>$f['accounts']['5000'],'sales_account_id'=>$f['accounts']['4000'],'purchase_account_id'=>$f['accounts']['5000'],'reason'=>'Sample browser fixture','idempotency_key'=>'product']);
$tax=pl_create_tax_code($actor,$company,$book,['code'=>'DEMO5','name'=>'Sample five percent','treatment'=>'standard','sales_account_id'=>$extra['tax_out'],'purchase_account_id'=>$extra['tax_in'],'reason'=>'Sample browser fixture','idempotency_key'=>'tax-code']);
pl_enter_tax_rate($actor,$company,$book,['tax_code_id'=>$tax['id'],'effective_from'=>'2026-01-01','percentage'=>'5','reason'=>'Sample browser fixture','idempotency_key'=>'tax-rate']);
if (($argv[1] ?? '') === '--ageing') {
    foreach (['invoice','bill'] as $kind) {
        foreach (['2026-09-30','2026-09-01','2026-08-01','2026-07-01','2026-05-01'] as $index=>$due) {
            $draft=pl_save_ar_document($actor,$company,$book,['kind'=>$kind,'party_id'=>$party['id'],'date'=>'2026-01-05','due_date'=>$due,'currency'=>'USD','reference'=>'Sample ageing bucket '.($index+1),'creation_key'=>'ageing-'.$kind.'-'.$index,
                'lines'=>[['description'=>'Sample ageing service','quantity'=>'1','unit_price'=>'10.2500','account_id'=>$f['accounts'][$kind==='invoice'?'4000':'5000']]]]);
            pl_post_ar_document($actor,$company,$book,$draft['id'],$draft['revision']);
        }
    }
}
echo json_encode(['email'=>$email,'actor_id'=>$actor,'company_id'=>$company,'book_id'=>$book,'party_id'=>$party['id'],'product_id'=>$product['id'],'tax_code_id'=>$tax['id'],'accounts'=>$f['accounts'],'extra'=>$extra],JSON_THROW_ON_ERROR|JSON_PRETTY_PRINT)."\n";
