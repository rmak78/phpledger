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
  <div class="panel-heading"><h3>Chart of Accounts<a href="<?php echo $_SERVER['PHP_SELF']; ?>?route=coa/add_coa" class=" pull-right btn btn-sm btn-primary"> <span class="glyphicon glyphicon-plus"></span> &nbsp;Add New Account</a> </h3> </div>
  <div class="panel-body">
<?php

function showMenu($level = 0) {

$result = mysql_query("SELECT * FROM ".DB_PREFIX.$_SESSION['co_prefix']."coa WHERE `parent_account_id` = ".$level); 
echo "<ul>";
    while ($node = mysql_fetch_array($result)) { 
        echo "<li>".$node['account_code']." - ".$node['account_desc_short'];
        $hasChild = mysql_fetch_array(mysql_query("SELECT * FROM ".DB_PREFIX.$_SESSION['co_prefix']."coa WHERE `parent_account_id` = ".$node['parent_account_id']));
        IF ($hasChild) {
            showMenu($node['parent_account_id']);
        }
        echo "</li>";
    }
echo "</ul>";
}

showMenu();

?>  
  
  
  
<ul>
<?php
$sql = 'SELECT * FROM '.DB_PREFIX.$_SESSION['co_prefix'].'coa ORDER BY account_code' ;
$get_coa = DB::query($sql);
foreach($get_coa as $coa) { 
?>
<li>
<?php

echo "<a class=' ' title='".$coa['account_desc_long']."' href ='".$_SERVER['PHP_SELF']."?route=coa/edit_coa&group_id=".$coa['account_group']."&coa_id=".$coa['account_id']."'>".$coa['account_code']." - ".$coa['account_desc_short']." </a>";

?>
</li>
<?php
}
			  
?>
</ul>
 </div>
</div>
<?php
include_once("tools_footer.php");

echo "<pre>";
$coa_query = 'SELECT * FROM '.DB_PREFIX.$_SESSION['co_prefix'].'coa';
$coa = DB::query($coa_query);
print_r($coa);
 
echo "</pre>";
echo $coa_query;
?>
