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
    echo (out(array("success" => false, "message" => "User is undefined")));
    exit;
}

/* - - - - -  получение значений полей «Псевдоним» и «Цвет кнопки» по нажатию кнопки «Получить код» (НАЧАЛО)  - - - - - */

$sda_short_name = getparam('sda_short_name');
$sda_button_color = getparam('sda_button_color');

/* - - - - -  получение значений полей «Псевдоним» и «Цвет кнопки» по нажатию кнопки «Получить код» (КОНЕЦ)  - - - - - */


/* - - - - -  получение значения переменной $sda_c2c_from – адрес получателя (НАЧАЛО)  - - - - - */

$sda_query = <<<EOD
SELECT
    ta.email as ta_email
FROM account_sip_caller tasc
LEFT JOIN account_sip tas
    ON tas.id = tasc.account_sip_id
LEFT JOIN account ta
    ON ta.id = tas.account_id
WHERE tasc.impi = '$sda_short_name'
EOD;

$data = compact_array(query_to_array($sda_query));

if (!is_array($data["data"]))
    echo out(array("success" => false, "message" => $data));


$sda_c2c_from = base64_encode($data["data"][0][0]);

/* - - - - -  получение значения переменной $sda_c2c_from – адрес получателя (КОНЕЦ)  - - - - - */


/* - - - - -  получение значения переменной $sda_c2c_cls – стиль кнопки (НАЧАЛО)  - - - - - */

switch ($sda_button_color) {

    case "btn" : $sda_c2c_cls = 'btn';
        break;

    case "btn btn-primary" : $sda_c2c_cls = 'btn btn-primary';
        break;

    case "btn btn-info" : $sda_c2c_cls = 'btn btn-info';
        break;

    case "btn btn-success" : $sda_c2c_cls = 'btn btn-success';
        break;

    case "btn btn-warning" : $sda_c2c_cls = 'btn btn-warning';
        break;

    case "btn btn-danger" : $sda_c2c_cls = 'btn btn-danger';
        break;

    case "btn btn-inverse" : $sda_c2c_cls = 'btn btn-inverse';
        break;

    default : $sda_c2c_cls = 'btn';
}

/* - - - - -  получение значения переменной $sda_c2c_cls – стиль кнопки (КОНЕЦ)  - - - - - */

// $sda_src = 'js/c2c-api.js';
$sda_src = 'http://komunikator.ru/js/c2c-api.js';
$sda_host = $_SERVER['SERVER_ADDR'];

$sda_button_code = <<<EOD
<script src='$sda_src'></script>

<script>

    c2c.horizontal_align = 'right'; // right / left
    c2c.vertical_align = 'top';	// bottom / top	
    c2c.horizontal_margin = '-60'; // значение в пикселях
    c2c.vertical_margin = '10';	// значение в пикселях
    c2c.button_type = 'vertical'; // vertical / horizontal

    c2c.from = '$sda_c2c_from';

    c2c.cls = '$sda_c2c_cls';

    c2c.text = 'Позвонить нам';
	
    c2c.calling_text = 'Установка соединения...';
    c2c.ringing_text = 'Вызов абонента...';
    c2c.in_call_text = 'Слушаем Вас, говорите';
    c2c.call_terminated_text = 'Вызов завершен';
    c2c.call_terminating_text = 'Завершение вызова...';
    c2c.fail_to_find_user_account_text = 'Нет такой учетной записи';

    c2c.config = {
        websocket_proxy_url: 'ws://$sda_host:10060',
    };

    c2c.init();
</script>
EOD;

$sda_button_code = htmlspecialchars($sda_button_code);
$obj = array("success" => true);
$obj["data"] = $sda_button_code;

echo out($obj);
