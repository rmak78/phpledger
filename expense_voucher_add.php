<?php



function create_new_voucher($voucher_ref, $voucher_date, $voucher_description, $voucher_paid_from_account ) {
// get user name and datetime now

 // perform sql insertion
   $insert = DB::Insert(DB_PREFIX.$_SESSION['co_prefix'].'voucher_expense', array(
   				
			'voucher_ref_no' 			=>  $voucher_ref,	
            'voucher_date' 	 		  => $voucher_date,
            'voucher description' 	  =>   $voucher_description,
            'petty_cash_account' 	   	 =>  $voucher_paid_from_account
			));
	
		
return mysql_insert_id();
	       


}
$voucher_id = 0;
$error_message = "";
if((isset($_GET['voucher_id'])) AND ($_GET['voucher_id'] <> "")) {
$voucher_id = $_GET['voucher_id'];
}
if(isset($_POST['action'])) {
$action = $_POST['action'];
switch ($action) {
    case "add_voucher":
       // create New Voucher
	$voucher_ref = mysql_real_escape_string($_POST['voucher_ref']);
	$voucher_date = getDateTime($_POST['voucher_date'], 'mySQL');
	$voucher_description = 	 mysql_real_escape_string($_POST['voucher_description']);
	$voucher_paid_from_account =  mysql_real_escape_string($_POST['voucher_paid_from_account']);
	$new_voucher_id = create_new_voucher($voucher_ref, $voucher_date, $voucher_description, $voucher_paid_from_account );
	if ($new_voucher_id) {
	$voucher_id = $new_voucher_id;
	} else {
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
<div class="container">
				<div class="row">
<?php
if($voucher_id != 0) {
	//Show the header of voucher ....
	
?>
<div class="panel-heading" >
</div>


<?php } //end Voucher Edit Screen ?>		

<?php
if($voucher_id == 0) { // Show Create Voucher Form
if ($error_message <> "") {
echo $error_message;
}
?>

				<form method="post" action="<?php $_SERVER['PHP_SELF']; ?>" class="form-horizontal">
				<label>Voucher Ref#</label>
				<input class="form-control" required="required" name="voucher_ref" id="voucher_ref" value="" />
				<label>Voucher Date</label>
				<input name="voucher_date" required="required" id="voucher_date"   class="date-picker form-control" size="16" type="text" value="">
			 	<label>Paid From Account</label>	
				 <select required="required" class="form-control" name="voucher_paid_from_account" id="voucher_paid_from_account">
					 
						<option value="<?php echo $_SESSION['default_expense_account']; ?>" selected="selected">Default Expense Account</option>
					 
				</select>
							
				<label>Voucher Description</label>
				<textarea required="required" class="form-control" name="voucher_description" type="text" ></textarea>	
				<input class="button btn-lg btn-success" type="submit" name="create_voucher" id="create_voucher" value="Create Voucher" />		
				<input type="hidden" name="action" id="action" value="add_voucher" />
				
				</form>
<?php } //end create Voucher Form ?>				
				
				
				</div>
			
			</div>