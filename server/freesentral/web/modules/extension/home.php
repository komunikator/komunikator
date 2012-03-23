<?php
/**
 * home.php
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

global $module, $method, $path, $action, $page, $target_path;

if(!$method)
	$method = $module;

if(substr($method,0,4) == "add_")
	$method = str_replace("add_","edit_",$method);

if($action)
	$call = $method.'_'.$action;
else
	$call = $method;

if($call != "home")
	print '<div class="content wide">';
$call();
if($call != "home")
	print '</div>';

function home()
{
	print '<div class="title wide">:: Voicemail ::</div>';
	print '<div class="content wide">';
	voicemail(5);
	print '</div>';
	print '<div class="title wide">:: Activity ::</div>';
	print '<div class="content wide">';
	activity();
	print '</div>';
}

function activity()
{
	$call_log = new Call_Log;
	$conditions = array();

	/*$conditions[0] = array("caller"=>$_SESSION["user"], "called"=>$_SESSION["user"]);
	$conditions["direction"] = "outgoing";

	$call_logs = Model::selection("call_log",$conditions,"time DESC",5);*/

	$where = " WHERE (caller='".$_SESSION["user"]."' AND direction='incoming') OR (called='".$_SESSION["user"]."' AND direction='outgoing')";

	$total = getparam("total");
	if(!$total)
	{
		$call_log = new Call_Log;
		$total = $call_log->fieldSelect('count(*)',NULL, NULL, NULL, NULL, NULL, $where);
	}

	$call_logs = Model::selection("call_log",NULL,"time DESC",5,NULL,$where);


	$columns = array("time"=>true, "address"=>false, "billid"=>false, "caller"=>true, "called"=>true, "duration"=>true, "billtime"=>false, "ringtime"=>false, "status"=>true, "reason"=>false, "ended"=>false);

	$formats = array();
	foreach($columns as $key=>$display)
	{
		if(!(getparam("col_".$key)=="on" || $display == true))
			continue;
		if($key != "time")
			array_push($formats, $key);
		else{
			$formats["function_select_date:date"] = "time";
			$formats["function_select_time:time"] = "time"; 
		}
	}
	$formats["function_getdirection:direction"] = "caller,called";

	tableOfObjects($call_logs, $formats, "call log");
}

?>