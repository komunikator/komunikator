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
require_once("socketconn.php");

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

if($call != "home")
	print '<div class="content wide">';
$call();
if($call != "home")
	print '</div>';

function home()
{
	print '<table><tr><td class="topvalign">';
	print '<div class="homecontent">';
	//$actions = array("&module=auto_attendant&method=wizard"=>array());

	print '<table class="hometable" cellspacing="0" cellpadding="0">';
		print '<tr>';
			print '<td>
							<table class="home_opt" cellspacing="0" cellpadding="0" onClick="location.href=\'main.php?module=auto_attendant&method=wizard\'">
								<tr onMouseover="this.bgColor=\'#dcf0f2\'" onMouseout="this.bgColor=\'#EEEEEE\'">';
									print '<td class="hometable">';
										print '<img src="images/auto-attendant.png"/>';
									print '</td>';
									print '<td class="hometable description">';
										print 'Auto&nbsp;Attendant';
									print '</td>';
								print '</tr>
							</table>
					</td>';
					print '<td>
								<table class="home_opt" cellspacing="0" cellpadding="0" onClick="location.href=\'main.php?module=extensions&method=add_extension\'">
									<tr onMouseover="this.bgColor=\'#dcf0f2\'" onMouseout="this.bgColor=\'#EEEEEE\'">';
										print '<td class="hometable">';
											print '<img src="images/extension.png"/>';
										print '</td>';
										print '<td class="hometable description">';
												print 'Add&nbsp;Extension';
										print '</td>';
									print '</tr>
								</table>
						</td>';
		print '</tr>';

		print '<tr>';
			print '<td>
							<table class="home_opt" cellspacing="0" cellpadding="0" onClick="location.href=\'main.php?module=outbound&method=add_gateway\'">
								<tr onMouseover="this.bgColor=\'#dcf0f2\'" onMouseout="this.bgColor=\'#EEEEEE\'">';
									print '<td class="hometable">';
										print '<img src="images/gateways.png"/>';
									print '</td>';
									print '<td class="hometable description">';
										print 'Add&nbsp;Gateway';
									print '</td>';
								print '</tr>
							</table>
					</td>';
			print '<td>
							<table class="home_opt" cellspacing="0" cellpadding="0" onClick="location.href=\'main.php?module=address_book&&method=add_short_name\'">
								<tr onMouseover="this.bgColor=\'#dcf0f2\'" onMouseout="this.bgColor=\'#EEEEEE\'">';
									print '<td class="hometable">';
										print '<img src="images/address_book.png"/>';
									print '</td>';
									print '<td class="hometable description">';
										print 'New&nbsp;Address&nbsp;Book&nbsp;Entry';
									print '</td>';
								print '</tr>
							</table>
					</td>';
		print '</tr>';

		print '<tr>';
			print '<td>
							<table class="home_opt" cellspacing="0" cellpadding="0" onClick="location.href=\'main.php?module=outbound&method=add_dial_plan\'">
								<tr onMouseover="this.bgColor=\'#dcf0f2\'" onMouseout="this.bgColor=\'#EEEEEE\'">';
									print '<td class="hometable">';
										print '<img src="images/dial_plan.png"/>';
									print '</td>';
									print '<td class="hometable description">';
										print 'Add&nbsp;Dial&nbsp;Plan';
									print '</td>';
								print '</tr>
							</table>
				</td>';
			print '<td>
							<table class="home_opt" cellspacing="0" cellpadding="0" onClick="location.href=\'main.php?module=dids&method=add_did\'">
								<tr onMouseover="this.bgColor=\'#dcf0f2\'" onMouseout="this.bgColor=\'#EEEEEE\'">';
									print '<td class="hometable">';
										print '<img src="images/dids.png"/>';
									print '</td>';
									print '<td class="hometable description">';
										print 'Add&nbsp;DID';
									print '</td>';
								print '</tr>
							</table>
				</td>';
		print '</tr>';
	print '</table>';
	print '</div>';
	print '</td><td class="topvalign">';
	print '<div class="copac copachome">';
	//$status = exec("/etc/init.d/yate status");

	$sock = new SocketConn;
	if($sock->socket) {
		$uptime = $sock->command("uptime");
		$status = $uptime;
		$pos1 = strpos($uptime,"(");
		$pos2 = strpos($uptime,")");
		$time = substr($uptime, $pos1+1, $pos2-$pos1-1);
		$time = query_to_array(Database::query("SELECT $time*'1 sec'::interval as interval"));
		$time = $time[0]["interval"];
		$s_time = $time;
		$time = explode(":",$time);
		$days = floor($time[0]/24);
		$hours = $time[0]%24;

		$text_status = "uptime: ";
		if (!$days)
			$text_status.= $s_time;
		else
			$text_status.= $days.' days '.$hours.":".$time[1].":".$time[2]." ";
		$err = "";
		/*$sock->write("status register.php");
		$sock->read();
		$status_fs = $sock->read();
		$status_fs = explode(";",$status_fs);

		if (count($status_fs)>=1)
			$international = str_replace("international=","",$status_fs[1]);
		else
			$international = "off";*/
		$sock->close();
	}else{
		$status = ": Can't connect to yate<br/>".$sock->error;
		$err = "error_";
	}
	print '<div class="titlu">SYSTEM STATUS</div>';
	print '<div class="'.$err.'systemstatus"> '.'
			<div style="float:right;"> Today, '.date('h:i a').'
			</div>Yate '.$text_status;
	print '</div>';

	$setting = Model::selection("setting", array("param"=>array("__sql_relation"=>"OR", "international_calls", "international_calls_live")), "param");

	$setting_international = (isset($setting[0]) && $setting[0]->param=="international_calls" && $setting[0]->value=="no") ? "off" : "on";
	$international = (isset($setting[1]) && $setting[1]->param=="international_calls_live" && $setting[1]->value=="no") ? "off" : "on";
	$when = (isset($setting[1])) ? $setting[1]->description : "";
	$when = explode(".",$when);
	$when = $when[0].$when[2];
	if ($international!="on" && $setting_international!=$international) {
		$link_enable = "&nbsp;&nbsp;<a class=\"llink\" href=\"main.php?module=home&method=enable_international\">Enable</a>";
		print '<div class="error_systemstatus borded">
				International calls: off'.$link_enable;
		print '<br/><font style="font-size:11px;">'.$when.'</font></div>';
	}
	$admins = Model::selection("user", array("login_attempts"=>">=3"), "login_attempts DESC");
	for($i=0; $i<count($admins); $i++) {
		print '<div class="'.$err.'systemstatus borded"> '.'
				Admin '.$admins[$i]->username.': '.$admins[$i]->login_attempts.' failed login attempts';
		print '</div>';
	}
	$extensions = Model::selection("extension", array("login_attempts"=>">=3"), "login_attempts DESC");
	for($i=0; $i<count($extensions); $i++) {
		print '<div class="error_systemstatus borded"> '.'
				Extension '.$extensions[$i]->extension.': '.$extensions[$i]->login_attempts.' failed login attempts';
		print '</div>';
	}
	if(!count($extensions) && !count($admins))
		print '<br/><br/>';
	print '</td></tr></table>';
}

