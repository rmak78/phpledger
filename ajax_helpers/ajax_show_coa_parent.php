<script>
$(function(){
	$('#parent_account_id').change(function(){
		var ac_code = $( '#parent_account_id option:selected').text();
		var acc_int = parseInt(ac_code);
		$('#account_code').val(acc_int+'-');
		$('#account_code').focus();
	});
});
</script>
<?php 
require_once ( '../tools_header.php');
require_once ("../functions.php");
$account_group=$_POST['account_group'];
?> 
<div class="form-group">
 <label class="col-md-3 col-sm-3 control-label">Parent Account:</label>
                         <div class="col-md-9 col-sm-9">
						 <select class="form-control" name="parent_account_id" id="parent_account_id">
						<option value="0"> -- None --</option>
						<?php 
						$accounts_query = "SELECT account_id, account_code, account_desc_short FROM ";
						$accounts_query .= DB_PREFIX.$_SESSION['co_prefix']."coa WHERE( (account_group='".$account_group."') AND(consolidate_only =  1 ) AND (account_status =  'active') ) ORDER BY account_code " ;
						
						
						$accounts = DB::query($accounts_query);
						if($accounts) {
							$options="";
						foreach ($accounts as $account) {					
							$options .="<option value='".$account['account_id']."' >".$account['account_code']."-".$account['account_desc_short']."</option>";
						}
						}
						echo $options;
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