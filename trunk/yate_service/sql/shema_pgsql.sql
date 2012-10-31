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

Date: 2012-06-15 17:26:02
*/


-- ----------------------------
-- Sequence structure for "public"."dial_plans_dial_plan_id_seq"
-- ----------------------------
DROP SEQUENCE "public"."dial_plans_dial_plan_id_seq";
CREATE SEQUENCE "public"."dial_plans_dial_plan_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 2
 CACHE 1;

-- ----------------------------
-- Sequence structure for "public"."dids_did_id_seq"
-- ----------------------------
DROP SEQUENCE "public"."dids_did_id_seq";
CREATE SEQUENCE "public"."dids_did_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for "public"."extensions_extension_id_seq"
-- ----------------------------
DROP SEQUENCE "public"."extensions_extension_id_seq";
CREATE SEQUENCE "public"."extensions_extension_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 4
 CACHE 1;

-- ----------------------------
-- Sequence structure for "public"."gateways_gateway_id_seq"
-- ----------------------------
DROP SEQUENCE "public"."gateways_gateway_id_seq";
CREATE SEQUENCE "public"."gateways_gateway_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 2
 CACHE 1;

-- ----------------------------
-- Sequence structure for "public"."group_members_group_member_id_seq"
-- ----------------------------
DROP SEQUENCE "public"."group_members_group_member_id_seq";
CREATE SEQUENCE "public"."group_members_group_member_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for "public"."groups_group_id_seq"
-- ----------------------------
DROP SEQUENCE "public"."groups_group_id_seq";
CREATE SEQUENCE "public"."groups_group_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for "public"."incoming_gateways_incoming_gateway_id_seq"
-- ----------------------------
DROP SEQUENCE "public"."incoming_gateways_incoming_gateway_id_seq";
CREATE SEQUENCE "public"."incoming_gateways_incoming_gateway_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for "public"."keys_key_id_seq"
-- ----------------------------
DROP SEQUENCE "public"."keys_key_id_seq";
CREATE SEQUENCE "public"."keys_key_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for "public"."limits_international_limit_international_id_seq"
-- ----------------------------
DROP SEQUENCE "public"."limits_international_limit_international_id_seq";
CREATE SEQUENCE "public"."limits_international_limit_international_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 3
 CACHE 1;

-- ----------------------------
-- Sequence structure for "public"."music_on_hold_music_on_hold_id_seq"
-- ----------------------------
DROP SEQUENCE "public"."music_on_hold_music_on_hold_id_seq";
CREATE SEQUENCE "public"."music_on_hold_music_on_hold_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 7
 CACHE 1;

-- ----------------------------
-- Sequence structure for "public"."network_interfaces_network_interface_id_seq"
-- ----------------------------
DROP SEQUENCE "public"."network_interfaces_network_interface_id_seq";
CREATE SEQUENCE "public"."network_interfaces_network_interface_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for "public"."pbx_settings_pbx_setting_id_seq"
-- ----------------------------
DROP SEQUENCE "public"."pbx_settings_pbx_setting_id_seq";
CREATE SEQUENCE "public"."pbx_settings_pbx_setting_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for "public"."playlist_items_playlist_item_id_seq"
-- ----------------------------
DROP SEQUENCE "public"."playlist_items_playlist_item_id_seq";
CREATE SEQUENCE "public"."playlist_items_playlist_item_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 7
 CACHE 1;

-- ----------------------------
-- Sequence structure for "public"."playlists_playlist_id_seq"
-- ----------------------------
DROP SEQUENCE "public"."playlists_playlist_id_seq";
CREATE SEQUENCE "public"."playlists_playlist_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for "public"."prefixes_prefix_id_seq"
-- ----------------------------
DROP SEQUENCE "public"."prefixes_prefix_id_seq";
CREATE SEQUENCE "public"."prefixes_prefix_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 3
 CACHE 1;

-- ----------------------------
-- Sequence structure for "public"."prompts_prompt_id_seq"
-- ----------------------------
DROP SEQUENCE "public"."prompts_prompt_id_seq";
CREATE SEQUENCE "public"."prompts_prompt_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for "public"."settings_setting_id_seq"
-- ----------------------------
DROP SEQUENCE "public"."settings_setting_id_seq";
CREATE SEQUENCE "public"."settings_setting_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 8
 CACHE 1;

