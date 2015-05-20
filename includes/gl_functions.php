<?php 
function voucher_ref_exists($voucher_ref){
				$sql = "SELECT count(*) FROM ".DB_PREFIX.$_SESSION['co_prefix']."journal_vouchers 
				WHERE voucher_ref_no='".$voucher_ref."'" ;	
				$journal_voucher_exists = DB::queryFirstField($sql);
 
				$sql2 = "SELECT count(*) FROM ".DB_PREFIX.$_SESSION['co_prefix']."voucher_expense 
				WHERE voucher_ref_no='".$voucher_ref."'" ;	
				$expense_voucher_exists = DB::queryFirstField($sql2);
 
		if (($journal_voucher_exists == 0) AND ($expense_voucher_exists == 0) ){
					return false;
					} else {
					return true;
					}
}
/********************** JOURNAL VOUCHER FUNCTIONS ******************/

function create_new_journal_voucher(
							  $voucher_ref
							, $voucher_date							
							, $voucher_description
							)
							{
$voucher_exist = 0;								
if (voucher_ref_exists($voucher_ref)){
	$voucher_exist = 1;
}					
if($voucher_exist == 0){
					$now= getDateTime(0,'mySQL');
					$insert = DB::Insert(DB_PREFIX.$_SESSION['co_prefix'].'journal_vouchers', 
								array(			
										'voucher_ref_no' 		=>  $voucher_ref,	
										'voucher_date' 	 		=>  getDateTime($voucher_date,"mySQL"),
										'voucher description' 	=>  $voucher_description,
										'created_on'			=>  $now,
										'created_by'			=>  $_SESSION['user_name'],
										'last_modified_by'		=>  $_SESSION['user_name'],
										'voucher_status'		=>	'draft'
									));
					$new_voucher_id =DB::insertId();
					if($new_voucher_id) { 
					return $new_voucher_id;
					} else {
					return 0;	
					}
	       }
}			
	

/********************** EXPENSE VOUCHER FUNCTIONS ******************/

function create_new_expense_voucher(
							  $voucher_ref
							, $voucher_date							
							, $voucher_description
							, $voucher_paid_from_account
							 ) 
{
	if (expense_voucher_ref_exists($voucher_ref)){
		$voucher_exist = 1;
		
	}					
	if($voucher_exist<>1){
					$now= getDateTime(0,'mySQL');
					$insert = DB::Insert(DB_PREFIX.$_SESSION['co_prefix'].'voucher_expense', 
								array(			
										'voucher_ref_no' 		=>  $voucher_ref,	
										'voucher_date' 	 		=>  $voucher_date,
										'voucher description' 	=>  $voucher_description,
										'petty_cash_account' 	=>  $voucher_paid_from_account,
										'created_on'			=>  $now,
										'created_by'			=>  $_SESSION['user_name'],
										'voucher_status'		=>	'Draft'
									));
					$new_voucher_id =DB::insertId();
					if($new_voucher_id) { 
					return $new_voucher_id;
					} else {
					return 0;	
					}
	       }
}		
/********************** JOURNAL VOUCHER DETAIL FUNCTIONS ******************/	
function journal_voucher_detail(
							  $voucher_id
							, $voucher_date
							, $account_id	
							, $entry_desc
							, $debit_amount
							, $credit_amount
							 ) 
{				
					$now= getDateTime(0,'mySQL');
					$insert = DB::Insert(DB_PREFIX.$_SESSION['co_prefix'].'journal_voucher_details', 
								array(			
										'voucher_id' 			=>  $voucher_id,	
										'voucher_date' 	 		=>  $voucher_date,
										'account_id' 			=>  $account_id,
										'entry_description' 	=>  $entry_desc,
										'debit_amount' 			=>  $debit_amount,	
										'credit_amount' 		=>  $credit_amount,										
										'created_on'			=>  $now,
										'created_by'			=>  $_SESSION['user_name'],
										'voucher_detail_status'	=>	'Draft'
									));
					$voucher_detail_id =DB::insertId();
					if($voucher_detail_id) { 
					return $voucher_detail_id;
					return $voucher_id;
					} else {
					return 0;	
					}
	       }
		   
/****************************UPDATE JOURNAL VOUCHER DETAIL FUNCTIONS****************************/		   
function update_journal_voucher_detail(
							  $voucher_id
							, $voucher_date
							, $account_id	
							, $entry_desc
							, $debit_amount
							, $credit_amount
							, $voucher_detail_id 
							 ) 
{				
					$now= getDateTime(0,'mySQL');
					$insert = DB::UPDATE(DB_PREFIX.$_SESSION['co_prefix'].'journal_voucher_details', 
								array(			
										'voucher_id' 			=>  $voucher_id,	
										'voucher_date' 	 		=>  $voucher_date,
										'account_id' 			=>  $account_id,
										'entry_description' 	=>  $entry_desc,
										'debit_amount' 			=>  $debit_amount,	
										'credit_amount' 		=>  $credit_amount,										
										'created_on'			=>  $now,
										'created_by'			=>  $_SESSION['user_name'],
										'voucher_detail_status'	=>	'Draft'
									)
									, "voucher_detail_id =%s", $voucher_detail_id 
									);
					$voucher_detail_id =DB::insertId();
					if($voucher_detail_id) { 
					return $voucher_detail_id;
					return $voucher_id;
					} else {
					return 0;	
					}
	       }
?>