<?php

// Maintain a company
/*

    [company_id] => 1
    [company_name] => Test Company Name
    [company_db_prefix] => test_
    [company_address_1] => Al-Sadeeq Akbar,
    [company_address_2] => Bahawalpur Road,
    [city] => Lodhran
    [country] => Pakistan
    [currency] => 
    [phone_1] => 
    [phone_2] => 
    [email] => mansoor@sutlej.net
    [website] => http://sutlej.net
    [industry] => IT
    [company_time_zone] => GMT
    [coa_code_length] => 10
    [company_logo_home] => 
    [company_logo_head] => 
    [company_logo_icon] => 
    [super_admin_user] => demo
    [super_admin_password] => demo
    [last_modified_by] => system
    [last_modified_on] => 2015-03-10 19:12:51
    [created_by] => system
    [created_on] => 2015-03-23 22:41:44
    [company_status] => active
 
*/ 
$company = DB::queryFirstRow('SELECT * FROM '.DB_PREFIX.'companies WHERE company_id = '.$_SESSION['company_id'])
?>
<div class="container">

<div class="row">
<div class="page-header">
        <h2>Company Profile & Configrations</h2>
</div>
</div>
<div class="row">
 
		<table  class="table table-bordered table-striped" >
                <tbody> 
                    <tr>         
                        <td width="35%">Company Name</td>
                        <td width="65%"><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="company_name" data-name="company_name" data-type="text" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Company Name"><?php echo $company['company_name'] ;?></a></td>
                    </tr>
                    <tr>         
                        <td width="35%">Address</td>
                        <td width="65%"><p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="company_address_1" data-name="company_address_1" data-type="text" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Address Line 1"><?php echo $company['company_address_1'] ;?></a></p>
						<p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="company_address_2" data-name="company_address_2" data-type="text" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Address Line 2"><?php echo $company['company_address_2'] ;?></a></p>
						<p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="company_city" data-name="city" data-type="text" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit City"><?php echo $company['city'] ;?></a> &nbsp;, &nbsp; <a href="#" class="editable-country" data-url="ajax_helpers/ajax_update_company_data.php" id="company_country" data-name="country" data-type="select" data-pk="<?php echo $company['company_id'] ;?>"   data-title="Edit Country"><?php echo $company['country'] ;?></a></p>
						
						</td>
                    </tr>
					<tr>         
                        <td width="35%">Phone No</td>
                        <td width="65%"><p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="phone_1" data-name="phone_1" data-type="tel" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Phone No 1"><?php echo $company['phone_1'] ;?></a>
						</p>
						<p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="phone_2" data-name="phone_2" data-type="tel" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Phone 2"><?php echo $company['phone_2'] ;?></a></p>
						
						</td>
                    </tr>
					<tr>         
                        <td width="35%">Email</td>
                        <td width="65%"><p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="email" data-name="email" data-type="email" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit email"><?php echo $company['email'] ;?></a>
						</p>		
						</td>
                    </tr>
					<tr>         
                        <td width="35%">Website</td>
                        <td width="65%"><p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="website" data-name="website" data-type="url" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Website"><?php echo $company['website'] ;?></a>
						</p>		
						</td>
                    </tr>
				</tbody>
			</table>
     
</div>					
<div class="row">
 
		<table  class="table table-bordered table-striped" >
                <tbody> 
 					<tr>         
                        <td width="35%">Industry</td>
                        <td width="65%"><p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="industry" data-name="industry" data-type="text" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Industry"><?php echo $company['industry'] ;?></a>
						</p>		
						</td>
                    </tr>
 					<tr>         
                        <td width="35%">Time Zone</td>
                        <td width="65%"><p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="company_time_zone" data-name="company_time_zone" data-type="text" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Time Zone"><?php echo $company['company_time_zone'] ;?></a>
						</p>		
						</td>
                    </tr>
 					<tr>         
                        <td width="35%">Currency</td>
                        <td width="65%"><p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="currency" data-name="currency" data-type="text" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Currency"><?php echo $company['currency'] ;?></a>
						</p>		
						</td>
                    </tr>
 					<tr>         
                        <td width="35%">Chart of Account Code Length</td>
                        <td width="65%"><?php echo $company['coa_code_length'] ;?> digits
					 	
						</td>
                    </tr>
 					<tr>         
                        <td width="35%">Database Tables Prefix</td>
                        <td width="65%"><?php echo DB_PREFIX.$company['company_db_prefix'] ;?>
					 	
						</td>
                    </tr>

				</tbody>
			</table>
     
</div>

<div class="row">
 
		<table  class="table table-bordered table-striped" >
                <tbody> 
  					<tr>         
                        <td width="35%">Logo URL(large 400px X 100px))</td>
                        <td width="65%"><img src="<?php echo $company['company_logo_home'] ;?>" alt="Large Logo" border="0"  width="400" height="100" /><p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="company_logo_home" data-name="company_logo_home" data-type="url" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Large Logo URL"><?php echo $company['company_logo_home'] ;?></a>
						</p>		
						</td>
                    </tr>
 					<tr>         
                        <td width="35%">Logo URL(medium 250px X 150px) </td>
                        <td width="65%"><img src="<?php echo $company['company_logo_head'] ;?>" alt="Medium Logo" border="0"  width="250" height="150" /><p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="company_logo_head" data-name="company_logo_head" data-type="url" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Medium Logo URL"><?php echo $company['company_logo_head'] ;?></a>
						
						</p>		
						</td>
                    </tr>
 					<tr>         
                        <td width="35%">Logo URL (small 100px x 100px)</td>
                        <td width="65%"><img   src="<?php echo $company['company_logo_icon'] ;?>" alt="Small Logo" border="0"  width="100" height="100" /><p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="company_logo_icon" data-name="company_logo_icon" data-type="url" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Small Logo URL"><?php echo $company['company_logo_icon'] ;?></a>
						
						</p>				
						</td>
                    </tr>
				</tbody>
			</table>
     
</div>				
<div class="row">
 
		<table  class="table table-bordered table-striped" >
                <tbody> 
					<tr>         
                        <td width="35%">Username For Company Admin</td>
                        <td width="65%"><p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="super_admin_user" data-name="super_admin_user" data-type="text" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Admin Username"><?php echo $company['super_admin_user'] ;?></a>
						</p>		
						</td>
                    </tr>
 					<tr>         
                        <td width="35%">Password for Company Admin</td>
                        <td width="65%"><p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="super_admin_password" data-name="super_admin_password" data-type="password" data-pk="<?php echo $company['company_id'] ;?>" data-title="Change Admin Password">********</a>
						</p>		
						</td>
                    </tr>
				</tbody>
			</table>
     
</div>		

<div class="row">
 
		<table  class="table table-bordered table-striped" >
                <tbody> 
					<tr>         
                        <td width="35%">History</td>
                        <td width="65%"><p><strong>Last modified</strong> by User :<b>[ <?php echo $company['last_modified_by'] ;?> ]</b> at <b>[ <?php echo getDateTime($company['last_modified_on'],'dtLong') ;?> ]</b>. Record was <b>first created</b> by User : <b>[  <?php echo $company['created_by'] ;?> ]</b> at <b>[ <?php echo getDateTime($company['created_on'],'dtLong') ;?> ]</b>. Company <b>Status</b> is<b> [ <?php echo $company['company_status'] ;?> ] </b>. Click here to view history of changes to company ID: <strong><?php echo $company['company_id'] ;?></strong> .
						</p>		
						</td>
                    </tr>
 				 
				</tbody>
			</table>
     
</div>						
</div>