/*
SQLyog Ultimate v10.00 Beta1
MySQL - 5.5.27-log : Database - phpledger2
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `sa_companies` */

DROP TABLE IF EXISTS `sa_companies`;

CREATE TABLE `sa_companies` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL,
  `company_db_prefix` varchar(6) NOT NULL,
  `company_address_1` varchar(255) DEFAULT NULL,
  `company_address_2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `phone_1` varchar(255) DEFAULT NULL,
  `phone_2` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `industry` varchar(255) DEFAULT NULL,
  `company_time_zone` varchar(255) DEFAULT 'GMT',
  `coa_levels` int(2) DEFAULT '4',
  `coa_level_1_label` varchar(255) DEFAULT 'company',
  `coa_level_1_length` int(1) DEFAULT '1',
  `coa_level_2_label` varchar(255) DEFAULT 'Main',
  `coa_level_2_length` int(1) DEFAULT '2',
  `coa)level_3_label` varchar(255) DEFAULT 'control',
  `coa_level_3_length` int(1) DEFAULT '2',
  `coa_level_4_label` varchar(255) DEFAULT 'sub-control',
  `coa_level_4_length` int(1) DEFAULT '2',
  `coa_level_5_label` varchar(255) DEFAULT 'Activity',
  `coa_level_5_length` int(1) DEFAULT '5',
  `coa_level_6_label` varchar(255) DEFAULT NULL,
  `coa_level_6_length` int(1) DEFAULT '5',
  `coa_level_7_label` varchar(255) DEFAULT NULL,
  `coa_level_7_length` int(1) DEFAULT '5',
  `coa_level_8_label` varchar(255) DEFAULT NULL,
  `coa_level_8_length` int(1) DEFAULT '5',
  `coa_level_9_label` varchar(255) DEFAULT NULL,
  `coa_level_9_length` int(1) DEFAULT '5',
  `company_logo_home` varchar(255) DEFAULT NULL,
  `company_logo_head` varchar(255) DEFAULT NULL,
  `company_logo_icon` varchar(255) DEFAULT NULL,
  `super_admin_user` varchar(255) DEFAULT NULL,
  `super_admin_password` varchar(255) DEFAULT NULL,
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `company_status` varchar(100) DEFAULT 'active',
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `sa_companies` */

insert  into `sa_companies`(`company_id`,`company_name`,`company_db_prefix`,`company_address_1`,`company_address_2`,`city`,`country`,`currency`,`phone_1`,`phone_2`,`email`,`website`,`industry`,`company_time_zone`,`coa_levels`,`coa_level_1_label`,`coa_level_1_length`,`coa_level_2_label`,`coa_level_2_length`,`coa)level_3_label`,`coa_level_3_length`,`coa_level_4_label`,`coa_level_4_length`,`coa_level_5_label`,`coa_level_5_length`,`coa_level_6_label`,`coa_level_6_length`,`coa_level_7_label`,`coa_level_7_length`,`coa_level_8_label`,`coa_level_8_length`,`coa_level_9_label`,`coa_level_9_length`,`company_logo_home`,`company_logo_head`,`company_logo_icon`,`super_admin_user`,`super_admin_password`,`last_modified_by`,`last_modified_on`,`created_by`,`created_on`,`company_status`) values (1,'Sutlej Solutions','test_','Al-Sadeeq Akbar','Bahawalpur Road,','Lodhran','Pakistan','PKR','365665555','+92-333-341-4999','mansoor@sutlej.net','http://sutlej.net','IT','GMT',5,'company',1,'Main',2,'control',2,'sub-control',4,'Activity',5,NULL,0,NULL,0,NULL,0,NULL,0,'http://www.sky-valley-web-design.ca/images/250x400.gif','http://www.sky-valley-web-design.ca/images/250x150.gif','http://www.sky-valley-web-design.ca/images/100x100.gif','admin','admin','admin','2015-04-18 00:41:07','system','2015-04-04 03:24:43','active');

/*Table structure for table `sa_sys_config` */

DROP TABLE IF EXISTS `sa_sys_config`;

CREATE TABLE `sa_sys_config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `key_label` varchar(255) DEFAULT NULL,
  `key_help_text` varchar(255) DEFAULT NULL,
  `value` varchar(255) NOT NULL,
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `sa_sys_config` */

