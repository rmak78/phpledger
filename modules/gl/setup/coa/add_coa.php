<?php
$group_id='';
?>    
<div class="content-wrapper">	
	<section class="content-header">
          <h1>
            Chart Of Account
            <small>Add New</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-book"></i></a>General Ledger</li>
            <li><a href="#">Chart of Account</a></li>
            <li class="active">Add new Account</li>
          </ol>
        </section>
		 <!-- Main content -->
 <section class="content">
    <div class="row">
<div class="col-md-10">
        <!-- general form elements -->
    <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Provide the required fields</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <form role="form">
                  <div class="box-body">
                    <div class="form-group">
								<label>Account Group:</label>
									<select class="form-control" name="account_group" id="account_group" >
										<option value=""> -- Select --</option>
										<?php 
										$groups_query = "SELECT group_id, group_code, group_description from ";
										$groups_query .= DB_PREFIX.$_SESSION['co_prefix']."coa_groups";
										$groups = DB::query($groups_query);	
										foreach ($groups as $group) {
										?>					
											<option <?php 
											if ($group_id == $group['group_id'] ) {
											echo 'selected = "selected"';
											}								
											?>  value="<?php echo $group['group_id']; ?>" ><?php echo $group['group_code']." - ".$group['group_description']; ?></option>
										<?php 
										}
										?>
									</select>
									 <p class="help-block"> </p>	
					</div>
                    <div class="form-group">
						<label>Parent Account:</label>
						 <select class="form-control" name="parent_account_id" id="parent_account_id">
										<option value="0"> -- None --</option>
										<?php 
										$accounts_query = "SELECT account_id, coa_level, account_code, account_desc_short FROM ";
										$accounts_query .= DB_PREFIX.$_SESSION['co_prefix']."coa WHERE((consolidate_only =  1 ) AND (account_status =  'active') ) ORDER BY coa_level " ;				
										$accounts = DB::query($accounts_query);
										if($accounts) {
											$options="";
											$temp_coa = DB::queryfirstfield($accounts_query);
											$temp_coa_level = $temp_coa['coa_level'];
											$options .=" <optgroup label='Level: ".$temp_coa_level."'>";
										foreach ($accounts as $account) {	
											if($temp_coa_level == $account['coa_level']){
												$options .="<option value='".$account['account_id']."'>".$account['account_code']."-".$account['account_desc_short']."</option>";
											}
											else{
												$options .="</optgroup>";
												$options .=" <optgroup label='Level: ".$account['coa_level']."'>";
												$options .="<option value='".$account['account_id']."'>".$account['account_code']."-".$account['account_desc_short']."</option>";
												$temp_coa_level = $account['coa_level'];
											}
											
										}
										}
										echo $options;
										?>
						
						</select>
							 <p class="help-block"> </p>
					
				</div>	
				<div class="form-group">
                        <label>Account Code:</label>
						<input class="form-control" name="account_code" type="number"  value="" required name="account_code" id="account_code">
				</div>
                <div class="form-group">
                        <label>Account Description Short Version:</label>
						 <input class="form-control" type="text" required name="account_desc_short">
				</div>
				 <div class="form-group">
                        <label>Account Description Long Version:</label>                         
						 <textarea class="form-control" cols="24" rows="3" required name="account_desc_long"></textarea>
				</div>
				<div class="form-group">
					<label>Account Status:</label>
					<select class="form-control" name="account_status">
						<option value="active">Active</option>
						<option value="in-active">In-Active</option>
					</select>
				</div>
				</div><!-- /.box-body -->
                  <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                  </div>
                </form>
    </div><!-- /.box -->
</div> 	
</div>	
</div>
</section>		