<?php
require_once ( '../tools_header.php');
require_once ("../functions.php");

if( isset($_GET['pk']) ) {
//TODO: make this a function so code can be reused for other forms/tables	
	$pk = $_GET['pk'];
	$name = $_GET['name'];
	$value = $_GET['value'];
    $update = DB::update(DB_PREFIX.$_SESSION['co_prefix'].'voucher_expense', array ($name => $value ), "voucher_id%s", $pk); 
	
	 echo $update;
//TODO: Make this a function and move to functions.php file	 
 if($update) {
	$time = getDateTime(0,'mySQL');
	$log = DB::update(DB_PREFIX.'companies', array ('last_modified_by' => $_SESSION['user_name'], 'last_modified_on' => $time ), 'voucher_id=%s', $pk);
	
	}
}
