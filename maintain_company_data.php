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
            <table id="user" class="table table-bordered table-striped" style="clear: both">
                <tbody> 
                    <tr>         
                        <td width="25%">Company Name</td>
                        <td width="75%"><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="company_name" data-name="company_name" data-type="text" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Company Name"><?php echo $company['company_name'] ;?></a></td>
                    </tr>
                    <tr>         
                        <td width="25%">Address</td>
                        <td width="75%"><p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="company_address_1" data-name="company_address_1" data-type="text" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Address Line 1"><?php echo $company['company_address_1'] ;?></a>
						<p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="company_address_2" data-name="company_address_2" data-type="text" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Address Line 2"><?php echo $company['company_address_2'] ;?></a></p>
						<p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="company_city" data-name="city" data-type="text" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit City"><?php echo $company['city'] ;?></a> &nbsp;, &nbsp; <a href="#" class="editable-country" data-url="ajax_helpers/ajax_update_company_data.php" id="company_country" data-name="country" data-type="select" data-pk="<?php echo $company['company_id'] ;?>"   data-title="Edit Country"><?php echo $company['country'] ;?></a></p>
						
						</td>
                    </tr>
				</tbody>
			</table>

</div>
</div>
 
<?php
echo "<pre>";
print_r($company);
echo "</pre>";
