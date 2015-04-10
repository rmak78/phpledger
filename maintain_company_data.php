 




		
		
	




<?php

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
                        <td width="65%"><p><a href="#" class="editable" data-url="ajax_helpers/ajax_update_company_data.php" id="industry" data-name="industry" data-type="select" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Industry"><?php echo $company['industry'] ;?></a>
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
                        <td width="35%">Time Zone</td>
                        <td width="65%"><p><a href="#" class="editable bfh-timezones" data-url="ajax_helpers/ajax_update_company_data.php"
                         id="company_time_zone" data-name="company_time_zone" data-type="select" data-pk="<?php echo $company['company_id'] ?>" data-title="Edit Time Zone">
						 <?php echo $company['company_time_zone'] ;?></a>
						</p>		
						</td>
                    </tr>
 					<tr>         
                        <td width="35%">Currency</td>
                        <td width="65%"><p><a href="#" class="" data-url="ajax_helpers/ajax_update_company_data.php" id="currency" data-name="currency" data-type="select" data-pk="<?php echo $company['company_id'] ;?>" data-title="Edit Currency"><?php echo $company['currency'] ;?></a>
						</p>		
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
							
							echo "Level ".$i." : ".$company['coa_level_'.$i.'_length']."<br>";
							$x=$company['coa_level_'.$i.'_length'];
							while ($x <> 0) {
							$code_sample .= "X";
							$x = $x-1;
							}
							$i++;
							} ?>
							Sample Code = <?php echo $code_sample; ?>
						</td>
                    </tr>
				</tbody>
			</table>
     
</div>
<div class="row">
 
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
     
</div>						
</div>
<script type="text/javascript">
$(document).ready(function(){
  $('#btnSaveCOAlevels').click(function(){
        $.ajax({
				type: "POST",
				url: "ajax_helpers/ajax_update_company_data.php",
			data: $('#frm_coa_levels').serialize()	,
				success: function(data){
					$("#coaLevelsModal").modal('hide');
					alert("COA Levels Saved: \n"+data);
					window.location.reload();
					},
				error: function(data){
					alert("Unable to save: \n"+data);					
					$("#coaLevelsModal").modal('hide');
					}
			});
	});
});	

		
</script>
<script type="text/javascript">
$(document).ready(function(){
	var n=$('#coa_levels_length').val();
	for(i=1;i<=n;i++){
		$('div#coa_level_'+i+'_length').show();
	}
	n++;
	for(;n<=9;n++){
		$('div#coa_level_'+n+'_length').hide();
	}
  $('#coa_levels_length').change(function(){
		var n=$(this).val();
		for(i=1;i<=n;i++){
			$('div#coa_level_'+i+'_length').show();
		}
		n++;
		for(;n<=9;n++){
			$('div#coa_level_'+n+'_length').hide();
		}
	});
});	
</script>

