<?php
/**
 * lib_gateways.php
 * This file is part of the FreeSentral Project http://freesentral.com
 *
 * FreeSentral - is a Web Graphical User Interface for easy configuration of the Yate PBX software
 * Copyright (C) 2008-2009 Null Team
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301, USA.
 */
?>
<?php
global  $module, $method, $path, $action, $page, $limit, $fields_for_extensions, $operations_for_extensions, $upload_path;

function edit_gateway($error=NULL, $protocol = "sip", $gw_type = 'reg')
{
	if($_SESSION["level"] != "admin") {
		forbidden();
		return;
	}

	if($error)
		errornote($error);

	if($gw_type == "reg")
		$gw_type = "Yes";
	elseif($gw_type == "noreg")
		$gw_type = "No";

	$gateway_id = getparam("gateway_id");

	$gateway = new Gateway;
	$gateway->gateway_id = $gateway_id;
	$gateway->select();

	$ip_transport = $oip_transport = array("UDP","TLS","TCP");
	$ip_transport["selected"] = ($gateway->ip_transport) ? $gateway->ip_transport : "UDP";
	$oip_transport["selected"] = ($gateway->oip_transport) ? $gateway->oip_transport : "UDP";

//fields for gateway with registration
	$sip_fields = array(
		"gateway"=>array("compulsory"=>true), 
		"username"=>array("compulsory"=>true, "Username is normally used to authenticate to the other server. It is the user part of the SIP address of your server when talking to the gateway you are currently defining.", "autocomplete"=>"off"), 
		"password"=>array("comment"=>"Insert only when you wish to change", "display"=>"password", "autocomplete"=>"off"),
		"server"=>array("compulsory"=>true, "comment"=>"Ex:10.5.5.5:5060, 10.5.5.5:5061 It is IP address of the gateway : port number used for sip on that machine. If transport is TLS then 5061 is the default port, otherwise 5060 is the default."),
		"description"=>array("display"=>"textarea"),
		"ip_transport"=>array($ip_transport, "display"=>"select","advanced"=>true, "column_name"=>"Transport", "comment"=>"Protocol used to register to gateway and sending calls. Default is UDP. If you use TLS keep in mind you might need to change the port value in 'Server' to 5061, as this is the default for TLS."),
		"rtp_localip"=>array("comment"=>"IP address to bind the RTP to. This overwrittes setting from yrtpchan.conf, if set.", "advanced"=>true, "column_name"=>"RTP local IP"),
		"authname"=>array("advanced"=>true, "comment"=>"Authentication ID is an ID used strictly for authentication purpose when the phone attempts to contact the SIP server. This may or may not be the same as the above field username. Set only if it's different."), 
		"outbound"=>array("advanced"=>true, "comment"=>"An Outbound proxy is mostly used in presence of a firewall/NAT to handle the signaling and media traffic across the firewall. Generally, if you have an outbound proxy and you are not using STUN or other firewall/NAT traversal mechanisms, you can use it. However, if you are using STUN or other firewall/NAT traversal tools, do not use an outbound proxy at the same time."),
		"domain"=>array("advanced"=>true, "comment"=>"Domain in which the server is in."),
		"localaddress"=>array("advanced"=>true, "comment"=>"Insert when you wish to force a certain address to be considered as the default address."),
		"interval"=>array("advanced"=>true, "comment"=>"Represents the interval in which the registration will expires. Default value is 600 seconds."),
		"formats"=>array("advanced"=>true,"display"=>"include_formats", "comment"=>"Codecs to be used. If none of the formats is checked then server will try to negociate formats automatically"), 
		"rtp_forward"=> array("advanced"=>true,"display"=>"checkbox", "comment"=>"Check this box so that the rtp won't pass  through yate(when possible)."),
		"enabled"=>array("comment"=>"Check this field to mark that you wish to register to this server"),
		"callerid"=>array("advanced"=>true, "comment"=>"Use this to set the caller number when call is routed to this gateway. If none set then the System's CallerID will be used."),
		"callername"=>array("advanced"=>true, "comment"=>"Use this to set the callername when call is routed to this gateway. If none set then the System's Callername will be used."),
		"send_extension"=>array("advanced"=>true, "display"=>"checkbox", "comment"=>"Check this if you want to send the extension as caller number when routing to this gateway."),
		"trusted"=>array("advanced"=>true, "display"=>"checkbox", "comment"=>"Allow calls from this gateway or it's associated gateways to be routed to another gateway."),
		"default_dial_plan"=>array("display"=>"checkbox", "comment"=>"Check this box if you wish to automatically add a dial plan for this gateway. The new dial plan is going to match all prefixed and will have the smallest priority.")
	);

	$h323_fields = $iax_fields = array(
		"gateway"=>array("compulsory"=>true),
		"username"=>array("compulsory"=>true, "autocomplete"=>"off"), 
		"password"=>array("comment"=>"Insert only when you wish to change", "display"=>"password", "autocomplete"=>"off"),
		"server"=>array("compulsory"=>true, "comment"=>"Ex:10.5.5.5:1720 It is IP address of the gateway : port number used for H323 on that machine."),
		"description"=>array("display"=>"textarea"), 
		"interval"=>array("advanced"=>true, "comment"=>"Represents the interval in which the registration will expires. Default value is 600 seconds."), 
		"formats"=>array("advanced"=>true,"display"=>"include_formats", "comment"=>"Codecs to be used. If none of the formats is checked then server will try to negociate formats automatically"), 
		"rtp_forward"=> array("advanced"=>true,"display"=>"checkbox", "comment"=>"Check this box so that the rtp won't pass  through yate(when possible)"),
		"enabled"=>array("comment"=>"Check this field to mark that you wish to register to this server"),
		"callerid"=>array("advanced"=>true, "comment"=>"Use this to set the caller number when call is routed to this gateway. If none set then the System's CallerID will be used."),
		"callername"=>array("advanced"=>true, "comment"=>"Use this to set the callername when call is routed to this gateway. If none set then the System's Callername will be used."),
		"send_extension"=>array("advanced"=>true, "display"=>"checkbox", "comment"=>"Check this if you want to send the extension as caller number when routing to this gateway."),
		"trusted"=>array("advanced"=>true, "display"=>"checkbox", "comment"=>"Allow calls from this gateway or it's associated gateways to be routed to another gateway."),
		"default_dial_plan"=>array("display"=>"checkbox", "comment"=>"Check this box if you wish to automatically add a dial plan for this gateway. The new dial plan is going to match all prefixed and will have the smallesc priority.")
	);
	unset($iax_fields["rtp_forward"]);

// fields for gateways without registration
	$sip = $h323 = array(
		"gateway"=>array("compulsory"=>true),
		'server'=>array("compulsory"=>true), 
		'port'=>array("compulsory"=>true), 
		"oip_transport"=>array($oip_transport, "display"=>"select", "advanced"=>true, "column_name"=>"Transport", "comment"=>"Protocol used for sending calls. Default is UDP."),
		"rtp_localip"=>array("comment"=>"IP address to bind the RTP to. This overwrittes setting from yrtpchan.conf, if set.", "advanced"=>true, "column_name"=>"RTP local IP","comment"=>"Protocol used for sending calls."),
		'formats'=>array("advanced"=>true,"display"=>"include_formats", "comment"=>"If none of the formats is checked then server will try to negociate formats automatically"), 
		//	'check_not_to_specify_formats' => array($check_not_to_specify_formats, "display"=>"checkbox"), 
		'rtp_forward'=> array("advanced"=>true,"display"=>"checkbox", "comment"=>"Check this box so that the rtp won't pass  through yate(when possible)"),
		"callerid"=>array("advanced"=>true, "comment"=>"Use this to set the caller number when call is routed to this gateway. If none set then the System's CallerID will be used."),
		"callername"=>array("advanced"=>true, "comment"=>"Use this to set the callername when call is routed to this gateway. If none set then the System's Callername will be used."),
		"send_extension"=>array("advanced"=>true, "display"=>"checkbox", "comment"=>"Check this if you want to send the extension as caller number when routing to this gateway."),
		"trusted"=>array("advanced"=>true, "display"=>"checkbox", "comment"=>"Allow calls from this gateway or it's associated gateways to be routed to another gateway."),
		"default_dial_plan"=>array("display"=>"checkbox", "comment"=>"Check this box if you wish to automatically add a dial plan for this gateway. The new dial plan is going to match all prefixed and will have the smallesc priority.")
	);
	unset($h323["rtp_localip"]);
	unset($h323["oip_transport"]);
	$sip["port"]["comment"] = "If 'Transport' is TLS, default port is 5061.";
	$sip["oip_transport"]["javascript"] = "onChange='check_transport();'";

	$pstn = array(
		"gateway"=>array("compulsory"=>true, "comment"=>"This must be defined as a link in isigchan.conf"),
	#	'chans_group'=>array("compulsory"=>true), 
	#	'formats'=>array("advanced"=>true,"display"=>"include_formats", "comment"=>"If none of the formats is checked then server will try to negociate formats automatically") ,
		"callerid"=>array("advanced"=>true, "comment"=>"Use this to set the caller number when call is routed to this gateway. If none set then the System's CallerID will be used."),
		"callername"=>array("advanced"=>true, "comment"=>"Use this to set the callername when call is routed to this gateway. If none set then the System's Callername will be used."),
		"send_extension"=>array("advanced"=>true, "display"=>"checkbox", "comment"=>"Check this if you want to send the extension as caller number when routing to this gateway."),
		"trusted"=>array("advanced"=>true, "display"=>"checkbox", "comment"=>"Allow calls from this gateway or it's associated gateways to be routed to another gateway."),
		"default_dial_plan"=>array("display"=>"checkbox", "comment"=>"Check this box if you wish to automatically add a dial plan for this gateway. The new dial plan is going to match all prefixed and will have the smallesc priority.")
	//	'check_not_to_specify_formats' => array($check_not_to_specify_formats, "display"=>"checkbox"), 
	);

	$iax = array(
		"gateway"=>array("compulsory"=>true),
		'server'=>array("compulsory"=>true), 
		'port'=>array("compulsory"=>true), 
		'iaxuser'=>array("advanced"=>true), 
		'iaxcontext'=>array("advanced"=>true), 
		'formats'=>array("advanced"=>true,"display"=>"include_formats", "comment"=>"If none of the formats is checked then server will try to negociate formats automatically") ,
		"callerid"=>array("advanced"=>true, "comment"=>"Use this to set the caller number when call is routed to this gateway. If none set then the System's CallerID will be used."),
		"callername"=>array("advanced"=>true, "comment"=>"Use this to set the callername when call is routed to this gateway. If none set then the System's Callername will be used."),
		"send_extension"=>array("advanced"=>true, "display"=>"checkbox", "comment"=>"Check this if you want to send the extension as caller number when routing to this gateway."),
		"trusted"=>array("advanced"=>true, "display"=>"checkbox", "comment"=>"Allow calls from this gateway or it's associated gateways to be routed to another gateway."),
		"default_dial_plan"=>array("display"=>"checkbox", "comment"=>"Check this box if you wish to automatically add a dial plan for this gateway. The new dial plan is going to match all prefixed and will have the smallesc priority.")
	//	'check_not_to_specify_formats' => array($check_not_to_specify_formats, "display"=>"checkbox"), 
	);

	$sig_trunk = new Sig_trunk;
	if($gateway->sig_trunk_id) {
		$sig_trunk->sig_trunk_id = $gateway->sig_trunk_id;
		$sig_trunk->select();
	}

	$gateway->merge($sig_trunk);

	$switchtype = array("euro-isdn-e1", "euro-isdn-t1", "national-isdn", "dms100", "lucent5e", "att4ess", "qsig", "unknown");
	$switchtype["selected"] = ($sig_trunk->switchtype) ? $sig_trunk->switchtype : "unknown";

	$strategy = array("increment", "decrement", "lowest", "highest", "random");
	$strategy["selected"] = ($sig_trunk->strategy) ? $sig_trunk->strategy : "increment";

	$strategy_restrict = array("even", "odd", "even-fallback", "odd-fallback");
	$strategy_restrict["selected"] = $sig_trunk->{"strategy-restrict"};

	$numplans = array("unknown", "isdn", "data", "telex", "national", "private");
	$numplans["selected"] = $sig_trunk->numplan;

	$numtypes = array("unknown","international","national","net-specific","subscriber","abbreviated","reserved");
	$numtypes["selected"] = $sig_trunk->numtype;

	$presentation = array("allowed", "restricted", "unavailable");
	$presentation["selected"] = $sig_trunk->presentation;

	$screening = array("user-provided", "user-provided-passed", "user-provided-failed", "network-provided");
	$screening["selected"] = $sig_trunk->screening;

	$pri_types = array("isdn-pri-cpe", "isdn-pri-net");
	$pri_types["selected"] = $sig_trunk->type;

	$card_port = new Card_port;
	$bri_ports = $card_port->fieldSelect("name", array("card_type"=>"BRI", "type"=>"TE"), null, "name", array("column"=>"name", "inner_column"=>"port", "relation"=>"NOT IN", "inner_table"=>"sig_trunks"));
	if(!$bri_ports)
		$bri_ports = array();
	else{
		$arr = array();
		if(!is_array($bri_ports))
			$bri_ports = array(0=>array("name"=>$bri_ports));
		for($i=0; $i<count($bri_ports); $i++)
			$arr[] = $bri_ports[$i]["name"];
		$bri_ports = $arr;
	}
	if($sig_trunk->port) 
		$bri_ports[] = $sig_trunk->port;
	$pri_ports = $card_port->fieldSelect("name", array("card_type"=>"PRI"), null, "name", array("column"=>"name", "inner_column"=>"port", "relation"=>"NOT IN", "inner_table"=>"sig_trunks"));
	if(!$pri_ports)
		$pri_ports = array();
	else{
		$arr = array();
		if(!is_array($pri_ports))
			$pri_ports = array(0=>array("name"=>$pri_ports));
		for($i=0; $i<count($pri_ports); $i++)
			$arr[] = $pri_ports[$i]["name"];
		$pri_ports = $arr;
	}
	if($sig_trunk->port)
		$pri_ports[] = $sig_trunk->port;

	$format = array("alaw", "mulaw", "g721");
	$BRI = $PRI = array(
		"sig_trunk_id" => array("value"=>$sig_trunk->sig_trunk_id, "display"=>"hidden"),

		"gateway" => array("column_name"=>_("Gateway"), "compulsory"=>true),

		"enable" => array("column_name"=>_("Enable"), "display"=>"checkbox", "value"=> "t"),

		"port" => array($bri_ports, "column_name"=>_("Port"), "display"=>"select", "compulsory"=>true, "comment"=>"Port number of the card to which the signalling and the voice are associated to"),

		"switchtype" => array($switchtype, "column_name"=>_("Switch type"), "compulsory"=>true, "display"=>"select", "comment"=>_("Specify the trunk type")),

		"type" => array($pri_types, "advanced"=>true, "compulsory"=>true, "display"=>"select", "comment"=>"One side must be NET, the other one CPE. Set according to your peer."),

		"callerid"=>array("advanced"=>true, "comment"=>"Use this to set the caller number when call is routed to this gateway. If none set then the System's CallerID will be used."),
		"callername"=>array("advanced"=>true, "comment"=>"Use this to set the callername when call is routed to this gateway. If none set then the System's Callername will be used."),
		"send_extension"=>array("advanced"=>true, "display"=>"checkbox", "comment"=>"Check this if you want to send the extension as caller number when routing to this gateway."),
		"trusted"=>array("advanced"=>true, "display"=>"checkbox", "comment"=>"Allow calls from this gateway or it's associated gateways to be routed to another gateway."),

		"rxunderrun" => array("column_name"=>_("Max. Interval"), "comment"=>_("Maximum interval in ms between two packets before we report.<br/>zero to disable or 2500+"), "advanced"=>true),

		"strategy" => array($strategy, "column_name"=>_("Strategy"), "display"=>"select", "comment"=>"The strategy used to allocate voice channels for outgoing calls", "advanced"=>true),

		"strategy-restrict" => array($strategy_restrict, "column_name"=>_("Strategy restrict"), "display"=>"select", "comment"=>_("Define channel allocation restrictions and behaviour"), "advanced"=>true),

		"userparttest" => array("column_name"=>_("Test interval"), "comment"=>_("Remote user part test interval in seconds"), "advanced"=>true),

		"channelsync" => array("column_name"=>_("Interval re-sync"), "comment"=>_("The interval (in seconds) at which the call controller will try to re-sync idle channels"), "advanced"=>true),

		"channellock" => array("column_name"=>_("Max. time lock channel"), "comment"=>_("Maximum time (in ms) spent trying to lock a remote channel"), "advanced"=>true),

		"numplan" => array($numplans, "display"=>"select", "column_name"=>_("Numbering plan"), "comment"=>_("Default numbering plan for outgoing calls"), "advanced"=>true),

		"numtype" => array($numtypes, "display"=>"select", "column_name"=>_("Number type"), "comment"=>_("Default number type for outgoing calls"), "advanced"=>true),

		"presentation" => array($presentation, "display"=>"select", "column_name"=>_("Presentation"), "comment"=>_("Default number presentation for outgoing calls"), "advanced"=>true),

		"screening" => array($screening, "display"=>"select", "column_name"=>_("Screening"), "comment"=>_("Default number screening for outgoing calls"), "advanced"=>true),

		"format" => array($format, "column_name"=>_("Format"), "display"=>"radio", "comment"=>_("If none of the formats is checked then server will use alaw")),

		"print-messages" => array("display"=>"checkbox", "value"=>($sig_trunk->{"print-messages"} == "yes") ? "t" : "f", "column_name"=>_("Print messages"), "comment"=>_("Print decoded protocol data units to output"), "advanced"=>true),

		"print-frames" => array("display"=>"checkbox", "value"=>($sig_trunk->{"print-frames"} == "yes") ? "t" : "f", "column_name"=>_("Print frames"), "comment"=>_("Print decoded Layer 2 (Q.921) frames to output"), "advanced"=>true),

		"layer2dump" =>  array("column_name"=>_("Layer2 dump"), "comment"=>_("Filename to dump Q.921 packets to"), "advanced"=>true),

		"layer3dump" =>  array("column_name"=>_("Layer3 dump"), "comment"=>_("Filename to dump Q.931 packets to"), "advanced"=>true),
	);
	unset($BRI["type"]);
	if($sig_trunk->format)
		$PRI["format"][0]["selected"] = $sig_trunk->format;
	$PRI["port"][0] = $pri_ports;
	$PRI["port"]["javascript"] = "onChange='set_format()'";
	unset($BRI["format"]);

	start_form(NULL,"post",false,"outbound");
	addHidden("database",array("gateway_id"=>$gateway_id));
	if(!$gateway_id) 
	{
		$sip_fields["password"]["compulsory"] = true;
		$h323_fields["password"]["compulsory"] = true;
		$iax_fields["password"]["compulsory"] = true;
		unset($sip_fields["password"]["comment"]);
		unset($h323_fields["password"]["comment"]);
		unset($iax_fields["password"]["comment"]);
		$protocols = array("sip", "h323", "iax");
		$allprotocols = array("sip", "h323", "iax", "pstn");//, "PRI", "BRI");
		$allprotocols["selected"] = $protocol;
		$protocols["selected"] = $protocol;

		if($_SESSION["pri_support"] == "yes")
			$allprotocols[] = "PRI";
		if($_SESSION["bri_support"] == "yes")
			$allprotocols[] = "BRI";

		if($protocol && $gw_type == "Yes")
		{
			$fields = $protocol."_fields";
			foreach(${$fields} as $fieldname=>$fieldformat)
			{
				if($gateway->variable($fieldname))
					$gateway->{$fieldname} = getparam("reg_".$protocol . $fieldname);
			}
			if($gateway->enabled == "on")
				$gateway->enabled = "t";
			$gateway->formats = get_formats("reg_".$protocol."formats");
		}elseif($protocol && $gw_type == "No"){
			$fields = $protocol;
			foreach(${$fields} as $fieldname=>$fieldformat)
			{
				if($gateway->variable($fieldname))
					$gateway->{$fieldname} = getparam("noreg_".$protocol . $fieldname);
			}
			if($gateway->enabled == "on")
				$gateway->enabled = "t";
			$gateway->formats = get_formats("noreg_".$protocol."formats");
		}

		$gw_types = array("Yes","No");
		$gw_types["selected"] = $gw_type;
		$step1 = array("gateway_with_registration"=>array($gw_types, "display"=>"radios", "javascript"=>'onClick="gateway_type();"', "comment"=>"A gateway with registration is a gateway for which you need an username and a password that will be used to autentify. If you wish to add a pstn/BRI/PRI gateway check 'No'."));

		editObject($gateway,$step1,"Select type of gateway to add","no");

		//select protocol for gateway with registration
		?><div id="div_Yes" style="display:<?php if ($gw_type != "Yes") print "none;"; else print "block;";?>"><?php
		editObject(
					$gateway,
					array(
							"protocol"=>array(
												$protocols,
												"display"=>"select",
												"javascript"=>'onChange="form_for_gateway(\'reg\');"'
											)
						),
					"Select protocol for new gateway",
					"no",null,null,null,"reg"
					);
		?></div><?php

		//select protocol for gateway without registration
		?><div id="div_No" style="display:<?php if ($gw_type != "No") print "none;"; else print "block;";?>"><?php
		editObject(
					$gateway,
					array(
							"protocol"=>array($allprotocols,
							"display"=>"select",
							"javascript"=>'onChange="form_for_gateway(\'noreg\');"')
					),
					"Select protocol for new gateway",
					"no",null,null,null,"noreg"
				);
		?></div><?php

		// display all the divs with fields for gateway with registration depending on the protocol
		for($i=0; $i<count($protocols); $i++)
		{
			if(!isset($protocols[$i]))
				continue;
			if(!isset(${$protocols[$i]."_fields"}))
				continue;

			?><div id="div_reg_<?php print $protocols[$i]?>" style="display:<?php if ($protocol == $protocols[$i] && $gw_type == "Yes") print "block;"; else print "none;";?>"><?php
			editObject(
						$gateway,
						${$protocols[$i]."_fields"}, 
						"Define ".strtoupper($protocols[$i])." gateway", 
						"Save",true,null,null,"reg_".$protocols[$i]
					);
			?></div><?php
		}
		// display all the div with fields for gateway without registration on the protocol
		for($i=0; $i<count($allprotocols); $i++)
		{
			if(!isset($allprotocols[$i]))
				continue;
			if(!isset(${$allprotocols[$i]}))
				continue;
			switch($allprotocols[$i]) {
						case 'sip':
							$gateway->port = '5060';
							break;
						case 'iax':
							$gateway->port = '4569';
							break;
						case 'h323':
							$gateway->port = '1720';
							break;
			}
			?><div id="div_noreg_<?php print $allprotocols[$i]?>" style="display:<?php if ($protocol == $allprotocols[$i] && $gw_type == "No") print "block;"; else print "none;";?>"><?php
			$hide_advanced = ($allprotocols[$i] == "BRI" || $allprotocols[$i] == "PRI") ? true : false;
			editObject(
						$gateway,
						${$allprotocols[$i]}, 
						"Define ".strtoupper($allprotocols[$i])." gateway",
						"Save",true,null,null,"noreg_".$allprotocols[$i],null, $hide_advanced
					);
			?></div><?php
		}
	}else{
		$function = ($gateway->username) ? $gateway->protocol . "_fields" : $gateway->protocol;
		$gw_type = ($gateway->username) ? "reg" : "noreg";

		if($gateway->protocol == "PRI" || $gateway->protocol == "BRI")
			${$function}["port"][0]["selected"] = $sig_trunk->port;

		unset(${$function}["default_dial_plan"]);
		$hide_advanced = ($gateway->protocol == "BRI" || $gateway->protocol == "PRI") ? true : false;
		editObject($gateway,${$function}, "Edit ".strtoupper($gateway->protocol). " gateway", "Save", true,null,null,$gw_type."_".$gateway->protocol,null, $hide_advanced);
	}
	end_form();
}

