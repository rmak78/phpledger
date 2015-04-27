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


?><!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          	Journal Voucher (J.V.)
            <small>Create Journal Voucher (Draft)</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="<?php echo SITE_ROOT; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">General Ledger</a></li>
            <li>Journal Vouchers</li>
            <li class="active">Add New Voucher</li>
          </ol>
        </section>
        <!-- Main content -->
        <section >
          <!-- title row -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Editing Voucher Ref # <?php echo $voucher_ref; ?></h3>
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
                <p><?echo $voucher_desc; ?></p>
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
              <h3 class="box-title">Voucher Details</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
<div class="box-body">
   
     
          
          
             </div><!-- /.box-body -->
            <div class="box-footer">
             <small> Explanation text for JV details</small>
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
     	 </section><!-- /.content -->      