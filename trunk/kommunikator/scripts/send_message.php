#!/usr/bin/php -q
<?
require_once('libyate.php');
require_once('lib_smtp.inc.php');
require_once('lib_queries.php');
set_time_limit(600);

function debug($msg) {
    Yate::Debug('send_mail.php: ' . $msg);
}
if (!isset($incoming_trunks))
    $incoming_trunks = array();

// Always the first action to do 
Yate::Init();

Yate::Install('call.cdr', 120);
// Ask Yate to restart this script if it dies unexpectedly
Yate::SetLocal('restart',true);

Yate::Debug(true);
setMultifonOpt(2,'Multifon'); // Send calls to both SIP & cell phone

// The main loop. We pick events and handle them
for (;;) {
    $ev=Yate::GetEvent();
    if ($ev === false)
        break;
    if ($ev === true)
        continue;
        
    // We are sure it's the timer message
    if ($ev->type == 'incoming') {
        switch ($ev->name) {
            case 'call.cdr':
                $ev->Acknowledge(); 
                
                if ($ev->GetValue('direction') == 'incoming' &&
                    $ev->GetValue('operation') == 'initialize' &&
                    $ev->GetValue('username') ) {
		//Звонок на входящую линию
                //сохраняем billid звонка
                    if (!isset($incoming_trunks[$ev->GetValue('billid')]))
                        $incoming_trunks[$ev->GetValue('billid')] = $ev->GetValue('called');
                    if (strlen($ev->GetValue('caller')) > 3 && strlen($ev->GetValue('called')) > 3) {
			//входящий звонок
                        $query = "SELECT value  FROM ntn_settings where param = 'incoming_trunk' and value = 'true'";
                        $res = query_to_array($query);
                        if(count($res)) {
                            $params = array();
                            $params['incoming_trunk'] = $incoming_trunks[$ev->GetValue('billid')];
                            $params['ftime'] = strftime(TIME_FMT, $ev->GetValue('time'));
                            $params['caller'] = $ev->GetValue('caller');
                            $params['called'] = $ev->GetValue('called');
                            $text = null;
                            $query = "SELECT value FROM ntn_settings where param = 'incoming_trunk_text'";
                            $res = query_to_array($query);
                            if (count($res))
                                $text = $res[0]["value"];                    
                            $subject = 'Звонок на входящую линию <called>';
                            
                            $text = format_msg($text,$params);
                            $subject = format_msg($subject,$params);
                            send_mail($text,$subject);
                        }
                    }
                }
                
                if ($ev->GetValue('operation') == 'update' &&
                    $ev->GetValue('reason')    == 'queued') {
                //звонок на группу
                
                };
                
                if ($ev->GetValue('direction') == 'outgoing' &&
                    $ev->GetValue('operation') == 'finalize') {
                    //завершился звонок
                    $params = array();
                    $params['incoming_trunk'] = $incoming_trunks[$ev->GetValue('billid')];
                    unset ($incoming_trunks[$ev->GetValue('billid')]);
                    
                    $params['username'] = $ev->GetValue('username');
                    $params['caller'] = ($params['username'])? $params['username'] : $ev->GetValue('caller');
                    $params['called'] = $ev->GetValue('called');
                    $params['ftime'] = strftime(TIME_FMT, $ev->GetValue('time'));
                    // Do not log internal calls
                    //if (strlen($params['caller']) <= 3 && strlen($params['called']) <= 3)
                    //	return;
                    $params['duration'] = $ev->GetValue('duration');
                    $params['status'] = $ev->GetValue('status') . ' ' . $ev->GetValue('reason');
                    $params['type'] = (strlen($params['caller']) <= 3)? 'Исходящий' : 'Входящий';
                    
                    $is_fax = false;
                    $filename = null;	
                    if (strpos($ev->GetValue('chan'), 'fax') === false) {
                        $subject = 'Звонок';
                        if ($ev->GetValue('status') != 'answered')
                            $subject .= ' не';
                        $subject .= ' принят';
                    }
                    else {
                        $is_fax = true;
                        $subject = 'Факс ';
                        $filename = $ev->GetValue('address');
                        if (is_file($filename)) {
                        //unlink($filename);
                        } else
                            $subject .= 'не принят';
                    }
                    $subject .= ' от ' . $params['caller'] . ' ' . $params['ftime'];
                    
                    $is_internal_call =	(strlen($params['caller']) <= 3 && strlen($params['called']) <= 3);
                    if ($is_internal_call) {
                        $query = "SELECT value  FROM ntn_settings where param = 'internal_call' and value = 'true'";
                        $res = query_to_array($query);
                    }	
                    if ($is_internal_call && count($res) || !$is_internal_call) {                          
                        $text = null;
                        $query = "SELECT value FROM ntn_settings where param = 'incoming_call_text'";
                        $res = query_to_array($query);
                        if (count($res))
                            $text = $res[0]["value"];                    
                        //$query = "SELECT value  FROM ntn_settings where param = 'exclude_called' and value = '$params[called]'";
                        $query = "SELECT value  FROM ntn_settings where param = 'exclude_called' and value = '$params[called]' and description > '".(time()-20)."'";
                        $res = query_to_array($query);
                        if(!count($res)) {
                            if (strlen($params['caller']) <= 3) {
                            //Исходящий
                                $query = "SELECT value  FROM ntn_settings where param = 'outgoing_call' and value = 'true'";
                                $res = query_to_array($query);
                            }
                            else {
                                $query = "SELECT value  FROM ntn_settings where param = 'incoming_call' and value = 'true'";
                                $res = query_to_array($query);
                            }
                            if (count($res)) {
                                $text = format_msg($text,$params);
                                $subject = format_msg($subject,$params);
                                send_mail($text,$subject,$is_fax,$filename);
                            }
                        }
                    }
                }
                break;
        }
    }
}
Yate::Debug('PHP: bye!');
?>