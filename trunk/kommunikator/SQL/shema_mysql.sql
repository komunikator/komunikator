/*
*  | RUS | - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

*    «Komunikator» – Web-интерфейс для настройки и управления программной IP-АТС «YATE»
*    Copyright (C) 2012-2013, ООО «Телефонные системы»

*    ЭТОТ ФАЙЛ является частью проекта «Komunikator»

*    Сайт проекта «Komunikator»: http://4yate.ru/
*    Служба технической поддержки проекта «Komunikator»: E-mail: support@4yate.ru

*    В проекте «Komunikator» используются:
*      исходные коды проекта «YATE», http://yate.null.ro/pmwiki/
*      исходные коды проекта «FREESENTRAL», http://www.freesentral.com/
*      библиотеки проекта «Sencha Ext JS», http://www.sencha.com/products/extjs

*    Web-приложение «Komunikator» является свободным и открытым программным обеспечением. Тем самым
*  давая пользователю право на распространение и (или) модификацию данного Web-приложения (а также
*  и иные права) согласно условиям GNU General Public License, опубликованной
*  Free Software Foundation, версии 3.

*    В случае отсутствия файла «License» (идущего вместе с исходными кодами программного обеспечения)
*  описывающего условия GNU General Public License версии 3, можно посетить официальный сайт
*  http://www.gnu.org/licenses/ , где опубликованы условия GNU General Public License
*  различных версий (в том числе и версии 3).

*  | ENG | - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

*    "Komunikator" is a web interface for IP-PBX "YATE" configuration and management
*    Copyright (C) 2012-2013, "Telephonnyie sistemy" Ltd.

*    THIS FILE is an integral part of the project "Komunikator"

*    "Komunikator" project site: http://4yate.ru/
*    "Komunikator" technical support e-mail: support@4yate.ru

*    The project "Komunikator" are used:
*      the source code of "YATE" project, http://yate.null.ro/pmwiki/
*      the source code of "FREESENTRAL" project, http://www.freesentral.com/
*      "Sencha Ext JS" project libraries, http://www.sencha.com/products/extjs

*    "Komunikator" web application is a free/libre and open-source software. Therefore it grants user rights
*  for distribution and (or) modification (including other rights) of this programming solution according
*  to GNU General Public License terms and conditions published by Free Software Foundation in version 3.

*    In case the file "License" that describes GNU General Public License terms and conditions,
*  version 3, is missing (initially goes with software source code), you can visit the official site
*  http://www.gnu.org/licenses/ and find terms specified in appropriate GNU General Public License
*  version (version 3 as well).

*  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
*/


--
-- Table structure for table `actionlogs`
--

DROP TABLE IF EXISTS `actionlogs`;

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


--
-- Table structure for table `call_logs`
--

DROP TABLE IF EXISTS `call_logs`;

