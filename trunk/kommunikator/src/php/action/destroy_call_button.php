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

need_user();

$data = json_decode($HTTP_RAW_POST_DATA);

if ( $data && !is_array($data) ) $data = array($data);



/* - - - - -  получение ключевых переменных (НАЧАЛО)  - - - - - */

foreach ($data as $row) {
    
    foreach ( $row as $key => $value ) {
        
        if ($key == 'id') { $sda_id = $value; }  // значение поля «id»
        
    }
    
}



// получение значений id удаляемой записи из всех трех таблиц

$sda_query = <<<EOD
SELECT
    tasc.id as tasc_id,
    tas.id as tas_id,
    ta.id as ta_id
FROM account_sip_caller tasc
LEFT JOIN account_sip tas
    ON tas.id = tasc.account_sip_id
LEFT JOIN account ta
    ON ta.id = tas.account_id
WHERE tasc.id = '$sda_id'
EOD;

$data = compact_array(query_to_array($sda_query));

if (!is_array($data["data"])) echo out(array("success"=>false,"message"=>$data));



foreach ($data["data"] as $row) {
    
    $sda_id_table_account_sip_caller = $row[0];
    $sda_id_table_account_sip = $row[1];
    $sda_id_table_account = $row[2];
    
}

/* - - - - -  получение ключевых переменных (КОНЕЦ)  - - - - - */



/* - - - - -  преобразование данных в требуемый формат (НАЧАЛО)  - - - - - */

$sda_query_table_account_sip_caller[id] = $sda_id_table_account_sip_caller;

$sda_query_table_account_sip[id] = $sda_id_table_account_sip;

$sda_query_table_account[id] = $sda_id_table_account;

/* - - - - -  преобразование данных в требуемый формат (КОНЕЦ)  - - - - - */




$need_out = false;


$rows = array();
$rows[] = $sda_query_table_account_sip_caller;

$id_name = 'id';  // DELETE FROM $table_name WHERE $id_name = $id
$action = 'destroy_account_sip_caller';
include("destroy.php");


$need_out = false;


$rows = array();
$rows[] = $sda_query_table_account_sip;

$id_name = 'id';
$action = 'destroy_account_sip';
include("destroy.php");


$rows = array();
$rows[] = $sda_query_table_account;

$id_name = 'id';
$action = 'destroy_account';
include("destroy.php");

?>