<?php
function ShowYesNo($pass_value) 
{

	if($pass_value==1) return "YES";
	elseif($pass_value==0) return "NO";
	elseif($pass_value==-1) return "UNKNOWN";

}
function ShowTickCross($pass_value) 
{

	if($pass_value==1) return "<!-- <span class='glyphicon glyphicon-ok' ></span> -->YES";
	elseif($pass_value==0) return "<!-- <span class='glyphicon glyphicon-remove' ></span> -->NO";
	elseif($pass_value==-1) return "<!-- <span class='glyphicon glyphicon-question-sign' ></span> -->N/A";

}
function getUserRoleID($user_id)
	{
		//ToDO: determine user_role_id
		return 1;
	}
function get_user_name($user_id) {
// TODO: Fix this function to get user's user_name;
$sql = "SELECT user_name FROM ".DB_PREFIX.$_SESSION['co_prefix']."users WHERE user_id='".$user_id."'";
$user_name = DB::queryFirstField($sql);
	return $user_name;
}
function get_db_co_prefix($company_id) {
	//TODO: get company ID from DB and set the appropriate DB PREFIX
	$sql = "SELECT company_db_prefix FROM ".DB_PREFIX."companies WHERE company_id = ".$company_id;
	$db_co_prefix = DB::queryFirstField($sql);
	if ($db_co_prefix) {
	return $db_co_prefix ;
	} else {
	return '' ;
	}
function get_company_details($company_id) {
	//get company details from DB and outputs an array about company
	$sql = "SELECT * FROM ".DB_PREFIX."companies WHERE company_id = ".$company_id;
	$company = DB::queryFirstRow($sql);
	if ($company) {
	return $company ;
	} else {
	return false ;
	}
}
function attempt_login_user($user_name, $password, $company_id, $superadmin) {
	// build a check here to put appropriate fields in the session
	$is_logged = DB::queryFirstRow("SELECT * FROM ".DB_PREFIX."test_users u WHERE (u.`user_name`='".$user_name."' OR u.`user_email`='".$user_name."') AND u.`password`='".$password."' AND u.`company_id`='".$company_id."' AND u.`user_status`='active'");
	
	if($is_logged){
	$company = get_company_details($company_id);
	$_SESSION['is_logged'] = 1; 
	$_SESSION['company_id'] = $company_id;
	$_SESSION['user_id'] = $is_logged['user_id'];
	$_SESSION['user_name'] = $is_logged['user_name'];
	$_SESSION['role_id'] = 1;
	$_SESSION['co_prefix'] = get_db_co_prefix($company_id);
	$_SESSION['company_name'] = $company['company_name'];
	$_SESSION['default_expense_account'] = 1; // get default Expense Account Company
	return true;
	}
	else
	{
	$prefix = DB_PREFIX;
		$is_company_admin = DB::queryFirstField("SELECT COUNT(*) FROM ".$prefix."companies WHERE super_admin_user = '".$user_name."' AND super_admin_password = '".$password."' " );
		if ($is_company_admin) {
		$company = get_company_details($company_id);
		$_SESSION['is_logged'] = 1; 
		$_SESSION['company_id'] = $company_id;
		$_SESSION['user_id'] = 1;
		$_SESSION['user_name'] = $user_name;
		$_SESSION['role_id'] = 1;
		$_SESSION['co_prefix'] = get_db_co_prefix($company_id);
		$_SESSION['company_name'] = $company['company_name'];
		$_SESSION['default_expense_account'] = 1; // get default Expense Account Company		
		return true;	
		} else {

		return '<h4 style="color:red;">Invalid User Name or Password</h4>';
		}
	}
}
	
	






?>