<?php
$group_id="";
$parent_account_id = "";

if(isset($_POST['account_group'])){
	$group_id = $_POST['account_group'];
}
if(isset($_POST['parent_account'])){
	$parent_account_id = $_POST['parent_account'];
}
if(isset($_POST['account_type'])){
	$account_type = $_POST['account_type'];
}


$current_level = get_account_level($parent_account_id) + 1 ;


$company_max_account_levels = DB::queryFirstField("SELECT coa_levels FROM ".DB_PREFIX."companies where company_id = ".$_SESSION['company_id']);



$field = "coa_level_".$current_level."_length";
$current_level_length = DB::queryFirstField("SELECT ".$field." FROM ".DB_PREFIX."companies where company_id = ".$_SESSION['company_id']);

$parent_code = DB::queryFirstField("SELECT account_code FROM ".DB_PREFIX.$_SESSION['co_prefix']."coa WHERE account_id =".$parent_account_id);

$company_max_coa_length = 0;

$i = 1;
While ($i <= $company_max_account_levels) {

$col = "coa_level_".$i."_length";
$result = DB::queryFirstField("SELECT ".$col." FROM ".DB_PREFIX."companies where company_id = ".$_SESSION['company_id']);
$company_max_coa_length += $result;
$i++;

}
echo $company_max_coa_length;

//echo $parent_code;
$parent_level_length = strlen($parent_code);
 
$remaining_length = ($company_max_coa_length - $parent_level_length);
echo $remaining_length;

print_r($_POST);
// we need to get company's Level data here 

if ( $account_type == "consolidate_only" ) {
	
 	// TODO:if current_level >= company_max_levels then you cannot create consolidate only account. user has to go back and select activity_account type
		if($parent_account_id == 0) {
			$mask ="";
			//we are defining a level 1 account
			$i = 0;	
	 		while($i < $current_level_length ) {
			$mask .="9"; 
			$i++;
		
			}
 		} else {
 			$mask = str_replace("9", "\\\\9", $parent_code);							
			$i = 0;	
	 		while($i < $current_level_length ) {
			$mask .="9"; 
			$i++;
		
			}
		}
} elseif ( $account_type == "activity_account" ) {
	
	$mask = str_replace("9", "\\\9", $parent_code);							
			$i = 0;	
	 		while($i < $remaining_length ) {
			$mask .="9"; 
			$i++;
		
			}
	
}


$placeholder =  str_replace("\\\9", "9", $mask);
	
	
 




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
              <h3 class="box-title">Add New Account Wizard (Step 3)</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
<div class="box-body">
     <div class="progress">
		<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%">
        <span class="sr-only">50% Complete  </span>
        </div>
      </div>
<form class="form-horizontal" role="form" method="POST" action="<?php echo SITE_ROOT."index.php?route=modules/gl/setup/coa/add_coa_4" ?>">
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
<div class="form-group has-success">
	<label class="col-md-3 col-sm-3 control-label"><i class="fa fa-check"></i>&nbsp;Parent Account:</label>
		<div class="col-md-9 col-sm-9">
<input type="hidden" name="parent_account" value="<?php echo $parent_account_id; ?>" />
						 <select disabled="disabled" class="form-control" name="parent_account">
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
						<option <?php 
						if ($parent_account_id == $account['account_id'] ) {
							echo 'selected="selected"';
						}
						?>
						 value="<?php echo $account['account_id']; ?>" ><?php echo $account['account_code']; ?> - <?php echo $account['account_desc_short']; ?>						</option>
						<?php 
						}
						?>

						</select>
		<p class="help-block"> </p>
	</div><!-- /.col -->
</div> <!-- /form-group -->
<input type="hidden" name="account_type" value="<?php echo $account_type; ?>" /> 
<div class="form-group  has-success">
	<label class="col-md-3 col-sm-3 control-label"><i class="fa fa-check"></i>&nbsp;Account Type:</label>
		<div class="col-md-9 col-sm-9">
			<div class="col-md-4 col-sm-4">
 		   		<input disabled="disabled" type="radio" <?php if($account_type == "consolidate_only") { echo 'checked="checked"';} ?> value="consolidate_only"   name="account_type" class="col-sm-2 line-blue"  />
            	<label>Consolidate Only</label>
           </div>
  			<div class="col-md-4 col-sm-4">
 		   		<input disabled="disabled" type="radio" <?php if($account_type == "activity_account") { echo 'checked="checked"';} ?> value="activity_account" name="account_type" class="col-sm-2 line-blue"  />
            	<label>Activity Account</label>
           </div>         
		<p class="help-block"> </p>
	</div><!-- /.col -->
</div> <!-- /form-group -->
<div class="form-group">
	<label class="col-md-3 col-sm-3 control-label">Account Code:</label>
		<div class="col-md-9 col-sm-9">
		<div class="input-group">
          <div class="input-group-addon">
            <i class="fa fa-book"></i>
          </div>
          <input required="reqired" type="text" name="account_code" class="masked form-control" data-inputmask="'mask': '<?php echo $mask;?>'" data-autoclear="true" placeholder="<?php echo $placeholder;?>" / >
          </div><!-- /.input group -->
                
		<p class="help-block"> </p>
	</div><!-- /.col -->
</div> <!-- /form-group --> 
<div class="form-group">
	<label class="col-md-3 col-sm-3 control-label">Short Description:</label>
		<div class="col-md-9 col-sm-9">
		<div class="input-group">
          <div class="input-group-addon">
            <i class="fa fa-tag"></i>
          </div>
          <input type="text" name="account_desc_short" class="form-control" required="required" />
          </div><!-- /.input group -->
                
		<p class="help-block"> </p>
	</div><!-- /.col -->
</div> <!-- /form-group --> 
<div class="form-group">
	<label class="col-md-3 col-sm-3 control-label">Longer Description:</label>
		<div class="col-md-9 col-sm-9">
<textarea  name="account_desc_long" class="form-control textarea"  ></textarea>
                
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
