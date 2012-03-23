<?php
/**
 * auto_attendant.php
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
global $module, $method, $path, $action, $page, $target_path, $iframe;

require_once("lib/lib_auto_attendant.php");

if(!getparam("method") || getparam("method") == "auto_attendant")
	$method = "wizard";

if(substr($method,0,4) == "add_")
	$method = str_replace("add_","edit_",$method);

if($action)
	$call = $method.'_'.$action;
else
	$call = $method;

$explanation = array(
	"default" => "Auto Attendant: Calls within the PBX are answered and directed to their desired destination by the auto attendant system.<br/><br/>The first step for setting it is to <font class=\"bold\">upload the two prompts</font> for online/offline mode. The prompts may vary depending on your company's business hours.<br/><br/>Then you need to <font class=\"bold\">define the keys</font> that can be pressed during each mode. Please note that the keys must match the  uploaded prompts exactly.Example: if your online prompt says \"Press 1 for Sales\", then you must select type: online, key: 1, and insert group: Sales (you must have Sales defined in the Groups section).<br/><br/>When <font class=\"bold\">scheduling</font> the Auto Attendant you set the time frames for each day during which ATT will be online. For periods not included in this time frames the offline mode will be used.<br/><br/>The last thing to get it working is to <font class=\"bold\">associate a DID</font> to it and set the default destination that will be reached when no digit was pressed.<br/>Note!! The system has a single Auto Attendant but depending on the DID, the default destination may be different.",
	"keys" => "If your online prompt says: Press 1 for Sales, then you must select type: online, key: 1, and insert group: Sales (you must have Sales defined in the Groups section). Same for offline state. <br/><br/>If you want to send a call directly to an extension or another number, you should insert the number in the 'Number(x)' field from Define Keys section.", 
	"prompts" => "The Auto Attendant has two states: online and offline. Each of these states has its own prompt.", 
	"scheduling" => "When <font class=\"bold\">scheduling</font> the Auto Attendant you set the time frames for each day during which ATT will be online. For periods not included in this time frames the offline mode will be used.",
	"DID" => "Associate DIDs to the Auto Attendant. The DID is the number that one needs to call to reach it.<br/><br/>Note!! The system has a single Auto Attendant but depending on the DID, the default destination may be different."
);

explanations("images/auto-attendant.png", "", $explanation);

print '<div class="content">';
$call();
print '</div>';

function auto_attendant()
{
	wizard();
}

function wizard($error = NULL)
{
	global $method;

	if($error)
		errornote($error);

	prompts(true);

	keys(true);

	scheduling(NULL,true);

	$method = "wizard";
	activate();
}

?>