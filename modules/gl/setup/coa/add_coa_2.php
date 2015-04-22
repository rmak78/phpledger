<?php
$group_id="";
if(isset($_POST['account_group'])){
	$group_id = $_POST['account_group'];
}

?>

 <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          	Add New Account
            <small>Add New Account to Chart of Accounts</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="<?php echo SITE_ROOT; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">General Ledger</a></li>
            <li class="active">Add New Account Wizard</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

 <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Add New Account Wizard (Step 2)</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
<div class="box-body">
     <div class="progress">
		<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="width: 25%">
        <span class="sr-only">25% Complete  </span>
        </div>
      </div>
<form class="form-horizontal" role="form" method="POST" action="<?php echo SITE_ROOT."index.php?route=modules/gl/setup/coa/add_coa_3" ?>">
	<div class="form-group has-success">
		<label class="col-md-3 col-sm-3 control-label"><i class="fa fa-check"></i>&nbsp;Account Group:
		</label>
        <div class="col-md-9 col-sm-9">
<input type="hidden" name="account_group" value="<?php echo $group_id; ?>" />
			 <select disabled="disabled" class="form-control" name="account_group" id="account_group" >
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
					}?>  value="<?php echo $group['group_id']; ?>" >
<?php echo $group['group_code']." - ".$group['group_description']; ?>
				</option>
			<?php }// end foreach loop ?>
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
						$accounts_query .= " AND consolidate_only =  1";
						}
						
						$accounts = DB::query($accounts_query);
						
						foreach ($accounts as $account) {
						?>					
							<option value="<?php echo $account['account_id']; ?>" ><?php echo $account['account_code']; ?> - <?php echo $account['account_desc_short']; ?></option>
						<?php 
						}
						?>

						</select>
		<p class="help-block"> </p>
	</div><!-- /.col -->
</div> <!-- /form-group -->

<div class="form-group">
	<label class="col-md-3 col-sm-3 control-label">Account Type:</label>
		<div class="col-md-9 col-sm-9">
			<div class="col-md-4 col-sm-4">
 		   		<input type="radio" value="consolidate_only" checked="checked" name="account_type" class="col-sm-2 line-blue"  />
            	<label>Consolidate Only</label>
           </div>
  			<div class="col-md-4 col-sm-4">
 		   		<input diabaled type="radio" value="activity_account" name="account_type" class="col-sm-2 line-blue"  />
            	<label>Activity Account</label>
           </div>         
		<p class="help-block"> </p>
	</div><!-- /.col -->
</div> <!-- /form-group -->

<div class="form-group">
	<div class="col-sm-12">
		<a class='btn btn-danger btn-lg pull-left' href="<?php echo SITE_ROOT."index.php?route=modules/gl/setup/coa/add_coa" ?>">Cancel & Restart &nbsp; <i class="fa fa-chevron-circle-left"></i></a>
		<button type="submit" class='btn btn-success btn-lg pull-right' name="add" value="Next">Next &nbsp; <i class="fa fa-chevron-circle-right"></i></button>
	</div>	<!-- /.col -->
</div>		<!-- /form-group -->	   
</form>
            <!-- Add Account Form Goes here -->
             </div><!-- /.box-body -->
            <div class="box-footer">
             <small> Please do not make changes to these unless you are really sure what you are doing. making changes here have system wide impact</small>
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
     	 </section><!-- /.content -->
