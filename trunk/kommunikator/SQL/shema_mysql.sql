-- MySQL dump 10.13  Distrib 5.5.29, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: kommunikator
-- ------------------------------------------------------
-- Server version	5.5.29-0ubuntu0.12.04.2

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
-- Table structure for table `actionlogs`
--

DROP TABLE IF EXISTS `actionlogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actionlogs` (
  `date` decimal(17,3) NOT NULL,
  `log` text,
  `performer_id` text,
  `performer` text,
  `real_performer_id` text,
  `object` text,
  `query` text,
  `ip` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `call_logs`
--

DROP TABLE IF EXISTS `call_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `call_logs` (
  `time` decimal(17,3) NOT NULL,
  `chan` text,
  `address` text,
  `direction` text,
  `billid` text,
  `caller` text,
  `called` text,
  `duration` decimal(7,3) DEFAULT NULL,
  `billtime` decimal(7,3) DEFAULT NULL,
  `ringtime` decimal(7,3) DEFAULT NULL,
  `status` text,
  `reason` text,
  `ended` tinyint(1) DEFAULT NULL,
  UNIQUE KEY `time_indx` (`time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `card_confs`
--

DROP TABLE IF EXISTS `card_confs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `card_confs` (
  `param_name` text,
  `param_value` text,
  `section_name` text,
  `module_name` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `card_ports`
--

DROP TABLE IF EXISTS `card_ports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `card_ports` (
  `BUS` int(11) DEFAULT NULL,
  `SLOT` int(11) DEFAULT NULL,
  `PORT` int(11) DEFAULT NULL,
  `filename` text,
  `span` text,
  `type` text,
  `card_type` text,
  `voice_interface` text,
  `sig_interface` text,
  `voice_chans` text,
  `sig_chans` text,
  `echocancel` tinyint(1) DEFAULT NULL,
  `dtmfdetect` tinyint(1) DEFAULT NULL,
  `name` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dial_plans`
--

DROP TABLE IF EXISTS `dial_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dial_plans` (
  `dial_plan_id` int(11) NOT NULL AUTO_INCREMENT,
  `dial_plan` text,
  `priority` int(11) DEFAULT NULL,
  `prefix` text,
  `gateway_id` int(11) DEFAULT NULL,
  `nr_of_digits_to_cut` int(11) DEFAULT NULL,
  `position_to_start_cutting` int(11) DEFAULT NULL,
  `nr_of_digits_to_replace` int(11) DEFAULT NULL,
  `digits_to_replace_with` text,
  `position_to_start_replacing` int(11) DEFAULT NULL,
  `position_to_start_adding` int(11) DEFAULT NULL,
  `digits_to_add` text,
  PRIMARY KEY (`dial_plan_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dids`
--

DROP TABLE IF EXISTS `dids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dids` (
  `did_id` int(11) NOT NULL AUTO_INCREMENT,
  `did` text,
  `number` text,
  `destination` text,
  `description` text,
  `extension_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`did_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `extensions`
--

DROP TABLE IF EXISTS `extensions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `extensions` (
  `extension_id` int(11) NOT NULL AUTO_INCREMENT,
  `extension` varchar(3) NOT NULL,
  `password` text,
  `firstname` text,
  `lastname` text,
  `address` text,
  `inuse` int(11) DEFAULT NULL,
  `location` text,
  `expires` decimal(17,3) DEFAULT NULL,
  `max_minutes` time DEFAULT NULL,
  `used_minutes` time DEFAULT NULL,
  `inuse_count` int(11) DEFAULT NULL,
  `inuse_last` decimal(17,3) DEFAULT NULL,
  `login_attempts` int(11) DEFAULT NULL,
  PRIMARY KEY (`extension_id`),
  UNIQUE KEY `extension` (`extension`)
) ENGINE=MyISAM AUTO_INCREMENT=134 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `extensions`
--

LOCK TABLES `extensions` WRITE;
/*!40000 ALTER TABLE `extensions` DISABLE KEYS */;
INSERT INTO `extensions` VALUES (1,'101','101101',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL),(2,'102','102102',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL);
/*!40000 ALTER TABLE `extensions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gateways`
--

DROP TABLE IF EXISTS `gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gateways` (
  `gateway_id` int(11) NOT NULL AUTO_INCREMENT,
  `gateway` text,
  `protocol` text,
  `server` text,
  `type` text,
  `username` text,
  `password` text,
  `enabled` tinyint(1) DEFAULT NULL,
  `description` text,
  `interval` text,
  `authname` text,
  `domain` text,
  `outbound` text,
  `localaddress` text,
  `formats` text,
  `rtp_localip` text,
  `ip_transport` text,
  `oip_transport` text,
  `port` text,
  `iaxuser` text,
  `iaxcontext` text,
  `rtp_forward` tinyint(1) DEFAULT NULL,
  `status` text,
  `modified` tinyint(1) DEFAULT NULL,
  `callerid` text,
  `callername` text,
  `send_extension` tinyint(1) DEFAULT NULL,
  `trusted` tinyint(1) DEFAULT NULL,
  `sig_trunk_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`gateway_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `group_members`
--

DROP TABLE IF EXISTS `group_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_members` (
  `group_member_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) DEFAULT NULL,
  `extension_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`group_member_id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `group_priority`
--

DROP TABLE IF EXISTS `group_priority`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_priority` (
  `group_id` int(11) NOT NULL,
  `extension_id` int(11) NOT NULL,
  `priority` smallint(6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(25) DEFAULT NULL,
  `description` text,
  `extension` varchar(2) DEFAULT NULL,
  `mintime` int(11) DEFAULT NULL,
  `length` int(11) DEFAULT NULL,
  `maxout` int(11) DEFAULT NULL,
  `greeting` text,
  `maxcall` int(11) DEFAULT NULL,
  `prompt` text,
  `detail` tinyint(1) DEFAULT NULL,
  `playlist_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `group` (`group`),
  UNIQUE KEY `extension` (`extension`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `incoming_gateways`
--

DROP TABLE IF EXISTS `incoming_gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `incoming_gateways` (
  `incoming_gateway_id` int(11) NOT NULL AUTO_INCREMENT,
  `incoming_gateway` text,
  `gateway_id` int(11) DEFAULT NULL,
  `ip` text,
  PRIMARY KEY (`incoming_gateway_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `keys`
--

DROP TABLE IF EXISTS `keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `keys` (
  `key_id` int(11) NOT NULL AUTO_INCREMENT,
  `key` text,
  `prompt_id` int(11) DEFAULT NULL,
  `destination` text,
  `description` text,
  PRIMARY KEY (`key_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `limits_international`
--

DROP TABLE IF EXISTS `limits_international`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `limits_international` (
  `limit_international_id` int(11) NOT NULL AUTO_INCREMENT,
  `limit_international` text,
  `name` text,
  `value` text,
  PRIMARY KEY (`limit_international_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `music_on_hold`
--

DROP TABLE IF EXISTS `music_on_hold`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `music_on_hold` (
  `music_on_hold_id` int(11) NOT NULL AUTO_INCREMENT,
  `music_on_hold` text,
  `description` text,
  `file` text,
  PRIMARY KEY (`music_on_hold_id`)
) ENGINE=MyISAM AUTO_INCREMENT=69 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `network_interfaces`
--

DROP TABLE IF EXISTS `network_interfaces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `network_interfaces` (
  `network_interface_id` int(11) NOT NULL AUTO_INCREMENT,
  `network_interface` text,
  `protocol` text,
  `ip_address` text,
  `netmask` text,
  `gateway` text,
  PRIMARY KEY (`network_interface_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ntn_settings`
--

DROP TABLE IF EXISTS `ntn_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ntn_settings` (
  `ntn_setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `param` text,
  `value` text,
  `description` text,
  PRIMARY KEY (`ntn_setting_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pbx_settings`
--

DROP TABLE IF EXISTS `pbx_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pbx_settings` (
  `pbx_setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `extension_id` int(11) DEFAULT NULL,
  `param` text,
  `value` text,
  PRIMARY KEY (`pbx_setting_id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `playlist_items`
--

DROP TABLE IF EXISTS `playlist_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `playlist_items` (
  `playlist_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `playlist_id` int(11) DEFAULT NULL,
  `music_on_hold_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`playlist_item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `playlists`
--

DROP TABLE IF EXISTS `playlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `playlists` (
  `playlist_id` int(11) NOT NULL AUTO_INCREMENT,
  `playlist` text,
  `in_use` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`playlist_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prefixes`
--

DROP TABLE IF EXISTS `prefixes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prefixes` (
  `prefix_id` int(11) NOT NULL AUTO_INCREMENT,
  `prefix` text,
  `name` text,
  `international` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`prefix_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prompts`
--

DROP TABLE IF EXISTS `prompts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prompts` (
  `prompt_id` int(11) NOT NULL AUTO_INCREMENT,
  `prompt` text,
  `description` text,
  `status` text,
  `file` text,
  PRIMARY KEY (`prompt_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `param` text,
  `value` text,
  `description` text,
  PRIMARY KEY (`setting_id`)
) ENGINE=MyISAM AUTO_INCREMENT=261 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'vm','external/nodata/leavemaildb.php','Script used for leaving a voicemail message.'),(2,'version','1',NULL),(3,'annonymous_calls','no','Allow calls from anomynous users if call is for one of the extensions. Use just \'yes\' or \'no\' as values.'),(5,'callername',NULL,NULL),(6,'prefix',NULL,NULL),(4,'callerid','79278837050',NULL),(7,'international_calls','yes','Allow calls to international/expensive destinations. This prefixes are set in Outbound>>International calls'),(8,'international_calls_live','no','Disabled at 2012-12-18 12:04:18.885601+04., limit 1minute');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `short_names`
--

DROP TABLE IF EXISTS `short_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `short_names` (
  `short_name_id` int(11) NOT NULL AUTO_INCREMENT,
  `short_name` text,
  `name` text,
  `number` text,
  `extension_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`short_name_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sig_trunks`
--

DROP TABLE IF EXISTS `sig_trunks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sig_trunks` (
  `sig_trunk_id` int(11) NOT NULL AUTO_INCREMENT,
  `sig_trunk` text,
  `enable` text,
  `type` text,
  `switchtype` text,
  `sig` text,
  `voice` text,
  `number` text,
  `rxunderrun` int(11) DEFAULT NULL,
  `strategy` text,
  `strategy-restrict` text,
  `userparttest` int(11) DEFAULT NULL,
  `channelsync` int(11) DEFAULT NULL,
  `channellock` int(11) DEFAULT NULL,
  `numplan` text,
  `numtype` text,
  `presentation` text,
  `screening` text,
  `format` text,
  `print-messages` text,
  `print-frames` text,
  `extended-debug` text,
  `layer2dump` text,
  `layer3dump` text,
  `port` text,
  PRIMARY KEY (`sig_trunk_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `time_frames`
--

DROP TABLE IF EXISTS `time_frames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `time_frames` (
  `time_frame_id` int(11) NOT NULL AUTO_INCREMENT,
  `prompt_id` int(11) DEFAULT NULL,
  `day` text,
  `start_hour` text,
  `end_hour` text,
  `numeric_day` int(11) DEFAULT NULL,
  PRIMARY KEY (`time_frame_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `time_frames` WRITE;
/*!40000 ALTER TABLE `time_frames` DISABLE KEYS */;
INSERT INTO `time_frames` VALUES (1,1,'Sunday',NULL,NULL,0),(2,1,'Monday','4','14',1),(3,1,'Tuesday','4','14',2),(4,1,'Wednesday','4','14',3),(5,1,'Thursday','4','14',4),(6,1,'Friday','4','13',5),(7,1,'Saturday',NULL,NULL,6);
/*!40000 ALTER TABLE `time_frames` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` text,
  `password` text,
  `firstname` text,
  `lastname` text,
  `email` text,
  `description` text,
  `fax_number` text,
  `ident` text,
  `login_attempts` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','admin',NULL,NULL,NULL,NULL,NULL,NULL,0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-03-06 23:24:00
