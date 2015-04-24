
<?php

$group_id=$_GET['group_id'];
if(isset($_POST['submit']))
{
	
 $group_code		= $_POST['group_code'];
 $group_description = $_POST['group_description'];
 $From_account_code = $_POST['From_account_code'];
 $to_account_code   = $_POST['to_account_code'];
 $balance_sheet_group= $_POST['balance_sheet_group'];
 $balance_sheet_side= $_POST['balance_sheet_side'];
 $pls_group		 = $_POST['pls_group'];
 $pls_side		  = $_POST['pls_side'];
 $statistics_only   = $_POST['statistics_only'];
 $group_status	  = $_POST['group_status'];
   if($group_id<>"")
   {
   DB::update(DB_PREFIX.$_SESSION['co_prefix'].'coa_groups', array(
   				
			'group_code' 			=> $group_code,	
            'group_description' 	 => $group_description,
            'from_account_code' 	 => $From_account_code,
            'to_account_code' 	   => $to_account_code,
            'balance_sheet_group'   => $balance_sheet_group,
            'balance_sheet_side'    => $balance_sheet_side,
            'pls_group' 			 => $pls_group,	 
            'pls_side' 			  => $pls_side,
            'statistics_only' 	   => $statistics_only,
            'group_status' 		  => $group_status
		
		),
		"group_id=%s", $group_id
		);
	       
   }


 }





?>
    
	
	<section class="content-header">
          <h1>
           Add Chart of Account Group
           
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-book"></i></a>General Ledger</li>
            <li><a href="#">Chart of Account Group</a></li>
            <li class="active">Add new Account Group</li>
          </ol>
        </section>
		 <!-- Main content -->
 <section class="content">
    
        <!-- general form elements -->
    <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title">Provide the required fields</h3>
				   <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
                </div><!-- /.box-header -->
                <!-- form start -->
                <form role="form" class="form-horizontal" action="" method="post" name="">
				  <?php 
					$slect_data = DB::query ("SELECT * from ".DB_PREFIX.$_SESSION['co_prefix']."coa_groups WHERE group_id='".$group_id."'");
	foreach ($slect_data as $coa_group)
	
						?>
                  <div class="box-body">
				  
                    <div class="form-group">
								<label class="col-md-3 col-sm-3 control-label">Group Code:</label>
									 <div class="col-md-9 col-sm-9">
									 <input type="text" class="form-control" required name="group_code" id="group_code"  
                                     value="<?php echo $coa_group['group_code']; ?>" />
									 </div>
							</div>
							
							
			    	
					
                    <div class="form-group">
						<label class="col-md-3 col-sm-3 control-label">Group Description:</label>
						  <div class="col-md-9 col-sm-9">
						 <input type="text" class="form-control" required name="group_description" id="group_description" value="<?php echo $coa_group['group_description']; ?>">
						  </div>
					</div>		
					
					
				<div class="form-group">
                        <label class="col-md-3 col-sm-3 control-label">Balance Sheet Group:</label>
						  <div class="col-md-9 col-sm-9">
						 <select class="form-control" name="balance_sheet_group" id="balance_sheet_group" > 
                        
						<?php
                       if($coa_group['balance_sheet_group']==1)
					   {
                       echo "<option value='1' selected >Yes</option>";
					   echo "<option value='0'  >No</option>"; 
					   }
					   else
					   {
						  echo "<option value='0' selected >No</option>"; 
						   echo "<option value='1'  >Yes</option>";
						   }
                        ?>
                        </select>
					  </div>
				</div>
                <div class="form-group">
                        <label class="col-md-3 col-sm-3 control-label">Balance Sheet Side:</label>
						<div class="col-md-9 col-sm-9">
						<select  class="form-control"  name="balance_sheet_side" id="balance_sheet_side">
                        <?php
                       if($coa_group['balance_sheet_side']==debit)
					   {
                       echo "<option value='debit' selected='selected' >Debit</option>";
					    echo "<option value='credit'  >Credit</option>";
					   }
					   else
					   {
						   echo "<option value='credit' selected='selected' >Credit</option>";
						    echo "<option value='debit' >Debit</option>"; 
						   }
                        ?>
                        </select>
						</div>
				</div>
				 <div class="form-group">
                        <label class="col-md-3 col-sm-3 control-label">Profit &Loss Statements Group</label>                         
						 <div class="col-md-9 col-sm-9">
						<select class="form-control" required  name="pls_group" id="pls_group">
                           <?php
                       if($coa_group['pls_group']==1)
					   {
                       echo "<option value='1' selected >Yes</option>";
					    echo "<option value='0'  >No</option>";
					   }
					   else
					   {
						  echo "<option value='0' selected >No</option>"; 
						   echo "<option value='1'  >Yes</option>";
						   }
                        ?>
                        
                        </select>
					 </div>
				</div>
				 <div class="form-group">
                        
						<label class="col-md-3 col-sm-3 control-label">Profit &Loss Statements Side</label>                         
						<div class="col-md-9 col-sm-9">
						<select  class="form-control"  name="pls_side" id="pls_side">
                             <?php
							 if($coa_group['pls_side']==income)
							 {
                       echo "<option value='income' selected='selected'>Income</option>";
					   echo"<option value='expense' >Expense</option>"; 
							 }
							 else
							 {
								echo"<option value='expense' selected='selected'>Expense</option>"; 
								echo "<option value='income' >Income</option>";
								 }
							 ?>
                        
                        </select>
						</div>
				</div>
				 <div class="form-group">
                        
						<label class="col-md-3 col-sm-3 control-label">Statistics Only</label>                         
						<div class="col-md-9 col-sm-9">
						<select  class="form-control"  name="statistics_only" id="statistics_only">
                         <?php
                       if($coa_group['statistics_only']==1)
					   {
                             echo "<option value='income' selected>Yes</option>";
					  		echo "<option value='income' >No</option>";
					   }
					   else
					   {
						   echo "<option value='income' selected>No</option>";
						   echo "<option value='income' >Yes</option>";
						   }
						  ?>
                         </select>
						</div>
				</div>
				 <div class="form-group">
                       
						<label class="col-md-3 col-sm-3 control-label">Created By</label>                         
						 <div class="col-md-9 col-sm-9">
						<input type="text" class="form-control" name="created_by" id="created_by"  value="<?php echo $coa_group['created_by']; ?>" >
						</div>
				</div>
				<div class="form-group">
                        
						<label class="col-md-3 col-sm-3 control-label">Created On</label>                         
						 <div class="col-md-9 col-sm-9">
						<input type="text" class="form-control" name="created_on" id="created_on" value="<?php echo $coa_group['created_on']; ?>" >
						</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 col-sm-3 control-label">Group Status:</label>
					<div class="col-md-9 col-sm-9">
					<select class="form-control"  required  name="group_status" id="group_status">
                        <option value="active">Active</option>
                       <option value="in-active">In-Active</option>  
                        </select>
				
					</div>
				</div>	
				
				</div><!-- /.box-body -->
                  <div class="box-footer">
					<div class="form-group">
					<div class="col-sm-3">
					</div>
					<div class="col-sm-9">
                    <input type="submit" class='btn btn-primary' name="submit" value="SAVE">
					
					</div>
					</div>
				 </div>
                </form>
    </div><!-- /.box -->


</section>		