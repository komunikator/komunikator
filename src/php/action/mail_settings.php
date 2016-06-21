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

function putMailSettings($data) {  // кнопка «Сохранить»
    if ($data) {
        foreach ($data as $data_key => $data_value) {
            $sql = "UPDATE ntn_settings SET value = '$data_value' WHERE param = '$data_key'";
            // file_put_contents('sql',$sql."\n",FILE_APPEND);
            query($sql);


            $sql = <<<EOD
                INSERT INTO ntn_settings (param, value)
                    SELECT '$data_key', '$data_value'
                    FROM DUAL
                    WHERE NOT EXISTS (
                        SELECT 1
                        FROM ntn_settings
                        WHERE param = '$data_key' AND value = '$data_value'
                    )
EOD;

            // file_put_contents('sql',$sql."\n",FILE_APPEND);
            query($sql);
        }
    }

    return true;  // почему-то эта функция всегда должна возвращать true, хотя ниже может быть и другой вариант развития событий
}

function getMailSettings() {  // кнопка «Загрузить»
    $sql = "SELECT s.param, s.value FROM ntn_settings s";
    $data = compact_array(query_to_array($sql));

    $ret = array();
    foreach ($data['data'] as $value) {
        $ret[$value[0]] = $value[1];
    }

    // file_put_contents('$test$.txt',print_r($ret,true));

    return $ret;
}

if (!$_SESSION['user']) {
    echo (out(array("success" => false, "message" => "User is undefined")));
    exit;
}

if (isset($_POST['incoming_trunk'])) {  // кнопка «Сохранить»
    // file_put_contents('$test2$.txt',print_r($_POST,true));
    $data = $_POST;

    if (putMailSettings($data)) {
        echo (out(array("success" => true)));
    } else {
        echo (out(array("success" => false, "message" => "mail_settings saving error")));
    }
} else {  // кнопка «Загрузить»
    // file_put_contents('$test3$.txt',print_r($_REQUEST,true));   
    $data = getMailSettings();
    $object = array("success" => true, "data" => $data);
    echo json_encode($object);
}
?>