function edit_gateway_database()
{
	global $module, $method, $path;
	$path .= "&method=gateways";

	if($_SESSION["level"] != "admin") {
		forbidden();
		return;
	}

	Database::transaction();

	$gateway_id = getparam("gateway_id");
	$gateway = new Gateway;
	$gateway->gateway_id = $gateway_id;
	if(!$gateway->gateway_id) {
		$gw_type = getparam("gateway_with_registration");
		$gw_type = ($gw_type == "Yes") ? "reg" : "noreg";
		$protocol = getparam($gw_type."protocol");
	}else{
		$gw = new Gateway;
		$gw->gateway_id = $gateway_id;
		$gw->select();
		$protocol = $gw->protocol;
		$gw_type = ($gw->username && $gw->username != '') ? "reg" : "noreg";
	}
	if(!$protocol)
	{
		Database::rollback();
		notice("Can't make this operation. Don't have a protocol setted.", "gateways", false);
		return;
	}

	$params["type"] = $gw_type;
	if($gw_type == "reg")
	{
		$compulsory = array("gateway", "username", "server");
		for($i=0; $i<count($compulsory); $i++)
			$params[$compulsory[$i]] = getparam($gw_type."_".$protocol.$compulsory[$i]);

		$sip = array('authname','outbound', 'domain', 'localaddress', 'description', 'interval', 'rtp_localip', 'ip_transport');
		$h323 = $iax = array('description', 'interval');
	
		for($i=0; $i<count(${$protocol}); $i++)
			$params[${$protocol}[$i]] = getparam($gw_type."_".$protocol.${$protocol}[$i]);

		if(getparam($gw_type."_".$protocol."password"))
			$params["password"] = getparam($gw_type."_".$protocol."password");
	}else{
		$params["gateway"] = getparam($gw_type."_".$protocol."gateway");
		switch($protocol)
		{
			case "iax":
				$params["iaxuser"] = getparam($gw_type."_".$protocol."iaxuser");
				$params["iaxcontext"] = getparam($gw_type."_".$protocol."iaxcontext");
				break;
			case "sip":
				$params["rtp_localip"] = getparam($gw_type."_".$protocol."rtp_localip");
				$params["oip_transport"] = getparam($gw_type."_".$protocol."oip_transport");
			case "h323":
				$params["server"] = getparam($gw_type."_".$protocol."server");
				$params["port"] = getparam($gw_type."_".$protocol."port");
				break;
			case "pstn":
				break;
			case "BRI":
			case "PRI":
				if($protocol == "BRI" && $_SESSION["bri_support"] != "yes") {
					errormess("BRI protocol is not supported.");
					return;
				}
				if($protocol == "PRI" && $_SESSION["pri_support"] != "yes") {
					errormess("PRI protocol is not supported.");
					return;
				}
				$socket = new SocketConn;
				if($socket->error != "") {
					errormess("I can't connect to yate.<br/> Note!! Check if you have all needed libraries: php_sockets, php_ssl and if yate is running.");
					return;
				}
				$socket->close();
				$sig_trunk = new Sig_trunk;
				$sig_trunk->sig_trunk_id = getparam($gw_type."_".$protocol."sig_trunk_id");
				$sig_trunk->select();

				$old_interface = $sig_trunk->sig;

				$trunk_params = array();
			//	$params["enable"] = (getparam("enable") == "on") ? "yes" : "no";
				$names = array("enable"=>"bool", "switchtype", "number", "rxunderrun", "strategy", "strategy-restrict", "userparttest", "channelsync", "channellock", "numplan", "numtype", "presentation", "screening", "print-messages"=>"bool", "print-frames"=>"bool", "layer2dump", "layer3dump");
				foreach($names as $key => $val)
				{
					$name = (is_numeric($key)) ? $val : $key;
					$trunk_params[$name] = getparam($gw_type."_".$protocol.$name);
					if(!is_numeric($key) && $val == "bool")
						$trunk_params[$name] = ($trunk_params[$name] == "on") ? "yes" : "no";
				}
				$port = getparam($gw_type."_".$protocol."port");
				$trunk_params["port"] = $port;
				$card_port = Model::selection("card_port", array("name"=>$port));

				if(!$port || !count($card_port)) {
					Database::rollback();
					edit_gateway("You need to select a port in order to add a gateway for ".$protocol, $protocol, $gw_type);
					return;
				}else
					$card_port = $card_port[0];
				switch($card_port->type) {
					case "TE":
						$trunk_params["type"] = "isdn-bri-cpe";
						break;
					case "NT":
						$trunk_params["type"] = "isdn-bri-net";	// this should not be possible (set here only when working as TE)
					case "E1":
					case "T1":
						$val = getparam($gw_type."_".$protocol."type");
						$trunk_params["type"] = ($val) ? $val : "isdn-pri-cpe";	// "isdn-pri-net" and "isdn-pri-cpe" are identical.
				}

				$interface = str_replace(".conf","",$card_port->filename);
				$trunk_params["sig"] = $interface;
				$trunk_params["voice"] = $interface;
				$trunk_params["format"] = getparam($gw_type."_".$protocol."format");

				if(!$sig_trunk->sig_trunk_id) {
					$name=getparam($gw_type."_".$protocol."gateway");
					while(true) {
						$s_trnk = new Sig_Trunk;
						$s_trnk->sig_trunk = $name;
						if($s_trnk->objectExists())
							$name = $name."_";
						else
							break;
					}
					$trunk_params["sig_trunk"] = $name;
				}
				$res = ($sig_trunk->sig_trunk_id) ? $sig_trunk->edit($trunk_params) : $sig_trunk->add($trunk_params);

				if(!$res[0]) {
					Database::rollback();
					edit_gateway("Can't define trunk for ".$protocol.": ".$res[1], $protocol, $gw_type);
					return;
				}

				if($protocol == "BRI") {
					$fields = array(
						"span"=>array(
							"params"=>array("module_name"=>"tdmcard", "param_name"=>"span", "section_name"=>$trunk_params["sig"], "param_value"=>$card_port->span), 
							"conditions" => array("section_name"=>$old_interface, "module_name"=>"tdmcard", "param_name"=>"span")
							), 
						"type"=>array(
							"params" => array("module_name"=>"tdmcard", "param_name"=>"type", "section_name"=>$trunk_params["sig"], "param_value"=>"CPE"),
							"conditions" => array("section_name"=>$old_interface, "module_name"=>"tdmcard", "param_name"=>"type")
							)
					);
				}else{	// when protocol is PRI
					$flds = array("type"=>$card_port->type, "siggroup"=>$card_port->sig_interface, "voicegroup"=>$card_port->voice_interface, "voicechans"=>$card_port->voice_chans, "echocancel"=>$card_port->echocancel, "dtmfdetect"=>$card_port->dtmfdetect);

					$fields = array();
					foreach($flds as $name=>$val) {
						$fields[$name] = array(
							"params"=>array("module_name"=>"wpcard", "param_name"=>$name, "section_name"=>$interface, "param_value"=>$val),
							"conditions"=>array("module_name"=>"wpcard", "param_name"=>$name, "section_name"=>$old_interface)
						);
					}
				}
				$err = false;
				foreach($fields as $name=>$field_params) {
					$new = ($old_interface) ? false : true;
					$res = set_card_confs($field_params["conditions"], $field_params["params"], $new);
					if(!$res[0]) {
						$err = true;
						break;
					}
				}
				if(!$err) {
					$command = getparam($gw_type."_".$protocol."sig_trunk_id") ? "configure" : "create";
					$sig_trunk->sendCommand($command);
				}
				break;
		}
	}
	$params["protocol"] = $protocol;
	
	if($protocol != "PRI" && $protocol != "BRI") {
		$params["formats"] = get_formats($gw_type."_".$protocol."formats");
		$params["enabled"] = (getparam($gw_type."_".$protocol."enabled") == "on") ? "t" : "f";
		$params["rtp_forward"] = (getparam($gw_type."_".$protocol."rtp_forward") == "on") ? "t" : "f";
		$params["modified"] = "t";
	}else
		$params["sig_trunk_id"] = $sig_trunk->sig_trunk_id;

	$params["callerid"] = getparam($gw_type."_".$protocol."callerid");
	$params["callername"] = getparam($gw_type."_".$protocol."callername");
	$params["send_extension"] = (getparam($gw_type."_".$protocol."send_extension") == "on") ? "t" : "f";
	$params["trusted"] = (getparam($gw_type."_".$protocol."trusted") == "on") ? "t" : "f";
	$next = "outbound";

	$res = ($gateway->gateway_id) ? $gateway->edit($params) : $gateway->add($params);
	if(!$res[0]) {
		Database::rollback();
		if(isset($res[2]))
			edit_gateway($res[1], $protocol, $gw_type);
		else
			notice($res[1], $next, $res[0]);
		return;
	}
	Database::commit();
	if(!$gateway_id && $gateway->gateway_id) {
		if (getparam($gw_type."_".$protocol."default_dial_plan") == "on") {
			$dial_plan = new Dial_Plan;
			$prio = $dial_plan->fieldSelect("max(priority)");
			if($prio)
				$prio += 10;
			else
				$prio = 10;
			$params["gateway_id"] = $gateway->gateway_id;
			$params["priority"] = $prio;
			$params["dial_plan"] = "default for ".$gateway->gateway;
			$res = $dial_plan->add($params);
			if(!$res[0]) 
				errormess("Could not add default dial plan: ".$res[1], "no");
		}
	}
	notice($res[1], $next, $res[0]);
}

