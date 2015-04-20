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


 
define('FOLDER_NAME','phpledger');
define('ROOT_PATH', 'D:\server\www'.'/'.FOLDER_NAME.'/');
define('SITE_ROOT', 'http://'.$_SERVER['HTTP_HOST'].'/'.FOLDER_NAME.'/');
define('DB_PREFIX', 'sa_');
 
include_once('db.php');
 