insert  into `sa_sys_config`(`config_id`,`key`,`key_label`,`key_help_text`,`value`,`last_modified_by`,`last_modified_on`,`created_by`,`created_on`) values (1,'super_user_id',NULL,NULL,'mansoor','system','2015-03-23 19:16:39','system','2015-03-23 19:17:28'),(2,'super_user_pass',NULL,NULL,'pakistan','system','2015-03-23 19:16:39','system','2015-03-23 19:17:29'),(3,'default_company_id',NULL,NULL,'1','system','2015-03-23 19:16:39','system','2015-03-23 19:16:46'),(4,'default_company_db_prefix',NULL,NULL,'test','system','2015-03-23 19:16:39','system','2015-03-23 19:17:34'),(5,'default_time_zone',NULL,NULL,'GMT','system','2015-03-23 19:16:39','system','2015-03-31 18:45:46');

/*Table structure for table `sa_test_coa` */

DROP TABLE IF EXISTS `sa_test_coa`;

CREATE TABLE `sa_test_coa` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_code` varchar(255) DEFAULT NULL,
  `account_group` int(11) DEFAULT NULL,
  `account_desc_short` varchar(255) DEFAULT NULL,
  `account_desc_long` varchar(255) DEFAULT NULL,
  `activity_account` tinyint(1) DEFAULT '0',
  `consolidate_only` tinyint(1) DEFAULT '1',
  `has_parent` tinyint(1) DEFAULT '0',
  `coa_level` tinyint(1) DEFAULT '1',
  `has_children` tinyint(1) DEFAULT '0',
  `parent_account_id` int(11) DEFAULT '0',
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` timestamp NULL DEFAULT NULL,
  `account_status` varchar(255) DEFAULT 'active',
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;

/*Data for the table `sa_test_coa` */

insert  into `sa_test_coa`(`account_id`,`account_code`,`account_group`,`account_desc_short`,`account_desc_long`,`activity_account`,`consolidate_only`,`has_parent`,`coa_level`,`has_children`,`parent_account_id`,`last_modified_by`,`last_modified_on`,`created_by`,`created_on`,`account_status`) values (1,'1',1,'Assets','Assets Group',0,1,0,1,1,0,NULL,NULL,NULL,NULL,'active'),(2,'101',1,'Current Assets','Current Assets',0,1,1,2,1,1,NULL,NULL,NULL,NULL,'active'),(3,'102',1,'Fixed Assets','Fixed Assets',0,1,1,2,1,1,NULL,NULL,NULL,NULL,'active'),(5,'10101',1,'Cash','Available Cash ',0,1,1,3,1,2,NULL,NULL,NULL,NULL,'active'),(10,'2',2,'Liabilities','Liabilities ',0,1,0,1,0,0,'test','1970-01-01 05:00:00','test','0000-00-00 00:00:00','Active'),(11,'3',3,'Equity','Owner\'s Equity',0,1,0,1,1,0,'test','1970-01-01 05:00:00','test','0000-00-00 00:00:00','Active'),(12,'4',4,'Income','Income',0,1,0,1,1,0,'test','1970-01-01 05:00:00','test','0000-00-00 00:00:00','Active'),(13,'5',5,'Expenses','Expenses',0,1,0,1,0,0,'test','1970-01-01 05:00:00','test','0000-00-00 00:00:00','Active'),(14,'6',6,'Adjustments','Adjustments',0,1,0,1,0,0,'test','1970-01-01 05:00:00','test','0000-00-00 00:00:00','Active'),(15,'7',7,'Retained Earnings','Retained Earnings',0,1,0,1,0,0,'test','1970-01-01 05:00:00','test','0000-00-00 00:00:00','Active'),(17,'101010001',1,'Petty Cash','Petty Cash Accounts',0,1,1,4,1,5,'test','1970-01-01 05:00:00','test','0000-00-00 00:00:00','Active'),(18,'10101000100001',1,'Petty Cash - Mansoor','Petty Cash with Mansoor',1,0,1,5,0,17,'test','1970-01-01 05:00:00','test','0000-00-00 00:00:00','Active'),(19,'10101000100002',1,'Petty Cash - Kafia','Petty Cash with Kafia',1,0,1,5,0,17,'test','1970-01-01 05:00:00','test','0000-00-00 00:00:00','Active'),(20,'101010002',1,'Bank Accounts','Cash in Bank Accounts',0,1,1,4,1,5,'test','1970-01-01 05:00:00','test','0000-00-00 00:00:00','Active'),(21,'10101000200001',1,'Meezan Bank (PKR)','PKR Bank Account in Meezan Bank',1,0,1,5,0,20,'test','1970-01-01 05:00:00','test','0000-00-00 00:00:00','Active'),(22,'10101000200002',1,'Meezan Bank (USD)','USD Forex Account in Meezan Bank',1,0,1,5,0,20,'test','1970-01-01 05:00:00','test','0000-00-00 00:00:00','Active'),(23,'10103',1,'Accounts Recieveable','Accounts Recieveables from Third Parties',0,1,1,3,0,2,'test','1970-01-01 05:00:00','test','0000-00-00 00:00:00','Active'),(24,'10102',1,'Investments','Investments ',0,1,1,3,0,2,'test','1970-01-01 05:00:00','test','0000-00-00 00:00:00','Active'),(25,'10104',1,'Inventory','Product inventory/ Supplies',0,1,1,3,0,2,'test','1970-01-01 05:00:00','test','0000-00-00 00:00:00','Active'),(26,'10105',1,'Pre-Paid Expenses','Pre-Paid Expenses that have yet to be consumed',0,1,1,3,0,2,'test','1970-01-01 05:00:00','test','0000-00-00 00:00:00','Active'),(27,'10201',1,'Land','Land ownership',0,1,1,3,0,3,'test','1970-01-01 05:00:00','test','0000-00-00 00:00:00','Active'),(28,'10202',1,'Buildings','Buildings Owned',0,1,1,3,0,3,'test','1970-01-01 05:00:00','test','0000-00-00 00:00:00','Active'),(29,'401',4,'Freelancing Sites','Income from Freelancing Site',0,1,1,2,0,12,'test','1970-01-01 05:00:00','test','0000-00-00 00:00:00','Active'),(30,'301',3,'Owners Equity','Owner\'s Equity Accounts',0,1,1,2,0,11,'test','1970-01-01 05:00:00','test','0000-00-00 00:00:00','Active');

