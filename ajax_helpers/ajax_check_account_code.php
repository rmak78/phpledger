<?php
require_once ( '../tools_header.php');
require_once ("../functions.php");
if(isset($_POST['account_code'])){ // Check for the group_code_availability posted
    $account_code = $_POST['account_code']; // Get the username values
	$account_check = check_coa_account_code_exist($account_code);
	echo $account_check;
}
?>