-- ----------------------------
-- Sequence structure for "public"."short_names_short_name_id_seq"
-- ----------------------------
DROP SEQUENCE "public"."short_names_short_name_id_seq";
CREATE SEQUENCE "public"."short_names_short_name_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for "public"."sig_trunks_sig_trunk_id_seq"
-- ----------------------------
DROP SEQUENCE "public"."sig_trunks_sig_trunk_id_seq";
CREATE SEQUENCE "public"."sig_trunks_sig_trunk_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for "public"."time_frames_time_frame_id_seq"
-- ----------------------------
DROP SEQUENCE "public"."time_frames_time_frame_id_seq";
CREATE SEQUENCE "public"."time_frames_time_frame_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Sequence structure for "public"."users_user_id_seq"
-- ----------------------------
DROP SEQUENCE "public"."users_user_id_seq";
CREATE SEQUENCE "public"."users_user_id_seq"
 INCREMENT 1
 MINVALUE 1
 MAXVALUE 9223372036854775807
 START 1
 CACHE 1;

-- ----------------------------
-- Table structure for "public"."actionlogs"
-- ----------------------------
DROP TABLE "public"."actionlogs";
CREATE TABLE "public"."actionlogs" (
"date" numeric(17,3),
"log" text,
"performer_id" text,
"performer" text,
"real_performer_id" text,
"object" text,
"query" text,
"ip" text
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."call_logs"
-- ----------------------------
DROP TABLE "public"."call_logs";
CREATE TABLE "public"."call_logs" (
"chan" text,
"address" text,
"direction" text,
"billid" text,
"caller" text,
"called" text,
"status" text,
"reason" text,
"time" numeric(17,3),
"duration" numeric(6,3),
"billtime" numeric(6,3),
"ringtime" numeric(6,3),
"ended" int2
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."card_confs"
-- ----------------------------
DROP TABLE "public"."card_confs";
CREATE TABLE "public"."card_confs" (
"param_name" text,
"param_value" text,
"section_name" text,
"module_name" text
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."card_ports"
-- ----------------------------
DROP TABLE "public"."card_ports";
CREATE TABLE "public"."card_ports" (
"BUS" int2,
"SLOT" int2,
"PORT" int2,
"filename" text,
"span" text,
"type" text,
"card_type" text,
"voice_interface" text,
"sig_interface" text,
"voice_chans" text,
"sig_chans" text,
"echocancel" ,
"dtmfdetect" ,
"name" text
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."dial_plans"
-- ----------------------------
DROP TABLE "public"."dial_plans";
CREATE TABLE "public"."dial_plans" (
"dial_plan_id" int4 DEFAULT nextval('dial_plans_dial_plan_id_seq'::regclass) NOT NULL,
"dial_plan" text,
"priority" int2,
"prefix" text,
"gateway_id" int4,
"nr_of_digits_to_cut" int2,
"position_to_start_cutting" int2,
"nr_of_digits_to_replace" int2,
"digits_to_replace_with" text,
"position_to_start_replacing" int2,
"position_to_start_adding" int2,
"digits_to_add" text
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."dids"
-- ----------------------------
DROP TABLE "public"."dids";
CREATE TABLE "public"."dids" (
"did_id" int4 DEFAULT nextval('dids_did_id_seq'::regclass) NOT NULL,
"did" text,
"number" text,
"destination" text,
"description" text,
"extension_id" int4,
"group_id" int4
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."extensions"
-- ----------------------------
DROP TABLE "public"."extensions";
CREATE TABLE "public"."extensions" (
"extension_id" int4 DEFAULT nextval('extensions_extension_id_seq'::regclass) NOT NULL,
"extension" text,
"password" text,
"firstname" text,
"lastname" text,
"address" text,
"inuse" int4,
"location" text,
"max_minutes" numeric(6,3),
"used_minutes" numeric(6,3),
"inuse_count" int2,
"login_attempts" int2,
"expires" numeric(17,3),
"inuse_last" numeric(17,3)
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."gateways"
-- ----------------------------
DROP TABLE "public"."gateways";
CREATE TABLE "public"."gateways" (
"gateway_id" int4 DEFAULT nextval('gateways_gateway_id_seq'::regclass) NOT NULL,
"gateway" text,
"protocol" text,
"server" text,
"type" text,
"username" text,
"password" text,
"description" text,
"interval" text,
"authname" text,
"domain" text,
"outbound" text,
"localaddress" text,
"formats" text,
"rtp_localip" text,
"ip_transport" text,
"oip_transport" text,
"port" text,
"iaxuser" text,
"iaxcontext" text,
"status" text,
"callerid" text,
"callername" text,
"sig_trunk_id" int4,
"enabled" int2,
"modified" int2,
"rtp_forward" int2,
"send_extension" int2,
"trusted" int2
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."group_members"
-- ----------------------------
DROP TABLE "public"."group_members";
CREATE TABLE "public"."group_members" (
"group_member_id" int4 DEFAULT nextval('group_members_group_member_id_seq'::regclass) NOT NULL,
"group_id" int4,
"extension_id" int4
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."groups"
-- ----------------------------
DROP TABLE "public"."groups";
CREATE TABLE "public"."groups" (
"group_id" int4 DEFAULT nextval('groups_group_id_seq'::regclass) NOT NULL,
"group" text,
"description" text,
"extension" text,
"mintime" int2,
"length" int2,
"maxout" int2,
"greeting" text,
"maxcall" int2,
"prompt" text,
"detail" ,
"playlist_id" int4
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."incoming_gateways"
-- ----------------------------
DROP TABLE "public"."incoming_gateways";
CREATE TABLE "public"."incoming_gateways" (
"incoming_gateway_id" int4 DEFAULT nextval('incoming_gateways_incoming_gateway_id_seq'::regclass) NOT NULL,
"incoming_gateway" text,
"gateway_id" int4,
"ip" text
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."keys"
-- ----------------------------
DROP TABLE "public"."keys";
CREATE TABLE "public"."keys" (
"key_id" int4 DEFAULT nextval('keys_key_id_seq'::regclass) NOT NULL,
"key" text,
"prompt_id" int4,
"destination" text,
"description" text
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."limits_international"
-- ----------------------------
DROP TABLE "public"."limits_international";
CREATE TABLE "public"."limits_international" (
"limit_international_id" int4 DEFAULT nextval('limits_international_limit_international_id_seq'::regclass) NOT NULL,
"limit_international" text,
"name" text,
"value" text
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."music_on_hold"
-- ----------------------------
DROP TABLE "public"."music_on_hold";
CREATE TABLE "public"."music_on_hold" (
"music_on_hold_id" int4 DEFAULT nextval('music_on_hold_music_on_hold_id_seq'::regclass) NOT NULL,
"music_on_hold" text,
"description" text,
"file" text
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."network_interfaces"
-- ----------------------------
DROP TABLE "public"."network_interfaces";
CREATE TABLE "public"."network_interfaces" (
"network_interface_id" int4 DEFAULT nextval('network_interfaces_network_interface_id_seq'::regclass) NOT NULL,
"network_interface" text,
"protocol" text,
"ip_address" text,
"netmask" text,
"gateway" text
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."pbx_settings"
-- ----------------------------
DROP TABLE "public"."pbx_settings";
CREATE TABLE "public"."pbx_settings" (
"pbx_setting_id" int4 DEFAULT nextval('pbx_settings_pbx_setting_id_seq'::regclass) NOT NULL,
"extension_id" int4,
"param" text,
"value" text
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."playlist_items"
-- ----------------------------
DROP TABLE "public"."playlist_items";
CREATE TABLE "public"."playlist_items" (
"playlist_item_id" int4 DEFAULT nextval('playlist_items_playlist_item_id_seq'::regclass) NOT NULL,
"playlist_id" int4,
"music_on_hold_id" int4
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."playlists"
-- ----------------------------
DROP TABLE "public"."playlists";
CREATE TABLE "public"."playlists" (
"playlist_id" int4 DEFAULT nextval('playlists_playlist_id_seq'::regclass) NOT NULL,
"playlist" text,
"in_use" int2
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."prefixes"
-- ----------------------------
DROP TABLE "public"."prefixes";
CREATE TABLE "public"."prefixes" (
"prefix_id" int4 DEFAULT nextval('prefixes_prefix_id_seq'::regclass) NOT NULL,
"prefix" text,
"name" text,
"international" int2
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."prompts"
-- ----------------------------
DROP TABLE "public"."prompts";
CREATE TABLE "public"."prompts" (
"prompt_id" int4 DEFAULT nextval('prompts_prompt_id_seq'::regclass) NOT NULL,
"prompt" text,
"description" text,
"status" text,
"file" text
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."settings"
-- ----------------------------
DROP TABLE "public"."settings";
CREATE TABLE "public"."settings" (
"setting_id" int4 DEFAULT nextval('settings_setting_id_seq'::regclass) NOT NULL,
"param" text,
"value" text,
"description" text
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."short_names"
-- ----------------------------
DROP TABLE "public"."short_names";
CREATE TABLE "public"."short_names" (
"short_name_id" int4 DEFAULT nextval('short_names_short_name_id_seq'::regclass) NOT NULL,
"short_name" text,
"name" text,
"number" text,
"extension_id" int4
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."sig_trunks"
-- ----------------------------
DROP TABLE "public"."sig_trunks";
CREATE TABLE "public"."sig_trunks" (
"sig_trunk_id" int4 DEFAULT nextval('sig_trunks_sig_trunk_id_seq'::regclass) NOT NULL,
"sig_trunk" text,
"enable" text,
"type" text,
"switchtype" text,
"sig" text,
"voice" text,
"number" text,
"rxunderrun" int2,
"strategy" text,
"strategy-restrict" text,
"userparttest" int2,
"channelsync" int4,
"channellock" int4,
"numplan" text,
"numtype" text,
"presentation" text,
"screening" text,
"format" text,
"print-messages" text,
"print-frames" text,
"extended-debug" text,
"layer2dump" text,
"layer3dump" text,
"port" text
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."time_frames"
-- ----------------------------
DROP TABLE "public"."time_frames";
CREATE TABLE "public"."time_frames" (
"time_frame_id" int4 DEFAULT nextval('time_frames_time_frame_id_seq'::regclass) NOT NULL,
"prompt_id" int4,
"day" text,
"start_hour" text,
"end_hour" text,
"numeric_day" int2
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Table structure for "public"."users"
-- ----------------------------
DROP TABLE "public"."users";
CREATE TABLE "public"."users" (
"user_id" int4 DEFAULT nextval('users_user_id_seq'::regclass) NOT NULL,
"username" text,
"password" text,
"firstname" text,
"lastname" text,
"email" text,
"description" text,
"fax_number" text,
"ident" text,
"login_attempts" int2
)
WITH (OIDS=TRUE)

;

-- ----------------------------
-- Alter Sequences Owned By 
-- ----------------------------
ALTER SEQUENCE "public"."dial_plans_dial_plan_id_seq" OWNED BY "dial_plans"."dial_plan_id";
ALTER SEQUENCE "public"."dids_did_id_seq" OWNED BY "dids"."did_id";
ALTER SEQUENCE "public"."extensions_extension_id_seq" OWNED BY "extensions"."extension_id";
ALTER SEQUENCE "public"."gateways_gateway_id_seq" OWNED BY "gateways"."gateway_id";
ALTER SEQUENCE "public"."group_members_group_member_id_seq" OWNED BY "group_members"."group_member_id";
ALTER SEQUENCE "public"."groups_group_id_seq" OWNED BY "groups"."group_id";
ALTER SEQUENCE "public"."incoming_gateways_incoming_gateway_id_seq" OWNED BY "incoming_gateways"."incoming_gateway_id";
ALTER SEQUENCE "public"."keys_key_id_seq" OWNED BY "keys"."key_id";
ALTER SEQUENCE "public"."limits_international_limit_international_id_seq" OWNED BY "limits_international"."limit_international_id";
ALTER SEQUENCE "public"."music_on_hold_music_on_hold_id_seq" OWNED BY "music_on_hold"."music_on_hold_id";
ALTER SEQUENCE "public"."network_interfaces_network_interface_id_seq" OWNED BY "network_interfaces"."network_interface_id";
ALTER SEQUENCE "public"."pbx_settings_pbx_setting_id_seq" OWNED BY "pbx_settings"."pbx_setting_id";
ALTER SEQUENCE "public"."playlist_items_playlist_item_id_seq" OWNED BY "playlist_items"."playlist_item_id";
ALTER SEQUENCE "public"."playlists_playlist_id_seq" OWNED BY "playlists"."playlist_id";
ALTER SEQUENCE "public"."prefixes_prefix_id_seq" OWNED BY "prefixes"."prefix_id";
ALTER SEQUENCE "public"."prompts_prompt_id_seq" OWNED BY "prompts"."prompt_id";
ALTER SEQUENCE "public"."settings_setting_id_seq" OWNED BY "settings"."setting_id";
ALTER SEQUENCE "public"."short_names_short_name_id_seq" OWNED BY "short_names"."short_name_id";
ALTER SEQUENCE "public"."sig_trunks_sig_trunk_id_seq" OWNED BY "sig_trunks"."sig_trunk_id";
ALTER SEQUENCE "public"."time_frames_time_frame_id_seq" OWNED BY "time_frames"."time_frame_id";
ALTER SEQUENCE "public"."users_user_id_seq" OWNED BY "users"."user_id";

-- ----------------------------
-- Indexes structure for table actionlogs
-- ----------------------------
CREATE INDEX "actionlogs-index" ON "public"."actionlogs" USING btree ("date");


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

