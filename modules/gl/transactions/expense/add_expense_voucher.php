<!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          	Expense Voucher
            <small>Create Expense Voucher (Draft)</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="<?php echo SITE_ROOT; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Expense Vouchers</a></li>
            <li class="active">Add New Expense Voucher</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

 <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Create Expense Voucher (Header)</h3>
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
<form class="form-horizontal" role="form" method="POST" action="<?php echo SITE_ROOT."index.php?route=modules/gl/transactions/expense/add_expense_voucher_detail" ?>">
				<div class="form-group">
					<label class="col-md-3 col-sm-3 control-label">Voucher Ref#&nbsp;</label>
						<div class="col-md-2 col-sm-2">
						<input class="form-control" required="required" name="voucher_ref" id="voucher_ref" value="" />				
						<p class="help-block"> </p>
					</div><!-- /.col -->
				</div> <!-- /form-group --> 
				<div class="form-group">
					<label class="col-md-3 col-sm-3 control-label">Voucher Date&nbsp;</label>
						<div class="col-md-9 col-sm-9">
						<input name="voucher_date" required="required" id="voucher_date"   class="date-picker form-control" size="16" type="text" value="" />				
						<p class="help-block"> </p>
					</div><!-- /.col -->
				</div> <!-- /form-group --> 
				<div class="form-group">
					<label class="col-md-3 col-sm-3 control-label">Paid From Account&nbsp;</label>
						<div class="col-md-9 col-sm-9">
						<select required="required" class="form-control" name="voucher_paid_from_account" id="voucher_paid_from_account">
						<?php 
							$accounts = DB::query("SELECT c.`account_id`, c.`account_desc_short` FROM ".DB_PREFIX.$_SESSION['co_prefix']."coa c WHERE c.`activity_account`=1 AND c.`account_status`='active'");
							foreach($accounts as $account){
						?>
						<option value="<?php echo $account['account_id']; ?>"><?php echo $account['account_desc_short']; ?></option>	 
						<?php } ?>
					</select>				
						<p class="help-block"> </p>
					</div><!-- /.col -->
				</div> <!-- /form-group --> 
				<div class="form-group">
					<label class="col-md-3 col-sm-3 control-label">Voucher Description: &nbsp;</label>
						<div class="col-md-9 col-sm-9">
				<textarea  name="account_desc_long" id="account_desc_long" class="form-control textarea"  ></textarea>				
						<p class="help-block"> </p>
					</div><!-- /.col -->
				</div> <!-- /form-group --> 
        
<div class="form-group">
	<div class="col-sm-12">
		<button type="submit" class='btn btn-success btn-lg pull-right' name="addExpenseVoucer" id="addExpenseVoucer" value="Next">Next &nbsp; <i class="fa fa-chevron-circle-right"></i></button>
	</div>	<!-- /.col -->
</div>		<!-- /form-group -->	   
</form>
            <!-- Add Account Form Goes here -->
             </div><!-- /.box-body -->
            <div class="box-footer">
             <small> Please provide the expense header</small>
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
     	 </section><!-- /.content -->