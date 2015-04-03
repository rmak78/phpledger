<?php
require_once ( '../tools_header.php');
require_once ("../functions.php");

if( isset($_GET['pk']) ) {
	
	$pk = $_GET['pk'];
	$name = $_GET['name'];
	$value = $_GET['value'];
    $update = DB::update(DB_PREFIX.'companies', array ($name => $value ), "company_id=%s", $pk); 
	
	 echo $update;
	
}
