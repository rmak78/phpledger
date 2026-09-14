<!-- Sidebar Menu -->
<ul class="sidebar-menu">
	<li class="header">General Legder</li>
            <!-- Optionally, you can add icons to the links -->     
    <li class="treeview">
    	<a href="#"><span><i class="fa fa-book fa-lg"></i>&nbsp;General Ledger</span> <i class="fa fa-angle-left pull-right"></i></a>
    	<ul class="treeview-menu">
			<li><a href="#"><span>Setup</span><i class="fa fa-angle-left pull-right"></i></a>
				<ul class="treeview-menu">
					<li>
						<a href="<?php echo SITE_ROOT; ?>?route=modules/system/sys_config">System Configurations</a>
					</li>
					<li>
						<a href="<?php echo SITE_ROOT; ?>?route=modules/gl/setup/company/company_info">Company</a>
					</li>
					<li>
						<a href="#"><span>Account Groups</span><i class="fa fa-angle-left pull-right"></i></a>
						<ul class="treeview-menu">
							<li>
								<a href="<?php echo SITE_ROOT; ?>?route=modules/gl/setup/coa_groups/list_coa_groups">List of Account Groups</a>
							</li>
							<li>
								<a href="<?php echo SITE_ROOT; ?>?route=modules/gl/setup/coa_groups/add_coa_group">Add New Account Group</a>
							</li>
				
						</ul>
					</li>
					<li>
						<a href="#"><span>Chart of Account</span><i class="fa fa-angle-left pull-right"></i></a>
                		<ul class="treeview-menu">
							<li>
								<a href="<?php echo SITE_ROOT; ?>?route=modules/gl/setup/coa/list_coa">List Chart of Account</a>
							</li>
							<li>
								<a href="<?php echo SITE_ROOT; ?>?route=modules/gl/setup/coa/add_coa">Add New Account</a>
							</li>
							
						</ul>
              		</li>
					<li>
							<a href="#">Financial Periods</span><i class="fa fa-angle-left pull-right"></i></a>
							<ul class="treeview-menu">
							<li>
								<a href="#">Fiscal Years</span><i class="fa fa-angle-left pull-right"></i></a>
							
									<ul class="treeview-menu">
									<li>
									<a href="<?php echo SITE_ROOT; ?>?route=modules/gl/setup/financial_periods/add_fiscal_year">Add Fiscal Year</a>
									</li>
									<li>
									<a href="<?php echo SITE_ROOT; ?>?route=modules/gl/setup/financial_periods/list_fiscal_years">List Fiscal Years</a>
									</li>
									</ul>
							</li>
							<li>
								<a href="#">Reporting Periods</span><i class="fa fa-angle-left pull-right"></i></a>
									<ul class="treeview-menu">
									<li>
									<a href="<?php echo SITE_ROOT; ?>?route=modules/gl/setup/financial_periods/add_reporting_period">Add Reporting Period</a>
									</li>
									<li>
									<a href="<?php echo SITE_ROOT; ?>?route=modules/gl/setup/financial_periods/list_reporting_periods">List Reporting Periods</a>
									</li>
									</ul>
							</li>
							</ul>
					</li>
			</ul>
		</li>	 	
    		<li>
    			<a href="#"><span>Transactions</span><i class="fa fa-angle-left pull-right"></i></a>
    			<ul class="treeview-menu">
	    			<li>
	    					<a href="#"><span>Expense Vouchers</span><i class="fa fa-angle-left pull-right"></i></a>
                		<ul class="treeview-menu">
							<li>
								<a href="<?php echo SITE_ROOT; ?>?route=modules/gl/transactions/expense/add_expense_voucher">Add Expense Voucher</a>
							</li>
							<li>
								<a href="<?php echo SITE_ROOT; ?>?route=modules/gl/transactions/expense/view_expense_vouchers">View Expense Vouchers</a>
							</li>

						</ul>
	    			</li>
	    			<li>
	    				<a href="#">Cash Reciepts</a>
	    			</li>
	    			<li>
	    				<a href="#">Bank Transactions</a>
	    			</li>
					<li>
						<a href="#"><span>Journal Vouchers</span><i class="fa fa-angle-left pull-right"></i></a>						
                		<ul class="treeview-menu">
							<li>
								<a href="<?php echo SITE_ROOT; ?>?route=modules/gl/transactions/journal_vouchers/add_journal_voucher">Add Journal Voucher</a>
							</li>
							<li>
								<a href="<?php echo SITE_ROOT; ?>?route=modules/gl/transactions/journal_vouchers/view_journal_vouchers">View Journal Vouchers</a>
							</li>
							
						</ul>						
					</li>
  				</ul>
    		</li>
		</ul>
	</li><!-- /li treeview general ledger -->
       
	<li class="header">Human Resources</li>
            <!-- Optionally, you can add icons to the links -->     
    <li class="treeview">
 		<a href="#"><span><i class="fa-lg fa fa-group"></i>&nbsp;Employees</span> <i class="fa fa-angle-left pull-right"></i></a>
		<ul class="treeview-menu">
	        <li>
	        	<a href="#">Setup</a>
	        </li>
	        <li>
	        	<a href="#">Employees</a>
	        </li>
	        <li>
	        	<a href="#">Attandance</a>
	        </li>
			<li>
				<a href="#">Payroll</a>
			</li>
		</ul>
	</li>
		 
</ul><!-- /.sidebar-menu  -->