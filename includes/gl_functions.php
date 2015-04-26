<?php 
function voucher_ref_exists($voucher_ref){
										$sql = "SELECT count(*) FROM ".DB_PREFIX.$_SESSION['co_prefix']."voucher_expense 
										WHERE voucher_ref_no='".$voucher_ref."'" ;	
										$voucher_exist = DB::queryFirstField($sql);
											if ($voucher_exist > 0){
													return true;
											} 
												else {
													return false;
												}
										}
function create_new_voucher(
							  $voucher_ref
							, $voucher_date							
							, $voucher_description
							, $voucher_paid_from_account
							 ) 
				{
if (voucher_ref_exists($voucher_ref)){
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
					if($new_voucher_id > 0) {
							DB::update( DB_PREFIX.$_SESSION['co_prefix'].'coa', array(), "voucher_id=%s", $new_voucher_id);
								}
					return $new_coa_id;
				 	}	
					else {
						return "0";
							}
	       }
				
	

?>