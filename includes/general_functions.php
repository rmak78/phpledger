<?php
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
		$strform = "j-M-Y g:ia";	
	}
	if ($time == 0 ){	
	$formated_time = date($strform);
	} else {
	$time = strtotime($time);	
	$formated_time  = date($strform, $time);
	}
	return $formated_time;
}

// To Prevent SQL injection
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

function get_domain($url)
{
  $pieces = parse_url($url);
  $domain = isset($pieces['host']) ? $pieces['host'] : '';
  if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
    return $regs['domain'];
  }
  return false;
}


function get_host() {
    if ($host = $_SERVER['HTTP_X_FORWARDED_HOST'])
    {
        $elements = explode(',', $host);
		
        $host = trim(end($elements));
    }
    else
    {
        if (!$host = $_SERVER['HTTP_HOST'])
        {
            if (!$host = $_SERVER['SERVER_NAME'])
            {
                $host = !empty($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
            }
        }
    }
	
    // Remove port number from host
    $host = preg_replace('/:\d+$/', '', $host);
	
    return trim($host);
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
	// get user name
	function get_user_name($user_id) {
// TODO: Fix this function to get user's user_name;
$sql = "SELECT user_name FROM ".DB_PREFIX.$_SESSION['co_prefix']."users WHERE user_id='".$user_id."'";
$user_name = DB::queryFirstField($sql);
	return $user_name;
}
//get db prefix
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
	?>
	 