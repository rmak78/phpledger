<?php
$group_id='';
?>
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
    
<div class="content-wrapper">	
	<section class="content-header">
          <h1>
           Add Chart of Account Group
           
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-book"></i></a>General Ledger</li>
            <li><a href="#">Chart of Account Group</a></li>
            <li class="active">Add new Account Group</li>
          </ol>
        </section>
		 <!-- Main content -->
 <section class="content">
    <div class="row">
<div class="col-md-10">
        <!-- general form elements -->
    <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Provide the required fields</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <form role="form">
                  <div class="box-body">
                    <div class="form-group">
								<label>Group Name:</label>
									 <input class="controls" type="text" required name="group_code" id="group_code"
						   pattern="[A-Z0-9]{4}"  maxlength="4">
							 <p class="help-block">Must be length 4 and Capital characters only</p>
							</div>
							
			    	
					
                    <div class="form-group">
						<label>Group Description:</label>
						 <input class="form-control" type="text" required name="group_description" id="group_description">
					</div>		
					
					
				<div class="form-group">
                        <label>Group Type:</label>
						<input type="radio" name="account_type" value="balance_sheet" checked> Balance Sheet&nbsp;
                        <input type="radio" name="account_type" value="pls_group"> Profit &Loss Statements Group&nbsp;
						<input type="radio" name="account_type" value="statistics_only"> Statistics Only&nbsp;
				</div>
                <div class="form-group">
                        <label>Balance Sheet Side:</label>
						 <select class="form-control" required  name="balance_sheet_side" id="balance_sheet_side">
							<option value="debit">Debit</option>
							<option value="credit">Credit</option>
						</select>
				</div>
				 <div class="form-group">
                        <label>Profit &Loss Statements Side:</label>                         
						<select class="form-control" required  name="pls_side" id="pls_side">
							 <option value="income">Income</option>
							<option value="expense">Expense</option>
						</select>
				</div>
				<div class="form-group">
					<label>Group Status:</label>
					<select class="form-control"  required  name="group_status" id="group_status">
                        <option value="active">Active</option>
                       <option value="in-active">In-Active</option>  
                        </select>
				</div>
					
				</div><!-- /.box-body -->
                  <div class="box-footer">
                    <input type="submit" class='btn btn-primary' name="submit" value="SAVE">
                  </div>
                </form>
    </div><!-- /.box -->
</div> 	
</div>	
</div>
</section>		