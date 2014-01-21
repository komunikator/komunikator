<?php

/*
*  | RUS | - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

*    «Komunikator» – Web-интерфейс для настройки и управления программной IP-АТС «YATE»
*    Copyright (C) 2012-2013, ООО «Телефонные системы»

*    ЭТОТ ФАЙЛ является частью проекта «Komunikator»

*    Сайт проекта «Komunikator»: http://4yate.ru/
*    Служба технической поддержки проекта «Komunikator»: E-mail: support@4yate.ru

*    В проекте «Komunikator» используются:
*      исходные коды проекта «YATE», http://yate.null.ro/pmwiki/
*      исходные коды проекта «FREESENTRAL», http://www.freesentral.com/
*      библиотеки проекта «Sencha Ext JS», http://www.sencha.com/products/extjs

*    Web-приложение «Komunikator» является свободным и открытым программным обеспечением. Тем самым
*  давая пользователю право на распространение и (или) модификацию данного Web-приложения (а также
*  и иные права) согласно условиям GNU General Public License, опубликованной
*  Free Software Foundation, версии 3.

*    В случае отсутствия файла «License» (идущего вместе с исходными кодами программного обеспечения)
*  описывающего условия GNU General Public License версии 3, можно посетить официальный сайт
*  http://www.gnu.org/licenses/ , где опубликованы условия GNU General Public License
*  различных версий (в том числе и версии 3).

*  | ENG | - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

*    "Komunikator" is a web interface for IP-PBX "YATE" configuration and management
*    Copyright (C) 2012-2013, "Telephonnyie sistemy" Ltd.

*    THIS FILE is an integral part of the project "Komunikator"

*    "Komunikator" project site: http://4yate.ru/
*    "Komunikator" technical support e-mail: support@4yate.ru

*    The project "Komunikator" are used:
*      the source code of "YATE" project, http://yate.null.ro/pmwiki/
*      the source code of "FREESENTRAL" project, http://www.freesentral.com/
*      "Sencha Ext JS" project libraries, http://www.sencha.com/products/extjs

*    "Komunikator" web application is a free/libre and open-source software. Therefore it grants user rights
*  for distribution and (or) modification (including other rights) of this programming solution according
*  to GNU General Public License terms and conditions published by Free Software Foundation in version 3.

*    In case the file "License" that describes GNU General Public License terms and conditions,
*  version 3, is missing (initially goes with software source code), you can visit the official site
*  http://www.gnu.org/licenses/ and find terms specified in appropriate GNU General Public License
*  version (version 3 as well).

*  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
*/

?><?php

require_once("config.php");
// require_once("lib/phpmailer.inc.php");  // файл lib_smtp.inc.php (этот файл) используют send_message.php и send_voicem.php, а следовательно и данную библиотеку


// mb_internal_encoding("utf-8");  // Установка/получение внутренней кодировки скрипта

define('TIME_FMT', '%d.%m.%Y %H:%M:%S');  // Определяет именованную константу


/*
class myMail extends phpmailer {
    var $From = "multifon@digt.ru";
    var $FromName = "multifon";
    var $CharSet = "utf-8";
}
*/


function format_array($arr) {
    $str = str_replace("\n", "", print_r($arr, true));
    $str = str_replace("\t", "", $str);

    while ( strlen($str) != strlen(str_replace("  ", " ", $str)) )
        $str = str_replace("  ", " ", $str);

    return $str;
}


function format_msg($text, $params) {
    return str_replace('\n', "\n", preg_replace("/\<([^\>]+)\>/e", 'isset($params["$1"]) ? $params["$1"] : "<$1>";', $text));
}


/* - - - - -  новая функция рассылки почтовых уведомлений (НАЧАЛО)  - - - - - */

/* (для справки)
send_mail(1, 2, 3, 4, 5, 6, 7);

$sda_FROM_ADDRESS     1 - Учетная запись (адрес ЭПЯ от имени, которого будет проводиться рассылка)
$sda_FROM_PASSWORD    2 - Пароль (для доступа к ЭПЯ)
$sda_FROM_NAME        3 - Имя отправителя письма
$sda_TO_ADDRESS       4 - Адрес получателя (адрес ЭПЯ получателя письма)
$sda_SUBJECT          5 - Заголовок письма
$sda_MESSAGE          6 - Текст письма
$sda_FILE             7 - Прикрепленный файл
*/

