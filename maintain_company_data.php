<?php

// Maintain a company
/*
   To Do  KA
   1- Name of cities for selected country
   
   
   
 
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
                        <td width="35%">Chart of Account</td>
                        <!-- <td width="65%"><?php //echo $company['coa_code_length'] ;?> digits -->
						
					 	<td width="65%">
						<?php 
						$accounts = DB::queryFirstField("SELECT COUNT(*) FROM ".DB_PREFIX.$_SESSION['co_prefix']."coa");
						
						
						
						if($accounts > 0){ ?><p>							 	
						Chart of Account can't be edited as some accounts have already been defined. </p>
						<?php }else{ ?>
						<a data-toggle="modal" data-target="#myModal">Modify Chart of Account Definitions</a>
						<?php } ?>
						<p>You have defined <strong>
						<?php echo $company['coa_levels']; ?> </strong> levels</p>
						
						<? 
							$code_sample = '';
							$i = 1 ;
							while ($i <> ($company['coa_levels'] + 1) ) {
							$code_sample .= " ";
							
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
						<abbr class="timeago" title="<?php echo  $company['last_modified_on'] ;?>"><?php echo  $company['last_modified_on'];?></abbr></td>
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
	if ($('#coa_levels_length').val() != '') {
		var divcoalevel='';
		//var a=$('#frm_coa_levels').serialize();
		var a=$('#coa_levels_length').val();
		for(var i=1;i<=a;i++){
		divcoalevel='<label>Enter Code Length for Level '+i+'</label><input value="'+i+'" type="number" class="form-control" name="coa_code_length'+i+'" id="code_length'+i+'" required="required">';
		$('div#coa_code_length'+i).html(divcoalevel);		
		}
		divcoalevel='';
	} else {
		alert('Comment cannot be blank');
	
	}
	});
});	
</script>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" 
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
		 <div class="row">
            <div class="control-group">
				<div  >		
				<label>Enter Chart of Account Levels (1-9)</label>
				<select value="4" type="number" class="form-control" name="coa_levels_length" id="coa_levels_length" required="required">
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
			</div>
		</div>
		<div class="row">	
			<!-- COA Code length DIVs -->
			<div class="page-header">
			   <h1>
				  <small>COA Code Length</small>
			   </h1>
			</div>
			<div class="control-group">
			<!-- TODO: Complete this for all 9 Levels
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
			<div  id="coa_level_1_length">				
			<label class="form-label" for="coa_level_1_length">Level 1:
			<select class="form-control" name="coa_level_1_length" >
				<option value="1" <?php if ($company['coa_level_1_length'] == 1) { echo 'selected="selected"';} ?> >1</option>
				<option value="2" <?php if ($company['coa_level_1_length'] == 2) { echo 'selected="selected"';} ?> >2</option>
			</select>
				</label>
			</div>
			<div  id="coa_level_2_length">				
			<label class="form-label" for="coa_level_2_length">Level 2:
			<select class="form-control" name="coa_level_2_length" >
				<option value="1" <?php if ($company['coa_level_2_length'] == 1) { echo 'selected="selected"';} ?> >1</option>
				<option value="2" <?php if ($company['coa_level_2_length'] == 2) { echo 'selected="selected"';} ?> >2</option>
				<option value="3" <?php if ($company['coa_level_2_length'] == 3) { echo 'selected="selected"';} ?> >3</option>
				<option value="4" <?php if ($company['coa_level_2_length'] == 4) { echo 'selected="selected"';} ?> >4</option>
			</select>
				</label>
			</div>	
			<div class="col-sm-8" id="coa_code_length3">				
			</div>	
			<div class="col-sm-8" id="coa_code_length4">				
			</div>	
			<div class="col-sm-8" id="coa_code_length5">				
			</div>	
			<div class="col-sm-8" id="coa_code_length6">				
			</div>
			<div class="col-sm-8" id="coa_code_length7">				
			</div>
			<div class="col-sm-8" id="coa_code_length8">				
			</div>
			<div class="col-sm-8" id="coa_code_length9">				
			</div>			
			<!-- End COA Code length DIV-->
			</div>
		 </div>	
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" 
               data-dismiss="modal">Close
            </button>
            <button name="btnSaveCOAlevels" id="btnSaveCOAlevels" type="button" class="btn btn-primary">
               Save
            </button>
         </div>
		 </form>
      </div><!-- /.modal-content -->
   </div><!-- /.modal-dialog -->
</div><!-- /.modal -->