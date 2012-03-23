<?php
/**
 * settings.php
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
global $dir, $module, $method, $path, $action, $page, $target_path, $telephony_cards;

require_once("lib/telephony_cards.php");
require_once("lib/lib_wizard.php");


if(!$method)
	$method = $module;

if(substr($method,0,4) == "add_")
	$method = str_replace("add_","edit_",$method);

if($action)
	$call = $method.'_'.$action;
else
	$call = $method;

$explanation = array("default"=>"General settings for the system, set admins and define system's address book.", "admins"=>"The admin is the role in charge of the maintenance of the system. It has unlimited access to configurations, setup and any kind of changes inside FreeSentral. The administrator role is available via 'admin' account.", "address_book"=>"Address book entries - numbers associated with a person's name. This allows you to call a certain extension by typing the person's name(the digits corresponding that person's name).");
$explanation["edit_admin"] = $explanation["admins"];
$explanation["edit_short_name"] = $explanation["address_book"];
$explanation["limits_international_calls"] = "Freesentral protects your system against attackers trying to make expensive calls. The system counts the number of calls for the specified prefixes and if counters reach a the specified limits, calls for those prefixes are disabled. By default all international calls are counted. <a class=\"llink\" href=\"main.php?module=outbound&method=international_calls\">Set prefixes</a>";
$explanation["edit_limit"] = $explanation["limits_international_calls"];

$image = "images/address_book.png";

if($method != "wizard_cards") {
	explanations($image, "", $explanation);
	print '<div class="content">';
	$call();
	print '</div>';
}else{
	print '<div class="content wide">';
	$call();
	print '</div>';
}
	
/*
function equipments()
{
	$equipments = Model::selection("equipment", NULL, "equipment");
	$formats = array("equipment","description");

	tableOfObjects($equipments, $formats, "equipment", array("&method=edit_equipment"=>'<img src="images/edit.gif" alt="edit" title="Edit"/>', "&method=delete_equipment"=>'<img src="images/delete.gif" alt="delete" title="Delete"/>'), array("&method=add_equipment"=>"Add equipment"));
}

function edit_equipment($error = NULL)
{
	if($error)
		errornote($error);

	$equipment = new Equipment;
	$equipment->equipment_id = getparam("equipment_id");
	$equipment->select();

	$fields = array(
					"equipment"=>array("compulsory"=>true),
					"description"=>array("display"=>"textarea")
				);

	if($equipment->equipment_id)
		$title = "Edit equipment";
	else
		$title = "Add equipment";

	start_form();
	addHidden("database", array("equipment_id"=>$equipment->equipment_id));
	editObject($equipment,$fields,$title,"Save",true);
	end_form();
}

function edit_equipment_database()
{
	global $path;
	$path .= "&method=equipments";

	$equipment = new Equipment;
	$equipment->equipment_id = getparam("equipment_id");
	$equipment->equipment = getparam("equipment");

	if(!$equipment->equipment)
	{
		edit_equipment("Field 'Equipment' is required");
		return;
	}
	if($equipment->objectExists())
	{
		edit_equipment("This equipment is already in the database");
		return;
	}
	$equipment->description = getparam("description");
	if($equipment->equipment_id)
		notify($equipment->update(),$path);
	else
		notify($equipment->insert(),$path);
}

function delete_equipment()
{
	ack_delete("equipment", getparam("equipment"), NULL, "equipment_id", getparam("equipment_id"));
}

function delete_equipment_database()
{
	global $path;
	$path .= "&method=equipments";

	$equipment = new Equipment;
	$equipment->equipment_id = getparam("equipment_id");
	if(!$equipment->equipment_id) 
	{
		errormess("Don't have equipment_id specified, can't delete equipment.");
		return;
	}
	notify($equipment->objDelete(),$path);
}
*/
function general()
{
	settings();
}

function settings()
{
	global $method;
	$method = "settings";

	$settings = Model::selection("setting",array("param"=>array("!=version", "!=wizard", "__NOT LIKEinternational")),"param");

	$formats = array("setting"=>"param", "value", "description");
	tableOfObjects($settings,$formats,"setting", array("&method=edit_setting"=>'<img src="images/edit.gif" title="Edit" alt="Edit"/>'));
}

