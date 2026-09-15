<?php
declare(strict_types=1);

/** Browser adapters use scoped services; templates never decide accounting outcomes. */
function pl_web_starter(int $actorId,int $companyId,int $bookId,array $user,array $company,string $path,string $method): never
{
    pl_require_company_access($actorId,$companyId);
    pl_ledger_book($companyId,$bookId);
    if ($method === 'POST') { pl_web_assert_scope($company,$_POST); }
    $module = match ($path) { '/ar'=>'ar','/ap'=>'ap','/inventory'=>'inventory','/purchasing'=>'purchasing',default=>null };
    if ($module !== null && in_array($module,['ar','ap'],true)) { pl_require_module($actorId,$companyId,$bookId,$module,false); }
    $handler = match ($path) { '/ar','/ap'=>'ar', '/parties'=>'parties', '/inventory'=>'inventory', '/purchasing'=>'purchasing', '/opening-conversion'=>'opening', '/tax'=>'tax', default=>throw new DomainException('Unknown starter page.') };
    require_once __DIR__ . '/starter_' . $handler . '_web_functions.php';
    $function = 'pl_web_starter_' . $handler;
    $function($actorId,$companyId,$bookId,$user,$company,$path,$method);
}

function pl_starter_options(array $rows,string $label='name'): array
{
    $options=[];
    foreach ($rows as $row) { $options[(string)$row['id']] = (isset($row['code']) ? $row['code'].' · ' : '').(string)$row[$label]; }
    return $options;
}

function pl_starter_accounts(int $actorId,int $companyId,int $bookId): array
{
    pl_require_company_access($actorId,$companyId); pl_ledger_book($companyId,$bookId);
    return DB::query('SELECT a.id,a.code,a.name,a.type,a.role,EXISTS(SELECT 1 FROM pl_open_item_accounts o WHERE o.account_id=a.id) AS open_item_managed FROM pl_accounts a WHERE a.company_id=%i AND a.book_id=%i AND a.is_active=1 ORDER BY a.code',$companyId,$bookId);
}

function pl_starter_parties(int $actorId,int $companyId,int $bookId): array
{
    pl_require_company_access($actorId,$companyId); pl_ledger_book($companyId,$bookId);
    return DB::query('SELECT id,legal_name,is_customer,is_vendor FROM pl_parties WHERE company_id=%i ORDER BY legal_name,id',$companyId);
}

function pl_starter_field(string $label,string $name,mixed $value='',string $type='text',bool $required=true): void
{
    static $sequence=0; $id='starter-field-'.++$sequence;
    echo '<label class="field" for="'.pl_e($id).'">'.pl_e($label).'<input id="'.pl_e($id).'" name="'.pl_e($name).'" type="'.pl_e($type).'" value="'.pl_e((string)($value??'')).'"'.($required?' required':'').($type==='text'?' maxlength="500"':'').'></label>';
}

function pl_starter_select(string $label,string $name,array $options,mixed $value='',bool $required=true): void
{
    static $sequence=0; $id='starter-select-'.++$sequence;
    echo '<label class="field" for="'.pl_e($id).'">'.pl_e($label).'<select id="'.pl_e($id).'" name="'.pl_e($name).'"'.($required?' required':'').'><option value="">Choose…</option>';
    foreach ($options as $key=>$text) { echo '<option value="'.pl_e((string)$key).'"'.((string)$value===(string)$key?' selected':'').'>'.pl_e((string)$text).'</option>'; }
    echo '</select></label>';
}

function pl_starter_hidden(string $name,mixed $value): void
{
    echo '<input type="hidden" name="'.pl_e($name).'" value="'.pl_e((string)$value).'">';
}

function pl_starter_form(array $company,string $action,array $fields=[]): void
{
    echo pl_csrf_field().pl_scope_fields($company);
    pl_starter_hidden('action',$action);
    pl_starter_hidden('request_key',$fields['request_key']??bin2hex(random_bytes(20)));
    foreach ($fields as $name=>$value) { if ($name!=='request_key') { pl_starter_hidden($name,$value); } }
}

function pl_starter_lines(array $input): array
{
    $rows = $input['lines'] ?? [];
    if (!is_array($rows) || count($rows)>100) { throw new DomainException('Use up to 100 document lines.'); }
    $result=[];
    foreach ($rows as $row) {
        if (!is_array($row)) { throw new DomainException('Invalid document line.'); }
        if (pl_web_text($row,'description')==='' && pl_web_text($row,'quantity')==='' && pl_web_id($row,'product_id')===0) { continue; }
        $result[]=['description'=>pl_web_text($row,'description'),'quantity'=>pl_web_text($row,'quantity'),
            'unit_price'=>pl_web_text($row,'unit_price'),'account_id'=>pl_web_id($row,'account_id'),
            'product_id'=>pl_web_id($row,'product_id')?:null,'tax_code_id'=>pl_web_id($row,'tax_code_id')?:null,'original_line_number'=>pl_web_id($row,'original_line_number')?:null];
    }
    return $result;
}

function pl_starter_header(string $title,string $description,array $form,array $company=[]): void
{
    echo '<div class="page-heading"><div><p class="eyebrow">Accounting starter</p><h1>'.pl_e($title).'</h1><p class="muted">'.pl_e($description).'</p></div></div>';
    echo '<nav class="starter-tabs" aria-label="Accounting modules">';
    $actor=pl_current_user_id();
    $visibility=$actor && isset($company['id']) ? pl_company_visibility($actor,(int)$company['id']) : ['show_ar'=>true,'show_ap'=>true];
    foreach (['/ar'=>'Receivables','/ap'=>'Payables','/purchasing'=>'Purchasing','/inventory'=>'Inventory','/parties'=>'Parties','/opening-conversion'=>'Opening debts','/tax'=>'Tax codes'] as $url=>$label) {
        if (($url==='/ar'&&!$visibility['show_ar']) || ($url==='/ap'&&!$visibility['show_ap'])) { continue; }
        if ($actor && isset($company['id'],$company['book_id']) && in_array($url,['/inventory','/purchasing'],true)
            && !pl_module_available($actor,(int)$company['id'],(int)$company['book_id'],substr($url,1))) { continue; }
        echo '<a href="'.pl_e(pl_url($url)).'">'.pl_e($label).'</a>';
    }
    echo '</nav>';
    if ($form['message']!=='') { echo '<div class="alert" role="alert" tabindex="-1" data-form-error>'.pl_e($form['message']).'</div>'; }
}