function enable_international()
{
	$sock = new SocketConn;
	if($sock->socket) {
		$sock->write("international on");
		// sleep 1 second, if closing too fast command won't be written
		sleep(1);
		$sock->close();
	} else {
		errormess("Can't connect to yate: ".$sock->error, "no");
	}

	home();
}

/*
function home()
{
	print '<div class="title wide">:: Active Calls ::</div>';
	print '<div class="content wide">';
	active_calls(5);
	print '</div>';
	print '<div class="title wide">:: Logs ::</div>';
	print '<div class="content wide">';
	logs(5);
	print '</div>';
}*/

function logs($lim = NULL)
{
	global $limit,$page;

	$use_limit = ($lim) ? $lim : $limit;

	if(!$lim)
	{
		$total = getparam("total");
		$actionlog = new ActionLog;
		$total = $actionlog->fieldSelect("count(*)");
		items_on_page();
		pages($total);
	}

	$logs = Model::selection("actionlog",NULL,"date DESC",$use_limit,$page);
	tableOfObjects($logs,array("function_select_date:date"=>"date", "function_select_time:time"=>"date", "ip", "performer", "log"),"log");
}

function active_calls($lim = NULL)
{
	global $limit,$page;

	$use_limit = ($lim) ? $lim : $limit;
	$total = getparam("total");
	$call_log = new Call_Log;
	$total = $call_log->fieldSelect("count(*)",array("ended"=>false, 'status' => '!= unknown'));
	if(!$lim)
	{
		items_on_page();
		pages($total);
	}
	$columns = array("time"=>true, "chan"=>false, "address"=>false, "direction"=>false, "billid"=>false, "caller"=>true, "called"=>true, "duration"=>true, "billtime"=>false, "ringtime"=>false, "status"=>true, "reason"=>false, "ended"=>false);

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
	$call_logs = Model::selection("call_log",array("ended"=>false, 'status' => "!=unknown"), "time DESC", $use_limit, $page);

	if(!$total)
		$total = count($call_logs);
	if($total)
		if($total != 1)
			print "There are ".$total." active calls in the system.<br/><br/>";
		else
			print "There is 1 active call in the system.<br/><br/>";

	tableOfObjects($call_logs,$formats, "active call");
}

