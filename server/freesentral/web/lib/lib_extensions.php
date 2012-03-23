<?php
/**
 * lib_extensions.php
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

if($_SESSION["level"] != "admin")
	forbidden();

function import_database()
{
	global $upload_path, $module;

	$filename = basename($_FILES["insert_file_location"]["name"]);
	if(strtolower(substr($filename,-4)) != ".csv")
	{
		notice("File format must be .csv", "no", false);
		extensions();
		return;
	}

	if(!is_dir($upload_path))
		mkdir($upload_path);

	$file = "$upload_path/$filename";
	if (!move_uploaded_file($_FILES["insert_file_location"]['tmp_name'],$file)) {
		//errormess("Could not upload file.");
		notice("Could not upload file.", "no", false);
		extensions();
		return;
	}

	$handle = fopen($file,'r');
	$content = fread($handle,filesize($file));

	$content = eregi_replace('"','',$content); // in case they choose to mark text fields with "
	$content = eregi_replace("'",'',$content);  // in case they choose to mark text fields with '
	$content = explode("\n",$content);

	$names = explode(",",$content[0]);	
	$error = '';

	$setting = new Setting;
	$prefix = $setting->fieldSelect("MAX(CASE WHEN param='prefix' THEN value ELSE NULL END) as prefix");

	for($i=1; $i<count($content); $i++)
	{
		$line = $content[$i];
		$line = explode(",",$line);
		if(count($line) != count($names))
			continue;
		$fields = array();

		for($j=0; $j<count($names); $j++)
			$fields[strtolower($names[$j])] = $line[$j];
		if(!isset($fields["extension"]))
			continue;
		if(!($fields["extension"]) || $fields["extension"] == "")
			continue;
		if(Numerify($fields["extension"]) == "NULL")
		{
			$error .= "Field extension must be numeric. Wrong input ".$fields["extension"].".<nr/>";
			continue;
		}
		if(strlen($fields["extension"]) < 3)
		{
			$error .= "Field extension must be minimum 3 digits long. Wrong input ".$fields["extension"].".<nr/>";
			continue; 
		}
		Database::transaction();
		$extension = new Extension;
		$extension->extension = $fields["extension"];
		if($extension->objectExists("extension_id")) {
			print "Skipping ".$extension->extension." because it already exists.<br/>";
			Database::rollback();
			continue;
		}
		if($prefix && $prefix==substr($extension->extension,0,strlen($prefix))) {
			print "Skipping ".$extension->extension." because it starts the same as the system prefix.<br/>";
			Database::rollback();
			continue;
		}
		$extension->firstname = $fields["firstname"];
		$extension->lastname = $fields["lastname"];
		$extension->address = $fields["address"];
		$extension->password = rand(100000,999999);
		$res = $extension->insert(true);
		if(!$res[0]) {
			$error .= "Could not insert extension : ".$extension->extension;
			Database::rollback();
			continue;
		}
		$groups = $fields["groups"];
		$groups = explode(";", $groups);
		for($j=0; $j<count($groups); $j++) {
			$groups[$j] = trim($groups[$j]);
			if(!$groups[$j] || $groups[$j] == '')
				continue;
			$gr = new Group;
			$gr_index = $gr->fieldSelect("group_id", array('__sql_lower(groups."group")'=>strtolower($groups[$j])));
			if(!$gr_index) {
				errormess('Unknown group '.$groups[$j].'<br/>', 'no');
				continue;
			}
			$group_member = new Group_Member;
			$group_member->group_id = $gr_index;
			$group_member->extension_id = $extension->extension_id;
			$group_member->insert();
			if(!$group_member->group_member_id) {
				errromess("Could not make extension ".$extension->extension." a member to group ".$groups[$j].'<br/>', 'no');
				continue;
			}
		}
		Database::commit();
	}

	if($error != '')
		notice($error, "no", false);
	else
		notice("Finished importing extensions", "no");
	extensions();
}

function edit_extension_database()
{
	global $module;

	$extension = new Extension;
	$extension->extension_id = getparam("extension_id");
	$params = form_params(array("extension", "firstname", "lastname", "address", /*"max_minutes",*/ "password"));
	if(getparam("generate_password") == "on")
		$params["password"] = rand(100000, 999999);

	$setting = new Setting;
	$prefix = $setting->fieldSelect("MAX(CASE WHEN param='prefix' THEN value ELSE NULL END) as prefix");
	if($prefix && $prefix==substr(getparam("extension"),0,strlen($prefix))) {
		errormess("Extension starts the same as the system prefix.","no");
		extensions();
		return;
	}

	$res = ($extension->extension_id) ? $extension->edit($params) : $extension->add($params);
	notice($res[1],"no",$res[0]);
	extensions();
}

function edit_range_database()
{
	global $module;

	$call = "edit_range";

	$from = getparam("from");
	$to = getparam("to");
	$error = '';
	if(strlen($from) < 3)
		$error .= " Field 'From' must have minimum 3 digits";

	if(Numerify($from) == "NULL")
		$error .= "Field 'From' must be numeric";

	if(strlen($to) != strlen($from))
		$error .= "Field 'To' must have the same number of digits as the 'From' field.";

	if(Numerify($to) == "NULL")
		$error .= "Field 'To' must be numeric";

	if($from > $to)
		$error .= "Field 'From' must be smaller than 'To'";

	if($error != "")
	{
		errormess($error, "no");
		$call();
		return;
	}

	$setting = new Setting;
	$prefix = $setting->fieldSelect("MAX(CASE WHEN param='prefix' THEN value ELSE NULL END) as prefix");

	$generate_password = getparam("generate_passwords");
	for($i=Numerify($from); $i<=Numerify($to); $i++)
	{
		$extension = new Extension;
		$extension->extension = Numerify($i);
		while(strlen($extension->extension) < strlen($from))
			$extension->extension = '0'.$extension->extension;
		if($extension->objectExists())
		{
			print 'Skipping extention '.$extension->extension.' because it was previously added.<br/>';
			continue;
		}
		if($prefix && $prefix==substr($extension->extension,0,strlen($prefix))) {
			print "Skipping extension ".$extension->extension." because it starts the same as the system prefix.<br/>";
			continue;
		}
		if($generate_password == "on")
			$extension->password = rand(100000,999999);
		$extension->insert(false);
	}
	//message("Finished inserting free extensions is range $from-$to");
	notice("Finished inserting free extensions is range $from-$to", "no");
	$call();
}

?>