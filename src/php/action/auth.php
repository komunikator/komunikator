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
/**
 * @param string user
 * @param string password
 * @assert ('admin','admin')==true
 */

$username = getparam("user");
$extension = getparam("extension");
$password = getparam("password");
$time_offset = getparam("time_offset");
if (is_null($time_offset))
    $time_offset = "-240";
//- - - - - - - - - - - - -  --  - - - - - 
if ($username)
  $extension = $username ;
//- - - - - -  - - - - - -  - - - -  - -  -
if ($username)
    $username = $conn->escapeSimple($username);
if ($extension)
    $extension = $conn->escapeSimple($extension);
if ($password)
    $password = $conn->escapeSimple($password);

if ($password && ($username || $extension)) {
    session_start();
    $_SESSION = array();
//echo ($extension);
    if ($username) {        
        $sql = "SELECT * from users where username = '$username' and password = '$password'";
        if (query_to_array($sql)) {
            $_SESSION['user'] = $username;
            $_SESSION['time_offset'] = $time_offset ;
            $sql = "insert into actionlogs (date,performer,log,ip) values (" . time() . ",\"{$_SESSION['user']}\",\"username $username logged in\", \"{$_SERVER['REMOTE_ADDR']}\")";
            query($sql);
        }
    } /*else*/
    if ($extension) {
        $sql = "SELECT * from extensions where extension = '$extension' and password = '$password'";
        if (query_to_array($sql)) {
            $_SESSION['extension'] = $extension;
            $_SESSION['time_offset'] = $time_offset;
            $sql = "insert into actionlogs (date,performer,log,ip) values (" . time() . ",\"{$_SESSION['extension']}\",\"extension $extension logged in\", \"{$_SERVER['REMOTE_ADDR']}\")";
            query($sql);
        }
    }
    session_write_close();
    if (isset($_SESSION['user']) || isset($_SESSION['extension'])) {
        $out = array("success" => true, "session_name" => session_name(), "session_id" => session_id()/* ,"message"=>"Auth successful" */);
        if (isset($_SESSION['user']))
            $out['user'] = $_SESSION['user'];
        if (isset($_SESSION['extension']))
            $out['extension'] = $_SESSION['extension'];
        echo (out($out));
    }
    else {
        $sql = "insert into actionlogs (date,performer,log,ip) values (" . time() . ",\"{$_SESSION['user']}\",\"failled attempt to log in as unknown : $extension$username\", \"{$_SERVER['REMOTE_ADDR']}\")";
        query($sql);
        echo (out(array("success" => false, "message" => "auth_failed")));
    }
}
?>