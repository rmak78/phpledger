<?php

if(isset($_POST['addfiscalyear'])){
	$fiscal_year_start_date  =   $_POST['fiscal_year_start_date'];
	$fiscal_year_end_date    =   $_POST['fiscal_year_end_date'];
	$fiscal_year_description =   $_POST['fiscalyeardiscr'];
	$fiscal_year_status      =   $_POST['fiscalyearstatus'];
 $insert = DB::Insert(DB_PREFIX.$_SESSION['co_prefix'].'fiscal_years', array(
 'fiscal_year_desc'			=>	$fiscal_year_description,	
 'fiscal_year_start_date'	=>	$fiscal_year_start_date,
 'fiscal_year_end_date'		=>	$fiscal_year_end_date,
 'fy_status'				=>	$fiscal_year_status
 ));
	}
	
  
?>  
  <!-- Main content -->
        <section class="content">

 <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Add Fiscal Year</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
<div class="box-body">
<form class="form-horizontal" role="form" method="POST" action="">

<div class="form-group">
                <label class="col-md-3 col-sm-3 control-label">Select Fiscal Year Start Date:</label>
                    <div class="col-md-9 col-sm-9">
                     <div class="input-group date">
                     <input type="text" class="form-control pull-right" name="fiscal_year_start_date" id="fiscalyeardaterange"  /><span class="input-group-addon" ><i class="fa fa-calendar"></i></span>
                    </div> <!-- /.input group -->
                    </div>
</div> <!-- /form-group -->
<div class="form-group">
                <label class="col-md-3 col-sm-3 control-label">Select Fiscal Year End Date:</label>
                    <div class="col-md-9 col-sm-9">
                     <div class="input-group date">
                     <input type="text" class="form-control pull-right" name="fiscal_year_end_date" id="fiscalyeardaterange" /><span class="input-group-addon" ><i class="fa fa-calendar"></i></span>
                    </div> <!-- /.input group -->
                    </div>
</div>

<div class="form-group">
                <label class="col-md-3 col-sm-3 control-label">Description:</label>
                    <div class="col-md-9 col-sm-9">
                      <input type="text" class="form-control pull-right" name="fiscalyeardiscr" id="fiscalyeardiscr"/>  
                    </div>
</div> <!-- /form-group -->
<div class="form-group">
                <label class="col-md-3 col-sm-3 control-label">Status:</label>
                    <div class="col-md-9 col-sm-9">
                    <select name="fiscalyearstatus" class="form-control">
						<option value="active">Active</option>
						<option value="in-active">In-Active</option>
					</select>
                    </div>
</div> <!-- /form-group -->    
<div class="form-group">
 <label class="col-md-3 col-sm-3 control-label">&nbsp;</label>             
	<div class="col-md-9 col-sm-9">
		<button type="submit" class='btn btn-success btn-lg' name="addfiscalyear" value="Save">Save &nbsp; <i class="fa fa fa-floppy-o"></i></button>
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
		 
