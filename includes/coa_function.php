<?php
$group_id = "";
$from_account_code='';
$to_account_code='';

if (isset($_GET['parent_account_id'])) {
	$parent_account_id = $_GET['parent_account_id']; 
	}

if ( (isset($_SESSION['is_logged'])) AND ($_SESSION['is_logged'] == 1)) {
			$username = get_user_name($_SESSION['user_id']);
				$role_id = getUserRoleID($_SESSION['user_id']);
		}
			$now=date("Y-m-d h:i:sa");
			
if(isset($_POST['action'])){

	$account_code= $_POST['account_code'];
	$account_group = $_POST['account_group'];
	$account_desc_short = $_POST['account_desc_short'];
	$account_desc_long = $_POST['account_desc_long'];
	$parent_account_id = $_POST['parent_account_id'];
$account_status = $_POST['account_status'];
$new_coa_id	=add_coa($account_code,$account_group,$account_group,$account_desc_short, $account_desc_long, $parent_account_id,$account_status);
if ($new_coa_id) {
		echo '<div id="success-alert" class="alert alert-success">
				<a href="#" class="close" data-dismiss="alert">
				&times;
				</a>
			  Created Successfully.
			</div>';
		} 
		else {
		$erro_message = "Unable to create , Check your Input!";
		}
}
function add_coa($account_code,$account_group,$account_group,$account_desc_short, $account_desc_long, $parent_account_id,$account_status){
$insert	= DB::Insert(DB_PREFIX.$_SESSION['co_prefix'].'coa',array(
			'account_code' => $account_code,
			'account_group' => $account_group,
			'account_desc_short' => $account_desc_short,
			'account_desc_long' => $account_desc_long,
			'parent_account_id' => $parent_account_id,
			'last_modified_by' => $username,
			'last_modified_on' => $now,
			'created_by' => $username,
			'created_on' => $now,
			'account_status' => $account_status)  );
			$new_coa_id =DB::insertId();
			return $new_coa_id;
}
?>