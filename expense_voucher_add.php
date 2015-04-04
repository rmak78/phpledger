<?php
// voucher heading updation

// Add expense

	 //total voucher
$new_voucher_id='';
if(isset($_GET['new_voucher_id'])){
	$new_voucher_id=$_GET['new_voucher_id'];
}
function create_new_voucher($voucher_ref, $voucher_date, $voucher_paid_from_account, $voucher_description ) {
// get user name and datetime now
	$now= getDateTime(0,'mySQL');
 // perform sql insertion
   $insert = DB::Insert(DB_PREFIX.$_SESSION['co_prefix'].'voucher_expense', array(			
			'voucher_ref_no' 			=>  $voucher_ref,	
            'voucher_date' 	 		  => $voucher_date,
            'voucher description' 	  =>   $voucher_description,
            'petty_cash_account' 	   	 =>  $voucher_paid_from_account,
			'created_on'				=>  $now,
			'created_by'				=>  $_SESSION['user_name']
			));
	$new_voucher_id =DB::insertId();
	return $new_voucher_id;
	       
}


if(isset($_POST['action'])) {
	$action = $_POST['action'];
	switch ($action) {
		case "add_voucher":
		   // create New Voucher
		$voucher_ref = mysql_real_escape_string($_POST['voucher_ref']);
		$voucher_date = getDateTime($_POST['voucher_date'], 'mySQL');
		$voucher_paid_from_account =  mysql_real_escape_string($_POST['voucher_paid_from_account']);
		$voucher_description = 	 mysql_real_escape_string($_POST['voucher_description']);
		$new_voucher_id = create_new_voucher($voucher_ref, $voucher_date, $voucher_paid_from_account, $voucher_description );
		if ($new_voucher_id) {
		echo '<div id="success-alert" class="alert alert-success">
				<a href="#" class="close" data-dismiss="alert">
				&times;
				</a>
			 Voucher Created Successfully.
			</div>';
		} 
		else {
		$erro_message = "Unable to create Voucher, Check your Input!";
		}

		   break;
		case "delete_voucher":
			
			break;
		case "edit_voucher":
			
			break;
		case "add_voucher_item":
			
			break;
		case "delete_voucher_item":
			
			break;
		case "edit_voucher_item":
			
			break;
		case "save_voucher":
			
			break;
		default:
			break;
	} 


}


 ?> 
 <?php if($new_voucher_id == ''){ ?>
	<!-- Add Voucher DIV -->

						<div class="col-md-5  toppad  pull-right col-md-offset-3 ">   
						<p class=" text-info"><?php echo date("Y-m-d h:i:sa"); ?> </p>
						</div>
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad" >

				  <div class="panel panel-info">
				
					<div class="panel-heading">
					  <h3 class="panel-title">Add Expense Voucher</h3>
					</div>
					<div class="panel-body">
								<form method="post" action="" class="form-horizontal">
								<div class='form-row'>
								  <div class='col-xs-12 form-group card required'>
									<label>Voucher Ref#</label>
									<input class="form-control" required="required" name="voucher_ref" id="voucher_ref" value="" />
									</div>
								</div>	
								<div class='form-row'>
								  <div class='col-xs-12 form-group card required'>
									<label>Voucher Date</label>
									<input name="voucher_date" required="required" id="voucher_date"   class="date-picker form-control" size="16" type="text" value="">
									</div>
								</div>
								<div class='form-row'>
								  <div class='col-xs-12 form-group card required'>
									<label>Paid From Account</label>	
									 <select required="required" class="form-control" name="voucher_paid_from_account" id="voucher_paid_from_account">
										<option value="<?php echo $_SESSION['default_expense_account']; ?>" selected="selected">Default Expense Account</option>	 
									</select>
									</div>
								</div>
								<div class='form-row'>
								  <div class='col-xs-12 form-group card required'>
									<label>Voucher Description</label>
									<textarea required="required" class="form-control" name="voucher_description" type="text" ></textarea>	
									</div>
								</div>	
								<div class='form-row'>
								  <div class='col-xs-12 form-group card required'>
									<input type="hidden" name="action" id="action" value="add_voucher" />
									<input class="button btn-lg btn-success" type="submit" name="create_voucher" id="create_voucher" value="Create Voucher" />
									</div>
								</div>	
									</form>				
						</div>
					
				</div>		
			</div>	
	<!-- Add Voucher DIV END -->	
 <?php }
 
else {
	$slect_data= DB::query ("SELECT * from ".DB_PREFIX.$_SESSION['co_prefix']."voucher_expense WHERE voucher_id='".$new_voucher_id."'");
	foreach ($slect_data as $select_voucher){
 ?>
<div class="well" style="width:80%; margin-left: auto; margin-right: auto ;">
 <b>Voucher ID: </b><input type="text" value="<?php echo $new_voucher_id; ?>" size="2" readonly="true">
 <BR><BR>
 <table  class="table table-bordered table-hover" >
                <tbody> 
                    <tr>         
                        <td>Voucher Ref#</td>
                        <td><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="company_name" data-name="company_name" data-type="text" data-pk="" data-title="Edit Voucher Ref#"><?php echo  $select_voucher['voucher_ref_no']; ?></a></td>
        
                        <td>Voucher Date</td>
                        <td><a title="" data-original-title="" class="editable editable-click" href="#"  data-pk="" data-type="date" data-placement="left" data-title="Select Date"><?php echo  $select_voucher['voucher_date']; ?></a>
						
						</td>
                    </tr>
					<tr>         
                        <td>Paid From Account</td>
                        <td><a title="" data-original-title="" class="editable editable-click" href="#" id="group" data-type="select" data-pk="1" data-placement="right" data-value="5" data-title="Select group">Default Expense Account</a>
						</td>
                             
                        <td>Voucher Description</td>
                        <td><a title="" data-original-title="" data-placement="left" class="editable editable-pre-wrapped editable-click" href="#" id="comments" data-type="textarea" data-pk="1" data-placeholder="Your comments here..." data-title="Enter comments"><?php echo  $select_voucher['voucher description']; ?></a>
						</td>
                    </tr>
				</tbody>
			</table>	
 </div>
<?php
	}
 } ?>
 
 
 
 
 
 
<script type="text/javascript">
				$("#success-alert").fadeTo(1000, 200).slideUp(200, function(){
				$("#success-alert").alert('close');
				window.location = "index.php?route=expense_voucher_add&new_voucher_id=<?php echo $new_voucher_id; ?>";
				});
</script>