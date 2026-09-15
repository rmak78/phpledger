<?php
declare(strict_types=1);

function pl_web_starter_parties(int $actorId,int $companyId,int $bookId,array $user,array $company,string $path,string $method): never
{
    if ($method==='POST') {
        $id=pl_web_id($_POST,'id');
        try {
            $action=pl_web_text($_POST,'action');
            if ($action==='save') {
                $before=$id?pl_get_party($actorId,$companyId,$bookId,$id):null;
                $input=$before ? ($before['details']+($before['financial']??[])+['identifiers'=>$before['identifiers'],
                    'linked_entity_id'=>$before['linked_entity_id']===null?null:(int)$before['linked_entity_id'],
                    'assigned_owner_id'=>$before['assigned_owner_id']===null?null:(int)$before['assigned_owner_id']]) : [];
                unset($input['party_id'],$input['company_id']);
                if (isset($input['withholding_applicable'])) { $input['withholding_applicable']=(bool)$input['withholding_applicable']; }
                foreach (['ar_account_id','ap_account_id'] as $field) { $input[$field]=pl_web_id($_POST,$field)?:null; }
                $input=array_replace($input,['legal_name'=>pl_web_text($_POST,'legal_name'),'trading_name'=>pl_web_text($_POST,'trading_name'),
                    'entity_type'=>pl_web_text($_POST,'entity_type'),'country_code'=>strtoupper(pl_web_text($_POST,'country_code')),
                    'is_customer'=>isset($_POST['is_customer']),'is_vendor'=>isset($_POST['is_vendor']),
                    'currency'=>strtoupper(pl_web_text($_POST,'currency')),'payment_terms'=>pl_web_text($_POST,'payment_terms'),
                    'request_key'=>pl_web_text($_POST,'request_key'),'reason'=>pl_web_text($_POST,'reason')]);
                if (!$before && pl_web_text($_POST,'identifier_value')!=='') {
                    $input['identifiers']=[['country_code'=>$input['country_code'],'scheme'=>strtoupper(pl_web_text($_POST,'identifier_scheme')),'value'=>pl_web_text($_POST,'identifier_value')]];
                }
                $saved=pl_save_party($actorId,$companyId,$bookId,$input,$id?:null,$id?pl_web_id($_POST,'revision'):null);
                $id=$saved['id'];
            } elseif ($action==='contact') {
                pl_save_contact($actorId,$companyId,$bookId,$id,['name'=>pl_web_text($_POST,'name'),'email'=>pl_web_text($_POST,'email'),
                    'role'=>pl_web_text($_POST,'role'),'is_primary'=>isset($_POST['is_primary']),
                    'phones'=>pl_web_text($_POST,'phone')!==''?[['phone'=>pl_web_text($_POST,'phone'),'is_whatsapp'=>false]]:[],
                    'acknowledge_phone_duplicates'=>isset($_POST['acknowledge_phone_duplicates']),'duplicate_reason'=>pl_web_text($_POST,'duplicate_reason'),
                    'request_key'=>pl_web_text($_POST,'request_key'),'reason'=>pl_web_text($_POST,'reason')]);
            } else { throw new DomainException('Choose a party action.'); }
            pl_notice('Party information saved.'); pl_redirect(pl_url($path,['id'=>$id]));
        } catch (DomainException $error) { pl_form_failure(pl_url($path,['id'=>$id?:null,'new'=>$id?null:'1']),array_filter($_POST,static fn(mixed $value):bool=>is_scalar($value)),$error->getMessage()); }
    }
    $id=pl_web_id($_GET,'id'); $party=$id?pl_get_party($actorId,$companyId,$bookId,$id):null;
    $form=pl_form_state(pl_url($path,['id'=>$id?:null,'new'=>$id?null:(pl_web_text($_GET,'new')?:null)]));
    pl_render('parties',['title'=>'Customers and vendors','user'=>$user,'company'=>$company,'form'=>$form,'party'=>$party,
        'rows'=>pl_starter_parties($actorId,$companyId,$bookId),'accounts'=>pl_starter_accounts($actorId,$companyId,$bookId),
        'contacts'=>$party?DB::query('SELECT c.id,c.name,c.email,c.role,c.is_primary,GROUP_CONCAT(p.phone ORDER BY p.id SEPARATOR ", ") AS phones FROM pl_contacts c LEFT JOIN pl_contact_phones p ON p.contact_id=c.id AND p.company_id=c.company_id WHERE c.company_id=%i AND c.party_id=%i GROUP BY c.id,c.name,c.email,c.role,c.is_primary ORDER BY c.name',$companyId,$id):[]]);
}
