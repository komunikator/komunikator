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

if (!$_SESSION['user']) {
    echo (out(array("success" => false, "message" => "User is undefined")));
    exit;
}
$total = compact_array(query_to_array("SELECT count(*) FROM call_records"));

if (!is_array($total["data"]))
    echo out(array("success" => false, "message" => $total));
$sql = <<<EOD
select 
call_records_id as id,
CASE
WHEN call_records.caller = '*'
THEN 'All'
WHEN call_records.caller = extensions.extension_id
THEN extensions.extension
WHEN call_records.caller = groups.group_id
THEN groups.group
else caller
END caller,

CASE
WHEN call_records.type= '*'
THEN 'all_calls'
WHEN call_records.type = '1'
THEN 'outgoing_calls'
WHEN call_records.type = '2'
THEN 'incoming_calls'
WHEN call_records.type = '3'
THEN 'internal_calls'
END type,

CASE
WHEN call_records.gateway = '*'
THEN 'All'
WHEN call_records.gateway = gateways.gateway_id
THEN gateways.gateway
END gateway,

CASE
WHEN call_records.number = '*'
THEN 'All'
WHEN call_records.number = x1.extension_id
THEN x1.extension
ELSE call_records.number
END number,

CASE
WHEN call_records.class = x2.group_id
THEN x2.group
END class,
call_records.enabled,
call_records.description

from call_records 

LEFT JOIN extensions ON extensions.extension_id = call_records.caller
LEFT JOIN extensions x1 ON x1.extension_id = call_records.number
LEFT JOIN groups ON groups.group_id = call_records.caller
LEFT JOIN groups x2 ON x2.group_id = call_records.class
LEFT JOIN gateways ON gateways.gateway_id = call_records.gateway
EOD;

$data = compact_array(query_to_array($sql . get_sql_order_limit()));

if (!is_array($data["data"]))
    echo out(array("success" => false, "message" => $data));
$obj = array("success" => true);

$obj["total"] = $total['data'][0][0];
$obj["data"] = $data['data'];

echo out($obj);