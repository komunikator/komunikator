#!/usr/bin/php -q
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
require_once("lib_queries.php");
require_once("libyate.php");

set_time_limit($time_out);

$ourcallid = "auto_attendant/" . uniqid(rand(),1);

$wait_time = 4; //number of seconds that script has to wait after user input in order to see if another digit will be pressed
//$hold_keys = '0';
$count = false; //start to count seconds since a digit has been pressed
$state = "enter";

function format_array($arr)
{
        $str =  str_replace("\n", "", print_r($arr, true));
        $str = str_replace("\t","",$str);
        while(strlen($str) != strlen(str_replace("  "," ",$str)))
        $str = str_replace("  "," ",$str);
        return $str;
}

function debug($mess)
{
    Yate::Debug("auto_attendant.php: ".$mess);
}

/* Perform machine status transitions */
function setState($newstate)
{
    global $ourcallid;
    global $partycallid;
    global $state;
    global $uploaded_prompts;
    global $keys;
    global $wait_time;
    global $caller;
    global $hold_keys;
    global $destination;
    global $called;
    global $ev;

    // are we exiting?
    if ($state == "")
	return;

    debug("setState('$newstate') state: $state");

    $state = $newstate;
    // always obey a return to prompt
    switch ($newstate) {
	case "greeting":
	    // check what prompt to use for this time of day

	    //$query = "select prompts.prompt_id, prompts.file as prompt from time_frames, prompts /*where numeric_day=extract(dow from now()) and cast(start_hour as integer)<=extract(HOUR FROM now()) AND cast(end_hour as integer)>extract(HOUR FROM now()) and time_frames.prompt_id=prompts.prompt_id*/ UNION select prompt_id,  file as prompt from prompts where status='online'";

	    $status = 'offline';
            $query =  "select prompt_id, day, start_hour, end_hour, numeric_day FROM time_frames"; 
	    $res = query_to_array($query);
	    if(count($res))
	    {
		$day_week = date('w');
		$hour 	  = date('H')*1;
		debug("Current week index '$day_week' : hour '$hour'");
		//$day_week = 2;
		//$hour 	  = 12;
		$status = 'offline';

 		foreach ($res as $row)
   		    if ($row["numeric_day"]==$day_week && $row["start_hour"]<=$hour && $hour<$row["end_hour"])	
 			$status = 'online';
	    };

	    $query = "select prompts.prompt_id, prompts.file as prompt from prompts where status='$status'";
	    $res = query_to_array($query);
             debug('greeting:'.format_array($res));
	
	    if(!count($res))
	    {
		debug("Auto-Attendant is not configured!!");
		setState("goodbye");
		return;
	    }
	    $prompt_id = $res[0]["prompt_id"];
	    $prompt =  $res[0]["prompt"];
	    // here we must have ".au"
	    //$prompt = str_ireplace(".mp3", ".slin", $prompt);
	    $query = "SELECT keys.key, destination FROM `keys` WHERE prompt_id=$prompt_id";

	    $keys = query_to_array($query);
              debug('keys:'.format_array($keys));
	    $m = new Yate("chan.attach" );
	    $m->params["source"] = "wave/play/$uploaded_prompts/auto_attendant/$prompt";
              debug('source:'."wave/play/$uploaded_prompts/auto_attendant/$prompt");

	    $m->params["notify"] = $ourcallid;
	    $m->Dispatch();
	    break;
	case "prolong_greeting":
	    $m = new Yate("chan.attach");
	    $m->params["consumer"] = "wave/record/-";
	    $m->params["notify"] = $ourcallid;
	    $m->params["maxlen"] = $wait_time*10000;
	    $m->Dispatch();
	    break;
	case "goodbye":
	    $m = new Yate("chan.attach");
	    $m->params["source"] = "tone/congestion";
	    $m->params["consumer"] = "wave/record/-";
	    $m->params["maxlen"] = 32000;
	    $m->params["notify"] = $ourcallid;
	    $m->Dispatch();
	    break;
	case "call.route":
	    $to_call = null;
	    debug ('$keys = '.$keys);
	    for($i=0; $i<count($keys); $i++) {
		if($keys[$i]["key"] == $hold_keys) {
		    $to_call = $keys[$i]["destination"];
		    //$hold_keys = null;
		    break;
		}
	    }
	    if($hold_keys == '') {
		debug ('$called = '.$called);
		$query = "SELECT (CASE WHEN extension_id IS NOT NULL THEN (SELECT extension FROM extensions WHERE extensions.extension_id=dids.extension_id) ELSE (SELECT extension FROM groups WHERE groups.group_id=dids.group_id) END) as called FROM dids WHERE number='$called'";
		$res = query_to_array($query);
		if(!count($res) || !strlen($res[0]["called"])) {
		    // this should never happen
		    setState("goodbye");
		    return;
		}
		$to_call = $res[0]["called"];
	    } 
	    if (!$to_call)
		$to_call = $hold_keys;
	    $m = new Yate("call.route");
	    $m->params["caller"] = $caller;
	    $m->params["called"] = $to_call;
	    $m->params["id"] = $ourcallid;
	    $m->params["already-auth"] = "yes";
	    $m->params["call_type"] = "from outside";
	    $m->Dispatch();
	    break;
	case "send_call":
	    $m = new Yate("chan.masquerade");
	    $m->params = $ev->params;
	    $m->params["message"] = "call.execute";
	    $m->params["id"] = $partycallid;
	    $m->params["callto"] = $destination;
		
	    $m->Dispatch();
	    break;
     }
}

