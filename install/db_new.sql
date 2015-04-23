/*
SQLyog Ultimate v10.00 Beta1
MySQL - 5.5.39 : Database - phpledger
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`phpledger` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `phpledger`;

/*Table structure for table `sa_test_financial_year` */

DROP TABLE IF EXISTS `sa_test_financial_year`;

CREATE TABLE `sa_test_financial_year` (
  `fy_id` int(11) NOT NULL AUTO_INCREMENT,
  `fy_description` varchar(200) DEFAULT NULL,
  `start_financial_year` date DEFAULT NULL,
  `end_financial_year` date DEFAULT NULL,
  PRIMARY KEY (`fy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `sa_test_financial_year` */


/*Table structure for table `sa_test_opening_balance` */

DROP TABLE IF EXISTS `sa_test_opening_balance`;

CREATE TABLE `sa_test_opening_balance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL,
  `financial_period_id` int(11) DEFAULT NULL,
  `opening_bl_date` date DEFAULT NULL,
  `opening_balance` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `sa_test_opening_balance` */

/*Table structure for table `sa_test_reporting_period` */

DROP TABLE IF EXISTS `sa_test_reporting_period`;

CREATE TABLE `sa_test_reporting_period` (
  `rp_id` int(11) NOT NULL AUTO_INCREMENT,
  `fy_id` int(11) DEFAULT NULL,
  `rp_description` varchar(200) DEFAULT NULL,
  `rp_start_date` date DEFAULT NULL,
  `rp_end_date` date DEFAULT NULL,
  PRIMARY KEY (`rp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `sa_test_reporting_period` */


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
