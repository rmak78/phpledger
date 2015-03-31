<div class="panel panel-info">
  <!-- Default panel contents -->
  <div class="panel-heading"><h3>Users<a href="<?php echo $_SERVER['PHP_SELF']; ?>?route=users/add_user" class=" pull-right btn btn-sm btn-primary"> <span class="glyphicon glyphicon-plus"></span> &nbsp;Add New User</a> </h3> </div>
  <div class="panel-body">
<?php
$tbl = new HTML_Table('', 'table table-striped table-bordered');
$tbl->addRow();
$tbl->addCell('User Name', '', 'header');
$tbl->addCell('First Name', '', 'header');
$tbl->addCell('Last Name', '', 'header');
$tbl->addCell('Email', '', 'header');
$tbl->addCell('Role ID', '', 'header');
$tbl->addCell('Actions', '', 'header');
?>

<?php
$sql = "SELECT * FROM cb_users";
$get_users = DB::query($sql);
foreach($get_users as $user) { 
$tbl->addRow();
$tbl->addCell($user['username']);
$tbl->addCell($user['firstname']);
$tbl->addCell($user['lastname']);
$tbl->addCell($user['email']);
$tbl->addCell($user['role_id']);
$tbl->addCell("<a class='btn btn-primary btn-sm' href ='".$_SERVER['PHP_SELF']."?route=users/edit_profile&user_id=".$user['user_id']."'>Edit&nbsp;<span class='glyphicon glyphicon-new-window'></span></a>
			   ");
}
			   echo $tbl->display();
?>
 </div>
</div>
<?php
include_once("../tools_footer.php");
?>