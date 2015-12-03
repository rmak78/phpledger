<?php
$voucher_id = '';
if(isset($_POST['voucher_id'])){
	$voucher_id = $_POST['voucher_id'];
}
if(isset($_GET['voucher_id'])){
	$voucher_id = $_GET['voucher_id'];
}
if(isset($_POST['voucher_ref'])){
	$voucher_ref = $_POST['voucher_ref'];
}
if(isset($_POST['voucher_date'])){
	$voucher_date = $_POST['voucher_date'];
}
if(isset($_POST['voucher_paid_from_account'])){
	$voucher_paid_from_account = $_POST['voucher_paid_from_account'];
}
if(isset($_POST['account_desc_long'])){
	$account_desc_long = $_POST['account_desc_long'];
}
if(isset($_POST['addExpenseVoucer'])){
	
	$voucher_id = create_new_expense_voucher($voucher_ref, $voucher_date, $account_desc_long, $voucher_paid_from_account);
}
?> 
<!-- check voucher id, if no voucher id then nothing to show -->
<?php if($voucher_id <> ''): ?>       
		<!-- Main content -->
        <section class="invoice">
          <!-- title row -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Add Expense Voucher Details</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
   <div class="box-body">
	<?php if($voucher_id <> ''): ?>
     <div class="progress">
		<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
        <span class="sr-only">100% </span>
        </div>
      </div>	
		<?php else: ?>
		<div class="progress">
		<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="90" style="width: 90%">
        <span class="sr-only">90% </span>
        </div>
      </div>
	  <?php endif; ?>
          <div class="row">
            <div class="col-xs-12">
              <h2 class="page-header">
				<?php $logo_path = DB::queryfirstfield('SELECT company_logo_icon FROM sa_companies'); ?>
                <i><img src="<?php echo $logo_path; ?>" height="50px" width="50px" /></i> <?php echo $_SESSION['company_name']; ?>
                <small class="pull-right"><?php echo date("j / n / Y"); ?></small>
              </h2>
            </div><!-- /.col -->
          </div>
          <!-- info row -->
          <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
                <strong>Voucher Description</strong><BR/>
				<?php echo $account_desc_long; ?>
				<BR/>
				 <?php $account_desc = DB::queryfirstfield("SELECT c.`account_desc_long` FROM sa_test_coa c WHERE c.`account_id`='".$voucher_paid_from_account."'"); ?>
              <b>Paid from Account:</b> <?php echo $account_desc; ?>
            </div><!-- /.col -->
			<div class="col-sm-4 invoice-col">
			<?php $invoice_no = DB::queryfirstfield("SELECT COUNT(*) FROM ".DB_PREFIX.$_SESSION['co_prefix']."voucher_expense"); ?>
              <b>Invoice# </b><?php echo $invoice_no+1; ?><br/>
              <b>Voucher Ref#:</b> <?php echo $voucher_ref; ?><br/>
              <b>Voucher Date:</b> <?php echo $voucher_date; ?><br/>
            </div><!-- /.col -->
			 <div class="col-sm-4 invoice-col">
              <a href="#addExpenseDetailModal" role="button" class="btn btn-large btn-primary pull-right" data-toggle="modal"><i class="fa fa-credit-card"></i> Add Detail</a>
            </div><!-- /.col -->
          </div><!-- /.row -->
		</div>
	</div>	
          <!-- Table row -->
          <div class="row">
            <div class="col-xs-12 table-responsive">
              <table id="example1" class="table table-bordered table-striped">
                    <thead>
                      <tr>
						<th>Expense Type</th>
                        <th>Expense Description</th>
                        <th>Ammount</th>
                        <th>Attachment</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
						<?php
						$expense_sql = DB::query("SELECT * FROM ".DB_PREFIX.$_SESSION['co_prefix']."voucher_expense_detail ex WHERE ex.`voucher_id`='".$voucher_id."'");
						foreach($expense_sql as $expense){
						?>
                      <tr>
                        <td><?php echo $expense['expense_account_id']; ?></td>
                        <td><?php echo $expense['expense_description']; ?></td>
                        <td><?php echo $expense['expense_amount']; ?></td>
                        <td>
						<?php } ?>
							<?php
						$attachment_sql = DB::query("SELECT * FROM ".DB_PREFIX.$_SESSION['co_prefix']."voucher_expense ex WHERE ex.`voucher_id`='".$voucher_id."'");
						foreach($attachment_sql as $expense){
						?>
							<?php if($expense['has_attachment']==0): ?>
								<p style="color:red">NO ATTACHMENT</p>
							<?php else: ?>
								<img src="<?php echo $expense['expense_attachment']; ?>" height="50" width="50"/> 
								<?php endif; ?>
						</td>
						
                        <td><a href="#delExpenseDetailModal" class="btn btn-danger" data-toggle="modal"><i class="fa fa-trash"></i>&nbsp;Delete</a>
						<a href="#editExpenseDetailModal" class="btn btn-success" data-toggle="modal"><i class="fa fa-pencil"></i>&nbsp;Edit</a>
						</td>
                      </tr>
					  <?php } ?>
					</tbody>
				</table>
            </div><!-- /.col -->
          </div><!-- /.row -->

          <div class="row">
            <div class="col-xs-6">
              <p class="lead">Amount Due <?php echo $voucher_date; ?></p>
              <div class="table-responsive">
                <table class="table">
                    <th>Total:</th>
                    <td>
					<?php
						$voucher_total = DB::queryfirstfield("SELECT e.`voucher_total` FROM sa_test_voucher_expense e WHERE e.`voucher_id`='".$voucher_id."'");
						echo $voucher_total;
					?>
					</td>
                  </tr>
                </table>
              </div>
            </div><!-- /.col -->
          </div><!-- /.row -->

          <!-- this row will not appear when printing -->
          <div class="row no-print">
            <div class="col-xs-12">
              <a href="invoice-print.php" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
              <button class="btn btn-primary pull-right" style="margin-right: 5px;"><i class="fa fa-download"></i> Generate PDF</button>
            </div>
          </div>
        </section>
