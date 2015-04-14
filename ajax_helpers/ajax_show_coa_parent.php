<script>
$(function(){
	$('#parent_account_id').change(function(){
		var ac_code = $( '#parent_account_id option:selected').text();
		ac_code = ac_code.replace(/[^0-9-]+/g, '');
		$('#account_code').val(ac_code);
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
						$accounts_query = "SELECT account_id, coa_level, account_code, account_desc_short FROM ";
						$accounts_query .= DB_PREFIX.$_SESSION['co_prefix']."coa WHERE( (account_group='".$account_group."') AND(consolidate_only =  1 ) AND (account_status =  'active') ) ORDER BY coa_level " ;
						
						
						$accounts = DB::query($accounts_query);
						if($accounts) {
							$options="";
							$temp_coa = DB::queryfirstfield($accounts_query);
							$temp_coa_level = $temp_coa['coa_level'];
							$options .=" <optgroup label='Level: ".$temp_coa_level."'>";
						foreach ($accounts as $account) {	
							if($temp_coa_level == $account['coa_level']){
								$options .="<option value='".$account['account_id']."'>".$account['account_code']."-".$account['account_desc_short']."</option>";
							}
							else{
								$options .="</optgroup>";
								$options .=" <optgroup label='Level: ".$account['coa_level']."'>";
								$options .="<option value='".$account['account_id']."'>".$account['account_code']."-".$account['account_desc_short']."</option>";
								$temp_coa_level = $account['coa_level'];
							}
							
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