function edit_setting($error = NULL)
{
	if($error)
		errornote($error);

	$setting = new Setting;
	$setting->setting_id = getparam("setting_id");
	$setting->select();

	$fields = array(
					"setting"=>array("value"=>$setting->param, "display"=>"fixed"),
					"value"=>array(),
					"description"=>array("display"=>"textarea")
				);

	start_form();
	addHidden("database", array("setting_id"=>$setting->setting_id));
	editObject($setting, $fields, "Edit setting", "Save");
	end_form();
}

function edit_setting_database()
{
	$setting = new Setting;
	$setting->setting_id = getparam("setting_id");
	$setting->select();

	$setting->value = getparam("value");
	if(!$setting->value)
	{
		edit_setting("Field 'Value' can not be empty");
		return;
	}
	$setting->description = getparam("description");
	$res = $setting->update();
	notice($res[1],NULL,$res[0]);
}

function network()
{
	global $block, $dir, $module;

	if(isset($block[$dir."_".$module]) && in_array("network",$block[$dir."_".$module]))
		return;
/*
	if($_SERVER["HTTP_HOST"] != "localhost" && $_SERVER["HTTP_HOST"] != "127.0.0.1")
		return;
*/

	$fields = array("DEVICE"=>"network_interface", "BOOTPROTO"=>"protocol", "IPADDR"=>"ip_address", "NETMASK"=>"netmask", "GATEWAY"=>"gateway", "DNS1"=>"DNS1", "DNS2"=>"DNS2");

	$ninterfaces = array();
	$dir = "/etc/sysconfig/network-scripts";
	if ($handle = opendir("$dir"))
	{
		while (false !== ($file = readdir($handle)))
		{
			if ($file == "ifcfg-lo")
				continue;
			if (substr($file,0,6) != "ifcfg-")
				continue;
			$ninterfaces[] = str_replace('ifcfg-','',$file);
		}
		closedir($handle);
	}

	$interfaces = array();
	for($i=0; $i<count($ninterfaces); $i++)
	{
		$filename = 'ifcfg-'.$ninterfaces[$i];
		$f = new ConfFile("$dir/$filename");
		$interfaces[$i] = array();
		foreach($fields as $name_in_conf=>$name_to_display)
		{
			if($name_to_display == "")
				$name_to_display = $name_in_conf;

			$interfaces[$i][$name_to_display] = (isset($f->sections[$name_in_conf])) ? $f->sections[$name_in_conf] : '';
		}
	}

	table($interfaces,$fields,"network interface","",array("&method=edit_network_interface"=>'<img src="images/edit.gif" title="Edit" alt="Edit"/>'));
}

function edit_network_interface($error = NULL)
{
	global $dir, $module, $block;

	if(isset($block[$dir."_".$module]) && in_array("network",$block[$dir."_".$module]))
		return;
/*
	if($_SERVER["HTTP_HOST"] != "localhost" && $_SERVER["HTTP_HOST"] != "127.0.0.1")
		return;
*/
	if($error)
		errornote($error);

	$dir = "/etc/sysconfig/network-scripts";
	$network_interface = getparam("network_interface");
	$file = "/etc/sysconfig/network-scripts/ifcfg-$network_interface";

	if(is_file($file)) {
		$conf = new ConfFile($file);
		$display = "fixed";
	}else{
		network();
		return;
	//	$conf = array();
	//	$display = "text";
	}
	$fields = array("DEVICE"=>"network_interface", "BOOTPROTO"=>"protocol", "IPADDR"=>"ip_address", "NETMASK"=>"netmask", "GATEWAY"=>"gateway", "DNS1"=>"DNS1", "DNS2"=>"DNS2");

	foreach($fields as $name_in_conf=>$name_to_display)
	{
		if($name_to_display == "")
			$name_to_display = $name_in_conf;

		$interface[$name_to_display] = (isset($conf->sections[$name_in_conf])) ? $conf->sections[$name_in_conf] : '';
	}

	$protocols = array("static", "dhcp");
	$protocols["selected"] = $interface["protocol"];
	$interface = array(
						"network_interface" => array("value"=>$interface["network_interface"], "display"=>"$display"),
						"protocol" => array($protocols, "display"=>"select", "javascript"=>'onChange="dependant_fields();"', "compulsory"=>true),
						"ip_address" => array("value"=>$interface["ip_address"],"display"=>($protocols["selected"] == "dhcp") ? "dependant_field_noedit" : "dependant_field_edit"),
						"netmask" => array("value"=>$interface["netmask"],"display"=>($protocols["selected"] == "dhcp") ? "dependant_field_noedit" : "dependant_field_edit"),
						"gateway" => array("value"=>$interface["gateway"],"display"=>($protocols["selected"] == "dhcp") ? "dependant_field_noedit" : "dependant_field_edit"),
						"DNS1" => array("value"=>$interface["DNS1"], "comment"=>"If protocol is DHCP you can leave this field empty."),
						"DNS2" => array("value"=>$interface["DNS2"])
					);

	start_form();
	addHidden("database",array("network_interface"=>$network_interface));
	editObject(NULL,$interface,"Set network interface", "Save");
	end_form();
}

