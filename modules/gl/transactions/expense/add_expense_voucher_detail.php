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
     <div class="progress">
		<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
        <span class="sr-only">100% </span>
        </div>
      </div>		  
          <div class="row">
            <div class="col-xs-12">
              <h2 class="page-header">
                <i class="fa fa-globe"></i> Sutlej Solutions.
                <small class="pull-right"><?php echo date("j / n / Y"); ?></small>
              </h2>
            </div><!-- /.col -->
          </div>
          <!-- info row -->
          <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
                <strong>Voucher Description</strong>
				<BR/>
            </div><!-- /.col -->
			<div class="col-sm-4 invoice-col">
              <b>Invoice #007612</b><br/>
              <b>Voucher Ref#:</b> 4F3S8J<br/>
              <b>Voucher Date:</b> 2/22/2014<br/>
              <b>Paid from Account:</b> 968-34567
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
                      <tr>
                        <td><select>
							<option>Option1</option>
							<option>Option2</option>
						</select></td>
                        <td>Nestle bottle</td>
                        <td>500</td>
                        <td>
						<img src="" height="50" width="50"/> 
						</td>
                        <td><a href="#" class="btn btn-danger"><i class="fa fa-trash"></i>&nbsp;Delete</a>
						<a href="#" class="btn btn-success"><i class="fa fa-pencil"></i>&nbsp;Edit</a>
						</td>
                      </tr>
					</tbody>
				</table>
            </div><!-- /.col -->
          </div><!-- /.row -->

          <div class="row">
            <div class="col-xs-6">
              <p class="lead">Amount Due 2/22/2014</p>
              <div class="table-responsive">
                <table class="table">
                  <tr>
                    <th style="width:50%">Subtotal:</th>
                    <td>$250.30</td>
                  </tr>
                  <tr>
                    <th>Tax (9.3%)</th>
                    <td>$10.34</td>
                  </tr>
                  <tr>
                    <th>Shipping:</th>
                    <td>$5.80</td>
                  </tr>
                  <tr>
                    <th>Total:</th>
                    <td>$265.24</td>
                  </tr>
                </table>
              </div>
            </div><!-- /.col -->
          </div><!-- /.row -->

          <!-- this row will not appear when printing -->
          <div class="row no-print">
            <div class="col-xs-12">
              <a href="invoice-print.php" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
              <button class="btn btn-success pull-right"><i class="fa fa-credit-card"></i> Submit Payment</button>
              <button class="btn btn-primary pull-right" style="margin-right: 5px;"><i class="fa fa-download"></i> Generate PDF</button>
            </div>
          </div>
        </section>
		
		
<!-- Modal Add Detail -->

<div id="addExpenseDetailModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Add Detail</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>		