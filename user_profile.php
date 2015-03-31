<?php
if ( (isset($_SESSION['is_logged'])) AND ($_SESSION['is_logged'] == 1)) {
	$username = $_SESSION['username'];
	$role_id = getUserRoleID($_SESSION['user_id']);
}	

$sql_user = "SELECT * FROM cb_users WHERE username ='".$username."'";
$user_data = mysql_query($sql_user);
$user_info = mysql_fetch_array($user_data);
?>

<div class="container">
      <div class="row">
      <div class="col-md-5  toppad  pull-right col-md-offset-3 ">
         
       <br>
		<p class=" text-info"><?php echo date("Y-m-d h:i:sa"); ?> </p>
      </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad" >
   
   
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title"><?php echo $user_info['firstname']." ".$user_info['lastname']; ?></h3>
			  <div align="right"><a class="btn btn-primary btn-sm" href="<?php echo $_SERVER['PHP_SELF']."?route=users/edit_profile"; ?>">Edit</a>
			  <a class="btn btn-primary btn-sm" href="<?php echo $_SERVER['PHP_SELF']."?route=users/change_pwd"; ?>">Change Password</a>
			  </div>
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-md-3 col-lg-3 " align="center"> <img alt="User Pic" src="images/user_thumb.png" class="img-circle"> </div>
         
                <div class=" col-md-9 col-lg-9 "> 
                  <table class="table table-user-information">
                    <tbody>
                      <tr>
                        <td>User Id:</td>
                        <td><?php echo $user_info['user_id']; ?></td>
                      </tr>
                      <tr>
                        <td>User Name:</td>
                        <td><?php echo $user_info['username']; ?></td>
                      </tr>
                      <tr>
                        <td>First Name</td>
                        <td><?php echo $user_info['firstname']; ?></td>
                      </tr>
                   
                         <tr>
                             <tr>
                        <td>Last Name</td>
                        <td><?php echo $user_info['lastname']; ?></td>
                      </tr>                       
                      <tr>
                        <td>Email</td>
                        <td><a href="<?php echo $user_info['email']; ?>"><?php echo $user_info['email']; ?></a></td>
                      </tr>                      
                           
                      </tr>
			
                     
                    </tbody>
                  </table>
                              
                </div>
              </div>
            </div>           
            
          </div>
        </div>
      </div>
    </div>

<?php
include_once("../tools_footer.php");
?>