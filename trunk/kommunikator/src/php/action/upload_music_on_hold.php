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

/**
 * Example processing of raw PUT/POST uploaded files - with hint, how to save the uploaded file.
 * File metadata should be sent through appropriate HTTP headers. Raw data are read from the standard input.
 * The response should be a JSON encoded string with these items:
 *   - success (boolean) - if the upload has been successful
 *   - message (string) - optional message, useful in case of error
 */
//define('UPLOAD_DIR', '/tmp/upload/');
define('UPLOAD_DIR', $uploaded_prompts . '/moh/');

/*
 * You should check these values for XSS or SQL injection.
 */
$mimeType = $_SERVER['HTTP_X_FILE_TYPE'];
$size = $_SERVER['HTTP_X_FILE_SIZE'];
$fileName = $_SERVER['HTTP_X_FILE_NAME'];

/*
 * Open the file you want to save the uploaded data to.
 * In real environment make sure, that:
 * - the directory exists
 * - the directory is writeable
 * - a file with the same name does not exist
 */
$target = fopen(UPLOAD_DIR . $fileName, 'w');
if (!$target) {
    _response(false, "Error writing to file '" . UPLOAD_DIR . $fileName . "'");
}

/*
 * Open the input stream.
 */
$fp = fopen('php://input', 'r');
$realSize = 0;
$data = '';

/*
 * Read data from the input stream and write them into the file.
 */
if ($fp) {
    while (!feof($fp)) {
        $data = fread($fp, 1024);
        $realSize += strlen($data);

        fwrite($target, $data);
    }
} else {
    _response(false, 'Error saving file');
}

fclose($target);
_response();

//---
function _log($value) {
    error_log(print_r($value, true));
}

function _response($success = true, $message = 'OK') {
    $response = array(
        'success' => $success,
        'message' => $message
    );

    global $fileName;
    if ($success) {
        $rows[] = array('music_on_hold' => "'$fileName'", 'description' => "''", 'file' => "'$fileName'");

        $action = 'create_music_on_hold';
        require_once("create.php");
    } else {
        echo json_encode($response);
        exit();
    }
}

?>