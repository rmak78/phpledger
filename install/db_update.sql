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
  `coa_code_length` int(11) DEFAULT '10' COMMENT 'Chart of Account Code length',
  `company_logo_home` varchar(255) DEFAULT NULL,
  `company_logo_head` varchar(255) DEFAULT NULL,
  `company_logo_icon` varchar(255) DEFAULT NULL,
  `super_admin_user` varchar(255) DEFAULT NULL,
  `super_admin_password` varchar(255) DEFAULT NULL,
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `company_status` varchar(100) DEFAULT 'active',
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `sa_companies` */

insert  into `sa_companies`(`company_id`,`company_name`,`company_db_prefix`,`company_address_1`,`company_address_2`,`city`,`country`,`currency`,`phone_1`,`phone_2`,`email`,`website`,`industry`,`coa_code_length`,`company_logo_home`,`company_logo_head`,`company_logo_icon`,`super_admin_user`,`super_admin_password`,`last_modified_by`,`last_modified_on`,`created_by`,`created_on`,`company_status`) values (1,'Test Company Name','test_','Al-Sadeeq Akbar,','Bahawalpur Road,','Lodhran','Pakistan',NULL,NULL,NULL,'mansoor@sutlej.net','http://sutlej.net','IT',10,NULL,NULL,NULL,'demo','demo','system','2015-03-10 19:12:51','system','2015-03-23 22:41:44','active'),(2,'','','','','Mumbai','','','','','','','',0,'','','','','',NULL,NULL,NULL,'0000-00-00 00:00:00','1'),(3,'','','','','-Select-','Pakistan','','','','','','',0,'','','','','',NULL,NULL,NULL,'0000-00-00 00:00:00','1');

/*Table structure for table `sa_sys_config` */

DROP TABLE IF EXISTS `sa_sys_config`;

CREATE TABLE `sa_sys_config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `sa_sys_config` */

insert  into `sa_sys_config`(`config_id`,`key`,`value`,`last_modified_by`,`last_modified_on`,`created_by`,`created_on`) values (1,'super_user_id','mansoor','system','2015-03-23 19:16:39','system','2015-03-23 19:17:28'),(2,'super_user_pass','pakistan','system','2015-03-23 19:16:39','system','2015-03-23 19:17:29'),(3,'default_company_id','1','system','2015-03-23 19:16:39','system','2015-03-23 19:16:46'),(4,'default_company_db_prefix','test','system','2015-03-23 19:16:39','system','2015-03-23 19:17:34');

/*Table structure for table `sa_test_coa` */

DROP TABLE IF EXISTS `sa_test_coa`;

CREATE TABLE `sa_test_coa` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_code` varchar(255) DEFAULT NULL,
  `account_group` int(11) DEFAULT NULL,
  `account_desc_short` varchar(255) DEFAULT NULL,
  `account_desc_long` varchar(255) DEFAULT NULL,
  `parent_account_id` int(11) DEFAULT '0',
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` timestamp NULL DEFAULT NULL,
  `account_status` varchar(255) DEFAULT 'active',
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `sa_test_coa` */

insert  into `sa_test_coa`(`account_id`,`account_code`,`account_group`,`account_desc_short`,`account_desc_long`,`parent_account_id`,`last_modified_by`,`last_modified_on`,`created_by`,`created_on`,`account_status`) values (1,'1200001',1,'Cash','Cash in Hand',0,'mansoor','2015-03-23 23:10:56','mansoor',NULL,'active');

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `sa_test_coa_groups` */

insert  into `sa_test_coa_groups`(`group_id`,`group_code`,`group_description`,`from_account_code`,`to_account_code`,`balance_sheet_group`,`balance_sheet_side`,`pls_group`,`pls_side`,`statistics_only`,`last_modified_by`,`last_modified_on`,`created_by`,`created_on`,`group_status`) values (1,'ASST','Assets','4500001','4599999',1,'Debit',0,'Expenses',0,'mansoor','2015-03-22 22:33:07','mansoor',NULL,'active');

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

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
