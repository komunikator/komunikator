<?
require_once("config.php");
require_once("lib/phpmailer.inc.php");

mb_internal_encoding("utf-8");
define('TIME_FMT', '%H.%M.%S %d.%m.%Y');


class myMail extends phpmailer {
    var $From = "multifon@digt.ru";
    var $FromName = "multifon";
    var $CharSet = "utf-8";
}

function format_array($arr) {
    $str =  str_replace("\n", "", print_r($arr, true));
    $str = str_replace("\t","",$str);
    while(strlen($str) != strlen(str_replace("  "," ",$str)))
        $str = str_replace("  "," ",$str);
    return $str;
}

function format_msg($text,$params) {
    return str_replace('\n',"\n",preg_replace("/\<([^\>]+)\>/e", 'isset($params["$1"])?$params["$1"]:"<$1>";', $text));
}

function send_mail($text=null,$subject=null,$is_fax=null,$filename=null,$from=null,$to=null,$fromname=null) {
    
    global $fax_email, $calls_email;
    $fax_email=$to;
    $calls_email=$to;
    
    if (!$calls_email) return;	
    if (!$text) return; 
    if (!$subject) return;
    if ($is_fax) if (!$fax_email) return;	
            
    $mail = new myMail;
    $mail->Body = $text;
    $mail->FromName = $fromname;
    $mail->From = $from;
    
    if (!$is_fax) {
        $mail->AddAddress($calls_email);
    }
    else {
        $mail->AddAddress($fax_email);
        if (is_file($filename)) {
            $mail->AddAttachment($filename);
        //unlink($filename);
        } 
    }
    
    $mail->Subject = mb_encode_mimeheader($subject, $mail->CharSet, 'B');
    Yate::Debug("send_mail: '$text'");
   $mail->Send();
}

function send_voicemail($address, $filename, $caller, $ftime = false) {
    if (!$address)
        return;
        
    if (!$ftime) 
        $ftime = strftime(TIME_FMT, time());
        
    $text =<<<EOD
    Абонент: $caller
    Дата: $ftime
EOD;
    
    $mail = new myMail;
    $mail->Body = $text;
    $mail->AddAddress($address);
    $subject = 'Звонок не принят от '.$caller.' '.$ftime;
    $mail->Subject = mb_encode_mimeheader($subject, $mail->CharSet, 'B');
    if (is_file($filename))
        $mail->AddAttachment($filename);
    $mail->Send();
}

function setMultifonOpt($opt,$gateway) {
// Valid options are 0,1,2
    if ($opt < 0 || $opt > 2) 
        return;
    if (!$gateway) return;
    $query = "select username,password from gateways where gateway = '$gateway'";
    $res = query_to_array($query);
    if(!count($res)) return;
    $username = $res[0]["username"];                    
    $password = $res[0]["password"];                    
    $ch = curl_init("https://sm.megafon.ru/sm/client/routing/set?login=$username@multifon.ru&password=$password&routing=$opt");
    
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}
?>