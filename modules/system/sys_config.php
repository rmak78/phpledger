<?php

$tbl = new HTML_Table('', 'table table-striped table-bordered');
$tbl->addRow();
$tbl->addCell('Config ID', '', 'header');
$tbl->addCell('Key', '', 'header');
$tbl->addCell('Key Label', '', 'header');
$tbl->addCell('Key Help Text', '', 'header');
$tbl->addCell('Value', '', 'header');
$tbl->addCell('Actions', '', 'header');
?>

<?php
$sql = 'SELECT * FROM '.DB_PREFIX.'sys_config';
$get = DB::query($sql);
foreach($get as $config) { 
$tbl->addRow();
$tbl->addCell($config['config_id']);
$tbl->addCell($config['key']);
$tbl->addCell($config['key_label']);
$tbl->addCell($config['key_help_text']);
$tbl->addCell($config['value']);
$tbl->addCell("<a class='pull btn btn-danger btn-xs' href ='".$_SERVER['PHP_SELF']."?route=modules/system/edit_sys_config&config_id=".$config['config_id']."'>Edit System Configrations &nbsp;&nbsp;<span class='glyphicon glyphicon-edit'></span></a>
			   ");
			   }
$tbl->addRow();
$tbl->addCell('');
$tbl->addCell('<input type="text" name="key" value=""/>');
$tbl->addCell('<input type="text" name="key_label" value=""/>');
$tbl->addCell('<input type="text" name="key_help_text" value=""/>');
$tbl->addCell('<input type="text" name="value" value=""/>');
$tbl->addCell("<a class='btn btn-primary' href ='#'>Save &nbsp;&nbsp;<span class='glyphicon glyphicons-disk-saved'></span></a>");


			  

?>
<!-- Content Header (Page header) -->
  <section class="content-header">
           <h1>
                System Configrations
           </h1>
                <ol class="breadcrumb">
                   <li><a href ="<?php echo SITE_ROOT; ?>"><i class = "fa fa-dashboard"></i> Home</a></li>
                   <li class ="active"> System Configrations </li>
                </ol>
  </section>
  <!-- Content  -->
   <section class="content">

 <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">System Configrations</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
				<?php 	echo $tbl->display(); ?>
            </div><!-- /.box-body -->
            <div class="box-footer">
             <small> Please do not make changes to these unless you are really sure what you are doing. making changes here have system wide impact</small>
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
     	 </section><!-- /.content -->