<?php
declare(strict_types=1);

/** Country context is explicit; currency and UI locale never determine legal identity. */
function pl_party_country(mixed $value): string
{
    if (!is_string($value) || !preg_match('/^[A-Z]{2}$/D', $value)) { throw new DomainException('Use an explicit two-letter country code.'); }
    return $value;
}

function pl_party_phone(mixed $value): string
{
    $value = pl_ledger_text($value, 'Phone', 40);
    $normalized = preg_replace('/[ ().-]/', '', $value);
    if (!is_string($normalized) || !preg_match('/^\+[1-9][0-9]{6,14}$/D', $normalized)) {
        throw new DomainException('Enter an international phone number beginning with + and its country calling code.');
    }
    return $normalized;
}

function pl_party_json(mixed $value, string $label): string
{
    if (!is_array($value) || count($value) > 100) { throw new DomainException($label . ' must be a list of at most 100 records.'); }
    $encoded = json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    if (strlen($encoded) > 32768) { throw new DomainException($label . ' is too large.'); }
    return $encoded;
}

function pl_party_normalize(array $input): array
{
    foreach (['status','tags','bank_accounts','attachments','opening_balance'] as $reserved) {
        if (array_key_exists($reserved, $input)) { throw new DomainException('This foundation does not accept ' . $reserved . ' changes.'); }
    }
    foreach (['is_customer','is_vendor'] as $flag) {
        if (!is_bool($input[$flag] ?? null)) { throw new DomainException('Choose the party customer and vendor roles.'); }
    }
    if (!$input['is_customer'] && !$input['is_vendor']) { throw new DomainException('A party must have at least one customer or vendor role.'); }
    $party = [
        'legal_name' => pl_ledger_text($input['legal_name'] ?? null, 'Legal name', 160),
        'trading_name' => pl_ledger_text($input['trading_name'] ?? '', 'Trading name', 160, false),
        'entity_type' => pl_ledger_text($input['entity_type'] ?? null, 'Entity type', 60),
        'country_code' => pl_party_country($input['country_code'] ?? null),
        'is_customer' => (int) $input['is_customer'], 'is_vendor' => (int) $input['is_vendor'],
        'linked_entity_id' => $input['linked_entity_id'] ?? null, 'assigned_owner_id' => $input['assigned_owner_id'] ?? null,
    ];
    foreach (['linked_entity_id','assigned_owner_id'] as $field) {
        if ($party[$field] !== null && (!is_int($party[$field]) || $party[$field] < 1)) { throw new DomainException('Invalid party relationship.'); }
    }
    $identifiers = [];
    if (!is_array($input['identifiers'] ?? [])) { throw new DomainException('Identifiers must be a list.'); }
    foreach ($input['identifiers'] ?? [] as $item) {
        if (!is_array($item)) { throw new DomainException('Invalid party identifier.'); }
        $scheme = strtoupper(pl_ledger_text($item['scheme'] ?? null, 'Identifier scheme', 40));
        if (!preg_match('/^[A-Z][A-Z0-9_]{0,39}$/D', $scheme)) { throw new DomainException('Invalid identifier scheme.'); }
        $value = pl_ledger_text($item['value'] ?? null, 'Identifier value', 120);
        $normalized = strtoupper((string) preg_replace('/[\s.-]/u', '', $value));
        if (!preg_match('/^[A-Z0-9]{1,120}$/D', $normalized)) { throw new DomainException('Identifier must contain letters or digits with optional separators.'); }
        $identifiers[] = ['country_code' => pl_party_country($item['country_code'] ?? $party['country_code']), 'scheme' => $scheme, 'value' => $value, 'normalized_value' => $normalized];
    }
    if (count($identifiers) > 50) { throw new DomainException('Too many party identifiers.'); }
    usort($identifiers, static fn(array $a, array $b): int => [$a['country_code'],$a['scheme'],$a['normalized_value']] <=> [$b['country_code'],$b['scheme'],$b['normalized_value']]);
    $names = $input['localized_names'] ?? [];
    if (!is_array($names)) { throw new DomainException('Localized names must be a language-to-name map.'); }
    foreach ($names as $language => $name) {
        if (!is_string($language) || !preg_match('/^[a-z]{2,3}(?:-[A-Za-z0-9]{2,8})*$/D', $language)) { throw new DomainException('Invalid name language tag.'); }
        $names[$language] = pl_ledger_text($name, 'Localized name', 160);
    }
    ksort($names);
    $addresses = $input['addresses'] ?? [];
    $registrations = $input['registrations'] ?? [];
    foreach (['addresses' => $addresses, 'registrations' => $registrations] as $kind => $rows) {
        if (!is_array($rows)) { throw new DomainException('Invalid party ' . $kind . '.'); }
        foreach ($rows as $row) {
            if (!is_array($row)) { throw new DomainException('Invalid party detail.'); }
            pl_party_country($row['country_code'] ?? null);
            if ($kind === 'addresses' && !in_array($row['role'] ?? null, ['registered','billing','shipping'], true)) { throw new DomainException('Choose a registered, billing or shipping address role.'); }
            foreach ($row as $value) { if (!is_string($value) || mb_strlen($value) > 500) { throw new DomainException('Party detail fields must be text up to 500 characters.'); } }
        }
    }
    $status = $input['registration_status'] ?? 'unknown';
    if (!in_array($status, ['unknown','registered','unregistered'], true)) { throw new DomainException('Invalid registration status.'); }
    $details = [
        'localized_names' => pl_party_json($names, 'Localized names'), 'registrations' => pl_party_json($registrations, 'Registrations'),
        'addresses' => pl_party_json($addresses, 'Addresses'), 'registration_status' => $status,
        'supply_country_code' => isset($input['supply_country_code']) ? pl_party_country($input['supply_country_code']) : null,
        'supply_subdivision' => pl_ledger_text($input['supply_subdivision'] ?? '', 'Supply subdivision', 100, false),
        'filer_status' => pl_ledger_text($input['filer_status'] ?? 'unknown', 'Filer status', 60),
        'filer_checked_on' => isset($input['filer_checked_on']) ? pl_ledger_date($input['filer_checked_on']) : null,
        'withholding_applicable' => $input['withholding_applicable'] ?? null,
        'exemption_reference' => pl_ledger_text($input['exemption_reference'] ?? '', 'Exemption reference', 160, false),
        'exemption_expires_on' => isset($input['exemption_expires_on']) ? pl_ledger_date($input['exemption_expires_on']) : null,
        'default_tax_treatment' => pl_ledger_text($input['default_tax_treatment'] ?? '', 'Tax treatment', 120, false),
        'notes' => pl_ledger_text($input['notes'] ?? '', 'Notes', 10000, false),
    ];
    if ($details['withholding_applicable'] !== null && !is_bool($details['withholding_applicable'])) { throw new DomainException('Withholding applicability must be a boolean or unknown.'); }
    if ($details['withholding_applicable'] !== null) { $details['withholding_applicable'] = (int) $details['withholding_applicable']; }
    $currency = $input['currency'] ?? null;
    if (!is_string($currency)) { throw new DomainException('Choose a supported party currency.'); }
    $currency = pl_currency_code($currency);
    $financial = ['currency' => $currency, 'payment_terms' => pl_ledger_text($input['payment_terms'] ?? '', 'Payment terms', 500, false),
        'credit_limit' => isset($input['credit_limit']) ? pl_amount($input['credit_limit']) : null,
        'ar_account_id' => $input['ar_account_id'] ?? null, 'ap_account_id' => $input['ap_account_id'] ?? null];
    foreach (['ar_account_id','ap_account_id'] as $field) {
        if ($financial[$field] !== null && (!is_int($financial[$field]) || $financial[$field] < 1)) { throw new DomainException('Invalid control account override.'); }
    }
    return ['party' => $party, 'details' => $details, 'identifiers' => $identifiers, 'financial' => $financial];
}

