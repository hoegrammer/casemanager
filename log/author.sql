-- MySQL dump 10.11
--
-- Host: localhost    Database: lcm
-- ------------------------------------------------------
-- Server version	5.0.51a-3ubuntu5.4

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `lcm_author`
--

DROP TABLE IF EXISTS `lcm_author`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lcm_author` (
  `id_author` bigint(21) NOT NULL auto_increment,
  `id_office` bigint(21) NOT NULL default '0',
  `name_first` text NOT NULL,
  `name_middle` text NOT NULL,
  `name_last` text NOT NULL,
  `date_creation` datetime NOT NULL,
  `date_update` datetime NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` tinytext NOT NULL,
  `lang` varchar(10) NOT NULL default 'en',
  `prefs` text NOT NULL,
  `status` enum('admin','normal','external','trash','waiting','suspended','majestic','group','member') NOT NULL,
  `cookie_recall` tinytext NOT NULL,
  `maj` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `pgp` blob NOT NULL,
  `imessage` varchar(3) NOT NULL default '',
  `messagerie` varchar(3) NOT NULL default '',
  `alea_actuel` tinytext NOT NULL,
  `alea_futur` tinytext NOT NULL,
  PRIMARY KEY  (`id_author`),
  KEY `username` (`username`),
  KEY `status` (`status`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `lcm_author`
--

LOCK TABLES `lcm_author` WRITE;
/*!40000 ALTER TABLE `lcm_author` DISABLE KEYS */;
INSERT INTO `lcm_author` VALUES (5,0,'matt','','glasspole','2008-02-26 13:36:14','2009-03-04 18:35:50','matt','d9fd34ccc9fe81941ae39be9cd2178aa','en','a:11:{s:3:\"cnx\";s:0:\"\";s:9:\"page_rows\";i:10;s:5:\"theme\";s:5:\"green\";s:6:\"screen\";s:6:\"narrow\";s:9:\"font_size\";s:11:\"medium_font\";s:10:\"case_owner\";s:6:\"public\";s:11:\"case_period\";i:91;s:4:\"mode\";s:6:\"simple\";s:14:\"time_intervals\";s:8:\"relative\";s:23:\"time_intervals_notation\";s:10:\"hours_only\";s:11:\"case_status\";s:3:\"all\";}','admin','','2009-06-27 18:18:38','','','','141817632049aeca068f3d04.98830810','65949131349aeca068f3f13.88757212'),(7,0,'Panel','','Group','2009-06-09 05:51:32','2009-07-20 17:53:51','Panel','be682266e593ea13faa51b399593ef7f','en','a:10:{s:3:\"cnx\";s:0:\"\";s:9:\"page_rows\";i:10;s:5:\"theme\";s:5:\"green\";s:6:\"screen\";s:4:\"wide\";s:9:\"font_size\";s:11:\"medium_font\";s:10:\"case_owner\";s:2:\"my\";s:11:\"case_period\";s:2:\"91\";s:4:\"mode\";s:6:\"simple\";s:14:\"time_intervals\";s:8:\"relative\";s:23:\"time_intervals_notation\";s:10:\"hours_only\";}','normal','','2009-07-30 13:48:05','','','','8294160624a64a11f1c7a83.86147052','5649917584a64a11f1c7af5.45419851'),(9,0,'1 Trainer','','test','2009-06-12 00:48:14','2009-07-30 14:51:55','Trainer','b480de61fa99c96de09ae74981e282ef','en','a:1:{s:3:\"cnx\";s:0:\"\";}','normal','','2009-07-30 13:51:55','','','','7594971644a71a57bcab3d6.63166737','8596272254a71a57bcab457.27437773'),(11,0,'Paul','','Harvey','2009-06-19 14:13:59','2009-06-19 14:13:59','paul','8b5ab09bdb80536660cbc2f55301b784','en','a:10:{s:3:\"cnx\";s:0:\"\";s:9:\"page_rows\";i:15;s:5:\"theme\";s:5:\"green\";s:6:\"screen\";s:4:\"wide\";s:9:\"font_size\";s:10:\"small_font\";s:10:\"case_owner\";s:2:\"my\";s:11:\"case_period\";s:2:\"91\";s:4:\"mode\";s:6:\"simple\";s:14:\"time_intervals\";s:8:\"relative\";s:23:\"time_intervals_notation\";s:10:\"hours_only\";}','normal','','2009-07-30 13:48:05','','','','14969842374a3b8f17ea63a5.81785997','12149190354a3b8f17ea6414.97322974'),(12,0,'Teresa','','Gibson','2009-06-19 14:29:44','2009-07-17 14:51:42','Teresa','aa6c4c99177886a35f1b09704f4f9be3','en','a:1:{s:3:\"cnx\";s:0:\"\";}','normal','','2009-07-30 13:48:05','','','','10837745014a6081ee2cebe5.19095169','14596197174a6081ee2cec55.73098376'),(13,0,'Zohreh','','Kian','2009-06-19 15:06:08','2009-06-19 15:06:08','zohreh','54dcfa77835445415293e37834f5e42b','en','a:10:{s:3:\"cnx\";s:0:\"\";s:9:\"page_rows\";i:15;s:5:\"theme\";s:5:\"green\";s:6:\"screen\";s:4:\"wide\";s:9:\"font_size\";s:10:\"large_font\";s:10:\"case_owner\";s:2:\"my\";s:11:\"case_period\";s:2:\"91\";s:4:\"mode\";s:6:\"simple\";s:14:\"time_intervals\";s:8:\"relative\";s:23:\"time_intervals_notation\";s:10:\"hours_only\";}','normal','','2009-07-30 13:48:05','','','','10655236254a3b9b50c53f65.79690525','1593201124a3b9b50c53fe8.43034655'),(14,0,'Tendero','','Makwenha','2009-06-19 15:08:27','2009-07-04 11:51:13','tendero','2736e66e36dcf2b300dd38d379bdc089','en','a:1:{s:3:\"cnx\";s:0:\"\";}','normal','','2009-07-30 13:48:05','','','','6361201274a4f3421a67e34.31833377','3185304164a4f3421a67eb5.29294788'),(15,0,'Cath','','Roberts','2009-06-22 18:46:35','2009-06-22 18:46:35','cath','bfb3051b67cae268ba8f8a111fdd4ad6','en','','normal','','2009-07-30 13:48:05','','','','1385202934a3fc37b40d3a3.99856458','17029195864a3fc37b40d416.80243323'),(16,7,'Danielle','','Cohen','2009-06-22 19:06:26','2009-06-22 19:06:26','cohend','ce8bc795872fc0e8c21ea35089c179a6','en','a:1:{s:3:\"cnx\";s:0:\"\";}','normal','','2009-07-30 13:48:05','','','','8714998064a3fc822055df6.94230344','18842658284a3fc822055e68.63244917'),(17,19,'Joy','','Allen','2009-07-03 11:13:47','2009-07-04 12:26:26','Joy','0bbbcb9afe743737074861adaf2450f4','en','a:1:{s:3:\"cnx\";s:0:\"\";}','normal','','2009-07-30 13:48:05','','','','12592241834a4f3c62751d06.40106559','1142899724a4f3c62751d83.58748314'),(18,0,'Graham','','Birkin','2009-07-03 20:26:34','2009-07-03 20:26:34','','','en','','majestic','','2009-07-30 13:51:39','','','','',''),(19,0,'Accommodation','','Group','2009-07-03 20:41:52','2009-07-03 20:43:19','Accommodation','460288696173787d0ec89319d10665bd','en','a:1:{s:3:\"cnx\";s:0:\"\";}','normal','','2009-07-30 13:48:05','','','','7564874014a4e5f577a11e4.33082979','10686590294a4e5f577a1266.04337539'),(20,0,'Helpdesk','','Group','2009-07-03 20:43:43','2009-07-03 20:43:43','Helpdesk','b23361037daf247a3fa525ffbe8a0a83','en','','normal','','2009-07-30 13:48:05','','','','14366601674a4e5f6fe3c3a2.45107707','12661798434a4e5f6fe3c419.34182454'),(21,0,'Advocacy','','Group','2009-07-03 20:44:12','2009-07-03 20:44:12','Advocacy','690c4f4383c91dd0d6852135ceebab3c','en','','normal','','2009-07-30 13:48:05','','','','16423141034a4e5f8cbd5904.19792065','15657230344a4e5f8cbd5973.18227295'),(22,7,'Jill','','Gibson','2009-07-04 12:28:22','2009-07-04 12:29:15','Jill','04eaf19af2da3fe1a1c438f5820c01f3','en','','normal','','2009-07-30 13:48:05','','','','7469784874a4f3cd6eef056.50553727','12615275554a4f3cd6eef0d6.89644469'),(23,21,'janice','','brocklebank','2009-07-04 12:29:01','2009-07-04 12:29:01','janice','bbc069043a574fc815b4da324def56ad','en','','normal','','2009-07-30 13:48:05','','','','341223034a4f3cfd0ad1a9.56911544','13291464794a4f3cfd0ad223.99108743'),(24,0,'Jonathan','','Draper','2009-07-10 14:56:22','2009-07-16 13:25:10','Jonathan','bde0a6dcb6391df368599137f5e10845','en','a:1:{s:3:\"cnx\";s:0:\"\";}','normal','','2009-07-30 13:48:05','','','','19765741214a5f1c26c50c57.26898940','5694032324a5f1c26c50ce6.80270917');
/*!40000 ALTER TABLE `lcm_author` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-08-08 11:12:43
