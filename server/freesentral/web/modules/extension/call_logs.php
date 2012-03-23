<?php
/**
 * call_logs.php
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
global $module, $method, $action;

if(!$method)
	$method = $module;

if(substr($method,0,4) == "add_")
	$method = str_replace("add_","edit_",$method);

if($action)
	$call = $method.'_'.$action;
else
	$call = $method;

$call();

function call_logs($error = NULL)
{
	if($error)
		errornote($error);

//	$caller = getparam("caller");
//	$called = getparam("called");
	$number = getparam("number");

	$fields = array(
				//	"caller"=>array("value"=>$caller, "comment"=>"If you insert the caller number the called number will automatically be ".$_SESSION["user"]),
				//	"called"=>array("value"=>$called, "comment"=>"If you insert the called number the caller number will automatically be ".$_SESSION["user"]),
					"number" => array("value"=>$number, "comment"=>"Number you wish to search calls for."),
					"from_date"=>array("display"=>"month_day_year_hour"),
					"to_date"=>array("display"=>"month_day_year_hour_end"),
					"available_columns"=>array("display"=>"available_call_logs_columns", "comment"=>"Check the columns you wish to be displayed")
				);

	start_form();
	addHidden("database");
	editObject(NULL,$fields,"Call Logs","Go",false,false,"widder_edit",NULL,array("left"=>"90px","right"=>"440px"));
	end_form();
}


function available_call_logs_columns()
{
	$columns = array("time"=>true, "address"=>false, "billid"=>false, "caller"=>true, "called"=>true, "duration"=>true, "billtime"=>false, "ringtime"=>false, "status"=>true, "reason"=>false, "ended"=>false);

	foreach($columns as $name=>$display)
	{
		print '<input type="checkbox" name="col_'.$name.'"';
		if(getparam("col_".$name) == "on" || $display == true)
			print ' CHECKED ';
		print '/>&nbsp;'.$name.' ';
	}
}

function call_logs_database()
{
	global $limit,$page;

/*	if(getparam("caller") && getparam("called"))
	{
		call_logs("You can't set both the caller and the called in a single search");
		return;
	}*/

	$number = getparam("number");

	$from = get_date(getparam("from_datehour"),'00',"from_date");
	$to = get_date(getparam("to_datehour"),'59',"to_date");
	$conditions = array("time"=>array(">$from", "<$to"));

	$where = " WHERE call_logs.\"time\">'$from' AND call_logs.\"time\"<'$to'";

	$caller = getparam("caller");
	$called = getparam("called");

	if($number)
	{
		$where .= " AND ((caller='$number' AND called='".$_SESSION["user"]."' AND direction='outgoing') OR (called='$number' AND caller='".$_SESSION["user"]."' AND direction='incoming'))";
	}else{
		$where .= " AND (caller='".$_SESSION["user"]."' AND direction='incoming') OR (called='".$_SESSION["user"]."' AND direction='outgoing')";
	}

	$total = getparam("total");
	if(!$total)
	{
		$call_log = new Call_Log;
		$total = $call_log->fieldSelect('count(*)',NULL, NULL, NULL, NULL, NULL, $where);
	}

	$call_logs = Model::selection("call_log",NULL,"time DESC",$limit,$page,$where);

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
	$formats["function_getdirection:direction"] = "caller, called";

	if(count($call_logs))
		items_on_page();
	pages($total);
	tableOfObjects($call_logs, $formats, "call log");
}

?>
</div>