/* Handle all DTMFs here */
function gotDTMF($text)
{
    global $state;
    global $keys;
    global $destination;
    global $hold_keys;
    global $count;

    debug("gotDTMF('$text') state: $state");

    // Reset default key
    if (!$count) 
	$hold_keys = '';
    $count = true;
    switch ($state) {
	case "greeting":
	case "prolong_greeting":
	    if($text != "#" && $text != "*")
		$hold_keys .= $text;
	    else {
		//i will consider that this are accelerating keys
		setState("call.route");
		break;
	    }
	    return;
    }
}

function gotNotify($reason)
{
    global $state;

    debug("gotNotify('$reason') state: $state");
    if ($reason == "replaced")
	return;

    switch($state) {
	case "greeting":
	    setState("prolong_greeting");
	    break;
	case "prolong_greeting":
	    setState("call.route");
	    break;
	case "goodbye":
	    setState("");
	    break;
    }
}

Yate::Init();

Yate::Debug(true);

/* Install filtered handlers for the wave end and dtmf notify messages */
// chan.dtmf should have a greater priority than that of pbxassist(by default 15)
Yate::Install("chan.dtmf",10,"targetid",$ourcallid);
Yate::Install("chan.notify",100,"targetid",$ourcallid);
Yate::Install("engine.timer",100);

/* The main loop. We pick events and handle them */
while ($state != "") {
    $ev=Yate::GetEvent();
    /* If Yate disconnected us then exit cleanly */
    if ($ev === false)
	break;
    /* No need to handle empty events in this application */
    if ($ev === true)
	continue;
    /* If we reached here we should have a valid object */
    debug(format_array($ev));
    switch ($ev->type) {
	case "incoming":
	    switch ($ev->name) {
		case "call.execute":
		    $partycallid = $ev->GetValue("id");
		    $caller = $ev->GetValue("caller");
		    $called = $ev->GetValue("called");
		    if ($ev->GetValue("debug_on") == "yes") {
			Yate::Output(true);
			Yate::Debug(true);
		    }
		    if ($ev->GetValue("query_on") == "yes") {
			$query_on = true;
		    }
		    $ev->params["targetid"] = $ourcallid;
		    $ev->handled = true;
		    /* We must ACK this message before dispatching a call.answered */
		    $ev->Acknowledge();
		    /* Prevent a warning if trying to ACK this message again */
		    $ev = false;

		    /* Signal we are answering the call */
		    $m = new Yate("call.answered");
		    $m->params["id"] = $ourcallid;
		    $m->params["targetid"] = $partycallid;
		    $m->Dispatch();

		    setState("greeting");
		    break;

		case "chan.notify":
		    gotNotify($ev->GetValue("reason"));
		    $ev->handled = true;
		    break;

		case "chan.dtmf":
		    $text = $ev->GetValue("text");
		    for ($i = 0; $i < strlen($text); $i++)
			gotDTMF($text[$i]);
		    $ev->handled = true;
		    break;

		case "engine.timer":
		    if(!$count)
			break;
		    if($count === true)
			$count = 1;
		    else
			$count++;
		    if($count == $wait_time)
			setState("call.route");
		    break;
	    }
	    /* This is extremely important.
	       We MUST let messages return, handled or not */
	    if ($ev)
		$ev->Acknowledge();
	    break;
	case "answer":
	    //Yate::Debug("PHP Answered: " . $ev->name . " id: " . $ev->id);
	    if ($ev->name == "call.route") {
		$destination = $ev->retval;
		if ($destination)
		    setState("send_call");
		else
		    setState("goodbye");
	    }
	    break;
	case "installed":
	    // Yate::Debug("PHP Installed: " . $ev->name);
	    break;
	case "uninstalled":
	    // Yate::Debug("PHP Uninstalled: " . $ev->name);
	    break;
	default:
	    // Yate::Output("PHP Event: " . $ev->type);
    }
}

Yate::Output("PHP Auto Attendant: bye!");
/* vi: set ts=8 sw=4 sts=4 noet: */
?>