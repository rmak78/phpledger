  <!-- Main content -->
<section class="content">
 <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Create Expense Voucher (Draft)</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
			<div class="box-body">
				<form class="form-inline">
					<div class="form-group">
						<label class="control-label col-sm-6">Voucher Ref#&nbsp;</label>
						<input class="form-control" required="required" name="voucher_ref" id="voucher_ref" value="" />
					</div>
					<div class="form-group">
						<label class="control-label col-sm-6">Voucher Tags&nbsp;</label>
						<input required="required" class="form-control" name="voucher_tags" type="text" ></textarea>	
					</div>
					<div class="form-group">
						<label class="control-label col-sm-6">Voucher Date&nbsp;</label>
						<input name="voucher_date" required="required" id="voucher_date"   class="date-picker form-control" size="16" type="text" value="">
					</div>
					<div class="form-group">
						<label class="control-label col-sm-6">Voucher Status&nbsp;</label>
						<input required="required" class="form-control" name="voucher_status" type="text" disabled="" value="Draft"></textarea>	
					</div>
					<div class="form-group">
						<label class="control-label col-sm-6">Paid From Account&nbsp;</label>	
						<select required="required" class="form-control" name="voucher_paid_from_account" id="voucher_paid_from_account">
						<option value="<?php echo $_SESSION['default_expense_account']; ?>" selected="selected">Default Expense Account</option>	 
					</select>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-6">Voucher Description&nbsp;</label>
						<textarea cols="70" rows="1" required="required" class="form-control" name="voucher_description" type="text" ></textarea>	
					</div>
				</form>
			</div>
		</div>
		<div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Expense Details</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
			<div class="box-body">
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
                        <td><a href="#">Edit</a></td>
                      </tr>
					</tbody>
				</table>	
			</div>
		</div>
		
</section>