function set_card_confs($conditions, $params, $new)
{
	if(!$new) {
		$card_confs = Model::selection("card_conf", $conditions);
		if(count($card_confs))
			$card_conf = $card_confs[0];
		else
			$card_conf = new Card_conf;
	}else
		$card_conf = new Card_conf;
	$res = ($card_conf->param_name) ? $card_conf->edit($params,$conditions) : $card_conf->add($params);
	return $res;
}

function edit_dial_plan($error = NULL)
{
	if($_SESSION["level"] != "admin") {
		forbidden();
		return;
	}

	if($error)
		errornote($error);

	$dial_plan = new Dial_Plan;
	$dial_plan->dial_plan_id = getparam("dial_plan_id");
	$dial_plan->select();

	$gateways = Model::selection("gateway", NULL, "gateway");
	$gateways = Model::objectsToArray($gateways, array("gateway_id"=>"", "gateway"=>""), "all");
	$gateways["selected"] = $dial_plan->gateway_id;

	$check_to_match_everything = (($dial_plan->prefix == "" && $dial_plan->dial_plan_id) || (getparam("check_to_match_everything") == "on")) ? 't' : 'f';

	$fields = array(
					"dial_plan" => array("compulsory" => true),
					"gateway" => array("compulsory" => true, $gateways, "display"=>"select"),
					"priority" => array("comment" => "Numeric. Priority 1 is higher than 10","compulsory"=>true), 
					"prefix" => array("compulsory" => true), 
					"check_to_match_everything" => array("value" => $check_to_match_everything, "display" => "checkbox", "comment" => "If you wish this route to match all prefixes"),
				);

	if($error)
	{
		foreach($fields as $field_name=>$field_format)
		{
			$fields[$field_name]["value"] = getparam($field_name);
		}
		$fields["gateway"][0]["selected"] = getparam("gateway");
	}

	$title = ($dial_plan->dial_plan_id) ? "Edit Dial Plan" : "Add Dial Plan";

	start_form();
	addHidden("database",array("dial_plan_id"=>$dial_plan->dial_plan_id));
	editObject($dial_plan,$fields,$title,"Save",true);
	end_form();
}

