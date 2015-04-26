<?php
$message=''; 

$_SESSION['company_id'] = 1;
$_SESSION['co_prefix'] = 'test_'; 

// Maintain a company
/*
   To Do  KA
   1- Name of cities for selected country
   2-List of Industries 
   3-List of different Time zones
   4- List of all currencies
   5-To upload the logo "choose the file" option
   6- chart of account code length
   7- Authentication for changing the password for company admin
   8- Country code for selected country 
   9- 
   
   
   
 
*/ 
$company = DB::queryFirstRow('SELECT * FROM '.DB_PREFIX.'companies WHERE company_id = '.$_SESSION['company_id'])
?>
          <!-- Content Header (Page header) -->
  <section class="content-header">
           <h1>
                Company Profile & Configrations
           </h1>
                <ol class="breadcrumb">
                   <li><a href ="<?php echo SITE_ROOT; ?>"><i class = "fa fa-dashboard"></i> Home</a></li>
                   <li class ="active"> Company Profile & Configrations </li>
                </ol>
  </section>
         <!-- Main content -->
  
  <section class = "content">
     
        <div class = "nav-tabs-custom">
            
			<ul class = "nav nav-tabs" id="myTab">
               
			   <li role = "presentation" class = "active"><a href="#home" data-toggle="tab"> Company Information</a></li>
               <li role = "presentation"><a href ="#profile" data-toggle="tab"> DB Prefix</a></li>
               <li role = "presentation"><a href ="#logo" data-toggle="tab"> Company Logo</a></li>
               <li role = "presentation"><a href ="#settings" data-toggle="tab"> Genral Information</a></li>
             
			</ul>
         
		<div class ="tab-content">
 
  <div role="tabpanel" class="tab-pane active" id="home">
 
 
		<table  class="table table-bordered table-striped" >
                <tbody> 
                    <tr>         
                        <td width="35%">Company Name</td>
                        <td width="65%"><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="company_name" data-name="company_name" data-type="text" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Company Name"><?php echo $company['company_name'] ;?></a></td>
                    </tr>
                    
					<tr>         
                        <td width="35%">Address</td>
                        <td width="65%"><p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" 
                        
						id="company_address_1" data-name="company_address_1" data-type="text" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Address Line 1"><?php echo $company['company_address_1'] ;?></a></p>
						<p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="company_address_2" data-name="company_address_2" data-type="text" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Address Line 2"><?php echo $company['company_address_2'] ;?></a></p>
						<p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="company_city" 
                        data-name="city" data-type="text" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit City"><?php echo $company['city'] ;?></a>
                       
                         &nbsp;, &nbsp; <a href="#" class="editable-country" data-url="ajax_helpers/ajax_update_company_data.php" id="company_country" data-name="country" data-type="select" data-pk="<?php echo $company['company_id'] ;?>"   data-title="Edit Country">
						 <?php echo $company['country'] ;?></a></p>
						
						</td>
                    </tr>
                    
					<tr>  
						      
                            <td width ="35%">Phone No</td>
                            <td width ="65%">
						<p>
						  <a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" 
                        id="phone_1" data-name="phone_1" data-type="tel" data-pk="<?php echo $company['company_id'] ;?>" 
                        data-title="Edit Phone No 1">
						<?php echo $company['phone_1'] ;?></a>
						</p>
						
						<p>
						<a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="phone_2" data-name="phone_2" data-type="tel" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Phone 2"><?php echo $company['phone_2'] ;?></a>
						</p>
						
						</td>
                    </tr>
					<tr>         
                            <td width="35%">Email</td>
                            <td width="65%">
						  <p>
						   <a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="email" data-name="email" data-type="email" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit email"><?php echo $company['email'] ;?></a>
						  </p>		
						  </td>
                    </tr>
					
					<tr>         
                        <td width="35%">Website</td>
                        <td width="65%">
						  <p>
						   <a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="website" data-name="website" data-type="url" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Website"><?php echo $company['website'] ;?></a>
						  </p>		
						</td>
                    </tr>
				</tbody>
			</table>
     
 
    </div> 
         <!-- close first tab contant -->
                                                    <!------------------------------>
 <div role="tabpanel" class="tab-pane" id="profile">					
 
 
		<table  class="table table-bordered table-striped" >
                <tbody> 
 					<tr>         
                        <td width="35%">Industry</td>
                        <td width="65%">
					<p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="industry" data-name="industry" data-type="select" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Industry"><?php echo $company['industry'] ;?></a>
						
						</p>		
						</td>
                    </tr>
 					
 					<tr>         
                        <td width="35%">Database Tables Prefix</td>
                        <td width="65%"><?php echo DB_PREFIX.$company['company_db_prefix'] ;?>
					 	
						</td>
                    </tr>

				</tbody>
			</table>
     
 
