<?php
$group_id = "";
$from_account_code='';
$to_account_code='';
if (isset($_GET['group_id'])) {
$group_id = $_GET['group_id']; 
$from_account_code = get_from_account_code($group_id, 'group');
$to_account_code = get_to_account_code($group_id, 'group');
}
if (isset($_GET['parent_account_id'])) {
$parent_account_id = $_GET['parent_account_id']; 
//$from_account_code = get_from_account_code($parent_account_id , 'account');
//$to_account_code = get_to_account_code($parent_account_id, 'account');
}

if ( (isset($_SESSION['is_logged'])) AND ($_SESSION['is_logged'] == 1)) {
	$username = get_user_name($_SESSION['user_id']);
	$role_id = getUserRoleID($_SESSION['user_id']);
}
$now=date("Y-m-d h:i:sa");
	$message= "";
if(isset($_POST['add'])){

$account_code= $_POST['account_code'];
$account_group = $_POST['account_group'];
$account_desc_short = $_POST['account_desc_short'];
$account_desc_long = $_POST['account_desc_long'];
$parent_account_id = $_POST['parent_account_id'];
$account_status = $_POST['account_status'];

//check if username already exists

/* Insert user data  */	
DB::Insert(DB_PREFIX.$_SESSION['co_prefix'].'coa',array(
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
$message = "Successfully Added Chart Of Account";
 
echo '<div id="success-alert" class="alert alert-success">
   <a href="#" class="close" data-dismiss="alert">
      &times;
   </a>
   <strong>Updated!</strong> Data Saved Succesfully.
</div>';

} else {

/* Retrive logged in user data */
$account_code= '';
$account_group = '';
$account_desc_short = '';
$account_desc_long = '';
$parent_account_id = '';
$account_status = '';
}

?>

<div class="container">
      <div class="row">
      <div class="col-md-5  toppad  pull-right col-md-offset-3 ">
         
       <br>
<p class=" text-info"><?php echo date("Y-m-d h:i:sa"); ?> </p>
      </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad" >
   
   
          <div class="panel panel-info">
		
            <div class="panel-heading">
              <h3 class="panel-title">Add A New Account</h3>
            </div>
            <div class="panel-body">
            <form method="post" action="<?php $_SERVER['PHP_SELF']; ?>">
			<div class="row">
               
         
                <div class=" col-md-9 col-lg-9 "> 
                  <table class="table table-user-information">
                    <tbody>
 	  					  <tr>
                        <td>Account Group:</td>
                        <td>
						<select type="form-control" name="account_group" id="account_group" required="required">
						<option value=""> -- Select -- </option>
						<?php 
						$groups_query = "SELECT group_id, group_code, group_description from ";
						$groups_query .= DB_PREFIX.$_SESSION['co_prefix']."coa_groups";
						
						$groups = DB::query($groups_query);
						
						foreach ($groups as $group) {
						?>					
							<option <?php 
							if ($group_id == $group['group_id'] ) {
							echo 'selected = "selected"';
							}								
							?>  value="<?php echo $group['group_id']; ?>" ><?php echo $group['group_code']." - ".$group['group_description']; ?></option>
						<?php 
						}
						?>
						</select>
                      </tr>
					<tr>
                        <td>Parent Account</td>
                        <td>
						<Select name="parent_account">
						<option value="0"> -- None --</option>
						<?php 
						$accounts_query = "SELECT account_id, account_code, account_desc_short from ";
						$accounts_query .= DB_PREFIX.$_SESSION['co_prefix']."coa WHERE 1 = 1";
						if ($group_id <> "") {
						$accounts_query .= " AND account_group =  ".$group_id;
						}
						
						$accounts = DB::query($accounts_query);
						
						foreach ($accounts as $account) {
						?>					
							<option value="<?php echo $account['account_id']; ?>" ><?php echo $account['account_code']; ?></option>
						<?php 
						}
						?>
						
						</select>
						</td>
                      </tr>   
                      <tr>
                        <td>Account Code:</td>
                        <td>
						<p><?php echo $from_account_code; ?> to <?php echo $to_account_code; ?></p>
						
						<input type="number" value="<?php echo substr($from_account_code, 0, 2);  ?>" required name="account_code"></td>
                      </tr>
					  
                      <tr>
                        <td>Account Description Short Version</td>
                        <td><input type="text" required name="account_desc_short"></td>
                      </tr>
                   
                         <tr>
                             <tr>
                        <td>Account Description Long Version</td>
                        <td><textarea cols="24" rows="3" required name="account_desc_long"></textarea></td>
                      </tr>                       
                     
					  <tr>
                        <td>Account Status</td>
                        <td><Select name="account_status">
						<option value="active">Active</option>
						<option value="in-active">In-Active</option>
						</select></td>
                      </tr>                      
                           
                      </tr>
					  <tr>
					  <td></td>
					  <td><input type="submit" class='btn btn-primary btn-sm' name="add" value="Add New Account">
					 
					  </td>
					  </tr>
                     
                    </tbody>
                  </table>
                              
                </div>
              </div>
			</form>
            </div>
                <!--  <div class="panel-footer">
                        <a data-original-title="Broadcast Message" data-toggle="tooltip" type="button" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-envelope"></i></a>
                        <span class="pull-right">
                            <a href="edit.html" data-original-title="Edit this user" data-toggle="tooltip" type="button" class="btn btn-sm btn-warning"><i class="glyphicon glyphicon-edit"></i></a>
                            <a data-original-title="Remove this user" data-toggle="tooltip" type="button" class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-remove"></i></a>
                        </span>
                    </div> -->

          </div>
        </div>
      </div>
    </div>
<script type="text/javascript">
$('#account_group').change(function() {
    window.location = "index.php?route=coa/add_coa&group_id=" + $(this).val();
});
</script>
<script type="text/javascript">
$("#success-alert").fadeTo(1000, 200).slideUp(200, function(){
    $("#success-alert").alert('close');
	window.location = "index.php?route=maintain_coa";
});

</script>
<?php
include_once("./tools_footer.php");
?>