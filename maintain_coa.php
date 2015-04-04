<?php

// Maintain Chart of Accounts
/*
			[account_id] => 1
            [account_code] => 1200001
            [account_group] => 1
            [account_desc_short] => Cash
            [account_desc_long] => Cash in Hand
            [parent_account_id] => 0
            [last_modified_by] => mansoor
            [last_modified_on] => 2015-03-23 23:10:56
            [created_by] => mansoor
            [created_on] => 
            [account_status] => active


echo "<pre>";
$coa_query = 'SELECT * FROM '.DB_PREFIX.$_SESSION['co_prefix'].'coa';
$coa = DB::query($coa_query);
print_r($coa);
 
echo "</pre>";
echo $coa_query;
*/
?>

<div class="panel panel-info">
  <!-- Default panel contents -->
  <div class="panel-heading"><h3>Maintain Chart of Accounts<a href="<?php echo $_SERVER['PHP_SELF']; ?>?route=coa/add_coa" class=" pull-right btn btn-sm btn-primary"> <span class="glyphicon glyphicon-plus"></span> &nbsp;Add New COA</a> </h3> </div>
  <div class="panel-body">
<?php
$tbl = new HTML_Table('', 'table table-striped table-bordered');
$tbl->addRow();
$tbl->addCell('Account Code', '', 'header');
$tbl->addCell('Account Group', '', 'header');
$tbl->addCell('Account Desc Short', '', 'header');
$tbl->addCell('Account Desc Long', '', 'header');
$tbl->addCell('Parent Account ID', '', 'header');
$tbl->addCell('Last Modified By', '', 'header');
$tbl->addCell('Last Modified On', '', 'header');
$tbl->addCell('Account Status', '', 'header');
$tbl->addCell('Actions', '', 'header');
?>

<?php
$sql = 'SELECT * FROM '.DB_PREFIX.$_SESSION['co_prefix'].'coa';
$get_coa = DB::query($sql);
foreach($get_coa as $coa) { 
$tbl->addRow();
$tbl->addCell($coa['account_code']);
$tbl->addCell($coa['account_group']);
$tbl->addCell($coa['account_desc_short']);
$tbl->addCell($coa['account_desc_long']);
$tbl->addCell($coa['parent_account_id']);
$tbl->addCell($coa['last_modified_by']);
$tbl->addCell($coa['last_modified_on']);
$tbl->addCell($coa['account_status']);
$tbl->addCell("<a class='btn btn-primary btn-sm' href ='".$_SERVER['PHP_SELF']."?route=coa/edit_coa&group_id=".$coa['account_group']."&coa_id=".$coa['account_id']."'>Edit&nbsp;<span class='glyphicon glyphicon-new-window'></span></a>
			   ");
}
			   echo $tbl->display();
?>
 </div>
</div>
<?php
include_once("tools_footer.php");
?>