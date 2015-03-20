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

require_once("php/socketconn.php");

function click_to_call($caller, $called, $site, $callthrough_time) {
    $command = "click_to_call $caller $called $site $callthrough_time";

    $socket = new SocketConn;
    if ($socket->error == "") {
        $obj = array("success" => true);
        $socket->command($command);
        $sql = "insert into actionlogs (date,performer,log,ip) values (" . time() . ",\"{$_SESSION['extension']}\",\"$command\", \"{$_SERVER['REMOTE_ADDR']}\")";
        query($sql);
    } else {
        $obj = array("success" => false);
        $obj['message'] = "Can't make call. Please contact your system administrator.";
        echo out($obj);
    }
}

header("Content-Type: application/javascript");
$callback = $_GET["callback"];
$called = getparam("number");
$call_back_id = $_GET["call_back_id"];

$sql1 = "SELECT destination, name_site, callthrough_time FROM call_back WHERE call_back_id = $call_back_id";
$res = query_to_array($sql1);

// - - - - - - - error checking - - - - - - -

$check_called = str_replace(' ', '', $called);
if (!$called || $check_called == '') {
    echo $callback . '({"warning":"Phone number is undefined"})';
    exit;
}

if (!$call_back_id || $call_back_id == '' || count($res) == 0) {
    echo $callback . '({"warning":"Caller undefined"})';
    exit;
}

$caller = $res[0]["destination"];
$site = urlencode($res[0]["name_site"]);
$callthrough_time = $res[0]["callthrough_time"];

if ($_SESSION['last_action']) {
    if ((time() - $_SESSION['last_action']) < ($callthrough_time + 5)) {
        $message = "You can reorder the call after " . ($callthrough_time + 5) . " seconds";
        echo $callback . '({"warning":"' . $message . '"}';
        exit;
    }
}

// - - - - - - - error checking(END) - - - - - - -

$_SESSION['last_action'] = time();
session_write_close();

$sql_action_call = <<<EOD
SELECT * FROM(
SELECT
    CASE
        WHEN x.extension IS NOT NULL AND x2.extension IS NOT NULL THEN 'internal'
        WHEN x.extension IS NOT NULL THEN 'outgoing'
        ELSE 'incoming'
    END type,
    a.caller,
    b.called,
    CASE 
        WHEN a.status!="answered" OR b.status!="answered" 
            THEN "progressing" 
        ELSE a.status 
    END status
FROM call_logs a  
    JOIN call_logs b ON b.billid=a.billid AND b.ended=0 AND b.direction='outgoing' AND b.status!='unknown'
    LEFT JOIN extensions x ON x.extension=a.caller
    LEFT JOIN extensions x2 ON x2.extension=b.called
    WHERE a.ended=0 AND a.direction='incoming' AND
    a.status!='unknown' AND b.called = $called AND
    (a.status="answered" AND b.status="answered")) a
EOD;

if (strlen($caller) == 2) {
    $sql = "SELECT group_id FROM groups WHERE extension = $caller";
    $res = query_to_array($sql);
    if ($res) {

        $group_id = $res[0]["group_id"];
        $last_priority = NULL;
        $count = round($callthrough_time / 11, 0, PHP_ROUND_HALF_DOWN);

        for ($i = 0; $i < $count; $i++) {
            $sql1 = "
                 SELECT e.extension as number, gp.priority
            FROM 
                (SELECT extensions.extension_id, extension, inuse_last 
                 FROM extensions,group_members 
                 WHERE extensions.extension_id=group_members.extension_id
                 AND group_members.group_id=$group_id
                 AND coalesce(extensions.inuse_count,0)=0
                 AND extensions.expires is not NULL) e 
            LEFT JOIN group_priority gp ON e.extension_id = gp.extension_id and gp.group_id = $group_id
            $last_priority 
	    ORDER BY priority DESC, inuse_last";
            $res1 = query_to_array($sql1);

            $caller = $res1[0]["number"];

            if (!$res1 && $last_priority == NULL) {
                $sql_count = "SELECT count(extension_id) as count FROM group_members WHERE group_id = $group_id";
                $res_count = query_to_array($sql_count);
                $jsonResponse = ($res_count[0]['count'] == 0) ? '({"warning":"Caller undefined(look at the group members)"})' : '({"success":"false"})';
                echo $callback . $jsonResponse;
                exit;
            }

            if (!$res1 && $last_priority !== NULL) {
                $last_priority = NULL;
                $res1 = query_to_array($sql1);
                if (!$res1) {
                    echo $callback . '({"success":"false"})';
                    break;
                };
            }
            click_to_call($caller, $called, $site, 10);

            for ($k = 0; $k < 3; $k++) {
                sleep(3);

                $data = compact_array(query_to_array($sql_action_call));
                $total = count($data["data"]);

                if ($total > 0) {
                    $stop = true;
                    break;
                }
            }
            if ($stop) {
                echo $callback . '({"success":"true"})';
                break;
            }
            if ($i == ($count - 1) && !$stop) 
                echo $callback . '({"success":"false"})';
            
            $last_priority = (!$res1[0]["priority"]) ? NULL : "WHERE coalesce(gp.priority, 0) < " . $res1[0]["priority"];
            sleep(2);
        }
    } else {
        echo $callback . '({"warning":"Caller group undefined"})';
        exit;
    }
} else {
    $sql_busyness = "SELECT inuse_count FROM extensions WHERE extension = $caller";
    $res_busyness = query_to_array($sql_busyness);

    if ($res_busyness[0]['inuse_count'] == 0) {
        click_to_call($caller, $called, $site, $callthrough_time);

        $count = round($callthrough_time / 4, 0, PHP_ROUND_HALF_DOWN);
        for ($i = 0; $i < $count; $i++) {
            sleep(4);
            $data = compact_array(query_to_array($sql_action_call));
            $total = count($data["data"]);

            if ($total > 0) {
                echo $callback . '({"success":"true"})';
                break;
            }

            if ($i == ($count - 1) && $total == 0) 
                echo $callback . '({"success":"false"})';
            
        }
    } else 
        echo $callback . '({"success":"false"})';
    
}