function call_logs($error = NULL)
{
	if($error)
		errornote($error);

	$caller = getparam("caller");
	$called = getparam("called");

	$direction = array("incoming", "outgoing", "both");
	$fields = array(
					"caller"=>array("value"=>$caller),
					"called"=>array("value"=>$called),
					"from_date"=>array("display"=>"month_day_year_hour"),
					"to_date"=>array("display"=>"month_day_year_hour_end"),
					"available_columns"=>array("display"=>"available_call_logs_columns", "comment"=>"Check the columns you wish to be displayed"),
					"direction"=>array($direction, "display"=>"select", "comment"=>"There are two call legs for each call:incoming and outgoing(both have the same billid). If you don't select anything, incoming calls will be displayed."),
				);

	start_form();
	addHidden("database");
	editObject(NULL,$fields,"Call Logs","Go",false,false,"widder_edit",NULL,array("left"=>"90px","right"=>"440px"));
	end_form();
}

function available_call_logs_columns()
{
	$columns = array("time"=>true, "chan"=>false, "address"=>false, "direction"=>false, "billid"=>false, "caller"=>true, "called"=>true, "duration"=>true, "billtime"=>false, "ringtime"=>false, "status"=>true, "reason"=>false, "ended"=>false);

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

	$from = get_date(getparam("from_datehour"),'00',"from_date");
	$to = get_date(getparam("to_datehour"),'59',"to_date");
	$conditions = array("time"=>array(">$from", "<$to"));

	$direction = getparam("direction");
	if($direction == "incoming" || $direction == "outgoing")
		$conditions["direction"] = $direction;
	elseif($direction == "Not selected" || $direction == "")
		$conditions["direction"] = "incoming";

	$caller = getparam("caller");
	if($caller)
		$conditions["caller"] = $caller;
	$called = getparam("called");
	if($called)
		$conditions["called"] = $called;

	$total = getparam("total");
	if(!$total)
	{
		$call_log = new Call_Log;
		$total = $call_log->fieldSelect('count(*)',$conditions);
	}

	$call_logs = Model::selection("call_log",$conditions,"time DESC",$limit,$page);

	$columns = array("time"=>true, "chan"=>false, "address"=>false, "direction"=>false, "billid"=>false, "caller"=>true, "called"=>true, "duration"=>true, "billtime"=>false, "ringtime"=>false, "status"=>true, "reason"=>false, "ended"=>false);

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

	if(count($call_logs))
		items_on_page();
	pages($total);
	tableOfObjects($call_logs, $formats, "call log");
}
?>