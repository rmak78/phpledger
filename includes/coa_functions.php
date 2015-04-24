<?php
function account_code_exists($account_code){
		$sql = "SELECT count(*) FROM ".DB_PREFIX.$_SESSION['co_prefix']."coa WHERE account_code='".$account_code."'" ;	
		$account_exist = DB::queryFirstField($sql);
			if ($account_exist > 0){
				return true;
			} else {
				return false;
			}
	 	
}
function account_desc_exists($account_desc_short){
	
	 	$sql = "SELECT count(*) FROM ".DB_PREFIX.$_SESSION['co_prefix']."coa WHERE account_desc_short='".$account_desc_short."'" ;	
		$account_exist = DB::queryFirstField($sql);
			if ($account_exist > 0){
				return true;
			} else {
				return false;
			}
	 	
}
function get_account_level($parent_account_id) {
	$level = DB::queryFirstField("SELECT coa_level FROM ".DB_PREFIX.$_SESSION['co_prefix']."coa WHERE account_id =".$parent_account_id);
	
	return $level;
}
// Function to add a new coa Record

function add_coa( 	  $account_code
					, $account_group
					, $account_desc_short
					, $account_desc_long
					, $parent_account_id
<<<<<<< HEAD
					, $username
					, $now
					, $username
					, $account_type
					
=======
					, $account_type 
>>>>>>> 86d31fc1dae380cdde33e1270066770f5113eecc
					) 
{
	
$consolidate_only = 0;
$activity_account = 0;
$has_parent = 0;
$has_children = 0;
$coa_level = 1;
$code_exists = 0;
$desc_exists = 0;

if	($parent_account_id <> 0){
	$has_parent = 1;
}
if	($has_parent == 1) {
	$coa_level = get_account_level($parent_account_id)+1;
}	
if ($account_type == "consolidate_only") {
	$consolidate_only = 1;
} 
if ($account_type == "activity_account") {
	$activity_account = 1;
}

if (account_code_exists($account_code)){
	$code_exists  = 1;
	
}
	//check if account desc already exists
if (account_desc_exists($account_desc_short)){
	$desc_exists = 1;
	
} 	
if (($code_exists <> 1) AND ($desc_exists <> 1)) {
	
	$insert	= DB::Insert(DB_PREFIX.$_SESSION['co_prefix'].'coa'
		,array(
			'account_code' => $account_code,
			'account_group' => $account_group,
			'account_desc_short' => $account_desc_short,
			'account_desc_long' => $account_desc_long,
			'activity_account' => $activity_account,
			'consolidate_only' => $consolidate_only,
			'has_parent' => $has_parent,
			'coa_level' => $coa_level,
			'has_children' => $has_children,
			'parent_account_id' => $parent_account_id,
<<<<<<< HEAD
			'last_modified_by' => $username,
			'last_modified_on' => $now,
			'created_by' => $username,
			'created_on' => $now,
			'account_status' => $account_type
=======
			'last_modified_by' => $_SESSION['user_name'],
			'last_modified_on' => getDateTime(date('now') ,"mySQL" ),
			'created_by' => $_SESSION['user_name'],
			'created_on' => getDateTime( date('now'),"mySQL" ),
			'account_status' => 'Active'
>>>>>>> 86d31fc1dae380cdde33e1270066770f5113eecc
			)  
	);
			
		$new_coa_id =DB::insertId();
<<<<<<< HEAD
		
		return True;
=======
		if(($new_coa_id > 0) AND ($has_parent == 1)) {
			DB::update( DB_PREFIX.$_SESSION['co_prefix'].'coa', array(
  'has_children' => 1
  ), "account_id=%s", $parent_account_id);
		}
		return $new_coa_id;
} else {
		return "0";
}
 
>>>>>>> 86d31fc1dae380cdde33e1270066770f5113eecc
} // Add new COA Function ends


