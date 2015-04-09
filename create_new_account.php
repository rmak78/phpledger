<?php 
$company = DB::queryFirstRow('SELECT * FROM '.DB_PREFIX.'companies WHERE company_id = '.$_SESSION['company_id']);
// Get Company Chart of Account Levels
$levels = $company['coa_levels'];

// Get Each Level's  code length
function create_new_account() {





}
?>
<div class="container">
      <div class="row">
	  <div class="col-lg-8 col-lg-offset-2 centre-block">
    <br>
<p class=" text-info"><?php echo date("Y-m-d h:i:sa"); ?> </p>
<div class="panel panel-primary">
  <div class="panel-heading">
		  <h3>Create New Account</h3>
           </div>
		  <form class="form-horizontal" role="form" method="post" action="">
        
            <div class="panel-body">
			<div class="form-group">
                        <label class="col-md-3 col-sm-3 control-label">Account Group:</label>
                         <div class="col-md-9 col-sm-9">
						  
						<select class="form-control" name="account_group" id="account_group" required="required">
						<option value=""> -- Select -- </option>
						<?php 
						$groups_query = "SELECT group_id, group_code, group_description from ";
						$groups_query .= DB_PREFIX.$_SESSION['co_prefix']."coa_groups";
						
						$groups = DB::query($groups_query);
						
						foreach ($groups as $group) {
						?>					
							<option   value="<?php echo $group['group_id']; ?>" ><?php echo $group['group_code']." - ".$group['group_description']; ?></option>
						<?php 
						}
						?>
						</select>
						  
							 <p class="  help-block"> </p>
				</div>
			  </div>	             
              <div class="form-group">
                        <label class="col-md-3 col-sm-3 control-label">Sub Account:</label>
                         <div class="col-md-9 col-sm-9">
						  <input class="controls" type="checkbox" name="has_parent" id="has_parent" value="has_parent" checked="checked" />
							 <p class="help-block">Only Un-Check it to Define Level 1 Accounts</p><!-- TODO:On click Parent Account Select box should Hide -->
							</div>
			  </div>	
              <div class="form-group">
                        <label class="col-md-3 col-sm-3 control-label">Parent Account:</label>
                         <div class="col-md-9 col-sm-9">
						 <select class="form-control" name="parent_account_id" id="parent_account_id">
						<option value="0"> -- None --</option>
						<?php 
						$accounts_query = "SELECT account_id, account_code, account_desc_short FROM ";
						$accounts_query .= DB_PREFIX.$_SESSION['co_prefix']."coa WHERE( (consolidate_only =  1 ) AND (account_status =  'active') ) ORDER BY account_code " ;
						
						
						$accounts = DB::query($accounts_query);
						if($accounts) {
						foreach ($accounts as $account) {
						?>					
							<option value="<?php echo $account['account_id']; ?>" ><?php echo $account['account_code']; ?> - <?php echo $account['account_desc_short']; ?></option>
						<?php 
						}
						}
						?>
						
						</select>
							 <p class="help-block"> </p>
							</div>
			  </div>					
               <div class="form-group">
                        <label class="col-md-3 col-sm-3 control-label">Account Code:</label>
                         <div class="col-md-9 col-sm-9">
						  
						 <input class=" form-control" type="text"  required="required" name="account_code" id="account_code">
						  
							 <p class="  help-block"> </p>
							</div>
			  </div>
			  
              <div class="form-group">
                        <label class="col-md-3 col-sm-3 control-label">Account Description (Short):</label>
                         <div class="col-md-9 col-sm-9">
						 <input class="form-control" type="text" required="required" name="account_desc_short" id="account_desc_short">
							 <p class="help-block"> </p>
							</div>
	 
				</div>	
				<div class="form-group">
                        <label class="col-md-3 col-sm-3 control-label">Account Description (Long):</label>
                         <div class="col-md-9 col-sm-9">
						 <textarea class="form-control" required="required" name="account_desc_long" id="account_desc_long"></textarea>
							 <p class="help-block"> </p>
							</div>
	 
				</div>	
                  
   
               <div class="form-group">
                         <label class="col-sm-3 control-label">Account Type</label>
                        <div class="col-sm-6">
						<input type="radio" name="account_type" value="consolidate_only" checked="checked" > Consolidate Only&nbsp;
                        <input type="radio"  name="account_type" value="activity_only">Activity Only
						 
						</div>
						</div>	
 

					  <div class="form-group">
					   <div class="col-sm-12">
					   <input type="submit" class='btn btn-primary pull-right' name="submit" value="Create Account">
						</div>
					  </div>

                              
                </div>
           
    </div>
 
					</div>
					</div>
            </form>
</div>
 
<?php
include_once("./tools_footer.php");
?>