<?php
declare(strict_types=1);

function pl_web_starter_opening(int $actorId,int $companyId,int $bookId,array $user,array $company,string $path,string $method): never
{
    if ($method==='POST') {
        try {
            $action=pl_web_text($_POST,'action');
            if ($action==='preview') {
                unset($_SESSION['starter_opening_preview']);
                $mappings=[];
                if (!is_array($_POST['mappings']??null)) { throw new DomainException('Map every opening document to its party.'); }
                foreach ($_POST['mappings'] as $mapping) {
                    if (!is_array($mapping)) { throw new DomainException('Invalid opening mapping.'); }
                    $mappings[]=['opening_document_id'=>pl_web_id($mapping,'opening_document_id'),'party_id'=>pl_web_id($mapping,'party_id')];
                }
                $review=pl_preview_opening_conversion($actorId,$companyId,$bookId,pl_web_id($_POST,'cutover_id'),$mappings);
                $_SESSION['starter_opening_preview']=['company_id'=>$companyId,'book_id'=>$bookId,'mappings'=>$mappings,'review'=>$review];
                pl_notice('The mapped opening documents reconcile. Review before confirming.');
            } elseif ($action==='confirm') {
                $saved=$_SESSION['starter_opening_preview']??null;
                if (!$saved || $saved['company_id']!==$companyId || $saved['book_id']!==$bookId) { throw new DomainException('Create a scoped opening review first.'); }
                pl_confirm_opening_conversion($actorId,$companyId,$bookId,(int)$saved['review']['cutover_id'],$saved['mappings'],pl_web_text($_POST,'expected_hash'),pl_web_text($_POST,'confirmed')==='1',pl_web_text($_POST,'request_key'),pl_web_text($_POST,'reason'));
                unset($_SESSION['starter_opening_preview']); pl_notice('Opening debts converted without a second journal.');
            } elseif ($action==='settle') {
                $itemId=pl_web_id($_POST,'item_id');
                if (!DB::queryFirstField('SELECT id FROM pl_open_item_entries WHERE item_id=%i AND company_id=%i AND book_id=%i AND opening_document_id IS NOT NULL',$itemId,$companyId,$bookId)) { throw new DomainException('Choose a converted opening debt in this book.'); }
                pl_settle_open_item($actorId,$companyId,$bookId,['item_id'=>$itemId,'date'=>pl_web_text($_POST,'date'),'amount_fc'=>pl_web_text($_POST,'amount_fc'),
                    'bank_account_id'=>pl_web_id($_POST,'bank_account_id'),'gain_account_id'=>pl_web_id($_POST,'gain_account_id'),'loss_account_id'=>pl_web_id($_POST,'loss_account_id'),
                    'actual_rate'=>pl_web_text($_POST,'actual_rate')?:null,'description'=>pl_web_text($_POST,'description'),'idempotency_key'=>pl_web_text($_POST,'request_key')]);
                pl_notice('Opening debt payment recorded.');
            } else { throw new DomainException('Choose an opening debt action.'); }
            pl_redirect($path);
        } catch (DomainException $error) {
            $input=array_map(static fn(mixed $value): string=>is_scalar($value)?(string)$value:'',$_POST);
            if (is_array($_POST['mappings']??null)) {
                $input['mappings']=array_values(array_map(
                    static fn(array $mapping): array=>array_map(static fn(mixed $value): string=>is_scalar($value)?(string)$value:'',$mapping),
                    array_filter($_POST['mappings'],'is_array')
                ));
            }
            pl_form_failure(pl_url($path),$input,$error->getMessage());
        }
    }
    $cutovers=DB::query("SELECT c.id,p.cutover_date FROM pl_opening_cutovers c JOIN pl_opening_previews p ON p.id=c.preview_id WHERE c.company_id=%i AND c.book_id=%i AND c.status='confirmed' AND NOT EXISTS(SELECT 1 FROM pl_opening_conversions x WHERE x.cutover_id=c.id) ORDER BY c.id",$companyId,$bookId);
    foreach ($cutovers as &$cutover) { $cutover['documents']=DB::query('SELECT * FROM pl_opening_documents WHERE cutover_id=%i AND company_id=%i AND book_id=%i ORDER BY id',$cutover['id'],$companyId,$bookId); } unset($cutover);
    $review=$_SESSION['starter_opening_preview']??null;
    if ($review && ($review['company_id']!==$companyId || $review['book_id']!==$bookId)) { $review=null; }
    $items=[];
    foreach (DB::query('SELECT DISTINCT e.item_id,d.reference,p.legal_name FROM pl_open_item_entries e JOIN pl_opening_documents d ON d.id=e.opening_document_id JOIN pl_open_items i ON i.id=e.item_id JOIN pl_parties p ON p.id=i.party_id WHERE e.company_id=%i AND e.book_id=%i AND e.opening_document_id IS NOT NULL',$companyId,$bookId) as $row) {
        $state=pl_get_open_item($actorId,$companyId,$bookId,(int)$row['item_id']);
        $items[]=$row+$state;
    }
    pl_render('opening-conversion',['title'=>'Opening debts','user'=>$user,'company'=>$company,'form'=>pl_form_state(pl_url($path)),
        'cutovers'=>$cutovers,'parties'=>pl_starter_parties($actorId,$companyId,$bookId),'review'=>$review,'items'=>$items,'accounts'=>pl_starter_accounts($actorId,$companyId,$bookId)]);
}
