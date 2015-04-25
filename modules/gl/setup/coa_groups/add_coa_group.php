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
    
        <!-- general form elements -->
    <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title">Provide the required fields</h3>
				   <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
                </div><!-- /.box-header -->
                <!-- form start -->
                <form role="form"class="form-horizontal" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                  <div class="box-body">
                    <div class="form-group">
								<label class="col-md-3 col-sm-3 control-label">Group Name:</label>
								<div class="col-md-9 col-sm-9">
									 <input class="form-control" type="text" required name="group_code" id="group_code"
						   pattern="[A-Z0-9]{4}"  maxlength="4">
								
							 <p class="help-block">Must be length 4 and Capital characters only</p>
							</div>
								</div>
			    	
					
                    <div class="form-group">
						<label class="col-md-3 col-sm-3 control-label">Group Description:</label>
						<div class="col-md-9 col-sm-9">
						 <input class="form-control" type="text" required name="group_description" id="group_description">
						</div>
					</div>		
					
					
				<div class="form-group">
                        <label class="col-md-3 col-sm-3 control-label">Group Type:</label>
						<div class="col-md-9 col-sm-9">
						<input type="radio" name="account_type" value="balance_sheet" checked> Balance Sheet&nbsp;
                        <input type="radio" name="account_type" value="pls_group"> Profit &Loss Statements Group&nbsp;
						<input type="radio" name="account_type" value="statistics_only"> Statistics Only&nbsp;
						</div>
				</div>
				<div id="balance_sheet">
					<div class="form-group">
							<label class="col-md-3 col-sm-3 control-label">Balance Sheet Side:</label>
							 <div class="col-md-9 col-sm-9">
							 <select class="form-control" required  name="balance_sheet_side" id="balance_sheet_side">
								<option value="debit">Debit</option>
								<option value="credit">Credit</option>
							</select>
							</div>
					</div>
				</div>
				<div id="pls">
					 <div class="form-group">
							<label class="col-md-3 col-sm-3 control-label">Profit &Loss Statements Side:</label>                         
							<div class="col-md-9 col-sm-9">
							<select class="form-control" required  name="pls_side" id="pls_side">
								 <option value="income">Income</option>
								<option value="expense">Expense</option>
							</select>
							</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 col-sm-3 control-label">Group Status:</label>
					<div class="col-md-9 col-sm-9">
					<select class="form-control"  required  name="group_status" id="group_status">
                        <option value="active">Active</option>
                        <option value="in-active">In-Active</option>  
                    </select>
					</div>
				</div>
					
				</div><!-- /.box-body -->
                  <div class="box-footer">
					<div class="form-group">
					<div class="col-sm-3">
					</div>
					<div class="col-sm-9">
                    <input type="submit" class='btn btn-primary' name="submit" value="SAVE">
						<a class='btn btn-danger btn-lg pull-right' href="<?php echo SITE_ROOT."index.php?route=modules/gl/setup/coa_groups/add_coa_group" ?>">Cancel & Restart &nbsp;<i class="fa fa-chevron-circle-right"></i></a>
					</div>
					</div>
				 </div>
                </form>
    </div><!-- /.box -->


</section>		

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