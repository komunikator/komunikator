#!/usr/bin/php -q
<?php
/**
 * voicemaildb.php
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
/* Simple voicemail manipulation for the Yate PHP interface
   To use add in regexroute.conf

   ^NNN$=external/nodata/voicemail.php

   where NNN is the number you want to assign to handle voicemail
*/
require_once("libyate.php");
require_once("libvoicemail.php");
require_once("lib_queries.php");
set_time_limit(600);

/* Always the first action to do */
Yate::Init();

/* Uncomment next line to get debugging messages */
//Yate::Debug(true);

$ourcallid = "voicemaildb/" . uniqid(rand(),1);
$partycallid = "";
$state = "call";
$dir = "";
$mailbox = "";
$collect_user = "";
$collect_pass = "";
$files = array();
$current = 0;

$vm_func_for_dir = "getCustomVoicemailDir";

function debug($mess)
{
    Yate::Debug("voicemaildb.php: ".$mess);
}

/* Ask the user to enter number */
function promptUser()
{
    global $collect_user;
    global $vm_base;
    $collect_user = "";
    $m = new Yate("chan.attach");
    $m->params["source"] = "wave/play/$vm_base/usernumber.au";
    $m->Dispatch();
}

/* Ask for the password */
function promptPass()
{
    global $collect_pass;
    global $vm_base;
    $collect_pass = "";
    $m = new Yate("chan.attach");
    $m->params["source"] = "wave/play/$vm_base/password.au";
    $m->Dispatch();
}

/* Perform machine status transitions */
function setState($newstate)
{
    global $ourcallid;
    global $partycallid;
    global $state;
    global $mailbox;
    global $vm_base;
    global $dir;
    global $files;
    global $current;

    // are we exiting?
    if ($state == "")
	return;

    debug("setState('$newstate') state: $state");

    // always obey a return to prompt
    switch ($newstate) {
	case "prompt":
	    $state = $newstate;
	    $m = new Yate("chan.attach");
	    $m->params["source"] = "wave/play/$vm_base/menu.au";
	    $m->Dispatch();
	    $m = new Yate("chan.attach");
	    $m->params["consumer"] = "wave/record/-";
	    $m->params["maxlen"] = 320000;
	    $m->params["notify"] = $ourcallid;
	    $m->Dispatch();
	    return;
	case "listen":
	    $state = $newstate;
	    if (vmSetMessageRead($mailbox,$files[$current])) {
		$mp3file = str_replace(".slin",".mp3",$files[$current]);
		if(is_file("$dir/n".$mp3file))
		    rename("$dir/n".$mp3file, "$dir/$mp3file"); 
		$m = new Yate("user.update");
		$m->id = "";
		$m->params["user"] = $mailbox;
		$m->Dispatch();
	    }
	    $f = $dir . "/" . $files[$current];
	    $m = new Yate("chan.attach");
	    if (is_file("$f"))
		$m->params["source"] = "wave/play/$f";
	    else
		$m->params["source"] = "wave/play/$vm_base/deleted.au";
	    $m->params["consumer"] = "wave/record/-";
	    $m->params["maxlen"] = 100000;
	    $m->params["notify"] = $ourcallid;
	    $m->Dispatch();
	    return;
    }

    if ($newstate == $state)
	return;

    switch ($newstate) {
	case "user":
	    promptUser();
	    $m = new Yate("chan.attach");
	    $m->params["consumer"] = "wave/record/-";
	    $m->params["maxlen"] = 160000;
	    $m->params["notify"] = $ourcallid;
	    $m->Dispatch();
	    break;
	case "pass":
	    promptPass();
	    $m = new Yate("chan.attach");
	    $m->params["consumer"] = "wave/record/-";
	    $m->params["maxlen"] = 160000;
	    $m->params["notify"] = $ourcallid;
	    $m->Dispatch();
	    break;
	case "record":
	    $m = new Yate("chan.attach");
	    $m->params["source"] = "wave/play/-";
	    $m->params["consumer"] = "wave/record/$dir/greeting.au";
	    $m->params["maxlen"] = 80000;
	    $m->params["notify"] = $ourcallid;
	    $m->Dispatch();
	    break;
	case "play":
	    $m = new Yate("chan.attach");
	    if (is_file("$dir/greeting.au"))
		$m->params["source"] = "wave/play/$dir/greeting.au";
	    else
		$m->params["source"] = "wave/play/$vm_base/nogreeting.au";
	    $m->params["consumer"] = "wave/record/-";
	    $m->params["maxlen"] = 100000;
	    $m->params["notify"] = $ourcallid;
	    $m->Dispatch();
	    break;
	case "goodbye":
	    $mailbox = "";
	    $m = new Yate("chan.attach");
	    $m->params["source"] = "tone/congestion";
	    $m->params["consumer"] = "wave/record/-";
	    $m->params["maxlen"] = 32000;
	    $m->params["notify"] = $ourcallid;
	    $m->Dispatch();
	    break;
    }
    $state = $newstate;
}

