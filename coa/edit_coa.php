<?php
if ( (isset($_SESSION['is_logged'])) AND ($_SESSION['is_logged'] == 1)) {
	$user_name = $_SESSION['user_name'];
	$role_id = getUserRoleID($_SESSION['user_id']);
}
$now=date("Y-m-d h:i:sa");
if(isset($_GET['coa_id'])){
	$coa_id = $_GET['coa_id'];
}
echo $coa_id;
	$message= "";
if(isset($_POST['update'])){
$account_code= $_POST['account_code'];
$account_group = $_POST['account_group'];
$account_desc_short = $_POST['account_desc_short'];
$account_desc_long = $_POST['account_desc_long'];
$parent_account_id = $_POST['parent_account_id'];
$account_status = $_POST['account_status'];
/* update logged in user data  */	
DB::UPDATE(DB_PREFIX.$_SESSION['co_prefix'].'coa',array(
			'account_code' => $account_code,
			'account_group' => $account_group,
			'account_desc_short' => $account_desc_short,
			'account_desc_long' => $account_desc_long,
			'parent_account_id' => $parent_account_id,
			'last_modified_by' => $user_name,
			'last_modified_on' => $now,
			'account_status' => $account_status), "account_id =%s", $coa_id );
$message = "Successfully Updated";
 
echo '<script type="text/javascript">
<!--
window.location = "'.$_SERVER['PHP_SELF'].'?route=maintain_coa"
//-->
</script>';
} else {

/* Retrive logged in user data */

$sql_coa = "SELECT * FROM ".DB_PREFIX.$_SESSION['co_prefix']."coa WHERE account_id ='".$coa_id."'";
$coa_info = DB::queryFirstRow($sql_coa);
$account_code= $coa_info['account_code'];
$account_group = $coa_info['account_group'];
$account_desc_short = $coa_info['account_desc_short'];
$account_desc_long = $coa_info['account_desc_long'];
$parent_account_id = $coa_info['parent_account_id'];
$account_status = $coa_info['account_status'];
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
		  <form method="post" action="<?php $_SERVER['PHP_SELF']; ?>">
            <div class="panel-heading">
              <h3 class="panel-title">Add New Chart of Account</h3>
            </div>
            <div class="panel-body">
              <div class="row">
               
         
                <div class=" col-md-9 col-lg-9 "> 
                  <table class="table table-user-information">
                    <tbody>
 	  
                      <tr>
                        <td>Account Code:</td>
                        <td><input type="text" required name="account_code" value="<?php echo $account_code; ?>"></td>
                      </tr>
					  <tr>
                        <td>Account Group:</td>
                        <td><input type="text" required name="account_group" value="<?php echo $account_group; ?>"></td>
                      </tr> 
                      <tr>
                        <td>Account Description Short</td>
                        <td><input type="text" required name="account_desc_short" value="<?php echo $account_desc_short; ?>"></td>
                      </tr>
                   
                         <tr>
                             <tr>
                        <td>Account Description Brief</td>
                        <td><textarea cols="24" rows="3" required name="account_desc_long"><?php echo $account_desc_long; ?></textarea></td>
                      </tr>                       
                      <tr>
                        <td>Parent Account ID</td>
                        <td><input type="number" required name="parent_account_id" value="<?php echo $parent_account_id; ?>"></td>
                      </tr>   
					  <tr>
                        <td>Account Status</td>
                        <td><Select name="account_status">
						<?php if($account_status=='active'){
							echo '<option value="active" SELECTED>Active</option>';
							echo '<option value="in-active">In-Active</option>';
						} 
						else{
							echo '<option value="in-active" SELECTED>In-Active</option>';
							echo '<option value="active">Active</option>';
						}
						?>
						</select></td>
                      </tr>                      
                           
                      </tr>
					  <tr>
					  <td></td>
					  <td><input type="submit" class='btn btn-primary btn-sm' name="update" value="Save Changes">
					  <font color="red"><?php echo $message;?> </font>
					  </td>
					  </tr>
                     
                    </tbody>
                  </table>
                              
                </div>
              </div>
            </div>
                <!--  <div class="panel-footer">
                        <a data-original-title="Broadcast Message" data-toggle="tooltip" type="button" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-envelope"></i></a>
                        <span class="pull-right">
                            <a href="edit.html" data-original-title="Edit this user" data-toggle="tooltip" type="button" class="btn btn-sm btn-warning"><i class="glyphicon glyphicon-edit"></i></a>
                            <a data-original-title="Remove this user" data-toggle="tooltip" type="button" class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-remove"></i></a>
                        </span>
                    </div> -->
            </form>
          </div>
        </div>
      </div>
    </div>

<?php
include_once("./tools_footer.php");
?>