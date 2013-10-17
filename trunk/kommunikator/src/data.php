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

?><?php

//ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] .'/tmp/');
//ini_set('session.gc_maxlifetime', 2592000); //30 day
//ini_set('session.cookie_lifetime', 2592000); //30 day

ini_set('session.name', 'session');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
//ini_set('session.use_cookies', 0);
ini_set('session.use_only_cookies', 0);
session_start();
//session_write_close(); //Запросы будут выполняться последовательно
//ini_set("error_reporting",'');
//ini_set("display_errors",true);

$log_file = 'log.log';
ini_set("log_errors",true);
ini_set("error_log",$log_file); 

require_once("libyate.php");
require_once("lib_queries.php");

$date_format = "d.m.y H:i:s"; 

if (!$conn) die('Database Connection Failed');

function handle_pear_error2($e) {
    die($e->getMessage().' '.print_r($e->getUserInfo(),true));
//echo json_encode(array("success"=>false,"message"=>"Error:'".$e->getMessage()/*.' '.print_r($e->getUserInfo(),true)*/));
};

require_once 'PEAR.php';
PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'handle_pear_error2');

//ini_set("session.use_only_cookies", "0");

require_once "php/util.php";

$action = getparam("action"); 

if ($action!='auth' && !(isset($_SESSION['user']) || isset($_SESSION['extension']))) {
    echo out(array("success"=>false,"message"=>"session_failed"));
    exit;
}

if (!isset($_SESSION['time_offset'])) $_SESSION['time_offset'] = 240;
$action_path = "php/action"; 

if (file_exists("$action_path/$action".".php")) include "$action_path/$action".".php"; 
else 
   echo out(array("success"=>false,"message"=>"Unknown action '".$action."'"));

?>