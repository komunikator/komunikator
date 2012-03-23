<?
require_once("lib/phpmailer.inc.php");

mb_internal_encoding("utf-8");

class myMail extends phpmailer
{
    var $From = "multifon@digt.ru";
    var $FromName = "multifon";
    var $CharSet = "utf-8";
}

function format_array($arr)
{
        $str =  str_replace("\n", "", print_r($arr, true));
        $str = str_replace("\t","",$str);
        while(strlen($str) != strlen(str_replace("  "," ",$str)))
        $str = str_replace("  "," ",$str);
        return $str;
}

function send_mail() 
{

    global $ev;
    
    $ftime = strftime('%c', $ev->GetValue('time'));
    $username = $ev->GetValue('username');
    $caller = ($username)? $username : $ev->GetValue('caller');
    $called = $ev->GetValue('called');

    // Do not log internal calls
    if (strlen($caller) <= 3 && strlen($called) <= 3)
	return;

    $duration = $ev->GetValue('duration');
    $status = $ev->GetValue('status') . ' ' . $ev->GetValue('reason');
    $type = (strlen($caller) <= 3)? 'Исходящий' : 'Входящий';

    $text = <<<EOD
    Абонент: $caller
    Кому: $called
    Дата: $ftime
    Длительность: $duration
    Состояние: $status
    Тип: $type
EOD;

    $mail = new myMail;
    $mail->Body = $text;

    if (strpos($ev->GetValue('chan'), 'fax') === false)
    {
	$mail->AddAddress('calls@digt.ru');
        $subject = 'Звонок ';
    }
    else
    {
	$mail->AddAddress('fax@digt.ru');
	$subject = 'Факс ';
	$filename = $ev->GetValue('address');
	if (is_file($filename))
	{
	    $mail->AddAttachment($filename);
	    //unlink($filename);
	}
    }
    $subject .= $ftime . ' от ' . $caller;

    $mail->Subject = mb_encode_mimeheader($subject, $mail->CharSet, 'B');
    $mail->Send();
}

function send_voicemail($address, $filename, $caller, $ftime = false)
{
    if (!$address)
	return;
	
    if (!$ftime) 
	$ftime = strftime("%c", time());
    
    $text =<<<EOD
    Абонент: $caller
    Дата: $ftime
EOD;

    $mail = new myMail;
    $mail->Body = $text;
    $mail->AddAddress($address);
    $subject = "Неотвеченный звонок " . $ftime . " от " . $caller;
    $mail->Subject = mb_encode_mimeheader($subject, $mail->CharSet, 'B');
    if (is_file($filename))
	$mail->AddAttachment($filename);
    $mail->Send();
}

?>