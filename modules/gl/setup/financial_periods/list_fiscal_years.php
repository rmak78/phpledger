<?php

$tbl = new HTML_Table('', 'table table-striped table-bordered');
$tbl->addRow();
$tbl->addCell('FY ID', '', 'header');
$tbl->addCell('FY Description', '', 'header');
$tbl->addCell('Start Date', '', 'header');
$tbl->addCell('End Date', '', 'header');
$tbl->addCell('Status', '', 'header');
$tbl->addCell('Actions', '', 'header');
?>

<?php
$sql = 'SELECT * FROM '.DB_PREFIX.$_SESSION['co_prefix'].'fiscal_years ORDER by fiscal_year_start_date';
$get_fy = DB::query($sql);
foreach($get_fy as $fy) { 
$tbl->addRow();
$tbl->addCell($fy['fiscal_year_id']);
$tbl->addCell($fy['fiscal_year_desc']);
$tbl->addCell(getDateTime($fy['fiscal_year_start_date'],"dLong"));
$tbl->addCell(getDateTime($fy['fiscal_year_end_date'],"dLong"));
$tbl->addCell($fy['fy_status']);
$tbl->addCell("<a class='pull btn btn-danger btn-xs' href ='".$_SERVER['PHP_SELF']."?route=modules/gl/setup/financial_periods/edit_fiscal_year&fisca_year_id=".$fy['fiscal_year_id']."'>Edit Fiscal Year&nbsp;&nbsp;<span class='glyphicon glyphicon-edit'></span></a>
			   ");
}
			  

?>
 <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          		Fiscal Years 
            <small>Defined Fiscal Years for Company Accounts.</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="<?php echo SITE_ROOT; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">General Ledger</a></li>
            <li class="active">List Fiscal Years</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

 <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Fiscal Years</h3>
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