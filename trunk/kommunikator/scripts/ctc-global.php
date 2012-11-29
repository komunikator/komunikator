#!/usr/bin/php -q
<?php
require_once("libyate.php");

// Initiate a call once we know the target
function callInitiate($target,$ev)
{
    Yate::Debug("Initiating dialout call to '$target'");
    $m = new Yate("call.execute");
    $m->params = $ev->params;
    $m->id = "";
    $m->SetParam("callto","external/nodata/ctc-dialer.php");
    $m->SetParam("direct",$target);
    $m->SetParam("caller",$ev->GetValue("real_called"));
    $m->SetParam("callername",$ev->GetValue("callername"));
    $m->SetParam("called",$ev->GetValue("real_caller"));
    $m->SetParam("cdrtrack","false");
    $m->Dispatch();
}

// Routing failed, the number may be invalid
function routeFailure($error,$ev)
{
    $number = $ev->GetValue("called");
    Yate::Output("Failed routing in ctc-global to '$number' with error '$error'");
}
// Always the first action to do 
Yate::Init();
// Only install a handler for the engine.command message
Yate::Install("engine.command");
// Ask Yate to restart this script if it dies unexpectedly
Yate::SetLocal("restart",true);

// The main loop. We pick events and handle them
for (;;) {
    $ev=Yate::GetEvent();
    if ($ev === false)
	break;
    if ($ev === true)
	continue;
    switch ($ev->type) {
	case "incoming":
	    // We are sure it's the timer message
	    $ev->Acknowledge();
	    if($ev->name == "engine.command") {
		$line = $ev->GetValue("line");
		if(substr($line,0,14) == "click_to_call ") {
		    $cmd = substr($line,14,strlen($line));
		    $cmd = explode(" ",$cmd);
		    $caller = $cmd[0];
		    $called = $cmd[1];
		    $m = new Yate("call.route");
		    $m->params["caller"] = "ctc"; // $caller;
		    $m->params["called"] = $caller; //$called;
		    $m->params["real_caller"] = $caller;
		    $m->params["real_called"] = $called;
		    $m->params["already-auth"] = "yes";
		    $m->Dispatch();
		}
	    }
	    break;
	case "answer":
	    // Use the return of the routing message
	    if ($ev->name == "call.route") {
		if ($ev->handled && ($ev->retval != "") && ($ev->retval != "-") && ($ev->retval != "error"))
		    callInitiate($ev->retval,$ev);
		else
		    routeFailure($ev->GetValue("error"),$ev);
	    }
	    break;
	default:
	    Yate::Debug("PHP Event: " . $ev->type);
    }
}

Yate::Debug("PHP: bye!");

/* vi: set ts=8 sw=4 sts=4 noet: */
?>