<?php
// Load Configuration File
include_once('includes/config.php');
include_once('includes/load_classes.php');
// SETUP DB Class ENV 
DB::$user = DB_USER_NAME;
DB::$password = DB_PASS_WORD;
DB::$dbName = DB_DATABASE ;
DB::$encoding = SYSTEM_ENCODING;
include_once('includes/general_functions.php');
include_once('includes/coa_functions.php');
include_once('includes/security.php');
 