</div> <!-- close 2nd tab contant -->
                                                    <!-------------------------------->
 
 <div role="tabpanel" class="tab-pane" id="logo">
 
 
		<table  class="table table-bordered table-striped" >
                <tbody> 
  					<tr>         
                        <td width="35%">Logo URL(large 400px X 100px))</td>
                        <td width="65%"><img src="<?php echo $company['company_logo_home'] ;?>" alt="Large Logo" border="0"  width="400" height="100" /><p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="company_logo_home" data-name="company_logo_home" data-type="url" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Large Logo URL"><?php echo $company['company_logo_home'] ;?></a> </p>
            
             <form method="post" action="<?php echo $_SERVER['PHP_SELF']."?route=maintain_company_data"; ?>" name="file_upload_form" id="file_upload_form" role="form"  enctype="multipart/form-data">
                        <table class="table">
                         <tbody>
                         <tr> 
            <td>     <input type="file" name="pic_upload" id="pic_upload" /> </td>
             <td>    <input type="submit" value="Upload" id="submit" name="logo_home" class="btn btn-primary"  /> </td>
             <td>			 <?php echo $message;  ?> </td>
             			</tr>
        </form>			
						</tbody>
						</table>
						</td>
       			 </tr>
 					<tr>         
                        <td width="35%">Logo URL(medium 250px X 150px) </td>
                        <td width="65%"><img src="<?php echo $company['company_logo_head'] ;?>" alt="Medium Logo" border="0"  width="250" height="150" /><p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="company_logo_head" data-name="company_logo_head" data-type="url" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Medium Logo URL"><?php echo $company['company_logo_head'] ;?></a></p>
                       
                       <form method="post" action="<?php echo $_SERVER['PHP_SELF']."?route=maintain_company_data"; ?>" 
                       name="file_upload_form" id="file_upload_form" role="form"  enctype="multipart/form-data">
                       
                         <table class="table">
                         <tbody>
				<tr>
        <td> <input type="file" name="pic_upload" id="pic_upload" /> </td>
        <td> <input type="submit" value="Upload" id="submit" name="logo_head" class="btn btn-primary"  /> </td>
        <td> <?php echo $message;  ?></td>
				</tr>
						</tbody>
						</table>
                           </form>
                        </td>
                    </tr>
 					<tr>         
                        <td width="35%">Logo URL (small 100px x 100px)</td>
                        <td width="65%"><img   src="<?php echo $company['company_logo_icon'] ;?>" alt="Small Logo" border="0"  width="100" height="100" />
					<p>
						<a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="company_logo_icon" data-name="company_logo_icon" data-type="url" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Small Logo URL"><?php echo $company['company_logo_icon'] ;?></a>
                          <form method="post" action="<?php echo $_SERVER['PHP_SELF']."?route=maintain_company_data"; ?>" name="file_upload_form" id="file_upload_form" role="form" 
                        enctype="multipart/form-data">
                       
                <table class="table">
                    <tbody>
                         
					<tr>  
                        <td><input type="file" name="pic_upload" id="pic_upload" /> </td>
                        <td><input type="submit" value="Upload" id="submit" name="logo_icon" class="btn btn-primary"  /></td> 
                        <td><?php echo $message;  ?></td>
       			    </tr>
                    
					</tbody>
              
			  </table>
       
                          </form>
						
					</p>				
						</td>
                    </tr>
				</tbody>
			</table>
     
 
