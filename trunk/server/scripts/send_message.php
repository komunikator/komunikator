#!/usr/bin/php -q
<?
require_once('libyate.php');
require_once('lib_smtp.inc.php');

function debug($msg) {
    Yate::Debug('send_mail.php: ' . $msg);
}

// Always the first action to do 
Yate::Init();

Yate::Install('call.cdr', 120);
// Ask Yate to restart this script if it dies unexpectedly
Yate::SetLocal('restart',true);

//Yate::Debug(true);
setMultifonOpt(2); // Send calls to both SIP & cell phone

// The main loop. We pick events and handle them
for (;;) {
    $ev=Yate::GetEvent();
    if ($ev === false)
	break;
    if ($ev === true)
	continue;

    // We are sure it's the timer message
    if ($ev->type == 'incoming')
    {
        switch ($ev->name) {
	case 'call.cdr':
		//debug(format_array($ev));
		$ev->Acknowledge();
		if ($ev->GetValue('direction') == 'outgoing' &&
		    $ev->GetValue('operation') == 'finalize') 
		{
		    send_mail();
		}
	    break;
	}
    }
}

Yate::Debug('PHP: bye!');


?>