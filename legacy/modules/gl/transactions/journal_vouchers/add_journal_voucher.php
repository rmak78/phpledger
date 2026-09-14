<?php
$voucher_desc = "";
$voucher_ref = "";
$voucher_date = "";
$error_ref = 0;
if(isset($_POST['voucher_ref'])){
	$voucher_ref = $_POST['voucher_ref'];
}

if(isset($_POST['voucher_date'])){
	$voucher_date = $_POST['voucher_date'];
}

if(isset($_POST['voucher_desc'])){
	$voucher_desc = $_POST['voucher_desc'];
}

if (isset($_POST['add'])){
	 
	// insert voucher in jv table and redirect to voucher details page
	$new_voucher_id = create_new_journal_voucher(
							  $voucher_ref
							, $voucher_date							
							, $voucher_desc
							); 
	if($new_voucher_id <> 0) {
		echo '<script>window.location.replace("'.SITE_ROOT.'?route=modules/gl/transactions/journal_vouchers/add_journal_voucher_detail&voucher_id='.$new_voucher_id.'");</script>';
		echo "i was here";
	} else {
		if (voucher_ref_exists($voucher_ref)){
			$error_ref = 1;
		} else {
			$error_db = 1;
		}
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
        <section class="content">

 <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Create Journal Voucher (Step 1)</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
<div class="box-body">
     <div class="progress">
		<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="width: 25%">
        <span class="sr-only">25% Complete  </span>
        </div>
      </div>
<form class="form-horizontal" role="form" method="POST" action="<?php echo SITE_ROOT."index.php?route=modules/gl/transactions/journal_vouchers/add_journal_voucher" ?>">
				<div class="form-group <?php if ($error_ref == 1) { echo "has-error"; } ?>">
					<label class="col-md-3 col-sm-3 control-label"><?php if ($error_ref == 1) { echo '<i class="fa fa-rimes"></i>&nbsp'; } ?>Voucher Ref#&nbsp;</label>
						<div class="col-md-2 col-sm-2">
						<input class="form-control" required="required" name="voucher_ref" id="voucher_ref" value="<?php echo $voucher_ref; ?>" />				
						<p class="help-block"> </p>
					</div><!-- /.col -->
				</div> <!-- /form-group --> 
				<div class="form-group">
					<label class="col-md-3 col-sm-3 control-label">Voucher Date&nbsp;</label>
						<div class="col-md-9 col-sm-9">
						<div class="input-group date">
						<input  name="voucher_date"type="text"  required="required" class="form-control" value="<?php echo $voucher_date; ?>"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
						</div>			
						<p class="help-block"> </p>
					</div><!-- /.col -->
				</div> <!-- /form-group --> 
				 
				<div class="form-group">
					<label class="col-md-3 col-sm-3 control-label">Voucher Description: &nbsp;</label>
						<div class="col-md-9 col-sm-9">
				<textarea required="required" name="voucher_desc" class="form-control textarea"  ><?php echo $voucher_desc; ?></textarea>				
						<p class="help-block"> </p>
					</div><!-- /.col -->
				</div> <!-- /form-group --> 
        
<div class="form-group">
	<div class="col-sm-12">
		<button type="submit" class='btn btn-success btn-lg pull-right' name="add" value="Next">Save & Add Details &nbsp; <i class="fa fa-chevron-circle-right"></i></button>
	</div>	<!-- /.col -->
</div>		<!-- /form-group -->	   
</form>
            <!-- Add Account Form Goes here -->
             </div><!-- /.box-body -->
            <div class="box-footer">
             <small> Please provide the voucher details and move to next page</small>
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
     	 </section><!-- /.content -->