<?php
/**
 * verify_settings.php
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
<div class="content wide">
<?php
require_once("lib_gateways.php");
require_once("lib_auto_attendant.php");
require_once("lib_extensions.php");

global  $module, $method, $path, $action, $page, $limit, $fields_for_extensions, $operations_for_extensions, $upload_path;

$steps = array(1=> "message_wizard", 2=> "verify_admin_pass", 3=> "extensions", 4=> "outbound", 5=> "set_voicemail", 6=> "aut_set_prompts", 7=>"aut_define_keys", 8=>"aut_scheduling", 9=>"aut_activate_did", 10=> "finish_message");

if(!isset($_SESSION["step_nr"])) {
	$_SESSION["step_nr"] = 1;
}

$name = $steps[$_SESSION["step_nr"]];
$method = (!$method) ? $name : $method;

$call = ($action) ? $method."_".$action : $method;

if(!function_exists($call))
	$call = $name;

$call();	

function message_wizard()
{
	global $module;

	print '<div class="message_wizard">';
	print '<font class="welcome">Welcome to FreeSentral!</font><br/><br/>This wizard will walk you trough the steps you need to perform in order to get your system up and running.<br/><br/>';
	print 'Do you wish to use the wizard to configure your system?<br/><br/>';
	print '<font class="font_button" onClick="location.href=\'main.php?module='.$module.'&method=message_wizard&action=database&answer=yes\'">Yes</font>&nbsp;&nbsp;&nbsp;&nbsp;
			<font class="font_button" onClick="location.href=\'main.php?module='.$module.'&method=message_wizard&action=database&answer=notnow\'">Not now</font>&nbsp;&nbsp;&nbsp;&nbsp;
			<font class="font_button" onClick="location.href=\'main.php?module='.$module.'&method=message_wizard&action=database&answer=no\'">No&nbsp;</font>';
	print '<br/><br/>By clicking \'No\', you will never get this message again.';
	print '</div>';
}

function message_wizard_database()
{
	$answer = getparam("answer");
	if($answer == "no") {
		$setting = Model::selection("setting",array("param"=>"wizard"));
		if(count($setting)) {
			$setting[0]->value = "never use";
			$setting[0]->update();
		}
		$_SESSION["wizard"] = "never use";
		echo '<meta http-equiv="refresh" content="0;url=main.php">';
	}elseif($answer == "notnow"){
		$_SESSION["wizard"] = "skip";
		echo '<meta http-equiv="refresh" content="0;url=main.php">';
	}else{
		$_SESSION["step_nr"] = 2;
		verify_admin_pass();
		return;
	}
}

function verify_admin_pass()
{
	$admin = Model::selection("user", array("username"=>"admin", "password"=>"admin"));

	if(!count($admin)) {
		$_SESSION["step_nr"] = "3";
		extensions();
		return;
	}

	print '<div class="message_wizard">';
	print("System has detected that you are still using the default user and default password.<br/><br/>Please use the below form in order to change the password for the default user.");

	$array = array(
					"new_password" => array("display"=>"password", "compulsory"=>true, "comment"=>"At least 5 digits long."),
					"retype_new_password" => array("display"=>"password", "compulsory"=>true)
				);

	start_form();
	addHidden("database", array("method"=>"verify_admin_pass"));
	editObject(NULL, $array, "Change default password.", "Save");
	end_form();
	print '</div>';
}

function verify_admin_pass_database()
{
	$password = getparam("new_password");
	$retype = getparam("retype_new_password");
	$admin = Model::selection("user", array("username"=>"admin", "password"=>"admin"));
	if(!count($admin)) {
		$_SESSION["step_nr"] = 3;
		extensions();
		return;
	}

	if($password != $retype) {
		notice("The two passwords don't match", "verify_admin_pass", false);
		return;
	}
	if(strlen($password)<5) {
		notice("Password must be at least 6 digits long", "verify_admin_pass", false);
		return;
	}

	$admin = $admin[0];
	$admin->password = $password;
	$res = $admin->update();
	if($res[0]) {
		$_SESSION["step_nr"] = 3;
		extensions();
		return;
	}else{
		notice("Could not change password", "verify_admin_pass", false);
	}

	extensions();
	return;
}

function extensions()
{
	print '<div class="subtitle">Extensions</div>';
	$extension = new Extension;
	$extensions = $extension->fieldSelect("extension",array(),NULL,'extension');
	print "There are ".count($extensions)." extensions in the system";
	if(count($extensions)) {
		print ': ';
		for($i=0; $i<count($extensions); $i++) {
			if($i!=0)
				print ', ';
			print $extensions[$i]["extension"];
		}
		print '. <br/><br/>';
	}else
		print '.<br/><br/>';

	print '<table class="col3" cellspacing="0" cellpadding="0">';
	print '<tr>';

	print '<td class="addextension">';
	$extension = new Extension;
	$extension->extension_id = getparam("extension_id");
	$extension->extension = getparam("extension_name");

	if(!$extension->extension_id)
	{
		$extension->extension = getparam("extension");
		$extension->password = getparam("password");
		$extension->firstname = getparam("firstname");
		$extension->lastname = getparam("lastname");
		$extension->address = getparam("address");
		$extension->max_minutes = getparam("max_minutes");
	/*	$extension->mac_address = getparam("mac_address");
		$extension->equipment_id = getparam("equipment");*/
	}
	$equipments["selected"] = $equipments;
	$max_minutes = interval_to_minutes($extension->used_minutes);
	$max_minutes = (!$max_minutes) ? NULL : $max_minutes;
	$fields = array(
					"extension"=>array("compulsory"=>true, "comment"=>"Must have minimum 3 digits."),
					"password"=>array("compulsory"=>true, "comment"=>"Password must be numeric and have at least 6 digits. You can either insert it or use the 'Generate&nbsp;Password' option."),
					"generate password"=>array("display"=>"checkbox", "comment"=>"Check to generate random password"),
					"firstname"=>"",
					"lastname"=>"",
					"address"=>"",
					"max minutes"=>array("value"=>$max_minutes,"comment"=>"Leave this field empty for unlimited number of minutes")
			/*		"mac_address"=>array("comment"=>"Insert mac address here if you wish to provision a certain type of equipment."),
					"equipment"=>array($equipments,"display"=>"select")*/
				);

	start_form();
	addHidden("database", array("method"=>"add_extension"));
	editObject($extension,$fields,"Add extensions one by one", "Save",true,NULL,"smaller_edit3",NULL,array("left"=>"70px", "right"=>"190px"));
	end_form();	
	print '</td>';

	print '<td class="addrange">';
	$fields = array(
					"from"=>array("value"=>getparam("from"), "compulsory"=>true, "comment"=>"Numeric value. Minimum 3 digits"),
					"to"=>array("value"=>getparam("to"), "compulsory"=>true, "comment"=>"Numeric value, higher than that inserted in the 'From' field. Must be the same number of digits as the 'From' field."),
					"generate passwords"=>array("value"=>"t","display"=>"checkbox", "comment"=>"Check in order to generate random 6 digits passwords for the newly added extensions")
				);

	start_form();
	addHidden("database",array("method"=>"add_range"));
	editObject(NULL, $fields, "Add range of extensions", "Save",NULL,NULL,"smaller_edit2",NULL,array("left"=>"60px", "right"=>"180px"));
	end_form();
	print '</td>';

	print '<td class="import">';

	$fields = array(
					"insert_file location" => array("display"=>"file", "comment"=>"File type must be .csv"),
					"sample .xls_file" => array("display"=>"fixed", "value"=>'<a class="llink" href="extensions.xls">Download</a>', "comment"=>"Complete the sample file with the information you wish to import and then export it as a .csv file. If you want to insert more than one group for an extension you should add ; between the name of the groups. Unexisting groups will be ignored.")
				);

	start_form(NULL,"post",true);
	addHidden("database",array("method"=>"import"));
	editObject(NULL,$fields,"Import extensions from .csv file", "Upload",NULL,NULL,"smaller_edit",NULL,array("left"=>"50px", "right"=>"260px"));
	end_form();	

	print '</td>';

	print '</tr>';
	print '</table>';

	buttons('outbound');
}

