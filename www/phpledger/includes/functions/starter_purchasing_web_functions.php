<?php
declare(strict_types=1);

function pl_web_starter_purchasing(int $actorId, int $companyId, int $bookId, array $user, array $company, string $path, string $method): never
{
    if (isset($_GET['receive']) || in_array(pl_web_text($_POST,'action'),['receive','receipt_preview','receipt_confirm'],true)) {
        pl_web_goods_receipt($actorId,$companyId,$bookId,$user,$company,$method);
    }
    if ($method === 'POST') {
        $id = pl_web_id($_POST, 'id');
        try {
            $action = pl_web_text($_POST, 'action'); $key = pl_web_text($_POST, 'request_key');
            if ($action === 'save') {
                if (pl_web_text($_POST,'editor_action')==='add_line' || isset($_POST['remove_line'])) {
                    pl_require_module($actorId,$companyId,$bookId,'purchasing');
                    pl_form_failure(pl_url($path,['id'=>$id?:null,'new'=>$id?null:'1']),pl_web_line_action($_POST),'',200);
                }
                $lines = [];
                foreach (pl_starter_lines($_POST) as $line) { $lines[] = array_intersect_key($line, array_flip(['product_id', 'description', 'quantity', 'unit_price'])); }
                $save=pl_web_text($_POST,'editor_action')==='confirm_order'?'pl_save_and_confirm_purchase_order':'pl_save_purchase_order';
                $order = $save($actorId, $companyId, $bookId, ['party_id' => pl_web_id($_POST, 'party_id'), 'date' => pl_web_text($_POST, 'date'), 'currency' => strtoupper(pl_web_text($_POST, 'currency')), 'reference' => pl_web_text($_POST, 'reference'), 'notes' => pl_web_text($_POST, 'notes'), 'creation_key' => $key, 'lines' => $lines], $id ?: null, $id ? pl_web_id($_POST, 'revision') : null);
                $id = $order['id'];
            } elseif ($action === 'confirm') {
                pl_confirm_purchase_order($actorId, $companyId, $bookId, $id, pl_web_id($_POST, 'revision'), $key);
            } elseif ($action === 'cancel') {
                pl_cancel_purchase_order($actorId, $companyId, $bookId, $id, pl_web_id($_POST, 'revision'), pl_web_text($_POST, 'reason'), $key);
            } elseif ($action === 'receive') {
                $lines = [];
                foreach (pl_purchase_rows($_POST['lines'] ?? null) as $line) {
                    if (pl_web_text($line, 'quantity') === '') { continue; }
                    $lines[] = ['order_line_id' => pl_web_id($line, 'order_line_id'), 'quantity' => pl_web_text($line, 'quantity')];
                }
                if (pl_web_text($_POST,'confirmed')!=='1') { throw new DomainException('Confirm that the quantities shown were physically received.'); }
                pl_receive_purchase_order($actorId, $companyId, $bookId, $id, ['date' => pl_web_text($_POST, 'date'), 'grni_account_id' => pl_web_id($_POST, 'grni_account_id'), 'rate' => pl_web_text($_POST, 'rate') ?: null, 'idempotency_key' => $key, 'lines' => $lines]);
            } elseif ($action === 'bill_preview') {
                unset($_SESSION['starter_purchase_bill_preview']);
                $lines = [];
                foreach (pl_purchase_rows($_POST['lines'] ?? null) as $line) {
                    if (pl_web_text($line, 'quantity') === '') { continue; }
                    $lines[] = ['receipt_line_id' => pl_web_id($line, 'receipt_line_id'), 'quantity' => pl_web_text($line, 'quantity'), 'unit_price' => pl_web_text($line, 'unit_price'), 'tax_code_id' => pl_web_id($line, 'tax_code_id') ?: null];
                }
                $input = ['party_id' => pl_web_id($_POST, 'party_id'), 'date' => pl_web_text($_POST, 'date'), 'due_date' => pl_web_text($_POST, 'due_date'), 'currency' => strtoupper(pl_web_text($_POST, 'currency')), 'reference' => pl_web_text($_POST, 'reference'), 'grni_account_id' => pl_web_id($_POST, 'grni_account_id'), 'rate' => pl_web_text($_POST, 'rate') ?: null, 'variance_confirmed' => pl_web_text($_POST,'variance_confirmed')==='1', 'price_mode' => pl_web_text($_POST, 'price_mode') ?: pl_tax_price_mode($actorId, $companyId, $bookId), 'idempotency_key' => $key, 'lines' => $lines];
                foreach (['variance_account_id', 'rounding_account_id'] as $field) { $input[$field] = pl_web_id($_POST, $field) ?: null; }
                $preview = pl_preview_purchase_bill($actorId, $companyId, $bookId, $input);
                $_SESSION['starter_purchase_bill_preview'] = ['company_id' => $companyId, 'book_id' => $bookId] + $preview;
            } elseif ($action === 'bill_confirm') {
                $preview = $_SESSION['starter_purchase_bill_preview'] ?? null;
                if (!$preview || $preview['company_id'] !== $companyId || $preview['book_id'] !== $bookId || !hash_equals($preview['payload_hash'], pl_web_text($_POST, 'expected_hash')) || !hash_equals($preview['input']['idempotency_key'],$key) || pl_web_text($_POST,'confirmed')!=='1') { throw new DomainException('Review and confirm the supplier bill in this company first.'); }
                $result = pl_bill_purchase_receipts($actorId, $companyId, $bookId, $preview['input'] + ['expected_hash' => $preview['payload_hash']]);
                unset($_SESSION['starter_purchase_bill_preview']);
                pl_notice('Supplier bill posted and receipt quantities matched.'); pl_redirect(pl_url('/ap', ['id' => $result['bill_document_id']]));
            } elseif ($action === 'return') {
                if (pl_web_text($_POST,'confirmed')!=='1') { throw new DomainException('Confirm the physical return and any linked supplier credit.'); }
                $input = ['receipt_line_id' => pl_web_id($_POST, 'receipt_line_id'), 'quantity' => pl_web_text($_POST, 'quantity'), 'date' => pl_web_text($_POST, 'date'), 'reason' => pl_web_text($_POST, 'reason'), 'idempotency_key' => $key, 'variance_confirmed' => pl_web_text($_POST,'variance_confirmed')==='1'];
                foreach (['match_id', 'variance_account_id', 'rounding_account_id'] as $field) { if (pl_web_id($_POST, $field)) { $input[$field] = pl_web_id($_POST, $field); } }
                pl_return_purchase_receipt($actorId, $companyId, $bookId, $input);
            } else { throw new DomainException('Choose a purchasing action.'); }
            pl_notice($action === 'bill_preview' ? 'Bill preview prepared. Review the amounts before posting.' : 'Purchasing action completed.');
            pl_redirect(pl_url($path, ['id' => $id ?: null]));
        } catch (DomainException $error) {
            $input=array_map(static fn(mixed $value): string=>is_scalar($value)?(string)$value:'',$_POST);
            $input['lines']=is_array($_POST['lines']??null)?array_values(array_map(
                static fn(array $line): array=>array_map(static fn(mixed $value): string=>is_scalar($value)?(string)$value:'',$line),
                array_filter($_POST['lines'],'is_array')
            )):[];
            pl_form_failure(pl_url($path, ['id' => $id ?: null, 'new' => $action === 'save' && !$id ? '1' : null]), $input, $error->getMessage());
        }
    }
    $id = pl_web_id($_GET, 'id'); $order = $id ? pl_get_purchase_order($actorId, $companyId, $bookId, $id) : null;
    $preview = $_SESSION['starter_purchase_bill_preview'] ?? null;
    if ($preview && ($preview['company_id'] !== $companyId || $preview['book_id'] !== $bookId)) { $preview = null; }
    pl_render('purchasing', ['title' => 'Purchasing', 'user' => $user, 'company' => $company, 'order' => $order,
        'form' => pl_form_state(pl_url($path, ['id' => $id ?: null, 'new' => $id ? null : (pl_web_text($_GET, 'new') ?: null)])),
        'enabled' => pl_module_available($actorId, $companyId, $bookId, 'purchasing'), 'accounts' => pl_starter_accounts($actorId, $companyId, $bookId),
        'parties' => pl_starter_parties($actorId, $companyId, $bookId), 'products' => pl_list_inventory_products($actorId, $companyId, $bookId),
        'orders' => pl_list_purchase_orders($actorId, $companyId, $bookId), 'receipts' => pl_purchasing_receipts($actorId, $companyId, $bookId),
        'reconciliation' => pl_purchase_received_unbilled($actorId, $companyId, $bookId, pl_web_text($_GET, 'as_of') ?: null),
        'priceMode' => pl_tax_price_mode($actorId, $companyId, $bookId), 'taxCodes' => pl_list_tax_codes($actorId, $companyId, $bookId), 'preview' => $preview]);
}

