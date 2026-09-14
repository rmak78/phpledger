-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2015 at 09:03 PM
-- Server version: 5.5.39
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `phpledger`
--

-- --------------------------------------------------------

--
-- Table structure for table `sa_companies`
--

CREATE TABLE IF NOT EXISTS `sa_companies` (
`company_id` int(11) NOT NULL,
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
  `company_status` varchar(100) DEFAULT 'active'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `sa_companies`
--

INSERT INTO `sa_companies` (`company_id`, `company_name`, `company_db_prefix`, `company_address_1`, `company_address_2`, `city`, `country`, `currency`, `phone_1`, `phone_2`, `email`, `website`, `industry`, `company_time_zone`, `coa_levels`, `coa_level_1_label`, `coa_level_1_length`, `coa_level_2_label`, `coa_level_2_length`, `coa)level_3_label`, `coa_level_3_length`, `coa_level_4_label`, `coa_level_4_length`, `coa_level_5_label`, `coa_level_5_length`, `coa_level_6_label`, `coa_level_6_length`, `coa_level_7_label`, `coa_level_7_length`, `coa_level_8_label`, `coa_level_8_length`, `coa_level_9_label`, `coa_level_9_length`, `company_logo_home`, `company_logo_head`, `company_logo_icon`, `super_admin_user`, `super_admin_password`, `last_modified_by`, `last_modified_on`, `created_by`, `created_on`, `company_status`) VALUES
(1, 'Sutlej Solutions', 'test_', 'Al-Sadeeq Akbar', 'Bahawalpur Road,', 'Lodhran', 'Pakistan', 'PKR', '365665555', '+92-333-341-4999', 'mansoor@sutlej.net', 'http://sutlej.net', 'IT', 'GMT', 5, 'company', 1, 'Main', 2, 'control', 2, 'sub-control', 4, 'Activity', 5, NULL, 0, NULL, 0, NULL, 0, NULL, 0, 'http://www.sky-valley-web-design.ca/images/250x400.gif', 'http://www.sky-valley-web-design.ca/images/250x150.gif', 'http://www.sky-valley-web-design.ca/images/100x100.gif', 'admin', 'admin', 'admin', '2015-04-18 00:41:07', 'system', '2015-04-03 22:24:43', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `sa_sys_config`
--

CREATE TABLE IF NOT EXISTS `sa_sys_config` (
`config_id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `key_label` varchar(255) DEFAULT NULL,
  `key_help_text` varchar(255) DEFAULT NULL,
  `value` varchar(255) NOT NULL,
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `sa_sys_config`
--

INSERT INTO `sa_sys_config` (`config_id`, `key`, `key_label`, `key_help_text`, `value`, `last_modified_by`, `last_modified_on`, `created_by`, `created_on`) VALUES
(1, 'super_user_id', NULL, NULL, 'mansoor', 'system', '2015-03-23 19:16:39', 'system', '2015-03-23 14:17:28'),
(2, 'super_user_pass', NULL, NULL, 'pakistan', 'system', '2015-03-23 19:16:39', 'system', '2015-03-23 14:17:29'),
(3, 'default_company_id', NULL, NULL, '1', 'system', '2015-03-23 19:16:39', 'system', '2015-03-23 14:16:46'),
(4, 'default_company_db_prefix', NULL, NULL, 'test', 'system', '2015-03-23 19:16:39', 'system', '2015-03-23 14:17:34'),
(5, 'default_time_zone', NULL, NULL, 'GMT', 'system', '2015-03-23 19:16:39', 'system', '2015-03-31 13:45:46');

-- --------------------------------------------------------

--
-- Table structure for table `sa_test_coa`
--

CREATE TABLE IF NOT EXISTS `sa_test_coa` (
`account_id` int(11) NOT NULL,
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
  `account_status` varchar(255) DEFAULT 'active'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `sa_test_coa`
--

INSERT INTO `sa_test_coa` (`account_id`, `account_code`, `account_group`, `account_desc_short`, `account_desc_long`, `activity_account`, `consolidate_only`, `has_parent`, `coa_level`, `has_children`, `parent_account_id`, `last_modified_by`, `last_modified_on`, `created_by`, `created_on`, `account_status`) VALUES
(1, '1', 1, 'Assets', 'Assets Group', 0, 1, 0, 1, 1, 0, NULL, NULL, NULL, NULL, 'active'),
(2, '101', 1, 'Current Assets', 'Current Assets', 0, 1, 1, 2, 1, 1, NULL, NULL, NULL, NULL, 'active'),
(3, '102', 1, 'Fixed Assets', 'Fixed Assets', 0, 1, 1, 2, 1, 1, NULL, NULL, NULL, NULL, 'active'),
(5, '10101', 1, 'Cash', 'Available Cash ', 0, 1, 1, 3, 1, 2, NULL, NULL, NULL, NULL, 'active'),
(10, '2', 2, 'Liabilities', 'Liabilities ', 0, 1, 0, 1, 0, 0, 'test', '1970-01-01 05:00:00', 'test', '0000-00-00 00:00:00', 'Active'),
(11, '3', 3, 'Equity', 'Owner''s Equity', 0, 1, 0, 1, 1, 0, 'test', '1970-01-01 05:00:00', 'test', '0000-00-00 00:00:00', 'Active'),
(12, '4', 4, 'Income', 'Income', 0, 1, 0, 1, 1, 0, 'test', '1970-01-01 05:00:00', 'test', '0000-00-00 00:00:00', 'Active'),
(13, '5', 5, 'Expenses', 'Expenses', 0, 1, 0, 1, 0, 0, 'test', '1970-01-01 05:00:00', 'test', '0000-00-00 00:00:00', 'Active'),
(14, '6', 6, 'Adjustments', 'Adjustments', 0, 1, 0, 1, 0, 0, 'test', '1970-01-01 05:00:00', 'test', '0000-00-00 00:00:00', 'Active'),
(15, '7', 7, 'Retained Earnings', 'Retained Earnings', 0, 1, 0, 1, 0, 0, 'test', '1970-01-01 05:00:00', 'test', '0000-00-00 00:00:00', 'Active'),
(17, '101010001', 1, 'Petty Cash', 'Petty Cash Accounts', 0, 1, 1, 4, 1, 5, 'test', '1970-01-01 05:00:00', 'test', '0000-00-00 00:00:00', 'Active'),
(18, '10101000100001', 1, 'Petty Cash - Mansoor', 'Petty Cash with Mansoor', 1, 0, 1, 5, 0, 17, 'test', '1970-01-01 05:00:00', 'test', '0000-00-00 00:00:00', 'Active'),
(19, '10101000100002', 1, 'Petty Cash - Kafia', 'Petty Cash with Kafia', 1, 0, 1, 5, 0, 17, 'test', '1970-01-01 05:00:00', 'test', '0000-00-00 00:00:00', 'Active'),
(20, '101010002', 1, 'Bank Accounts', 'Cash in Bank Accounts', 0, 1, 1, 4, 1, 5, 'test', '1970-01-01 05:00:00', 'test', '0000-00-00 00:00:00', 'Active'),
(21, '10101000200001', 1, 'Meezan Bank (PKR)', 'PKR Bank Account in Meezan Bank', 1, 0, 1, 5, 0, 20, 'test', '1970-01-01 05:00:00', 'test', '0000-00-00 00:00:00', 'Active'),
(22, '10101000200002', 1, 'Meezan Bank (USD)', 'USD Forex Account in Meezan Bank', 1, 0, 1, 5, 0, 20, 'test', '1970-01-01 05:00:00', 'test', '0000-00-00 00:00:00', 'Active'),
(23, '10103', 1, 'Accounts Recieveable', 'Accounts Recieveables from Third Parties', 0, 1, 1, 3, 0, 2, 'test', '1970-01-01 05:00:00', 'test', '0000-00-00 00:00:00', 'Active'),
(24, '10102', 1, 'Investments', 'Investments ', 0, 1, 1, 3, 0, 2, 'test', '1970-01-01 05:00:00', 'test', '0000-00-00 00:00:00', 'Active'),
(25, '10104', 1, 'Inventory', 'Product inventory/ Supplies', 0, 1, 1, 3, 0, 2, 'test', '1970-01-01 05:00:00', 'test', '0000-00-00 00:00:00', 'Active'),
(26, '10105', 1, 'Pre-Paid Expenses', 'Pre-Paid Expenses that have yet to be consumed', 0, 1, 1, 3, 0, 2, 'test', '1970-01-01 05:00:00', 'test', '0000-00-00 00:00:00', 'Active'),
(27, '10201', 1, 'Land', 'Land ownership', 0, 1, 1, 3, 0, 3, 'test', '1970-01-01 05:00:00', 'test', '0000-00-00 00:00:00', 'Active'),
(28, '10202', 1, 'Buildings', 'Buildings Owned', 0, 1, 1, 3, 0, 3, 'test', '1970-01-01 05:00:00', 'test', '0000-00-00 00:00:00', 'Active'),
(29, '401', 4, 'Freelancing Sites', 'Income from Freelancing Site', 0, 1, 1, 2, 0, 12, 'test', '1970-01-01 05:00:00', 'test', '0000-00-00 00:00:00', 'Active'),
(30, '301', 3, 'Owners Equity', 'Owner''s Equity Accounts', 0, 1, 1, 2, 0, 11, 'test', '1970-01-01 05:00:00', 'test', '0000-00-00 00:00:00', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `sa_test_coa_groups`
--

CREATE TABLE IF NOT EXISTS `sa_test_coa_groups` (
`group_id` int(11) NOT NULL,
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
  `group_status` varchar(255) DEFAULT 'active'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `sa_test_coa_groups`
--

INSERT INTO `sa_test_coa_groups` (`group_id`, `group_code`, `group_description`, `balance_sheet_group`, `balance_sheet_side`, `pls_group`, `pls_side`, `statistics_only`, `last_modified_by`, `last_modified_on`, `created_by`, `created_on`, `group_status`) VALUES
(1, '1', 'Asset Accounts', 1, 'Debit', 0, 'Expenses', 0, NULL, NULL, NULL, NULL, 'active'),
(2, '2', 'Liability Accounts', 1, 'Credit', 0, 'Expenses', 0, NULL, NULL, NULL, NULL, 'active'),
(3, '3', 'Equity Accounts', 1, 'Credit', 0, 'Expenses', 0, NULL, NULL, NULL, NULL, 'active'),
(4, '4', 'Revenue Accounts', 0, 'Credit', 1, 'Income', 0, NULL, NULL, NULL, NULL, 'active'),
(5, '5', 'Expense Accounts', 0, 'Debit', 1, 'Expense', 0, NULL, NULL, NULL, NULL, 'active'),
(6, '6', 'Contra Accounts', 1, 'Credit', 0, 'Expenses', 0, NULL, NULL, NULL, NULL, 'active'),
(7, '7', 'Retained Earnings', 1, 'Credit', 0, 'Expenses', 0, NULL, NULL, NULL, NULL, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `sa_test_fiscal_years`
--

CREATE TABLE IF NOT EXISTS `sa_test_fiscal_years` (
`fiscal_year_id` int(11) NOT NULL,
  `fiscal_year_desc` varchar(255) DEFAULT NULL,
  `fiscal_year_start_date` date DEFAULT NULL,
  `fiscal_year_end_date` date DEFAULT NULL,
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `fy_status` varchar(255) DEFAULT 'active'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `sa_test_fiscal_years`
--

INSERT INTO `sa_test_fiscal_years` (`fiscal_year_id`, `fiscal_year_desc`, `fiscal_year_start_date`, `fiscal_year_end_date`, `last_modified_by`, `last_modified_on`, `created_by`, `created_on`, `fy_status`) VALUES
(1, 'FY2014-15', '2014-06-01', '2015-05-31', NULL, '2015-04-23 09:13:42', NULL, NULL, 'active'),
(2, 'FY2015', '2015-06-01', '2016-05-31', NULL, '2015-04-23 09:14:07', NULL, NULL, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `sa_test_general_journal`
--

CREATE TABLE IF NOT EXISTS `sa_test_general_journal` (
`entry_id` int(11) NOT NULL,
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
  `status` varchar(255) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sa_test_journal_vouchers`
--

CREATE TABLE IF NOT EXISTS `sa_test_journal_vouchers` (
`voucher_id` int(11) NOT NULL,
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
  `voucher_status` varchar(255) DEFAULT 'draft'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `sa_test_journal_vouchers`
--

INSERT INTO `sa_test_journal_vouchers` (`voucher_id`, `voucher_ref_no`, `voucher_date`, `voucher description`, `debits_total`, `credits_total`, `voucher_tags`, `has_attachment`, `attachment_url`, `voucher_approved_by`, `voucher_approved_on`, `voucher_approval_comments`, `created_by`, `created_on`, `last_modified_by`, `last_modified_on`, `voucher_status`) VALUES
(1, 'JV', '2015-04-23 00:00:00', 'dsfsada', '0.00', '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-04-26 05:10:48', 'test', '2015-04-26 00:10:48', 'draft'),
(4, '123', '2015-11-30 00:00:00', 'tp', '0.00', '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-11-30 17:53:15', 'test', '2015-11-30 16:53:15', 'draft'),
(5, '1233', '2015-12-02 00:00:00', 'xxxc', '0.00', '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-12-02 14:30:26', 'test', '2015-12-02 13:30:26', 'draft'),
(6, '567', '2015-12-02 00:00:00', 'test', '0.00', '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-12-02 17:59:59', 'test', '2015-12-02 16:59:59', 'draft');

-- --------------------------------------------------------

--
-- Table structure for table `sa_test_journal_voucher_details`
--

CREATE TABLE IF NOT EXISTS `sa_test_journal_voucher_details` (
`voucher_detail_id` int(11) NOT NULL,
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
  `voucher_detail_status` varchar(255) DEFAULT 'draft'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `sa_test_journal_voucher_details`
--

INSERT INTO `sa_test_journal_voucher_details` (`voucher_detail_id`, `voucher_id`, `voucher_date`, `account_id`, `entry_description`, `debit_amount`, `credit_amount`, `created_by`, `created_on`, `last_modified_by`, `last_modified_on`, `voucher_detail_status`) VALUES
(1, 2, '2015-11-28 00:00:00', 13, ' Mob load', '12.00', '12.00', 'test', '2015-11-28 16:05:50', NULL, NULL, 'Draft'),
(2, 2, '2015-11-28 00:00:00', 13, ' travelling expense', '20.00', '20.00', 'test', '2015-11-28 16:09:07', NULL, NULL, 'Draft'),
(3, 5, '2015-12-02 00:00:00', 1, ' vvv', '123.00', '12.00', 'test', '2015-12-02 14:31:07', NULL, NULL, 'Draft');

-- --------------------------------------------------------

--
-- Table structure for table `sa_test_reporting_periods`
--

CREATE TABLE IF NOT EXISTS `sa_test_reporting_periods` (
`reporting_period_id` int(11) NOT NULL,
  `fiscal_year_id` int(11) DEFAULT NULL,
  `reporting_period_desc` varchar(255) DEFAULT NULL,
  `reporting_period_start_date` date DEFAULT NULL,
  `reporting_period_end_date` date DEFAULT NULL,
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `rp_status` varchar(255) DEFAULT 'active'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `sa_test_reporting_periods`
--

INSERT INTO `sa_test_reporting_periods` (`reporting_period_id`, `fiscal_year_id`, `reporting_period_desc`, `reporting_period_start_date`, `reporting_period_end_date`, `last_modified_by`, `last_modified_on`, `created_by`, `created_on`, `rp_status`) VALUES
(1, 1, 'Q1-2014-15', '2014-06-01', '2014-08-31', NULL, '2015-04-23 11:34:29', NULL, NULL, 'active'),
(2, 1, 'Q2-2014-15', '2014-09-01', '2014-12-31', NULL, '2015-04-23 11:36:24', NULL, NULL, 'active'),
(3, 1, 'Q3-2014-15', '2015-01-01', '2015-03-31', NULL, '2015-04-23 11:37:19', NULL, NULL, 'active'),
(4, 1, 'Q4-2014-15', '2015-04-01', '2015-06-30', NULL, '2015-04-24 23:59:34', NULL, NULL, 'active'),
(5, 2, NULL, NULL, '2015-08-31', NULL, '2015-04-25 00:02:14', NULL, NULL, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `sa_test_transactions`
--

CREATE TABLE IF NOT EXISTS `sa_test_transactions` (
`transaction_id` int(11) NOT NULL,
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
  `status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sa_test_users`
--

CREATE TABLE IF NOT EXISTS `sa_test_users` (
`user_id` int(11) NOT NULL,
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
  `user_status` varchar(255) DEFAULT 'active'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `sa_test_users`
--

INSERT INTO `sa_test_users` (`user_id`, `user_name`, `user_email`, `first_name`, `last_name`, `password`, `role_id`, `company_id`, `auth_code`, `user_avatar_url`, `user_nic`, `user_title`, `last_modified_by`, `last_modified_on`, `created_by`, `created_on`, `user_status`) VALUES
(1, 'test', 'test@test.com', 'test', 'tester', 'test', 1, 1, '778899', NULL, NULL, 'Mr.', 'system', '2015-03-23 19:14:45', 'system', NULL, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `sa_test_voucher_expense`
--

CREATE TABLE IF NOT EXISTS `sa_test_voucher_expense` (
`voucher_id` int(11) NOT NULL,
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
  `voucher_status` varchar(255) DEFAULT 'draft'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `sa_test_voucher_expense`
--

INSERT INTO `sa_test_voucher_expense` (`voucher_id`, `voucher_ref_no`, `voucher_date`, `voucher description`, `paid_from_account`, `voucher_total`, `voucher_tags`, `has_attachment`, `voucher_attachment_url`, `voucher_approved_by`, `voucher_approved_on`, `voucher_approval_comments`, `created_by`, `created_on`, `last_modified_by`, `last_modified_on`, `voucher_status`) VALUES
(1, 'ACD345', '1970-01-01 05:00:00', ' Testing Voucher', 1, '2500.00', NULL, 0, NULL, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', NULL, NULL, 'draft'),
(2, 'ACD345', '1970-01-01 05:00:00', ' Testing Voucher', 1, '0.00', NULL, 0, NULL, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', NULL, NULL, 'draft'),
(3, 'ACD345', '1970-01-01 05:00:00', ' Testing Voucher', 1, '0.00', NULL, 0, NULL, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', NULL, NULL, 'draft'),
(4, 'ACD345', '2015-02-04 00:00:00', '45567', 1, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'admin', '2015-04-07 12:30:37', NULL, NULL, 'draft'),
(5, 'ACD345', '2015-04-18 00:41:51', 'cvx', 1, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'admin', '2015-04-18 00:41:51', NULL, NULL, 'draft'),
(6, 'BDD$', '2015-04-22 00:00:00', 'fdsf', NULL, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-04-26 05:03:28', 'test', NULL, 'draft'),
(7, 'BDD4', '2015-04-22 00:00:00', 'fdsf', NULL, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-04-26 05:04:01', 'test', NULL, 'draft'),
(8, 'BDD6', '2015-04-22 00:00:00', 'fdsf', NULL, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-04-26 05:05:51', 'test', NULL, 'draft'),
(9, 'BDdfsd', '2015-04-22 00:00:00', 'fdsf', NULL, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-04-26 05:06:49', 'test', NULL, 'draft'),
(10, 'first voucher', '2015-04-22 00:00:00', 'fdsf', NULL, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-04-26 05:08:04', 'test', NULL, 'draft'),
(11, '12', '0000-00-00 00:00:00', '18', 0, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-12-03 16:43:33', NULL, NULL, 'Draft'),
(12, '13', '0000-00-00 00:00:00', 'it is also test', 18, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-12-03 16:59:02', NULL, NULL, 'Draft'),
(13, '12345', '0000-00-00 00:00:00', 'its 2nd test', 18, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-12-03 19:21:23', NULL, NULL, 'Draft'),
(14, '112233', '0000-00-00 00:00:00', 'its 3rd test', 18, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-12-03 19:25:37', NULL, NULL, 'Draft'),
(15, '99', '0000-00-00 00:00:00', 'jghgh', 18, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-12-03 19:27:31', NULL, NULL, 'Draft'),
(16, '77', '0000-00-00 00:00:00', 'nnn', 18, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-12-03 19:34:03', NULL, NULL, 'Draft'),
(17, '5677', '0000-00-00 00:00:00', 'jkhkjnkjn', 18, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-12-03 19:44:36', NULL, NULL, 'Draft'),
(18, '00999', '0000-00-00 00:00:00', 'nnnn', 18, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-12-03 19:52:17', NULL, NULL, 'Draft'),
(19, '5655', '0000-00-00 00:00:00', 'waseem', 18, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-12-03 20:26:47', NULL, NULL, 'Draft'),
(20, '789', '0000-00-00 00:00:00', 'shahzad', 18, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-12-03 20:29:37', NULL, NULL, 'Draft'),
(21, 'BDD4n', '0000-00-00 00:00:00', 'nm nm ', 18, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-12-03 20:38:25', NULL, NULL, 'Draft'),
(22, 'BDD4nn', '0000-00-00 00:00:00', 'b bn', 18, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-12-03 20:44:09', NULL, NULL, 'Draft'),
(23, 'b67', '0000-00-00 00:00:00', 'jkjk', 18, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-12-03 20:46:21', NULL, NULL, 'Draft'),
(24, '12ac', '0000-00-00 00:00:00', 'bjbvjf', 18, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-12-03 20:56:31', NULL, NULL, 'Draft'),
(25, '123hh', '0000-00-00 00:00:00', 'erhh', 18, '0.00', NULL, 0, NULL, NULL, NULL, NULL, 'test', '2015-12-03 20:57:21', NULL, NULL, 'Draft');

-- --------------------------------------------------------

--
-- Table structure for table `sa_test_voucher_expense_detail`
--

CREATE TABLE IF NOT EXISTS `sa_test_voucher_expense_detail` (
`voucher_detail_id` int(11) NOT NULL,
  `voucher_id` int(11) NOT NULL,
  `expense_date` datetime DEFAULT NULL,
  `expense_account_id` int(11) NOT NULL,
  `expense_type` varchar(100) NOT NULL,
  `expense_description` varchar(255) DEFAULT NULL,
  `expense_amount` decimal(11,2) NOT NULL DEFAULT '0.00',
  `has_attachment` varchar(100) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_on` datetime NOT NULL,
  `last_modified_by` varchar(255) DEFAULT NULL,
  `last_modified_on` timestamp NULL DEFAULT NULL,
  `voucher_detail_status` varchar(255) DEFAULT 'draft'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `sa_test_voucher_expense_detail`
--

INSERT INTO `sa_test_voucher_expense_detail` (`voucher_detail_id`, `voucher_id`, `expense_date`, `expense_account_id`, `expense_type`, `expense_description`, `expense_amount`, `has_attachment`, `created_by`, `created_on`, `last_modified_by`, `last_modified_on`, `voucher_detail_status`) VALUES
(1, 1, NULL, 0, '', 'Test Expenst Type', '2500.00', '', NULL, '2015-04-03 16:20:23', NULL, NULL, 'draft'),
(2, 18, NULL, 0, 'Cash', 'hhgh', '77.00', NULL, 'test', '2015-12-03 19:52:28', NULL, NULL, 'Draft'),
(3, 25, NULL, 18, 'Cash', 'hvghh', '45.00', NULL, 'test', '2015-12-03 20:57:40', NULL, NULL, 'Draft');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sa_companies`
--
ALTER TABLE `sa_companies`
 ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `sa_sys_config`
--
ALTER TABLE `sa_sys_config`
 ADD PRIMARY KEY (`config_id`);

--
-- Indexes for table `sa_test_coa`
--
ALTER TABLE `sa_test_coa`
 ADD PRIMARY KEY (`account_id`);

--
-- Indexes for table `sa_test_coa_groups`
--
ALTER TABLE `sa_test_coa_groups`
 ADD PRIMARY KEY (`group_id`);

--
-- Indexes for table `sa_test_fiscal_years`
--
ALTER TABLE `sa_test_fiscal_years`
 ADD PRIMARY KEY (`fiscal_year_id`);

--
-- Indexes for table `sa_test_general_journal`
--
ALTER TABLE `sa_test_general_journal`
 ADD PRIMARY KEY (`entry_id`);

--
-- Indexes for table `sa_test_journal_vouchers`
--
ALTER TABLE `sa_test_journal_vouchers`
 ADD PRIMARY KEY (`voucher_id`);

--
-- Indexes for table `sa_test_journal_voucher_details`
--
ALTER TABLE `sa_test_journal_voucher_details`
 ADD PRIMARY KEY (`voucher_detail_id`);

--
-- Indexes for table `sa_test_reporting_periods`
--
ALTER TABLE `sa_test_reporting_periods`
 ADD PRIMARY KEY (`reporting_period_id`);

--
-- Indexes for table `sa_test_transactions`
--
ALTER TABLE `sa_test_transactions`
 ADD PRIMARY KEY (`transaction_id`);

--
-- Indexes for table `sa_test_users`
--
ALTER TABLE `sa_test_users`
 ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `sa_test_voucher_expense`
--
ALTER TABLE `sa_test_voucher_expense`
 ADD PRIMARY KEY (`voucher_id`);

--
-- Indexes for table `sa_test_voucher_expense_detail`
--
ALTER TABLE `sa_test_voucher_expense_detail`
 ADD PRIMARY KEY (`voucher_detail_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sa_companies`
--
ALTER TABLE `sa_companies`
MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `sa_sys_config`
--
ALTER TABLE `sa_sys_config`
MODIFY `config_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `sa_test_coa`
--
ALTER TABLE `sa_test_coa`
MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT for table `sa_test_coa_groups`
--
ALTER TABLE `sa_test_coa_groups`
MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `sa_test_fiscal_years`
--
ALTER TABLE `sa_test_fiscal_years`
MODIFY `fiscal_year_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `sa_test_general_journal`
--
ALTER TABLE `sa_test_general_journal`
MODIFY `entry_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sa_test_journal_vouchers`
--
ALTER TABLE `sa_test_journal_vouchers`
MODIFY `voucher_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `sa_test_journal_voucher_details`
--
ALTER TABLE `sa_test_journal_voucher_details`
MODIFY `voucher_detail_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `sa_test_reporting_periods`
--
ALTER TABLE `sa_test_reporting_periods`
MODIFY `reporting_period_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `sa_test_transactions`
--
ALTER TABLE `sa_test_transactions`
MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sa_test_users`
--
ALTER TABLE `sa_test_users`
MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `sa_test_voucher_expense`
--
ALTER TABLE `sa_test_voucher_expense`
MODIFY `voucher_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT for table `sa_test_voucher_expense_detail`
--
ALTER TABLE `sa_test_voucher_expense_detail`
MODIFY `voucher_detail_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
