 <?php
 
 //Set Default time zone Pakistan, You can change as well
 date_default_timezone_set("Asia/Karachi"); 
 
if(session_id() == '') {
    session_start();
}
/*
Helper Functions in this file:
getDateTime($time = 0, $form = "dtLong")
round2dp($number) 
col_index($string , $line)
getUserRoleID($user_id)
pageAllowed($user_id, $page)
GetFXRate($from_ccy, $to_ccy)
ShowYesNo($pass_value)
ShowTickCross($pass_value)
get_user_name($user_id) 
*/
//Returns Date given in Selected Format
function getDateTime($time = 0, $form = "dtLong") {
	Switch($form) {
		case "dtVLong":
		$strform = "D, jS F, Y g:i:s a (\G\M\T O)";
		break;
		case "dtLong":
		$strform = "D, jS F, Y g:i a";
		break;
		case "dtShort":
		$strform = "jS M, Y g:i a";
		break;
		case "dtMin":
		$strform = "j-n-y G:i";
		break;
		case "dLong":
		$strform = "D, jS F Y";
		break;	
		case "dShort":
		$strform = "j-M-Y";
		break;
		case "dMin":
		$strform = "j-n-y";
		break;
		case "tLong":
		$strform = "G:i:s (\G\M\T O)";
		break;
		case "tShort":
		$strform = "G:i";
		break;
		case "mySQL":
		$strform = "Y-m-d H:i:s";
		break;
		default:
		$strform = "j-M-Y g:i:a";	
	}
	if ($time === 0 ){	
	$formated_time = date($strform);
	} else {
	$time = strtotime($time);	
	$formated_time  = date($strform, $time);
	}
	return $formated_time;
}

function round2dp($number){
		return number_format((float)$number, 2, '.', ',');
	}

function col_index($string , $line){
	
		$found = 0;
		$i = 0;
		while ($found == 0 && $i < count ($line))
		{
			if ($line[$i] == $string)
			{
					$found = 1;
			}
			else
			{
				$i = $i + 1;
			}
		}
		if ($found ==0) return -1;
		else return $i;
		
	}
		
function getUserRoleID($user_id)
	{
		//ToDO: determine user_role_id
		return 1;
	}
	
function pageAllowed($user_id, $page)
	{
	
	//TODO: setup permissions module and write a function here
	
	return 1;
	
	
	
	}





function GetFXRate($from_ccy, $to_ccy)
{

	if($from_ccy == $to_ccy) 
	{
		return 1;
	}
	else
	{
		$today = date('Y-m-j');
		$rate = DB::queryfirstfield("SELECT fx_rate from cb_fx_rate where from_ccy = '".$from_ccy."' and to_ccy = '".$to_ccy."' and last_update = '".$today."'");
		if($rate == "")
		{
			$amount = urlencode(1);
			$from_Currency = urlencode($from_ccy);
			$to_Currency = urlencode($to_ccy);

			$url = "http://www.google.com/finance/converter?a=$amount&from=$from_Currency&to=$to_Currency";

			$ch = curl_init();
			$timeout = 0;
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

			curl_setopt ($ch, CURLOPT_USERAGENT,
					 "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$rawdata = curl_exec($ch);
			curl_close($ch);
			$data = explode('bld>', $rawdata);
			$data = explode($to_Currency, $data[1]);

			$rate =  round($data[0], 2);
			
			DB::query("DELETE from cb_fx_rate where from_ccy = '$from_ccy' and to_ccy = '$to_ccy'");
			DB::query("INSERT INTO cb_fx_rate (from_ccy, to_ccy, fx_rate, last_update) values ('$from_ccy', '$to_ccy',$rate,'$today')");
		}
		return $rate;

	}
	

}


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


function get_from_account_code($id, $type) {
$from_number = 0;
$id = intval($id);
if ($type == 'group'){
$from_number = DB::queryFirstField("SELECT from_account_code FROM ".DB_PREFIX.$_SESSION['co_prefix']."coa_groups WHERE group_id = ".$id);

}  
if ($type == 'account' ){


}
return $from_number;
}

function get_to_account_code($id, $type) {
	$to_number = 0;
	$id = intval($id);
		if ($type == 'group'){
			$to_number = DB::queryFirstField("SELECT to_account_code FROM ".DB_PREFIX.$_SESSION['co_prefix']."coa_groups WHERE group_id = ".$id);

		}  
			if ($type == 'account' ){
		}
	return $to_number;
}

function get_coa_group_type($coa_group_id){
	$type = "";
// Get Group Type from db
$sql = "SELECT * FROM ".DB_PREFIX.$_SESSION['co_prefix']."coa_groups WHERE group_code='".$coa_group_id."'" ;

$group = DB::queryFirstRow($sql);

if ( $group['balance_sheet_group'] == 1 ) {
	$type = "Balance Sheet";
} else if ( $group['pls_group'] == 1 ) {
	$type = "P/L Statement";
} else if ( $group['statistics_only'] == 1 ) {
	$type = "Statistics Only";
} else {
	$type = "UnKnown" ;
}
	return $type;

}
function get_coa_group_side($coa_group_id) {
	$side = "";
	// TODO: To be done by WS.
	$sql = "SELECT * FROM ".DB_PREFIX.$_SESSION['co_prefix']."coa_groups WHERE group_code='".$coa_group_id."'" ;	
		$group = DB::queryFirstRow($sql);
		if ( $group['balance_sheet_group'] == 1 ) {
			$side = "Balance  Side";
} 			else if ( $group['pls_group'] == 1 ) {
				$side = "P/L Statement Side";
}			 else if ( $group['statistics_only'] == 1 ) {
				$side = "N/A";
}			 else {
				$type = "UnKnown" ;
}
		
	return $side;

}


function check_coa_group_code_exist($group_code){
		$sql = "SELECT count(*) FROM ".DB_PREFIX.$_SESSION['co_prefix']."coa_groups WHERE group_code='".$group_code."'" ;	
		$group_exist = DB::queryfirstfield($sql);
			return $group_exist;
		
}


function get_account_level($account_id)	{
	$sql = "SELECT coa_level FROM ".DB_PREFIX.$_SESSION['co_prefix']."coa WHERE account_id='".$account_id."'";
	$level = DB::queryFirstField($sql);
		return $level;
	}
?>