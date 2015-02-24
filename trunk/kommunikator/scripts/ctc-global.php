#!/usr/bin/php -q
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
require_once("libyate.php");
set_time_limit(600);

// Initiate a call once we know the target
function callInitiate($target, $ev) {
    Yate::Debug("Initiating dialout call to '$target'");
    $m = new Yate("call.execute");
    $m->params = $ev->params;
    $m->id = "";
    $m->SetParam("callto", "external/nodata/ctc-dialer.php");
    $m->SetParam("direct", $target);
    $m->SetParam("caller", $ev->GetValue("real_called"));
    $m->SetParam("call_from", $ev->GetValue("call_from"));
    $m->SetParam("called", $ev->GetValue("real_caller"));
    $m->SetParam("cdrtrack", "true");
    $m->Dispatch();
}

// Routing failed, the number may be invalid
function routeFailure($error, $ev) {
    $number = $ev->GetValue("called");
    Yate::Output("Failed routing in ctc-global to '$number' with error '$error'");
}

// Always the first action to do 
Yate::Init();
// Only install a handler for the engine.command message
Yate::Install("engine.command");
// Ask Yate to restart this script if it dies unexpectedly
Yate::SetLocal("restart", true);

// The main loop. We pick events and handle them
for (;;) {
    $ev = Yate::GetEvent();
    if ($ev === false)
        break;
    if ($ev === true)
        continue;
    switch ($ev->type) {
        case "incoming":
            // We are sure it's the timer message
            $ev->Acknowledge();
            if ($ev->name == "engine.command") {
                $line = $ev->GetValue("line");
                if (substr($line, 0, 14) == "click_to_call ") {
                    $cmd = substr($line, 14, strlen($line));
                    $cmd = explode(" ", $cmd);
                    $caller = $cmd[0];
                    $called = $cmd[1];

                    $m = new Yate("call.route");
                    $m->params["caller"] = "ctc"; // $caller;
                    $m->params["called"] = $caller; //$called;
                    $m->params["real_caller"] = $caller;
                    $m->params["real_called"] = $called;
                    $m->params["already-auth"] = "yes";
                    if ($cmd[2]) {
                        $m->params["call_from"] = $cmd[2]; 
                    }
                    $m->params["maxcall"] = ($cmd[3]) ? $cmd[3] * 1000 : 25000; 
                    $m->Dispatch();
                }
            }
            break;
        case "answer":
            // Use the return of the routing message

            if ($ev->name == "call.route") {
                if ($ev->handled && ($ev->retval != "") && ($ev->retval != "-") && ($ev->retval != "error"))
                    callInitiate($ev->retval, $ev);
                else
                    routeFailure($ev->GetValue("error"), $ev);
            }
            break;
        default:
            Yate::Debug("PHP Event: " . $ev->type);
    }
}

Yate::Debug("PHP: bye!");
/* vi: set ts=8 sw=4 sts=4 noet: */
?>