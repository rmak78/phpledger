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
						  <p id="check_account_code"></p>
							 <p class="help-block">Code length should not be less than, You've defined </p>
							</div>
			  </div>
			  
			  <script type="text/javascript">
$(document).ready(function(){
    $('#account_code').keyup(function(){ // Keyup function for check the user action in input
        var account_code = $(this).val(); // Get the username textbox using $(this) or you can use directly $('#username')
            
			$('#check_account_code').html('Checking..'); // Preloader, use can use loading animation here
            var dataToPass = 'account_code='+account_code;
			$.ajax({ // Send the username val to another checker.php using Ajax in POST menthod
            type : 'POST',
			url  : 'ajax_helpers/ajax_check_account_code.php',
            data : dataToPass,
			success: function(data){ // Get the result and asign to each cases
				if(data == 0){
                    $('#check_account_code').html('<span style="color:green;">Account code available</span>');
                }
                else if(data > 0){
                    $('#check_account_code').html('<span style="color:red;">Account code already taken</span>');
                }
                else{
                    $('#check_account_code').html('<span style="color:red;">Problem in checking..</span>');
                }
				
            }

            });
			
			
			
			
    });
});	
</script>