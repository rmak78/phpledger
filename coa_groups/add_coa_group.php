<?php
if(isset($_POST['submit']))
{
 $group_code		= $_POST['group_code'];
 $group_description = $_POST['group_description'];
 $balance_sheet_side = $_POST['balance_sheet_side'];
 $pls_side		  = $_POST['pls_side'];
 $group_status	  = $_POST['group_status'];
 $account_type = $_POST['account_type'];
  if($account_type=='balance_sheet'){
	  $insert = DB::Insert(DB_PREFIX.$_SESSION['co_prefix'].'coa_groups', array(		
			'group_code' 			=> $group_code,	
            'group_description' 	 => $group_description,
            'balance_sheet_group'   => 1,
            'balance_sheet_side'    => $balance_sheet_side,
            'pls_group' 			 => '',	 
            'pls_side' 			  => '',
            'statistics_only' 	   => 0,
            'group_status' 		  => $group_status
		
		)); 
  }
  else if($account_type=='pls_group'){
	  $insert = DB::Insert(DB_PREFIX.$_SESSION['co_prefix'].'coa_groups', array(		
			'group_code' 			=> $group_code,	
            'group_description' 	 => $group_description,
           'balance_sheet_group'   => '',
            'balance_sheet_side'    => '',
            'pls_group' 			 => $pls_group,	 
            'pls_side' 			  => $pls_side,
            'statistics_only' 	   => 0,
            'group_status' 		  => $group_status
		
		)); 
  }
  else{
	  $insert = DB::Insert(DB_PREFIX.$_SESSION['co_prefix'].'coa_groups', array(
   				
			'group_code' 			=> $group_code,	
            'group_description' 	 => $group_description,
           'balance_sheet_group'   => '',
            'balance_sheet_side'    => '',
            'pls_group' 			 => '',	 
            'pls_side' 			  => '',
			'statistics_only' 	   => 1,
            'group_status' 		  => $group_status
		
		)); 
  }
	       		 
 }



?>
<div class="container">
      <div class="row">
	 <div class="col-lg-8 col-lg-offset-2 centre-block">
    <br>
<p class=" text-info"><?php echo date("Y-m-d h:i:sa"); ?> </p>
<div class="panel panel-primary">
  <div class="panel-heading">
		  <h3>Add Chart of Account Group</h3>
           </div>
		  <form class="form-horizontal" role="form" method="post" action="">
        
            <div class="panel-body">
              <div class="form-group form-inline">
				
                        <label class="col-sm-3 control-label">Group Code:</label>
                         <div class="col-sm-4">
						 <input class="controls" type="text" required name="group_code" id="group_code"
						   pattern="[A-Z0-9]{4}"  maxlength="4">
							 <p id="check_group_code"></p>
							</div>
							<p class="help-block">Must be length 4 and Capital characters only</p>
			  </div>	
				
              <div class="form-group">
                        <label class="col-sm-3 control-label">Group Description:</label>
                        <div class="col-sm-6">
						<input class="form-control" type="text" required name="group_description" id="group_description">
						</div>
				</div>		
    
                  <?php 
				  $to_account=DB::queryfirstfield('SELECT to_account_code FROM '.DB_PREFIX.$_SESSION['co_prefix'].'coa_groups ORDER BY group_id DESC LIMIT 1');
				  
				  ?>
                   
                   
                        <div class="form-group">
                         <label class="col-sm-3 control-label">Group Type</label>
                        <div class="col-sm-6">
						<input type="radio" name="account_type" value="balance_sheet" checked> Balance Sheet&nbsp;
                        <input type="radio" name="account_type" value="pls_group"> Profit &Loss Statements Group&nbsp;
						<input type="radio" name="account_type" value="statistics_only"> Statistics Only&nbsp;
						</div>
						</div>	
					<div id="balance_sheet">	
						<div class="form-group">
							 <label class="col-sm-3 control-label">Balance Sheet Side</label>
							<div class="col-sm-6"><select class="form-control" required  name="balance_sheet_side" id="balance_sheet_side">
							<option value="debit">Debit</option>
							<option value="credit">Credit</option>
							</select>
							</div>
						  </div>  
					 </div>
                      <div id="pls">                   
						  <div class="form-group">
							<label class="col-sm-3 control-label">Profit &Loss Statements Side</label>
							<div class="col-sm-6"><select class="form-control" required  name="pls_side" id="pls_side">
							 <option value="income">Income</option>
							<option value="expense">Expense</option>
							</select>
							</div>
							</div>
					</div>
                       <div class="form-group">
							<label class="col-sm-3 control-label">Group Status</label>
                        <div class="col-sm-6"><select class="form-control" type="text" required  name="group_status" id="group_status">
                        <option value="active">Active</option>
                       <option value="in-active">In-Active</option>  
                        </select>
                        
                        
                        </div>
                      </div>  

					  <div class="form-group">
					   <div class="col-sm-6">
					   <input type="submit" class='btn btn-primary pull-right' name="submit" value="SAVE">
						</div>
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
					</div>
					</div>
            </form>
</div>


<script>
$(document).ready(function(){
	$('div#pls').hide();
	$("input:radio[name=account_type]").click(function() {
    var value = $(this).val();
	if(value=='pls_group'){
		$('div#balance_sheet').hide();
		$('div#pls').show();
	}
	else if(value=='balance_sheet'){
		$('div#balance_sheet').show();
		$('div#pls').hide();
	}
	else{
		$('div#balance_sheet').hide();
		$('div#pls').hide();
	}
	
});
});

</script>


<script type="text/javascript">
$(document).ready(function(){
    $('#group_code').keyup(function(){ // Keyup function for check the user action in input
        var group_code = $(this).val(); // Get the username textbox using $(this) or you can use directly $('#username')
            if(group_code.length > 3){
			$('#check_group_code').html('Checking..'); // Preloader, use can use loading animation here
            var dataToPass = 'group_code='+group_code;
			$.ajax({ // Send the username val to another checker.php using Ajax in POST menthod
            type : 'POST',
			url  : 'ajax_helpers/ajax_check_group_code.php',
            data : dataToPass,
			success: function(data){ // Get the result and asign to each cases
                if(data == 0){
                    $('#check_group_code').html('<span style="color:green;">Group code available</span>');
                }
                else if(data > 0){
                    $('#check_group_code').html('<span style="color:red;">Group code already taken</span>');
                }
                else{
                    alert('Problem with sql query');
                }
				
            }

            });
			}
			else{
				 $('#check_group_code').html('<span style="color:red;">Enter 4 char</span>');
			}
			
			
    });
});	
</script>

<?php
include_once("./tools_footer.php");
?>