<!-- Modal -->
<div class="modal fade" id="coaLevelsModal" tabindex="-1" role="dialog" 
   aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
	  <form class="form-horizontal" id="frm_coa_levels" name="frm_coa_levels" role="form" method="POST">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" 
               aria-hidden="true">×
            </button>
            <h4 class="modal-title" id="myModalLabel">
               Define Chart of Account Levels
            </h4>
         </div>
         <div class="modal-body">
		 
            <div class="control-group">	
				<label>Chart of Account Levels (1-9)</label>
				<select class="form-control" name="coa_levels_length" id="coa_levels_length">
				<option value="1" <?php if ($company['coa_levels'] == 1) { echo 'selected="selected"';} ?> >1</option>
				<option value="2" <?php if ($company['coa_levels'] == 2) { echo 'selected="selected"';} ?> >2</option>
				<option value="3" <?php if ($company['coa_levels'] == 3) { echo 'selected="selected"';} ?> >3</option>
				<option value="4" <?php if ($company['coa_levels'] == 4) { echo 'selected="selected"';} ?> >4</option>
				<option value="5" <?php if ($company['coa_levels'] == 5) { echo 'selected="selected"';} ?> >5</option>
				<option value="6" <?php if ($company['coa_levels'] == 6) { echo 'selected="selected"';} ?> >6</option>
				<option value="7" <?php if ($company['coa_levels'] == 7) { echo 'selected="selected"';} ?> >7</option>
				<option value="8" <?php if ($company['coa_levels'] == 8) { echo 'selected="selected"';} ?> >8</option>
				<option value="9" <?php if ($company['coa_levels'] == 9) { echo 'selected="selected"';} ?> >9</option>
				</select>
				</div>
			
			<!-- COA Code length DIVs -->
			<div class="page-header">
			   <h1>
				  <small>COA Code Length</small>
			   </h1>
			</div>
			
			<div class="form-inline">
			<!-- Completed this for all 9 Levels
				Level 1 = 2 max
				Level 2 = 4 max
				Level 3 = 4 max
				Level 4 = 5 max
				Level 5 = 9 max
				Level 6 = 9 max
				Level 7 = 9 max
				Level 8 = 9 max
				Level 9 = 9 max
				
			-->
			<div class="form-group col-sm-4"  id="coa_level_1_length">				
			<label class="form-label" for="coa_level_1_length">Level 1:
			<select class="form-control" name="coa_level_1_length" >
				<option value="1" <?php if ($company['coa_level_1_length'] == 1) { echo 'selected="selected"';} ?> >1</option>
				<option value="2" <?php if ($company['coa_level_1_length'] == 2) { echo 'selected="selected"';} ?> >2</option>
			</select>
				</label>
			</div>
			<div class="form-group col-sm-4"  id="coa_level_2_length">				
			<label class="form-label" for="coa_level_2_length">Level 2:
			<select class="form-control" name="coa_level_2_length" >
				<option value="1" <?php if ($company['coa_level_2_length'] == 1) { echo 'selected="selected"';} ?> >1</option>
				<option value="2" <?php if ($company['coa_level_2_length'] == 2) { echo 'selected="selected"';} ?> >2</option>
				<option value="3" <?php if ($company['coa_level_2_length'] == 3) { echo 'selected="selected"';} ?> >3</option>
				<option value="4" <?php if ($company['coa_level_2_length'] == 4) { echo 'selected="selected"';} ?> >4</option>
			</select>
				</label>
			</div>	
			
			<div class="form-group col-sm-4"  id="coa_level_3_length">				
			<label class="form-label" for="coa_level_3_length">Level 3:
			<select class="form-control" name="coa_level_3_length" >
				<option value="1" <?php if ($company['coa_level_3_length'] == 1) { echo 'selected="selected"';} ?> >1</option>
				<option value="2" <?php if ($company['coa_level_3_length'] == 2) { echo 'selected="selected"';} ?> >2</option>
				<option value="3" <?php if ($company['coa_level_3_length'] == 3) { echo 'selected="selected"';} ?> >3</option>
				<option value="4" <?php if ($company['coa_level_3_length'] == 4) { echo 'selected="selected"';} ?> >4</option>
			</select>
				</label>
			</div>
			<div class="form-group col-sm-4"  id="coa_level_4_length">				
			<label class="form-label" for="coa_level_4_length">Level 4:
			<select class="form-control" name="coa_level_4_length" >
				<option value="1" <?php if ($company['coa_level_4_length'] == 1) { echo 'selected="selected"';} ?> >1</option>
				<option value="2" <?php if ($company['coa_level_4_length'] == 2) { echo 'selected="selected"';} ?> >2</option>
				<option value="3" <?php if ($company['coa_level_4_length'] == 3) { echo 'selected="selected"';} ?> >3</option>
				<option value="4" <?php if ($company['coa_level_4_length'] == 4) { echo 'selected="selected"';} ?> >4</option>
				<option value="5" <?php if ($company['coa_level_4_length'] == 5) { echo 'selected="selected"';} ?> >5</option>			
			</select>
				</label>
			</div>
			<div class="form-group col-sm-4"  id="coa_level_5_length">				
			<label class="form-label" for="coa_level_5_length">Level 5:
			<select class="form-control" name="coa_level_5_length" >
				<option value="1" <?php if ($company['coa_level_5_length'] == 1) { echo 'selected="selected"';} ?> >1</option>
				<option value="2" <?php if ($company['coa_level_5_length'] == 2) { echo 'selected="selected"';} ?> >2</option>
				<option value="3" <?php if ($company['coa_level_5_length'] == 3) { echo 'selected="selected"';} ?> >3</option>
				<option value="4" <?php if ($company['coa_level_5_length'] == 4) { echo 'selected="selected"';} ?> >4</option>
				<option value="5" <?php if ($company['coa_level_5_length'] == 5) { echo 'selected="selected"';} ?> >5</option>			
				<option value="6" <?php if ($company['coa_level_5_length'] == 6) { echo 'selected="selected"';} ?> >6</option>
				<option value="7" <?php if ($company['coa_level_5_length'] == 7) { echo 'selected="selected"';} ?> >7</option>
				<option value="8" <?php if ($company['coa_level_5_length'] == 8) { echo 'selected="selected"';} ?> >8</option>
				<option value="9" <?php if ($company['coa_level_5_length'] == 9) { echo 'selected="selected"';} ?> >9</option>
			</select>
				</label>
			</div>
			<div class="form-group col-sm-4"  id="coa_level_6_length">				
			<label class="form-label" for="coa_level_6_length">Level 6:
			<select class="form-control" name="coa_level_6_length" >
				<option value="1" <?php if ($company['coa_level_6_length'] == 1) { echo 'selected="selected"';} ?> >1</option>
				<option value="2" <?php if ($company['coa_level_6_length'] == 2) { echo 'selected="selected"';} ?> >2</option>
				<option value="3" <?php if ($company['coa_level_6_length'] == 3) { echo 'selected="selected"';} ?> >3</option>
				<option value="4" <?php if ($company['coa_level_6_length'] == 4) { echo 'selected="selected"';} ?> >4</option>
				<option value="5" <?php if ($company['coa_level_6_length'] == 5) { echo 'selected="selected"';} ?> >5</option>			
				<option value="6" <?php if ($company['coa_level_6_length'] == 6) { echo 'selected="selected"';} ?> >6</option>
				<option value="7" <?php if ($company['coa_level_6_length'] == 7) { echo 'selected="selected"';} ?> >7</option>
				<option value="8" <?php if ($company['coa_level_6_length'] == 8) { echo 'selected="selected"';} ?> >8</option>
				<option value="9" <?php if ($company['coa_level_6_length'] == 9) { echo 'selected="selected"';} ?> >9</option>
			</select>
				</label>
			</div>
			<div class="form-group col-sm-4"  id="coa_level_7_length">				
			<label class="form-label" for="coa_level_7_length">Level 7:
			<select class="form-control" name="coa_level_7_length" >
				<option value="1" <?php if ($company['coa_level_7_length'] == 1) { echo 'selected="selected"';} ?> >1</option>
				<option value="2" <?php if ($company['coa_level_7_length'] == 2) { echo 'selected="selected"';} ?> >2</option>
				<option value="3" <?php if ($company['coa_level_7_length'] == 3) { echo 'selected="selected"';} ?> >3</option>
				<option value="4" <?php if ($company['coa_level_7_length'] == 4) { echo 'selected="selected"';} ?> >4</option>
				<option value="5" <?php if ($company['coa_level_7_length'] == 5) { echo 'selected="selected"';} ?> >5</option>			
				<option value="6" <?php if ($company['coa_level_7_length'] == 6) { echo 'selected="selected"';} ?> >6</option>
				<option value="7" <?php if ($company['coa_level_7_length'] == 7) { echo 'selected="selected"';} ?> >7</option>
				<option value="8" <?php if ($company['coa_level_7_length'] == 8) { echo 'selected="selected"';} ?> >8</option>
				<option value="9" <?php if ($company['coa_level_7_length'] == 9) { echo 'selected="selected"';} ?> >9</option>
			</select>
				</label>
			</div>
			<div class="form-group col-sm-4"  id="coa_level_8_length">				
			<label class="form-label" for="coa_level_8_length">Level 8:
			<select class="form-control" name="coa_level_8_length" >
				<option value="1" <?php if ($company['coa_level_8_length'] == 1) { echo 'selected="selected"';} ?> >1</option>
				<option value="2" <?php if ($company['coa_level_8_length'] == 2) { echo 'selected="selected"';} ?> >2</option>
				<option value="3" <?php if ($company['coa_level_8_length'] == 3) { echo 'selected="selected"';} ?> >3</option>
				<option value="4" <?php if ($company['coa_level_8_length'] == 4) { echo 'selected="selected"';} ?> >4</option>
				<option value="5" <?php if ($company['coa_level_8_length'] == 5) { echo 'selected="selected"';} ?> >5</option>			
				<option value="6" <?php if ($company['coa_level_8_length'] == 6) { echo 'selected="selected"';} ?> >6</option>
				<option value="7" <?php if ($company['coa_level_8_length'] == 7) { echo 'selected="selected"';} ?> >7</option>
				<option value="8" <?php if ($company['coa_level_8_length'] == 8) { echo 'selected="selected"';} ?> >8</option>
				<option value="9" <?php if ($company['coa_level_8_length'] == 9) { echo 'selected="selected"';} ?> >9</option>
			</select>
				</label>
			</div>
			<div class="form-group col-sm-4"  id="coa_level_9_length">				
			<label class="form-label" for="coa_level_9_length">Level 9:
			<select class="form-control" name="coa_level_9_length" >
				<option value="1" <?php if ($company['coa_level_9_length'] == 1) { echo 'selected="selected"';} ?> >1</option>
				<option value="2" <?php if ($company['coa_level_9_length'] == 2) { echo 'selected="selected"';} ?> >2</option>
				<option value="3" <?php if ($company['coa_level_9_length'] == 3) { echo 'selected="selected"';} ?> >3</option>
				<option value="4" <?php if ($company['coa_level_9_length'] == 4) { echo 'selected="selected"';} ?> >4</option>
				<option value="5" <?php if ($company['coa_level_9_length'] == 5) { echo 'selected="selected"';} ?> >5</option>			
				<option value="6" <?php if ($company['coa_level_9_length'] == 6) { echo 'selected="selected"';} ?> >6</option>
				<option value="7" <?php if ($company['coa_level_9_length'] == 7) { echo 'selected="selected"';} ?> >7</option>
				<option value="8" <?php if ($company['coa_level_9_length'] == 8) { echo 'selected="selected"';} ?> >8</option>
				<option value="9" <?php if ($company['coa_level_9_length'] == 9) { echo 'selected="selected"';} ?> >9</option>
			</select>
				</label>
			<input type="hidden" name="company_id" id="company_id" value="<?php echo $company['company_id']; ?>" >	
			</div>
					
			<!-- End COA Code length DIV-->
			</div>
         <BR><BR><BR><BR><BR><BR><BR>
         <div class="modal-footer">
            <button onclick="javascript:window.location.reload();" type="button" class="btn btn-default" 
               data-dismiss="modal">Close
            </button>
            <button name="btnSaveCOAlevels" id="btnSaveCOAlevels" type="button" class="btn btn-primary">
               Save
            </button>
         </div>
		 </form>
         
         <script>
		 $(document).ready(function() {
$.fn.editable.defaults.ajaxOptions = {type: "GET"};
		 $("#company_time_zone").editable({
  value: "bar", // The option with this value should be selected
  source: [
    {value: "GMT+1", text: "GMT+1"},
    {value: "GMT+2", text: "GMT+2"},
    {value: "GMT+3", text: "GMT+3"},
	{value: "GMT+4", text: "GMT+4"},
	{value: "GMT+5", text: "GMT+5"},
	{value: "GMT+6", text: "GMT+6"},
	{value: "GMT+7", text: "GMT+7"},
	{value: "GMT+8", text: "GMT+8"},
	{value: "GMT+9", text: "GMT+9"},
    {value: "GMT+10", text: "GMT+10"},
	{value: "GMT+11", text: "GMT+11"},
	{value: "GMT+12", text: "GMT+12"},
			 
  ]
});
		 });
         </script>
         
         
		 <h6 style="color:grey;">*If you don't understand the Chart of Account, Please don't fill this</h6>
		 </div><!-- /.modal-body -->
      </div><!-- /.modal-content -->
   </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

