<?php


if( (isset($_POST['user_name'])) AND (isset($_POST['password'])) ) {
//TODO: cleanup the input
$user_name = $_POST['user_name'];
$password = $_POST['password'];
$company_id = 1;
$superadmin = false;

$check = attempt_login_user($user_name, $password, $company_id, $superadmin);
echo $check;
if ( $check == True )
{
	
echo $_SESSION['user_id'];
}	
?>	
 <script type='text/javascript'>
 window.location='<?php echo $_SERVER['PHP_SELF']; ?>'; 
</script> 
<?php
} else {
 
?>
     <div class="container">    
        <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">                    
            <div class="panel panel-info" >
                    <div class="panel-heading">
                        <div class="panel-title">Sign In</div>
                        <div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="#">Forgot password?</a></div>
                    </div>     

                    <div style="padding-top:30px" class="panel-body" >

                        <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>
                            
                       <form id="loginform" action ='<?php echo $_SERVER['PHP_SELF']; ?>' method='post' enctype='multipart/form-data'  class="form-horizontal" role="form">
                                    
                            <div style="margin-bottom: 25px" class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                        <input id="login-user_name" type="text" class="form-control" name="user_name" value="" placeholder="user name or email">                                        
                                    </div>
									
									
                                
                            <div style="margin-bottom: 25px" class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                        <input id="login-password" type="password" class="form-control" name="password" placeholder="password">
                                    </div>
                                    
						<div style="margin-bottom: 25px" class="input-group">
								<span class="input-group-addon"><i class="glyphicon glyphicon-list"></i></span>
								<select class="form-control" name="company" required="required">
									<option></option> 
									<?php
									$companies = DB::query("SELECT company_id, company_name FROM ".DB_PREFIX."companies WHERE company_status = 'active'");
									foreach ($companies as $company)
									{
										echo "<option value='".$company['company_id']."'>".$company['company_name']."</option>";
									}
									
									
									?>
								</select>
							</div>
                                
                            <div class="input-group">
                                      <div class="checkbox">
                                        <label>
                                          <input id="login-remember" type="checkbox" name="remember" value="1"> Remember me
                                        </label>
                                      </div>
                                    </div>
							

                                <div style="margin-top:10px" class="form-group">
                                    <!-- Button -->

                                    <div class="col-sm-12 controls">
                                      <input id="btn-login" type="submit" class="btn btn-success" value="Login" />
                                      

                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="col-md-12 control">
                                        <div style="border-top: 1px solid#888; padding-top:15px; font-size:85%" >
                                        Branding Text for here
                                        </div>
                                    </div>
                                </div>    
                            </form>     



                        </div>                     
                    </div>  
        </div>

    </div>

 
 
<?php
 }



?>