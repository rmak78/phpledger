<?php
$group_id = "";
/* This code is total bullshit!!!!!

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
///////Add New  			
if(isset($_POST['add'])){
							$account_code= $_POST['account_code'];
							$account_group = $_POST['account_group'];
							$account_desc_short = $_POST['account_desc_short'];
							$account_desc_long = $_POST['account_desc_long'];
							$parent_account_id = $_POST['parent_account_id'];
							$account_status = $_POST['account_status'];
							$new_coa_id	=add_coa($account_code,$account_group,$account_desc_short, $account_desc_long, $parent_account_id,$account_status);
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

							 
								$account_code= '';
								$account_group = '';
								$account_desc_short = '';
								$account_desc_long = '';
								$parent_account_id = '';
								$account_status = '';
							}
*/
?>

<div class="container">
      <div class="row">
		<div class="col-lg-10 centre-block">         

       <br>
<p class=" text-info"><?php echo date("Y-m-d h:i:sa"); ?> </p>




<div class="panel panel-primary">

  <div class="panel-heading">
		  <h3>Add A New Account</h3>
           </div>
		   <form class="form-horizontal" role="form" method="post" action="<?php $_SERVER['PHP_SELF']; ?>">
            <div class="panel-body">

			<div class="form-group">
				<label class="col-md-3 col-sm-3 control-label">Account Group:</label>
                         <div class="col-md-9 col-sm-9">








						 <select class="form-control" name="account_group" id="account_group" >
						<option value=""> -- Select --</option>
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
					</select>
							 <p class="help-block"> </p>
							</div>
			  </div>	
<div class="form-group">
	<label class="col-md-3 col-sm-3 control-label">Parent Account:</label>
		<div class="col-md-9 col-sm-9">

						 <select class="form-control" name="parent_account">
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
		<p class="help-block"> </p>
	</div><!-- /.col -->
</div> <!-- /form-group -->
			  <div class="form-group">
                        <label class="col-md-3 col-sm-3 control-label">Account Code:</label>
						<div class="col-md-9 col-sm-9">


					 <p class="help-block"> </p>
							</div>
			  </div>						 
               <div class="form-group">
                        <label class="col-md-3 col-sm-3 control-label">Account Description Short Version:</label>
                         <div class="col-md-9 col-sm-9">
						 <input class="form-control" type="text" required name="account_desc_short">
							 <p class="help-block"> </p>
							</div>
			  </div>	    

              						 
               <div class="form-group">
                        <label class="col-md-3 col-sm-3 control-label">Account Description Long Version:</label>
                         <div class="col-md-9 col-sm-9">








						 <textarea class="form-control" cols="24" rows="3" required name="account_desc_long"></textarea>
							 <p class="help-block"> </p>
							</div>
			  </div>	  
			<div class="form-group">
				<label class="col-md-3 col-sm-3 control-label">Account Status:</label>
                         <div class="col-md-9 col-sm-9">




						 <select class="form-control" name="account_status">
						<option value="active">Active</option>
						<option value="in-active">In-Active</option>
						</select>
							 <p class="help-block"> </p>
							</div>
			  </div>	
				  <div class="form-group">
					   <div class="col-sm-12">
					   <input type="submit" class='btn btn-primary pull-right' name="add" value="Add New Account">
						</div>
					  </div>






				              
                </div>
                </div>






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
$("#success-alert").fadeTo(1000, 200).slideUp(200, function(){
    $("#success-alert").alert('close');
	window.location = "index.php?route=maintain_coa";
});

</script>
