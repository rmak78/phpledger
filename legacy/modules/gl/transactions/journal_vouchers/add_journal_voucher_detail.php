<?php
print_r($_GET);
echo "<br/>";
print_r($_POST);
$voucher_desc = "";
$voucher_ref = "";
$voucher_date = "";
if(isset($_GET['voucher_id'])) {
	$voucher_id = $_GET['voucher_id'];
}
if($voucher_id > 0) {
	$sql = "SELECT * FROM ".DB_PREFIX.$_SESSION['co_prefix']."journal_vouchers WHERE voucher_id=".$voucher_id;
	$voucher = DB::queryFirstRow($sql);

	$voucher_ref = $voucher['voucher_ref_no'];
	$voucher_date = $voucher['voucher_date'];
	$voucher_desc = $voucher['voucher description'];
}
if(isset($_POST['account'])){
	$account_id = $_POST['account'];
}

if(isset($_POST['debit_amount'])){
	$debit_amount = $_POST['debit_amount'];
}

if(isset($_POST['credit_amount'])){
	$credit_amount = $_POST['credit_amount'];
}
if(isset($_POST['entry_desc'])){
	$entry_desc = $_POST['entry_desc'];
}

if (isset($_POST['save'])){
	 
	
	$voucher_detail_id = journal_voucher_detail(
							  $voucher_id
							, $voucher_date
							, $account_id	
							, $entry_desc
							, $debit_amount
							, $credit_amount
							); 
	if($voucher_detail_id <> 0) {
		echo '<script>window.location.replace("'.SITE_ROOT.'?route=modules/gl/transactions/journal_vouchers/view_journal_vouchers_detail&voucher_id='.$voucher_id.'");</script>';
		///echo "i was here";
	}
}	


?><!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          	Journal Voucher (J.V.)
            <small>Create Journal Voucher (Draft)</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="<?php echo SITE_ROOT; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">General Ledger</a></li>
            <li>Journal Vouchers</li>
            <li class="active">Add New Voucher</li>
          </ol>
        </section>
        <!-- Main content -->
        <section >
          <!-- title row -->
          <div class="box">
             <div class="box-header with-border">
              <h3 class="box-title">Create Journal Voucher (Step 2)</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
<div class="box-body">
			<div class="progress">
		<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%">
        <span class="sr-only">50% Complete  </span>
        </div>
      </div>
          <div class="row info">
            <div class="col-xs-12">
              <h2 class="page-header">
                <i class="fa fa-globe"></i> &nbsp;<?php echo $_SESSION['company_name']; ?>
                <small class="pull-right"><?php echo getDateTime(0,"dLong"); ?></small>
              </h2>
            </div><!-- /.col -->
          </div>
          <!-- info row -->
          <div class="row ">
            <div class="col-sm-8  "> 
                <strong>Voucher Description :</strong>
                <BR/>
                <p><?php echo $voucher_desc; ?></p>
            </div><!-- /.col -->
			<div class="col-sm-4  ">
              <b>JV ID: </b> <?php echo $voucher_id; ?></b><br/>
              <b>Voucher Ref#:</b> <?php echo $voucher_ref; ?><br/>
              <b>Voucher Date:</b> <?php echo getDateTime($voucher_date,"dLong"); ?><br/>
              
            </div><!-- /.col -->
			 
          </div><!-- /.row -->
          
          
             </div><!-- /.box-body -->
            <div class="box-footer">
             <small> Explanation text for JV header</small>
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
     	 </section><!-- /.content -->
 
          <section >
          <!-- title row -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Add Voucher Details</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
<div class="box-body">
   <form role="form"class="form-horizontal" method="post" action="<?php echo SITE_ROOT."index.php?route=modules/gl/transactions/journal_vouchers/add_journal_voucher_detail&voucher_id=".$voucher_id ?>">
					<div class="form-group">
						<label class="col-md-3 col-sm-3 control-label"> Account &nbsp;</label>
							<div class="col-md-9 col-sm-9">
								 <select class="form-control" name="account" required>
										<option value="0"> -- None --</option>
										<?php 
										$accounts_query = "SELECT account_id, account_code, account_desc_short from ";
										$accounts_query .= DB_PREFIX.$_SESSION['co_prefix']."coa WHERE 1 = 1";
										
										$accounts = DB::query($accounts_query);
										
										foreach ($accounts as $account) {
										?>					
											<option value="<?php echo $account['account_id']; ?>" ><?php echo $account['account_code']; ?> - <?php echo $account['account_desc_short']; ?></option>
										<?php 
										}
										?>

						</select>
							</div>
						</div> <!-- /form-group -->		   
					<div class="form-group">
						<label class="col-md-3 col-sm-3 control-label">Debit Ammount&nbsp;</label>
							<div class="col-md-9 col-sm-9">
								 <input class="form-control" placeholder="00.00" type="text" required name="debit_amount" id="debit_amount"  >
							</div>
					</div> <!-- /form-group -->
					<div class="form-group">
						<label class="col-md-3 col-sm-3 control-label">Credit Ammount&nbsp; </label>
							<div class="col-md-9 col-sm-9">
								 <input class="form-control" placeholder="00.00" type="text" required name="credit_amount" id="credit_amount" >
							</div>
					</div> <!-- /form-group -->
					<div class="form-group">
					<label class="col-md-3 col-sm-3 control-label">Entry Description &nbsp;</label>
						<div class="col-md-9 col-sm-9">
						<textarea required="required" name="entry_desc" class="form-control textarea" > </textarea>				
						<p class="help-block"> </p>
					</div><!-- /.col -->
				</div> <!-- /form-group --> 
					<div class="form-group">
					  <div class="col-sm-12">
						<button type="submit" class='btn btn-success btn-lg pull-right' name="save" value="Next">Save &nbsp; <i class="fa fa-chevron-circle-right"></i></button>
					  </div>	<!-- /.col -->
					</div>
   </form>               
          
</div><!-- /.box-body -->
            <div class="box-footer">
             <small> Explanation text for JV details</small>
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
     	 </section><!-- /.content -->      