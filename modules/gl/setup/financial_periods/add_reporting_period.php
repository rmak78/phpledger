<?php
if(isset($_POST['add_reporting_periods'])){
	$selected_fiscal_year			= $_POST['selected_fiscal_year'];
	$reporting_period_description	= $_POST['reporting_period_description'];
	$reporting_period_start_date	= $_POST['reporting_period_start_date'];
	$reporting_period_end_date			= $_POST['reporting_period_end_date'];
	$reporting_period_status		= $_POST['reporting_period_status'];
	 $insert = DB::Insert(DB_PREFIX.$_SESSION['co_prefix'].'reporting_periods', array(
 'fiscal_year_id'					=>  $selected_fiscal_year,
 'reporting_period_desc'			=>	$reporting_period_description,	
 'reporting_period_start_date'		=>	$reporting_period_start_date,
 'reporting_period_end_date'		=>	$reporting_period_end_date,
 'rp_status'						=>	$reporting_period_status
 ));
	
	
}
  
?>  
  <!-- Main content -->
        <section class="content">

 <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Add Reporting Periods </h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
<div class="box-body">
<form class="form-horizontal" role="form" method="POST" action="">
<div class="form-group">
                <label class="col-md-3 col-sm-3 control-label">Select Fiscal Year:</label>
                    <div class="col-md-9 col-sm-9">
					<select type="dropdown" class="form-control pull-right" name="selected_fiscal_year"  >  
                    <option value=""> -- Select --</option>
					<?php
					$sql = "SELECT * FROM ".DB_PREFIX.$_SESSION['co_prefix']."fiscal_years" ;
					$select_fiscal_year = DB::query($sql);
					foreach($select_fiscal_year as $fiscal_year )
					{
                    
					echo "<option value='".$fiscal_year['fiscal_year_id']."'>".$fiscal_year['fiscal_year_desc']."</option>";
					
					
					}
					?>
                    </select>
					</div>
</div> <!-- /form-group -->
<div class="form-group">
                <label class="col-md-3 col-sm-3 control-label">Description:</label>
                    <div class="col-md-9 col-sm-9">
                      <input type="text" class="form-control pull-right" name="reporting_period_description" "/>  
                    </div>
</div> <!-- /form-group -->

<div class="form-group">
                <label class="col-md-3 col-sm-3 control-label">Select Reporting Period Start Date:</label>
                    <div class="col-md-9 col-sm-9">
                     <div class="input-group date">
                     <input type="text" class="form-control pull-right" name="reporting_period_start_date"   /><span class="input-group-addon" ><i class="fa fa-calendar"></i></span>
                    </div> <!-- /.input group -->
                    </div>
</div> <!-- /form-group -->
<div class="form-group">
                <label class="col-md-3 col-sm-3 control-label">Select Reporting Period End Date:</label>
                    <div class="col-md-9 col-sm-9">
                     <div class="input-group date">
                     <input type="text" class="form-control pull-right" name="reporting_period_end_date"   /><span class="input-group-addon" ><i class="fa fa-calendar"></i></span>
                    </div> <!-- /.input group -->
                    </div>
</div>


<div class="form-group">
                <label class="col-md-3 col-sm-3 control-label">Status:</label>
                    <div class="col-md-9 col-sm-9">
                    <select name="reporting_period_status" class="form-control">
						<option value="active">Active</option>
						<option value="in-active">In-Active</option>
					</select>
                    </div>
</div> <!-- /form-group -->    
<div class="form-group">
 <label class="col-md-3 col-sm-3 control-label">&nbsp;</label>             
	<div class="col-md-9 col-sm-9">
		<button type="submit" class='btn btn-success btn-lg' name="add_reporting_periods" value="Save">Save &nbsp; <i class="fa fa fa-floppy-o"></i></button>
	</div>	<!-- /.col -->
</div>		<!-- /form-group -->	   
</form>
            <!-- Add Account Form Goes here -->
             </div><!-- /.box-body -->
            <div class="box-footer">
             <small> Please select date range from year to year for your financial year</small>
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
     	 </section><!-- /.content -->
		 

