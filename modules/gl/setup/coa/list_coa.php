<?php
 
 //Section list
$refs = array();
$list = array();

$sql = 'SELECT * FROM '.DB_PREFIX.$_SESSION['co_prefix'].'coa ORDER BY account_code';
$result = DB::query($sql);
$num_rows = DB::count();

    foreach ($result as $data) {
        $thisref = &$refs[ $data['account_id'] ];
        $thisref['sect_parent'] = $data['parent_account_id'];
		 $thisref['sect_name'] = $data['account_code']." - ".$data['account_desc_short']."&nbsp;&nbsp;<a class='pull btn btn-danger btn-xs' href ='".$_SERVER['PHP_SELF']."?route=modules/gl/setup/coa/edit_coa&coa_id=".$data['account_code']."'>Edit Coa&nbsp;&nbsp;<span class='glyphicon glyphicon-edit'></span></a>";
        $thisref['sect_id'] = $data['account_id'];
    
        if ($data['parent_account_id'] == 0) {
            $list[ $data['account_id'] ] = &$thisref;
        } else {
            $refs[ $data['parent_account_id'] ]['children'][ $data['account_id'] ] = &$thisref;
        }
    } 
 
 
 
 
  
?>
 <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          		Chart of Accounts 
            <small>List Chart of Accounts .</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="<?php echo SITE_ROOT; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">General Ledger</a></li>
            <li class="active">Chart of Accounts</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

 <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Chart of Accounts</h3> <a href="<?php echo SITE_ROOT; ?>?route=modules/gl/setup/coa/add_coa" class="btn btn-primary btn-sm" ><i class="fa fa-plus"></i>&nbsp Add New Account</a>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
			 
				 <?php
    printTree($list);
    $result1 = parseTree(null, $list);
    printTree($result1);
?>
			 
            </div><!-- /.box-body -->
            <div class="box-footer">
             <small> Please do not make changes to these unless you are really sure what you are doing. making changes here have system wide impact</small>
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
     	 </section><!-- /.content -->