/*Table structure for table `sa_test_coa_groups` */

DROP TABLE IF EXISTS `sa_test_coa_groups`;

CREATE TABLE `sa_test_coa_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_code` varchar(255) DEFAULT NULL,
  `group_description` varchar(255) DEFAULT NULL,
  `balance_sheet_group` tinyint(1) DEFAULT '1' COMMENT 'Is a Balance Sheet Account',
  `balance_sheet_side` varchar(255) DEFAULT 'Debit' COMMENT 'Debit or Credit',
  `pls_group` tinyint(1) DEFAULT '0' COMMENT 'Is a PLS account',
  `pls_side` varchar(255) DEFAULT 'Expenses' COMMENT 'Expenses or Income',
  `statistics_only` tinyint(1) DEFAULT '0' COMMENT 'NonFinancial Account for statistics only',
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` timestamp NULL DEFAULT NULL,
  `group_status` varchar(255) DEFAULT 'active',
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

/*Data for the table `sa_test_coa_groups` */

insert  into `sa_test_coa_groups`(`group_id`,`group_code`,`group_description`,`balance_sheet_group`,`balance_sheet_side`,`pls_group`,`pls_side`,`statistics_only`,`last_modified_by`,`last_modified_on`,`created_by`,`created_on`,`group_status`) values (1,'1','Asset Accounts',1,'Debit',0,'Expenses',0,NULL,NULL,NULL,NULL,'active'),(2,'2','Liability Accounts',1,'Credit',0,'Expenses',0,NULL,NULL,NULL,NULL,'active'),(3,'3','Equity Accounts',1,'Credit',0,'Expenses',0,NULL,NULL,NULL,NULL,'active'),(4,'4','Revenue Accounts',0,'Credit',1,'Income',0,NULL,NULL,NULL,NULL,'active'),(5,'5','Expense Accounts',0,'Debit',1,'Expense',0,NULL,NULL,NULL,NULL,'active'),(6,'6','Contra Accounts',1,'Credit',0,'Expenses',0,NULL,NULL,NULL,NULL,'active'),(7,'7','Retained Earnings',1,'Credit',0,'Expenses',0,NULL,NULL,NULL,NULL,'active');

/*Table structure for table `sa_test_fiscal_years` */

DROP TABLE IF EXISTS `sa_test_fiscal_years`;

CREATE TABLE `sa_test_fiscal_years` (
  `fiscal_year_id` int(11) NOT NULL AUTO_INCREMENT,
  `fiscal_year_desc` varchar(255) DEFAULT NULL,
  `fiscal_year_start_date` date DEFAULT NULL,
  `fiscal_year_end_date` date DEFAULT NULL,
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `fy_status` varchar(255) DEFAULT 'active',
  PRIMARY KEY (`fiscal_year_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `sa_test_fiscal_years` */

