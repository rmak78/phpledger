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

<!--Top Main Menu -->
<?php include('top_nav_bar.php'); ?>


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







  
        
