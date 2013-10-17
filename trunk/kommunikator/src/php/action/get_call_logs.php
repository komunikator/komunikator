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

//sleep(10);
if (!$_SESSION['user'] && !$_SESSION['extension']) {
    echo (out(array("success" => false, "message" => "auth_failed")));
    exit;
}
if ($_SESSION['user']) {
    $call === NULL;
}

if ($_SESSION['extension']) {
    $exten = $_SESSION['extension'];
    $call = "and (a.caller = '$exten' OR b.called ='$exten')";
}

$sql =
        <<<EOD
select * from (
select
	"" id,
	b.time,
        case 
          when x.extension is not null and x2.extension is not null then 'internal'
          when x.extension is not null then 'outgoing'
	  else 'incoming'
        end type,
	case when x.firstname is null then a.caller else concat(x.firstname,' ',x.lastname,' (',a.caller,')') end caller,
	case when x2.firstname is null then b.called else concat(x2.firstname,' ',x2.lastname,' (',b.called,')') end called,
	round(b.billtime) duration,
        case 
	 when g.description is not null and g.description !='' then g.description 
	 when g.gateway     is not null                        then g.gateway	
	 when g.authname    is not null                        then g.authname
	else null 
        end gateway,
        case when b.reason="" then b.status else replace(lower(b.reason),' ','_') end status
from call_logs a  
join call_logs b on b.billid=a.billid and b.ended=1 and b.direction='outgoing' and b.status!='unknown'
left join extensions x on x.extension=a.caller 
left join extensions x2 on x2.extension=b.called
left join gateways g  on g.authname=a.called or g.authname=b.caller
   where a.ended=1 and a.direction='incoming' and a.status!='unknown'  $call ) a
EOD;

$data = compact_array(query_to_array($sql . get_filter()));
if (!is_array($data["data"]))
    echo out(array("success" => false, "message" => $data));
$total = count($data["data"]);

$data = compact_array(query_to_array($sql . get_sql_order_limit()));
if (!is_array($data["data"]))
    echo out(array("success" => false, "message" => $data));

//$total = count($data["data"]);

$obj = array("success" => true);
$obj["total"] = $total;

/*
  $total =  compact_array(query_to_array("SELECT count(*) FROM call_logs ".get_filter()));
  if(!is_array($total["data"]))  echo out(array("success"=>false,"message"=>$total));
  $data =  compact_array(query_to_array("SELECT \"\" id, time, caller, called, duration,  status FROM call_logs  ".get_sql_order_limit()));
  if(!is_array($data["data"])) echo out(array("success"=>false,"message"=>$data));
  $obj=array("success"=>true);
  $obj["total"] = $total['data'][0][0];
  //$obj["sql"] = get_sql_order_limit();
  //$obj["sql"] = strtotime('2010/08/11 06:33:00'); //get_sql_order_limit();
 */

$f_data = array();
foreach ($data["data"] as $row) {
    $row[1] -= $_SESSION['time_offset'] * 60;
    $row[1] = date($date_format, $row[1]);
    $f_data[] = $row;
}
$obj["header"] = $data["header"];
$obj["data"] = $f_data;
echo out($obj);
?>