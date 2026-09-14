<?php
$group_id="";
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
              <h3 class="box-title">Add New Account Wizard (Step 1)</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
<div class="box-body">
     <div class="progress">
		<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" style="width: 10%">
        <span class="sr-only">10% Complete  </span>
        </div>
      </div>
<form class="form-horizontal" role="form" method="POST" action="<?php echo SITE_ROOT."index.php?route=modules/gl/setup/coa/add_coa_2" ?>">
	<div class="form-group">
		<label class="col-md-3 col-sm-3 control-label">Account Group:
		</label>
        <div class="col-md-9 col-sm-9">

			 <select required="required" class="form-control" name="account_group" id="account_group" >
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
		<div class="col-sm-12">
		<button type="submit" class='btn btn-success btn-lg pull-right' name="add" value="Next">Next &nbsp; <i class="fa fa-chevron-circle-right"></i></button>
		</div>
	</div>			   
</form>
            <!-- Add Account Form Goes here -->
             </div><!-- /.box-body -->
            <div class="box-footer">
             <small> Please do not make changes to these unless you are really sure what you are doing. making changes here have system wide impact</small>
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
     	 </section><!-- /.content -->