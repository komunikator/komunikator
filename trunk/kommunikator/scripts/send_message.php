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

function getValueFromNtnSettings($param, $default) {
    $query = "SELECT value FROM ntn_settings WHERE param = '$param'";
    $res = query_to_array($query);
    
    $value = $default;
    
    if (count($res)) {
        $value = $res[0]['value'];
    }
    
    return $value;
}


require_once('libyate.php');
require_once('lib_smtp.inc.php');
require_once('lib_queries.php');

set_time_limit(600);

function debug($msg) {
    Yate::Debug('send_mail.php: ' . $msg);
}

if (!isset($incoming_trunks)) {
    $incoming_trunks = array();
}

// Always the first action to do 
Yate::Init();

Yate::Install('call.cdr', 120);

// Ask Yate to restart this script if it dies unexpectedly
Yate::SetLocal('restart', true);

Yate::Debug(true);

// The main loop. We pick events and handle them
for (;;) {
    $ev = Yate::GetEvent();
    
    if ($ev === false)
        break;
    
    if ($ev === true)
        continue;
        
    // We are sure it's the timer message
    if ($ev->type == 'incoming') {
        
        switch ($ev->name) {
            case 'call.cdr' : $ev->Acknowledge();
                
                // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                if ($ev->GetValue('direction') == 'incoming' &&
                    $ev->GetValue('operation') == 'initialize' &&
                    $ev->GetValue('username') ) {
                    
		    // звонок на входящую линию
                    
                    // сохраняем billid звонка
                    if (!isset($incoming_trunks[$ev->GetValue('billid')]))
                        $incoming_trunks[$ev->GetValue('billid')] = $ev->GetValue('called');
                    
                    if (strlen($ev->GetValue('caller')) > 3 && strlen($ev->GetValue('called')) > 3) {
                        
			// Событие: Входящий вызов (с указанием шлюза)
                        
                        $query = "SELECT value FROM ntn_settings WHERE param = 'incoming_trunk' and value = 'true'";
                        $res = query_to_array($query);
                        
                        if (count($res)) {
                            
                            $params = array();
                            
                            $params['incoming_trunk'] = $incoming_trunks[$ev->GetValue('billid')];
                            $params['ftime'] = strftime(TIME_FMT, $ev->GetValue('time') + 60 * 60 * $def_time_offset);
                            $params['caller'] = $ev->GetValue('caller');
                            // $params['called'] = $ev->GetValue('called');  // если снять комментарий, то элементы incoming_trunk и caller будут идентичны

                            $text = getValueFromNtnSettings('incoming_trunk_text', '');
                            $subject = getValueFromNtnSettings('incoming_subject', 'Звонок на внешнюю линию <incoming_trunk>');
                            
                            $text = format_msg($text, $params);
                            $subject = format_msg($subject, $params);

                            // Доступные параметры: <incoming_trunk>, <ftime>, <caller>, /*<called>*/ (их можно использовать как в заголовке так и в тексте)

                            send_mail(getValueFromNtnSettings('from', ''), getValueFromNtnSettings('password', ''), getValueFromNtnSettings('fromname', ''), getValueFromNtnSettings('email', ''), $subject, $text, null);

                        }
                    }
                }
                // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                if ($ev->GetValue('operation') == 'update' &&
                    $ev->GetValue('reason') == 'queued') {

                    // звонок на группу
                
                };
                // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                
                // исходящий вызов (по-моему не только исходящий, аргументы? - см. ниже)
                
                if ($ev->GetValue('direction') == 'outgoing' &&
                    $ev->GetValue('operation') == 'finalize') {
                    
                    // завершился звонок
                    
                    $params = array();
                    
                    $params['incoming_trunk'] = $incoming_trunks[$ev->GetValue('billid')];
                    unset ($incoming_trunks[$ev->GetValue('billid')]);
                    
                    $params['username'] = $ev->GetValue('username');
                    $params['caller'] = ($params['username']) ? $params['username'] : $ev->GetValue('caller');
                    $params['called'] = $ev->GetValue('called');
                    $params['ftime'] = strftime(TIME_FMT, $ev->GetValue('time') + 60 * 60 * $def_time_offset);
                    
                    // Do not log internal calls
                    // if (strlen($params['caller']) <= 3 && strlen($params['called']) <= 3) return;
                    
                    $params['duration'] = $ev->GetValue('duration');
                    $params['status'] = $ev->GetValue('status') . ' ' . $ev->GetValue('reason');
                    $params['type'] = (strlen($params['caller']) <= 3) ? 'Исходящий' : 'Входящий';
                    
                    
                    if (strpos($ev->GetValue('chan'),'fax') === false) {  // строка «fax» не найдена - т.е. режим, не требующий передачи файла

                        $filename = null;

                        if ($ev->GetValue('status') != 'answered') {
                            $subject = getValueFromNtnSettings('outgoing_subject_call_not_accepted', 'Звонок не принят от');
                        } else {
                            $subject = getValueFromNtnSettings('outgoing_subject_call_accepted', 'Звонок принят от');
                        }
                                    
                    } else {
                        
                        $filename = $ev->GetValue('address');

                        if (is_file($filename)) {
                            $subject = getValueFromNtnSettings('outgoing_subject_fax_accepted', 'Факс принят от');
                        } else {
                            $subject = getValueFromNtnSettings('outgoing_subject_fax_not_accepted', 'Факс не принят от');
                        }
                        
                    }


                    // А вот тут были проблемы:
                    //   некорректно отображались параметры <...>, т.е. их нужно подбирать
                    //   и их (параметры <...>) лучше добавить выше [ например, $subject = getValueFromNtnSettings('outgoing_subject_fax_accepted', 'Факс принят от <caller>'); ]
                    //   иначе они будут добавляться к набранным пользователем заголовкам
                    //   и если он их уже использовал, то они будут продублированы

                    // $subject = $subject . ' ' . $params['caller'] . ' ' . $params['ftime'];
                    

                    $is_internal_call =	( strlen($params['caller']) <= 3 && strlen($params['called']) <= 3 );
                    
                    if ($is_internal_call) {

                        // Событие: Внутренний вызов

                        $query = "SELECT value FROM ntn_settings WHERE param = 'internal_call' AND value = 'true'";
                        $res = query_to_array($query);
                    }
                    
                    if ($is_internal_call && count($res) || !$is_internal_call) {
                        $text = getValueFromNtnSettings('incoming_call_text', '');

                        // панель «Почтовые уведомления» не создает параметр exclude_called

                        // запись (параметр exclude_called) создает или изменяет файл get_state.php
                        // требуется в работе панели телефонии Komunikator (1C)
                        
                        $query = "SELECT value FROM ntn_settings WHERE param = 'exclude_called' AND value = '$params[called]' AND description > '" . (time()-20) . "'";
                        $res = query_to_array($query);
                        
                        if (!count($res)) {
                            
                            if (strlen($params['caller']) <= 3) {

                                // Событие: Исходящий вызов
                                
                                $query = "SELECT value FROM ntn_settings WHERE param = 'outgoing_call' AND value = 'true'";
                                $res = query_to_array($query);
                            }
                            else {

                                // Событие: Входящий вызов
                                
                                $query = "SELECT value FROM ntn_settings WHERE param = 'incoming_call' AND value = 'true'";
                                $res = query_to_array($query);
                            }
                            
                            if (count($res)) {
                                $text = format_msg($text, $params);
                                $subject = format_msg($subject, $params);
                                
                                send_mail(getValueFromNtnSettings('from', ''), getValueFromNtnSettings('password', ''), getValueFromNtnSettings('fromname', ''), getValueFromNtnSettings('email', ''), $subject, $text, $filename);
                            }
                            
                        }
                    }
                }
                // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

                break;
        }
        
    }

}

Yate::Debug('PHP: bye!');

?>