function buttons($next = NULL, $previous = NULL, $align = "center", $finish = false)
{
	global $module;

	if($align == "center")
		print '<center>';

	if($previous) {
		print '<font class="font_button"';
		print ' onClick="location.href=\'main.php?module='.$module.'&method='.$previous.'\'"';
		print '>Previous</font>&nbsp;&nbsp;&nbsp;&nbsp;';
	}
	if($next) {
		print '<font class="font_button"';
		print ' onClick="location.href=\'main.php?module='.$module.'&method='.$next.'\'"';
		if($finish === true)
			print '>Finish</font>';
		else
			print '>Next</font>';
	}
	if($align == "center")
		print '</center>';
}

function outbound()
{
	global $method;
	print '<div class="subtitle">Outbound</div>';

	$_SESSION["step_nr"] = 4;

	$gateways = Model::selection("gateway", NULL, "gateway");
	$formats = array("function_gateway_status:&nbsp;"=>"enabled,status,username", "gateway", "function_gateway_type:requires_registration"=>"username","server", "protocol", "function_registration_status:status"=>"status,username", "enabled");

	if(count($gateways)) {
		tableOfObjects($gateways, $formats, "gateway");
		print "<br />";
	} else
		print 'There isn\'t any gateway defined.<br/><br/>';

	$dial_plan = new Dial_Plan;
	$dial_plan->extend(array("gateway"=>"gateways", "protocol"=>"gateways"));
	$dial_plans = $dial_plan->extendedSelect(array(), "prefix,priority");

	$formats = array("dial_plan","prefix","priority","gateway","protocol");

	if(count($dial_plans)) {
		tableOfObjects($dial_plans, $formats, "dial plan");
		print "<br />";
	} else
		print 'There isn\'t any dial plan defined.<br/><br/>';

	print '<table class="col3">';
	print '<tr>';
	print '<td class="addgateway">';
	$method = "edit_gateway";
	edit_gateway();
	print '</td>';
	print '<td class="adddialplan">';
	$method = "edit_dial_plan";
	edit_dial_plan();
	print '</td>';
	print '</tr>';
	print '</table>';

	if(!count($gateways) || !count($dial_plans)) {
		print '<font class="error">Note!!</font> You must define at least one gateway and one dial plan before passing to the next step of the configuration.<br/><br/>';
		buttons(NULL, "extensions");
	}else
		buttons("set_voicemail", "extensions");
}

