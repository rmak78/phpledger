<?php
print_r($_GET);
echo "<br/>";
print_r($_POST);
$voucher_desc = "";
$voucher_ref = "";
$voucher_date = "";
if(isset($_GET['voucher_id'])) {
	$voucher_id = $_GET['voucher_id'];
}
if($voucher_id > 0) {
	$sql = "SELECT * FROM ".DB_PREFIX.$_SESSION['co_prefix']."journal_vouchers WHERE voucher_id=".$voucher_id;
	$voucher = DB::queryFirstRow($sql);

	$voucher_ref = $voucher['voucher_ref_no'];
	$voucher_date = $voucher['voucher_date'];
	$voucher_desc = $voucher['voucher description'];
}
?>
<?php
//Draft Journal Vouchers
$tbl_draft = new HTML_Table('', 'table table-striped table-bordered');
$tbl_draft->addRow();
$tbl_draft->addCell('ID', '', 'header');
$tbl_draft->addCell('Voucher Date', '', 'header');
$tbl_draft->addCell('Account', '', 'header');
$tbl_draft->addCell('Entry Description', '', 'header');
$tbl_draft->addCell('Debit', '', 'header');
$tbl_draft->addCell('Credit', '', 'header');
$tbl_draft->addCell('Action', '', 'header');
?>

<?php
$sql = 'SELECT * FROM '.DB_PREFIX.$_SESSION['co_prefix'].'journal_voucher_details  WHERE voucher_detail_status="draft" && voucher_id='.$voucher_id.' ORDER by voucher_detail_id DESC';
$draft_jv = DB::query($sql);
foreach($draft_jv as $jv_detail) { 
$tbl_draft->addRow();
$tbl_draft->addCell($jv_detail['voucher_detail_id']);
$tbl_draft->addCell($jv_detail['voucher_date']);

$accounts_query = 'SELECT account_id, account_code, account_desc_short from '.DB_PREFIX.$_SESSION['co_prefix'].'coa WHERE account_id='.$jv_detail['account_id'];
$accounts = DB::queryFirstRow($accounts_query);
										
$tbl_draft->addCell($accounts['account_code']  ."-". $accounts['account_desc_short']);
$tbl_draft->addCell($jv_detail['entry_description']);
$tbl_draft->addCell($jv_detail['debit_amount']);
$tbl_draft->addCell($jv_detail['credit_amount']);
$tbl_draft->addCell("<a href ='".SITE_ROOT."?route=modules/gl/transactions/journal_vouchers/edit_journal_voucher_detail&voucher_detail_id=".$jv_detail['voucher_detail_id']."'><button type='button' class='btn btn-primary btn-xs' name='edit' value='Edit'>Edit&nbsp;<i class='glyphicon glyphicon-edit'></i></button></a>&nbsp;&nbsp;<a href ='".SITE_ROOT."?route=modules/gl/transactions/journal_vouchers/edit_journal_voucher_detail&voucher_detail_id=".$jv_detail['voucher_detail_id']."'><button type='submit' name='delete' value='delete' class='btn btn-danger btn-xs'>Delete&nbsp;&nbsp;<i class='glyphicon glyphicon-trash'></i></button</a>");
}
			  

?>

<!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          	Journal Voucher (J.V.)
            <small>View Journal Voucher Details</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="<?php echo SITE_ROOT; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">General Ledger</a></li>
            <li>Journal Vouchers</li>
            <li class="active">View Journal Voucher Details</li>
          </ol>
        </section>
        <!-- Main content -->
        <section >
          <!-- title row -->
          <div class="box">
             <div class="box-header with-border">
              <h3 class="box-title">View Journal Voucher Details</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
<div class="box-body">
          <div class="row info">
            <div class="col-xs-12">
              <h2 class="page-header">
                <i class="fa fa-globe"></i> &nbsp;<?php echo $_SESSION['company_name']; ?>
                <small class="pull-right"><?php echo getDateTime(0,"dLong"); ?></small>
              </h2>
            </div><!-- /.col -->
          </div>
          <!-- info row -->
          <div class="row ">
            <div class="col-sm-8  "> 
                <strong>Voucher Description :</strong>
                <BR/>
                <p><?php echo $voucher_desc; ?></p>
            </div><!-- /.col -->
			<div class="col-sm-4  ">
              <b>JV ID: </b> <?php echo $voucher_id; ?></b><br/>
              <b>Voucher Ref#:</b> <?php echo $voucher_ref; ?><br/>
              <b>Voucher Date:</b> <?php echo getDateTime($voucher_date,"dLong"); ?><br/>
              
            </div><!-- /.col -->
			 
          </div><!-- /.row -->
          
          
             </div><!-- /.box-body -->
            <div class="box-footer">
             <small> Explanation text for JV header</small>
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
     	 </section><!-- /.content -->
 
          <section >
          <!-- title row -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">View Voucher Details</h3>
              <div class="box-tools pull-right">
<a class="pull btn btn-success btn-sm" href="<?php echo SITE_ROOT."index.php?route=modules/gl/transactions/journal_vouchers/add_journal_voucher_detail&voucher_id=".$voucher_id ?>">
			Add More Details &nbsp; <span class="fa fa-chevron-circle-right"></span> </a>
                
              </div>
            </div>
<div class="box-body">
        <?php  echo $tbl_draft->display(); ?>
          
</div><!-- /.box-body -->
            <div class="box-footer">
             <small> Explanation text for JV details</small>
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
     	 </section><!-- /.content -->      