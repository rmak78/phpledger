<?php 
if(isset($_POST['submit']))
{
	
	$active_only ='';
	$consolidate_only='';
	$has_parent=0;
	$account_group=$_POST['account_group'];
	$account_code=$_POST['account_code'];
	$account_des_short=$_POST['account_desc_short'];
	$account_des_long=$_POST['account_desc_long'];
	$account_type=$_POST['account_type'];
	$parent_account=$_POST['parent_account_id'];
	echo $parent_account;
	if($parent_account<>0)
	{
		$has_parent=1;
		}
	 if($account_type=='activity_only')
	 {
	 
	 $active_only    =1;
	 }
	 else
	 {
	  $consolidate_only  =1;
	 }
	 $account_level=get_chiled_account_level($parent_account);
	 if(!$account_level)
	 {
		 $account_level=1;
		 }
	
	 $insert = DB::Insert(DB_PREFIX.$_SESSION['co_prefix'].'coa', array(
	 
	 'account_group'	=>		$account_group,
	 'account_code'		=>	 $account_code,
	 'account_desc_short' =>	  $account_des_short,
	 'account_desc_long'  =>	  $account_des_long,
	 'activity_account' =>		$active_only ,
	 'consolidate_only' =>		$consolidate_only,
	 'parent_account_id'  =>	  $parent_account,
	 'has_parent'		=>	   $has_parent,
	 'coa_level'		=>		$account_level
	
 	 ));
	
	
	}

?>


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
		  <form class="form-horizontal" role="form" method="post" action="" name="create_new_account">
        
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
							<option value="<?php echo $group['group_id']; ?>" ><?php echo $group['group_code']." - ".$group['group_description']; ?></option>
						<?php 
						}
						?>
						</select>
						  
							 <p class="help-block"> </p>
				</div>
			  </div>	             
             
              <div id="div_parent_account">
                      
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

<script>
$(document).ready(function(){
	$('#div_parent_account').hide();
	$('#account_group').change(function(){
		var account_group=$(this).val();
		var dataToPass = 'account_group='+account_group;
			$.ajax({ // Send the username val to another checker.php using Ajax in POST menthod
            type : 'POST',
			url  : 'ajax_helpers/ajax_show_coa_parent.php',
            data : dataToPass,
			success: function(data){ // Get the result and asign to each cases
                    $('#div_parent_account').show();
					$('#div_parent_account').html(data);
					
					
			}
         });
	});
	
});
</script>