function send_mail($sda_FROM_ADDRESS=null, $sda_FROM_PASSWORD=null, $sda_FROM_NAME=null, $sda_TO_ADDRESS=null, $sda_SUBJECT=null, $sda_MESSAGE=null, $sda_FILE=null) {

    /* - - - - -  условия аварийного завершения функции (НАЧАЛО)  - - - - - */
    if (!$sda_FROM_ADDRESS) return;
    if (!$sda_FROM_PASSWORD) return;
    if (!$sda_TO_ADDRESS) return;
    /* - - - - -  условия аварийного завершения функции (КОНЕЦ)  - - - - - */


    /* - - - - -  подключение пакетов PEAR (НАЧАЛО)  - - - - - */
    require_once "Mail.php";
    require_once "Mail/mime.php";
    /* - - - - -  подключение пакетов PEAR (КОНЕЦ)  - - - - - */


    /* - - - - -  формирование письма (НАЧАЛО)  - - - - - */
    if ($sda_FROM_NAME) {
        $sda_auxiliary_variable = $sda_FROM_NAME . ' <' . $sda_FROM_ADDRESS . '>';
    }
    else {
        $sda_auxiliary_variable = $sda_FROM_ADDRESS;
    }


    $sda_HEADER = array('From' => $sda_auxiliary_variable, 'To' => $sda_TO_ADDRESS, 'Subject' => $sda_SUBJECT);


    $sda_MIME = new Mail_mime( array( "html_charset" => "UTF-8", "text_charset" => "UTF-8", "head_charset" => "UTF-8" ) );

    $sda_MIME->setHTMLBody('<html><body>' . $sda_MESSAGE . '</body></html>');

    $sda_MIME->addAttachment($sda_FILE, 'application/octet-stream');


    $sda_BODY = $sda_MIME->get();

    $sda_EXTENDED_HEADER = $sda_MIME->headers($sda_HEADER);
    /* - - - - -  формирование письма (КОНЕЦ)  - - - - - */


    /* - - - - -  данные для получения доступа к исходящему почтовому серверу по протоколу SSL (НАЧАЛО)  - - - - - */
    $sda_SMTP = array();

    $sda_SMTP["host"] = "ssl://smtp.gmail.com";  // константа
    $sda_SMTP["port"] = "465";  // константа
    $sda_SMTP["auth"] = true;
    $sda_SMTP["username"] = $sda_FROM_ADDRESS;
    $sda_SMTP["password"] = $sda_FROM_PASSWORD;
    /* - - - - -  данные для получения доступа к исходящему почтовому серверу по протоколу SSL (КОНЕЦ)  - - - - - */


    /* - - - - -  установление соединения и отправка письма (НАЧАЛО)  - - - - - */
    $sda_SMTP = &Mail::factory('smtp', $sda_SMTP);

    $sda_ERROR = $sda_SMTP->send($sda_TO_ADDRESS, $sda_EXTENDED_HEADER, $sda_BODY);

    if (PEAR::isError($sda_ERROR)) {
        echo $sda_ERROR->getMessage();
    }
    /* - - - - -  установление соединения и отправка письма (КОНЕЦ)  - - - - - */

}

/* - - - - -  новая функция рассылки почтовых уведомлений (КОНЕЦ)  - - - - - */


/* (старая функция рассылки почтовых уведомлений)
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
*/


/* (старая функция рассылки голосовых сообщений)
function send_voicemail($address, $filename, $caller, $ftime = false) {

    if (!$address)
        return;

    global $def_time_offset;

    if (!$ftime)
        $ftime = strftime(TIME_FMT, time() + 60 * 60 * $def_time_offset);

    $text = <<<EOD
Абонент: $caller
Дата: $ftime
EOD;
    

    $mail = new myMail;

    $mail->Body = $text;
    $mail->AddAddress($address);
    $subject = 'Звонок не принят от ' . $caller . ' ' . $ftime;
    $mail->Subject = mb_encode_mimeheader($subject, $mail->CharSet, 'B');
    if ( is_file($filename) )
        $mail->AddAttachment($filename);

    $mail->Send();
}
*/

?>