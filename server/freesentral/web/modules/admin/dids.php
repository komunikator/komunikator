<?php
/**
 * dids.php
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
require_once("lib/lib_auto_attendant.php");
global $module, $method, $path, $action, $page;

if(!$method)
	$method = $module;

if(substr($method,0,4) == "add_")
	$method = str_replace("add_","edit_",$method);

if($action)
	$call = $method.'_'.$action;
else
	$call = $method;

$explanation = array("default"=>"DIDs - A call can go directly to a phone from inside the FreeSentral, by defining the destination as a DID. The destination can be an extension, a group of extensions, a voicemail, etc. ", "conferences"=>"Conferences - use the number associated with each room to connect to the active conference room.");
$explanation["edit_conference"] = $explanation["conferences"];

explanations("images/dids.png", "", $explanation);

print '<div class="content">';
$call();
print '</div>';

function manage()
{
	dids();
}

function edit_conference()
{
	$did = new Did;
	$did->did_id = getparam("did_id");
	$did->select();

	$fields = array(
					"conference" => array("value"=>get_conf_from_did($did->did, $did->destination), "compulsory"=>true, "comment"=>"Name for conference chamber"),
					"number" => array("value"=>$did->number, "compulsory"=>true, "comment"=>"Number people need to call to enter the conference. This number must be unique in the system: must not match a did, extension or group.")
				);
	$title = ($did->did_id) ? "Edit conference" : "Add conference";

	start_form();
	addHidden("database", array("did_id"=>$did->did_id));
	editObject(null, $fields, $title, "Save");
	end_form();
}

function edit_conference_database()
{
	$did = new Did;
	$did->did_id = getparam("did_id");
	$params = array("did"=>"conference " . getparam("conference"), "number"=>getparam("number"), "destination"=>"conf/".getparam("conference"));
	$res = ($did->did_id) ? $did->edit($params) : $did->add($params);
	notice($res[1], "conferences", $res[0]);
}

function delete_conference()
{
	ack_delete("conference", get_conf_from_did(getparam("did"), getparam("destination")), null, "did_id", getparam("did_id"));
}

function delete_conference_database()
{
	$did = new Did;
	$did->did_id = getparam("did_id");
	$res = $did->objDelete();

	if($res[0])
		notice("Conference was deleted.", "conferences", true);
	else
		notice("Could not delete conference.", "conferences", false);
}

function conferences()
{
	$conferences = Model::selection("did", array("destination"=>"__LIKEconf/"), "did");
	$fields = array("function_get_conf_from_did:conference"=>"did,destination", "number", "function_conference_participants:participants"=>"number");
	tableOfObjects($conferences, $fields, "conference", array("&method=edit_conference"=>'<img src="images/edit.gif" alt="Edit" title="Edit" />', "&method=delete_conference"=>'<img src="images/delete.gif" alt="Delete" title="Delete" />'), array("&method=add_conference"=>"Add conference"));
}

function dids()
{
	global $method, $action;
	$method = "dids";
	$action = NULL;

	$did = new Did;
	$did->extend(array("extension"=>"extensions", "group"=>"groups"));
	$dids = $did->extendedSelect(array("destination"=>"__NOT LIKEconf/"),"number");

	$formats = array("DID"=>"number", "function_verif_destination:destination"=>"destination"/*, "function_get_default_destination:default_destination"=>"extension,group"*/);
	tableOfObjects($dids, $formats, "DIDs", array("&method=edit_did"=>'<img src="images/edit.gif" title="Edit" alt="Edit"/>', "&method=delete_did"=>'<img src="images/delete.gif" title="Delete" alt="Delete"/>'),array("&method=add_did"=>"Add DID"));
}

function verif_destination($destination)
{
	if ($destination == "external/nodata/voicemaildb.php")
		return "voicemail";
	elseif ($destination == "external/nodata/auto_attendant.php")
		return "auto attendant";
	return $destination;
}

