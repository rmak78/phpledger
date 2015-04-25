<?php

$tbl = new HTML_Table('', 'table table-striped table-bordered');
$tbl->addRow();
$tbl->addCell('Voucher ID', '', 'header');
$tbl->addCell('Voucher Refrance #', '', 'header');
$tbl->addCell('Voucher Description', '', 'header');
$tbl->addCell('Total Amount', '', 'header');
$tbl->addCell('Voucher Approved By', '', 'header');
$tbl->addCell('Voucher Status', '', 'header');
$tbl->addCell('Actions', '', 'header');
?>

<?php
$sql = 'SELECT * FROM '.DB_PREFIX.$_SESSION['co_prefix'].'voucher_expense ORDER by voucher_id';
$voucher_expense = DB::query($sql);
foreach($voucher_expense as $list_voucher_expense) { 
$tbl->addRow();
$tbl->addCell($list_voucher_expense['voucher_id']);
$tbl->addCell($list_voucher_expense['voucher_ref_no']);
$tbl->addCell($list_voucher_expense['voucher description']);
$tbl->addCell($list_voucher_expense['voucher_total']);
$tbl->addCell($list_voucher_expense['voucher_approved_by']);
$tbl->addCell($list_voucher_expense['voucher_status']);
$tbl->addCell("<a class='pull btn btn-danger btn-xs' href ='#'>Edit Voucher&nbsp;&nbsp;<span class='glyphicon glyphicon-edit'></span></a> ");
}
			  

?>
 <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          		Expense Voucher 
            <small>List of Draft Expense Voucher .</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="<?php echo SITE_ROOT; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">General Ledger</a></li>
            <li class="active">List OF Expense Voucher</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

 <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Draft Expense Voucher</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
				<?php  echo $tbl->display(); ?>
            </div><!-- /.box-body -->
           
          </div><!-- /.box -->
		</section> 
		<section class="content">  
		   <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Expense Voucher Pending Approvel</h3>
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