<?php
if ( (isset($_SESSION['is_logged'])) AND ($_SESSION['is_logged'] == 1)) {
	$username = $_SESSION['username'];
	$role_id = getUserRoleID($_SESSION['user_id']);
}



	$message= "";
if(isset($_POST['add'])){

$user_name = $_POST['user_name'];
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$email = $_POST['email'];
$password = $_POST['password'];
$user_role_id = $_POST['user_role_id'];

//check if username already exists

/* Insert user data  */	
DB::Insert('cb_users',array(
			'username' => $user_name,
			'firstname' => $firstname,
			'lastname' => $lastname,
			'email' => $email,
			'password' => $password,
			'role_id' => $user_role_id)  );
$message = "Successfully Added User";
 
echo '<script type="text/javascript">
<!--
window.location = "'.$_SERVER['PHP_SELF'].'?route=user_management"
//-->
</script>';

} else {

/* Retrive logged in user data */
	$user_name = "";
	$firstname = "";
	$lastname = "";
	$email = "";
	$password = "";
	$user_role_id = "";
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
              <h3 class="panel-title">Add New User</h3>
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-md-3 col-lg-3 " align="center"> <img alt="User Pic" src="images/user_thumb.png" class="img-circle"> </div>
         
                <div class=" col-md-9 col-lg-9 "> 
                  <table class="table table-user-information">
                    <tbody>
 	  
                      <tr>
                        <td>User Name:</td>
                        <td><input type="text" required value = "<?php echo $user_name; ?>" name="user_name"></td>
                      </tr>
					  <tr>
                        <td>Password:</td>
                        <td><input type="password" required value = "<?php echo $password; ?>" name="password"></td>
                      </tr> 
                      <tr>
                        <td>First Name</td>
                        <td><input type="text" required value="<?php echo $firstname; ?>" name="firstname"></td>
                      </tr>
                   
                         <tr>
                             <tr>
                        <td>Last Name</td>
                        <td><input type="text" required value = "<?php echo $lastname; ?>" name="lastname"></td>
                      </tr>                       
                      <tr>
                        <td>Email</td>
                        <td><input type="email" required value = "<?php echo $email; ?>" name="email"></td>
                      </tr>   
					  <tr>
                        <td>User Role</td>
                        <td><input type="number" required value = "<?php echo $user_role_id; ?>" name="user_role_id"></td>
                      </tr>                      
                           
                      </tr>
					  <tr>
					  <td></td>
					  <td><input type="submit" class='btn btn-primary btn-sm' name="add" value="Add New User">
					  <font color="red"><?php echo $message;?> </font>
					  </td>
					  </tr>
                     
                    </tbody>
                  </table>
                              
                </div>
              </div>
            </div>
                <!--  <div class="panel-footer">
                        <a data-original-title="Broadcast Message" data-toggle="tooltip" type="button" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-envelope"></i></a>
                        <span class="pull-right">
                            <a href="edit.html" data-original-title="Edit this user" data-toggle="tooltip" type="button" class="btn btn-sm btn-warning"><i class="glyphicon glyphicon-edit"></i></a>
                            <a data-original-title="Remove this user" data-toggle="tooltip" type="button" class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-remove"></i></a>
                        </span>
                    </div> -->
            </form>
          </div>
        </div>
      </div>
    </div>

<?php
include_once("../tools_footer.php");
?>