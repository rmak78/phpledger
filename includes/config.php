<?php
 ini_set('display_errors',1); 
error_reporting(E_ALL);
if(session_id() == '') {
    session_start();
//Load essential PHP Classes
}
  
// Define System CONSTANTS
define('SYSTEM_ENCODING', 'utf8' );
define('BR','</br>');

include_once('db_config.php');
	

 

define('FOLDER_NAME','phpledger');
define('ROOT_PATH', 'E:\xampp\htdocs'.'/'.FOLDER_NAME.'/');
=======
define('FOLDER_NAME','phpledger'); 
define('ROOT_PATH', realpath(dirname(__FILE__)."/../").'/');
>>>>>>> origin/master
define('SITE_ROOT', 'http://'.$_SERVER['HTTP_HOST'].'/'.FOLDER_NAME.'/');
define('DB_PREFIX', 'sa_');


	$_SESSION['is_logged'] = 1; 
	$_SESSION['company_id'] =1 ;
	$_SESSION['user_id'] =1;
	$_SESSION['user_name'] ='test';
	$_SESSION['role_id'] = 1;
	$_SESSION['co_prefix'] = "test_";
	$_SESSION['default_expense_account'] = 1; // get default Expense Account Company
	$_SESSION['DB_PREFIX'] = 'sa_';
 	$_SESSION['company_name'] = 'Sutlej Solutions';
 

	?>
