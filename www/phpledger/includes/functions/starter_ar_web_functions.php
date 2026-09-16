<?php
declare(strict_types=1);

function pl_web_starter_ar(int $actorId,int $companyId,int $bookId,array $user,array $company,string $path,string $method): never
{
    $receivable=$path==='/ar'; $normalKind=$receivable?'invoice':'bill'; $creditKind=$receivable?'customer_credit':'supplier_credit';
    if ($method==='POST') {
        $id=pl_web_id($_POST,'id');
        try {
            if ($id && !in_array(pl_get_ar_document($actorId,$companyId,$bookId,$id)['kind'],[$normalKind,$creditKind],true)) { throw new DomainException('Open this document in its owning module.'); }
            $action=pl_web_text($_POST,'action'); $key=pl_web_text($_POST,'request_key');
            if (in_array($action,['save','correct'],true)) {
                $kind=pl_web_text($_POST,'kind',$normalKind);
                if (!in_array($kind,[$normalKind,$creditKind],true)) { throw new DomainException('Invalid document type for this module.'); }
                $input=['kind'=>$kind,'price_mode'=>pl_web_text($_POST,'price_mode')?:null,'party_id'=>pl_web_id($_POST,'party_id'),'date'=>pl_web_text($_POST,'date'),
                    'due_date'=>pl_web_text($_POST,'due_date'),'currency'=>strtoupper(pl_web_text($_POST,'currency')),
                    'reference'=>pl_web_text($_POST,'reference'),'terms'=>pl_web_text($_POST,'terms'),'notes'=>pl_web_text($_POST,'notes'),
                    'creation_key'=>$key,'rounding_account_id'=>pl_web_id($_POST,'rounding_account_id')?:null,'lines'=>pl_starter_lines($_POST)];
                if ($kind===$creditKind) { $input['original_document_id']=pl_web_id($_POST,'original_document_id'); }
                $result=$action==='correct'
                    ?pl_correct_ar_document($actorId,$companyId,$bookId,$id,$input,pl_web_id($_POST,'revision'),$key,pl_web_text($_POST,'reason'),pl_web_text($_POST,'reversal_date')?:null,pl_web_text($_POST,'rate')?:null)
                    :pl_save_ar_document($actorId,$companyId,$bookId,$input,$id?:null,$id?pl_web_id($_POST,'revision'):null);
                $id=(int)$result['id'];
            } elseif ($action==='post') {
                pl_post_ar_document($actorId,$companyId,$bookId,$id,pl_web_id($_POST,'revision'),null,pl_web_text($_POST,'rate')?:null);
            } elseif ($action==='settle') {
                pl_settle_ar_document($actorId,$companyId,$bookId,$id,['date'=>pl_web_text($_POST,'date'),'amount_fc'=>pl_web_text($_POST,'amount_fc'),
                    'bank_account_id'=>pl_web_id($_POST,'bank_account_id'),'gain_account_id'=>pl_web_id($_POST,'gain_account_id')?:null,
                    'loss_account_id'=>pl_web_id($_POST,'loss_account_id')?:null,'actual_rate'=>pl_web_text($_POST,'actual_rate')?:null,
                    'description'=>pl_web_text($_POST,'description'),'idempotency_key'=>$key]);
            } elseif ($action==='reverse') {
                pl_reverse_ar_document($actorId,$companyId,$bookId,$id,pl_web_text($_POST,'date')?:null,pl_web_text($_POST,'reason'),$key);
            } elseif ($action==='reverse_payment') {
                $document=pl_get_ar_document($actorId,$companyId,$bookId,$id);
                if ($document['kind']!==$normalKind || $document['open_item_id']===null) { throw new DomainException('Open the original invoice or bill to reverse its payment.'); }
                $journalId=pl_web_id($_POST,'journal_id');
                $owned=DB::queryFirstField('SELECT e.id FROM pl_open_item_entries e JOIN pl_journal_lines l ON l.id=e.journal_line_id WHERE e.item_id=%i AND e.company_id=%i AND e.book_id=%i AND e.kind=%s AND l.journal_id=%i',$document['open_item_id'],$companyId,$bookId,'allocation',$journalId);
                if (!$owned) { throw new DomainException('Payment is unavailable for this document.'); }
                $journal=pl_get_journal($actorId,$companyId,$bookId,$journalId);
                if ($journal['source_type']!=='open_item_settlement' || DB::queryFirstField('SELECT id FROM pl_ar_document_revisions WHERE journal_id=%i AND company_id=%i AND book_id=%i',$journalId,$companyId,$bookId)) { throw new DomainException('Reverse credits through their own document.'); }
                pl_reverse_journal($actorId,$companyId,$bookId,$journalId,pl_web_text($_POST,'date')?:null,$key,pl_web_text($_POST,'reason'));
            } elseif ($action==='activate') {
                pl_activate_open_item_account($actorId,$companyId,$bookId,pl_web_id($_POST,'account_id'),pl_web_text($_POST,'reason'));
            } else { throw new DomainException('Choose a document action.'); }
            pl_notice('Accounting action completed. Review the updated document and balances.');
            pl_redirect(pl_url($path,['id'=>$id?:null]));
        } catch (DomainException $error) {
            $input=array_filter($_POST,static fn(mixed $value):bool=>is_scalar($value));
            if (is_array($_POST['lines']??null)) { $input['lines']=array_values(array_map(static fn(array $line):array=>array_filter($line,static fn(mixed $value):bool=>is_scalar($value)),array_filter($_POST['lines'],'is_array'))); }
            pl_form_failure(pl_url($path,['id'=>$id?:null,'new'=>!$id&&in_array(pl_web_text($_POST,'action'),['save','correct'],true)?'1':null]),$input,$error->getMessage());
        }
    }
    $id=pl_web_id($_GET,'id'); $document=$id?pl_get_ar_document($actorId,$companyId,$bookId,$id):null;
    if ($document && !in_array($document['kind'],[$normalKind,$creditKind],true)) { throw new DomainException('Open this document in its owning module.'); }
    $creditFor=pl_web_id($_GET,'credit_for'); $original=$creditFor?pl_get_ar_document($actorId,$companyId,$bookId,$creditFor):null;
    if ($original && ($original['kind']!==$normalKind || $original['journal_id']===null || $original['payment_status']==='reversed' || bccomp($original['outstanding_fc'],'0',4)<=0)) { throw new DomainException('Choose a posted invoice or bill with an outstanding amount in this module.'); }
    $form=pl_form_state(pl_url($path,['id'=>$id?:null,'new'=>$id?null:(pl_web_text($_GET,'new')?:null)]));
    $originalId=$document['original_document_id']??pl_web_id($form['input'],'original_document_id');
    if ($original===null && $originalId) { $original=pl_get_ar_document($actorId,$companyId,$bookId,(int)$originalId); }
    $all=pl_list_ar_documents($actorId,$companyId,$bookId);
    $documents=array_values(array_filter($all['documents'],fn($d)=>in_array($d['kind'],[$normalKind,$creditKind],true)));
    $report=pl_ar_ap_open_items($actorId,$companyId,$bookId,$receivable?'receivable':'payable',pl_web_text($_GET,'as_of')?:null);
    $settlements=$document&&$document['open_item_id']?DB::query("SELECT e.kind,l.journal_id,l.amount_fc,l.amount_base,j.journal_date AS posting_date,j.source_type,d.id AS credit_document_id,d.kind AS credit_kind,EXISTS(SELECT 1 FROM pl_journals v WHERE v.reversal_of_id=j.id) AS already_reversed FROM pl_open_item_entries e JOIN pl_journal_lines l ON l.id=e.journal_line_id JOIN pl_journals j ON j.id=l.journal_id LEFT JOIN pl_ar_document_revisions r ON r.journal_id=j.id AND r.company_id=e.company_id AND r.book_id=e.book_id LEFT JOIN pl_ar_documents d ON d.id=r.document_id AND d.kind IN ('customer_credit','supplier_credit') WHERE e.item_id=%i AND e.company_id=%i AND e.book_id=%i ORDER BY e.id",$document['open_item_id'],$companyId,$bookId):[];
    pl_render($receivable?'ar':'ap',['title'=>$receivable?'Accounts receivable':'Accounts payable','user'=>$user,'company'=>$company,'path'=>$path,
        'form'=>$form,'document'=>$document,'original'=>$original,'normalKind'=>$normalKind,'creditKind'=>$creditKind,'documents'=>$documents,'report'=>$report,
        'accounts'=>pl_starter_accounts($actorId,$companyId,$bookId),'parties'=>pl_starter_parties($actorId,$companyId,$bookId),
        'priceMode'=>pl_tax_price_mode($actorId,$companyId,$bookId),'products'=>pl_list_inventory_products($actorId,$companyId,$bookId),'taxCodes'=>pl_list_tax_codes($actorId,$companyId,$bookId),'settlements'=>$settlements]);
}