function edit_network_interface_database()
{
	global $dir, $module, $block, $path;

	if(isset($block[$dir."_".$module]) && in_array("network",$block[$dir."_".$module]))
		return;
/*
	if($_SERVER["HTTP_HOST"] != "localhost" && $_SERVER["HTTP_HOST"] != "127.0.0.1")
		return;
*/

	$path .= "&method=network";

	$dir = "/etc/sysconfig/network-scripts";
	$network_interface = getparam("network_interface");
	$file = "/etc/sysconfig/network-scripts/ifcfg-$network_interface";

	$conf = new ConfFile($file);
	$network_interface = getparam("network_interface");
	if(!isset($conf->structure["DEVICE"]))
		$conf->structure["DEVICE"] = $network_interface;
	
	$protocol = getparam("protocol");

	if(!$protocol || $protocol == "Not selected"){
		edit_network_interface("Please select a protocol when defining the interface.");
		return;
	}

	$ip_address = getparam("ip_address");
	$netmask = getparam("netmask");
	$gateway = getparam("gateway");

	if($protocol)
		$conf->structure["BOOTPROTO"] = $protocol;
	if($protocol == "static") {
		if (!$ip_address) {
			edit_network_interface("Field Ip Address is required when Protocol is static.");
			return;
		}
		if (!$netmask) {
			edit_network_interface("Field Netmask is required when Protocol is static.");
			return;
		}
		if (!$gateway) {
			edit_network_interface("Field Gateway is required when Protocol is static.");
			return;
		}
		$conf->structure["IPADDR"] = $ip_address;
		$conf->structure["NETMASK"] = $netmask;
		$conf->structure["GATEWAY"] = $gateway;
	}elseif($protocol == "dinamic"){
		$conf->structure["IPADDR"] = '';
		$conf->structure["NETMASK"] = '';
		$conf->structure["GATEWAY"] = '';
	}
	$conf->structure["DNS1"] = (getparam("DNS1")) ? getparam("DNS1") : '';
	$conf->structure["DNS2"] = (getparam("DNS2")) ? getparam("DNS2") : '';

	$conf->initial_comment = 
"#================================================
# Interface $network_interface Configuration File
#================================================
#
# Note: This file was generated automatically
#       by Freesentral web interface
#
#       If you want to edit this file, it is
#       recommended that you use the web interface
#       to do so.
#================================================";
	$out = shell_command("network_stop");
	$err = "Don't know command to stop network";
	if(substr($out,0,strlen($err)) != $err) {
		print str_replace("\n","<br/>",$out);
		$conf->save(true);
	}else{
		notice("Could not configure network interface: ".$err, "network", false);
		return;
	}
	exec("chmod +x ".$conf->filename);
	$out = shell_command("network_start");
	print str_replace("\n","<br/>",$out);

//	message("Network interface was configured.",$path);
	notice("Network interface was configured.", "network");
}

function dependant_field_edit($value, $name)
{
	print '<div id="div_'.$name.'" style="display:table-cell;"><input name="'.$name.'" type="'.$name.'" value="'.$value.'"/>&nbsp;</div>';
	print '<div id="text_'.$name.'" style="display:none;">&nbsp;'.$value."</div>";
}

