-- MySQL dump 10.13  Distrib 5.7.23, for Linux (x86_64)
--
-- Host: localhost    Database: interview
-- ------------------------------------------------------
-- Server version	5.7.23-0ubuntu0.16.04.1

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
-- Table structure for table `int_admins`
--

DROP TABLE IF EXISTS `int_admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `int_admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `int_admins`
--

LOCK TABLES `int_admins` WRITE;
/*!40000 ALTER TABLE `int_admins` DISABLE KEYS */;
/*!40000 ALTER TABLE `int_admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `int_adminvotes`
--

DROP TABLE IF EXISTS `int_adminvotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `int_adminvotes` (
  `au_id` varchar(8) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` tinyint(1) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`au_id`),
  UNIQUE KEY `PK_COMBO_VOTE` (`admin_id`,`user_id`),
  UNIQUE KEY `au_id` (`au_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `int_adminvotes`
--

LOCK TABLES `int_adminvotes` WRITE;
/*!40000 ALTER TABLE `int_adminvotes` DISABLE KEYS */;
/*!40000 ALTER TABLE `int_adminvotes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `int_applicant`
--

DROP TABLE IF EXISTS `int_applicant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `int_applicant` (
  `a_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) NOT NULL,
  `cdkey` varchar(9) NOT NULL,
  `pseudonym` text NOT NULL,
  `interview_status` varchar(18) DEFAULT 'preliminary',
  `dm_reason` text,
  `cases_against` text,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `C_ID` (`a_id`),
  UNIQUE KEY `Applicant` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `int_applicant`
--

LOCK TABLES `int_applicant` WRITE;
/*!40000 ALTER TABLE `int_applicant` DISABLE KEYS */;
/*!40000 ALTER TABLE `int_applicant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `int_dm`
--

DROP TABLE IF EXISTS `int_dm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `int_dm` (
  `dm_id` int(11) NOT NULL AUTO_INCREMENT,
  `dm` varchar(25) NOT NULL,
  PRIMARY KEY (`dm_id`),
  UNIQUE KEY `id` (`dm_id`),
  UNIQUE KEY `user` (`dm`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `int_dm`
--

LOCK TABLES `int_dm` WRITE;
/*!40000 ALTER TABLE `int_dm` DISABLE KEYS */;
/*!40000 ALTER TABLE `int_dm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `int_dmapp`
--

DROP TABLE IF EXISTS `int_dmapp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `int_dmapp` (
  `Username` varchar(36) NOT NULL,
  `CDKey` varchar(20) NOT NULL,
  `Answer` text NOT NULL,
  `App Link` varchar(255) DEFAULT NULL,
  `PCs` text,
  `Cases Against` text,
  `Titania Vote` varchar(4) DEFAULT NULL,
  `Grumpy Vote` varchar(4) DEFAULT NULL,
  `Titania Comments` text,
  `Grumpy Comments` text,
  UNIQUE KEY `CD Key` (`CDKey`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `int_dmapp`
--

LOCK TABLES `int_dmapp` WRITE;
/*!40000 ALTER TABLE `int_dmapp` DISABLE KEYS */;
/*!40000 ALTER TABLE `int_dmapp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `int_qadm`
--

DROP TABLE IF EXISTS `int_qadm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `int_qadm` (
  `R_Q_ID` int(11) NOT NULL,
  `R_A_ID` varchar(64) NOT NULL,
  `R_DM_ID` varchar(64) NOT NULL,
  `Vote_Int` int(11) NOT NULL,
  `Vote_Comment` text,
  `Date_Created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `R_Q_ID` (`R_Q_ID`,`R_A_ID`,`R_DM_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `int_qadm`
--

LOCK TABLES `int_qadm` WRITE;
/*!40000 ALTER TABLE `int_qadm` DISABLE KEYS */;
/*!40000 ALTER TABLE `int_qadm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `int_question`
--

DROP TABLE IF EXISTS `int_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `int_question` (
  `q_id` int(11) NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `integral` tinyint(1) NOT NULL,
  `sequence` int(11) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `Q_ID` (`q_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `int_question`
--

LOCK TABLES `int_question` WRITE;
/*!40000 ALTER TABLE `int_question` DISABLE KEYS */;
INSERT INTO `int_question` VALUES (1,'Why do you want to be a DM? What are the essential attributes and skills that you believe make up a DM? Two paragraphs are permitted.\r\n',1,2,0,'2021-03-28 04:31:38'),(2,'What sort of aspect of DMing can you see yourself performing mostly and why?',1,1,0,'2021-03-28 04:34:10'),(3,'Which do you believe is more important, and why â€“ fairness or fun?',1,3,0,'2021-03-28 04:34:41'),(4,'What for you are the elements that make up a good roleplayer?',0,NULL,0,'2021-03-28 04:34:52'),(5,'Has there been a case that you feel was badly handled by the DMs, and if so, why?',0,NULL,0,'2021-03-28 04:35:53'),(6,'Describe a DM quest you\'d like to run.',0,NULL,0,'2021-03-28 04:36:44'),(7,'If you find Queen Titania breaking the rules player side - what do you do?',0,NULL,0,'2021-03-28 04:38:32'),(8,'Do you believe there is anything the current DM staff isn\'t doing, but should be?',0,NULL,0,'2021-03-28 04:38:50'),(9,'What do you most love about Arelith? What do you dislike about Arelith?',0,NULL,0,'2021-03-28 04:39:10'),(10,'A good friend of yours is involved in a case where their character is killed without rp by another charaacter who\'s a history of this sort of poor behavior. They send in a report and, after investigation, another DM gives the griefer a Ban. The next day your friend chats to you on discord about the situation and their frustration with it. What is your response?',0,NULL,0,'2021-03-28 04:42:36'),(11,'If you could change four things on the server, what would they be? Two paragraphs are permitted.',0,5,0,'2021-03-28 04:45:48'),(12,'You are talking to a player about a rulebreak and they request to speak to another DM. What do you do?',0,4,0,'2021-03-28 04:46:37'),(13,'You spot a character, a Paladin, in the underdark â€“ kissing a Drow. What action do you take?',0,NULL,0,'2021-03-28 04:47:16'),(14,'What place do you feel Forgotten Realms Lore has in Arelith?',0,NULL,0,'2021-03-28 04:48:23'),(15,'From an admin perspective, the single most valued quality in a DM is integrity. In the context of DMing, what does that mean to you?',0,NULL,0,'2021-03-28 04:48:43'),(16,'You are asked a question about a techinical matter that you aren\'t completely sure of the answer on due to it\'s obscurity by a player who\'s becoming frustrated. None of the other team are immediately reachable, what do you do?',0,NULL,0,'2021-03-28 04:49:44');
/*!40000 ALTER TABLE `int_question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `int_response`
--

DROP TABLE IF EXISTS `int_response`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `int_response` (
  `id` varchar(60) NOT NULL,
  `username` varchar(60) NOT NULL,
  `question_id` int(11) NOT NULL,
  `response` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `ID` (`id`),
  UNIQUE KEY `id_2` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `int_response`
--

LOCK TABLES `int_response` WRITE;
/*!40000 ALTER TABLE `int_response` DISABLE KEYS */;
/*!40000 ALTER TABLE `int_response` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `int_status`
--

DROP TABLE IF EXISTS `int_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `int_status` (
  `id` int(1) NOT NULL DEFAULT '1',
  `status` varchar(16) DEFAULT NULL,
  `first_round_end_date` date DEFAULT NULL,
  `second_round_start_date` date DEFAULT NULL,
  `second_round_end_date` date DEFAULT NULL,
  `date_modified` date NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `int_status`
--

LOCK TABLES `int_status` WRITE;
/*!40000 ALTER TABLE `int_status` DISABLE KEYS */;
INSERT INTO `int_status` VALUES (1,'round1','2022-02-28','2022-03-01','2022-03-08','2022-02-14');
/*!40000 ALTER TABLE `int_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `int_users`
--

DROP TABLE IF EXISTS `int_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `int_users` (
  `dm_id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(25) NOT NULL,
  `password` varchar(70) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dm_id`),
  UNIQUE KEY `id` (`dm_id`),
  UNIQUE KEY `user` (`user`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `int_users`
--

LOCK TABLES `int_users` WRITE;
/*!40000 ALTER TABLE `int_users` DISABLE KEYS */;
INSERT INTO `int_users` VALUES (1,'Queen Titania','$2y$10$ymh9786/Ox.hxnjPTY8fSeosFI8nH831Z5yq8bo8ziYEJRMNkZIeO','2021-03-26 15:25:50'),(2,'DM Tinkerbell','$2y$10$laorOvBZUNMQSfANlM6VuucrKC8gN6Foa8ZqL4z9vl.CprKJXKSnS','2019-12-11 05:00:00'),(3,'Grumpycat','$2y$10$zrGKr9Xqr4fD2ZuEsgdqFeaiVqC/Z6L65HdIC0Waqah0vjkVlZusi','2021-03-26 15:26:28'),(4,'DM Hoodoo','$2y$10$4lGavPzSdVuO1w33IVrXce0bIVx/yIf2DA7kvrDqj4lrU4KTxJACq','2021-03-26 15:27:15');
/*!40000 ALTER TABLE `int_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `int_votes`
--

DROP TABLE IF EXISTS `int_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `int_votes` (
  `Related_A_ID` varchar(64) NOT NULL,
  `Related_DM_ID` varchar(64) NOT NULL,
  `DM_Vote` varchar(3) NOT NULL COMMENT 'Yes/No',
  `Date Created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `Related_A_ID` (`Related_A_ID`,`Related_DM_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `int_votes`
--

LOCK TABLES `int_votes` WRITE;
/*!40000 ALTER TABLE `int_votes` DISABLE KEYS */;
/*!40000 ALTER TABLE `int_votes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-02-14 15:26:48
