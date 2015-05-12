<?php
$account_group ="";
$parent_account_id = "";
$account_type = "";
$account_code = "";
$account_desc_short = "";
$account_desc_long = "";
$code_exists = 0;
$desc_exists = 0;
if(isset($_POST['account_group'])){
	$account_group = $_POST['account_group'];
}
if(isset($_POST['parent_account'])){
	$parent_account_id = $_POST['parent_account'];
}
if(isset($_POST['account_type'])){
	$account_type = $_POST['account_type'];
}
if(isset($_POST['account_code'])){
	$account_code = $_POST['account_code'];
}
if(isset($_POST['account_desc_short'])){
	$account_desc_short = $_POST['account_desc_short'];
}
if(isset($_POST['account_desc_long'])){
	$account_desc_long = $_POST['account_desc_long'];
}

//check if account code already exists
if (account_code_exists($account_code)){
	$code_exists  = 1;
	
}
	//check if account desc already exists
if (account_desc_exists($account_desc_short)){
	$desc_exists = 1;
	
} 

if (($code_exists <> 1) AND ($desc_exists <> 1) ) {
	// add record to database
$update_account =	update_coa(  		
					$account_code
					, $account_group
					, $account_desc_short
					, $account_desc_long
					, $parent_account_id
					, $account_type
					, $parent_account_id
					); 
	echo $update_account;
	if ($update_account > 0) {
		echo '<script>window.location.replace("'.SITE_ROOT.'?route=modules/gl/setup/coa/list_coa");</script>';
	} else {
		echo '<script>window.location.replace("'.SITE_ROOT.'?route=modules/gl/setup/coa/add_coa");</script>';
	}

}
 


?>