function pl_get_party(int $actorId, int $companyId, int $bookId, int $partyId): array
{
    pl_require_company_access($actorId, $companyId);
    pl_ledger_book($companyId, $bookId);
    $row = DB::queryFirstRow('SELECT * FROM pl_parties WHERE id=%i AND company_id=%i FOR SHARE', $partyId, $companyId);
    if (!$row) { throw new DomainException('Party is unavailable in this company.'); }
    $row['id'] = (int) $row['id']; $row['revision'] = (int) $row['revision'];
    $row['details'] = DB::queryFirstRow('SELECT * FROM pl_party_details WHERE party_id=%i AND company_id=%i FOR SHARE', $partyId, $companyId);
    foreach (['localized_names','registrations','addresses'] as $field) { $row['details'][$field] = json_decode($row['details'][$field], true, 512, JSON_THROW_ON_ERROR); }
    $row['identifiers'] = DB::query('SELECT country_code,scheme,value FROM pl_party_identifiers WHERE party_id=%i AND company_id=%i ORDER BY id FOR SHARE', $partyId, $companyId);
    $row['financial'] = DB::queryFirstRow('SELECT currency,payment_terms,credit_limit,ar_account_id,ap_account_id FROM pl_party_financial_profiles WHERE party_id=%i AND company_id=%i AND book_id=%i FOR SHARE', $partyId, $companyId, $bookId);
    return $row;
}

