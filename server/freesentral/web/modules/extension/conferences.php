<?php
/**
 * conferences.php
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
global $module,$method,$action;

if(!$method)
	$method = strtolower($module);

if(substr($method,0,4) == "add_")
	$method = str_replace("add_","edit_",$method);

if($method == "edit_admin")
	$method = "edit_user";

if($method == "manage")
	$method = "home";

if($action)
	$call = $method.'_'.$action;
else
	$call = $method;

$call = strtolower($call);

$call();

function conferences()
{
	$conferences = Model::selection("did", array("destination"=>"__LIKEconf/"), "did");
	$fields = array("function_get_conf_from_did:conference"=>"did", "number", "function_conference_participants:participants"=>"number");
	tableOfObjects($conferences, $fields, "conference",array());
}

?>
</div>