function dependant_field_noedit($value, $name)
{
	print '<div id="div_'.$name.'" style="display:none;"><input name="'.$name.'"  type="'.$name.'" value="'.$value.'"/>&nbsp;</div>';
	print '<div id="text_'.$name.'" style="display:table-cell;">&nbsp;'.$value."</div>";
}

function address_book()
{
	global $method, $action;
	$method = "address_book";
	$action = NULL;
	$short_names = Model::selection("short_name", array("extension_id"=>"__empty"), "short_name");	
	tableOfObjects($short_names, array("short_name", "number", "name"), "short_name", array("&method=edit_short_name"=>'<img src="images/edit.gif" title="Edit" alt="Edit"/>', "&method=delete_short_name"=>'<img src="images/delete.gif" title="Delete" alt="Delete"/>'), array("&method=add_short_name"=>"Add shortcut"));
}

function edit_short_name($error=NULL)
{
	if($error)
		errornote($error);

	$short_name = new Short_Name;
	$short_name->short_name_id = getparam("short_name_id");
	$short_name->select();

	$fields = array(
					"short_name" => array("comment"=>"Name to be dialed", "compulsory"=>true),
					"number" => array("comment"=>"Number where to place the call", "compulsory"=>true),
					"name" => ''
				);

	$title = ($short_name->short_name_id) ? "Edit shortcut" : "Add shortcut ";

	start_form();
	addHidden("database",array("short_name_id"=>$short_name->short_name_id));
	editObject($short_name, $fields, $title, "Save");
	end_form();
}

function edit_short_name_database()
{
	global $module;

	$short_name = new Short_Name;
	$short_name->short_name_id = getparam("short_name_id");
	$params = form_params(array("short_name", "number", "name"));;
	$res = ($short_name->short_name_id) ? $short_name->edit($params) : $short_name->add($params);
	notice($res[1], "address_book", $res[0]);
}

function delete_short_name()
{
	ack_delete("short_name", getparam("short_name"), NULL, "short_name_id", getparam("short_name_id"));
}

function delete_short_name_database()
{
	global $module;

	$short_name = new Short_Name;
	$short_name->short_name_id = getparam("short_name_id");
	$res = $short_name->objDelete();
	notice($res[1], "address_book", $res[0]);
}

function admins()
{
	global $method;
	$method = "admins";

	// select all the users in the system order by username
	$users = Model::selection("user", NULL, 'username');

	tableOfObjects($users,array("username","firstname","lastname","email"),"admin",array("&method=edit_user"=>'<img src="images/edit.gif" title="Edit" alt="edit"/>', "&method=delete_user"=>'<img src="images/delete.gif" title="Delete" alt="delete"/>'),array("&method=add_user"=>"Add admin"));
}

//generate form to edit or add a user
function edit_user($error = NULL)
{
	if($error)
		errornote($error);

	$user = new User;
	$user->user_id = getparam("user_id");
	$user->select();

	$fields = array(
						"username"=>array("display"=>"fixed", "compulsory"=>true, "autocomplete"=>"off"), 
						"password"=>array("display"=>"password", "comment"=>"Minimum 5 digits. Insert only if you wish to change.", "autocomplete"=>"off"),
						"email"=>array("compulsory"=>true),
						"firstname"=>"",
						"lastname"=>"",
						"description"=>array("display"=>"textarea")
				);
	if(!$user->user_id)
	{
		$fields["username"]["display"] = "text";
		$fields["password"]["compulsory"] = true;
		$fields["password"]["comment"] = "Minimum 5 digits.";
		$title = "Add admin";

		$var_names = array("username", "email", "firstname", "lastname", "description");
		for($i=0; $i<count($var_names); $i++)
			$user->{$var_names[$i]} = getparam($var_names[$i]);
	}else
		$title = "Edit admin ".$user->username;

	start_form();
	addHidden("database", array("user_id"=>$user->user_id));
	editObject($user, $fields, $title, "Save", true);
	end_form();	
}