function modify_number()
{
	if($_SESSION["level"] != "admin") {
		forbidden();
		return;
	}

	$dial_plan = new Dial_Plan;
	$dial_plan->dial_plan_id = getparam("dial_plan_id");
	$dial_plan->select();

	$fields2 = array(
					"examples" => array("value"=>"Click on the question mark to show/hide the examples.","display"=>"fixed","comment"=>"
Number: 0744224022<br/>
You wish to send the number in international format: +40744334011<br/>
You should set :
Position to start adding : 1<br/>
Digits to add: +4<br/><br/>

Number: 5550744224011<br/>
You wish to send the number in international format: +40744334011<br/>

You can achieve this in 2 ways:<br/>
1)<br/>
Position to start replacing: 1<br/>
Nr of digits to replace: 3<br/>
Digits to replace with: +4<br/>
2)<br/>
Position to start cut: 1<br/>
Nr of digits to cut: 3<br/>
Position to start add: 1<br/>
Digits to add: +4<br/>
<br/>

Number: 0744224022555<br/>
You wish to send the number without the last 555 like this: 0744224022<br/>

Position to start cutting: -3<br/>
Nr of digits to cut: 3
"),
					"position_to_start_cutting" => array("comment" => "The first position in the number is 1. If inserted number is negative, position will be taken from the end of the number. Unless you insert the 'Nr of digits to cut' this field will be ignored. Order for performing operations on the phone number : cut, replace, add."),
					"nr_of_digits_to_cut" => array("comment" => "Number of digits you wish to remove from the number starting from the position inserted above. Unless you insert the 'position to start cutting' this field will be ignored."),
					"position_to_start_replacing" => array("comment" => "The first position in the number is 1. If inserted number is negative, position will be taken from the end of the number.Unless you insert the 'No of digits to replace' and 'Digits to replace with' this field will be ignored"),
					"nr_of_digits_to_replace" => array("comment" => "Unless you insert the Position to start replacing and the Digits to replace with, this field will be ignored"),
					"digits_to_replace_with" => array("comment" => "Digits that will replace the Number of digits to replace starting at 'Position to start replacing'"),
					"position_to_start_adding" => array("comment" => "If inserted number is negative, position will be taken from the end of the number.Unless 'Digits' to add is inserted this field will be ignored"),
					"digits_to_add" => array("comment"=>"Digits that will be added in the 'Position to start adding'"),
				);

	start_form();
	addHidden("database",array("dial_plan_id"=>$dial_plan->dial_plan_id));
	editObject($dial_plan,$fields2,"Options for modifying phone number <br/>when call is sent through this gateway","Save",true);
	end_form();
}

