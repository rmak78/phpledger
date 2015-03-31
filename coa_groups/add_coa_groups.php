<?php
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
   
   $insert = DB::Insert('sa_test_coa_groups', array(
   				
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
		
		));
	       
		  


 }





?>

<div class="container">
      <div class="row">
      <div class="col-md-5  toppad  pull-right col-md-offset-3 ">
         
       <br>
<p class=" text-info"><?php echo date("Y-m-d h:i:sa"); ?> </p>
      </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad" >
   
   
          <div class="panel panel-info">
		  <form method="post" action="<?php $_SERVER['PHP_SELF']; ?>">
          
          
           
            <div class="panel-body">
              <div class="row">
               
         
                <div class=" col-md-9 col-lg-9 "> 
                  <table class="table table-user-information">
                    <tbody>
                     		  
                      <tr>
                        <td>Group Code:</td>
                        <td><input type="text" required name="group_code" id="group_code"></td>
                      </tr>
					  <tr>
                        <td>Group Description:</td>
                        <td><input type="text" required name="group_description" id="group_description"></td>
                      </tr>
                       <?php 
					   $company_id =$_SESSION['company_id']; 
							$length = DB::query(" SELECT coa_code_length FROM ".DB_PREFIX."companies WHERE company_id='".$company_id."'");
						foreach ($length as $get_length)
						{
						echo $get_length['coa_code_length'];
						$max_length = $get_length['coa_code_length'];
						}
						?>
                  
                     
                      <tr>
                        <td>From Account Code</td>
                        <td><input type="text" required name="From_account_code" id="From_account_code" maxlength="<?php echo $max_length; ?>" ></td>
                      </tr>
                   
                         <tr>
                             <tr>
                        <td>To Account Code</td>
                        <td><input type="text" required name="to_account_code" id="to_account_code"  maxlength="<?php echo $max_length; ?>" ></td>
                      </tr>                       
                      <tr>
                        <td>Balance Sheet Group</td>
                        <td><select type="number" required name="balance_sheet_group" id="balance_sheet_group"  >
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                        
                        </select>
                        </td>
                      </tr>                      
                    <tr>
                        <td>Balance Sheet Side</td>
                        <td><select  required  name="balance_sheet_side" id="balance_sheet_side">
                        <option value="debit">Debit</option>
                        <option value="credit">Credit</option>
                        </select>
                        </td>
                      </tr>  
                      </tr>
					 
                                         
                    <tr>
                        <td>Profit &Loss Statements Group</td>
                        <td><select  required  name="pls_group" id="pls_group">
                         <option value="yes">Yes</option>
                        <option value="no">No</option>
                        
                        </select></td>
                      </tr>  
                      </tr>
                      
                      
                      <tr>
                        <td>Profit &Loss Statements Side</td>
                        <td><select  required  name="pls_side" id="pls_side">
                         <option value="income">Income</option>
                        <option value="expense">Expense</option>
                        </select>
                        </td>
                      </tr>  
                      </tr>
                      
                      
                      
                      <tr>
                        <td>Statistics Only</td>
                        <td><select  required  name="statistics_only" id="statistics_only">
                         <option value="1">Yes</option>
                        <option value="0">No</option>
                        </select></td>
                      </tr>  
                      </tr>
                      
                      
                      
                      <tr>
                        <td>Group Status</td>
                        <td><select type="text" required  name="group_status" id="group_status">
                        <option value="1">Active</option>
                       <option value="0">In-Active</option>  
                        </select>
                        
                        
                        </td>
                      </tr>  
                      </tr>
                      
                      
                      
                      
                     
                     
                     
                     
                     
                     
                      <tr>
                      
					  <td></td>
					  <td><input type="submit" class='btn btn-primary btn-sm' name="submit" value="Submit">
					 
					  </td>
					  </tr>
                     
                    </tbody>
                  </table>
                              
                </div>
              </div>
            </div>
                <!--  <div class="panel-footer">
                        <a data-original-title="Broadcast Message" data-toggle="tooltip" type="button" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-envelope"></i></a>
                        <span class="pull-right">
                            <a href="edit.html" data-original-title="Edit this user" data-toggle="tooltip" type="button" class="btn btn-sm btn-warning"><i class="glyphicon glyphicon-edit"></i></a>
                            <a data-original-title="Remove this user" data-toggle="tooltip" type="button" class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-remove"></i></a>
                        </span>
                    </div> -->
            </form>
          </div>
        </div>
      </div>
    </div>

<?php
include_once("./tools_footer.php");
?>