//make the database operation associated to adding/editing a user
function edit_user_database()
{
	global $module;

	$user = new User;
	$user->user_id = getparam("user_id");
	$user->select();
	$params = form_params(array("email", "firstname", "lastname", "description"));
	if(!getparam("user_id"))
		$params["username"] = getparam("username");
	if($password=getparam("password"))
		$params["password"] = $password;

	$res = ($user->user_id) ? $user->edit($params) : $user->add($params);
	notice($res[1], "admins", $res[0]);
}

// user must acknowledge delete 
function delete_user()
{
	global $dont_change_admin_pass; 
	$user = new User;
	$user->user_id = getparam("user_id");
	$user->select();

	if ($user->username == "admin" && $dont_change_admin_pass===true)
		return errormess("You are not allowed to delete this user.");
	ack_delete('admin',$user->username,''/*$user->ackDelete()*/,"user_id",getparam("user_id"));
}

// perfom the delete option in the database
function delete_user_database()
{
	global $module,$dont_change_admin_pass;

	$user = new User;
	$user->user_id = getparam("user_id");
	$user->select();
	if ($user->username == "admin" && $dont_change_admin_pass===true)
		return errormess("You are not allowed to delete this user.");

	$count = $user->fieldSelect("count(*)");
	if ($count==1) 
		return errormess("You are not allowed to delete the last administrator.");
	$res = $user->objDelete();
	notice($res[1], "admins", $res[0]);
}

function cards()
{
	global $module, $block, $dir;

	if($_SESSION["pri_support"] != "yes" && $_SESSION["bri_support"] != "yes")
		return;
	if(isset($block[$dir."_".$module]) && in_array("cards",$block[$dir."_".$module]))
		return;

	$out = shell_command("server_hwprobe");

	if(!verify_wanrouter_output($out)) {
		errormess("No wanpipe cards present:".$out, "no");
		return;
	}

	$spans = get_spans();
	$out = array(); $err = array();
	
	exec("ls /etc/wanpipe/wanpipe*.conf",$out,$err);

	if(!count($out)) {
		message("The cards on your system are not configured. Click ".'<a class="llink" href="main.php?module='.$module.'&method=wizard_cards">here</a>'." to configure them.", "no");
		return;
	}
	// for testing purposes
	// print '<meta http-equiv="refresh" content="0;url='."main.php?module=$module&method=wizard_BRI".'">';

	$card_ports = Model::selection("card_port", null, "name, \"BUS\", \"SLOT\", \"PORT\"");
	$fields = array("card"=>"name", "protocol"=>"card_type", "function_get_port_type:type"=>"type", "filename");
	tableOfObjects($card_ports, $fields, "card_port", array("&method=configuration_file"=>_("View&nbsp;configuration")), array("&method=wizard_cards"=>_("Configure ports")));
}

function configuration_file()
{
	global $path, $module;

	$bus = getparam("BUS");
	$slot = getparam("SLOT");
	$port = getparam("PORT");

	$card_port = Model::selection("card_port", array("BUS"=>$bus, "SLOT"=>$slot, "PORT"=>$port));
	if(!count($card_port)) {
		$path .= "&method=cards";
		errormess(_("Invalid port"), $path);
		return;
	}
	$filename = "/etc/wanpipe/".$card_port[0]->filename;
	$fh = fopen($filename, "r");
	$content = fread($fh,filesize($filename));

	print '<a class="llink" href="main.php?module='.$module.'&method=cards">'._("Return").'</a><br/><br/>';
	print str_replace("\n", "<br/>", $content);
	print '<br/><br/><a class="llink" href="main.php?module='.$module.'&method=cards">'._("Return").'</a>';
}

function wizard_cards()
{
	global $steps, $logo, $title, $method, $module, $telephony_cards, $block, $dir;
	$method = "wizard_cards";

	if($_SESSION["pri_support"] != "yes" && $_SESSION["bri_support"] != "yes")
		return;
	if(isset($block[$dir."_".$module]) && in_array("cards",$block[$dir."_".$module]))
		return;


	$spans = get_spans();
	$steps = get_span_steps($spans);

	//$title = _("Configuring Sangoma cards");
	$title= "Configuring Sangoma cards";
	$logo = "images/small_logo.png";

	for($i=0; $i<count($spans); $i++) {
		$tel_card = $telephony_cards[$spans[$i]["telephony_card"]];
		$functions[$i] = "set_".$tel_card["type"]."_port";
	}

	$wizard = new Wizard($steps, $logo, $title, "wizard_cards_database", "main.php?module=$module&method=cards");
}

