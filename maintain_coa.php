<?php

// Maintain Chart of Accounts
/*
			[account_id] => 1
            [account_code] => 1200001
            [account_group] => 1
            [account_desc_short] => Cash
            [account_desc_long] => Cash in Hand
            [parent_account_id] => 0
            [last_modified_by] => mansoor
            [last_modified_on] => 2015-03-23 23:10:56
            [created_by] => mansoor
            [created_on] => 
            [account_status] => active

*/
echo "<pre>";
$coa_query = 'SELECT * FROM '.DB_PREFIX.$_SESSION['co_prefix'].'coa';
$coa = DB::query($coa_query);
print_r($coa);
 
echo "</pre>";
echo $coa_query;