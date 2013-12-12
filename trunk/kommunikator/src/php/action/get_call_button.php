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

/* - - - - -  функция – получение внутреннего номера от SIP-адреса (НАЧАЛО)  - - - - - */

function extension_number_from_SIP_addresses($s) {

    $p = '~^sip\:(\d+)@~';

    if ( preg_match($p, $s, $a) ) {

        $result = $a[1];

    }
    else {

        $result = 'ERROR - function extension_number_from_SIP_addresses( '.$s.' )';

    }


    return $result;

}

/* - - - - -  функция – получение внутреннего номера от SIP-адреса (КОНЕЦ)  - - - - - */




if (!$_SESSION['user']) {
    echo ( out(array("success"=>false,"message"=>"User is undefined")) );
    exit;
} 


$total = compact_array(query_to_array("SELECT count(*) FROM account_sip_caller"));

if (!is_array($total["data"])) echo out(array("success"=>false,"message"=>$total));


$sda_query = <<<EOD
SELECT
    tasc.id as id,
    ta.name as description,
    tas.address as destination,
    tasc.impi as short_name
FROM account_sip_caller tasc
LEFT JOIN account_sip tas
    ON tas.id = tasc.account_sip_id
LEFT JOIN account ta
    ON ta.id = tas.account_id
EOD;

$data = compact_array(query_to_array($sda_query));

if (!is_array($data["data"])) echo out(array("success"=>false,"message"=>$data));


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

/*
сопоставление номеров групп их символьным обозначениям
скрытие несуществующих номеров
*/


$sda_get_groups = $_SESSION["get_groups"];  // 1 - group, 3 - extension

$sda_get_extensions = $_SESSION["get_extensions"];  // 2 - extension


foreach ($data["data"] as &$row_x) {
    
    $row_x[2] = extension_number_from_SIP_addresses( base64_decode( $row_x[2] ) );  // 2 - destination
    
    
    $sda_tick_groups = "NO";
    $sda_tick_extensions = "NO";
    
    foreach ($sda_get_groups as $row_y) {
        
        if ($row_x[2] == $row_y[3]) {
            
            $row_x[2] = $row_y[1];
            
            $sda_tick_groups = "YES";
            
            break;
            
        }
        
    }
    
    foreach ($sda_get_extensions as $row_z) {
        
        if ($row_x[2] == $row_z[2]) {
            
            $sda_tick_extensions = "YES";
            
            break;
            
        }
        
    }
    
    if ( $sda_tick_groups == "NO" && $sda_tick_extensions == "NO" ) {
        
        $row_x[2] = "";
        
    }
    
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


$obj = array("success"=>true);

$obj["total"] = $total['data'][0][0];
$obj["data"] = $data['data'];

echo out($obj);

?>