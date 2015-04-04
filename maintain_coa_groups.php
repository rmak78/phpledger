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
  <div class="panel-heading"><h3>Maintain Chart of Accounts Groups<a href="<?php echo $_SERVER['PHP_SELF']; ?>?route=coa_groups/add_coa_groups" class=" pull-right btn btn-sm btn-primary"> <span class="glyphicon glyphicon-plus"></span> &nbsp;Add New COA</a> </h3> </div>
  <div class="panel-body">
<?php
$tbl = new HTML_Table('', 'table table-striped table-bordered');
$tbl->addRow();
$tbl->addCell('Group Code', '', 'header');
$tbl->addCell('Group Description', '', 'header');
$tbl->addCell('From Account Code', '', 'header');
$tbl->addCell('To Account Code', '', 'header');

$tbl->addCell('Type', '', 'header');

$tbl->addCell('Side', '', 'header');
$tbl->addCell('Statistic Only', '', 'header');


$tbl->addCell('Status', '', 'header');
$tbl->addCell('Actions', '', 'header');
?>

<?php
$sql = 'SELECT * FROM '.DB_PREFIX.$_SESSION['co_prefix'].'coa_groups';
$get_coa = DB::query($sql);
foreach($get_coa as $coa) { 
$tbl->addRow();
$tbl->addCell($coa['group_code']);
$tbl->addCell($coa['group_description']);
$tbl->addCell($coa['from_account_code']);
$tbl->addCell($coa['to_account_code']);
$tbl->addCell($coa['pls_group']);
$tbl->addCell($coa['pls_side']);
$tbl->addCell($coa['statistics_only']);
$tbl->addCell($coa['group_status']);
$tbl->addCell("<a class='btn btn-primary btn-sm' href ='".$_SERVER['PHP_SELF']."?route=coa_groups/edit_coa_groups&group_id=".$coa['group_id']."'>Edit&nbsp;<span class='glyphicon glyphicon-new-window'></span></a>
			   ");
}
			   echo $tbl->display();
?>
 </div>
</div>
<?php
include_once("tools_footer.php");
?>