<?php

/*
 *  | RUS | - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

 *    <Komunikator> - Web-интерфейс для настройки и управления программной IP-АТС <YATE>
 *    Copyright (C) 2012-2013, ООО <Телефонные системы>

 *    ЭТОТ ФАЙЛ является частью проекта <Komunikator>

 *    Сайт проекта «Komunikator»: http://komunikator.ru/
 *    Служба технической поддержки проекта «Komunikator»: E-mail: support@komunikator.ru

 *    В проекте <Komunikator> используются:
 *      исходные коды проекта <YATE>, http://yate.null.ro/pmwiki/
 *      исходные коды проекта <FREESENTRAL>, http://www.freesentral.com/
 *      библиотеки проекта <Sencha Ext JS>, http://www.sencha.com/products/extjs

 *    Web-приложение <Komunikator> является свободным и открытым программным обеспечением. Тем самым
 *  давая пользователю право на распространение и (или) модификацию данного Web-приложения (а также
 *  и иные права) согласно условиям GNU General Public License, опубликованной
 *  Free Software Foundation, версии 3.

 *    В случае отсутствия файла <License> (идущего вместе с исходными кодами программного обеспечения)
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

$data = json_decode($HTTP_RAW_POST_DATA);
if ($data && !is_array($data))
    $data = array($data);

$rows = array();

// - - - - - - - - - - - - - - - - - - - -
/* Звонок с сайта */

$sda_tick_condition_call_website = 'NO';
$sda_tick_id_call_website = 'NO';

// - - - - - - - - - - - - - - - - - - - -
/* Почтовые уведомления */

$sda_tick_condition_mail_settings = 'NO';
$sda_tick_id_mail_settings = 'NO';

// - - - - - - - - - - - - - - - - - - - -

/* Запись звонков */

$sda_tick_condition_record_settings = 'NO';
$sda_tick_id_record_settings = 'NO';

// - - - - - - - - - - - - - - - - - - - -

foreach ($data as $row) {
    $values = array();
    foreach ($row as $key => $value) {
        $values[$key] = "'$value'";

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        /* Звонок с сайта */

        if ($sda_tick_condition_call_website === 'NO' or $sda_tick_id_call_website === 'NO') {
            if ($key == 'condition') {
                if ($value == 1) {
                    $sda_tick_condition_call_website = $value;
                } else {
                    $sda_tick_condition_call_website = 0;
                }
            }

            if ($key == 'id') {
                if ($value == 1) {
                    $sda_tick_id_call_website = $value;
                } else {
                    $sda_tick_condition_call_website = 'NO';
                }
            }
        }

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        /* Почтовые уведомления */

        if ($sda_tick_condition_mail_settings === 'NO' or $sda_tick_id_mail_settings === 'NO') {
            if ($key == 'condition') {
                if ($value == 1) {
                    $sda_tick_condition_mail_settings = $value;
                } else {
                    $sda_tick_condition_mail_settings = 0;
                }
            }

            if ($key == 'id') {
                if ($value == 2) {
                    $sda_tick_id_mail_settings = $value;
                } else {
                    $sda_tick_condition_mail_settings = 'NO';
                }
            }
        }

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        /* Запись звонков */

        if ($sda_tick_condition_record_settings === 'NO' or $sda_tick_id_record_settings === 'NO') {
            if ($key == 'condition') {
                if ($value == 1) {
                    $sda_tick_condition_record_settings = $value;
                } else {
                    $sda_tick_condition_record_settings = 0;
                }
            }

            if ($key == 'id') {
                if ($value == 3) {
                    $sda_tick_id_record_settings = $value;
                } else {
                    $sda_tick_condition_record_settings = 'NO';
                }
            }
        }

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    }

    $rows[] = $values;
}


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
/* Звонок с сайта */

if ($sda_tick_id_call_website == 1 and $sda_tick_condition_call_website == 1) {
    $sda_action = 'start';
    include("addition_call_button.php");
}

if ($sda_tick_id_call_website == 1 and $sda_tick_condition_call_website == 0) {
    $sda_action = 'stop';
    include("addition_call_button.php");
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
/* Почтовые уведомления */

if ($sda_tick_id_mail_settings == 2 and $sda_tick_condition_mail_settings == 1) {
    $sda_action = 'start';
    include("module_yate_send_message.php");
}

if ($sda_tick_id_mail_settings == 2 and $sda_tick_condition_mail_settings == 0) {
    $sda_action = 'stop';
    include("module_yate_send_message.php");
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
/* Запись звонков */

if ($sda_tick_id_record_settings == 3 and $sda_tick_condition_record_settings == 1) {
    $sda_action = 'start';
    include("module_call_record.php");
}

if ($sda_tick_id_record_settings == 3 and $sda_tick_condition_record_settings == 0) {
    $sda_action = 'stop';
    include("module_call_record.php");
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

$id_name = 'module_name_id';
require_once("update.php");
?>