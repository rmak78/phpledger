<?php
// voucher heading updation
if(isset($_POST['edit_voucher']))
{
	$voucher_ref = ($_POST['voucher_ref']);
	$voucher_date = getDateTime($_POST['voucher_date'], 'mySQL');
	$voucher_description = $_POST['voucher_description'];
	$voucher_ids= $_POST['voucher_ids'];
	  
   if($voucher_ids<>"")
	{
	 DB::update(DB_PREFIX.$_SESSION['co_prefix'].'voucher_expense', array(
	 	'voucher_ref_no' 			=>  $voucher_ref,	
            'voucher_date' 	 		  => $voucher_date,
            'voucher description' 	  =>   $voucher_description
	 	),
		"voucher_id=%s", $voucher_ids
		);
	}

}
// Add expense
if(isset($_POST['edit_add']))
{
	//$expense_type= $_POST['expense_type'];
	$description=  $_POST['description'];
	$amount=  $_POST['amount'];
	$voucher_ids =$_POST['voucher_ids'];
	 $insert = DB::Insert(DB_PREFIX.$_SESSION['co_prefix'].'voucher_expense_detail', array(
	 			//	'expense_account_id' => $expense_type,
					'expense_description' => $description,
					'expense_amount' 	=> $amount,
					'voucher_id'		=> $voucher_ids
	 ));
	 //total voucher
	 if($amount<>"")
	 {
	  DB::update(DB_PREFIX.$_SESSION['co_prefix'].'voucher_expense', array(
	  
	  				'voucher_total' => $amount
	  ),
	  "voucher_id=%s", $voucher_ids
	  );
	 
	}
}

function create_new_voucher($voucher_ref, $voucher_date, $voucher_description, $voucher_paid_from_account ) {
// get user name and datetime now
$now= getDateTime('mySQL');
echo $now;
 // perform sql insertion
   $insert = DB::Insert(DB_PREFIX.$_SESSION['co_prefix'].'voucher_expense', array(
   				
			'voucher_ref_no' 			=>  $voucher_ref,	
            'voucher_date' 	 		  => $voucher_date,
            'voucher description' 	  =>   $voucher_description,
            'petty_cash_account' 	   	 =>  $voucher_paid_from_account,
			'created_on'				=>  $now
			));
	$id_get =DB::insertId();

return $id_get;
	       


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
	define('NEW_VOUCHER_ID',$new_voucher_id);
	if ($new_voucher_id) {
	$voucher_id = $new_voucher_id;
	echo $voucher_id;
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
<div class="panel panel-info">
<div class='panel-heading' >
				
                
   	<form method="post" action="<?php $_SERVER['PHP_SELF']; ?>" class="form-horizontal">         
<?php


// Show the voucher heading....


if($voucher_id != 0) {
	$slect_data= DB::query ("SELECT * from ".DB_PREFIX.$_SESSION['co_prefix']."voucher_expense WHERE voucher_id='".$voucher_id."'");
	foreach ($slect_data as $select_voucher)
?>
<div class='panel-heading' >
 <div><h4 align="center">Expense Voucher</h4></div>

	<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-6">
<label>Voucher Ref#</label>

				<input class="form-control" required="required" name="voucher_ref" id="voucher_ref" value=" <?php echo  $select_voucher['voucher_ref_no']; ?>" />
               
                </div>
               <div class="col-xs-12 col-sm-6 col-md-6">
               
               
 <label>Voucher Date</label>
  
	<input name="voucher_date" required="required" id="voucher_date" align="right"  class="date-picker form-control" size="16" type="text" value="<?php echo  $select_voucher['voucher_date']; ?>" />

</div>


  
    </div>
    <div class="row">
     <div class="col-xs-12 col-sm-8 col-md-8">
<label>Voucher Description</label>

				<input class="form-control" required="required" name="voucher_description" id="voucher_description" value="<?php echo  $select_voucher['voucher description']; ?>"  />
           </div> 
           <input type="hidden" name="voucher_ids" id="voucher_ids"  value="<?php echo  $select_voucher['voucher_id']; ?>" />  
         <br />   
           <input  class="btn btn-primary btn-sm " type="submit" name="edit_voucher" id="edit_voucher" value="Edit" />
               </div>
                </div>
               </div> 
                 <br>
    
    <!--Body of voucher to Add the expense-->
              
                <div class="'panel-body">
                <div class="row">
    <div class="col-xs-12 col-sm-4 col-md-4">
                <label>Expense Type</label>
				<input class="form-control"  name="expense_type" id="expense_type" value="" />
                </div>
                 <div class="col-xs-12 col-sm-3 col-md-3">
                 <label>Description</label>
				<input class="form-control"  name="description" id="description" value="" />
               </div>
                <div class="col-xs-12 col-sm-3 col-md-3">
                 <label>Amount</label>
				<input class="form-control"  name="amount" id="amount" value="" />
                </div>
            <br />
                   <input  class='btn btn-primary btn-sm' " type="submit" name="edit_add" id="edit_add" value="Add" align="right" />
              
               
                </div>
                 <div class="row">
                   <div class="col-xs-12 col-sm-4 col-md-4">
                  <label>Total Voucher</label>
				<input class="form-control" required="required" name="total_voucher" id="total_voucher" />
                value="<?php echo  $select_voucher['voucher_total']; ?> " />
                 </div>
               <br />
                 <input class='btn btn-primary btn-sm' type="submit" name="save_voucher" id="save_voucher" value="Save Voucher" align="right"  />
               
               
                </div>
                </div>
<?php  } //end Voucher Edit Screen ?>	
</form>	


</div>
<?php
if($voucher_id == 0) { // Show Create Voucher Form
if ($error_message <> "") {
echo $error_message;
}
?>
<div class="container">
<div class="panel-body">
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