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

// sleep(10);

if (!$_SESSION['user'] && !$_SESSION['extension']) {
    echo (out(array("success" => false, "message" => "auth_failed")));
    exit;
}


$call = NULL;

if ($_SESSION['extension']) {
    $exten = $_SESSION['extension'];
    $call = "WHERE a.caller = '$exten' OR b.called = '$exten'";
}

$sql = <<<EOD
SELECT * FROM (
    SELECT
        "" AS id,
        time,
        direction,
        CASE
            WHEN x1.firstname IS NULL
                THEN caller
            ELSE CONCAT( x1.firstname, ' ', x1.lastname, ' (', caller, ')' )
        END caller,
        CASE
            WHEN x2.firstname IS NULL
                THEN called
            ELSE CONCAT( x2.firstname, ' ', x2.lastname, ' (', called, ')' )
        END called,
        ROUND(billtime) duration,
        CASE
            WHEN g.description IS NOT NULL AND g.description != ''
                THEN g.description
            WHEN g.gateway IS NOT NULL
                THEN g.gateway
            WHEN g.authname IS NOT NULL
                THEN g.authname
            ELSE NULL
        END gateway,
        status,
        record       
    FROM call_history a
    LEFT JOIN extensions x1 ON x1.extension = caller
    LEFT JOIN extensions x2 ON x2.extension = called
    $call
) a
EOD;

$data = compact_array(query_to_array($sql . get_filter()));
if (!is_array($data["data"])) echo out(array("success" => false, "message" => $data));

$total = count($data["data"]);

$data = compact_array(query_to_array($sql . get_sql_order_limit()));
if (!is_array($data["data"])) echo out(array("success" => false, "message" => $data));


$obj = array("success" => true);
$obj["total"] = $total;

/*
$total =  compact_array(query_to_array("SELECT count(*) FROM call_logs ".get_filter()));
if(!is_array($total["data"]))  echo out(array("success"=>false,"message"=>$total));
$data =  compact_array(query_to_array("SELECT \"\" id, time, caller, called, duration,  status FROM call_logs  ".get_sql_order_limit()));
if(!is_array($data["data"])) echo out(array("success"=>false,"message"=>$data));
$obj=array("success"=>true);
$obj["total"] = $total['data'][0][0];
// $obj["sql"] = get_sql_order_limit();
// $obj["sql"] = strtotime('2010/08/11 06:33:00'); //get_sql_order_limit();
*/

$f_data = array();
foreach ($data["data"] as $row) {
    $row[1] = $row[1] - $_SESSION['time_offset'] * 60;
    $row[1] = date($date_format, $row[1]);  // $date_format = "d.m.y H:i:s"; - data.php
    $f_data[] = $row;
    $f_data = translate($f_data, $_SESSION['lang'] ? $_SESSION['lang'] : 'ru');   //переводим на рус/англ
}

$obj["header"] = $data["header"];
$obj["data"] = $f_data;
echo out($obj);
?>