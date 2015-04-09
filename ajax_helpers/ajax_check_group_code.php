<?php
require_once ( '../tools_header.php');
require_once ("../functions.php");
if(isset($_POST['group_code'])){ // Check for the group_code_availability posted
    $group_code = $_POST['group_code']; // Get the username values
	$group_check = check_coa_group_code_exist($group_code);
	echo $group_check;
}
?>