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


//$caller = $_GET["caller"];//добавить проверку
//$site = $_GET["site"];//добавить проверк

//получаем Номер исполняющий заказ звонка. Если он отсутствует - выводим ошибку
/*$obj = "SELECT value FROM additional_settings WHERE settings_id=3 AND description = 'call_order_executor'";
$caller = query_to_array($obj);
if ($caller[0]['value'] == '' || $caller[0]['value'] == null) {
    $message = "Call order executor is undefined";
    $jsonResponse = "{\"warning\":\"" . $message . "\"}";
    echo $callback . "(" . $jsonResponse . ")";
    exit;
}*/
//-----
require_once("php/socketconn.php");

header("Content-Type: application/javascript");
$callback = $_GET["callback"];
$called = getparam("number");
$call_back_id= $_GET["call_back_id"];
$sql = "SELECT destination, name_site, callthrough_time FROM call_back WHERE call_back_id = $call_back_id";
$res = query_to_array($sql);
$caller = $res[0]["destination"];
$site = $res[0]["name_site"];
$callthrough_time = $res[0]["callthrough_time"];

//echo("----------------------------------------------". $site);
//--------


//если номер телефона, на который нужно совершить заказанный звонок, не передан - выводим ошибку
$check_called = str_replace(' ', '', $called);
if (!$called || $check_called == '') {
    $message = "Phone number is undefined";
    $jsonResponse = "{\"warning\":\"" . $message . "\"}";
    echo $callback . "(" . $jsonResponse . ")";
    exit;
}

//если с одного и того же браузера(или одна и та же сессия) получаем запрос несколько раз подряд 
//запрос на заказ звонка, то проверяем промежуток между этим запросами. Он должен быть не менее 30сек
if ($_SESSION['last_action']) {
    if ((time() - $_SESSION['last_action']) < 30) {
        $message = "You can reorder the call after 30 seconds";
        $jsonResponse = "{\"warning\":\"" . $message . "\"}";
        echo $callback . "(" . $jsonResponse . ")";
        exit;
    }
}

//записываем время сессии и после этого закрываем файл, где пишутся некие данные о сессии
//это сделано, чтобы можно было с одной сессии делать один и тот же запрос несколько раз
//(иначе, пока первый запрос не выполнится, второй также не выполняется. с закрыванием - 
//можно выполнять второй запрос, пока первый еще не завершился)
$_SESSION['last_action'] = time();
session_write_close();


$command = "click_to_call $caller $called $site $callthrough_time";
$socket = new SocketConn;
if ($socket->error == "") {
    $obj = array("success" => true);
    $socket->command($command);
    $sql = "insert into actionlogs (date,performer,log,ip) values (" . time() . ",\"{$_SESSION['extension']}\",\"$command\", \"{$_SERVER['REMOTE_ADDR']}\")";
    query($sql);
} else {
    $obj = array("success" => false);
    $obj['message'] = "Can't make call. Please contact your system administrator.";
    echo out($obj);
}

//проверяем есть ли активные звонки с нужными параметрами
$sql =
        <<<EOD
select * from (
select
    case 
        when x.extension is not null and x2.extension is not null then 'internal'
        when x.extension is not null then 'outgoing'
        else 'incoming'
    end type,
    a.caller,
    b.called,
    case 
        when a.status!="answered" or b.status!="answered" 
            then "progressing" 
        else a.status 
    end status
from call_logs a  
    join call_logs b on b.billid=a.billid and b.ended=0 and b.direction='outgoing' and b.status!='unknown'
    left join extensions x on x.extension=a.caller
    left join extensions x2 on x2.extension=b.called
    where a.ended=0 and a.direction='incoming' and 
    a.status!='unknown' and b.called = $called and
    (a.status="answered" and b.status="answered")) a
EOD;


for ($i = 0; $i <= 4; $i++) {
    sleep(5);
    $data = compact_array(query_to_array($sql));
    $total = count($data["data"]);
    // соединение прошло успешно, отвечаем true
    if ($total > 0) {
        $message = "true";
        $jsonResponse = "{\"success\":\"" . $message . "\"}";
        echo $callback . "(" . $jsonResponse . ")";
        break;
    }
    //если соединение между АБОНЕНТАМИ не произошло в течении 25сек, отвечаем false
    if ($i == 4 && $total == 0) {
        $message = "false";
        $jsonResponse = "{\"success\":\"" . $message . "\"}";
        echo $callback . "(" . $jsonResponse . ")";
    }
}
?>