/* Check if the maibox exists, create if not, scan voicemail files */
function initUser()
{
    global $dir;
    global $mailbox;
    global $files;
    vmInitMessageDir($mailbox);
    vmGetMessageFiles($mailbox,$files);
	$all_files = $files; // this array contains both .mp3 and .slin files
	$files = array();
	for($i=0; $i<count($all_files); $i++)
	if(substr($all_files[$i],-5) == ".slin")
	    $files[] = $all_files[$i];
    $dir = vmGetVoicemailDir($mailbox);
    debug("found " . count($files) . " file entries for mailbox $mailbox");
    setState("prompt");
}

/* Transition to password entering state if user is not empty else exit */
function checkUser()
{
    global $collect_user;
    if ($collect_user == "")
	setState("goodbye");
    else
	setState("pass");
}

/* Transition to authentication state if password is not empty else exit */
function checkPass()
{
    global $collect_user;
    global $collect_pass;
    if ($collect_pass == "")
	setState("goodbye");
    else {
	setState("auth");
	$m = new Yate("user.auth");
	$m->params["username"] = $collect_user;
	$m->Dispatch();
    }
}

/* Check the system known password agains the user enterd one */
function checkAuth($pass)
{
    global $collect_user;
    global $collect_pass;
    global $mailbox;
//    Yate::Debug("checking passwd if '$collect_pass' == '$pass'");
    if ($collect_pass == $pass) {
	$mailbox = $collect_user;
	initUser();
    } else
	setState("goodbye");
    $collect_pass = "";
}

/* Handle EOF of wave files */
function gotNotify($reason)
{
    global $ourcallid;
    global $partycallid;
    global $state;

    debug("gotNotify('$reason') state: $state");
    if ($reason == "replaced")
	return;

    switch ($state) {
	case "goodbye":
	    setState("");
	    break;
	case "record":
	case "play":
	case "listen":
	    setState("prompt");
	    break;
	case "user":
	    checkUser();
	    break;
	case "pass":
	    checkPass();
	    break;
	default:
	    setState("goodbye");
	    break;
    }
}

/* Play the n-th voicemail file */
function listenTo($n)
{
    global $files;
    global $current;

    if (($n < 0) || ($n >= count($files)))
	return;
    $current = $n;
    setState("listen");
}

/* Handle DTMFs after successfully logging in */
function navigate($text)
{
    global $state;
    global $current;

    switch ($text) {
	case "0":
	    listenTo(0);
	    break;
	case "7":
	    listenTo($current-1);
	    break;
	case "8":
	    listenTo($current);
	    break;
	case "9":
	    listenTo($current+1);
	    break;
	case "1":
	    setState("record");
	    break;
	case "2":
	    setState("play");
	    break;
	case "3":
	    setState("");
	    break;
	case "*":
	    setState("prompt");
	    break;
    }
}

/* Handle all DTMFs here */
function gotDTMF($text)
{
    global $state;
    global $mailbox;
    global $collect_user;
    global $collect_pass;

    debug("gotDTMF('$text') state: $state");

    switch ($state) {
	case "user":
	    if ($text == "*") {
		promptUser();
		return;
	    }
	    if ($text == "#")
		checkUser();
	    else
		$collect_user .= $text;
	    return;
	case "pass":
	    if ($text == "*") {
		promptPass();
		return;
	    }
	    if ($text == "#")
		checkPass();
	    else
		$collect_pass .= $text;
	    return;
	case "record":
	    setState("prompt");
	    return;
    }
    if ($mailbox == "")
	return;

    navigate($text);
}

/* Install filtered handlers for the wave end and dtmf notify messages */
Yate::Install("chan.dtmf",10,"targetid",$ourcallid);
Yate::Install("chan.notify",100,"targetid",$ourcallid);

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
    switch ($ev->type) {
	case "incoming":
	    switch ($ev->name) {
		case "call.execute":
		    $mailbox = $ev->GetValue("user");
		    $partycallid = $ev->GetValue("id");
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

		    /* If the user is unknown we need to identify and authenticate */
		    if ($mailbox == "")
			setState("user");
		    else
			initUser();
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
	    }
	    /* This is extremely important.
	       We MUST let messages return, handled or not */
	    if ($ev)
		$ev->Acknowledge();
	    break;
	case "answer":
	    // Yate::Debug("PHP Answered: " . $ev->name . " id: " . $ev->id);
	    if ($ev->name == "user.auth")
		checkAuth($ev->retval);
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

Yate::Output("PHP voicemaildb : bye!");
/* vi: set ts=8 sw=4 sts=4 noet: */
?>
