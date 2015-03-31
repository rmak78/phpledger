<?php
if ( (isset($_SESSION['is_logged'])) AND ($_SESSION['is_logged'] == 1)) {
	$username = $_SESSION['username'];
	$role_id = getUserRoleID($_SESSION['user_id']);
}

if(isset($_GET['user_id'])){

	$user_id = $_GET['user_id'];
	
} else {

	$user_id = $_SESSION['user_id'];
	
}

$message= "";

if( isset($_POST['update']) ) {

		$password = $_POST['password'];
		$confirm_pwd = $_POST['confirm_pwd'];

	if( $password != $confirm_pwd ) {
	
		$message = "Password do not match";
			
	} else {

	DB::UPDATE('cb_users',array(			
			'password' => $password ), "user_id =%s", $user_id );
			$message = "Successfully Updated";

			}
}
?>

<div class="container">
      <div class="row">
      <div class="col-md-5  toppad  pull-right col-md-offset-3 ">
         
       <br>
<p class=" text-info"><?php echo date("Y-m-d h:i:sa"); ?> </p>
      </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad" >
   
   
          <div class="panel panel-info">
		  <form method="post" action="<?php $_SERVER['PHP_SELF']; ?>">
            <div class="panel-heading">
              <h3 class="panel-title">Password Change</h3>
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-md-3 col-lg-3 " align="center"> <img alt="User Pic" src="images/user_thumb.png" class="img-circle"> </div>
         
                <div class=" col-md-9 col-lg-9 "> 
                  <table class="table table-user-information">
                    <tbody>                      
					  <tr>
                        <td>Password:</td>
                        <td><input type="password" value = "" name="password"></td>
                      </tr>
                      <tr>
                        <td>Confirm Password</td>
                        <td><input type="password" value = "" name="confirm_pwd"></td>
                      </tr>                                           					  
					  <tr>
					  <td></td>
					  <td><input type="submit" class='btn btn-primary btn-sm' name="update" value="Update">
					  <font color="red"><?php echo $message;?> </font>
					  </td>
					  </tr>
                     
                     </tbody>
                   </table>
                              
                </div>
               </div>
             </div>               
            </form>
          </div>
        </div>
      </div>
    </div>

<?php
include_once("../tools_footer.php");
?>