<?php else: ?>
	<p style="color:red"> &nbsp;&nbsp;&nbsp;&nbsp;Sorry..! Please provide the voucher header details </p>
	<a href="<?php echo SITE_ROOT."index.php?route=modules/gl/transactions/expense/add_expense_voucher" ?>"> &nbsp;&nbsp;&nbsp;&nbsp;Click here to add expense voucher </a>
<?php endif; ?>		
<!-- Modal Add Detail -->

<div id="addExpenseDetailModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Add Expense Detail</h4>
            </div>
            <div class="modal-body">
				 <form class="form-horizontal" role="form" >
    <div class="form-group">
      <label class="control-label col-sm-4" for="expense_type">Expense Type:</label>
      <div class="col-sm-8">
        <select class="form-control" id="expense_type" name="expense_type">
			<option>Cheque</option>
			<option>Cash</option>
		</select>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-4" for="expense_detail">Expense Detail:</label>
      <div class="col-sm-8">          
        <input type="text" class="form-control" id="expense_detail" name="expense_detail" placeholder="Expense Detail">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-4" for="expense_ammount">Expense Ammount:</label>
      <div class="col-sm-8">          
        <input type="text" class="form-control" id="expense_ammount" name="expense_ammount" placeholder="Expense Ammount">
      </div>
    </div>
	<div class="form-group">
      <label class="control-label col-sm-4" for="expense_ammount">Expense Attachment:</label>
      <div class="col-sm-8">          
        <input type="file" id="expense_attachment" name="expense_attachment">
      </div>
    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save</button>
            </div>
			</form>
        </div>
    </div>
</div>	

<!-- Modal Edit Detail -->

<div id="editExpenseDetailModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Edit Expense Detail</h4>
            </div>
            <div class="modal-body">
				 <form class="form-horizontal" role="form">
    <div class="form-group">
      <label class="control-label col-sm-4" for="expense_type">Expense Type:</label>
      <div class="col-sm-8">
        <select class="form-control" id="expense_type" name="expense_type">
			<option>Cheque</option>
			<option>Cash</option>
		</select>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-4" for="expense_detail">Expense Detail:</label>
      <div class="col-sm-8">          
        <input type="text" class="form-control" id="expense_detail" name="expense_detail" placeholder="Expense Detail">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-4" for="expense_ammount">Expense Ammount:</label>
      <div class="col-sm-8">          
        <input type="text" class="form-control" id="expense_ammount" name="expense_ammount" placeholder="Expense Ammount">
      </div>
    </div>
	<div class="form-group">
      <label class="control-label col-sm-4" for="expense_ammount">Expense Attachment:</label>
      <div class="col-sm-8">          
        <input type="file" id="expense_attachment" name="expense_attachment">
      </div>
    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save</button>
            </div>
			</form>
        </div>
    </div>
</div>	

<!-- Delete Modal Expense Detail -->
<div class="modal fade" id="delExpenseDetailModal" tabindex="-1" role="dialog" aria-labelledby="delModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="delModalLabel">Confirm Delete</h4>
      </div>
<form role="form" method="POST"  id="frmdel2" name="delete" action="" class="form-inline" >
 
      <div class="modal-body bg-warning">
       Are you sure you want to delete this Expense Detail..
				<input type="hidden" name="voucher_detail_id" id="voucher_detail_id" value="0" />
		<input type="hidden" name="operation" id="delVoucherDetail" value="delVoucherDetail" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="submitdeladj btn btn-danger">Delete</button>
      </div>
	  </form>
    </div>
  </div>
</div>

