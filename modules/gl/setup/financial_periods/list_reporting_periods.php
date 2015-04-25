<?php

$tbl = new HTML_Table('', 'table table-striped table-bordered');
$tbl->addRow();
$tbl->addCell('Fiscal Year', '', 'header');
$tbl->addCell('Period Description', '', 'header');
$tbl->addCell('Start Date', '', 'header');
$tbl->addCell('End Date', '', 'header');
$tbl->addCell('Status', '', 'header');
$tbl->addCell('Actions', '', 'header');
?>

<?php
$sql = 'SELECT * FROM '.DB_PREFIX.$_SESSION['co_prefix'].'reporting_periods ORDER by reporting_period_start_date';
$get_rp = DB::query($sql);
foreach($get_rp as $rp) {
	$fiscal_year = DB::queryFirstField("SELECT fiscal_year_desc FROM ".DB_PREFIX.$_SESSION['co_prefix']."fiscal_years WHERE fiscal_year_id =".$rp['fiscal_year_id']);
	 
$tbl->addRow();
$tbl->addCell($fiscal_year);
$tbl->addCell($rp['reporting_period_desc']);
$tbl->addCell(getDateTime($rp['reporting_period_start_date'],"dLong"));
$tbl->addCell(getDateTime($rp['reporting_period_end_date'],"dLong"));
$tbl->addCell($rp['rp_status']);
$tbl->addCell("<a class='pull btn btn-danger btn-xs' href ='".$_SERVER['PHP_SELF']."?route=modules/gl/setup/financial_periods/edit_reporting_period&reporting_period_id=".$rp['reporting_period_id']."'>Edit Reporting Period&nbsp;&nbsp;<span class='glyphicon glyphicon-edit'></span></a>
			   ");
}
			  

?>
 <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          		Reporting Periods 
            <small>Defined Reporting Periods In Fiscal Years.</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="<?php echo SITE_ROOT; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">General Ledger</a></li>
            <li class="active">List Reporting Periods</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

 <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Reporting Periods</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
				<?php  echo $tbl->display(); ?>
            </div><!-- /.box-body -->
            <div class="box-footer">
             <small> Please do not make changes to these unless you are really sure what you are doing. making changes here have system wide impact</small>
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
     	 </section><!-- /.content -->