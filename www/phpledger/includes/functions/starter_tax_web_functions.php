<?php
declare(strict_types=1);

function pl_web_starter_tax(int $actorId,int $companyId,int $bookId,array $user,array $company,string $path,string $method): never
{
    if ($method==='POST') {
        try {
            $action=pl_web_text($_POST,'action');
            if ($action==='settings') {
                pl_set_tax_price_mode($actorId,$companyId,$bookId,pl_web_text($_POST,'price_mode'),pl_web_id($_POST,'revision'),pl_web_text($_POST,'reason'),pl_web_text($_POST,'request_key'));
            } elseif ($action==='code') {
                pl_create_tax_code($actorId,$companyId,$bookId,['code'=>pl_web_text($_POST,'code'),'name'=>pl_web_text($_POST,'name'),
                    'treatment'=>pl_web_text($_POST,'treatment'),'sales_account_id'=>pl_web_id($_POST,'sales_account_id'),
                    'purchase_account_id'=>pl_web_id($_POST,'purchase_account_id'),'reason'=>pl_web_text($_POST,'reason'),'idempotency_key'=>pl_web_text($_POST,'request_key')]);
            } elseif ($action==='rate') {
                pl_enter_tax_rate($actorId,$companyId,$bookId,['tax_code_id'=>pl_web_id($_POST,'tax_code_id'),'effective_from'=>pl_web_text($_POST,'effective_from'),
                    'percentage'=>pl_web_text($_POST,'percentage'),'reason'=>pl_web_text($_POST,'reason'),'idempotency_key'=>pl_web_text($_POST,'request_key')]);
            } else { throw new DomainException('Choose a tax configuration action.'); }
            pl_notice('Tax configuration recorded. Posted documents retain their original tax snapshots.'); pl_redirect($path);
        } catch (DomainException $error) {
            $input=array_map(static fn(mixed $value): string=>is_scalar($value)?(string)$value:'',$_POST);
            pl_form_failure(pl_url($path),$input,$error->getMessage());
        }
    }
    pl_render('tax',['title'=>'Tax codes and rates','user'=>$user,'company'=>$company,'form'=>pl_form_state(pl_url($path)),
        'settings'=>pl_tax_settings($actorId,$companyId,$bookId),'codes'=>pl_list_tax_codes($actorId,$companyId,$bookId),'accounts'=>pl_starter_accounts($actorId,$companyId,$bookId),
        'rates'=>DB::query('SELECT r.*,c.code FROM pl_tax_rates r JOIN pl_tax_codes c ON c.id=r.tax_code_id WHERE r.company_id=%i AND r.book_id=%i ORDER BY r.effective_from DESC,r.id DESC',$companyId,$bookId)]);
}
