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

need_user();

$table_name = 'extensions';
$values = array();
$data = json_decode($HTTP_RAW_POST_DATA);
$rows = array();
$pbx_values = array();
$prior_values = array();

if ($data && !is_array($data))
    $data = array($data);
foreach ($data as $row) {
    $values = array();
    foreach ($row as $key => $value)
        if ($key == 'group_name')
            $group = ($value == null) ? 'null' : $value;
        else {
            if ($key == 'id')
                $extension_id = $value;
            if (!in_array($key, array('forward', 'forward_busy', 'forward_noanswer', 'noanswer_timeout', 'priority')))
                $values[$key] = "'$value'";
            else {
                if (!in_array($key, array('forward', 'forward_busy', 'forward_noanswer', 'noanswer_timeout')))
                    $prior_values[$key] = "'$value'";
                /* $sql="update pbx_settings set value = '$value' where extension_id = $extension_id and param = '$key'";
                  print_r(q	uery ($sql));
                  /*$sql= "insert into pbx_settings (extension_id,param,value) values (select $extension_id,'$key' where not exists (select 1 from pbx_settings where extension_id = $extension_id and param = '$key'))";
                  query ($sql);
                 */
                else {
                    $pbx_values[$key] = "'$value'";
                };
            };
        }
    $rows[] = $values;
}

if ($pbx_values)
    foreach ($pbx_values as $pbx_key => $pbx_value) {
        $sql = "update pbx_settings set value = $pbx_value where extension_id = $extension_id and param = '$pbx_key'";
        query($sql);
        $sql = "insert into pbx_settings (extension_id,param,value) select $extension_id,'$pbx_key', $pbx_value from dual where not exists (select 1 from pbx_settings where extension_id = $extension_id and param = '$pbx_key' and value = $pbx_value)";
        query($sql);
    }

$id_name = 'extension_id';
if ($group)
    $need_out = false;
include ("update.php");

if ($group) {

    $sql =
            <<<EOD
	SELECT group_member_id,g.group_id FROM group_members gm 
	left join groups g on g.group = '$group'  
	where gm.extension_id = '$extension_id'			
EOD;

    $rows = array();

    $result = compact_array(query_to_array($sql));
    if (!is_array($result['data']))
        echo out(array('success' => false, 'message' => $result));
    $row = $result['data'][0];

    if ($row) {
        $id_name = 'group_member_id';
        $rows[] = array('id' => $row[0], 'group_id' => $row[1]);
        if ($group != 'null') {
            $action = 'update_group_members';
            include ("update.php");
        } else {
            $action = 'destroy_group_members';
            include ("destroy.php");
        }
    } else {
        $rows[] = array('extension_id' => "'$extension_id'", 'group_id' => " (SELECT group_id FROM groups WHERE groups.group = '$group') ");
        $action = 'create_group_members';
        include ("create.php");
    }
}
if ($prior_values)
    foreach ($prior_values as $prior_key => $prior_value) {
        /*    $sql = "update group_priority set priority = $prior_value where extension_id = $extension_id and group_id = (select group_id from group_members where extension_id = $extension_id)";
          query($sql);
          $sql = "insert into group_priority (group_id, extension_id, priority) select (select group_id from group_members where extension_id = $extension_id), $extension_id, $prior_value from dual where not exists (select 1 from group_priority where extension_id = $extension_id and group_id = (select group_id from group_members where extension_id = $extension_id))";
          query($sql); */
        $sql = "update group_priority set priority = $prior_value, group_id =(select group_id from group_members where extension_id = $extension_id)  where extension_id = $extension_id";
        query($sql);
        $sql = "insert into group_priority (group_id, extension_id, priority) select (SELECT group_id FROM groups WHERE groups.group = '$group'), $extension_id, $prior_value from dual where not exists (select 1 from group_priority where extension_id = $extension_id )";
        query($sql);
    }

if (!$group)
    return;
?>