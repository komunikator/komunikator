#!/usr/bin/php -q
<?php
/*
 * Callback system dialer - call leg A script
 */
require_once("libyate.php");

$ourcallid = "ctc-dialer/" . uniqid(rand(),1);
$billid = rand();

function secondCall($route,$ev)
{
    global $partycallid;
    global $real_caller;
    global $real_called;
    global $billid;

    $m = new Yate("chan.masquerade");
    $m->params = $ev->params;
    $m->SetParam("message", "call.execute");
    $m->SetParam("callto", $route);
    $m->SetParam("caller", $real_caller);
    $m->SetParam("called", $real_called);
    $m->SetParam("id", $partycallid);
    $m->SetParam("billid", $billid);
    $m->Dispatch();
}

/* Always the first action to do */
Yate::Init();

Yate::SetLocal("id",$ourcallid);
Yate::SetLocal("disconnected","true");

Yate::Install("call.answered",50,"targetid",$ourcallid);

$exit = false;

/* The main loop. We pick events and handle them */
for (;;) {
    if($exit)
	break;
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
//	    Yate::Debug("PHP Incoming: " . $ev->name);
	    switch ($ev->name) {
		case "call.execute":
		$m = new Yate("call.execute");
		$m->params = $ev->params;
		$real_caller = $ev->GetValue("real_caller");
		$real_called = $ev->GetValue("real_called");
		$m->params["callto"] = $ev->GetValue("direct");
		$m->SetParam("id", $ourcallid);
		$m->SetParam("billid", $billid);
		$m->Dispatch();
		$ev->handled=true;
		break;
	    case "call.answered":
		$partycallid = $ev->GetValue("id");
		$m = new Yate("call.route");
		$m->params["true_party"] = $partycallid;
		$m->params["caller"] = $real_caller;
		$m->params["called"] = $real_called;
		$m->params["already-auth"] = "yes";
		$m->Dispatch();
		break;
	    }
	    /* This is extremely important.
	    We MUST let messages return, handled or not */
	    if ($ev)
		$ev->Acknowledge();
	    break;
	case "answer":
	    if($ev->name == "call.route") {
		$route = $ev->retval;
		if($ev->retval)
		    secondCall($route,$ev);
		else
		    $exit = true;
	    }
	    break;
//	default:
//	    Yate::Debug("PHP Event: " . $ev->type);
    }
}

Yate::Output("PHP: bye!");

/* vi: set ts=8 sw=4 sts=4 noet: */
?>