CREATE TABLE `call_logs` (
  `time` decimal(17,3) NOT NULL,
  `chan` text,
  `address` text,
  `direction` text,
  `billid` varchar(20) DEFAULT NULL,
  `caller` varchar(20) DEFAULT NULL,
  `called` varchar(20) DEFAULT NULL,
  `duration` decimal(7,3) DEFAULT NULL,
  `billtime` decimal(7,3) DEFAULT NULL,
  `ringtime` decimal(7,3) DEFAULT NULL,
  `status` varchar(64) DEFAULT NULL,
  `reason` varchar(64) DEFAULT NULL,
  `ended` tinyint(1) DEFAULT NULL,
  UNIQUE KEY `time_indx` (`time`) USING HASH,
  KEY `billid_indx` (`billid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `card_confs`
--

DROP TABLE IF EXISTS `card_confs`;

CREATE TABLE `card_confs` (
  `param_name` text,
  `param_value` text,
  `section_name` text,
  `module_name` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `card_ports`
--

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


--
-- Table structure for table `dial_plans`
--

DROP TABLE IF EXISTS `dial_plans`;

CREATE TABLE `dial_plans` (
  `dial_plan_id` int(11) NOT NULL AUTO_INCREMENT,
  `dial_plan` text,
  `priority` int(11) DEFAULT NULL,
  `prefix` varchar(32) DEFAULT NULL,
  `gateway_id` int(11) DEFAULT NULL,
  `nr_of_digits_to_cut` int(11) DEFAULT NULL,
  `position_to_start_cutting` int(11) DEFAULT NULL,
  `nr_of_digits_to_replace` int(11) DEFAULT NULL,
  `digits_to_replace_with` text,
  `position_to_start_replacing` int(11) DEFAULT NULL,
  `position_to_start_adding` int(11) DEFAULT NULL,
  `digits_to_add` text,
  PRIMARY KEY (`dial_plan_id`),
  UNIQUE KEY `priority` (`priority`) USING BTREE,
  UNIQUE KEY `prefix` (`prefix`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `dids`
--

DROP TABLE IF EXISTS `dids`;

CREATE TABLE `dids` (
  `did_id` int(11) NOT NULL AUTO_INCREMENT,
  `did` text,
  `number` varchar(25) DEFAULT NULL,
  `destination` text,
  `description` text,
  `extension_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`did_id`),
  UNIQUE KEY `number` (`number`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `extensions`
--

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
  UNIQUE KEY `extension` (`extension`),
  KEY `extension_id` (`extension_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `gateways`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `group_members`
--

DROP TABLE IF EXISTS `group_members`;

CREATE TABLE `group_members` (
  `group_member_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) DEFAULT NULL,
  `extension_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`group_member_id`),
  KEY `group_id` (`group_id`),
  KEY `extension_id` (`extension_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `group_priority`
--

DROP TABLE IF EXISTS `group_priority`;

CREATE TABLE `group_priority` (
  `group_id` int(11) NOT NULL,
  `extension_id` int(11) NOT NULL,
  `priority` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;

CREATE TABLE `groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(50) DEFAULT NULL,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `groups` WRITE;

INSERT INTO `groups` (
  `group_id`,
  `group`,
  `description`,
  `extension`,
  `mintime`,
  `length`,
  `maxout`,
  `greeting`,
  `maxcall`,
  `prompt`,
  `detail`,
  `playlist_id`
)
VALUES
(NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

UNLOCK TABLES;


--
-- Table structure for table `incoming_gateways`
--

DROP TABLE IF EXISTS `incoming_gateways`;

CREATE TABLE `incoming_gateways` (
  `incoming_gateway_id` int(11) NOT NULL AUTO_INCREMENT,
  `incoming_gateway` text,
  `gateway_id` int(11) DEFAULT NULL,
  `ip` text,
  PRIMARY KEY (`incoming_gateway_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `keys`
--

DROP TABLE IF EXISTS `keys`;

CREATE TABLE `keys` (
  `key_id` int(11) NOT NULL AUTO_INCREMENT,
  `key` text,
  `prompt_id` int(11) DEFAULT NULL,
  `destination` text,
  `description` text,
  PRIMARY KEY (`key_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `limits_international`
--

DROP TABLE IF EXISTS `limits_international`;

CREATE TABLE `limits_international` (
  `limit_international_id` int(11) NOT NULL AUTO_INCREMENT,
  `limit_international` text,
  `name` text,
  `value` text,
  PRIMARY KEY (`limit_international_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `music_on_hold`
--

DROP TABLE IF EXISTS `music_on_hold`;

CREATE TABLE `music_on_hold` (
  `music_on_hold_id` int(11) NOT NULL AUTO_INCREMENT,
  `music_on_hold` text,
  `description` text,
  `file` text,
  PRIMARY KEY (`music_on_hold_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `network_interfaces`
--

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


--
-- Table structure for table `ntn_settings`
--

DROP TABLE IF EXISTS `ntn_settings`;

CREATE TABLE `ntn_settings` (
  `ntn_setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `param` text,
  `value` text,
  `description` text,
  PRIMARY KEY (`ntn_setting_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `pbx_settings`
--

DROP TABLE IF EXISTS `pbx_settings`;

CREATE TABLE `pbx_settings` (
  `pbx_setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `extension_id` int(11) DEFAULT NULL,
  `param` text,
  `value` text,
  PRIMARY KEY (`pbx_setting_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `playlist_items`
--

DROP TABLE IF EXISTS `playlist_items`;

CREATE TABLE `playlist_items` (
  `playlist_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `playlist_id` int(11) DEFAULT NULL,
  `music_on_hold_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`playlist_item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `playlists`
--

DROP TABLE IF EXISTS `playlists`;

CREATE TABLE `playlists` (
  `playlist_id` int(11) NOT NULL AUTO_INCREMENT,
  `playlist` text,
  `in_use` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`playlist_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `playlists` (
  `playlist_id`,
  `playlist`,
  `in_use`
)
VALUES
(NULL, '', '0');


--
-- Table structure for table `prefixes`
--

DROP TABLE IF EXISTS `prefixes`;

CREATE TABLE `prefixes` (
  `prefix_id` int(11) NOT NULL AUTO_INCREMENT,
  `prefix` text,
  `name` text,
  `international` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`prefix_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `prompts`
--

DROP TABLE IF EXISTS `prompts`;

CREATE TABLE `prompts` (
  `prompt_id` int(11) NOT NULL AUTO_INCREMENT,
  `prompt` text,
  `description` text,
  `status` text,
  `file` text,
  PRIMARY KEY (`prompt_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `param` text,
  `value` text,
  `description` text,
  PRIMARY KEY (`setting_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `short_names`
--

DROP TABLE IF EXISTS `short_names`;

CREATE TABLE `short_names` (
  `short_name_id` int(11) NOT NULL AUTO_INCREMENT,
  `short_name` varchar(20) DEFAULT NULL,
  `name` text,
  `number` varchar(3) DEFAULT NULL,
  `extension_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`short_name_id`),
  UNIQUE KEY `short_name` (`short_name`) USING BTREE,
  UNIQUE KEY `number` (`number`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `sig_trunks`
--

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


--
-- Table structure for table `time_frames`
--

DROP TABLE IF EXISTS `time_frames`;

CREATE TABLE `time_frames` (
  `time_frame_id` int(11) NOT NULL AUTO_INCREMENT,
  `prompt_id` int(11) DEFAULT NULL,
  `day` text,
  `start_hour` text,
  `end_hour` text,
  `numeric_day` int(11) DEFAULT NULL,
  PRIMARY KEY (`time_frame_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `time_frames` WRITE;

INSERT INTO `time_frames` (
  `time_frame_id`,
  `prompt_id`,
  `day`,
  `start_hour`,
  `end_hour`,
  `numeric_day`
)
VALUES
(NULL, '1', 'Sunday',     NULL, NULL, '0'),
(NULL, '1', 'Monday',     '4',  '14', '1'),
(NULL, '1', 'Tuesday',    '4',  '14', '2'),
(NULL, '1', 'Wednesday',  '4',  '14', '3'),
(NULL, '1', 'Thursday',   '4',  '14', '4'),
(NULL, '1', 'Friday',     '4',  '14', '5'),
(NULL, '1', 'Saturday',   NULL, NULL, '6');

UNLOCK TABLES;


--
-- Table structure for table `users`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `users` WRITE;

INSERT INTO `users` (
  `user_id`,
  `username`,
  `password`,
  `firstname`,
  `lastname`,
  `email`,
  `description`,
  `fax_number`,
  `ident`,
  `login_attempts`
)
VALUES
(NULL, 'admin', 'admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

UNLOCK TABLES;


--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;

CREATE TABLE `modules` (
  `module_name_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(70) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `version` varchar(20) DEFAULT NULL,
  `condition` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`module_name_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `modules` WRITE;

INSERT INTO `modules` (
  `module_name_id`,
  `module_name`,
  `description`,
  `version`,
  `condition`
)
VALUES (
  1,
  'Call_website_Grid',
  'Позволяет IP-АТС обрабатывать входящие вызовы, совершаемые с web-сайтов.',
  '1.0',
  0
), (
  2,
  'Mail_Settings_Panel',
  'Позволяет IP-АТС отслеживать те или иные вызовы и уведомлять о них посредствам рассылки электронных писем.',
  '1.0',
  0
);

UNLOCK TABLES;


--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `param` text,
  `value` text,
  `description` text,
  PRIMARY KEY (`setting_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `settings` WRITE;

INSERT INTO `settings` (
  `setting_id`,
  `param`,
  `value`,
  `description`
)
VALUES (
  NULL,
  'vm',
  'external/nodata/leavemaildb.php',
  NULL
), (
  NULL,
  'version',
  '1',
  NULL
), (
  NULL,
  'annonymous_calls',
  'yes',
  NULL
), (
  NULL,
  'callername',
  NULL,
  NULL
), (
  NULL,
  'prefix',
  NULL,
  NULL
), (
  NULL,
  'callerid',
  NULL,
  NULL
), (
  NULL,
  'international_calls',
  'yes',
  NULL
), (
  NULL,
  'international_calls_live',
  'yes',
  NULL
);

UNLOCK TABLES;