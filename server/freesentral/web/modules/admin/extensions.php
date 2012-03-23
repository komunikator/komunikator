<?php
/**
 * extensions.php
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
<script language="javascript">
function groupsClick(lin,col,role)
{
    if (copacClick(lin,col,role)) {
		if (role.substring(0,8) == 'group')
			window.location = "main.php?module=extensions&method=group_members&group="+copacValoare(lin,col);
		if (role == 'extension')
			window.location = "main.php?module=extensions&method=edit_extension&extension_name="+copacValoare(lin,col)+"&group="+copacParinte(lin,col);
    }
}
</script>
<?php
global $module, $method, $path, $action, $page, $limit, $fields_for_extensions, $operations_for_extensions, $upload_path;

require_once("lib/lib_extensions.php");

$fields_for_extensions = array("function_detect_busy:currently"=>"inuse_count,location", "extension", "firstname", "lastname", "function_groups_extension:groups"=>"extension_id");
$operations_for_extensions = array("&method=edit_extension"=>'<img src="images/edit.gif" title="Edit" alt="edit"/>', "&method=delete_extension"=>'<img src="images/delete.gif" title="Delete" alt="delete">', "&method=join_group"=>'<img src="images/join_group.gif" title="Join Group" alt="join group"/>', "&method=impersonate"=>'<img src="images/impersonate.gif" alt="impersonate" title="Impersonate"/>');

if(!$method || $method == "manage")
	$method = $module;

if(substr($method,0,4) == "add_")
	$method = str_replace("add_","edit_",$method);

if($action)
	$call = $method.'_'.$action;
else
	$call = $method;

$explanation = array("default" => "Extensions - Internal phones attached to the IP PBX.", "groups"=>"Groups - organise extensions in groups, in order to use the call hunting and queues functionality.");

$explanation["edit_group"] = $explanation["groups"];

print '<div style="display:inline; float:left; width:21%;margin-right:10px;">';
draw_tree();
explanations("images/extension.png", "", $explanation, "copac_explanations custom_explanation");
print '</div>';
if (getparam("group"))
	print "<script language=\"javascript\">copacComuta('".getparam("group")."');</script>\n";
print '<div class="content">';
$call();
print '</div>';

function draw_tree()
{
	$group_member = new Group_Member;
	$group_member->extend(array("extension"=>"extensions", "group"=>array("table"=>"groups", "join"=>"RIGHT")));
	$members = $group_member->extendedSelect(NULL, "\"group\",extension");

	//get an array with only the group and extension columns 
	$members = Model::objectsToArray($members, array("group"=>"","extension"=>""),true);
	$members = array_merge(array(array("group"=>"All extensions", "extension"=>"")), $members);
	tree($members,"groupsClick","copac_explanations");
}

function extensions()
{
	global $limit, $page, $fields_for_extensions, $module,  $operations_for_extensions, $method, $action;

	$module = "extensions";
	$method = "extensions";
	$action = NULL;

	$total = getparam("total");
	if(!$total)
	{
		$extension = new Extension;
		$total = $extension->fieldSelect("count(*)");
	}
	$extensions = Model::selection("extension",NULL,"extension", $limit, $page);

	items_on_page();
	pages($total);
	tableOfObjects($extensions, $fields_for_extensions, "extension",  $operations_for_extensions, array("&method=add_extension"=>"Add extension"));
}

function groups_extension($extension_id)
{
	$member = new Group_Member;
	$member->extend(array("group"=>"groups"));
	$groups = $member->extendedSelect(array("extension_id"=>$extension_id));

	$text = "";
	for($i=0; $i<count($groups); $i++) {
		if($text != "")
			$text .= ",";
		$text .= $groups[$i]->group;
	}
	return $text;
}

function group_members()
{
	global $fields_for_extensions, $operations_for_extensions, $module;

	$group = getparam("group");
	if(!$group)
	{
		errormess("Don't have name of group to make selection.");
		return;
	}
	if($group == "All extensions")
	{
		extensions();
		return;
	}
	$group = Model::selection("group", array("group"=>$group));
	if(!count($group))
	{
		errormess("Invalid group name");
		return;
	}

	$operations_for_extensions["&method=remove_from_group"] = '<img src="images/remove_from_group.gif" title="Remove from group" alt="remove from group"/>';
	$group = $group[0];
	$member = new Group_Member;
	$member->extend(array("extension"=>"extensions", "firstname"=>"extensions", "lastname"=>"extensions", "location"=>"extensions", "inuse_count"=>"extensions"));
	$members = $member->extendedSelect(array("group_id"=>$group->group_id), "extension");
	
	tableOfObjects($members, $fields_for_extensions, "extension",  $operations_for_extensions, array("&method=extension_to_group&group_id=".$group->group_id."&group=".$group->group=>"Add extension to group"), "main.php?module=$module&group_id=".$group->group_id);
}

function extension_to_group()
{
	global $module;

	$group_id = getparam("group_id");
	if (!$group_id) {
		errormess("Don't have group id.");
		return;
	}
	$group = Model::selection("group", array("group_id"=>$group_id));
	if (!count($group)) {
		errormess("Invalid group.");
		return;
	}
	$group = $group[0]->group;
	$extensions = Model::selection("extension", null, "extension", null, null, null, array("column"=>"extension_id", "inner_table"=>"group_members", "conditions"=>array("group_id"=>$group_id), "relation"=>"NOT IN"));
	$extensions = Model::objectsToArray($extensions, array("extension_id"=>"", "extension"=>""), true);

	$fields = array("extension"=>array($extensions, "display"=>"select", "comment"=>"Select extension to join group."));
	start_form();
	addHidden("database", array("group_id"=>$group_id, "group"=>$group));
	editObject(null, $fields, "Join group ".$group, "Save");
	end_form();
}

function extension_to_group_database()
{
	$extension_id = getparam("extension");
	$group_id = getparam("group_id");

	if (!$group_id)
		return errormess("Don't have group id.");

	if (!$extension_id) 
		return notice ("Please select extension before submitting.", "extension_to_group", false);

	$group_member = new Group_member;
	$res = $group_member->add(array("extension_id"=>$extension_id, "group_id"=>$group_id));
	if (!$res[0]) 
		return notice ($res[1], "group_members", false);
	else
		return notice ("Selected extension joined group.", "group_members", true);
}

function edit_extension($error = NULL)
{
	global $module;

	if($error)
		errornote($error);

/*	$equipments = Model::selection("equipment", NULL, "equipment");
	$equipments = Model::objectsToArray($equipments, array("equipment_id"=>"", "equipment"=>""));*/

	$extension = new Extension;
	$extension->extension_id = getparam("extension_id");
	if(getparam("extension_name")) {
		$extension->extension = getparam("extension_name");
		$extension->select('extension');
	}else
		$extension->select();

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
//	$equipments["selected"] = $equipments;
	$max_minutes = interval_to_minutes($extension->used_minutes);
	$max_minutes = (!$max_minutes) ? NULL : $max_minutes;
	$fields = array(
					"extension"=>array("compulsory"=>true, "comment"=>"Must have minimum 3 digits."),
					"password"=>array("compulsory"=>true, "comment"=>"Password must be numeric and have at least 6 digits. You can either insert it or use the 'Generate&nbsp;Password' option."),
					"generate_password"=>array("display"=>"checkbox", "comment"=>"Check to generate random password"),
					"firstname"=>"",
					"lastname"=>"",
					"address"=>""/*,
					"max_minutes"=>array("value"=>$max_minutes,"comment"=>"Leave this field empty for unlimited number of minutes"),
					"used_minutes"=>array("value"=>interval_to_minutes($extension->used_minutes), "display"=>"fixed", "comment"=>'<a href="main.php?module='.$module.'&extension_id='.$extension->extension_id.'">Reset&nbsp;Used&nbsp;Minutes</a>')*/,
			/*		"mac_address"=>array("comment"=>"Insert mac address here if you wish to provision a certain type of equipment."),
					"equipment"=>array($equipments,"display"=>"select")*/
				);
	if($extension->extension_id)
		$title = "Edit extension";
	else{
		$title = "Add extension";
		unset($fields["used_minutes"]);
	}

	start_form();
	addHidden("database", array("extension_id"=>$extension->extension_id));
	editObject($extension,$fields,$title, "Save",true);
	end_form();
}

