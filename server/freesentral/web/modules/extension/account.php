<?php
/**
 * account.php
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

global $module, $method, $path, $action, $page;

if(!$method)
	$method = $module;

if(substr($method,0,4) == "add_")
	$method = str_replace("add_","edit_",$method);

if($action)
	$call = $method.'_'.$action;
else
	$call = $method;

$call();

function account()
{
	$extension = new Extension;
	//$extension->extend(array("equipment"=>"equipments"));
	$extension = $extension->extendedSelect(array("extension"=>$_SESSION["user"]));
	$extension = $extension[0];

	$fields = array(
					"extension" => array("display"=>"fixed"),
					"status" => array("value"=>show_status($extension->location), "display"=>"fixed"),
					"password" => array("display"=>"link_change_password"),
					"firstname" => array("display"=>"fixed"),
					"lastname" => array("display"=>"fixed"),
					"address" => array("display"=>"fixed"),
				/*	"max_minutes" => array("display"=>"fixed"),
					"used_minutes" => array("display"=>"fixed"),*/
				/*	"equipment" => array("display"=>"fixed"),
					"mac_address" => array("display"=>"fixed")*/
				);

	start_form();
	addHidden("modify");
	editObject($extension,$fields,"Account information", "Modify", false, true);
	end_form();
}

function link_change_password()
{
	global $module;

	print '<a class="llink" href="main.php?module='.$module.'&method=change_password">Change&nbsp;Password</a>';
}

function change_password($error=NULL)
{
	if($error)
		errornote($error);	

	start_form();
	addHidden("database");
	editObject(NULL,array("old_password"=>array("display"=>"password", "compulsory"=>true), "new_password"=>array("display"=>"password", "compulsory"=>true, "comment"=>"Numeric. Must be at least 6 digits long"), "retype_new_password"=>array("display"=>"password", "compulsory"=>true)), "Change password", "Save");
	end_form();
}

function change_password_database()
{
	$extension = new Extension;
	$extension->select(array("extension"=>$_SESSION["user"]));

	$old_password = getparam("old_password");
	$new_password = getparam("new_password");
	$retype_new_password = getparam("retype_new_password");

	if($old_password != $extension->password)
	{
		change_password("Wrong password");
		return;
	}

	if($new_password != $retype_new_password)
	{
		change_password("The two passwords don't match");
		return;
	}

	if(strlen($new_password) < 6)
	{
		change_password("Password is too short");
		return;
	}

	if(Numerify($new_password) == "NULL")
	{
		change_password("Password must be numeric");
		return;
	}

	$extension->password = $new_password;
	$res = $extension->update();
	if($res[0])
//		message("Password was succesfully changed");
		notice("Password was succesfully changed");
	else
//		errormess("Could not change your password");
		notice("Could not change your password", NULL, false);
}

function account_modify()
{
	$extension = new Extension;
	$extension->select(array("extension"=>$_SESSION["user"]));

/*	$equipments = Model::selection("equipment",NULL,"equipment");
	$equipments = Model::objectsToArray($equipments,array("equipment_id"=>"", "equipment"=>""),true);
	$equipments["selected"] = $extension->equipment_id;*/

	$fields = array(
					"extension" => array("display"=>"fixed"),
					"firstname" => array(),
					"lastname" => array(),
					"address" => array(),
				/*	"max_minutes" => array("display"=>"fixed"),
					"used_minutes" => array("display"=>"fixed"),*/
				/*	"equipment" => array($equipments, "display"=>"select", "comment"=>"Supported types of equipment. If your equipment is in this list you can provision it by inserting the mac address below."),
					"mac_address" => array("comment"=>"Insert the mac address here if your equipmnent is supported and you wish to provision it automatically."),*/
				);

	start_form();
	addHidden("modify_database");
	editObject($extension,$fields,"Account information", "Save", false, true);
	end_form();
}

function account_modify_database()
{
	$extension = Model::selection("extension", array("extension"=>$_SESSION["user"]));
	if(!count($extension)) {
		message("Can't select account information");
		return;
	}	
	$extension = $extension[0];
/*	if(getparam("equipment") && getparam("equipment") != "Not selected")
	{
		$extension->equipment_id = getparam("equipment");
		$extension->mac_address = getparam("mac_address");
		if(!$extension->mac_address){
			$extension->equipment_id = NULL	;
			print "Ignoring field equipment since you didn't insert a mac address.<br/>";
		}
	}

	if(getparam("mac_address") && (!getparam("equipment") || getparam("equipment") == "Not selected"))
	{
		print "Ignoring field mac address since you didn't select an equipment";
	}*/

	$fields = form_params(array("firstname","lastname","address"));
	$res = $extension->edit($fields);
	notice($res[1],NULL,$res[0]);
}

?>
</div>