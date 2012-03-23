<?php
/**
 * address_book.php
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
global $module, $method, $path, $action, $page, $limit, $fields_for_extensions, $operations_for_extensions, $upload_path;

require_once("socketconn.php");

if(!$method || $method == "manage")
	$method = $module;

if(substr($method,0,4) == "add_")
	$method = str_replace("add_","edit_",$method);

if($action)
	$call = $method.'_'.$action;
else
	$call = $method;

$call();

function address_book()
{
	print "<div class=\"notify\">System address book</div><br/>";
	$short_names = Model::selection("short_name", array("extension_id"=>"__empty"), "short_name");	
	tableOfObjects($short_names, array("short_name", "number", "name"), "system short name", array("&method=make_call"=>'<img src="images/call.gif" title="Call" alt="Call"/>'));

	print "<br/><br/><div class=\"notify\">Personal address book</div><br/>";
	$short_names = Model::selection("short_name", array("extension_id"=>$_SESSION["user_id"]), "short_name");
	tableOfObjects($short_names, array("short_name", "number","name"), "short name defined by you", array("&method=edit_short_name"=>'<img src="images/edit.gif" title="Edit" alt="Edit"/>', "&method=delete_short_name"=>'<img src="images/delete.gif" title="Delete" alt="Delete"/>', "&method=make_call"=>'<img src="images/call.gif" title="Call" alt="Call"/>'), array("&method=edit_short_name"=>"Add shortcut"));
}

function make_call()
{
	$called = getparam("number");
	$caller = $_SESSION["username"];

	$command = "click_to_call $caller $called";

	$socket = new SocketConn;
	if($socket->error == "") {
		$socket->command($command);
	}else{
		errormess("Can't make call. Please contact your system administrator.", "no");
		print "<br/>";
	}

	address_book();
}

function edit_short_name($error=NULL)
{
	if($error)
		errornote($error);

	$short_name = new Short_Name;
	$short_name->short_name_id = getparam("short_name_id");
	$short_name->select();

	if($short_name->short_name_id && $short_name->extension_id != $_SESSION["user_id"]) {
		address_book();
		return;
	}

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
	$params = form_params(array("short_name", "number", "name"));
	$params["extension_id"] = $_SESSION["user_id"];
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
	$short_name->select();
	if($short_name->extension_id != $_SESSION["user_id"]) {
		address_book();
		return;
	}
	$res = $short_name->objDelete();
	notice($res[1], "address_book", $res[0]);
}

?>
</div>