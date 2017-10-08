-- MySQL dump 10.13  Distrib 5.5.23, for Win64 (x86)
--
-- Host: 127.0.0.1    Database: wkportal_test
-- ------------------------------------------------------
-- Server version	5.5.23

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
-- Table structure for table `auth_assignment`
--

DROP TABLE IF EXISTS `auth_assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_assignment` (
  `user_id` binary(16) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `person` (`person_id`),
  CONSTRAINT `item_name` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_assignment`
--

LOCK TABLES `auth_assignment` WRITE;
/*!40000 ALTER TABLE `auth_assignment` DISABLE KEYS */;
INSERT INTO `auth_assignment` VALUES ('�Y�$�5�X\0�Bt�','Administrator',1507473196);
/*!40000 ALTER TABLE `auth_assignment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_item`
--

DROP TABLE IF EXISTS `auth_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_item` (
  `name` varchar(64) NOT NULL,
  `type` smallint(6) NOT NULL,
  `view` tinyint(1) NOT NULL DEFAULT '0',
  `ldap_group` varchar(255) DEFAULT NULL,
  `description` text,
  `rule_name` varchar(64) DEFAULT NULL,
  `data` binary(16) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`name`),
  KEY `idx-auth_item-type` (`type`),
  KEY `rule_name` (`rule_name`),
  CONSTRAINT `rule_name` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_item`
--

LOCK TABLES `auth_item` WRITE;
/*!40000 ALTER TABLE `auth_item` DISABLE KEYS */;
INSERT INTO `auth_item` VALUES ('Administrator',1,0,NULL,'Администратор системы',NULL,NULL,1507473195,1507473195),('baseAdministrator',1,1,NULL,'Администратор базовой конфигурации',NULL,NULL,1507473195,1507473195),('baseDolzhEdit',1,1,NULL,'Оператор справочника \"Должности\"',NULL,NULL,1507473195,1507473195),('basePodrazEdit',1,1,NULL,'Оператор справочника \"Подразделения\"',NULL,NULL,1507473195,1507473195),('dolzhEdit',2,1,NULL,'Редактирование справочника \"Должности\"',NULL,NULL,1507473195,1507473195),('podrazEdit',2,1,NULL,'Редактирование справочника \"Подразделения\"',NULL,NULL,1507473195,1507473195),('roleEdit',2,1,NULL,'Редактирование ролей пользователя',NULL,NULL,1507473195,1507473195),('roleOperator',1,1,NULL,'Оператор менеджера ролей',NULL,NULL,1507473195,1507473195),('userEdit',2,1,NULL,'Редактирование пользователей',NULL,NULL,1507473195,1507473195),('userOperator',1,1,NULL,'Оператор менеджера пользователей',NULL,NULL,1507473195,1507473195);
/*!40000 ALTER TABLE `auth_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_item_child`
--

DROP TABLE IF EXISTS `auth_item_child`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_item_child` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `child` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `parent` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_item_child`
--

LOCK TABLES `auth_item_child` WRITE;
/*!40000 ALTER TABLE `auth_item_child` DISABLE KEYS */;
INSERT INTO `auth_item_child` VALUES ('Administrator','baseAdministrator'),('baseAdministrator','baseDolzhEdit'),('baseAdministrator','basePodrazEdit'),('baseDolzhEdit','dolzhEdit'),('basePodrazEdit','podrazEdit'),('roleOperator','roleEdit'),('baseAdministrator','roleOperator'),('userOperator','userEdit'),('baseAdministrator','userOperator');
/*!40000 ALTER TABLE `auth_item_child` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_rule`
--

DROP TABLE IF EXISTS `auth_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_rule` (
  `name` varchar(64) NOT NULL,
  `data` blob,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_rule`
--

LOCK TABLES `auth_rule` WRITE;
/*!40000 ALTER TABLE `auth_rule` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `build`
--

DROP TABLE IF EXISTS `build`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `build` (
  `build_id` binary(16) NOT NULL,
  `build_name` varchar(255) NOT NULL,
  PRIMARY KEY (`build_id`),
  UNIQUE KEY `build_name` (`build_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `build`
--

LOCK TABLES `build` WRITE;
/*!40000 ALTER TABLE `build` DISABLE KEYS */;
/*!40000 ALTER TABLE `build` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config_ldap`
--

DROP TABLE IF EXISTS `config_ldap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_ldap` (
  `config_ldap_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `config_ldap_host` varchar(255) DEFAULT NULL,
  `config_ldap_port` int(11) unsigned NOT NULL DEFAULT '389',
  `config_ldap_admin_login` varchar(255) NOT NULL DEFAULT '',
  `config_ldap_admin_password` blob NOT NULL,
  `config_ldap_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`config_ldap_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config_ldap`
--

LOCK TABLES `config_ldap` WRITE;
/*!40000 ALTER TABLE `config_ldap` DISABLE KEYS */;
INSERT INTO `config_ldap` VALUES (1,'',389,'','',0);
/*!40000 ALTER TABLE `config_ldap` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dolzh`
--

DROP TABLE IF EXISTS `dolzh`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dolzh` (
  `dolzh_id` binary(16) NOT NULL,
  `dolzh_name` varchar(255) NOT NULL,
  PRIMARY KEY (`dolzh_id`),
  UNIQUE KEY `dolzh_name` (`dolzh_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dolzh`
--

LOCK TABLES `dolzh` WRITE;
/*!40000 ALTER TABLE `dolzh` DISABLE KEYS */;
/*!40000 ALTER TABLE `dolzh` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee`
--

DROP TABLE IF EXISTS `employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee` (
  `employee_id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` binary(16) NOT NULL,
  `dolzh_id` binary(16) NOT NULL,
  `podraz_id` binary(16) NOT NULL,
  `build_id` binary(16) DEFAULT NULL,
  `employee_begin` date NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `updated_by` varchar(255) NOT NULL,
  PRIMARY KEY (`employee_id`),
  UNIQUE KEY `person_id` (`person_id`),
  KEY `dolzh_id_employee` (`dolzh_id`),
  KEY `podraz_id_employee` (`podraz_id`),
  KEY `build_id_employee` (`build_id`),
  CONSTRAINT `build_id_employee` FOREIGN KEY (`build_id`) REFERENCES `build` (`build_id`),
  CONSTRAINT `dolzh_id_employee` FOREIGN KEY (`dolzh_id`) REFERENCES `dolzh` (`dolzh_id`),
  CONSTRAINT `person_id_employee` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`),
  CONSTRAINT `podraz_id_employee` FOREIGN KEY (`podraz_id`) REFERENCES `podraz` (`podraz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee`
--

LOCK TABLES `employee` WRITE;
/*!40000 ALTER TABLE `employee` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_history`
--

DROP TABLE IF EXISTS `employee_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_history` (
  `employee_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` binary(16) NOT NULL,
  `dolzh_id` binary(16) NOT NULL,
  `podraz_id` binary(16) NOT NULL,
  `build_id` binary(16) DEFAULT NULL,
  `employee_history_begin` date NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `updated_by` varchar(255) NOT NULL,
  PRIMARY KEY (`employee_history_id`),
  KEY `person_id_employee_history` (`person_id`),
  KEY `dolzh_id_employee_history` (`dolzh_id`),
  KEY `podraz_id_employee_history` (`podraz_id`),
  KEY `build_id_employee_history` (`build_id`),
  CONSTRAINT `build_id_employee_history` FOREIGN KEY (`build_id`) REFERENCES `build` (`build_id`),
  CONSTRAINT `dolzh_id_employee_history` FOREIGN KEY (`dolzh_id`) REFERENCES `dolzh` (`dolzh_id`),
  CONSTRAINT `person_id_employee_history` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`),
  CONSTRAINT `podraz_id_employee_history` FOREIGN KEY (`podraz_id`) REFERENCES `podraz` (`podraz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_history`
--

LOCK TABLES `employee_history` WRITE;
/*!40000 ALTER TABLE `employee_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration`
--

LOCK TABLES `migration` WRITE;
/*!40000 ALTER TABLE `migration` DISABLE KEYS */;
INSERT INTO `migration` VALUES ('m000000_000000_base',1507473193),('m130524_201442_init',1507473195),('m170604_042824_reportLoader',1507473195),('m170610_085215_session',1507473195),('m170913_050424_configLdap',1507473195),('m170922_104145_rbac',1507473196),('m170929_074819_employee',1507473198);
/*!40000 ALTER TABLE `migration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parttime`
--

DROP TABLE IF EXISTS `parttime`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parttime` (
  `parttime_id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` binary(16) NOT NULL,
  `dolzh_id` binary(16) NOT NULL,
  `podraz_id` binary(16) NOT NULL,
  `build_id` binary(16) DEFAULT NULL,
  `parttime_begin` date NOT NULL,
  `parttime_end` date DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `updated_by` varchar(255) NOT NULL,
  PRIMARY KEY (`parttime_id`),
  KEY `person_id_parttime` (`person_id`),
  KEY `dolzh_id_parttime` (`dolzh_id`),
  KEY `podraz_id_parttime` (`podraz_id`),
  KEY `build_id_parttime` (`build_id`),
  CONSTRAINT `build_id_parttime` FOREIGN KEY (`build_id`) REFERENCES `build` (`build_id`),
  CONSTRAINT `dolzh_id_parttime` FOREIGN KEY (`dolzh_id`) REFERENCES `dolzh` (`dolzh_id`),
  CONSTRAINT `person_id_parttime` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`),
  CONSTRAINT `podraz_id_parttime` FOREIGN KEY (`podraz_id`) REFERENCES `podraz` (`podraz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parttime`
--

LOCK TABLES `parttime` WRITE;
/*!40000 ALTER TABLE `parttime` DISABLE KEYS */;
/*!40000 ALTER TABLE `parttime` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person`
--

DROP TABLE IF EXISTS `person`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person` (
  `person_id` binary(16) NOT NULL,
  `person_code` int(11) NOT NULL AUTO_INCREMENT,
  `person_fullname` varchar(255) NOT NULL,
  `person_username` varchar(255) NOT NULL,
  `person_auth_key` char(32) NOT NULL,
  `person_password_hash` varchar(255) NOT NULL,
  `person_email` varchar(255) DEFAULT NULL,
  `person_hired` date DEFAULT NULL,
  `person_fired` date DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `updated_by` varchar(255) NOT NULL,
  PRIMARY KEY (`person_id`),
  UNIQUE KEY `person_username` (`person_username`),
  UNIQUE KEY `person_code` (`person_code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person`
--

LOCK TABLES `person` WRITE;
/*!40000 ALTER TABLE `person` DISABLE KEYS */;
INSERT INTO `person` VALUES ('�Y�$�5�X\0�Bt�',1,'Администратор','admin','vcsWYN_8yf_U3HhIcpsI0FfCJxqpHmbB','$2y$13$13Q79tT/2WwTzZ3/Nvaoi.oEHw2CbQj2xlngA7UxYEON2xGy8znb2','admin@mm.ru',NULL,NULL,1507473195,1507473195,'system','system');
/*!40000 ALTER TABLE `person` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `podraz`
--

DROP TABLE IF EXISTS `podraz`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `podraz` (
  `podraz_id` binary(16) NOT NULL,
  `podraz_name` varchar(255) NOT NULL,
  PRIMARY KEY (`podraz_id`),
  UNIQUE KEY `podraz_name` (`podraz_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `podraz`
--

LOCK TABLES `podraz` WRITE;
/*!40000 ALTER TABLE `podraz` DISABLE KEYS */;
/*!40000 ALTER TABLE `podraz` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profile` (
  `profile_id` binary(16) NOT NULL,
  `profile_inn` char(12) DEFAULT NULL,
  `profile_dr` date DEFAULT NULL,
  `profile_pol` tinyint(1) DEFAULT NULL,
  `profile_snils` char(11) DEFAULT NULL,
  `profile_address` varchar(400) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `updated_by` varchar(255) NOT NULL,
  PRIMARY KEY (`profile_id`),
  UNIQUE KEY `profile_inn` (`profile_inn`),
  UNIQUE KEY `profile_snils` (`profile_snils`),
  CONSTRAINT `person_profile` FOREIGN KEY (`profile_id`) REFERENCES `person` (`person_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile`
--

LOCK TABLES `profile` WRITE;
/*!40000 ALTER TABLE `profile` DISABLE KEYS */;
/*!40000 ALTER TABLE `profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_loader`
--

DROP TABLE IF EXISTS `report_loader`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_loader` (
  `rl_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rl_process_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `rl_report_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `rl_report_filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `rl_report_displayname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `rl_report_type` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rl_status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `rl_percent` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `rl_start` datetime NOT NULL,
  PRIMARY KEY (`rl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_loader`
--

LOCK TABLES `report_loader` WRITE;
/*!40000 ALTER TABLE `report_loader` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_loader` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `expire` int(11) DEFAULT NULL,
  `data` blob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session`
--

LOCK TABLES `session` WRITE;
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
/*!40000 ALTER TABLE `session` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-10-08 19:34:04