function modify_number_database()
{
	if($_SESSION["level"] != "admin") {
		forbidden();
		return;
	}

	global $path;
	$path .= "&method=dial_plan";

	$dial_plan = new Dial_Plan;
	$dial_plan->dial_plan_id = getparam("dial_plan_id");
	$dial_plan->select();
	$fields = array("position_to_start_cutting"=>"int", "nr_of_digits_to_cut"=>"int", "position_to_start_replacing"=>"int", "nr_of_digits_to_replace"=>"int", "digits_to_replace_with"=>"", "position_to_start_adding"=>"", "digits_to_add"=>"");

	foreach($fields as $field_name=>$field_type)
	{
		$value = getparam($field_name);
		if($field_type == "int" && $value) {
			if(Numerify($value) == "NULL") {
				edit_dial_plan("Field '".ucfirst(str_replace("_"," ",$field_name))."' must be numeric when inserted.");
				return;
			}
		}
		$dial_plan->{$field_name} = $value;
	}
	//notify($dial_plan->update());
	$res = $dial_plan->update();
	notice($res[1], "dial_plan", $res[0]);
}

function edit_dial_plan_database()
{
	if($_SESSION["level"] != "admin") {
		forbidden();
		return;
	}

	global $path;
	$path .= "&method=dial_plan";

	$dial_plan = new Dial_Plan;
	$dial_plan->dial_plan_id = getparam("dial_plan_id");

	$fields = array("dial_plan", "priority");
	$params = form_params($fields);
	$params["gateway_id"] = getparam("gateway");
	if(!$params["gateway_id"]){
		edit_dial_plan("You must select a gateway");
		return;
	}
	if(getparam("check_to_match_everything") != "on" && !strlen(getparam("prefix")))
	{
		edit_dial_plan("Please insert the prefix you wish to match or check to match everything");
		return;
	}
	$params["prefix"] = (getparam("check_to_match_everything") == "on") ? NULL : getparam("prefix");

	$res = ($dial_plan->dial_plan_id) ? $dial_plan->edit($params) : $dial_plan->add($params);
	if(isset($res[2]) && !$res[0]) {
		edit_dial_plan($res[1]);
		return;
	}

	notice($res[1],"dial_plan",$res[0]);
}


function gateway_status($enabled,$status,$username)
{
	if(!$username)
	{
		print "&nbsp;";
		return;
	}
	if($enabled != "t")
		print '<img src="images/gray_dot.gif" title="Not enabled" alt="Not enabled"/>';
	elseif($status == "online")
		print '<img src="images/green_dot.gif" title="Online" alt="Online"/>';
	else
		print '<img src="images/red_dot.gif" title="Offline" alt="Offlibe"/>';
}

function gateway_type($username)
{
	if ($username)
		return "Yes";
	return "No";
}

function registration_status($status,$username)
{
	if(!$username)
		return "&nbsp;";
	elseif(!$status)
		return "offline";
	else
		return $status;
}
?>