function groups()
{
	global $method, $action;
	$method = "groups";
	$action = NULL;

	$groups = Model::selection("group", NULL, '"group"');
	tableOfObjects($groups, array("group", "extension"), "group",array("&method=edit_group"=>'<img src="images/edit.gif" title="Edit" alt="edit"/>', "&method=delete_group"=>'<img src="images/delete.gif" title="Delete" alt="delete"/>', "&method=group_members"=>'<img src="images/group_members.gif" title="Members" alt="members"/>'), array("&method=add_group"=>"Add group"));
}

function edit_group($error = NULL)
{
	if($error)
		errornote($error);

	$playlists = Model::selection("playlist",NULL,"playlist");
	$playlists = Model::objectsToArray($playlists, array("playlist_id"=>"", "playlist"=>""), true);

	$group = new Group;
	$group->group_id = getparam("group_id");
	$group->select();
	$playlists["selected"] = $group->playlist_id;
	$fields = array(
					"group" => array("compulsory"=>true),
					"extension" => array("compulsory"=>true, "comment"=>"Ex: 01 for Sales(Must be 2 digits long)"),
					"playlist" => array($playlists, "display"=>"select", "comment"=>"Music on hold playlist for this group."),
					"description" => array("display"=>"textarea")
				);
	if($group->group_id)
		$title = "Edit group";
	else
		$title = "Add group";
	start_form();
	addHidden("database", array("group_id"=>$group->group_id));
	editObject($group, $fields, $title, "Save", true);
	end_form();
}

