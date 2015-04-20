<?php

$tbl = new HTML_Table('', 'table table-striped table-bordered');
$tbl->addRow();
$tbl->addCell('Group Code', '', 'header');
$tbl->addCell('Group Description', '', 'header');
$tbl->addCell('Type', '', 'header');
$tbl->addCell('Side', '', 'header');
$tbl->addCell('Status', '', 'header');
$tbl->addCell('Actions', '', 'header');
?>

<?php
$sql = 'SELECT * FROM '.DB_PREFIX.$_SESSION['co_prefix'].'coa_groups ORDER by group_code';
$get_coa = DB::query($sql);
foreach($get_coa as $coa) { 
$tbl->addRow();
$tbl->addCell($coa['group_code']."<a class='btn  pull-right btn-info btn-xs' href ='".$_SERVER['PHP_SELF']."?route=modules/gl/setup/coa/list_coa&view=bygroupid&group_id=".$coa['group_id']."'>Sub Accounts&nbsp;<span class='fa  fa-search-plus'></span></a>");
$tbl->addCell($coa['group_description']);
$tbl->addCell(get_coa_group_type($coa['group_id']));
$tbl->addCell(get_coa_group_side($coa['group_id']));
$tbl->addCell($coa['group_status']);
$tbl->addCell("<a class='pull btn btn-danger btn-xs' href ='".$_SERVER['PHP_SELF']."?route=modules/gl/setup/coa_groups/edit_coa_group&group_id=".$coa['group_id']."'>Edit Group&nbsp;&nbsp;<span class='glyphicon glyphicon-edit'></span></a>
			   ");
}
			  

?>
 <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          		Chart of Account Groups 
            <small>list of major chart of account groups .</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="<?php echo SITE_ROOT; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">General Ledger</a></li>
            <li class="active">List Account Groups</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

 <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Level 1 Accounts</h3>
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