/*

	PBX CREATE
	v1.0

*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `actionlogs`
-- ----------------------------
DROP TABLE IF EXISTS `actionlogs`;
CREATE TABLE `actionlogs` (
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `log` text,
  `performer_id` text,
  `performer` text,
  `real_performer_id` text,
  `object` text,
  `query` text,
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
  `chan` text,
  `address` text,
  `direction` text,
  `billid` text,
  `caller` text,
  `called` text,
  `duration` decimal(5,3) DEFAULT NULL,
  `billtime` decimal(5,3) DEFAULT NULL,
  `ringtime` decimal(5,3) DEFAULT NULL,
  `status` text,
  `reason` text,
  `ended` tinyint(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of call_logs
-- ----------------------------

-- ----------------------------
-- Table structure for `card_confs`
-- ----------------------------
DROP TABLE IF EXISTS `card_confs`;
CREATE TABLE `card_confs` (
  `param_name` text,
  `param_value` text,
  `section_name` text,
  `module_name` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of card_confs
-- ----------------------------

-- ----------------------------
-- Table structure for `card_ports`
-- ----------------------------
DROP TABLE IF EXISTS `card_ports`;
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

-- ----------------------------
-- Records of card_ports
-- ----------------------------

-- ----------------------------
-- Table structure for `dial_plans`
-- ----------------------------
DROP TABLE IF EXISTS `dial_plans`;
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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dial_plans
-- ----------------------------

-- ----------------------------
-- Table structure for `dids`
-- ----------------------------
DROP TABLE IF EXISTS `dids`;
CREATE TABLE `dids` (
  `did_id` int(11) NOT NULL AUTO_INCREMENT,
  `did` text,
  `number` text,
  `destination` text,
  `description` text,
  `extension_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`did_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dids
-- ----------------------------

-- ----------------------------
-- Table structure for `extensions`
-- ----------------------------
DROP TABLE IF EXISTS `extensions`;
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
) ENGINE=MyISAM AUTO_INCREMENT=125 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of extensions
-- ----------------------------

-- ----------------------------
-- Table structure for `gateways`
-- ----------------------------
DROP TABLE IF EXISTS `gateways`;
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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gateways
-- ----------------------------

-- ----------------------------
-- Table structure for `group_members`
-- ----------------------------
DROP TABLE IF EXISTS `group_members`;
CREATE TABLE `group_members` (
  `group_member_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) DEFAULT NULL,
  `extension_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`group_member_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of group_members
-- ----------------------------

-- ----------------------------
-- Table structure for `group_priority`
-- ----------------------------
DROP TABLE IF EXISTS `group_priority`;
CREATE TABLE `group_priority` (
  `group_id` int(11) NOT NULL,
  `extension_id` int(11) NOT NULL,
  `priority` smallint(6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of group_priority
-- ----------------------------

-- ----------------------------
-- Table structure for `groups`
-- ----------------------------
DROP TABLE IF EXISTS `groups`;
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
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of groups
-- ----------------------------

-- ----------------------------
-- Table structure for `incoming_gateways`
-- ----------------------------
DROP TABLE IF EXISTS `incoming_gateways`;
CREATE TABLE `incoming_gateways` (
  `incoming_gateway_id` int(11) NOT NULL AUTO_INCREMENT,
  `incoming_gateway` text,
  `gateway_id` int(11) DEFAULT NULL,
  `ip` text,
  PRIMARY KEY (`incoming_gateway_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of incoming_gateways
-- ----------------------------

-- ----------------------------
-- Table structure for `keys`
-- ----------------------------
DROP TABLE IF EXISTS `keys`;
CREATE TABLE `keys` (
  `key_id` int(11) NOT NULL AUTO_INCREMENT,
  `key` text,
  `prompt_id` int(11) DEFAULT NULL,
  `destination` text,
  `description` text,
  PRIMARY KEY (`key_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of keys
-- ----------------------------

-- ----------------------------
-- Table structure for `limits_international`
-- ----------------------------
DROP TABLE IF EXISTS `limits_international`;
CREATE TABLE `limits_international` (
  `limit_international_id` int(11) NOT NULL AUTO_INCREMENT,
  `limit_international` text,
  `name` text,
  `value` text,
  PRIMARY KEY (`limit_international_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of limits_international
-- ----------------------------

-- ----------------------------
-- Table structure for `music_on_hold`
-- ----------------------------
DROP TABLE IF EXISTS `music_on_hold`;
CREATE TABLE `music_on_hold` (
  `music_on_hold_id` int(11) NOT NULL AUTO_INCREMENT,
  `music_on_hold` text,
  `description` text,
  `file` text,
  PRIMARY KEY (`music_on_hold_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of music_on_hold
-- ----------------------------

-- ----------------------------
-- Table structure for `network_interfaces`
-- ----------------------------
DROP TABLE IF EXISTS `network_interfaces`;
CREATE TABLE `network_interfaces` (
  `network_interface_id` int(11) NOT NULL AUTO_INCREMENT,
  `network_interface` text,
  `protocol` text,
  `ip_address` text,
  `netmask` text,
  `gateway` text,
  PRIMARY KEY (`network_interface_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of network_interfaces
-- ----------------------------

-- ----------------------------
-- Table structure for `ntn_settings`
-- ----------------------------
DROP TABLE IF EXISTS `ntn_settings`;
CREATE TABLE `ntn_settings` (
  `ntn_setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `param` text,
  `value` text,
  `description` text,
  PRIMARY KEY (`ntn_setting_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ntn_settings
-- ----------------------------

-- ----------------------------
-- Table structure for `pbx_settings`
-- ----------------------------
DROP TABLE IF EXISTS `pbx_settings`;
CREATE TABLE `pbx_settings` (
  `pbx_setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `extension_id` int(11) DEFAULT NULL,
  `param` text,
  `value` text,
  PRIMARY KEY (`pbx_setting_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pbx_settings
-- ----------------------------

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
-- Records of playlist_items
-- ----------------------------

-- ----------------------------
-- Table structure for `playlists`
-- ----------------------------
DROP TABLE IF EXISTS `playlists`;
CREATE TABLE `playlists` (
  `playlist_id` int(11) NOT NULL AUTO_INCREMENT,
  `playlist` text,
  `in_use` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`playlist_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of playlists
-- ----------------------------

-- ----------------------------
-- Table structure for `prefixes`
-- ----------------------------
DROP TABLE IF EXISTS `prefixes`;
CREATE TABLE `prefixes` (
  `prefix_id` int(11) NOT NULL AUTO_INCREMENT,
  `prefix` text,
  `name` text,
  `international` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`prefix_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of prefixes
-- ----------------------------

-- ----------------------------
-- Table structure for `prompts`
-- ----------------------------
DROP TABLE IF EXISTS `prompts`;
CREATE TABLE `prompts` (
  `prompt_id` int(11) NOT NULL AUTO_INCREMENT,
  `prompt` text,
  `description` text,
  `status` text,
  `file` text,
  PRIMARY KEY (`prompt_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of prompts
-- ----------------------------

-- ----------------------------
-- Table structure for `settings`
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `param` text,
  `value` text,
  `description` text,
  PRIMARY KEY (`setting_id`)
) ENGINE=MyISAM AUTO_INCREMENT=261 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of settings
-- ----------------------------
INSERT INTO `settings` VALUES ('1', 'vm', 'external/nodata/leavemaildb.php', 'Script used for leaving a voicemail message.');
INSERT INTO `settings` VALUES ('2', 'version', '1', null);
INSERT INTO `settings` VALUES ('3', 'annonymous_calls', 'no', 'Allow calls from anomynous users if call is for one of the extensions. Use just \'yes\' or \'no\' as values.');
INSERT INTO `settings` VALUES ('4', 'international_calls', 'yes', 'Allow calls to international/expensive destinations. This prefixes are set in Outbound>>International calls');
INSERT INTO `settings` VALUES ('5', 'international_calls_live', 'yes', 'Allow calls to international/expensive destinations. This prefixes are set in Outbound>>International calls');
INSERT INTO `settings` VALUES ('6', 'callerid', '', null);
INSERT INTO `settings` VALUES ('7', 'callername', null, null);
INSERT INTO `settings` VALUES ('8', 'prefix', null, null);

-- ----------------------------
-- Table structure for `short_names`
-- ----------------------------
DROP TABLE IF EXISTS `short_names`;
CREATE TABLE `short_names` (
  `short_name_id` int(11) NOT NULL AUTO_INCREMENT,
  `short_name` text,
  `name` text,
  `number` text,
  `extension_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`short_name_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of short_names
-- ----------------------------

-- ----------------------------
-- Table structure for `sig_trunks`
-- ----------------------------
DROP TABLE IF EXISTS `sig_trunks`;
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

-- ----------------------------
-- Records of sig_trunks
-- ----------------------------

-- ----------------------------
-- Table structure for `time_frames`
-- ----------------------------
DROP TABLE IF EXISTS `time_frames`;
CREATE TABLE `time_frames` (
  `time_frame_id` int(11) NOT NULL AUTO_INCREMENT,
  `prompt_id` int(11) DEFAULT NULL,
  `day` text,
  `start_hour` text,
  `end_hour` text,
  `numeric_day` int(11) DEFAULT NULL,
  PRIMARY KEY (`time_frame_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of time_frames
-- ----------------------------
INSERT INTO `time_frames` VALUES ('1', '1', 'Sunday', null, null, '0');
INSERT INTO `time_frames` VALUES ('2', '1', 'Monday', '8', '18', '1');
INSERT INTO `time_frames` VALUES ('3', '1', 'Tuesday', '8', '18', '2');
INSERT INTO `time_frames` VALUES ('4', '1', 'Wednesday', '8', '18', '3');
INSERT INTO `time_frames` VALUES ('5', '1', 'Thursday', '8', '18', '4');
INSERT INTO `time_frames` VALUES ('6', '1', 'Friday', '8', '18', '5');
INSERT INTO `time_frames` VALUES ('7', '1', 'Saturday', null, null, '6');

-- ----------------------------
-- Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
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
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', 'admin', 'admin', null, null, null, null, null, null, '0');

COMMIT;