function set_voicemail()
{
	global $method;
	$method = "set_voicemail";

	$_SESSION["step_nr"] = 5;

	print '<div class="subtitle">Set Voicemail</div>';

	$did = Model::selection("did", array("destination"=>"external/nodata/voicemaildb.php"));
	if(count($did)) {
		print "Voicemail is set. If you want do define another voicemail number use the DIDs tab and insert another did  with the destination 'external/nodata/voicemaildb.php'. <br /><br />";

		tableOfObjects($did, array("did", "number", "destination"), "did");
		print "<br /><br />";
	}else{
		$did = new Did;
		
		$fields = array(
						"did" => array("value"=>"voicemail", "display"=>"text", "compulsory"=>true),
						"number" => array("display"=>"text", "compulsory"=>true, "comment"=>"Number for voicemail."),
						"destination" => array("value"=>"external/nodata/voicemaildb.php", "display"=>"fixed", "compulsory"=>true)
					);
		start_form();
		addHidden("database");
		editObject($did, $fields, "Set voicemail", "Save");
		end_form();
	}
	buttons("aut_set_prompts", "outbound");
}

function aut_set_prompts()
{
	print '<div class="subtitle">Auto Attendant: Set Prompts</div>';

	$_SESSION["step_nr"] = 6;
	$nr = prompts();

	print '<br /><br />';
	if($nr)
		buttons("aut_define_keys", "set_voicemail");
	else
		buttons(NULL, "set_voicemail");
}

function aut_define_keys()
{
	print '<div class="subtitle">Auto Attendant: Define keys</div>';

	$_SESSION["step_nr"] = 7;

	$nr = keys();

	if($nr)
		buttons("aut_scheduling", "aut_set_prompts");
	else
		buttons(NULL, "aut_set_prompts");
}

function aut_scheduling()
{
	print '<div class="subtitle">Auto Attendant: Schedule online Auto Attendant </div>';
	$_SESSION["step_nr"] = 8;

	scheduling();

	buttons("aut_activate_did", "aut_define_keys");
}

function aut_activate_did()
{
	print '<div class="subtitle">Auto Attendant: Activate DID for Auto Attendant </div>';
	$_SESSION["step_nr"] = 9;

	activate();

	print "<br />";
	buttons("finish_message", "aut_scheduling", "center", true);
}

function finish_message()
{
	$_SESSION["wizard"] = "used";
	unset($_SESSION["step_nr"]);

	$setting = Model::selection("setting", array("param"=>"wizard"));
	if(count($setting)) {
		$setting[0]->value = "used";
		$setting[0]->update();
	}

	print '<div class="message_wizard">';
	print 'You have finished configuring your system. Go to <a class="llink" href="main.php">Home</a>';
	print '</div>';
}

function set_voicemail_database()
{
	$did = new Did;
	$did->did = getparam("did");
	if(!$did->did) {
		errormess("Field 'Did' is required.", "no");
		print "<br /><br />";
		set_voicemail();
		return;
	}
	if($did->ObjectExists()) {
		errormess("There is already a did with this name.", 'no');
		print "<br /><br />";
		set_voicemail();
		return;
	}
	$did->destination = "external/nodata/voicemaildb.php";
	$did2 = new Did;
	$did2->number = getparam("number");
	if(!$did2->number) {
		errormess("Field 'Number' is required.","no");
		print "<br /><br />";
		set_voicemail();
		return;
	}
	if($did2->objectExists()) {
		errormess("There is already a did for this number.", "no");
		print "<br /><br />";
		set_voicemail();
		return;
	}
	$did->number = $did2->number;
	$res = $did->insert();
	notice($res[1],"no",$res[0]);
	print "<br /><br />";
	set_voicemail();
}

function add_range_database()
{
	edit_range_database();
}

function add_extension_database()
{
	edit_extension_database();
}

?>
</div>