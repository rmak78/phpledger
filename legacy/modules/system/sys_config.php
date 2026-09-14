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
$tbl->addCell("<a data-toggle = 'modal' href ='#edit_system_configuration' class='pull btn btn-danger btn-xs' >Edit System Configrations &nbsp;&nbsp;<span class='glyphicon glyphicon-edit'></span></a>
			   ");
			   }
$tbl->addRow();
$tbl->addCell('');
$tbl->addCell('<input type="text" name="key" value=""/>');
$tbl->addCell('<input type="text" name="key_label" value=""/>');
$tbl->addCell('<input type="text" name="key_help_text" value=""/>');
$tbl->addCell('<input type="text" name="value" value=""/>');
$tbl->addCell("<a   class='btn btn-primary'  href ='#'>Save &nbsp;&nbsp;<span class='glyphicon glyphicons-disk-saved'></span></a>");


			  

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
		

		 
		      <!-- Modal -->
<div class="modal fade" id="edit_system_configuration" tabindex="-1" role="dialog" 
   aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog">
         <div class="modal-content">
          <div class="modal-header">
           
            <h3>Edit System Configrations</h3>
          </div>
          <div class="modal-body">
           <h1>Edit system configration</h1>
          </div>
          <div class="modal-footer">
           <button class="btn btn-default" type="button" data-dismiss="modal">Close</button>
          <button class="btn btn-primary" type="button" name="submit_button" id="submit_button">Sign in</button>
          
           
       
      </div>
       <p align="center"> Php Ladger.com </p>
        </div>
        </div> 

</div>
