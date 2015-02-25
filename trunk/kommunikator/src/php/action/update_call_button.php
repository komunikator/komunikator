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

/* - - - - -  функция – получение внутреннего номера от SIP-адреса (НАЧАЛО)  - - - - - */

function extension_number_from_SIP_addresses($s) {
    $p = '~^sip\:(\d+)@~';
    if (preg_match($p, $s, $a)) {
        $result = $a[1];
    } else {
        $result = 'ERROR - function extension_number_from_SIP_addresses( ' . $s . ' )';
    }
    return $result;
}

/* - - - - -  функция – получение внутреннего номера от SIP-адреса (КОНЕЦ)  - - - - - */

need_user();
$data = json_decode($HTTP_RAW_POST_DATA);
if ($data && !is_array($data)) {
    $data = array($data);
}

/* - - - - -  получение ключевых переменных (НАЧАЛО)  - - - - - */

$sda_id = '';
$sda_description = '';
$sda_extension_number = '';
$sda_from_whom = '';

foreach ($data as $row) {

    foreach ($row as $key => $value) {

        if ($key == 'id') {
            $sda_id = $value;
        }  // значение поля «id»
        if ($key == 'description') {
            $sda_description = $value;
        }  // значение поля «Описание»
        if ($key == 'destination') {
            $sda_extension_number = $value;
        }  // значение поля «Назначение»
        if ($key == 'short_name') {
            $sda_from_whom = $value;
        }  // значение поля «Псевдоним»      
    }
}

$sda_ip_address = $_SERVER['SERVER_ADDR'];  // IP-адрес IP-АТС

/* - - - - -  получение ключевых переменных (КОНЕЦ)  - - - - - */


/* - - - - -  ХОД КОНЕМ (НАЧАЛО)  - - - - - */

/*
  1) функция json_decode($HTTP_RAW_POST_DATA) возвращает в данном случае только значения полей, которые были изменены
  2) имеются поля изменения значений, в которых отображается как в текущей, так и в других таблицах
  (+ значения некоторых полей требуют преобразований)
 */

$sda_query = <<<EOD
SELECT
    ta.id as ta_id,
    ta.name as description,
    ta.password as password,
    tas.id as tas_id,
    tas.address as destination,
    tasc.id as tasc_id,
    tasc.impi as short_name
FROM account_sip_caller tasc
LEFT JOIN account_sip tas
    ON tas.id = tasc.account_sip_id
LEFT JOIN account ta
    ON ta.id = tas.account_id
WHERE tasc.id = '$sda_id'
EOD;

$data = compact_array(query_to_array($sda_query));

if (!is_array($data["data"]))
    echo out(array("success" => false, "message" => $data));

$sda_get_groups = $_SESSION["get_groups"];  // 1 - group, 3 - extension
foreach ($data["data"] as $row) {
    $sda_id_table_account = $row[0];
    if ($sda_description == '') {
        $sda_description = $row[1];
    }
    $sda_password_table_account = $row[2];
    $sda_id_table_account_sip = $row[3];
    if ($sda_extension_number == '') {
        $sda_extension_number = extension_number_from_SIP_addresses(base64_decode($row[4]));
    } else {
        foreach ($sda_get_groups as $row_y) {
            if ($sda_extension_number == $row_y[1]) {
                $sda_extension_number = $row_y[3];
                break;
            }
        }
    }
    $sda_id_table_account_sip_caller = $row[5];
    if ($sda_from_whom == '') {
        $sda_from_whom = $row[6];
    }
}

/* - - - - -  ХОД КОНЕМ (КОНЕЦ)  - - - - - */


/* - - - - -  изменение записи в таблице account (НАЧАЛО)  - - - - - */

$sda_name_table_account = $sda_description;

$sda_email_table_account = 'komunikator@' . $sda_from_whom;

$sda_auxiliary_variable = $sda_password_table_account . ':' . $sda_email_table_account . ':click2call.org';
$sda_auth_token_table_account = MD5($sda_auxiliary_variable);



$sda_query_table_account[id] = "'$sda_id_table_account'";

$sda_query_table_account[name] = "'$sda_name_table_account'";

$sda_query_table_account[email] = "'$sda_email_table_account'";

$sda_query_table_account[auth_token] = "'$sda_auth_token_table_account'";

/* - - - - -  изменение записи в таблице account (КОНЕЦ)  - - - - - */



/* - - - - -  изменение записи в таблице account_sip (НАЧАЛО)  - - - - - */

$sda_auxiliary_variable = 'sip:' . $sda_extension_number . '@' . $sda_ip_address;
$sda_address_table_account_sip = base64_encode($sda_auxiliary_variable);



$sda_query_table_account_sip[id] = "'$sda_id_table_account_sip'";

$sda_query_table_account_sip[address] = "'$sda_address_table_account_sip'";

/* - - - - -  изменение записи в таблице account_sip (КОНЕЦ)  - - - - - */



/* - - - - -  изменение записи в таблице account_sip_caller (НАЧАЛО)  - - - - - */

$sda_display_name_table_account_sip_caller = $sda_from_whom;

$sda_impu_table_account_sip_caller = 'sip:' . $sda_from_whom . '@' . $sda_ip_address;

$sda_impi_table_account_sip_caller = $sda_from_whom;

$sda_realm_table_account_sip_caller = $sda_ip_address;

$sda_auxiliary_variable = $sda_from_whom . ':' . $sda_ip_address . ':' . $sda_from_whom;
$sda_ha1_table_account_sip_caller = MD5($sda_auxiliary_variable);



$sda_query_table_account_sip_caller[id] = "'$sda_id_table_account_sip_caller'";

$sda_query_table_account_sip_caller[display_name] = "'$sda_display_name_table_account_sip_caller'";

$sda_query_table_account_sip_caller[impu] = "'$sda_impu_table_account_sip_caller'";

$sda_query_table_account_sip_caller[impi] = "'$sda_impi_table_account_sip_caller'";

$sda_query_table_account_sip_caller[realm] = "'$sda_realm_table_account_sip_caller'";

$sda_query_table_account_sip_caller[ha1] = "'$sda_ha1_table_account_sip_caller'";

/* - - - - -  изменение записи в таблице account_sip_caller (КОНЕЦ)  - - - - - */


$sda_action = 'stop';
include("addition_call_button.php");

$need_out = false;

$rows = array();
$rows[] = $sda_query_table_account;

$id_name = 'id';  // UPDATE $table_name SET implode(', ', $updates) WHERE $id_name = $id
$action = 'update_account';
include("update.php");

$need_out = false;

$rows = array();
$rows[] = $sda_query_table_account_sip;

$id_name = 'id';
$action = 'update_account_sip';
include("update.php");

$rows = array();
$rows[] = $sda_query_table_account_sip_caller;

$id_name = 'id';
$action = 'update_account_sip_caller';
include("update.php");

$conn->disconnect();

$sda_action = 'start';
include("addition_call_button.php");
?>