// Function to update a COA Record
function update_coa( 	  $account_code
						, $account_group
						, $account_desc_short
						, $account_desc_long
						, $parent_account_id
						, $account_status
					)
{
	//Define $now ??? where is it comming from
	$edit	= DB::UPDATE(DB_PREFIX.$_SESSION['co_prefix'].'coa'
			,array(
					  'account_code' => $account_code
					, 'account_group' => $account_group
					, 'account_desc_short' => $account_desc_short
					, 'account_desc_long' => $account_desc_long
					, 'parent_account_id' => $parent_account_id
					, 'last_modified_by' => $user_name 
					, 'last_modified_on' => $now 
					, 'account_status' => $account_status
					
				)
					, "account_id =%s", $coa_id 
				);
	$coa_id =DB::insertId();
	
	return $coa_id;

}	
/*	ZAHID: you need to explain this code to me!!!! 
///////Add New  			
if(isset($_POST['add'])){
	$account_code= $_POST['account_code'];
	$account_group = $_POST['account_group'];
	$account_desc_short = $_POST['account_desc_short'];
	$account_desc_long = $_POST['account_desc_long'];
	$parent_account_id = $_POST['parent_account_id'];
	$account_status = $_POST['account_status'];
	$new_coa_id	= add_coa($account_code,$account_group,$account_desc_short, $account_desc_long, $parent_account_id,$account_status);
							if ($new_coa_id) {
							$message = "Successfully Added Chart Of Account";
 							echo '<div id="success-alert" class="alert alert-success">
   							<a href="#" class="close" data-dismiss="alert">
      							&times;
   							</a>
   							<strong>Updated!</strong> Data Saved Succesfully.
						 	</div>';
											}
										else {
												$erro_message = "Unable to create , Check your Input!";
											}
						}
						else {

		// Retrive logged in user data ?????
								$account_code= '';
								$account_group = '';
								$account_desc_short = '';
								$account_desc_long = '';
								$parent_account_id = '';
								$account_status = '';
							}
if(isset($_POST['update'])){	
								$coa_id	=$_GET['id'];
								$account_code	= 	$_POST['account_code'];
								$account_group 	= 	$_POST['account_group'];
								$account_desc_short = $_POST['account_desc_short'];
								$account_desc_long 	= $_POST['account_desc_long'];
								$parent_account_id 	= $_POST['parent_account_id'];
								$account_status 	= 	$_POST['account_status'];
								$update = update_coa($account_code,$account_group,$account_desc_short, $account_desc_long, $parent_account_id,$account_status);
								if ($update) {
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
							 else {

						// Retrive COA data 
								$sql_coa = "SELECT * FROM ".DB_PREFIX.$_SESSION['co_prefix']."coa WHERE account_id ='".$coa_id."'";
								$coa_info = DB::queryFirstRow($sql_coa);
								$account_code= $coa_info['account_code'];
								$account_group = $coa_info['account_group'];
								$account_desc_short = $coa_info['account_desc_short'];
								$account_desc_long = $coa_info['account_desc_long'];
								$parent_account_id = $coa_info['parent_account_id'];
								$account_status = $coa_info['account_status'];
							}							
		

*/
function get_coa_group_type($coa_group_id){
	$type = "";
 
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

	$sql = "SELECT * FROM ".DB_PREFIX.$_SESSION['co_prefix']."coa_groups WHERE group_code='".$coa_group_id."'" ;	
		$group = DB::queryFirstRow($sql);
		if ( $group['balance_sheet_group'] == 1 ) {
				$side = $group['balance_sheet_side'];
} 			else if ( $group['pls_group'] == 1 ) {
				$side = $group['pls_side'];
}			 else if ( $group['statistics_only'] == 1 ) {
				$side = "-";
}			 else {
				$type = "UnKnown" ;
}
		
	return $side;

}	
function get_max_coa_levels( ) {
	$company_max_account_levels = DB::queryFirstField("SELECT coa_levels FROM ".DB_PREFIX."companies where company_id = ".$_SESSION['company_id']);
	return $company_max_account_levels;

}
?>