function edit_group_database()
{
	global $path;
	$path .= "&method=groups";
	$group = new Group;
	$group->group_id = getparam("group_id");
	$params = form_params(array("group", "extension", "playlist"));
	$res = ($group->group_id) ? $group->edit($params) : $group->add($params);
	notice($res[1],"groups",$res[0]);
}

function join_group()
{
	$extension = new Extension;
	$extension->extension_id = getparam("extension_id");
	$extension->select();
	if(!$extension->extension)
	{
		errormess("Missing extension id or invalid one.");
		return;
	}
	$groups = Model::selection("group",NULL,'"group"',NULL,NULL,NULL,array("column"=>"group_id", "other_table"=>"group_members", "relation"=>"NOT IN", "conditions"=>array("extension_id"=>$extension->extension_id)));
	$groups = Model::objectsToArray($groups, array("group_id"=>"", "group"=>""),true);
	if(!count($groups))
	{
		errormess("There are no groups that this extension can join.");
		return;
	}

	start_form();
	addHidden("database", array("extension_id"=>$extension->extension_id));
	editObject(NULL, array("group"=>array($groups,"display"=>"select")), "Select group to join for extension ".$extension->extension, "Save");
	end_form();
}

function join_group_database()
{
	global $path;
	$path .= "&method=groups";
	$extension_id = getparam("extension_id");
	if(!$extension_id)
	{
		//errormess("Missing extension id");
		notice("Missing extension id", "groups", false);
		return;
	}
	$group_id = getparam("group");
	$group = new Group;
	$nr = $group->fieldSelect("count(*)", array("group_id"=>$group_id));
	if(!$nr)
	{
		//errormess("Invalid group id");
		notice("Invalid group id", "groups", false);
		return;
	}
	$group_member = new Group_Member;
	$res = $group_member->add(array("extension_id"=>$extension_id, "group_id"=>$group_id));

	$extension = new Extension;
	$extension->extension_id = $extension_id;
	$extension->select();

	if(!$res[0])
		//errormess("Could not join selected group", $path);
		notice("Could not join selected group", "groups", false);
	else
		//message("Extension ".$extension->extension." joined selected group", $path);
		notice("Extension ".$extension->extension." joined selected group", "groups");
}

//adding a range and setting a random password
function edit_range($error=NULL)
{
	if($error) 
		errornote($error);

	$fields = array(
					"from"=>array("value"=>getparam("from"), "compulsory"=>true, "comment"=>"Numeric value. Minimum 3 digits"),
					"to"=>array("value"=>getparam("to"), "compulsory"=>true, "comment"=>"Numeric value, higher than that inserted in the 'From' field. Must be the same number of digits as the 'From' field."),
					"generate_passwords"=>array("value"=>"t","display"=>"checkbox", "comment"=>"Check to generate random 6 digits passwords for the newly added extensions.")
				);

	start_form();
	addHidden("database");
	editObject(NULL, $fields, "Add range", "Save");
	end_form();
}

function delete_group()
{
	ack_delete("group", getparam("group"), NULL, "group_id", getparam("group_id"));
}

function delete_group_database()
{
	global $path;
	$path .= "&method=groups";

	$group = new Group;
	$group->group_id = getparam("group_id");
	//notify($group->objDelete(),$path);
	$res = $group->objDelete();
	notice($res[1], "groups", $res[0]);
}

