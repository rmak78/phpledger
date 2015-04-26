<?php
//Draft Expense Voucher
$tbl_draft = new HTML_Table('', 'table table-striped table-bordered');
$tbl_draft->addRow();
$tbl_draft->addCell('Voucher ID', '', 'header');
$tbl_draft->addCell('Voucher Refrance #', '', 'header');
$tbl_draft->addCell('Voucher Description', '', 'header');
$tbl_draft->addCell('Total Amount', '', 'header');
$tbl_draft->addCell('Voucher Approved By', '', 'header');
$tbl_draft->addCell('Voucher Status', '', 'header');
$tbl_draft->addCell('Actions', '', 'header');
?>

<?php
$sql = 'SELECT * FROM '.DB_PREFIX.$_SESSION['co_prefix'].'voucher_expense ORDER by voucher_id';
$voucher_expense = DB::query($sql);
foreach($voucher_expense as $list_voucher_expense) { 
$tbl_draft->addRow();
$tbl_draft->addCell($list_voucher_expense['voucher_id']);
$tbl_draft->addCell($list_voucher_expense['voucher_ref_no']);
$tbl_draft->addCell($list_voucher_expense['voucher description']);
$tbl_draft->addCell($list_voucher_expense['voucher_total']);
$tbl_draft->addCell($list_voucher_expense['voucher_approved_by']);
$tbl_draft->addCell($list_voucher_expense['voucher_status']);
$tbl_draft->addCell("<a class='pull btn btn-danger btn-xs' href ='#'>Edit Voucher&nbsp;&nbsp;<span class='glyphicon glyphicon-edit'></span></a> ");
}
			  

?>
<?php
//Expense Voucher Pending Approvel
$tbl_pending = new HTML_Table('', 'table table-striped table-bordered');
$tbl_pending->addRow();
$tbl_pending->addCell('Voucher ID', '', 'header');
$tbl_pending->addCell('Voucher Refrance #', '', 'header');
$tbl_pending->addCell('Voucher Description', '', 'header');
$tbl_pending->addCell('Total Amount', '', 'header');
$tbl_pending->addCell('Voucher Approved By', '', 'header');
$tbl_pending->addCell('Voucher Status', '', 'header');
$tbl_pending->addCell('Actions', '', 'header');
?>

<?php
$sql = 'SELECT * FROM '.DB_PREFIX.$_SESSION['co_prefix'].'voucher_expense ORDER by voucher_id';
$voucher_expense = DB::query($sql);
foreach($voucher_expense as $list_voucher_expense) { 
$tbl_pending->addRow();
$tbl_pending->addCell($list_voucher_expense['voucher_id']);
$tbl_pending->addCell($list_voucher_expense['voucher_ref_no']);
$tbl_pending->addCell($list_voucher_expense['voucher description']);
$tbl_pending->addCell($list_voucher_expense['voucher_total']);
$tbl_pending->addCell($list_voucher_expense['voucher_approved_by']);
$tbl_pending->addCell($list_voucher_expense['voucher_status']);
$tbl_pending->addCell("<a class='pull btn btn-danger btn-xs' href ='#'>Edit Voucher&nbsp;&nbsp;<span class='glyphicon glyphicon-edit'></span></a> ");
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
				<?php  echo $tbl_draft->display(); ?>
            </div><!-- /.box-body -->
            <div class="box-footer">
             
            </div>
          </div><!-- /.box -->
		</section> 
		<section class="content">  <!-- Expense Voucher Pending Approvel Section -->
		   <div class="box">   <!-- Expense Voucher Pending Approvel Boox -->
            <div class="box-header with-border">
              <h3 class="box-title">Expense Voucher Pending Approvel</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
				<?php  echo $tbl_pending->display(); ?>
            </div><!-- /.box-body -->
            <div class="box-footer">
             
            </div><!-- /.box-footer-->
          </div><!-- /.2nd box -->
     	 </section><!-- /.content -->