function pl_web_goods_receipt_input(array $input): array
{
    $lines=[];
    foreach (pl_purchase_rows($input['lines']??null) as $line) {
        if (pl_web_text($line,'quantity')==='') { continue; }
        $lines[]=['order_line_id'=>pl_web_id($line,'order_line_id'),'quantity'=>pl_web_text($line,'quantity')];
    }
    return ['date'=>pl_web_text($input,'date'),'grni_account_id'=>pl_web_id($input,'grni_account_id'),'rate'=>pl_web_text($input,'rate')?:null,
        'idempotency_key'=>pl_web_text($input,'request_key'),'lines'=>$lines];
}

function pl_web_goods_receipt(int $actorId,int $companyId,int $bookId,array $user,array $company,string $method): never
{
    pl_require_company_access($actorId,$companyId,true);
    $id=pl_web_id($method==='POST'?$_POST:$_GET,'id'); $return=pl_url('/purchasing',['id'=>$id,'receive'=>'1']);
    if ($method==='POST') {
        try {
            $input=pl_web_goods_receipt_input($_POST);
            if (pl_web_text($_POST,'action')==='receipt_confirm') {
                if (pl_web_text($_POST,'confirmed')!=='1') { throw new DomainException('Confirm that the quantities shown were physically received.'); }
                $review=$_SESSION['goods_receipt_review']??[];
                $hash=is_array($review) && ($review['company_id']??null)===$companyId && ($review['book_id']??null)===$bookId && ($review['key']??null)===$input['idempotency_key'] ? ($review['hash']??'') : '';
                pl_confirm_purchase_receipt($actorId,$companyId,$bookId,$id,$input,is_string($hash)?$hash:'');
                pl_notice('Goods receipt recorded. Stock and received-but-unbilled value were posted together.');
                pl_redirect(pl_url('/purchasing',['id'=>$id]));
            }
            $preview=pl_preview_purchase_receipt($actorId,$companyId,$bookId,$id,$input);
            $_SESSION['goods_receipt_review']=['company_id'=>$companyId,'book_id'=>$bookId,'key'=>$input['idempotency_key'],'hash'=>hash('sha256',json_encode($preview,JSON_THROW_ON_ERROR))];
            pl_form_failure($return,$_POST,'',200);
        } catch (DomainException $error) { pl_form_failure($return,$_POST,$error->getMessage()); }
    }
    $order=pl_get_purchase_order($actorId,$companyId,$bookId,$id); $form=pl_form_state($return); $input=$form['input']; $preview=null;
    if ($input) {
        try { $preview=pl_preview_purchase_receipt($actorId,$companyId,$bookId,$id,pl_web_goods_receipt_input($input)); }
        catch (DomainException $error) { if ($form['message']==='') { $form['message']=$error->getMessage(); } }
    }
    pl_render('goods-receipt',['title'=>'Receive goods','user'=>$user,'company'=>$company,'order'=>$order,'form'=>$form,'input'=>$input,'preview'=>$preview,
        'accounts'=>pl_starter_accounts($actorId,$companyId,$bookId)]);
}