insert  into `sa_test_fiscal_years`(`fiscal_year_id`,`fiscal_year_desc`,`fiscal_year_start_date`,`fiscal_year_end_date`,`last_modified_by`,`last_modified_on`,`created_by`,`created_on`,`fy_status`) values (1,'FY2014-15','2014-06-01','2015-05-31',NULL,'2015-04-23 14:13:42',NULL,NULL,'active'),(2,'FY2015','2015-06-01','2016-05-31',NULL,'2015-04-23 14:14:07',NULL,NULL,'active');

/*Table structure for table `sa_test_general_journal` */

DROP TABLE IF EXISTS `sa_test_general_journal`;

CREATE TABLE `sa_test_general_journal` (
  `entry_id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_date` datetime DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `entry_description` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `reference_no` varchar(255) DEFAULT NULL,
  `debit` decimal(11,2) DEFAULT '0.00',
  `credit` decimal(11,2) DEFAULT '0.00',
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` timestamp NULL DEFAULT NULL,
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `status` varchar(255) DEFAULT 'active',
  PRIMARY KEY (`entry_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `sa_test_general_journal` */

/*Table structure for table `sa_test_journal_voucher_details` */

DROP TABLE IF EXISTS `sa_test_journal_voucher_details`;

CREATE TABLE `sa_test_journal_voucher_details` (
  `voucher_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `voucher_id` int(11) NOT NULL,
  `voucher_date` datetime DEFAULT NULL,
  `account_id` int(11) NOT NULL,
  `entry_description` varchar(255) DEFAULT NULL,
  `debit_amount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `credit_amount` decimal(11,2) DEFAULT '0.00',
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` datetime NOT NULL,
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` timestamp NULL DEFAULT NULL,
  `voucher_detail_status` varchar(255) DEFAULT 'draft',
  PRIMARY KEY (`voucher_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `sa_test_journal_voucher_details` */

/*Table structure for table `sa_test_journal_vouchers` */

DROP TABLE IF EXISTS `sa_test_journal_vouchers`;

CREATE TABLE `sa_test_journal_vouchers` (
  `voucher_id` int(11) NOT NULL AUTO_INCREMENT,
  `voucher_ref_no` varchar(255) NOT NULL,
  `voucher_date` datetime DEFAULT NULL,
  `voucher description` varchar(255) DEFAULT NULL,
  `debits_total` decimal(11,2) NOT NULL DEFAULT '0.00',
  `credits_total` decimal(11,2) NOT NULL DEFAULT '0.00',
  `voucher_tags` varchar(255) DEFAULT NULL,
  `has_attachment` tinyint(1) DEFAULT '0',
  `attachment_url` varchar(255) DEFAULT NULL,
  `voucher_approved_by` varchar(255) DEFAULT NULL,
  `voucher_approved_on` datetime DEFAULT NULL,
  `voucher_approval_comments` varchar(255) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `voucher_status` varchar(255) DEFAULT 'draft',
  PRIMARY KEY (`voucher_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `sa_test_journal_vouchers` */

insert  into `sa_test_journal_vouchers`(`voucher_id`,`voucher_ref_no`,`voucher_date`,`voucher description`,`debits_total`,`credits_total`,`voucher_tags`,`has_attachment`,`attachment_url`,`voucher_approved_by`,`voucher_approved_on`,`voucher_approval_comments`,`created_by`,`created_on`,`last_modified_by`,`last_modified_on`,`voucher_status`) values (1,'JV','2015-04-23 00:00:00','dsfsada','0.00','0.00',NULL,0,NULL,NULL,NULL,NULL,'test','2015-04-26 05:10:48','test','2015-04-26 05:10:48','draft');

/*Table structure for table `sa_test_reporting_periods` */

DROP TABLE IF EXISTS `sa_test_reporting_periods`;

CREATE TABLE `sa_test_reporting_periods` (
  `reporting_period_id` int(11) NOT NULL AUTO_INCREMENT,
  `fiscal_year_id` int(11) DEFAULT NULL,
  `reporting_period_desc` varchar(255) DEFAULT NULL,
  `reporting_period_start_date` date DEFAULT NULL,
  `reporting_period_end_date` date DEFAULT NULL,
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `rp_status` varchar(255) DEFAULT 'active',
  PRIMARY KEY (`reporting_period_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `sa_test_reporting_periods` */

insert  into `sa_test_reporting_periods`(`reporting_period_id`,`fiscal_year_id`,`reporting_period_desc`,`reporting_period_start_date`,`reporting_period_end_date`,`last_modified_by`,`last_modified_on`,`created_by`,`created_on`,`rp_status`) values (1,1,'Q1-2014-15','2014-06-01','2014-08-31',NULL,'2015-04-23 16:34:29',NULL,NULL,'active'),(2,1,'Q2-2014-15','2014-09-01','2014-12-31',NULL,'2015-04-23 16:36:24',NULL,NULL,'active'),(3,1,'Q3-2014-15','2015-01-01','2015-03-31',NULL,'2015-04-23 16:37:19',NULL,NULL,'active'),(4,1,'Q4-2014-15','2015-04-01','2015-06-30',NULL,'2015-04-25 04:59:34',NULL,NULL,'active'),(5,2,NULL,NULL,'2015-08-31',NULL,'2015-04-25 05:02:14',NULL,NULL,'active');

/*Table structure for table `sa_test_transactions` */

DROP TABLE IF EXISTS `sa_test_transactions`;

CREATE TABLE `sa_test_transactions` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `account_id` int(11) NOT NULL,
  `account_code` varchar(255) NOT NULL,
  `currency` varchar(3) NOT NULL,
  `debit` double(11,2) NOT NULL DEFAULT '0.00',
  `credit` double(11,2) NOT NULL DEFAULT '0.00',
  `reference` varchar(255) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `appproved_by` varchar(255) DEFAULT NULL,
  `approved_on` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT 'CURRENT_TIMESTAMP',
  `created_on` datetime DEFAULT NULL,
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `sa_test_transactions` */

/*Table structure for table `sa_test_users` */

DROP TABLE IF EXISTS `sa_test_users`;

CREATE TABLE `sa_test_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(24) NOT NULL,
  `user_email` varchar(50) NOT NULL,
  `first_name` varchar(24) NOT NULL,
  `last_name` varchar(24) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `auth_code` varchar(30) DEFAULT NULL,
  `user_avatar_url` varchar(255) DEFAULT NULL,
  `user_nic` varchar(255) DEFAULT NULL,
  `user_title` varchar(255) DEFAULT NULL,
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` timestamp NULL DEFAULT NULL,
  `user_status` varchar(255) DEFAULT 'active',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `sa_test_users` */

insert  into `sa_test_users`(`user_id`,`user_name`,`user_email`,`first_name`,`last_name`,`password`,`role_id`,`company_id`,`auth_code`,`user_avatar_url`,`user_nic`,`user_title`,`last_modified_by`,`last_modified_on`,`created_by`,`created_on`,`user_status`) values (1,'test','test@test.com','test','tester','test',1,1,'778899',NULL,NULL,'Mr.','system','2015-03-23 19:14:45','system',NULL,'active');

/*Table structure for table `sa_test_voucher_expense` */

DROP TABLE IF EXISTS `sa_test_voucher_expense`;

CREATE TABLE `sa_test_voucher_expense` (
  `voucher_id` int(11) NOT NULL AUTO_INCREMENT,
  `voucher_ref_no` varchar(255) DEFAULT NULL,
  `voucher_date` datetime DEFAULT NULL,
  `voucher description` varchar(255) DEFAULT NULL,
  `paid_from_account` int(11) DEFAULT NULL,
  `voucher_total` decimal(11,2) DEFAULT '0.00',
  `voucher_tags` varchar(255) DEFAULT NULL,
  `has_attachment` tinyint(1) DEFAULT '0',
  `voucher_attachment_url` varchar(255) DEFAULT NULL,
  `voucher_approved_by` varchar(255) DEFAULT NULL,
  `voucher_approved_on` datetime DEFAULT NULL,
  `voucher_approval_comments` varchar(255) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `voucher_status` varchar(255) DEFAULT 'draft',
  PRIMARY KEY (`voucher_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

/*Data for the table `sa_test_voucher_expense` */

insert  into `sa_test_voucher_expense`(`voucher_id`,`voucher_ref_no`,`voucher_date`,`voucher description`,`paid_from_account`,`voucher_total`,`voucher_tags`,`has_attachment`,`voucher_attachment_url`,`voucher_approved_by`,`voucher_approved_on`,`voucher_approval_comments`,`created_by`,`created_on`,`last_modified_by`,`last_modified_on`,`voucher_status`) values (1,'ACD345','1970-01-01 05:00:00',' Testing Voucher',1,'2500.00',NULL,0,NULL,NULL,NULL,NULL,NULL,'0000-00-00 00:00:00',NULL,NULL,'draft'),(2,'ACD345','1970-01-01 05:00:00',' Testing Voucher',1,'0.00',NULL,0,NULL,NULL,NULL,NULL,NULL,'0000-00-00 00:00:00',NULL,NULL,'draft'),(3,'ACD345','1970-01-01 05:00:00',' Testing Voucher',1,'0.00',NULL,0,NULL,NULL,NULL,NULL,NULL,'0000-00-00 00:00:00',NULL,NULL,'draft'),(4,'ACD345','2015-02-04 00:00:00','45567',1,'0.00',NULL,0,NULL,NULL,NULL,NULL,'admin','2015-04-07 12:30:37',NULL,NULL,'draft'),(5,'ACD345','2015-04-18 00:41:51','cvx',1,'0.00',NULL,0,NULL,NULL,NULL,NULL,'admin','2015-04-18 00:41:51',NULL,NULL,'draft'),(6,'BDD$','2015-04-22 00:00:00','fdsf',NULL,'0.00',NULL,0,NULL,NULL,NULL,NULL,'test','2015-04-26 05:03:28','test',NULL,'draft'),(7,'BDD4','2015-04-22 00:00:00','fdsf',NULL,'0.00',NULL,0,NULL,NULL,NULL,NULL,'test','2015-04-26 05:04:01','test',NULL,'draft'),(8,'BDD6','2015-04-22 00:00:00','fdsf',NULL,'0.00',NULL,0,NULL,NULL,NULL,NULL,'test','2015-04-26 05:05:51','test',NULL,'draft'),(9,'BDdfsd','2015-04-22 00:00:00','fdsf',NULL,'0.00',NULL,0,NULL,NULL,NULL,NULL,'test','2015-04-26 05:06:49','test',NULL,'draft'),(10,'first voucher','2015-04-22 00:00:00','fdsf',NULL,'0.00',NULL,0,NULL,NULL,NULL,NULL,'test','2015-04-26 05:08:04','test',NULL,'draft');

/*Table structure for table `sa_test_voucher_expense_detail` */

DROP TABLE IF EXISTS `sa_test_voucher_expense_detail`;

CREATE TABLE `sa_test_voucher_expense_detail` (
  `voucher_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `voucher_id` int(11) NOT NULL,
  `expense_date` datetime DEFAULT NULL,
  `expense_account_id` int(11) NOT NULL,
  `expense_description` varchar(255) DEFAULT NULL,
  `expense_amount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` datetime NOT NULL,
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` timestamp NULL DEFAULT NULL,
  `voucher_detail_status` varchar(255) DEFAULT 'draft',
  PRIMARY KEY (`voucher_detail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `sa_test_voucher_expense_detail` */

insert  into `sa_test_voucher_expense_detail`(`voucher_detail_id`,`voucher_id`,`expense_date`,`expense_account_id`,`expense_description`,`expense_amount`,`created_by`,`created_on`,`last_modified_by`,`last_modified_on`,`voucher_detail_status`) values (1,1,NULL,0,'Test Expenst Type','2500.00',NULL,'2015-04-03 16:20:23',NULL,NULL,'draft');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
