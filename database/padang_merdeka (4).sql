-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `branch`;
CREATE TABLE `branch` (
  `branch_id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `address` text,
  PRIMARY KEY (`branch_id`),
  KEY `store_id` (`store_id`),
  CONSTRAINT `branch_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `store` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `branch` (`branch_id`, `store_id`, `name`, `address`) VALUES
(1,	1,	'Kota Tua',	'Jl. Lada No.1, RT.4/RW.6, Pinangsia, Tamansari, Kota Jakarta Barat, Daerah Khusus Ibukota Jakarta 11110');

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `category` (`category_id`, `name`) VALUES
(1,	'Makanan'),
(2,	'Minuman');

DROP TABLE IF EXISTS `opening_balance`;
CREATE TABLE `opening_balance` (
  `opening_balance_id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `open_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `close_at` timestamp NULL DEFAULT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`opening_balance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `opening_balance` (`opening_balance_id`, `date`, `open_at`, `close_at`, `balance`) VALUES
(1,	'2017-11-28',	'2017-11-28 08:58:36',	'2017-11-28 08:58:36',	1500000.00),
(3,	'2017-11-29',	'2017-11-29 00:50:43',	'2017-11-29 00:50:43',	800000.00);

DROP TABLE IF EXISTS `payment_method`;
CREATE TABLE `payment_method` (
  `payment_method_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`payment_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `payment_method` (`payment_method_id`, `name`) VALUES
(0,	'Cash'),
(1,	'BCA Debit'),
(2,	'BCA Credit'),
(3,	'Niaga Debit'),
(4,	'Niaga Credit'),
(5,	'Mega Debit'),
(6,	'Mega Credit'),
(8,	'Transfer');

DROP TABLE IF EXISTS `printer`;
CREATE TABLE `printer` (
  `printer_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `address` varchar(20) NOT NULL,
  `port` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`printer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `printer` (`printer_id`, `name`, `address`, `port`) VALUES
(1,	'Cashier Printer',	'192.168.0.11',	'9100'),
(2,	'Bar Printer',	'192.168.0.12',	'9100'),
(3,	'Kitchen Printer',	'192.168.0.13',	'9100'),
(4,	'Palung Printer',	'192.168.0.14',	'9100');

DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `sub_category_id` int(11) DEFAULT NULL,
  `rfid_code` varchar(100) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `price_gojek` decimal(10,2) DEFAULT NULL,
  `printer_id` int(11) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_id`),
  KEY `branch_id` (`branch_id`),
  KEY `sub_category_id` (`sub_category_id`),
  KEY `printer_id` (`printer_id`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`),
  CONSTRAINT `product_ibfk_3` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`sub_category_id`),
  CONSTRAINT `product_ibfk_4` FOREIGN KEY (`printer_id`) REFERENCES `printer` (`printer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `product` (`product_id`, `branch_id`, `sub_category_id`, `rfid_code`, `name`, `description`, `price`, `price_gojek`, `printer_id`, `stock`) VALUES
(56,	1,	1,	'',	'Nasi Putih',	'',	9000.00,	9900.00,	4,	0),
(57,	1,	1,	'',	'Nasi Rames',	'',	13000.00,	14300.00,	4,	0),
(58,	1,	1,	'',	'Nasi Merah',	'',	10000.00,	11000.00,	4,	0),
(59,	1,	2,	'',	'Rendang Sapi Merdeka',	'',	22000.00,	24200.00,	NULL,	0),
(60,	1,	2,	'',	'Dendeng Lambok',	'',	21000.00,	23100.00,	NULL,	0),
(61,	1,	2,	'',	'Dendeng Balado',	'',	21000.00,	23100.00,	NULL,	0),
(62,	1,	2,	'',	'Dendeng Batoko',	'',	21000.00,	23100.00,	NULL,	0),
(63,	1,	2,	'',	'Dendeng Iris ',	'',	21000.00,	23100.00,	NULL,	0),
(64,	1,	2,	'',	'Kikil Gulai',	'',	22000.00,	24200.00,	NULL,	0),
(65,	1,	2,	'',	'Kikil Cabe Hijau',	'',	22000.00,	24200.00,	NULL,	0),
(66,	1,	2,	'',	'Paru Sapi',	'',	21000.00,	23100.00,	NULL,	0),
(67,	1,	2,	'',	'Otak Telor',	'',	21000.00,	23100.00,	NULL,	0),
(68,	1,	2,	'',	'Gulai Otak',	'',	21000.00,	23100.00,	NULL,	0),
(69,	1,	2,	'',	'Gulai Limpa',	'',	20500.00,	22550.00,	NULL,	0),
(70,	1,	2,	'',	'Gulai Cincang Sapi',	'',	27000.00,	29700.00,	NULL,	0),
(71,	1,	2,	'',	'Sate Padang',	'',	35000.00,	38500.00,	3,	0),
(72,	1,	2,	'',	'Rendang Ayam',	'',	20500.00,	22550.00,	NULL,	0),
(73,	1,	2,	'',	'Ayam Gulai',	'',	20500.00,	22550.00,	NULL,	0),
(74,	1,	2,	'',	'Ayam Balado',	'',	20500.00,	22550.00,	NULL,	0),
(75,	1,	2,	'',	'Ayam Pop',	'',	20500.00,	22550.00,	3,	0),
(76,	1,	2,	'',	'Ayam Goreng',	'',	20500.00,	22550.00,	3,	0),
(77,	1,	2,	'',	'Ayam Bakar',	'',	20500.00,	22550.00,	NULL,	0),
(78,	1,	2,	'',	'Ayam Cabe Ijo',	'',	20500.00,	22550.00,	NULL,	0),
(79,	1,	2,	'',	'Ayam Geprek',	'',	20500.00,	22550.00,	NULL,	0),
(80,	1,	2,	'',	'Ayam Pedas',	'',	20500.00,	22550.00,	NULL,	0),
(81,	1,	2,	'',	'Rendang Sapi 1/2 kg',	'',	126000.00,	138600.00,	NULL,	0),
(82,	1,	2,	'',	'Rendang Sapi 1 kg',	'',	250000.00,	275000.00,	NULL,	0),
(83,	1,	3,	'',	'Kepala Ikan Kakap',	'',	198000.00,	217800.00,	NULL,	0),
(84,	1,	3,	'',	'Daging Kakap Gulai',	'',	29000.00,	31900.00,	NULL,	0),
(85,	1,	3,	'',	'Ikan Goreng Nila Dabu2',	'',	36000.00,	39600.00,	NULL,	0),
(86,	1,	3,	'',	'Ikan Kembung Bakar',	'',	28500.00,	31350.00,	NULL,	0),
(87,	1,	3,	'',	'ikan Kakap Balado',	'',	29000.00,	31900.00,	NULL,	0),
(88,	1,	3,	'',	'Udang Pancet',	'',	41000.00,	45100.00,	NULL,	0),
(89,	1,	3,	'',	'Sambal Udang Kecil',	'',	26000.00,	28600.00,	NULL,	0),
(90,	1,	4,	'',	'Perkedel',	'',	13000.00,	14300.00,	NULL,	0),
(91,	1,	4,	'',	'Telur Dadar',	'',	11500.00,	12650.00,	NULL,	0),
(92,	1,	4,	'',	'Telur Gulai',	'',	12000.00,	13200.00,	NULL,	0),
(93,	1,	4,	'',	'Telur Balado',	'',	12000.00,	13200.00,	NULL,	0),
(94,	1,	4,	'',	'Tempe Mendoan',	'',	10000.00,	11000.00,	NULL,	0),
(95,	1,	4,	'',	'Kerupuk Kulit (Jange)',	'',	9000.00,	9900.00,	NULL,	0),
(96,	1,	4,	'',	'Jengkol Gulai',	'',	18000.00,	19800.00,	NULL,	0),
(97,	1,	4,	'',	'Jengkol Rendang',	'',	18000.00,	19800.00,	NULL,	0),
(98,	1,	4,	'',	'Soto Padang',	'',	35000.00,	38500.00,	4,	0),
(99,	1,	4,	'',	'Tahu Geprek',	'',	10000.00,	11000.00,	NULL,	0),
(101,	1,	7,	'',	'Jus Apel',	'',	21000.00,	23100.00,	2,	0),
(102,	1,	7,	'',	'Jus Stroberi',	'',	21000.00,	23100.00,	2,	0),
(103,	1,	7,	'',	'Juice Stroberi Pisang',	'',	22500.00,	24750.00,	2,	0),
(104,	1,	7,	'',	'Jus Jeruk Hangat',	'',	21000.00,	23100.00,	2,	0),
(105,	1,	7,	'',	'Jus Jeruk',	'',	21000.00,	23100.00,	2,	0),
(106,	1,	7,	'',	'Jus Belimbing',	'',	21000.00,	23100.00,	2,	0),
(107,	1,	7,	'',	'Jus Semangka',	'',	21000.00,	23100.00,	2,	0),
(108,	1,	7,	'',	'Jus Sirsak',	'',	21000.00,	23100.00,	2,	0),
(109,	1,	7,	'',	'Jus Alpukat',	'',	21000.00,	23100.00,	2,	0),
(110,	1,	7,	'',	'Jus Terong Belanda',	'',	21000.00,	23100.00,	2,	0),
(111,	1,	7,	'',	'Jus Terong Belanda Markisa',	'',	22500.00,	24750.00,	2,	0),
(112,	1,	7,	'',	'Jus Nanas',	'',	21000.00,	23100.00,	2,	0),
(113,	1,	7,	'',	'Jus Mangga',	'',	21000.00,	23100.00,	2,	0),
(114,	1,	7,	'',	'Jus Melon',	'',	21000.00,	23100.00,	2,	0),
(115,	1,	7,	'',	'Jus Jambu',	'',	21000.00,	23100.00,	2,	0),
(116,	1,	7,	'',	'Jus Grape Kiss',	'',	22500.00,	24750.00,	2,	0),
(117,	1,	7,	'',	'Jus Kiwi Fantasi',	'',	22500.00,	24750.00,	2,	0),
(119,	1,	8,	'',	'Es Kopyor',	'',	26000.00,	28600.00,	2,	0),
(120,	1,	8,	'',	'Es Kopyor Durian',	'',	29500.00,	32450.00,	2,	0),
(121,	1,	8,	'',	'Es Markisa',	'',	21000.00,	23100.00,	2,	0),
(122,	1,	8,	'',	'Es Campur',	'',	26000.00,	28600.00,	2,	0),
(123,	1,	8,	'',	'Es Teler',	'',	26000.00,	28600.00,	2,	0),
(124,	1,	9,	'',	'Aqua Botol',	'',	8000.00,	8800.00,	2,	0),
(125,	1,	9,	'',	'Soft Drink (Fanta merah, Coca cola)',	'',	9500.00,	10450.00,	2,	0),
(126,	1,	9,	'',	'Es Teh Manis',	'',	9500.00,	10450.00,	2,	0),
(127,	1,	9,	'',	'Es Teh Tawar (Refill)',	'',	10000.00,	11000.00,	2,	0),
(128,	1,	9,	'',	'Teh Tawar Hangat (Refill)',	'',	10000.00,	11000.00,	2,	0),
(129,	1,	9,	'',	'Ice Lemon Tea',	'',	15000.00,	16500.00,	2,	0),
(130,	1,	9,	'',	'Lemon Tea Hangat',	'',	15000.00,	16500.00,	2,	0),
(131,	1,	9,	'',	'Buah Campur',	'',	25000.00,	27500.00,	2,	0),
(132,	1,	9,	'',	'Kopi',	'',	15000.00,	16500.00,	2,	0),
(133,	1,	9,	'',	'Kopi Susu',	'',	18000.00,	19800.00,	2,	0),
(134,	1,	9,	'',	'Soda Gembira',	'',	21000.00,	23100.00,	2,	0),
(135,	1,	4,	'',	'Tempe Geprek',	'',	10000.00,	11000.00,	NULL,	0),
(136,	1,	5,	'',	'Sayur Nangka',	'',	13000.00,	14300.00,	NULL,	0),
(137,	1,	5,	'',	'Sayur Singkong',	'',	13000.00,	14300.00,	NULL,	0),
(138,	1,	5,	'',	'Daun Ubi Tumbuk',	'',	17000.00,	18700.00,	NULL,	0),
(139,	1,	5,	'',	'Tumis Pare',	'',	17000.00,	18700.00,	NULL,	0),
(140,	1,	5,	'',	'Sawi Kailan Seafood',	'',	19000.00,	20900.00,	NULL,	0),
(141,	1,	5,	'',	'Cah Buncis Ayam',	'',	18000.00,	19800.00,	NULL,	0),
(142,	1,	5,	'',	'Brokoli Bawang Putih',	'',	16000.00,	17600.00,	NULL,	0),
(143,	1,	5,	'',	'Sawi Kailan Cah Bawang Putih',	'',	16000.00,	17600.00,	NULL,	0),
(144,	1,	5,	'',	'Lalapan ',	'',	6000.00,	6600.00,	NULL,	0),
(145,	1,	5,	'',	'Petai Goreng',	'',	15000.00,	16500.00,	NULL,	0),
(146,	1,	5,	'',	'Terong Utuh',	'',	18000.00,	19800.00,	NULL,	0),
(147,	1,	6,	'',	'Sambal Ijo',	'',	10900.00,	11990.00,	NULL,	0),
(148,	1,	6,	'',	'Sambal Merah',	'test',	10900.00,	11990.00,	NULL,	0),
(149,	1,	6,	'',	'Sambal Ati Ayam',	'',	16500.00,	18150.00,	NULL,	0),
(150,	1,	6,	'',	'Sambal Jengkol',	'',	18000.00,	19800.00,	NULL,	0),
(151,	1,	6,	'',	'Sambal Teri Tempe Ijo',	'',	17000.00,	18700.00,	NULL,	0),
(153,	1,	6,	'',	'Sambal Terong Ikan Gabus',	'',	18000.00,	19800.00,	NULL,	0),
(154,	1,	6,	'',	'Sambal Pete',	'',	15000.00,	16500.00,	NULL,	0),
(156,	1,	10,	'',	'Nasi Box A',	'- Nasi Putih<br>\r\n- Telur (Gulai/Balado)<br>\r\n- Sayur (Nangka/Daun Singkong/Ubi Tumbuk)<br>\r\n- Sambal Ijo<br>\r\n- Timun<br>\r\n- Sambal (Ati/Teri Tempe Ijo)<br>\r\n- Jeruk/Pisang',	25000.00,	25000.00,	NULL,	0),
(157,	1,	10,	'',	'Nasi Box B',	'- Nasi Putih<br>\r\n- Ayam (Rendang/Gulai/Goreng/Pop/Bakar/Cabe Ijo/Tutu Ruga/Pedas)<br>\r\n- Sayur (Nangka/Daun Singkong/Ubi Tumbuk)<br>\r\n- Sambal Ijo<br>\r\n- Timun<br>\r\n- Sambal (Ati/Teri Tempe Ijo)<br>\r\n- Jeruk/Pisang',	32000.00,	32000.00,	NULL,	0),
(158,	1,	10,	'',	'Nasi Box C',	'- Nasi Putih<br>\r\n- Daging (Rendang / Dendeng Lambok / Dendeng Balado / Dendeng Batoko / Dendeng Cabe Ijo / Paru Sapi / Gulai Otak / Otak Telor / Gulai Limpa / Gulai Kikil / Kikil Cabe Ijo)<br>\r\n- Sayur (Nangka/Daun Singkong/Ubi Tumbuk)<br>\r\n- Sambal Ijo<br>\r\n- Timun<br>\r\n- Sambal (Ati/Teri Tempe Ijo)<br>\r\n- Jeruk/Pisang',	34000.00,	34000.00,	NULL,	0),
(159,	1,	10,	'',	'Nasi Box D',	'- Nasi Putih<br>\r\n- Daging Ikan Kakap<br>\r\n- Sayur (Nangka/Daun Singkong/Ubi Tumbuk)<br>\r\n- Sambal Ijo<br>\r\n- Timun<br>\r\n- Sambal (Ati/Teri Tempe Ijo)<br>\r\n- Jeruk/Pisang',	37000.00,	37000.00,	NULL,	0),
(160,	1,	3,	'',	'Telur Ikan Gulai',	'',	31000.00,	34100.00,	NULL,	0),
(161,	1,	11,	NULL,	'Tape Colenak',	NULL,	19000.00,	20900.00,	2,	0),
(162,	1,	11,	NULL,	'Mocca Nougat',	NULL,	19000.00,	20900.00,	2,	0),
(163,	1,	11,	NULL,	'Pandan Gula Malaka',	NULL,	19000.00,	20900.00,	2,	0);

DROP TABLE IF EXISTS `promotion`;
CREATE TABLE `promotion` (
  `promotion_id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `description` text,
  `type` enum('percent','value') NOT NULL DEFAULT 'percent',
  `value` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `start_date` date DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`promotion_id`),
  KEY `branch_id` (`branch_id`),
  CONSTRAINT `promotion_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `promotion` (`promotion_id`, `branch_id`, `name`, `description`, `type`, `value`, `created_at`, `start_date`, `expired_date`, `status`) VALUES
(1,	1,	'15% Bank Mega Credit Card',	'Transaction min 250k - max 2jt',	'percent',	15.00,	'2017-11-27 02:16:37',	NULL,	NULL,	'active'),
(2,	1,	'Opening Promo 15%',	'29 Nov 2017 - 5 Dec 2017',	'percent',	15.00,	'2017-11-27 02:17:13',	'2017-11-29',	'2017-12-05',	'active'),
(3,	1,	'Free of Charge',	'Discount 100%',	'percent',	100.00,	'2017-11-28 08:38:07',	NULL,	NULL,	'active');

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `role` (`role_id`, `name`) VALUES
(1,	'Admin'),
(2,	'Waiter'),
(3,	'Purchasing'),
(4,	'Warehouse'),
(5,	'Cashier');

DROP TABLE IF EXISTS `store`;
CREATE TABLE `store` (
  `store_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  PRIMARY KEY (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `store` (`store_id`, `name`) VALUES
(1,	'Restoran Padang Merdeka');

DROP TABLE IF EXISTS `sub_category`;
CREATE TABLE `sub_category` (
  `sub_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL DEFAULT '1',
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`sub_category_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `sub_category_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `sub_category` (`sub_category_id`, `category_id`, `name`) VALUES
(1,	1,	'Menu Extra'),
(2,	1,	'Menu Utama'),
(3,	1,	'Menu Seafood'),
(4,	1,	'Menu Sampingan'),
(5,	1,	'Menu Sayur'),
(6,	1,	'Menu Sambal'),
(7,	2,	'Jus'),
(8,	2,	'Es'),
(9,	2,	'Lain-Lain'),
(10,	1,	'Paket'),
(11,	1,	'Kue');

DROP TABLE IF EXISTS `table`;
CREATE TABLE `table` (
  `table_id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `number` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`table_id`),
  KEY `branch_id` (`branch_id`),
  CONSTRAINT `table_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `table` (`table_id`, `branch_id`, `number`) VALUES
(1,	1,	1),
(2,	1,	2),
(3,	1,	3),
(4,	1,	4),
(5,	1,	5),
(6,	1,	6),
(7,	1,	7),
(8,	1,	8),
(9,	1,	9),
(10,	1,	10),
(11,	1,	11),
(12,	1,	12),
(13,	1,	13),
(14,	1,	14),
(15,	1,	15),
(16,	1,	16),
(17,	1,	17),
(18,	1,	18),
(19,	1,	19),
(20,	1,	20),
(21,	1,	21),
(22,	1,	22),
(23,	1,	23),
(24,	1,	24),
(25,	1,	25),
(26,	1,	26);

DROP TABLE IF EXISTS `transaction`;
CREATE TABLE `transaction` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `table_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `payment_method_id` int(11) DEFAULT '0',
  `promotion_id` int(11) DEFAULT NULL,
  `type` enum('disajikan','rames','takeaway') NOT NULL DEFAULT 'disajikan',
  `price_category` enum('general','gojek') NOT NULL DEFAULT 'general',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `paid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payable` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `void_by` int(11) DEFAULT NULL,
  `note` text,
  `status` enum('pending','finished','void') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`transaction_id`),
  KEY `table_id` (`table_id`),
  KEY `user_id` (`user_id`),
  KEY `payment_method_id` (`payment_method_id`),
  KEY `promotion_id` (`promotion_id`),
  KEY `void_by` (`void_by`),
  CONSTRAINT `transaction_ibfk_1` FOREIGN KEY (`table_id`) REFERENCES `table` (`table_id`),
  CONSTRAINT `transaction_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  CONSTRAINT `transaction_ibfk_3` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_method` (`payment_method_id`),
  CONSTRAINT `transaction_ibfk_4` FOREIGN KEY (`promotion_id`) REFERENCES `promotion` (`promotion_id`),
  CONSTRAINT `transaction_ibfk_5` FOREIGN KEY (`void_by`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `transaction` (`transaction_id`, `table_id`, `user_id`, `payment_method_id`, `promotion_id`, `type`, `price_category`, `total`, `grand_total`, `discount`, `paid`, `payable`, `created_at`, `updated_at`, `void_by`, `note`, `status`) VALUES
(196,	1,	7,	0,	1,	'disajikan',	'general',	20000.00,	18700.00,	3000.00,	20000.00,	1300.00,	'2017-11-28 12:24:35',	'2017-11-28 12:24:35',	NULL,	NULL,	'finished'),
(197,	NULL,	7,	6,	1,	'disajikan',	'general',	166000.00,	155210.00,	24900.00,	200000.00,	44790.00,	'2017-11-28 12:27:53',	'2017-11-28 12:54:39',	3,	NULL,	'void'),
(198,	NULL,	3,	5,	2,	'disajikan',	'general',	111000.00,	103785.00,	16650.00,	120000.00,	16215.00,	'2017-11-28 12:41:01',	'2017-11-28 12:41:01',	NULL,	NULL,	'finished'),
(199,	NULL,	3,	0,	NULL,	'disajikan',	'general',	101000.00,	111100.00,	0.00,	120000.00,	8900.00,	'2017-11-28 22:49:47',	'2017-11-28 22:49:47',	NULL,	NULL,	'finished'),
(200,	4,	3,	1,	2,	'disajikan',	'general',	54000.00,	50490.00,	8100.00,	100000.00,	49510.00,	'2017-11-28 22:50:29',	'2017-11-28 22:50:29',	NULL,	NULL,	'finished'),
(201,	NULL,	3,	0,	2,	'disajikan',	'general',	87000.00,	81345.00,	13050.00,	100000.00,	18655.00,	'2017-11-29 08:50:43',	'2017-11-29 08:50:43',	NULL,	NULL,	'finished'),
(202,	9,	3,	3,	3,	'disajikan',	'general',	106000.00,	0.00,	106000.00,	0.00,	0.00,	'2017-11-29 08:58:24',	'2017-11-29 08:58:24',	NULL,	'hahaha',	'finished'),
(204,	NULL,	3,	0,	NULL,	'disajikan',	'general',	0.00,	0.00,	0.00,	0.00,	0.00,	'2017-11-29 00:04:25',	'2017-11-29 00:53:32',	3,	NULL,	'void'),
(205,	NULL,	3,	0,	NULL,	'disajikan',	'general',	30000.00,	33000.00,	0.00,	0.00,	0.00,	'2017-11-29 00:21:07',	'2017-11-29 00:53:43',	3,	NULL,	'void'),
(206,	NULL,	3,	0,	NULL,	'disajikan',	'general',	10000.00,	11000.00,	0.00,	0.00,	0.00,	'2017-11-29 00:22:15',	'2017-11-29 00:53:48',	3,	NULL,	'void'),
(207,	NULL,	3,	0,	NULL,	'disajikan',	'general',	10000.00,	11000.00,	0.00,	0.00,	0.00,	'2017-11-29 00:23:57',	'2017-11-29 00:53:52',	3,	'',	'void'),
(208,	NULL,	3,	0,	NULL,	'disajikan',	'general',	10000.00,	11000.00,	0.00,	0.00,	0.00,	'2017-11-29 00:26:32',	'2017-11-29 00:53:55',	3,	NULL,	'void'),
(209,	NULL,	3,	0,	NULL,	'disajikan',	'general',	10000.00,	11000.00,	0.00,	0.00,	0.00,	'2017-11-29 00:27:53',	'2017-11-29 00:54:25',	3,	NULL,	'void'),
(210,	NULL,	3,	0,	NULL,	'disajikan',	'general',	10000.00,	11000.00,	0.00,	0.00,	0.00,	'2017-11-29 00:28:17',	'2017-11-29 00:54:29',	3,	NULL,	'void'),
(211,	NULL,	3,	0,	NULL,	'disajikan',	'general',	20000.00,	22000.00,	0.00,	0.00,	0.00,	'2017-11-29 00:29:35',	'2017-11-29 00:54:33',	3,	NULL,	'void'),
(212,	NULL,	3,	4,	2,	'disajikan',	'general',	38000.00,	35530.00,	5700.00,	0.00,	0.00,	'2017-11-29 00:30:29',	'2017-11-29 00:54:37',	3,	NULL,	'void'),
(213,	NULL,	3,	0,	NULL,	'disajikan',	'general',	10000.00,	11000.00,	0.00,	11000.00,	0.00,	'2017-11-29 00:34:51',	'2017-11-29 00:36:14',	NULL,	'testttt',	'finished'),
(214,	5,	3,	3,	2,	'disajikan',	'general',	29000.00,	27115.00,	4350.00,	20000.00,	1300.00,	'2017-11-29 00:37:33',	'2017-11-29 00:37:49',	NULL,	'04400',	'finished'),
(215,	NULL,	3,	4,	NULL,	'disajikan',	'general',	20000.00,	22000.00,	0.00,	30000.00,	8000.00,	'2017-11-29 00:41:56',	'2017-11-29 00:41:56',	NULL,	'0333',	'finished'),
(216,	2,	3,	0,	2,	'disajikan',	'general',	40000.00,	37400.00,	6000.00,	0.00,	0.00,	'2017-11-29 00:43:25',	'2017-11-29 00:54:05',	3,	'test',	'void');

DROP TABLE IF EXISTS `transaction_detail`;
CREATE TABLE `transaction_detail` (
  `transaction_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sent` bit(1) NOT NULL DEFAULT b'0',
  `note` text,
  `void_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`transaction_detail_id`),
  KEY `transaction_id` (`transaction_id`),
  KEY `product_id` (`product_id`),
  KEY `void_by` (`void_by`),
  CONSTRAINT `transaction_detail_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transaction` (`transaction_id`),
  CONSTRAINT `transaction_detail_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  CONSTRAINT `transaction_detail_ibfk_3` FOREIGN KEY (`void_by`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `transaction_detail` (`transaction_detail_id`, `transaction_id`, `product_id`, `quantity`, `price`, `subtotal`, `sent`, `note`, `void_by`) VALUES
(326,	196,	58,	2,	10000.00,	20000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(327,	197,	58,	2,	10000.00,	20000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(328,	197,	56,	4,	9000.00,	36000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(329,	197,	57,	6,	13000.00,	78000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(330,	197,	124,	4,	8000.00,	32000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(331,	198,	58,	2,	10000.00,	20000.00,	CONV('0', 2, 10) + 0,	'testset',	NULL),
(332,	198,	56,	1,	9000.00,	9000.00,	CONV('0', 2, 10) + 0,	'tsete',	NULL),
(333,	198,	79,	2,	20500.00,	41000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(334,	198,	76,	1,	20500.00,	20500.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(335,	198,	74,	1,	20500.00,	20500.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(336,	199,	58,	2,	10000.00,	20000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(337,	199,	56,	3,	9000.00,	27000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(338,	199,	96,	3,	18000.00,	54000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(339,	200,	58,	1,	10000.00,	10000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(340,	200,	56,	2,	9000.00,	18000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(341,	200,	57,	2,	13000.00,	26000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(342,	201,	58,	1,	10000.00,	10000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(343,	201,	96,	2,	18000.00,	36000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(344,	201,	74,	2,	20500.00,	41000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(345,	202,	58,	2,	10000.00,	20000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(346,	202,	56,	2,	9000.00,	18000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(347,	202,	142,	2,	16000.00,	32000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(348,	202,	96,	2,	18000.00,	36000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(351,	205,	58,	3,	10000.00,	30000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(353,	206,	58,	1,	10000.00,	10000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(357,	208,	58,	1,	10000.00,	10000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(359,	209,	58,	1,	10000.00,	10000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(361,	210,	58,	1,	10000.00,	10000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(363,	211,	58,	2,	10000.00,	20000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(368,	212,	58,	2,	10000.00,	20000.00,	CONV('0', 2, 10) + 0,	'anget',	NULL),
(369,	212,	56,	2,	9000.00,	18000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(370,	207,	58,	1,	10000.00,	10000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(373,	213,	58,	1,	10000.00,	10000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(379,	214,	58,	2,	10000.00,	20000.00,	CONV('0', 2, 10) + 0,	'test',	NULL),
(380,	214,	56,	1,	9000.00,	9000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(381,	215,	58,	2,	10000.00,	20000.00,	CONV('0', 2, 10) + 0,	'',	NULL),
(386,	216,	58,	4,	10000.00,	40000.00,	CONV('0', 2, 10) + 0,	'',	NULL);

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` text NOT NULL,
  `token` text,
  `api_token` varchar(60) DEFAULT NULL,
  `remember_token` text,
  `last_login` timestamp NULL DEFAULT NULL,
  `void_pass` varchar(20) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  KEY `role_id` (`role_id`),
  KEY `branch_id` (`branch_id`),
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`),
  CONSTRAINT `user_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `user` (`user_id`, `role_id`, `branch_id`, `name`, `email`, `phone`, `password`, `token`, `api_token`, `remember_token`, `last_login`, `void_pass`, `status`) VALUES
(1,	1,	NULL,	'Admin',	'admin@pm.com',	'01234',	'$2y$10$o7L5YzPKkPFwwMFk6BUsduI4FhRYot5vg8JdJWnmSRHlT5Z/S08li',	'5401612b71e16b3933705e63abfd1d46',	NULL,	'XBHmweLOL3ZHANq6r3cI3azFpR3XNFKCPzM8ue3wcIrIGHaZh7rewxO1B6aN',	'2017-11-22 09:46:09',	NULL,	1),
(3,	5,	NULL,	'Cashier Eka - Pluit',	'eka@pm.com',	'',	'$2y$10$rdg5Ru6a/Ei.CwkuxnOT4OmL7Poc0WqW4wwXI0oKbti1g738Bn9LO',	'329e9ae0c964cfec0b8b817c796e3dca',	'329e9ae0c964cfec0b8b817c796e3dca',	'vq6a6MJMjxqburp4TGgk6SZTAvozKdZtiRp7zP5jzmrVj93lQxzkTqyWoEsv',	NULL,	NULL,	1),
(7,	5,	NULL,	'Cashier Ria - Pluit',	'ria@pm.com',	'',	'$2y$10$5wXnkW/dMlKoNZY6gqRgNOCCzbxLziZvOsKirOgLiD/rqNBIDzMFy',	'Yi2RSJDF0zj4oaxer0w8dD7VfIOKG3xMiu2UXDrV',	NULL,	'W4LiA27xkh8jhJelWCGmnl5T9lKx7ybuWD5zPgVuswVRqCoBhCpRP4ubstQm',	NULL,	NULL,	1);

-- 2017-11-29 01:10:17