function pl_party_request(int $companyId, string $key, string $hash): ?array
{
    $prior = DB::queryFirstRow('SELECT * FROM pl_party_actions WHERE company_id=%i AND request_key=%s FOR UPDATE', $companyId, $key);
    if ($prior && !hash_equals((string) $prior['payload_hash'], $hash)) { throw new DomainException('Party request identity was already used for different content.'); }
    return $prior ?: null;
}

/** Internal master-data entry; bank changes, tags, uploads and vetting are deliberately absent. */
function pl_save_party(int $actorId, int $companyId, int $bookId, array $input, ?int $id = null, ?int $revision = null): array
{
    pl_demo_require_setup_action();
    $data = pl_party_normalize($input);
    $key = pl_request_key(pl_ledger_text($input['request_key'] ?? null, 'Request key', 128));
    $reason = pl_ledger_text($input['reason'] ?? null, 'Reason', 500);
    $hash = hash('sha256', json_encode([$actorId,$companyId,$bookId,$id,$revision,$data,$reason], JSON_THROW_ON_ERROR));
    return pl_ledger_transaction(function () use ($actorId,$companyId,$bookId,$id,$revision,$data,$key,$reason,$hash): array {
        pl_require_company_access($actorId,$companyId,true); pl_ledger_book($companyId,$bookId,true);
        // Company lock serializes identity/phone decisions across future books as well.
        DB::queryFirstField('SELECT id FROM pl_companies WHERE id=%i FOR UPDATE', $companyId);
        $prior = pl_party_request($companyId,$key,$hash);
        if ($prior) { return ['id'=>(int)$prior['party_id'],'revision'=>(int)$prior['result_revision'],'request_key'=>$key]; }
        if ($id !== null) {
            $before = pl_get_party($actorId,$companyId,$bookId,$id);
            if ($revision !== $before['revision']) { throw new DomainException('Party changed; reload the current revision.'); }
        }
        if ($data['party']['assigned_owner_id'] !== null) { pl_require_company_access($data['party']['assigned_owner_id'],$companyId); }
        if ($data['party']['linked_entity_id'] !== null) { pl_require_company_access($actorId,$data['party']['linked_entity_id']); }
        foreach (['ar_account_id'=>'receivables','ap_account_id'=>'payables'] as $field=>$role) {
            if ($data['financial'][$field] !== null) {
                $account=pl_get_account($actorId,$companyId,$bookId,$data['financial'][$field]);
                if (!$account['is_active'] || $account['role'] !== $role) { throw new DomainException('Choose an active control account with the matching receivable or payable role.'); }
            }
        }
        $seen=[];
        foreach ($data['identifiers'] as $identifier) {
            $identity=$identifier['country_code'].':'.$identifier['scheme'].':'.$identifier['normalized_value'];
            if (isset($seen[$identity])) { throw new DomainException('Identifier is repeated in this party.'); } $seen[$identity]=true;
            $other=DB::queryFirstField('SELECT party_id FROM pl_party_identifiers WHERE company_id=%i AND country_code=%s AND scheme=%s AND normalized_value=%s FOR UPDATE',$companyId,$identifier['country_code'],$identifier['scheme'],$identifier['normalized_value']);
            if ($other !== null && $other !== false && (int)$other !== $id) { throw new DomainException('This legal identifier already belongs to another party in the company.'); }
        }
        $next=$id === null ? 1 : $revision+1;
        if ($id === null) {
            DB::insert('pl_parties',$data['party']+['company_id'=>$companyId,'created_by'=>$actorId]); $id=(int)DB::insertId();
            DB::insert('pl_party_status_history',['party_id'=>$id,'company_id'=>$companyId,'from_status'=>null,'to_status'=>'draft','actor_id'=>$actorId,'reason'=>$reason]);
            DB::insert('pl_party_details',$data['details']+['party_id'=>$id,'company_id'=>$companyId]);
            DB::insert('pl_party_financial_profiles',$data['financial']+['party_id'=>$id,'company_id'=>$companyId,'book_id'=>$bookId]);
        } else {
            DB::update('pl_parties',$data['party']+['revision'=>$next,'updated_at'=>gmdate('Y-m-d H:i:s')],'id=%i AND company_id=%i',$id,$companyId);
            DB::update('pl_party_details',$data['details'],'party_id=%i AND company_id=%i',$id,$companyId);
            DB::update('pl_party_financial_profiles',$data['financial'],'party_id=%i AND company_id=%i AND book_id=%i',$id,$companyId,$bookId);
            DB::delete('pl_party_identifiers','party_id=%i AND company_id=%i',$id,$companyId);
        }
        foreach ($data['identifiers'] as $identifier) { DB::insert('pl_party_identifiers',$identifier+['party_id'=>$id,'company_id'=>$companyId]); }
        DB::insert('pl_party_actions',['company_id'=>$companyId,'book_id'=>$bookId,'party_id'=>$id,'actor_id'=>$actorId,'request_key'=>$key,'payload_hash'=>$hash,'action'=>$next===1?'created':'updated','result_revision'=>$next,'reason'=>$reason]);
        pl_enqueue_outbound_event($companyId,$bookId,$next===1?'party.created':'party.updated','party',$id,$next,'party:'.hash('sha256',$key));
        return ['id'=>$id,'revision'=>$next,'request_key'=>$key];
    });
}

