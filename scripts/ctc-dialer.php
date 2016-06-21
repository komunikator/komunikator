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
/*
 * Callback system dialer - call leg A script
 */
require_once("libyate.php");
set_time_limit(600);

$ourcallid = "ctc-dialer/" . uniqid(rand(), 1);
$billid = rand();

function secondCall($route, $ev) {
    global $partycallid;
    global $real_called;
    global $billid;
    global $callFrom;
    // global $place;
    //$real_caller = '79297333104';
    $m = new Yate("chan.masquerade");
    $m->params = $ev->params;
    $m->SetParam("message", "call.execute");
    $m->SetParam("callto", $route);
    //$m->SetParam("caller", $real_caller);
    $m->SetParam("called", $real_called);
    $m->SetParam("id", $partycallid);
    $m->SetParam("billid", $billid);
    $m->SetParam("call_from", $callFrom);
    $m->Dispatch();
}

/* Always the first action to do */
Yate::Init();

Yate::SetLocal("id", $ourcallid);
Yate::SetLocal("disconnected", "true");

Yate::Install("call.answered", 50, "targetid", $ourcallid);

$exit = false;

/* The main loop. We pick events and handle them */
for (;;) {
    if ($exit)
        break;
    $ev = Yate::GetEvent();
    /* If Yate disconnected us then exit cleanly */
    if ($ev === false)
        break;
    /* No need to handle empty events in this application */
    if ($ev === true)
        continue;
    /* If we reached here we should have aC valid object */
    switch ($ev->type) {
        case "incoming":
//	    Yate::Debug("PHP Incoming: " . $ev->name);
            switch ($ev->name) {
                case "call.execute":

                    $m = new Yate("call.execute");
                    $m->params = $ev->params;
                    $real_caller = $ev->GetValue("real_caller");
                    $real_called = $ev->GetValue("real_called");
                    $m->params["callto"] = $ev->GetValue("direct");
                    $m->SetParam("id", $ourcallid);
                    $m->SetParam("billid", $billid);
                    $callFrom = $ev->GetValue("call_from");
                 //   $m->SetParam("callername", $sd);
                    $m->SetParam("call_from", $callFrom);
                    $m->Dispatch();
                    $ev->handled = true;
                    break;
                case "call.answered":
                    $partycallid = $ev->GetValue("id");
                    $m = new Yate("call.route");
                    $m->params["true_party"] = $partycallid;
                    //  $m->params["true_party"] = $ev->GetValue("id");
                    $m->params["caller"] = $real_caller;
                    $m->params["called"] = $real_called;
                    $m->params["already-auth"] = "yes";
                   // $m->params["callername"] = $sd;
                    $m->params["call_from"] = $callFrom;
                    $m->Dispatch();
                    break;
            }
            /* This is extremely important.
              We MUST let messages return, handled or not */
            if ($ev) {

                $ev->Acknowledge();
            }
            break;
        case "answer":
            if ($ev->name == "call.route") {//print_r($ev);echo("////////////");
                $route = $ev->retval;
                if ($ev->retval) {
                    secondCall($route, $ev); /* print_r($ev);echo("4444"); */
                }
                //   echo ("3-------------------------------" . $ev->GetValue("callername") . "-----------------------");}
                else
                    $exit = true;
            }
            break;
//	default:
//	    Yate::Debug("PHP Event: " . $ev->type);
    }
}

Yate::Output("PHP: bye!");
/* vi: set ts=8 sw=4 sts=4 noet: */
?>