function wizard_cards_database()
{
	global $telephony_cards, $block, $dir, $module;

	if($_SESSION["pri_support"] != "yes" && $_SESSION["bri_support"] != "yes")
		return;
	if(isset($block[$dir."_".$module]) && in_array("cards",$block[$dir."_".$module]))
		return;

	$out = shell_command("server_stop");
	$mess = "Stopping wanrouter<br/>".str_replace("\n","<br/>",$out)."<br/>";

	$spans = get_spans();
	$fields = $_SESSION["fields"];
	$message = "";
	$errormess = "";
	Database::transaction();
	// delete existing BRI ports
	Database::query("DELETE FROM card_ports");

	$interfaces = "";
	for($i=0; $i<count($spans); $i++)
	{
		$tel_card = $telephony_cards[$spans[$i]["telephony_card"]];
		$card_type = $tel_card["type"];

		if($card_type == "BRI" && isset($fields[$i]))
			$fields[$i]["group"] = $i+1;
		$function = "configure_".$card_type."_file";;
		$ret = (isset($fields[$i])) ? $function($spans[$i], $i+1, $fields[$i]) : $function($spans[$i], $i+1);
		if(!$ret[0])
			$errormess .=  $ret[1];
		else {
			$message .= $ret[0];
			$index = $i+1;
			if($interfaces != "")
				$interfaces .= " ";
			$interfaces .= "wanpipe$index";
		}
	}

	if($errormess != "") {
		Database::rollback();
		return array(false, $errormess);
	}

	// clean sig_trunks and gateways that would user ports that aren't anymore
	$sig_trunk = new Sig_trunk;
	$trunks = $sig_trunk->fieldSelect("sig_trunk_id, sig", null, null, null, array("column"=>"port", "inner_column"=>"name", "inner_table"=>"card_ports", "relation"=>"NOT IN"));
	for($i=0; $i<count($trunks); $i++) {
		$card_conf = new Card_conf;
		$res = $card_conf->objDelete(array("section_name"=>$trunks[$i]["sig"]));

		$gateway = new Gateway;
		$res = $gateway->objDelete(array("sig_trunk_id"=>$trunks[$i]["sig_trunk_id"]));

		$sig_trunk = new Sig_trunk;
		$res = $sig_trunk->objDelete(array("sig_trunk_id"=>$trunks[$i]["sig_trunk_id"]));
	}

	Database::commit();

	// add all configured interfaces in wanrouter.rc from  /etc/wanpipe
	$conf_file = new ConfFile("/etc/wanpipe/wanrouter.rc");
	$conf_file->sections["WAN_DEVICES"] = '"'.$interfaces.'"';
	$conf_file->save();

	$out = shell_command("server_start");
	$mess .= "Starting wanrouter<br/>".str_replace("\n","<br/>",$out);

	return array(true,$mess);
}

function limits_international_calls()
{
	$limits = Model::selection("limit_international", array(), "limit_international_id");

	tableOfObjects($limits, array("Limit International calls"=>"limit_international", "value"), "limit", array("&method=edit_limit"=>'<img src="images/edit.gif" title="Edit" alt="Edit"/>'));
}

function edit_limit($error=NULL)
{
	if($error)
		errornote($error);

	$limit = new Limit_international;
	$limit->limit_international_id = getparam("limit_international_id");
	$limit->select();

	$fields = array(
		"limit_international" => array("display"=>"fixed"),
		"value" => array("comment"=>"Numeric value.")
	);

	start_form();
	addHidden("database",array("limit_international_id"=>getparam("limit_international_id")));
	editObject($limit, $fields, "Edit international limit", "Save");
	end_form();
}

function edit_limit_database()
{
	$nr = Numerify(getparam("value"));

	$limit = new Limit_international;
	$limit->value = ($nr=="NULL") ? "" : $nr;
	$res = $limit->fieldUpdate(array("limit_international_id"=>getparam("limit_international_id")),array("value"));
	notice($res[1], "limits_international_calls", $res[0]);
}

?>