function pl_get_contact(int $actorId,int $companyId,int $bookId,int $partyId,int $contactId): array
{
    pl_get_party($actorId,$companyId,$bookId,$partyId);
    $row=DB::queryFirstRow('SELECT * FROM pl_contacts WHERE id=%i AND party_id=%i AND company_id=%i FOR SHARE',$contactId,$partyId,$companyId);
    if (!$row) { throw new DomainException('Contact is unavailable in this party.'); }
    $row['id']=(int)$row['id']; $row['revision']=(int)$row['revision'];
    $row['phones']=DB::query('SELECT phone,is_whatsapp FROM pl_contact_phones WHERE contact_id=%i AND company_id=%i ORDER BY id FOR SHARE',$contactId,$companyId);
    return $row;
}

function pl_save_contact(int $actorId,int $companyId,int $bookId,int $partyId,array $input,?int $id=null,?int $revision=null): array
{
    pl_demo_require_setup_action();
    $data=['name'=>pl_ledger_text($input['name']??null,'Contact name',160),'designation'=>pl_ledger_text($input['designation']??'','Designation',120,false),
        'email'=>pl_ledger_text($input['email']??'','Email',254,false),'language_preference'=>$input['language_preference']??'en',
        'role'=>$input['role']??null,'is_primary'=>$input['is_primary']??false,'notes'=>pl_ledger_text($input['notes']??'','Notes',10000,false)];
    if ($data['email']!=='' && !filter_var($data['email'],FILTER_VALIDATE_EMAIL)) { throw new DomainException('Enter a valid contact email.'); }
    if (!is_string($data['language_preference']) || !preg_match('/^[a-z]{2,3}(?:-[A-Za-z0-9]{2,8})*$/D',$data['language_preference']) || strlen($data['language_preference'])>35) { throw new DomainException('Invalid language preference.'); }
    if (!in_array($data['role'],['billing','receiving','authorised_signatory','owner'],true) || !is_bool($data['is_primary'])) { throw new DomainException('Invalid contact role or primary flag.'); }
    $data['is_primary']=(int)$data['is_primary'];
    $phones=[];
    if (!is_array($input['phones']??[])) { throw new DomainException('Contact phones must be a list.'); }
    foreach ($input['phones']??[] as $phone) {
        if (!is_array($phone) || !is_bool($phone['is_whatsapp']??false)) { throw new DomainException('Invalid contact phone.'); }
        $normalized=pl_party_phone($phone['phone']??null);
        if (isset($phones[$normalized])) { throw new DomainException('Contact phone is repeated.'); }
        $phones[$normalized]=['phone'=>trim($phone['phone']),'normalized_phone'=>$normalized,'is_whatsapp'=>(int)($phone['is_whatsapp']??false)];
    }
    if (count($phones)>20) { throw new DomainException('Too many contact phones.'); } ksort($phones);
    $ack=$input['acknowledge_phone_duplicates']??false;
    if (!is_bool($ack)) { throw new DomainException('Phone duplicate acknowledgement must be explicit.'); }
    $duplicateReason=pl_ledger_text($input['duplicate_reason']??'','Duplicate reason',500,false);
    $reason=pl_ledger_text($input['reason']??null,'Reason',500);
    $key=pl_request_key(pl_ledger_text($input['request_key']??null,'Request key',128));
    $hash=hash('sha256',json_encode([$actorId,$companyId,$bookId,$partyId,$id,$revision,$data,$phones,$ack,$duplicateReason,$reason],JSON_THROW_ON_ERROR));
    return pl_ledger_transaction(function () use($actorId,$companyId,$bookId,$partyId,$id,$revision,$data,$phones,$ack,$duplicateReason,$reason,$key,$hash):array {
        pl_require_company_access($actorId,$companyId,true); pl_ledger_book($companyId,$bookId,true);
        DB::queryFirstField('SELECT id FROM pl_companies WHERE id=%i FOR UPDATE',$companyId);
        $party=pl_get_party($actorId,$companyId,$bookId,$partyId);
        $prior=pl_party_request($companyId,$key,$hash);
        if($prior){return ['id'=>(int)$prior['contact_id'],'revision'=>(int)$prior['result_revision'],'request_key'=>$key];}
        if($id!==null && pl_get_contact($actorId,$companyId,$bookId,$partyId,$id)['revision']!==$revision){throw new DomainException('Contact changed; reload the current revision.');}
        if($data['is_primary'] && DB::queryFirstField('SELECT id FROM pl_contacts WHERE company_id=%i AND party_id=%i AND is_primary=1 AND id<>%i FOR UPDATE',$companyId,$partyId,$id??0)){throw new DomainException('This party already has a primary contact. Clear that flag explicitly first.');}
        $duplicates=false;
        foreach($phones as $phone){
            if(DB::queryFirstField('SELECT id FROM pl_contact_phones WHERE company_id=%i AND normalized_phone=%s AND contact_id<>%i LIMIT 1 FOR UPDATE',$companyId,$phone['normalized_phone'],$id??0)){$duplicates=true;}
        }
        if($duplicates && (!$ack || $duplicateReason==='')){throw new DomainException('Phone duplicates exist in this company. Explicit acknowledgement and a reason are required; no records were merged.');}
        $next=$id===null?1:$revision+1;
        if($id===null){DB::insert('pl_contacts',$data+['party_id'=>$partyId,'company_id'=>$companyId,'created_by'=>$actorId]);$id=(int)DB::insertId();}
        else{DB::update('pl_contacts',$data+['revision'=>$next],'id=%i AND party_id=%i AND company_id=%i',$id,$partyId,$companyId);DB::delete('pl_contact_phones','contact_id=%i AND company_id=%i',$id,$companyId);}
        foreach($phones as $phone){DB::insert('pl_contact_phones',$phone+['contact_id'=>$id,'party_id'=>$partyId,'company_id'=>$companyId]);}
        DB::insert('pl_party_actions',['company_id'=>$companyId,'book_id'=>$bookId,'party_id'=>$partyId,'contact_id'=>$id,'actor_id'=>$actorId,'request_key'=>$key,'payload_hash'=>$hash,'action'=>'contact_saved','result_revision'=>$next,'reason'=>$reason,'phone_duplicate_acknowledged'=>(int)$duplicates,'duplicate_reason'=>$duplicates?$duplicateReason:'']);
        DB::update('pl_parties',['revision'=>$party['revision']+1,'updated_at'=>gmdate('Y-m-d H:i:s')],'id=%i AND company_id=%i',$partyId,$companyId);
        pl_enqueue_outbound_event($companyId,$bookId,'party.updated','party',$partyId,$party['revision']+1,'contact:'.hash('sha256',$key));
        return ['id'=>$id,'revision'=>$next,'request_key'=>$key];
    });
}
