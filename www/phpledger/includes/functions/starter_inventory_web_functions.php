<?php
declare(strict_types=1);

function pl_web_starter_inventory(int $actorId,int $companyId,int $bookId,array $user,array $company,string $path,string $method): never
{
    if ($method==='POST') {
        $id=pl_web_id($_POST,'id');
        try {
            $action=pl_web_text($_POST,'action'); $key=pl_web_text($_POST,'request_key');
            if ($action==='product') {
                if (isset($_POST['is_active']) && pl_web_text($_POST,'is_active')!=='1') { throw new DomainException('Choose whether the product is active.'); }
                $input=['sku'=>pl_web_text($_POST,'sku'),'name'=>pl_web_text($_POST,'name'),'kind'=>pl_web_text($_POST,'kind'),
                    'base_unit'=>pl_web_text($_POST,'base_unit'),'selling_price'=>pl_web_text($_POST,'selling_price'),'is_active'=>isset($_POST['is_active']),
                    'reason'=>pl_web_text($_POST,'reason'),'idempotency_key'=>$key];
                foreach (['inventory_account_id','cogs_account_id','sales_account_id','purchase_account_id'] as $field) { $input[$field]=pl_web_id($_POST,$field)?:null; }
                $result=pl_save_inventory_product($actorId,$companyId,$bookId,$input,$id?:null,$id?pl_web_id($_POST,'revision'):null); $id=(int)$result['id'];
            } elseif (in_array($action,['receive','issue','count','value'],true)) {
                $input=['product_id'=>$id,'date'=>pl_web_text($_POST,'date'),'quantity'=>pl_web_text($_POST,'quantity'),
                    'offset_account_id'=>pl_web_id($_POST,'offset_account_id'),'source_type'=>'manual_stock','source_reference'=>pl_web_text($_POST,'reference'),
                    'reason'=>pl_web_text($_POST,'reason'),'idempotency_key'=>$key];
                if ($action==='receive') { $input['amount_base']=pl_web_text($_POST,'amount_base'); pl_inventory_receive($actorId,$companyId,$bookId,$input); }
                elseif ($action==='issue') { pl_inventory_issue($actorId,$companyId,$bookId,$input); }
                elseif ($action==='value') { $input['amount_base']=pl_web_text($_POST,'amount_base'); $input['expected_quantity']=pl_web_text($_POST,'expected_quantity'); $input['expected_value_base']=pl_web_text($_POST,'expected_value_base'); pl_inventory_value_adjustment($actorId,$companyId,$bookId,$input); }
                else { $input['counted_quantity']=pl_web_text($_POST,'counted_quantity'); $input['expected_quantity']=pl_web_text($_POST,'expected_quantity'); $input['unit_cost']=pl_web_text($_POST,'unit_cost','0'); pl_inventory_adjust_count($actorId,$companyId,$bookId,$input); }
            } elseif ($action==='opening_preview') {
                unset($_SESSION['starter_inventory_preview']);
                $lines=[];
                $submittedLines=$_POST['lines']??[];
                if (!is_array($submittedLines)||count($submittedLines)>500) { throw new DomainException('Use up to 500 opening product rows.'); }
                foreach ($submittedLines as $row) { if (is_array($row)&&pl_web_id($row,'product_id')) { $lines[]=['product_id'=>pl_web_id($row,'product_id'),'quantity'=>pl_web_text($row,'quantity'),'amount_base'=>pl_web_text($row,'amount_base')]; } }
                $preview=pl_preview_inventory_opening($actorId,$companyId,$bookId,['date'=>pl_web_text($_POST,'date'),'reason'=>pl_web_text($_POST,'reason'),'lines'=>$lines]);
                $_SESSION['starter_inventory_preview']=['company_id'=>$companyId,'book_id'=>$bookId]+$preview;
            } elseif ($action==='opening_confirm') {
                $preview=$_SESSION['starter_inventory_preview']??null;
                if (!$preview || $preview['company_id']!==$companyId || $preview['book_id']!==$bookId || (int)$preview['id']!==pl_web_id($_POST,'preview_id') || !hash_equals($preview['payload_hash'],pl_web_text($_POST,'expected_hash'))) { throw new DomainException('Review the current opening stock preview in this company before confirming.'); }
                pl_confirm_inventory_opening($actorId,$companyId,$bookId,pl_web_id($_POST,'preview_id'),pl_web_text($_POST,'expected_hash'),pl_web_text($_POST,'confirmed')==='1',$key);
                unset($_SESSION['starter_inventory_preview']);
            } else { throw new DomainException('Choose an inventory action.'); }
            pl_notice($action==='opening_preview'?'Opening quantities and values reconcile. Review before confirmation.':'Inventory action completed.'); pl_redirect(pl_url($path,['id'=>$id?:null]));
        } catch (DomainException $error) {
            $input=array_map(static fn(mixed $value): string=>is_scalar($value)?(string)$value:'',$_POST);
            $input['lines']=is_array($_POST['lines']??null)?array_values(array_map(
                static fn(array $line): array=>array_map(static fn(mixed $value): string=>is_scalar($value)?(string)$value:'',$line),
                array_filter($_POST['lines'],'is_array')
            )):[];
            pl_form_failure(pl_url($path,['id'=>$id?:null,'new'=>!$id&&pl_web_text($_POST,'action')==='product'?'1':null]),$input,$error->getMessage());
        }
    }
    $id=pl_web_id($_GET,'id'); $product=$id?pl_get_inventory_product($actorId,$companyId,$bookId,$id):null;
    $preview=$_SESSION['starter_inventory_preview']??null;
    if ($preview && ($preview['company_id']!==$companyId||$preview['book_id']!==$bookId)) { $preview=null; }
    pl_render('inventory',['title'=>'Inventory','user'=>$user,'company'=>$company,'product'=>$product,
        'form'=>pl_form_state(pl_url($path,['id'=>$id?:null,'new'=>$id?null:(pl_web_text($_GET,'new')?:null)])),
        'enabled'=>pl_module_available($actorId,$companyId,$bookId,'inventory'),'accounts'=>pl_starter_accounts($actorId,$companyId,$bookId),
        'products'=>pl_list_inventory_products($actorId,$companyId,$bookId),'valuation'=>pl_inventory_valuation($actorId,$companyId,$bookId,pl_web_text($_GET,'as_of')?:null),
        'movements'=>pl_inventory_history($actorId,$companyId,$bookId,$id?:null),'preview'=>$preview,
        'balance'=>$product?pl_inventory_balance($actorId,$companyId,$bookId,$id):null]);
}
