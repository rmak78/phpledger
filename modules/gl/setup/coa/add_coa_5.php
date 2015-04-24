<?php
//print_r($_POST);

$group_id="";
$parent_account_id = "";
$now=date("Y-m-d h:i:sa");
$username	="Admin";
if(isset($_POST['account_group'])){
	$group_id = $_POST['account_group'];	
}
if(isset($_POST['parent_account'])){
	$parent_account_id = $_POST['parent_account'];
}
if(isset($_POST['account_type'])){
	$account_type = $_POST['account_type'];
}

if(isset($_POST['end']))
{
$account_code = $_POST['account_code'];
$account_desc_short = $_POST['account_desc_short'];
$account_desc_long  = $_POST['account_desc_long'];
$add = add_coa($account_code,$group_id,$account_desc_short,$account_desc_long, $parent_account_id ,$username,$now,$username,$account_type);
if ($add="True")
	{
	echo"<br> We create new account at this page and ask user to create another account or go to List  of accounts. Or we can redirect them to list of accounts.";
		}
}
?>