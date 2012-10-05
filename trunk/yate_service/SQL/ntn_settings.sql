/*
Navicat MySQL Data Transfer

Source Server         : 172.17.2.48
Source Server Version : 50162
Source Host           : localhost:3306
Source Database       : FREESENTRAL

Target Server Type    : MYSQL
Target Server Version : 50162
File Encoding         : 65001

Date: 2012-09-26 16:27:45
*/

SET FOREIGN_KEY_CHECKS=0;

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
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ntn_settings
-- ----------------------------
INSERT INTO `ntn_settings` VALUES ('1', 'incoming_trunk', 'true', null);
INSERT INTO `ntn_settings` VALUES ('2', 'exclude_called', '104', null);
INSERT INTO `ntn_settings` VALUES ('3', 'exclude_called', '103', null);
INSERT INTO `ntn_settings` VALUES ('4', 'incoming_call', 'true', null);
INSERT INTO `ntn_settings` VALUES ('5', 'outgoing_call', 'true', null);
INSERT INTO `ntn_settings` VALUES ('6', 'internal_call', 'false', null);
INSERT INTO `ntn_settings` VALUES ('7', 'incoming_trunk_text', '    Абонент: <caller>\\n    Входящая линия: <called>\\n    Дата: <ftime>', null);
INSERT INTO `ntn_settings` VALUES ('8', 'incoming_call_text', '    Абонент: <caller>\\n    Входящая линия: <incoming_trunk>\\n    Кому: <called>\\n    Дата: <ftime>\\n    Длительность: <duration>\\n    Состояние: <status>\\n    Тип: <type>', null);
