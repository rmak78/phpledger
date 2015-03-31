<?php
session_start();

include_once ('tools_header.php');
require_once ('functions.php');
require_once('calendar/classes/tc_calendar.php');
require_once('PHPMailer-master/class.phpmailer.php');
include_once ('header_script.php');

if (isset($_GET['logout'])){
	if($_GET['logout'] == 1) {
		unset($_SESSION['is_logged']);
		unset($_SESSION['username']);
		include_once ("./login_page.php");
	  
	}
}


$path ="";
if (isset($_GET['route'])) {
$path = $_GET['route'];
} else {
	if (isset($_POST['route'])) {
	$path = $_POST['route'];
	}
}


if ( (isset($_SESSION['is_logged'])) AND ($_SESSION['is_logged'] == 1)) {
	 print_r($_SESSION);
	$role_id = $_SESSION['role_id'] ;
	// $username = $_SESSION['username'];
	// $user_id =  $_SESSION['user_id'];
	// $role_id = getUserRoleID($_SESSION['user_id']);
?>

<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
 
      <a class="navbar-brand" href="#">Sutlej.NET Accouting System</a>
    </div>

    <!--  Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
<li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Master Files Setup<span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
			<li><a href="<?php echo $_SERVER['PHP_SELF']."?route=maintain_company_data"; ?>"><span class='glyphicon glyphicon-home'></span> &nbsp; Company Basics</a></li>
			
			<li class="divider"></li> 
 
<li class="dropdown-header"  >&nbsp;Chart of Accounts </li>
 
			<li><a href="<?php echo $_SERVER['PHP_SELF']."?route=maintain_coa_groups"; ?>"><span class='glyphicon glyphicon-book'></span> &nbsp;Account Groups</a></li>
		  
			<li><a href="<?php echo $_SERVER['PHP_SELF']."?route=maintain_coa"; ?>"><span class='glyphicon glyphicon-list'></span> &nbsp;Chart of Account </a></li>
			<li class="divider"></li> 
 		
 
			<li class="divider"></li> 
          </ul>
 </li>
 
 </ul>
 

  
      
      <ul class="nav navbar-nav navbar-right">
    <li><a href="#">Link</a></li>  
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> My Account  <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
         <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>  
  		        <li class="divider"></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']."?route=user_profile"; ?>"><span class='glyphicon glyphicon-user'></span> User Profile</a></li> 
			<li class="divider"></li>  
			<?php
			if($role_id < 2) {
?>			
					<li><a href="<?php echo $_SERVER['PHP_SELF']."?route=user_management"; ?>"><span class='glyphicon glyphicon-user'></span>&nbsp; Manage Users</a></li> 
					<li class="divider"></li>
					<li><a href="<?php echo $_SERVER['PHP_SELF']."?route=system_config"; ?>"><span class='glyphicon glyphicon-cog'></span> &nbsp;System Configuration</a></li>
					<li class="divider"></li> 					
<?php }?>			
             
            <li><a href="<?php echo $_SERVER['PHP_SELF'].'?logout=1'; ?>" ><span class="glyphicon glyphicon-off"></span> Logout</a></li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<?php

		if ( (( $role_id == 1 ) || ( pageAllowed($_SESSION['user_id'], $path) == 1 )) && $path<> "" ) {
			if($path <> "") {
			include ("./".$path.".php");
			} else {
			include ("./default.php");
			}
		}
		elseif($path=="")
		{
			echo "WELCOME TO Sutlej Solutions Accounting System";
		}
		else {
			echo "PERMISSION DENIED";
		}	
} else { //if not logged in
	include_once ("./login_page.php");
}
include_once('./footer_script.php');
?>