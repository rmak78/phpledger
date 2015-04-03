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
echo "<pre>";
print_r(DB::queryFirstRow('SELECT * FROM '.DB_PREFIX.'companies WHERE company_id = '.$_SESSION['company_id']));
echo "</pre>";