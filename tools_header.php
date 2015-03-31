<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
if(session_id() == '') {
    session_start();
}
  
// define('ROOT_PATH', '');
// define('SITE_ROOT', '');
// define('DB_PREFIX', '');

// $username = "";
// $password = "";
// $hostname = ""; 
// $dbName = "";
define('FOLDER_NAME','phpledger');
define('ROOT_PATH', 'D:\server\www'.'/'.FOLDER_NAME.'/');
define('SITE_ROOT', 'http://'.$_SERVER['HTTP_HOST'].'/'.FOLDER_NAME.'/');
define('DB_PREFIX', 'sa_');

$username = "root";
$password = "";
$hostname = "localhost"; 
$dbName = "phpledger";

require_once (ROOT_PATH.'meekrodb.2.2.class.php');
require_once(ROOT_PATH.'html_table.class.php');

DB::$user = $username;
DB::$password = $password;
DB::$dbName = $dbName;
DB::$host = $hostname;  

?>
