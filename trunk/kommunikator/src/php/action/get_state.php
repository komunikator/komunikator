<?php

/*
 *  | RUS | - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

 *    «Komunikator» – Web-интерфейс для настройки и управления программной IP-АТС «YATE»
 *    Copyright (C) 2012-2013, ООО «Телефонные системы»

 *    ЭТОТ ФАЙЛ является частью проекта «Komunikator»

 *    Сайт проекта «Komunikator»: http://komunikator.ru/
 *    Служба технической поддержки проекта «Komunikator»: E-mail: support@komunikator.ru

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

 *    "Komunikator" project site: http://komunikator.ru/
 *    "Komunikator" technical support e-mail: support@komunikator.ru

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

if ($_SESSION["extension"])
    $extension = "'" . $_SESSION["extension"] . "'";
$period = getparam("period");
$limit = getparam("limit");
$time_offset = getparam("time_offset");

if ($period)
    $period = $conn->escapeSimple($period);
if ($limit)
    $limit = $conn->escapeSimple($limit);
if ($time_offset)
    $time_offset = $conn->escapeSimple($time_offset);

if (!$period)
    $period = 10;
if (!$limit)
    $limit = 1000;
if (!$time_offset)
    $time_offset = $_SESSION['time_offset'];
if (!$time_offset)
    $time_offset = -240;

//$time_offset = 0;

if (!$extension) {
    echo (out(array("success" => false, "message" => "Extension is undefined")));
    exit;
}

$sql = "update ntn_settings set value = $extension, description = '" . time() . "' where param = 'exclude_called' and value = $extension";
query($sql);
$sql = "insert into ntn_settings (param,value,description) select 'exclude_called',$extension, '" . time() . "' from dual where not exists (select 1 from ntn_settings where param = 'exclude_called' and value = $extension)";
query($sql);

$status = compact_array(query_to_array("SELECT extension,CASE WHEN expires is not NULL THEN 'online' ELSE 'offline' END as status FROM extensions where extension in ($extension) ORDER BY 2 LIMIT 1000 OFFSET 0"));
$obj = array("success" => true);
if (isset($_SESSION['extension']))
    $obj["status"] = $status['data'][0][1];
else
    $obj["status"] = $status['data'];

$inuse_count = compact_array(query_to_array("select inuse_count from extensions where extension = $extension"));
$obj["inuse_count"] = $inuse_count['data'][0][0];
//$data  =  compact_array(query_to_array ("SELECT time-($time_offset)*60,caller,time FROM call_logs where ".time()."-time < $period and (/*caller in ($extension) or*/ called in ($extension)) and direction='outgoing' LIMIT $limit OFFSET 0"));
$data = compact_array(query_to_array("SELECT a.time-($time_offset)*60,a.caller,a.time,case when b.called = a.called then null  when c.description !='' then c.description else b.called end FROM call_logs a left join call_logs b on b.billid=a.billid and b.direction='incoming' and b.ended=0 left join gateways c on c.authname=b.called where " . time() . "-a.time < $period and (b.caller in ($extension) or a.called in ($extension)) and a.direction='outgoing' and a.ended=0 LIMIT $limit OFFSET 0"));
/*
  $f_data = array();
  foreach ($data["data"] as $row) {
  $row[0] = date($date_format,$row[0]);
  $f_data[] = $row;
  //$f_data[] = array('time'=>$row[0],'number'=>$row[1]);
  }

  //$obj["calls"] = $f_data;
 */
if ($data["data"][0] && ($_SESSION['last_call'] != $data["data"][0][2])) {
    //$obj["incoming_call"] = array('time'=>date($date_format,$data["data"][0][0]),'number'=>$data["data"][0][1]); 
    $obj["incoming_call"] = array('time' => date($date_format, $data["data"][0][0]), 'number' => $data["data"][0][1], 'incoming_trunk' => $data["data"][0][3]);
    session_start();
    $_SESSION['last_call'] = $data["data"][0][2];
    session_write_close();
}
echo out($obj);
?>