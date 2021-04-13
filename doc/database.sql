-- MySQL dump 10.13  Distrib 5.7.32, for osx10.12 (x86_64)
--
-- Host: localhost    Database: halo
-- ------------------------------------------------------
-- Server version	5.7.32

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
-- Table structure for table `activity`
--

DROP TABLE IF EXISTS `activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity` (
  `activityId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Autocreated',
  `activityName` varchar(50) NOT NULL COMMENT 'Autocreated',
  `activityDescription` varchar(191) NOT NULL,
  PRIMARY KEY (`activityId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity`
--

LOCK TABLES `activity` WRITE;
/*!40000 ALTER TABLE `activity` DISABLE KEYS */;
INSERT INTO `activity` VALUES (1,'login','logged in'),(2,'logout','logged out');
/*!40000 ALTER TABLE `activity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activityLog`
--

DROP TABLE IF EXISTS `activityLog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activityLog` (
  `activityLogTimestamp` datetime NOT NULL,
  `activityLogId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Autocreated',
  `userId` int(10) unsigned NOT NULL,
  `activityId` int(10) unsigned NOT NULL COMMENT 'Autocreated',
  PRIMARY KEY (`activityLogId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activityLog`
--

LOCK TABLES `activityLog` WRITE;
/*!40000 ALTER TABLE `activityLog` DISABLE KEYS */;
INSERT INTO `activityLog` VALUES ('2021-03-19 19:07:47',1,1,1);
/*!40000 ALTER TABLE `activityLog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deployment`
--

DROP TABLE IF EXISTS `deployment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deployment` (
  `deploymentId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `deploymentCommitDate` datetime NOT NULL,
  `deploymentDate` datetime NOT NULL,
  `deploymentCommitMessage` varchar(765) NOT NULL,
  `deploymentCommitAuthor` varchar(255) DEFAULT NULL,
  `deploymentCommitSha` varchar(256) NOT NULL,
  PRIMARY KEY (`deploymentId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deployment`
--

LOCK TABLES `deployment` WRITE;
/*!40000 ALTER TABLE `deployment` DISABLE KEYS */;
INSERT INTO `deployment` VALUES (2,'2021-03-07 16:58:33','2021-03-19 19:07:36','Fix Corsican language','Henno Täht','4fe454d');
/*!40000 ALTER TABLE `deployment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `settingName` varchar(255) NOT NULL,
  `settingValue` varchar(765) DEFAULT NULL,
  PRIMARY KEY (`settingName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES ('projectVersion','4fe454d'),('translationUpdateLastRun','2021-03-19 19:07:36');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `translationLanguages`
--

DROP TABLE IF EXISTS `translationLanguages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translationLanguages` (
  `translationLanguageCode` varchar(255) NOT NULL,
  `translationLanguageName` varchar(255) NOT NULL,
  PRIMARY KEY (`translationLanguageCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `translationLanguages`
--

LOCK TABLES `translationLanguages` WRITE;
/*!40000 ALTER TABLE `translationLanguages` DISABLE KEYS */;
INSERT INTO `translationLanguages` VALUES ('af','Afrikaans'),('am','Amharic'),('ar','Arabic'),('az','Azerbaijani'),('be','Belarusian'),('bg','Bulgarian'),('bn','Bengali'),('bs','Bosnian'),('ca','Catalan'),('ceb','Cebuano'),('co','Corsican'),('cs','Czech'),('cy','Welsh'),('da','Danish'),('de','German'),('el','Greek'),('en','English'),('eo','Esperanto'),('es','Spanish'),('et','Estonian'),('eu','Basque'),('fa','Persian'),('fi','Finnish'),('fr','French'),('fy','Frisian'),('ga','Irish'),('gd','Scots Gaelic'),('gl','Galician'),('gu','Gujarati'),('ha','Hausa'),('haw','Hawaiian'),('he','Hebrew'),('hi','Hindi'),('hmn','Hmong'),('hr','Croatian'),('ht','Haitian'),('hu','Hungarian'),('hy','Armenian'),('id','Indonesian'),('ig','Igbo'),('is','Icelandic'),('it','Italian'),('ja','Japanese'),('jv','Javanese'),('ka','Georgian'),('kk','Kazakh'),('km','Khmer'),('kn','Kannada'),('ko','Korean'),('ku','Kurdish'),('ky','Kyrgyz'),('la','Latin'),('lb','Luxembourgish'),('lo','Lao'),('lt','Lithuanian'),('lv','Latvian'),('mg','Malagasy'),('mi','Maori'),('mk','Macedonian'),('ml','Malayalam'),('mn','Mongolian'),('mr','Marathi'),('ms','Malay'),('mt','Maltese'),('my','Myanmar'),('ne','Nepali'),('nl','Dutch'),('no','Norwegian'),('ny','Nyanja (Chichewa)'),('or','Odia (Oriya)'),('pa','Punjabi'),('pl','Polish'),('ps','Pashto'),('pt','Portuguese'),('ro','Romanian'),('ru','Russian'),('rw','Kinyarwanda'),('sd','Sindhi'),('si','Sinhala (Sinhalese)'),('sk','Slovak'),('sl','Slovenian'),('sm','Samoan'),('sn','Shona'),('so','Somali'),('sq','Albanian'),('sr','Serbian'),('st','Sesotho'),('su','Sundanese'),('sv','Swedish'),('sw','Swahili'),('ta','Tamil'),('te','Telugu'),('tg','Tajik'),('th','Thai'),('tk','Turkmen'),('tl','Tagalog (Filipino)'),('tr','Turkish'),('tt','Tatar'),('ug','Uyghur'),('uk','Ukrainian'),('ur','Urdu'),('uz','Uzbek'),('vi','Vietnamese'),('xh','Xhosa'),('yi','Yiddish'),('yo','Yoruba'),('zh','Chinese'),('zu','Zulu');
/*!40000 ALTER TABLE `translationLanguages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `translations`
--

DROP TABLE IF EXISTS `translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translations` (
  `translationId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `translationPhrase` varchar(765) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `translationState` varchar(255) NOT NULL DEFAULT 'existsInCode',
  `TranslationSource` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`translationId`),
  UNIQUE KEY `translations_translationPhrase_uindex` (`translationPhrase`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `translations`
--

LOCK TABLES `translations` WRITE;
/*!40000 ALTER TABLE `translations` DISABLE KEYS */;
INSERT INTO `translations` VALUES (1,'Invalid username','existsInCode',NULL),(2,'Invalid password','existsInCode',NULL),(3,'User already exists','existsInCode',NULL),(4,'You cannot delete yourself','existsInCode',NULL),(5,'Server returned response in an unexpected format','existsInCode',NULL),(6,'Forbidden','existsInCode',NULL),(7,'Server returned an error. Please try again later','existsInCode',NULL),(8,'Module Name','existsInCode',NULL),(9,'Access denied','existsInCode',NULL),(10,'Wrong username or password','existsInCode',NULL),(11,'Please sign in','existsInCode',NULL),(12,'Email','existsInCode',NULL),(13,'Password','existsInCode',NULL),(14,'Sign in','existsInCode',NULL),(15,'Users','existsInCode',NULL),(16,'Logs','existsInCode',NULL),(17,'Halo','existsInCode',NULL),(18,'Translations','existsInCode',NULL),(19,'Admin','existsInCode',NULL),(20,'Disabled','existsInCode',NULL),(21,'Action','existsInCode',NULL),(22,'Another action','existsInCode',NULL),(23,'Something else here','existsInCode',NULL),(24,'Logout','existsInCode',NULL),(25,'Oops...','existsInCode',NULL),(26,'Close','existsInCode',NULL),(27,'Time','existsInCode',NULL),(28,'User','existsInCode',NULL),(29,'Activity','existsInCode',NULL),(30,'Add','existsInCode',NULL),(31,'Name','existsInCode',NULL),(32,'Edit user','existsInCode',NULL),(33,'Set to 1 if user must be admin','existsInCode',NULL),(34,'Save changes','existsInCode',NULL),(35,'Are you sure?','existsInCode',NULL),(36,'Phrase','existsInCode',NULL),(37,'Untranslated','existsInCode',NULL),(38,'Search','existsInCode',NULL),(39,'Languages','existsInCode',NULL),(40,'Select language','existsInCode',NULL),(41,'Google translates < 5000 chars at a time','existsInCode',NULL),(42,'Select language first','existsInCode',NULL),(43,'Are you really sure you want to remove the language %%% and destroy its translations?','existsInCode',NULL);
/*!40000 ALTER TABLE `translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `userId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userName` varchar(191) NOT NULL,
  `userEmail` varchar(191) NOT NULL,
  `userIsAdmin` tinyint(4) NOT NULL DEFAULT '0',
  `userPassword` varchar(191) NOT NULL,
  `userDeleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'Demo User','demo@example.com',1,'$2y$10$vTje.ndUFKHyuotY99iYkO.2aHJUgOsy2x0RMXP1UmrTe6CQsKbtm',0);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-04-13 11:09:57
