-- MySQL dump 10.13  Distrib 5.6.17, for Win64 (x86_64)
--
-- Host: localhost    Database: wkportal
-- ------------------------------------------------------
-- Server version	5.6.36-log

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
  CONSTRAINT `item_name` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `person` (`person_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_assignment`
--

LOCK TABLES `auth_assignment` WRITE;
/*!40000 ALTER TABLE `auth_assignment` DISABLE KEYS */;
INSERT INTO `auth_assignment` VALUES ('ˀ?��s�+4y�','Administrator',1514442290);
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
  `data` blob,
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
INSERT INTO `auth_item` VALUES ('Administrator',1,0,NULL,'Администратор системы',NULL,NULL,1514442290,1514442290),('authorized',2,1,NULL,'Права авторизованного пользователя',NULL,NULL,1514442290,1514442290),('baseAdministrator',1,1,NULL,'Администратор базовой конфигурации',NULL,NULL,1514442290,1514442290),('baseAuthorized',1,1,NULL,'Авторизованный пользователь',NULL,NULL,1514442290,1514442290),('baseBuildEdit',1,1,NULL,'Оператор справочника \"Здания\"',NULL,NULL,1514442290,1514442290),('baseDolzhEdit',1,1,NULL,'Оператор справочника \"Должности\"',NULL,NULL,1514442290,1514442290),('basePodrazEdit',1,1,NULL,'Оператор справочника \"Подразделения\"',NULL,NULL,1514442290,1514442290),('buildEdit',2,1,NULL,'Редактирование справочника \"Здания\"',NULL,NULL,1514442290,1514442290),('dolzhEdit',2,1,NULL,'Редактирование справочника \"Должности\"',NULL,NULL,1514442290,1514442290),('ipContactEdit',2,0,NULL,'Редактирование контактов IP Телефонии',NULL,NULL,1514442310,1514442310),('ipContactOperator',1,0,NULL,'Оператор контактов IP Телефонии',NULL,NULL,1514442310,1514442310),('ofomsPrik',2,0,NULL,'Разрешение прикрепления пациентов к врачам ЛПУ на портале ОФОМС',NULL,NULL,1514442305,1514442305),('ofomsPrikList',2,0,NULL,'Разрешение прикрепления списком пациентов к врачам ЛПУ на портале ОФОМС',NULL,NULL,1514442305,1514442305),('ofomsPrikListNGP',1,0,NULL,'Пакетное прикрепление списком пациентов к врачам ЛПУ на портале ОФОМС',NULL,NULL,1514442305,1514442305),('ofomsPrikNGP',1,0,NULL,'Прикрепление пациентов к врачам ЛПУ на портале ОФОМС',NULL,NULL,1514442305,1514442305),('ofomsView',2,0,NULL,'Разрешение проверки полисов на портале ОФОМС',NULL,NULL,1514442305,1514442305),('ofomsViewNGP',1,0,NULL,'Проверка полисов на портале ОФОМС',NULL,NULL,1514442305,1514442305),('podrazEdit',2,1,NULL,'Редактирование справочника \"Подразделения\"',NULL,NULL,1514442290,1514442290),('roleEdit',2,1,NULL,'Редактирование ролей пользователя',NULL,NULL,1514442290,1514442290),('roleOperator',1,1,NULL,'Оператор менеджера ролей',NULL,NULL,1514442290,1514442290),('tilesEdit',2,0,NULL,'Редактирование плиток на главной странице',NULL,NULL,1514442305,1514442305),('tilesOperator',1,0,NULL,'Оператор плиток на главной странице',NULL,NULL,1514442305,1514442305),('userEdit',2,1,NULL,'Редактирование пользователей',NULL,NULL,1514442290,1514442290),('userOperator',1,1,NULL,'Оператор менеджера пользователей',NULL,NULL,1514442290,1514442290);
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
INSERT INTO `auth_item_child` VALUES ('baseAuthorized','authorized'),('Administrator','baseAdministrator'),('baseAdministrator','baseAuthorized'),('baseAdministrator','baseBuildEdit'),('baseAdministrator','baseDolzhEdit'),('baseAdministrator','basePodrazEdit'),('baseBuildEdit','buildEdit'),('baseDolzhEdit','dolzhEdit'),('ipContactOperator','ipContactEdit'),('Administrator','ipContactOperator'),('ofomsPrikNGP','ofomsPrik'),('ofomsPrikListNGP','ofomsPrikList'),('Administrator','ofomsPrikListNGP'),('Administrator','ofomsPrikNGP'),('ofomsViewNGP','ofomsView'),('Administrator','ofomsViewNGP'),('basePodrazEdit','podrazEdit'),('roleOperator','roleEdit'),('baseAdministrator','roleOperator'),('tilesOperator','tilesEdit'),('Administrator','tilesOperator'),('userOperator','userEdit'),('baseAdministrator','userOperator');
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
-- Table structure for table `cardlist`
--

DROP TABLE IF EXISTS `cardlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cardlist` (
  `cardlist_id` int(11) NOT NULL AUTO_INCREMENT,
  `cardlist_page` varchar(255) NOT NULL,
  `cardlist_title` varchar(255) NOT NULL,
  `cardlist_description` varchar(255) DEFAULT NULL,
  `cardlist_style` varchar(255) DEFAULT NULL,
  `cardlist_link` varchar(255) NOT NULL,
  `cardlist_icon` varchar(255) DEFAULT NULL,
  `cardlist_roles` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`cardlist_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cardlist`
--

LOCK TABLES `cardlist` WRITE;
/*!40000 ALTER TABLE `cardlist` DISABLE KEYS */;
INSERT INTO `cardlist` VALUES (1,'wkportal-backend|site/index','Плитки на главной странице','Добавление/Редактирование/Удаление плиток','wk-blue-style','FrontendUrlManager[tiles]','fa fa-table','tilesEdit'),(2,'wkportal-backend|site/index','Портал ОФОМС','Проверка полисов на портале ОФОМС. Прикрепление пациентов к врачам ЛПУ','wk-blue-style','FrontendUrlManager[ofoms]','fa fa-users','ofomsView'),(3,'wkportal-backend|configuration/config/index','Настройки портала ОФОМС','','wk-blue-style','FrontendUrlManager[ofoms-config]','fa fa-wrench','Administrator'),(4,'wkportal-backend|site/index','Контакты IP телефонии','Добавление/Редактирование/Удаление контактов для IP телефонии','wk-blue-style','FrontendUrlManager[ip-contact]','fa fa-phone-square','ipContactOperator');
/*!40000 ALTER TABLE `cardlist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config_common`
--

DROP TABLE IF EXISTS `config_common`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_common` (
  `config_common_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `config_common_portal_mail` varchar(255) DEFAULT NULL,
  `config_common_mail_administrators` varchar(255) DEFAULT NULL,
  `config_common_footer_company` varchar(255) DEFAULT NULL,
  `config_common_footer_addition` varchar(255) DEFAULT NULL,
  `config_common_christmas` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`config_common_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config_common`
--

LOCK TABLES `config_common` WRITE;
/*!40000 ALTER TABLE `config_common` DISABLE KEYS */;
INSERT INTO `config_common` VALUES (1,NULL,NULL,NULL,NULL,0);
/*!40000 ALTER TABLE `config_common` ENABLE KEYS */;
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
-- Table structure for table `config_ofoms`
--

DROP TABLE IF EXISTS `config_ofoms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_ofoms` (
  `config_ofoms_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `config_ofoms_url` varchar(255) DEFAULT NULL,
  `config_ofoms_url_prik` varchar(255) DEFAULT NULL,
  `config_ofoms_login` varchar(255) NOT NULL DEFAULT '',
  `config_ofoms_password` blob NOT NULL,
  `config_ofoms_remote_host_name` varchar(255) DEFAULT NULL,
  `config_ofoms_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`config_ofoms_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config_ofoms`
--

LOCK TABLES `config_ofoms` WRITE;
/*!40000 ALTER TABLE `config_ofoms` DISABLE KEYS */;
INSERT INTO `config_ofoms` VALUES (1,'',NULL,'','',NULL,0);
/*!40000 ALTER TABLE `config_ofoms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doh_files`
--

DROP TABLE IF EXISTS `doh_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doh_files` (
  `doh_files_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_type` varchar(255) NOT NULL,
  `file_path` varchar(400) NOT NULL,
  `file_description` varchar(400) NOT NULL,
  PRIMARY KEY (`doh_files_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doh_files`
--

LOCK TABLES `doh_files` WRITE;
/*!40000 ALTER TABLE `doh_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `doh_files` ENABLE KEYS */;
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
  `employee_begin` date NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `updated_by` varchar(255) NOT NULL,
  PRIMARY KEY (`employee_id`),
  UNIQUE KEY `person_id` (`person_id`),
  KEY `dolzh_id_employee` (`dolzh_id`),
  KEY `podraz_id_employee` (`podraz_id`),
  CONSTRAINT `dolzh_id_employee` FOREIGN KEY (`dolzh_id`) REFERENCES `dolzh` (`dolzh_id`),
  CONSTRAINT `person_id_employee` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`) ON DELETE CASCADE,
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
  `employee_history_begin` date NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `updated_by` varchar(255) NOT NULL,
  PRIMARY KEY (`employee_history_id`),
  UNIQUE KEY `idx_employee_history` (`person_id`,`employee_history_begin`),
  KEY `dolzh_id_employee_history` (`dolzh_id`),
  KEY `podraz_id_employee_history` (`podraz_id`),
  CONSTRAINT `dolzh_id_employee_history` FOREIGN KEY (`dolzh_id`) REFERENCES `dolzh` (`dolzh_id`),
  CONSTRAINT `person_id_employee_history` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`) ON DELETE CASCADE,
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
-- Table structure for table `employee_history_build`
--

DROP TABLE IF EXISTS `employee_history_build`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_history_build` (
  `ehb_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_history_id` int(11) NOT NULL,
  `build_id` binary(16) NOT NULL,
  `employee_history_build_deactive` date DEFAULT NULL,
  PRIMARY KEY (`ehb_id`),
  UNIQUE KEY `idx_employee_history_build` (`employee_history_id`,`build_id`),
  KEY `build_id_employee_history_build` (`build_id`),
  CONSTRAINT `build_id_employee_history_build` FOREIGN KEY (`build_id`) REFERENCES `build` (`build_id`),
  CONSTRAINT `employee_history_id_employee_history_build` FOREIGN KEY (`employee_history_id`) REFERENCES `employee_history` (`employee_history_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_history_build`
--

LOCK TABLES `employee_history_build` WRITE;
/*!40000 ALTER TABLE `employee_history_build` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_history_build` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `handler`
--

DROP TABLE IF EXISTS `handler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `handler` (
  `handler_id` int(11) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(64) NOT NULL,
  `handler_name` varchar(255) NOT NULL,
  `handler_description` varchar(400) NOT NULL,
  `handler_at` int(11) NOT NULL,
  `handler_percent` int(11) unsigned NOT NULL DEFAULT '0',
  `handler_status` tinyint(1) NOT NULL DEFAULT '1',
  `handler_done_time` float DEFAULT NULL,
  `handler_used_memory` int(11) DEFAULT NULL,
  `handler_short_report` varchar(400) DEFAULT NULL,
  `handler_files` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`handler_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `handler`
--

LOCK TABLES `handler` WRITE;
/*!40000 ALTER TABLE `handler` DISABLE KEYS */;
/*!40000 ALTER TABLE `handler` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `handler_files`
--

DROP TABLE IF EXISTS `handler_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `handler_files` (
  `doh_files_id` int(11) NOT NULL,
  `handler_id` int(11) NOT NULL,
  UNIQUE KEY `idx_handler_files` (`doh_files_id`,`handler_id`),
  KEY `handler_files_handler` (`handler_id`),
  CONSTRAINT `handler_files_doh_files` FOREIGN KEY (`doh_files_id`) REFERENCES `doh_files` (`doh_files_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `handler_files_handler` FOREIGN KEY (`handler_id`) REFERENCES `handler` (`handler_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `handler_files`
--

LOCK TABLES `handler_files` WRITE;
/*!40000 ALTER TABLE `handler_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `handler_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ip_contact`
--

DROP TABLE IF EXISTS `ip_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ip_contact` (
  `ip_contact_id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_contact_name` varchar(255) NOT NULL,
  `ip_contact_phone` varchar(255) NOT NULL,
  `ip_contact_groups_id` int(11) NOT NULL,
  `ip_contact_phone2` varchar(255) DEFAULT NULL,
  `ip_contact_phone3` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ip_contact_id`),
  UNIQUE KEY `idx_contact_groups` (`ip_contact_name`,`ip_contact_phone`,`ip_contact_groups_id`),
  KEY `ip_contact_groups` (`ip_contact_groups_id`),
  CONSTRAINT `ip_contact_groups` FOREIGN KEY (`ip_contact_groups_id`) REFERENCES `ip_contact_groups` (`ip_contact_groups_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ip_contact`
--

LOCK TABLES `ip_contact` WRITE;
/*!40000 ALTER TABLE `ip_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `ip_contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ip_contact_groups`
--

DROP TABLE IF EXISTS `ip_contact_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ip_contact_groups` (
  `ip_contact_groups_id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_contact_groups_name` varchar(255) NOT NULL,
  PRIMARY KEY (`ip_contact_groups_id`),
  UNIQUE KEY `ip_contact_groups_name` (`ip_contact_groups_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ip_contact_groups`
--

LOCK TABLES `ip_contact_groups` WRITE;
/*!40000 ALTER TABLE `ip_contact_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `ip_contact_groups` ENABLE KEYS */;
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
INSERT INTO `migration` VALUES ('m000000_000000_base',1514442276),('m130524_201442_init',1514442286),('m170604_042824_reportLoader',1514442289),('m170610_085215_session',1514442290),('m170913_050424_configLdap',1514442290),('m170922_104145_rbac',1514442290),('m170929_074819_employee',1514442305),('m171031_105453_cardlist',1514442305),('m171031_113027_cardlist_data',1514442305),('m171102_042755_rbac',1514442305),('m171102_111209_ofoms',1514442306),('m171104_052922_doh',1514442308),('m171111_052153_tiles',1514442309),('m171201_082438_configCommon',1514442309),('m171220_111106_ip_contacts',1514442311),('m171223_161003_christmas',1514442311),('m171226_053601_multi_phones_ip_contact',1514442312),('m171226_060809_add_phones_to_user_profile',1514442313),('m171226_083412_change_ip_contact_unique_index',1514442314),('yii\\queue\\db\\migrations\\M161119140200Queue',1514442286),('yii\\queue\\db\\migrations\\M170307170300Later',1514442287),('yii\\queue\\db\\migrations\\M170509001400Retry',1514442288),('yii\\queue\\db\\migrations\\M170601155600Priority',1514442289);
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
  `parttime_begin` date NOT NULL,
  `parttime_end` date DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `updated_by` varchar(255) NOT NULL,
  PRIMARY KEY (`parttime_id`),
  UNIQUE KEY `idx_parttime` (`person_id`,`dolzh_id`,`podraz_id`,`parttime_begin`,`parttime_end`),
  KEY `dolzh_id_parttime` (`dolzh_id`),
  KEY `podraz_id_parttime` (`podraz_id`),
  CONSTRAINT `dolzh_id_parttime` FOREIGN KEY (`dolzh_id`) REFERENCES `dolzh` (`dolzh_id`),
  CONSTRAINT `person_id_parttime` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`) ON DELETE CASCADE,
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
-- Table structure for table `parttime_build`
--

DROP TABLE IF EXISTS `parttime_build`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parttime_build` (
  `pb` int(11) NOT NULL AUTO_INCREMENT,
  `parttime_id` int(11) NOT NULL,
  `build_id` binary(16) NOT NULL,
  `parttime_build_deactive` date DEFAULT NULL,
  PRIMARY KEY (`pb`),
  UNIQUE KEY `idx_parttime_build` (`parttime_id`,`build_id`),
  KEY `build_id_parttime_build` (`build_id`),
  CONSTRAINT `build_id_parttime_build` FOREIGN KEY (`build_id`) REFERENCES `build` (`build_id`),
  CONSTRAINT `parttime_id_parttime_build` FOREIGN KEY (`parttime_id`) REFERENCES `parttime` (`parttime_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parttime_build`
--

LOCK TABLES `parttime_build` WRITE;
/*!40000 ALTER TABLE `parttime_build` DISABLE KEYS */;
/*!40000 ALTER TABLE `parttime_build` ENABLE KEYS */;
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
INSERT INTO `person` VALUES ('ˀ?��s�+4y�',1,'Администратор','admin','ATJsIOsOO9_xnpiE1O_Sg2t8R0Md2hOp','$2y$13$8hoFqMvhqbF8cPeoLau1e.sNoPr4gOl/vYcjCCy/PiKDlSFoS4x0C','admin@mm.ru',NULL,NULL,1514442285,1514442285,'system','system');
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
  `profile_phone` varchar(255) DEFAULT NULL,
  `profile_internal_phone` varchar(255) DEFAULT NULL,
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
-- Table structure for table `queue`
--

DROP TABLE IF EXISTS `queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel` varchar(255) NOT NULL,
  `job` blob NOT NULL,
  `pushed_at` int(11) NOT NULL,
  `ttr` int(11) NOT NULL,
  `delay` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) unsigned NOT NULL DEFAULT '1024',
  `reserved_at` int(11) DEFAULT NULL,
  `attempt` int(11) DEFAULT NULL,
  `done_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `channel` (`channel`),
  KEY `reserved_at` (`reserved_at`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `queue`
--

LOCK TABLES `queue` WRITE;
/*!40000 ALTER TABLE `queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `queue` ENABLE KEYS */;
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

--
-- Table structure for table `tiles`
--

DROP TABLE IF EXISTS `tiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tiles` (
  `tiles_id` int(11) NOT NULL AUTO_INCREMENT,
  `tiles_name` varchar(255) NOT NULL,
  `tiles_description` varchar(400) DEFAULT NULL,
  `tiles_link` varchar(255) NOT NULL,
  `tiles_thumbnail` varchar(255) DEFAULT NULL,
  `tiles_icon` varchar(255) DEFAULT NULL,
  `tiles_icon_color` varchar(255) DEFAULT NULL,
  `tiles_keywords` varchar(255) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `updated_by` varchar(255) NOT NULL,
  PRIMARY KEY (`tiles_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tiles`
--

LOCK TABLES `tiles` WRITE;
/*!40000 ALTER TABLE `tiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `tiles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-12-28 11:26:04