function remove_from_group()
{
	global $module;

	$group_id = getparam("group_id");
	$extension_id = getparam("extension_id");
	if(!$group_id)
	{
		//errormess("Don't have group id");
		notice("Don't have group id", $module, false);
		return;
	}
	if(!$extension_id)
	{
		//errormess("Don't have extension id");
		notice("Don't have extension id", $module, false);
		return;
	}

	$member = Model::selection("group_member", array("group_id"=>$group_id, "extension_id"=>$extension_id));
	if(!count($member))
	{
		//errormess("This extension is not a member of the group");
		notice("This extension is not a member of the group", $module, false);
		return;
	}
	$res = $member[0]->objDelete();
	if($res[0])
		//message("Succesfully removed extension from group");
		notice("Succesfully removed extension from group", $module);
	else
		//errormess("Could not remore extension from group");
		notice("Could not remore extension from group", $module, false);
}

function delete_extension()
{
	ack_delete("extension",getparam("extension"),NULL,"extension_id",getparam("extension_id"));
}

function delete_extension_database()
{
	global $module;

	$extension = new Extension;
	$extension->extension_id = getparam("extension_id");
	//notify($extension->objDelete());
	$res = $extension->objDelete();
	notice($res[1],$module,$res[0]);
}

function search($error = NULL)
{
	if($error)
		errornote($error);

	$fields = array(
					"digits"=>array("value"=>getparam("digits"),"comment"=>"Insert digit or combination of digits to search after.", "compulsory"=>true),
					"start"=>array("display"=>"checkbox", "comment"=>"Check if extension should start with this digits"),
					"end"=>array("display"=>"checkbox", "comment"=>"Check if extension should end with this digits"),
					"all"=>array("display"=>"checkbox", "comment"=>"Check to get all extension that contain the inserted digits")
				);

	start_form();
	addHidden("database");
	editObject(NULL,$fields,"Search for extensions");
	end_form();
}

function search_database()
{
	global $fields_for_extensions, $operations_for_extensions;
	$digits = getparam("digits");
	$start = getparam("start");
	$end = getparam("end");
	$all = getparam("all");
	if(!$digits)
	{
		search("Please insert the digits to seach after");
		return;
	}
	if(Numerify($digits) == "NULL")
	{
		search("Field digits must be numeric");
		return;
	}
	if($all == "on")
		$conditions = array("extension"=>"__LIKE%$digits%");
	elseif($start == "on")
		$conditions = array("extension"=>"__LIKE$digits%");
	elseif($end == "on")
		$conditions = array("extension"=>"__LIKE%$digits");
	else{
		search("Please check one of the options for matching");
		return;
	}

	$extensions = Model::selection("extension",$conditions,"extension");
	tableOfObjects($extensions,$fields_for_extensions,"extension", $operations_for_extensions,array("&method=extensions"=>"Return"));
}

function import($error = NULL)
{
	if($error)
		errornote($error);

	$fields = array(
					"insert_file_location" => array("display"=>"file", "comment"=>"File type must be .csv"),
					"sample_.xls_file" => array("display"=>"fixed", "value"=>'<a class="llink" href="extensions.xls">Download</a>', "comment"=>"Complete the sample file with the information you wish to import and then export it as a .csv file. If you want to insert more than one group for an extension you should add ; between the name of the groups. Unexisting groups will be ignored.")
				);

	start_form(NULL,"post",true);
	addHidden("database");
	editObject(NULL,$fields,"Import extensions from .csv file", "Upload");
	end_form();
}

function export()
{
	global $upload_path;
	
	$file = "upload/exported_extensions.xls";
	if(is_file($file)) {
		unlink($file);
	}
	$fh = fopen($file, 'w') or die("Can't open file for writing.");
	$extensions = Model::selection("extension",NULL,"extension");

	$names = "Extension,Firstname,Lastname,Address\n";
	fwrite($fh, $names);
	for($i=0; $i<count($extensions); $i++)
	{
		$string = $extensions[$i]->extension . "," . $extensions[$i]->firstname . "," . $extensions[$i]->lastname . "," . $extensions[$i]->address . "\n";
		fwrite($fh, $string);
	}
	fclose($fh);
//	message('Extensions were exported. <a class="llink" href="exported_extensions.xls">Download</a>');
	notice('Extensions were exported. <a class="llink" href="'.$file.'">Download</a>');
}