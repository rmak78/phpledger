/*
SQLyog Ultimate v10.00 Beta1
MySQL - 5.5.27-log : Database - sutlejacct
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
  `coa_level_1_length` int(1) DEFAULT '1',
  `coa_level_2_length` int(1) DEFAULT '2',
  `coa_level_3_length` int(1) DEFAULT '2',
  `coa_level_4_length` int(1) DEFAULT '3',
  `coa_level_5_length` int(1) DEFAULT '5',
  `coa_level_6_length` int(1) DEFAULT '5',
  `coa_level_7_length` int(1) DEFAULT '5',
  `coa_level_8_length` int(1) DEFAULT '5',
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `sa_companies` */

insert  into `sa_companies`(`company_id`,`company_name`,`company_db_prefix`,`company_address_1`,`company_address_2`,`city`,`country`,`currency`,`phone_1`,`phone_2`,`email`,`website`,`industry`,`company_time_zone`,`coa_levels`,`coa_level_1_length`,`coa_level_2_length`,`coa_level_3_length`,`coa_level_4_length`,`coa_level_5_length`,`coa_level_6_length`,`coa_level_7_length`,`coa_level_8_length`,`coa_level_9_length`,`company_logo_home`,`company_logo_head`,`company_logo_icon`,`super_admin_user`,`super_admin_password`,`last_modified_by`,`last_modified_on`,`created_by`,`created_on`,`company_status`) values (1,'Sutlej Solutions','test_','Al-Sadeeq Akbar','Bahawalpur Road,','Lodhran','Pakistan','PKR','365665555','+92-333-341-4999','mansoor@sutlej.net','http://sutlej.net','IT','GMT',4,1,2,2,3,5,5,5,5,5,'http://www.sky-valley-web-design.ca/images/400x100.gif','http://www.sky-valley-web-design.ca/images/250x150.gif','http://www.sky-valley-web-design.ca/images/100x100.gif','admin','admin','admin','2015-04-04 03:24:43','system','2015-04-04 03:24:43','active'),(2,'','','','','','','','','','','','','GMT',4,1,2,2,3,5,5,5,5,5,'','','','','',NULL,NULL,NULL,'2015-03-31 18:48:59','1');

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
  `has_parent` tinyint(1) DEFAULT '1',
  `parent_account_id` int(11) DEFAULT '0',
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` timestamp NULL DEFAULT NULL,
  `account_status` varchar(255) DEFAULT 'active',
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `sa_test_coa` */

/*Table structure for table `sa_test_coa_groups` */

DROP TABLE IF EXISTS `sa_test_coa_groups`;

CREATE TABLE `sa_test_coa_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_code` varchar(255) DEFAULT NULL,
  `group_description` varchar(255) DEFAULT NULL,
  `from_account_code` varchar(255) DEFAULT NULL,
  `to_account_code` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `sa_test_coa_groups` */

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
  `petty_cash_account` int(11) DEFAULT NULL,
  `voucher_total` decimal(11,2) DEFAULT '0.00',
  `voucher_tags` varchar(255) DEFAULT NULL,
  `voucher_approved_by` varchar(255) DEFAULT NULL,
  `voucher_approved_on` datetime DEFAULT NULL,
  `voucher_approval_comments` varchar(255) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `voucher_status` varchar(255) DEFAULT 'draft',
  PRIMARY KEY (`voucher_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `sa_test_voucher_expense` */

insert  into `sa_test_voucher_expense`(`voucher_id`,`voucher_ref_no`,`voucher_date`,`voucher description`,`petty_cash_account`,`voucher_total`,`voucher_tags`,`voucher_approved_by`,`voucher_approved_on`,`voucher_approval_comments`,`created_by`,`created_on`,`last_modified_by`,`last_modified_on`,`voucher_status`) values (1,'ACD345','1970-01-01 05:00:00',' Testing Voucher',1,'2500.00',NULL,NULL,NULL,NULL,NULL,'0000-00-00 00:00:00',NULL,NULL,'draft'),(2,'ACD345','1970-01-01 05:00:00',' Testing Voucher',1,'0.00',NULL,NULL,NULL,NULL,NULL,'0000-00-00 00:00:00',NULL,NULL,'draft'),(3,'ACD345','1970-01-01 05:00:00',' Testing Voucher',1,'0.00',NULL,NULL,NULL,NULL,NULL,'0000-00-00 00:00:00',NULL,NULL,'draft'),(4,'ACD345','2015-02-04 00:00:00','45567',1,'0.00',NULL,NULL,NULL,NULL,'admin','2015-04-07 12:30:37',NULL,NULL,'draft');

/*Table structure for table `sa_test_voucher_expense_detail` */

DROP TABLE IF EXISTS `sa_test_voucher_expense_detail`;

CREATE TABLE `sa_test_voucher_expense_detail` (
  `voucher_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `voucher_id` int(11) NOT NULL,
  `expense_account_id` int(11) NOT NULL,
  `expense_description` varchar(255) DEFAULT NULL,
  `expense_amount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `has_attachment` tinyint(1) NOT NULL DEFAULT '0',
  `expense_attachment` longblob,
  `expense_attachment_description` varchar(255) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` date DEFAULT NULL,
  `voucher_detail_status` varchar(255) DEFAULT 'draft',
  PRIMARY KEY (`voucher_detail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `sa_test_voucher_expense_detail` */

insert  into `sa_test_voucher_expense_detail`(`voucher_detail_id`,`voucher_id`,`expense_account_id`,`expense_description`,`expense_amount`,`has_attachment`,`expense_attachment`,`expense_attachment_description`,`created_by`,`created_on`,`last_modified_by`,`last_modified_on`,`voucher_detail_status`) values (1,1,0,'Test Expenst Type','2500.00',0,NULL,NULL,NULL,'2015-04-03 16:20:23',NULL,NULL,'draft');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
