
<?php 
if(isset($_POST['edit_voucher']))
{
	$voucher_ref = ($_POST['voucher_ref']);
	$voucher_date = getDateTime($_POST['voucher_date'], 'mySQL');
	$voucher_description = $_POST['voucher_description'];
	$voucher_ids= $_POST['voucher_ids'];
	  
   if($voucher_ids<>"")
	{
	 DB::update(DB_PREFIX.$_SESSION['co_prefix'].'voucher_expense', array(
	 	'voucher_ref_no' 			=>  $voucher_ref,	
            'voucher_date' 	 		  => $voucher_date,
            'voucher description' 	  =>   $voucher_description
	 	),
		"voucher_id=%s", $voucher_ids
		);
	}

}
?>