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

$total = compact_array(query_to_array("SELECT count(*) FROM modules"));

if (!is_array($total["data"]))
    echo out(array("success" => false, "message" => $total));

$sql = <<<EOD
SELECT
    modules.module_name_id as id,
    modules.module_name,
    modules.description,
    modules.version,
    modules.condition
FROM
    modules
EOD;

$data = compact_array(query_to_array($sql . get_sql_order_limit()));

if (!is_array($data["data"]))
    echo out(array("success" => false, "message" => $data));

/* - - - - -  подключение или отключение модулей (НАЧАЛО)  - - - - - */

$sda_tick_condition_call_website = 'NO';
$sda_tick_condition_mail_settings = 'NO';
$sda_tick_condition_call_record = 'NO';

foreach ($data["data"] as $row) {

    if ($row[1] == 'Call_website_Grid') {

        if ($row[4] == 1) {
            $sda_tick_condition_call_website = $row[4];
        } else {
            $sda_tick_condition_call_website = 0;
        }
    }

    if ($row[1] == 'Mail_Settings_Panel') {

        if ($row[4] == 1) {
            $sda_tick_condition_mail_settings = $row[4];
        } else {
            $sda_tick_condition_mail_settings = 0;
        }
    }
    if ($row[1] == 'Call_Record_Grid') {

        if ($row[4] == 1) {
            $sda_tick_condition_call_record = $row[4];
        } else {
            $sda_tick_condition_call_record = 0;
        }
    }
}


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
/* Звонок с сайта */

if ($sda_tick_condition_call_website !== 'NO') {

    if ($sda_tick_condition_call_website == 1) {
        $sda_action = 'start';
        include("addition_call_button.php");
    }

    if ($sda_tick_condition_call_website == 0) {
        $sda_action = 'stop';
        include("addition_call_button.php");
    }
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
/* Почтовые уведомления */

if ($sda_tick_condition_mail_settings !== 'NO') {

    if ($sda_tick_condition_mail_settings == 1) {
        $sda_action = 'start';
        include("module_yate_send_message.php");
    }

    if ($sda_tick_condition_mail_settings == 0) {
        $sda_action = 'stop';
        include("module_yate_send_message.php");
    }
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
/* запись звонка */

if ($sda_tick_condition_call_record !== 'NO') {

    if ($sda_tick_condition_call_record == 1) {
        $sda_action = 'start';
         include("module_call_record.php");
    }

    if ($sda_tick_condition_call_record == 0) {
        $sda_action = 'stop';
         include("module_call_record.php");
    }
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


/* - - - - -  подключение или отключение модулей (КОНЕЦ)  - - - - - */



$obj = array("success" => true);

$obj["total"] = $total['data'][0][0];
$obj["data"] = $data['data'];

echo out($obj);
?>