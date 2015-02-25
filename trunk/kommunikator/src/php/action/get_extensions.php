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

if (!$_SESSION['user']) {
    echo (out(array("success" => false, "message" => "User is undefined")));
    exit;
}

$sql = <<<EOD
SELECT * FROM (

    SELECT
	ex.extension_id as id,

	CASE
		-- WHEN (SELECT count(*) FROM call_logs where caller =ex.extension and status!='unknown' and ended = false) > 1 THEN 'busy'
		WHEN COALESCE(inuse_count,0)!=0 THEN 'busy'
		WHEN expires is not NULL THEN 'online'
		ELSE 'offline' END as status,

	ex.extension,
	ex.password,
	firstname,
	lastname,
	ex.address,
	m.group as group_name,
	priority,
	fwd.value as forward,
	fwd_busy.value as forward_busy,
	fwd_no_answ.value as forward_noanswer,
	no_answ_to.value as noanswer_timeout
    FROM extensions ex

    LEFT JOIN group_members gm
	on ex.extension_id = gm.extension_id
    LEFT JOIN groups m
	on gm.group_id = m.group_id
    LEFT JOIN group_priority gp
	on ex.extension_id = gp.extension_id
    LEFT JOIN pbx_settings fwd
	on fwd.extension_id = ex.extension_id and fwd.param = "forward"
    LEFT JOIN pbx_settings fwd_busy
	on fwd_busy.extension_id = ex.extension_id and fwd_busy.param = "forward_busy"
    LEFT JOIN pbx_settings fwd_no_answ
	on fwd_no_answ.extension_id = ex.extension_id and fwd_no_answ.param = "forward_noanswer"
    LEFT JOIN pbx_settings no_answ_to
	on no_answ_to.extension_id = ex.extension_id and no_answ_to.param = "noanswer_timeout"

	) a
EOD;


$data = compact_array(query_to_array($sql . get_filter()));

if (!is_array($data["data"]))
    echo out(array("success" => false, "message" => $data));

$total = count($data["data"]);


$data = compact_array(query_to_array($sql . get_sql_order_limit()));

if (!is_array($data["data"]))
    echo out(array("success" => false, "message" => $data));


$obj = array("success" => true);

$obj["total"] = $total;
$obj["data"] = $data['data'];

$_SESSION["get_extensions"] = $data['data'];

/*
  $_SESSION :

  id
  status
  extension
  password
  firstname
  lastname
  address
  group_name
  priority
  forward
  forward_busy
  forward_noanswer
  noanswer_timeout

  FROM :

  extensions
  group_members
  groups
  group_priority
  pbx_settings
 */

echo out($obj);
?>