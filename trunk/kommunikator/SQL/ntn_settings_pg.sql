/*
Navicat PGSQL Data Transfer

Source Server         : ats-dev.digt.local
Source Server Version : 80403
Source Host           : 172.17.2.48:5432
Source Database       : freesentral
Source Schema         : public

Target Server Type    : PGSQL
Target Server Version : 80403
File Encoding         : 65001

Date: 2012-09-26 16:28:55
*/


-- ----------------------------
-- Table structure for "public"."ntn_settings"
-- ----------------------------
DROP SEQUENCE "public"."ntn_settings_ntn_setting_id_seq";
CREATE SEQUENCE "public"."ntn_settings_ntn_setting_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

DROP TABLE "public"."ntn_settings";
CREATE TABLE "public"."ntn_settings" (
"ntn_setting_id" int4 DEFAULT nextval('ntn_settings_ntn_setting_id_seq'::regclass) NOT NULL,
"param" text,
"value" text,
"description" text
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Records of settings
-- ----------------------------
INSERT INTO "public"."ntn_settings"(param,value) VALUES ('incoming_trunk', 'true');                                                                                                                                                                                 
INSERT INTO "public"."ntn_settings"(param,value) VALUES ('exclude_called', '104');                                                                                                                                                                                  
INSERT INTO "public"."ntn_settings"(param,value) VALUES ('exclude_called', '103');                                                                                                                                                                                  
INSERT INTO "public"."ntn_settings"(param,value) VALUES ('incoming_call', 'true');                                                                                                                                                                                  
INSERT INTO "public"."ntn_settings"(param,value) VALUES ('outgoing_call', 'true');                                                                                                                                                                                  
INSERT INTO "public"."ntn_settings"(param,value) VALUES ('internal_call', 'false');                                                                                                                                                                                 
INSERT INTO "public"."ntn_settings"(param,value) VALUES ('incoming_trunk_text', '    └сюэхэЄ: <caller>\\n    ┬їюф ∙р  ышэш : <called>\\n    ─рЄр: <ftime>');                                                                                                        
INSERT INTO "public"."ntn_settings"(param,value) VALUES ('incoming_call_text', '    └сюэхэЄ: <caller>\\n    ┬їюф ∙р  ышэш : <incoming_trunk>\\n    ╩юьє: <called>\\n    ─рЄр: <ftime>\\n    ─ышЄхы№эюёЄ№: <duration>\\n    ╤юёЄю эшх: <status>\\n    ╥шя: <type>'); 

-- ----------------------------
-- Alter Sequences Owned By 
-- ----------------------------
