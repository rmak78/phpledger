<?php

// Maintain COA Groups
/*
[group_id] => 1
            [group_code] => ASST
            [group_description] => Assets
            [from_account_code] => 4500001
            [to_account_code] => 4599999
            [balance_sheet_group] => 1
            [balance_sheet_side] => Debit
            [pls_group] => 0
            [pls_side] => Expenses
            [statistics_only] => 0
            [last_modified_by] => mansoor
            [last_modified_on] => 2015-03-22 22:33:07
            [created_by] => mansoor
            [created_on] => 
            [group_status] => active
*/

$coa_groups_query = 'SELECT * FROM '.DB_PREFIX.$_SESSION['co_prefix'].'coa_groups';
$groups = DB::query($coa_groups_query);

print_r($groups);
echo "<!--".$coa_groups_query."-->";