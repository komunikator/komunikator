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

?><?


require_once("php/socketconn.php");

$called = getparam("number");
$obj = "SELECT value FROM additional_settings WHERE settings_id=3 AND description = 'call_order_executor'";

$caller = query_to_array($obj);
if (!$called) {echo out("Called number is undefined"); exit;} 

//$caller = $_SESSION["user"];

$command = "click_to_call " . $caller[0]['value'] . " $called";
$socket = new SocketConn;
$msg = '';
if($socket->error == "") {
    $obj=array("success"=>true);
    $socket->command($command);
    	    $sql="insert into actionlogs (date,performer,log,ip) values (".time().",\"{$_SESSION['extension']}\",\"$command\", \"{$_SERVER['REMOTE_ADDR']}\")";
	    query ($sql);
}
else {
    $obj=array("success"=>false);
    $obj['message'] ="Can't make call. Please contact your system administrator.";
    echo out($obj);
}
//echo out($obj);
$sleep_time = 20;
sleep($sleep_time);

$sql =
        <<<EOD
select * from (
select * 
from call_logs a  
join call_logs b on b.billid=a.billid and b.ended=0 and b.direction='outgoing' and b.status!='unknown'
left join extensions x on x.extension=a.caller
left join extensions x2 on x2.extension=b.called
left join gateways g  on g.authname=a.called or g.authname=b.caller
   where a.ended=0 and a.direction='incoming' and a.status!='unknown' and a.called= $called) a
EOD;


 header("Content-Type: application/javascript");
    $callback = $_GET["callback"];
    $message = "you got a response from server yipeee!!!";
    $jsonResponse = "{\"message\":\"" . $message . "\"}";
    echo $callback . "(" . $jsonResponse . ")";
?>