</div>  <!-- close 3rd tab contant -->
                                                      <!--------------------------->
													  
 <div role="tabpanel" class="tab-pane" id="settings">				
 
 
		<table  class="table table-bordered table-striped" >
                <tbody> 
					<tr>         
                        <td width="35%">Username For Company Admin</td>
                        <td width="65%">
					
				          <p>
					       <a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="super_admin_user" data-name="super_admin_user" data-type="text" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Admin Username"><?php echo $company['super_admin_user'] ;?></a>
						
				          </p>		
						
						</td>
                    </tr>
 					
					<tr>         
                        <td width="35%">Password for Company Admin</td>
                        <td width="65%">
						  
						  <p>
						   <a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="super_admin_password" data-name="super_admin_password" data-type="password" data-pk="<?php echo $company['company_id'] ;?>" data-title="Change Admin Password">********</a>
						
						  </p>		
						</td>
                    </tr>
				</tbody>
		</table>
     
 	


 
 
		<table  class="table table-bordered table-striped" >
                <tbody> 
					<tr>         
                          <td width="35%">Time Zone</td>
                          <td width="65%">
						
						<p><a href="#" class="editable bfh-timezones" data-url="ajax_helpers/ajax_update_company_data.php"
                         id="company_time_zone" data-name="company_time_zone" data-type="select" data-pk="<?php echo $company['company_id'] ?>" data-title="Edit Time Zone">
						 <?php echo $company['company_time_zone'] ;?></a>
						 </p>		
						
						  </td>
                    </tr>
 					
					<tr>         
                          <td width="35%">Currency</td>
                          <td width="65%">
						
						<p><a href="#" class="" data-url="ajax_helpers/ajax_update_company_data.php" id="currency" data-name="currency" data-type="select" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Currency"><?php echo $company['currency'] ;?></a></p>		
						 
						 </td>
                    </tr>
 					
					<tr>         
                        <td width="35%">Chart of Account</td>
                       
					   <!-- <td width="65%"><?php //echo $company['coa_code_length'] ;?> digits -->
						
					 	<td width="65%">
						<?php 
						$accounts = DB::queryFirstField("SELECT COUNT(*) FROM ".DB_PREFIX.$_SESSION['co_prefix']."coa");
						
						
						
						if($accounts > 0){ ?><p>							 	
						Chart of Account can't be edited as some accounts have already been defined. </p>
						<?php }else{ ?>
						<a data-toggle="modal" data-target="#coaLevelsModal">Modify Chart of Account Definitions</a>
						
						<?php } ?>
						<p>You have defined <strong>
						
						<?php echo $company['coa_levels']; ?> </strong> levels</p>
						
						   
                            <?php
							$code_sample = '';
							$i = 1 ;
							
								while ($i <> ($company['coa_levels'] + 1) ) {
									$code_sample = " ";
									$level=$company['coa_level_'.$i.'_length'];
											while ($level <> 0) {
												$code_sample .= "X";
												
												$level--;												
												}
									 echo "Level ".$i." : ".$code_sample."<br>";																										
									 $i++;
									} ?>
							Sample Code = <?php  
													$code_sample = '';
													$i = 1 ;														
													while ($i <> ($company['coa_levels'] + 1) ) {
													$code_sample = " ";
													$level=$company['coa_level_'.$i.'_length'];
													while ($level <> 0) {
															$code_sample .= "X";
															$level--;												
															}
													echo $code_sample;																										
													$i++;
												}
												?>
						</td>
                    </tr>
				</tbody>
			</table>
     
 
 
 
		<table  class="table table-bordered table-striped" >
                <tbody> 
					<tr>         
                        <td width="35%">Last Change: 
						<abbr class="timeago" title="<?php echo  $company['last_modified_on'] ;?>">
						</abbr></td>
                        <td width="65%"><p><strong>Last modified</strong> by User :<b>[ <?php echo $company['last_modified_by'] ;?> ]</b> at <b>[ <?php echo getDateTime($company['last_modified_on'],'dtLong');?> ]</b>. Record was <b>first created</b> by User : <b>[  <?php echo $company['created_by'] ;?> ]</b> at <b>[ <?php echo   getDateTime($company['created_on'] , 'dtShort')  ;?> ]</b>. Company <b>Status</b> is<b> [ <?php echo $company['company_status'] ;?> ] </b>. Click here to view history of changes to company ID: <strong><?php echo $company['company_id'] ;?></strong> .
						</p>		
						</td>
                    </tr>
				</tbody>
			</table>
            
     
 
		</div>  <!-- close 4th tab contant -->
    </div>


        </section><!-- /.content -->

 

