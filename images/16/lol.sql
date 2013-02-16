-- MySQL dump 10.11
--
-- Host: localhost    Database: lcm
-- ------------------------------------------------------
-- Server version	5.0.67-0ubuntu6

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
-- Table structure for table `lcm_keyword_group`
--

DROP TABLE IF EXISTS `lcm_keyword_group`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lcm_keyword_group` (
  `id_group` bigint(21) NOT NULL auto_increment,
  `id_parent` bigint(21) NOT NULL default '0',
  `name` varchar(255) NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `type` enum('system','contact','case','stage','followup','client','org','client_org','author') NOT NULL,
  `policy` enum('optional','recommended','mandatory') NOT NULL default 'optional',
  `quantity` enum('one','many') NOT NULL default 'one',
  `suggest` text NOT NULL,
  `ac_admin` enum('Y','N') NOT NULL default 'Y',
  `ac_author` enum('Y','N') NOT NULL default 'Y',
  PRIMARY KEY  (`id_group`),
  UNIQUE KEY `idx_kwg_name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `lcm_keyword_group`
--

LOCK TABLES `lcm_keyword_group` WRITE;
/*!40000 ALTER TABLE `lcm_keyword_group` DISABLE KEYS */;
INSERT INTO `lcm_keyword_group` VALUES (1,0,'followups','kwg_followups_title','kwg_followups_description','system','mandatory','one','followups01','Y','Y'),(2,0,'+email_main','kw_contacts_emailmain_title','kw_contacts_emailmain_description','contact','optional','one','','N','Y'),(3,0,'+email_alternate','kw_contacts_emailalternate_title','kw_contacts_emailalternate_description','contact','optional','many','','Y','Y'),(4,0,'+phone_home','kw_contacts_phonehome_title','kw_contacts_phonehome_description','contact','recommended','many','','Y','Y'),(5,0,'+phone_office','kw_contacts_phoneoffice_title','kw_contacts_phoneoffice_description','contact','optional','many','','Y','Y'),(6,0,'+phone_mobile','kw_contacts_phonemobile_title','kw_contacts_phonemobile_description','contact','optional','many','','Y','Y'),(7,0,'+address_main','kw_contacts_addressmain_title','kw_contacts_addressmain_description','contact','recommended','one','','Y','Y'),(8,0,'appointments','kwg_appointments_title','kwg_appointments_description','system','optional','many','appointments04','Y','Y'),(9,0,'civilstatus','kwg_civilstatus_title','kwg_civilstatus_description','system','optional','one','unknown','Y','Y'),(10,0,'income','kwg_income_title','kwg_income_description','system','optional','one','unknown','Y','Y'),(11,0,'stage','kwg_stage_title','kwg_stage_description','system','optional','one','investigation','Y','Y'),(12,0,'conclusion','kwg_conclusion_title','kwg_conclusion_description','system','optional','one','conclusion01','Y','Y'),(13,0,'sentence','kwg_sentence_title','kwg_sentence_description','system','optional','one','none','Y','Y'),(14,0,'_crimresults','kwg__crimresults_title','kwg__crimresults_title','system','optional','one','none','Y','Y'),(15,0,'_refnumbers','kwg__refnumbers_title','kwg__refnumbers_description','stage','optional','many','','Y','N'),(16,0,'_institutions','kwg__institutions_title','kwg__institutions_description','stage','optional','one','','Y','N'),(17,0,'_exptypes','kwg__exptypes_title','kwg__exptypes_description','system','mandatory','one','','Y','Y'),(18,0,'Referral','Referral','','followup','optional','one','','Y','Y'),(19,0,'status','Status','','client','mandatory','one','','Y','Y'),(20,0,'ethnicity','Nationality of Origin','','client','mandatory','one','','Y','Y'),(21,0,'Language','Language Spoken','','client','mandatory','one','Language01','Y','Y'),(22,0,'interpreter','Interpreter?','','client','mandatory','one','interpreter01','Y','Y'),(23,0,'disabled','Disabled?','','client','mandatory','one','disabled01','Y','Y'),(24,0,'funder','Funder','','client','mandatory','one','','Y','Y'),(27,0,'casetype','Case type','','case','mandatory','one','','Y','Y');
/*!40000 ALTER TABLE `lcm_keyword_group` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-04-26 20:39:08
