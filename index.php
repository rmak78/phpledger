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
		include_once ("login_page.php");
	  
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
	$role_id = $_SESSION['role_id'] ;
	
	// $username = $_SESSION['username'];
	// $user_id =  $_SESSION['user_id'];
	// $role_id = getUserRoleID($_SESSION['user_id']);

?>

<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
 
      <a class="navbar-brand" href="#"><?php echo $_SESSION['company_name']; ?></a>
    </div>

    <!--  Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
<li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Master Files Setup<span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
			<li><a  data-toggle="modal" href="#myModal"><span class='glyphicon glyphicon-home'></span> &nbsp; Company Basics</a></li>
			
			<li class="divider"></li> 
 
<li class="dropdown-header"  >&nbsp;Chart of Accounts </li>
 
			<li><a href="<?php echo $_SERVER['PHP_SELF']."?route=maintain_coa_groups"; ?>"><span class='glyphicon glyphicon-book'></span> &nbsp;Account Groups</a></li>
		  
			<li><a href="<?php echo $_SERVER['PHP_SELF']."?route=maintain_coa"; ?>"><span class='glyphicon glyphicon-list'></span> &nbsp;Chart of Account </a></li>
			<li class="divider"></li> 
 		
 
			<li class="divider"></li> 
          </ul>
 </li>
 
 </ul>
 
<!-- Vendors Menu -->

      <ul class="nav navbar-nav">
<li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Purchase<span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
			<li><a href="<?php echo $_SERVER['PHP_SELF']."?route=purchase_order"; ?>"> &nbsp; Purchase Orders</a></li>	
			<li class="divider"></li> 
			<li><a href="<?php echo $_SERVER['PHP_SELF']."?route=product_vendors"; ?>"> &nbsp;Product by Vendors</a></li>
		  <li class="divider"></li> 
			<li><a href="<?php echo $_SERVER['PHP_SELF']."?route=vendor_invoice"; ?>"> &nbsp;INVOICE Given by Vendors </a></li>
			<li class="divider"></li> 
          </ul>
 </li>
 
 </ul>
  <!-- Clients Menu -->

      <ul class="nav navbar-nav">
<li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Sales<span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
			<li><a href="<?php echo $_SERVER['PHP_SELF']."?route=sale_order"; ?>"> &nbsp; New Sale Order</a></li>	
			<li class="divider"></li> 
			<li><a href="<?php echo $_SERVER['PHP_SELF']."?route=sale_invoice"; ?>"> &nbsp;Sale INVOICE</a></li>
		  <li class="divider"></li> 
			<li><a href="<?php echo $_SERVER['PHP_SELF']."?route=customer_payment"; ?>"> &nbsp;Customer Payment</a></li>
			<li class="divider"></li> 
          </ul>
 </li>
 
 </ul>
 <!-- Finance Menu -->

      <ul class="nav navbar-nav">
<li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Finance<span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
			<li><a href="<?php echo $_SERVER['PHP_SELF']."?route=expense_voucher_add"; ?>"> &nbsp; Add Expense Voucher</a></li>	
			<li class="divider"></li> 
			<li><a href="<?php echo $_SERVER['PHP_SELF']."?route=received_voucher"; ?>"> &nbsp;Cash Received Voucher</a></li>
		  <li class="divider"></li> 

          </ul>
 </li>
 
 </ul>
 

 
 
 
      
      <ul class="nav navbar-nav navbar-right">
  
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
	  
	  
	   <!-- CRM -->
       <ul class="nav navbar-nav navbar-right multi-level">
  
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-briefcase"></span> CRM <span class="caret"></span></a>
          <ul class="dropdown-menu " role="menu">
			<li class="dropdown-header" >Suppliers or Vendors Management</li>
					<li><a href="<?php echo $_SERVER['PHP_SELF']."?route=vendors_management"; ?>">&nbsp; Manage Vendors</a></li> 
					<li class="divider"></li>
				<li class="dropdown-header">Clients or Buyer Management</li>
					<li><a href="<?php echo $_SERVER['PHP_SELF']."?route=clients_management"; ?>">&nbsp;Manage Clients</a></li>
					<li class="divider"></li>
				<li class="dropdown-header">Other Party Members Management</li>
					<li class="dropdown-submenu"><a tabindex="-1" href="#">&nbsp;Others</a>
					 <ul class="dropdown-menu">
                        <li><a href="#">Area Management</a></li>
						<li class="divider"></li>
                    	<li><a href="#">Area Managers</a></li>
						<li class="divider"></li>
						<li><a href="#">Loading Parties</a></li>
                    </ul>
					</li>
					<li class="divider"></li> 
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>


 <script>
$(function(){
  $("#submit_button").click(function(){
	 
  $.ajax({
				type: "POST",
				url: "ajax_helpers/ajax_update_company_data.php",
				data: $('#admin_login_form').serialize(),
				success: function(data){
					$("#myModal").modal('hide');
					if(data==1){
					window.location.href="<?php echo $_SERVER['PHP_SELF']."?route=maintain_company_data"; ?>";
					}
					else{
						alert("invalid user name or password: \n");
						}
					},
				error: function(data){
					alert("Something wrong: \n"+data);					
					
					}
		});
									});
	});
</script>

     <!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" 
   aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog">
         <div class="modal-content">
          <div class="modal-header">
           
            <h3>Super Admin Name & Password</h3>
          </div>
          <div class="modal-body">
            <form method="post" name="admin_login_form" id="admin_login_form" >
            
              <p><input type="text" class="span3" name="admin_name" id="admin_name" required="required" placeholder="admin_email"></p>
              <p><input type="password" class="span3" name="admin_password" id="admin_password" required="required" placeholder="admin_password"></p>
           
               
           
            </form>
          </div>
          <div class="modal-footer">
           <button class="btn btn-default" type="button" data-dismiss="modal">Close</button>
          <button class="btn btn-primary" type="button" name="submit_button" id="submit_button">Sign in</button>
          
           
       
      </div>
       <p align="center"> Php Ladger.com </p>
        </div>
        </div> 

</div>



<?php

		if ( (( $role_id == 1 ) || ( pageAllowed($_SESSION['user_id'], $path) == 1 )) && $path<> "" ) {
			if($path <> "") {
			include ("./".$path.".php");
			} else {
			include ("default.php");
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
	include_once ("login_page.php");
}
include_once('footer_script.php');
echo "SESSION Variables:<pre>";
print_r($_SESSION);
echo "</pre>";	

echo "GET Variables:<pre>";
print_r($_GET);
echo "</pre>";
	
echo "POST Variables:<pre>";
print_r($_POST);
echo "</pre>";
?>







  
        
