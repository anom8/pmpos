# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.01 (MySQL 5.7.21)
# Database: padang_merdeka
# Generation Time: 2018-04-16 04:56:06 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table csvdatas
# ------------------------------------------------------------

DROP TABLE IF EXISTS `csvdatas`;

CREATE TABLE `csvdatas` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `report_name` varchar(255) DEFAULT NULL,
  `report_date` datetime DEFAULT NULL,
  `gross_sales` decimal(10,0) DEFAULT NULL,
  `discount` decimal(10,0) DEFAULT NULL,
  `free_of_charge` decimal(10,0) DEFAULT NULL,
  `net_sales` decimal(10,0) DEFAULT NULL,
  `tax_ten_percent_total` decimal(10,0) DEFAULT NULL,
  `no_of_receipt` int(11) DEFAULT NULL,
  `average_receipt` decimal(10,0) DEFAULT NULL,
  `total_sales` decimal(10,0) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `csvdatas` WRITE;
/*!40000 ALTER TABLE `csvdatas` DISABLE KEYS */;

INSERT INTO `csvdatas` (`id`, `report_name`, `report_date`, `gross_sales`, `discount`, `free_of_charge`, `net_sales`, `tax_ten_percent_total`, `no_of_receipt`, `average_receipt`, `total_sales`, `created_at`, `updated_at`)
VALUES
	(1,'Report 01-01-2018 - 13-04-2018','2018-04-13 14:31:54',3372757240,44641755,8156200,3319959285,331995929,14136,258344,3651955214,'2018-04-13 14:31:54','2018-04-13 14:31:54'),
	(2,'Report 01-01-2018 - 13-04-2018','2018-04-16 11:25:41',3372757240,44641755,8156200,3319959285,331995929,14136,258344,3651955214,'2018-04-16 11:25:41','2018-04-16 11:25:41');

/*!40000 ALTER TABLE `csvdatas` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
