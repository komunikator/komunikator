/*
Navicat MySQL Data Transfer

Source Server         : 172.17.2.48
Source Server Version : 50162
Source Host           : localhost:3306
Source Database       : FREESENTRAL

Target Server Type    : MYSQL
Target Server Version : 50162
File Encoding         : 65001

Date: 2012-06-15 17:24:01
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `actionlogs`
-- ----------------------------
DROP TABLE IF EXISTS `actionlogs`;
CREATE TABLE `actionlogs` (
  `date` decimal(17,3) NOT NULL,
  `log` varchar(255),
  `performer_id` varchar(255),
  `performer` varchar(255),
  `real_performer_id` varchar(255),
  `object` varchar(255),
  `query` varchar(255),
  `ip` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of actionlogs
-- ----------------------------

-- ----------------------------
-- Table structure for `call_logs`
-- ----------------------------
DROP TABLE IF EXISTS `call_logs`;
CREATE TABLE `call_logs` (
  `time` decimal(17,3) NOT NULL,
  `chan` varchar(255),
  `address` varchar(255),
  `direction` varchar(255),
  `billid` varchar(255),
  `caller` varchar(255),
  `called` varchar(255),
  `duration` decimal(7,3) DEFAULT NULL,
  `billtime` decimal(7,3) DEFAULT NULL,
  `ringtime` decimal(7,3) DEFAULT NULL,
  `status` varchar(255),
  `reason` varchar(255),
  `ended` tinyint(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Table structure for `card_confs`
-- ----------------------------
DROP TABLE IF EXISTS `card_confs`;
CREATE TABLE `card_confs` (
  `param_name` varchar(255),
  `param_value` varchar(255),
  `section_name` varchar(255),
  `module_name` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `card_ports`
-- ----------------------------
DROP TABLE IF EXISTS `card_ports`;
CREATE TABLE `card_ports` (
  `BUS` int(11) DEFAULT NULL,
  `SLOT` int(11) DEFAULT NULL,
  `PORT` int(11) DEFAULT NULL,
  `filename` varchar(255),
  `span` varchar(255),
  `type` varchar(255),
  `card_type` varchar(255),
  `voice_interface` varchar(255),
  `sig_interface` varchar(255),
  `voice_chans` varchar(255),
  `sig_chans` varchar(255),
  `echocancel` tinyint(1) DEFAULT NULL,
  `dtmfdetect` tinyint(1) DEFAULT NULL,
  `name` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `dial_plans`
-- ----------------------------
DROP TABLE IF EXISTS `dial_plans`;
CREATE TABLE `dial_plans` (
  `dial_plan_id` int(11) NOT NULL AUTO_INCREMENT,
  `dial_plan` varchar(255),
  `priority` int(11) DEFAULT NULL,
  `prefix` varchar(255),
  `gateway_id` int(11) DEFAULT NULL,
  `nr_of_digits_to_cut` int(11) DEFAULT NULL,
  `position_to_start_cutting` int(11) DEFAULT NULL,
  `nr_of_digits_to_replace` int(11) DEFAULT NULL,
  `digits_to_replace_with` varchar(255),
  `position_to_start_replacing` int(11) DEFAULT NULL,
  `position_to_start_adding` int(11) DEFAULT NULL,
  `digits_to_add` varchar(255),
  PRIMARY KEY (`dial_plan_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `dids`
-- ----------------------------
DROP TABLE IF EXISTS `dids`;
CREATE TABLE `dids` (
  `did_id` int(11) NOT NULL AUTO_INCREMENT,
  `did` varchar(255),
  `number` varchar(255),
  `destination` varchar(255),
  `description` varchar(255),
  `extension_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`did_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `extensions`
-- ----------------------------
DROP TABLE IF EXISTS `extensions`;
CREATE TABLE `extensions` (
  `extension_id` int(11) NOT NULL AUTO_INCREMENT,
  `extension` varchar(255),
  `password` varchar(255),
  `firstname` varchar(255),
  `lastname` varchar(255),
  `address` varchar(255),
  `inuse` int(11) DEFAULT NULL,
  `location` varchar(255),
  `expires` decimal(17,3) DEFAULT NULL,
  `max_minutes` decimal(7,3) DEFAULT NULL,
  `used_minutes` decimal(7,3) DEFAULT NULL,
  `inuse_count` int(11) DEFAULT NULL,
  `inuse_last` decimal(17,3) DEFAULT NULL,
  `login_attempts` int(11) DEFAULT NULL,
  PRIMARY KEY (`extension_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `gateways`
-- ----------------------------
DROP TABLE IF EXISTS `gateways`;
CREATE TABLE `gateways` (
  `gateway_id` int(11) NOT NULL AUTO_INCREMENT,
  `gateway` varchar(255),
  `protocol` varchar(255),
  `server` varchar(255),
  `type` varchar(255),
  `username` varchar(255),
  `password` varchar(255),
  `enabled` tinyint(1) DEFAULT NULL,
  `description` varchar(255),
  `interval` varchar(255),
  `authname` varchar(255),
  `domain` varchar(255),
  `outbound` varchar(255),
  `localaddress` varchar(255),
  `formats` varchar(255),
  `rtp_localip` varchar(255),
  `ip_transport` varchar(255),
  `oip_transport` varchar(255),
  `port` varchar(255),
  `iaxuser` varchar(255),
  `iaxcontext` varchar(255),
  `rtp_forward` tinyint(1) DEFAULT NULL,
  `status` varchar(255),
  `modified` tinyint(1) DEFAULT NULL,
  `callerid` varchar(255),
  `callername` varchar(255),
  `send_extension` tinyint(1) DEFAULT NULL,
  `trusted` tinyint(1) DEFAULT NULL,
  `sig_trunk_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`gateway_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `group_members`
-- ----------------------------
DROP TABLE IF EXISTS `group_members`;
CREATE TABLE `group_members` (
  `group_member_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) DEFAULT NULL,
  `extension_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`group_member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `groups`
-- ----------------------------
DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(255),
  `description` varchar(255),
  `extension` varchar(255),
  `mintime` int(11) DEFAULT NULL,
  `length` int(11) DEFAULT NULL,
  `maxout` int(11) DEFAULT NULL,
  `greeting` varchar(255),
  `maxcall` int(11) DEFAULT NULL,
  `prompt` varchar(255),
  `detail` tinyint(1) DEFAULT NULL,
  `playlist_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `incoming_gateways`
-- ----------------------------
DROP TABLE IF EXISTS `incoming_gateways`;
CREATE TABLE `incoming_gateways` (
  `incoming_gateway_id` int(11) NOT NULL AUTO_INCREMENT,
  `incoming_gateway` varchar(255),
  `gateway_id` int(11) DEFAULT NULL,
  `ip` varchar(255),
  PRIMARY KEY (`incoming_gateway_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `keys`
-- ----------------------------
DROP TABLE IF EXISTS `keys`;
CREATE TABLE `keys` (
  `key_id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255),
  `prompt_id` int(11) DEFAULT NULL,
  `destination` varchar(255),
  `description` varchar(255),
  PRIMARY KEY (`key_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `limits_international`
-- ----------------------------
DROP TABLE IF EXISTS `limits_international`;
CREATE TABLE `limits_international` (
  `limit_international_id` int(11) NOT NULL AUTO_INCREMENT,
  `limit_international` varchar(255),
  `name` varchar(255),
  `value` varchar(255),
  PRIMARY KEY (`limit_international_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `music_on_hold`
-- ----------------------------
DROP TABLE IF EXISTS `music_on_hold`;
CREATE TABLE `music_on_hold` (
  `music_on_hold_id` int(11) NOT NULL AUTO_INCREMENT,
  `music_on_hold` varchar(255),
  `description` varchar(255),
  `file` varchar(255),
  PRIMARY KEY (`music_on_hold_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `network_interfaces`
-- ----------------------------
DROP TABLE IF EXISTS `network_interfaces`;
CREATE TABLE `network_interfaces` (
  `network_interface_id` int(11) NOT NULL AUTO_INCREMENT,
  `network_interface` varchar(255),
  `protocol` varchar(255),
  `ip_address` varchar(255),
  `netmask` varchar(255),
  `gateway` varchar(255),
  PRIMARY KEY (`network_interface_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `pbx_settings`
-- ----------------------------
DROP TABLE IF EXISTS `pbx_settings`;
CREATE TABLE `pbx_settings` (
  `pbx_setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `extension_id` int(11) DEFAULT NULL,
  `param` varchar(255),
  `value` varchar(255),
  PRIMARY KEY (`pbx_setting_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `playlist_items`
-- ----------------------------
DROP TABLE IF EXISTS `playlist_items`;
CREATE TABLE `playlist_items` (
  `playlist_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `playlist_id` int(11) DEFAULT NULL,
  `music_on_hold_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`playlist_item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `playlists`
-- ----------------------------
DROP TABLE IF EXISTS `playlists`;
CREATE TABLE `playlists` (
  `playlist_id` int(11) NOT NULL AUTO_INCREMENT,
  `playlist` varchar(255),
  `in_use` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`playlist_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `prefixes`
-- ----------------------------
DROP TABLE IF EXISTS `prefixes`;
CREATE TABLE `prefixes` (
  `prefix_id` int(11) NOT NULL AUTO_INCREMENT,
  `prefix` varchar(255),
  `name` varchar(255),
  `international` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`prefix_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `prompts`
-- ----------------------------
DROP TABLE IF EXISTS `prompts`;
CREATE TABLE `prompts` (
  `prompt_id` int(11) NOT NULL AUTO_INCREMENT,
  `prompt` varchar(255),
  `description` varchar(255),
  `status` varchar(255),
  `file` varchar(255),
  PRIMARY KEY (`prompt_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `settings`
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `param` varchar(255),
  `value` varchar(255),
  `description` varchar(255),
  PRIMARY KEY (`setting_id`)
) ENGINE=MyISAM AUTO_INCREMENT=261 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `short_names`
-- ----------------------------
DROP TABLE IF EXISTS `short_names`;
CREATE TABLE `short_names` (
  `short_name_id` int(11) NOT NULL AUTO_INCREMENT,
  `short_name` varchar(255),
  `name` varchar(255),
  `number` varchar(255),
  `extension_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`short_name_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `sig_trunks`
-- ----------------------------
DROP TABLE IF EXISTS `sig_trunks`;
CREATE TABLE `sig_trunks` (
  `sig_trunk_id` int(11) NOT NULL AUTO_INCREMENT,
  `sig_trunk` varchar(255),
  `enable` varchar(255),
  `type` varchar(255),
  `switchtype` varchar(255),
  `sig` varchar(255),
  `voice` varchar(255),
  `number` varchar(255),
  `rxunderrun` int(11) DEFAULT NULL,
  `strategy` varchar(255),
  `strategy-restrict` varchar(255),
  `userparttest` int(11) DEFAULT NULL,
  `channelsync` int(11) DEFAULT NULL,
  `channellock` int(11) DEFAULT NULL,
  `numplan` varchar(255),
  `numtype` varchar(255),
  `presentation` varchar(255),
  `screening` varchar(255),
  `format` varchar(255),
  `print-messages` varchar(255),
  `print-frames` varchar(255),
  `extended-debug` varchar(255),
  `layer2dump` varchar(255),
  `layer3dump` varchar(255),
  `port` varchar(255),
  PRIMARY KEY (`sig_trunk_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `time_frames`
-- ----------------------------
DROP TABLE IF EXISTS `time_frames`;
CREATE TABLE `time_frames` (
  `time_frame_id` int(11) NOT NULL AUTO_INCREMENT,
  `prompt_id` int(11) DEFAULT NULL,
  `day` varchar(255),
  `start_hour` varchar(255),
  `end_hour` varchar(255),
  `numeric_day` int(11) DEFAULT NULL,
  PRIMARY KEY (`time_frame_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255),
  `password` varchar(255),
  `firstname` varchar(255),
  `lastname` varchar(255),
  `email` varchar(255),
  `description` varchar(255),
  `fax_number` varchar(255),
  `ident` varchar(255),
  `login_attempts` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ntn_settings`;
CREATE TABLE `ntn_settings` (
  `ntn_setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `param` varchar(255),
  `value` varchar(255),
  `description` varchar(255),
  PRIMARY KEY (`ntn_setting_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of time_frames
-- ----------------------------
INSERT INTO `users` VALUES ('1', 'admin', 'admin', null, null, null, null, null, null, '0');

INSERT INTO `time_frames` VALUES ('1', '1', 'Sunday', null, null, '0');
INSERT INTO `time_frames` VALUES ('2', '1', 'Monday', '8', '18', '1');
INSERT INTO `time_frames` VALUES ('3', '1', 'Tuesday', '8', '18', '2');
INSERT INTO `time_frames` VALUES ('4', '1', 'Wednesday', '8', '18', '3');
INSERT INTO `time_frames` VALUES ('5', '1', 'Thursday', '8', '18', '4');
INSERT INTO `time_frames` VALUES ('6', '1', 'Friday', '8', '18', '5');
INSERT INTO `time_frames` VALUES ('7', '1', 'Saturday', null, null, '6');

/*
INSERT INTO `keys` VALUES ('1', '0', '1', '23', null);
INSERT INTO `keys` VALUES ('2', '0', '2', '23', null);
INSERT INTO `keys` VALUES ('3', '2', '2', '222', null);
INSERT INTO `keys` VALUES ('4', '1', '1', '222', null);
*/

INSERT INTO `ntn_settings` VALUES ('1', 'incoming_trunk', 'true', null);
INSERT INTO `ntn_settings` VALUES ('2', 'exclude_called', '104', null);
INSERT INTO `ntn_settings` VALUES ('3', 'exclude_called', '103', null);
INSERT INTO `ntn_settings` VALUES ('4', 'incoming_call', 'true', null);
INSERT INTO `ntn_settings` VALUES ('5', 'outgoing_call', 'true', null);
INSERT INTO `ntn_settings` VALUES ('6', 'internal_call', 'false', null);
INSERT INTO `ntn_settings` VALUES ('7', 'incoming_trunk_text', '    Абонент: <caller>\\n    Входящая линия: <called>\\n    Дата: <ftime>', null);
INSERT INTO `ntn_settings` VALUES ('8', 'incoming_call_text', '    Абонент: <caller>\\n    Входящая линия: <incoming_trunk>\\n    Кому: <called>\\n    Дата: <ftime>\\n    Длительность: <duration>\\n    Состояние: <status>\\n    Тип: <type>', null);

ALTER TABLE extensions ADD UNIQUE (extension);
ALTER TABLE groups ADD UNIQUE (`group`);
