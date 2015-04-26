<?php 
$config_id= "";
if(isset($_GET['config_id'])){
	$config_id=$_GET['config_id'];
}
else { echo "not ";}
?>
<!-- Content Header (Page header) -->
  <section class="content-header">
           <h1>
                Edit System Configrations
           </h1>
                <ol class="breadcrumb">
                   <li><a href ="<?php echo SITE_ROOT; ?>"><i class = "fa fa-dashboard"></i> Home</a></li>
                   <li class ="active"> Edit System Configrations </li>
                </ol>
  </section>
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
				<?php 	echo $config_id; ?>
            </div><!-- /.box-body -->
            <div class="box-footer">
             <small> Please do not make changes to these unless you are really sure what you are doing. making changes here have system wide impact</small>
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
     	 </section><!-- /.content -->