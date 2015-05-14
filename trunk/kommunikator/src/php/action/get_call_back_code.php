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

?><?php

if (!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined")));
    exit;
}

$id_call_back = getparam('id');
$callthrough_time = getparam('callthrough_time');
$host = $_SERVER['SERVER_ADDR'];
$host = "http://".$host;

$data = compact_array(query_to_array("SELECT settings FROM call_back WHERE call_back_id = $id_call_back"));
if (!is_array($data["data"]))
    echo out(array("success" => false, "message" => $data));

$data = json_decode($data['data'][0][0]); 

$color_before = $data[6]->{'7'}->{'field4'};
$color_after = $data[7]->{'8'}->{'field4'};
$main_time = $callthrough_time + 5;
$sec_time = $callthrough_time;
$nPage = $data[2]->{'3'}->{'field4'};
$ua =  $data[3]->{'4'}->{'field4'};
$url = $data[5]->{'6'}->{'field4'};

$onUserVisit_check = ($data[0]->{'1'}->{'field2'}=='1') ? 'true' : 'false';
$onUserExit_check = ($data[1]->{'2'}->{'field2'}=='1') ? 'true' : 'false';
$onCheckURLHistory_check =  ($data[5]->{'6'}->{'field2'}=='1') ? 'true' : 'false';
$onUserActivity2_check = ($data[3]->{'4'}->{'field2'}=='1') ? 'true' : 'false';
$onMetrica_check = ($data[4]->{'5'}->{'field2'}=='1') ? 'true' : 'false';
$onCheckNumberPage_check = ($data[2]->{'3'}->{'field2'}=='1') ? 'true' : 'false';

$onUserVisit = $data[0]->{'1'}->{'field5'};
$onUserExit = $data[1]->{'2'}->{'field5'};
$onCheckURLHistory = $data[2]->{'3'}->{'field5'};
$onUserActivity2 = $data[3]->{'4'}->{'field5'};
$onMetrica = $data[4]->{'5'}->{'field5'};
$onSpecificPage = $data[5]->{'6'}->{'field5'};

$call_back_code = <<<EOD
     
<script type='text/javascript'>
        
var komunikatorCallback={
    id:$id_call_back,
    server:'$host',
    timer:{main:'$main_time',sec:'$sec_time',ua:'$ua'},
    nPage:'$nPage',
    timePopupBlocker:'5',
    url:'$url',
    color:{before:'$color_before',after:'$color_after'},
    msg:{
        onUserVisit:'$onUserVisit',
        onUserExit:'$onUserExit',
        onCheckURLHistory:'$onCheckURLHistory',
        onUserActivity2:'$onUserActivity2',
        onMetrica:'$onMetrica',
        onSpecificPage:'$onSpecificPage'},
    trigger:{
        onUserVisit:$onUserVisit_check,
        onUserExit:$onUserExit_check,
        onCheckURLHistory:$onCheckURLHistory_check,
        onUserActivity2:$onUserActivity2_check,
        onMetrica:$onMetrica_check,
        onCheckNumberPage:$onCheckNumberPage_check}
 };
(function(){var x=document.createElement('script');x.type='text/javascript';x.async=true;x.src=komunikatorCallback.server+'/callback/loader.js';
var xx=document.getElementsByTagName('script')[0];xx.parentNode.insertBefore(x,xx);})();
</script>
EOD;

$call_back_code = htmlspecialchars($call_back_code);
$obj = array("success"=>true);
$obj["data"] = $call_back_code;

echo out($obj);