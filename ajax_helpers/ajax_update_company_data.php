<?php
require_once ( '../tools_header.php');
require_once ("../functions.php");

if( isset($_GET['pk']) ) {
//TODO: make this a function so code can be reused for other forms/tables	
	$pk = $_GET['pk'];
	$name = $_GET['name'];
	$value = $_GET['value'];
    $update = DB::update(DB_PREFIX.'companies', array ($name => $value ), "company_id=%s", $pk); 
	
	 echo $update;
//TODO: Make this a function and move to functions.php file	 
 if($update) {
	$time = getDateTime(0,'mySQL');
	$log = DB::update(DB_PREFIX.'companies', array ('last_modified_by' => $_SESSION['user_name'], 'last_modified_on' => $time ), 'company_id=%s', $pk);
	
	}
}

// Update the coa_levels_length
 
if( isset($_POST['company_id']) ) {
	$company_id = $_POST['company_id'];
	$coa_levels_length = $_POST['coa_levels_length'];
	$coa_level_1_length = $_POST['coa_level_1_length'];
	$coa_level_2_length = $_POST['coa_level_2_length'];
	$coa_level_3_length = $_POST['coa_level_3_length'];
	$coa_level_4_length = $_POST['coa_level_4_length'];
	$coa_level_5_length = $_POST['coa_level_5_length'];
	$coa_level_6_length = $_POST['coa_level_6_length'];
	$coa_level_7_length = $_POST['coa_level_7_length'];
	$coa_level_8_length = $_POST['coa_level_8_length'];
	$coa_level_9_length = $_POST['coa_level_9_length'];
	// Truncate the variables that are out of defined coa_levels_length 
	if($coa_levels_length > 0){
		$i=$coa_levels_length+1;
		for(;$i<10;$i++){
			${"coa_level_{$i}_length"}='';
			
		}
	}
	 //Update Data
	 $update = DB::update(DB_PREFIX.'companies', array (
	'coa_levels' => $coa_levels_length,
	'coa_level_1_length' => $coa_level_1_length,
	'coa_level_2_length' => $coa_level_2_length,
	'coa_level_3_length' => $coa_level_3_length,
	'coa_level_4_length' => $coa_level_4_length,
	'coa_level_5_length' => $coa_level_5_length,
	'coa_level_6_length' => $coa_level_6_length,
	'coa_level_7_length' => $coa_level_7_length,
	'coa_level_8_length' => $coa_level_8_length,
	'coa_level_9_length' => $coa_level_9_length
	 ), "company_id=%s", $company_id);
	 if($update){
		 echo "Updated Successfully";
	 }
	 else{
		 echo "Whoops! Fail to update";
	 }
}
 // check authentication of admin

		if( (isset($_POST['admin_name'])) AND (isset($_POST['admin_password'])) )
{ 
	$Admin_name=$_POST['admin_name'];
	$admin_pass=$_POST['admin_password'];
	$company_id=$_SESSION['company_id'];
	echo $Admin_name;
//	$is_logged = DB::queryFirstRow("SELECT * FROM ".DB_PREFIX."companies u WHERE u.`super_admin_user`='".$Admin_name."' AND u.`super_admin_password`='".$admin_pass."' AND u.`company_id`='".$company_id."' AND u.`company_status`='active'");
	
	if($is_logged)
	{	 
	echo "Successfully ";
	}
	 else {

		echo "Fail";
		  }
}
