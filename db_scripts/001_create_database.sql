CREATE DATABASE  IF NOT EXISTS `showthatyouhelp` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `showthatyouhelp`;
-- MySQL dump 10.13  Distrib 5.6.13, for Win32 (x86)
--
-- Host: 127.0.0.1    Database: showthatyouhelp
-- ------------------------------------------------------
-- Server version	5.5.20-log

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
-- Table structure for table `image`
--

DROP TABLE IF EXISTS `image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `image` (
  `image_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `image_folder` varchar(50) NOT NULL,
  `image_filename` varchar(255) NOT NULL,
  `image_fileext` varchar(10) NOT NULL,
  `image_created` datetime NOT NULL,
  PRIMARY KEY (`image_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site`
--

DROP TABLE IF EXISTS `site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `site_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `update`
--

DROP TABLE IF EXISTS `update`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `update` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(10) unsigned NOT NULL,
  `type` enum('text','link','video','photo') NOT NULL,
  `comment` text NOT NULL,
  `update_comment_count` int(10) unsigned NOT NULL DEFAULT '0',
  `update_delete` bit(1) NOT NULL DEFAULT b'0',
  `update_delete_date` date NOT NULL,
  `update_reshare_update_id` int(10) unsigned NOT NULL,
  `update_reshare_user_id` int(10) unsigned NOT NULL,
  `update_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`update_delete`),
  KEY `user` (`user`,`update_delete`),
  KEY `update_delete` (`update_delete`,`update_delete_date`),
  CONSTRAINT `update_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=279 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `update_comment`
--

DROP TABLE IF EXISTS `update_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `update_comment` (
  `update_comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `update_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `update_comment_content` text NOT NULL,
  `update_comment_created` datetime NOT NULL,
  PRIMARY KEY (`update_comment_id`),
  KEY `update_id` (`update_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `update_comment_ibfk_1` FOREIGN KEY (`update_id`) REFERENCES `update` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `update_comment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `update_image`
--

DROP TABLE IF EXISTS `update_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `update_image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `update` int(10) unsigned NOT NULL,
  `image_id` int(10) unsigned NOT NULL,
  `update_image_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `update` (`update`),
  KEY `image_id` (`image_id`),
  CONSTRAINT `update_image_ibfk_1` FOREIGN KEY (`update`) REFERENCES `update` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `update_image_ibfk_2` FOREIGN KEY (`image_id`) REFERENCES `image` (`image_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `update_mention`
--

DROP TABLE IF EXISTS `update_mention`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `update_mention` (
  `update_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `user_mention_count` int(10) unsigned NOT NULL,
  PRIMARY KEY (`update_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `update_mention_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `update_mention_ibfk_2` FOREIGN KEY (`update_id`) REFERENCES `update` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `update_url`
--

DROP TABLE IF EXISTS `update_url`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `update_url` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `update` int(10) unsigned NOT NULL,
  `url` int(10) unsigned NOT NULL,
  `url_details` int(10) unsigned NOT NULL,
  `url_image` int(10) unsigned NOT NULL,
  `update_url_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `update` (`update`),
  CONSTRAINT `update_url_ibfk_1` FOREIGN KEY (`update`) REFERENCES `update` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `url`
--

DROP TABLE IF EXISTS `url`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `url` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site` int(10) unsigned NOT NULL,
  `link` varchar(2000) NOT NULL,
  `share_count` int(10) unsigned NOT NULL,
  `url_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `link` (`link`(255)),
  KEY `site` (`site`),
  CONSTRAINT `url_ibfk_1` FOREIGN KEY (`site`) REFERENCES `site` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `url_details`
--

DROP TABLE IF EXISTS `url_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `url_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` int(10) unsigned NOT NULL,
  `title` varchar(70) NOT NULL,
  `description` varchar(255) NOT NULL,
  `keywords` text NOT NULL,
  `og_type` varchar(50) NOT NULL,
  `url_details_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `url` (`url`),
  CONSTRAINT `url_details_ibfk_1` FOREIGN KEY (`url`) REFERENCES `url` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `url_image`
--

DROP TABLE IF EXISTS `url_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `url_image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` int(10) unsigned NOT NULL,
  `original` varchar(2000) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `url_image_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `url` (`url`,`original`(255)),
  CONSTRAINT `url_image_ibfk_1` FOREIGN KEY (`url`) REFERENCES `url` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(22) NOT NULL,
  `password` varchar(64) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `language` varchar(4) NOT NULL,
  `bio` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `image_ext` varchar(5) NOT NULL,
  `invites` smallint(5) unsigned NOT NULL DEFAULT '5',
  `is_admin` bit(1) NOT NULL DEFAULT b'0',
  `update_count` int(11) NOT NULL DEFAULT '0',
  `user_created` datetime NOT NULL,
  `user_delete` bit(1) NOT NULL DEFAULT b'0',
  `user_delete_date` date NOT NULL,
  `last_login_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `email` (`email`),
  KEY `update_count` (`update_count`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Creating admin user
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'showthatyouhelp','$2a$12$o4riFoGHQ2foTziFBONwN.AcyBNlopYs9yNp.Z118OE/GtR41UIrW','showthatyouhelp','your@email.com','en','Main showthatyouhelp account','http://www.showthatyouhelp.com','','',4978,'',0,'2012-07-16 00:00:00','\0','0000-00-00','2013-03-27');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_extra`
--

DROP TABLE IF EXISTS `user_extra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_extra` (
  `user_id` int(10) unsigned NOT NULL,
  `notifications` text NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `user_extra_idx` (`user_id`),
  CONSTRAINT `user_extra_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_extra`
--

LOCK TABLES `user_extra` WRITE;
/*!40000 ALTER TABLE `user_extra` DISABLE KEYS */;
INSERT INTO `user_extra` VALUES (8,'{\"notify_connect\":\"1\",\"notify_mention\":\"1\",\"notify_comment\":\"1\",\"notify_reshare\":\"1\",\"notify_pending\":\"1\",\"notify_other\":\"1\"}');
/*!40000 ALTER TABLE `user_extra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_invite`
--

DROP TABLE IF EXISTS `user_invite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_invite` (
  `user_invite_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_invite_email` varchar(255) NOT NULL,
  `user_invite_token` varchar(32) NOT NULL,
  `user_invite_date` datetime NOT NULL,
  PRIMARY KEY (`user_invite_id`),
  KEY `user_invite_token` (`user_invite_token`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_notification`
--

DROP TABLE IF EXISTS `user_notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_notification` (
  `user_notification_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_id_created_by` int(10) unsigned NOT NULL,
  `user_notification_type` enum('comment','mention','comment_mention','connect','reshare') NOT NULL,
  `update_id` int(10) unsigned NOT NULL,
  `user_notification_read` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `user_notification_email` tinyint(1) NOT NULL DEFAULT '0',
  `user_notification_created` datetime NOT NULL,
  PRIMARY KEY (`user_notification_id`),
  KEY `user_id` (`user_id`,`user_notification_read`,`user_notification_created`),
  KEY `user_id_2` (`user_id`,`update_id`,`user_notification_read`),
  KEY `user_id_created_by` (`user_id_created_by`),
  KEY `email` (`user_notification_email`,`user_notification_read`),
  CONSTRAINT `user_notification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `user_notification_ibfk_2` FOREIGN KEY (`user_id_created_by`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=212 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_oauth`
--

DROP TABLE IF EXISTS `user_oauth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_oauth` (
  `user_id` int(10) unsigned NOT NULL,
  `username` varchar(255) NOT NULL,
  `oauth_provider` enum('twitter') NOT NULL,
  `oauth_uid` varchar(255) NOT NULL,
  `oauth_token` text NOT NULL,
  `oauth_secret` text NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `oauth_uid` (`oauth_provider`,`oauth_uid`),
  CONSTRAINT `user_oauth_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_recover`
--

DROP TABLE IF EXISTS `user_recover`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_recover` (
  `user_recover_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_recover_token` varchar(32) NOT NULL,
  `user_recover_password` varchar(64) NOT NULL,
  `user_recover_date` date NOT NULL,
  PRIMARY KEY (`user_recover_id`),
  KEY `user_id` (`user_id`),
  KEY `user_recover_token` (`user_recover_token`),
  CONSTRAINT `user_recover_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `view`
--

DROP TABLE IF EXISTS `view`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `view` (
  `user_view` int(10) unsigned NOT NULL,
  `user_show` int(10) unsigned NOT NULL,
  `view_created` datetime NOT NULL,
  PRIMARY KEY (`user_view`,`user_show`),
  KEY `date_created` (`view_created`),
  KEY `user_show` (`user_show`),
  CONSTRAINT `view_ibfk_1` FOREIGN KEY (`user_view`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `view_ibfk_2` FOREIGN KEY (`user_show`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-03-02 20:43:19