function edit_did($error=NULL, $title=null)
{
	if($error)
		errornote($error);
	$did = new Did;
	$did->did_id = getparam("did_id");
	$did->select();
	if($error) {
		$did->number = getparam("number");
		$dest = getparam("destination");
		$did->destination = ($dest && $dest!="custom") ? $dest : getparam("insert_destination");
		$did->default_destination = getparam("default_destination");
	}

	$extensions = Model::selection("extension", null, "extension");
	$extensions = Model::objectsToArray($extensions, array("extension"=>"destination_id", "2_extension"=>"destination"), true);
	$groups = Model::selection("group", null, "\"group\"");
	$groups = Model::objectsToArray($groups, array("extension"=>"destination_id", "group"=>"destination"),true);

	$destinations = array_merge(
			array(array("destination_id"=>"custom", "destination"=>"Custom destination >>")),
			array(array("destination_id"=>"external/nodata/voicemaildb.php", "destination"=>"voicemail")),
			array(array("destination_id"=>"external/nodata/auto_attendant.php", "destination"=>"auto attendant")),
			array(array("destination_id"=>"__disabled", "destination"=>"--Groups--")),
			$groups,
			array(array("destination_id"=>"__disabled", "destination"=>"--Extensions--")),
			$extensions
	);
	$insert_destination='';
	if ($did->destination) {
		for($i=0; $i<count($destinations); $i++) {
			if($destinations[$i]["destination_id"] == $did->destination) {
				$destinations["selected"] = $did->destination;
				break;
			}
		}
		if (!isset($destinations["selected"])) {
			$destinations["selected"] = "custom";
			$insert_destination = $did->destination;
		}
	}
	$def = build_default_options($did);
	$fields = array (
		"number"=>array("compulsory"=>true, "column_name"=>"DID", "comment"=>"Incoming phone number."),
		"destination"=>array($destinations, "display"=>"select","compulsory"=>true, "comment"=>"Select extension/group/voicemail or custom destination to send the call coming to the number set in DID field.", "javascript"=>"onChange='check_selected_destination();'"),
		"insert_destination"=>array("value"=>$insert_destination, "triggered_by"=>"custom", "comment"=>"Number or script: \"external/nodata/script_name.php\""),
		"default_destination" => array($def, "display"=>"select", "triggered_by"=>"auto attendant", "compulsory"=>true, "comment"=>"Choose a group or an extension for the call to go to if no digit was pressed. Set only when destination is voicemail."),
		"description"=>array("display"=>"textarea")
	);
	if ($insert_destination != "")
		unset($fields["insert_destination"]["triggered_by"]);
	if (isset($destinations["selected"]) && $destinations["selected"] == "external/nodata/auto_attendant.php")
		unset($fields["default_destination"]["triggered_by"]);

	start_form();
	addHidden("database",array("did_id"=>$did->did_id));
	if($did->did_id)
		$title = "Edit Direct inward dialing";
	else
		$title = "Add Direct inward dialing";
	editObject($did,$fields,$title,"Save",true);
	end_form();
}

function edit_did_database()
{
	global $module;

	$did = new Did;
	$did->did_id  = getparam("did_id");
	$params = form_params(array("number", "description"));
	if (($def=getparam("default_destination"))) {
		$def = explode(":", $def);
		$params[$def[0]."_id"] = $def[1];
		$oth = ($def[0] == "extension") ? "group_id" : "extension_id";
		$params[$oth] = null;
	} else {
		$params["extension_id"] = null;
		$params["group_id"] = null;
	}
	$dest = getparam("destination");
	$params["destination"] = ($dest && $dest != "custom") ? $dest : getparam("insert_destination");

	$res = ($did->did_id) ? $did->edit($params) : $did->add($params);

	if($res[0])
		notice($res[1], $module, $res[0]);
	else
		edit_did($res[1]);
}

?>