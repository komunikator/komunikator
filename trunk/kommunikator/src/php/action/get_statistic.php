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
?><?

/*  for Windows
  $obj = array("success"=>true);
  $obj["total"] = count($f_data);
  $obj["data"] = array();
  echo out($obj);return;
 */

// ini_set("display_errors", 1);

if (!$_SESSION['user']) {
    echo ( out(array("success" => false, "message" => "User is undefined")) );
    exit;
}

//$extension = getparam("extension");
// - текущее время на сервере  - - - - - - - - - - - - - - - - - - - - - - - - -

function get_time_current() {
    $v_time = exec("date '+%H ч. %M мин.'");
    $v_day = exec("date '+%w'");

    if ($v_day == "0") {
        $v_day = "Вс.";
    } else {

        if ($v_day == "1") {
            $v_day = "Пн.";
        } else {

            if ($v_day == "2") {
                $v_day = "Вт.";
            } else {

                if ($v_day == "3") {
                    $v_day = "Ср.";
                } else {

                    if ($v_day == "4") {
                        $v_day = "Чт.";
                    } else {

                        if ($v_day == "5") {
                            $v_day = "Пт.";
                        } else {

                            if ($v_day == "6") {
                                $v_day = "Сб.";
                            } else {
                                $v_day = "";
                            }
                        }
                    }
                }
            }
        }
    }

    if ($v_day <> "") {
        $time_current = $v_time . ", " . $v_day;
    } else {
        $time_current = $v_time;
    }


    return $time_current;
}

$f_data[] = array('time_current', get_time_current());

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


function lastDayToTimestamp() {
//returns an array containing day start and day end timestamps
    $format = '%d/%m/%Y%H';
    $date = strftime($format);
    $date = strptime($date, $format);
    //print_r($date);	
    $day_start = mktime(/* $date['tm_hour']-$_SESSION['time_offset']/60 */0, 0, 0, $date['tm_mon'] + 1, $date['tm_mday'], ($date['tm_year'] + 1900));
    $day_end = $day_start + (60 * 60 * 24);
    //print_r(array('start'=>date($format,$day_start), 'end'=>date($format,$day_end)));	
    return array('start' => $day_start, 'end' => $day_end);
}

$cur_date = lastDayToTimestamp(); {
    $status = 'offline';
    $query = "select prompt_id, day, start_hour, end_hour, numeric_day FROM time_frames";
    $res = query_to_array($query);
    if (count($res)) {
        $day_week = date('w');
        $hour = date('H') * 1;
        //echo("Current week index '$day_week' : hour '$hour'");
        //$day_week = 2;
        //$hour 	  = 19;
        $status = 'offline';
        foreach ($res as $row)
            if ($row["numeric_day"] == $day_week && $row["start_hour"] <= $hour && $hour < $row["end_hour"])
                $status = 'online';
    };
}

$f_data[] = array('status', $status);
/* $sql =
  <<<EOD
  select count(*)
  from call_logs a
  join call_logs b on b.billid=a.billid and b.ended=1 and b.direction='outgoing' and b.status!='unknown'
  left join extensions x on x.extension=a.caller
  left join extensions x2 on x2.extension=b.called
  where a.ended=1 and a.direction='incoming' and a.status!='unknown'
  and a.time between  {$cur_date['start']}  and {$cur_date['end']} ;
  EOD; */
$sql = <<<EOD
select count(*)
  from call_history 
  where time between  {$cur_date['start']}  and {$cur_date['end']} ;
EOD;
$data = compact_array(query_to_array($sql));
$f_data[] = array('day_total_calls', $data["data"][0][0]);

$sql =
        <<<EOD
     select count(*) from extensions 
	 where coalesce(inuse_count,0)!=0;
EOD;
$data = compact_array(query_to_array($sql));
$f_data[] = array('active_calls', $data["data"][0][0]);

$data = compact_array(query_to_array("SELECT count(*) FROM gateways  where status = 'online'"));
$f_data[] = array('active_gateways', $data["data"][0][0]);

function get_userdata($name) {
    return $_SESSION['userdata'][$name];
}

function set_userdata($ar) {
    session_start();
    if (!$_SESSION['userdata'])
        $_SESSION['userdata'] = array();
    foreach ($ar as $key => $value)
        $_SESSION['userdata'][$key] = $value;
}

function get_cpu() {
    $file = file("/proc/stat");
    $tmp = explode(" ", $file[0]);
    $cpu_user_old = get_userdata("cpu_user");
    $cpu_nice_old = get_userdata("cpu_nice");
    $cpu_sys_old = get_userdata("cpu_sys");
    $cpu_idle_old = get_userdata("cpu_idle");
    $cpu_io_old = get_userdata("cpu_io");
    $cpu_user = $tmp[2];
    $cpu_nice = $tmp[3];
    $cpu_sys = $tmp[4];
    $cpu_idle = $tmp[5];
    $cpu_io = $tmp[6];
    $diff_used = ($cpu_user - $cpu_user_old) + ($cpu_nice - $cpu_nice_old) + ($cpu_sys - $cpu_sys_old) + ($cpu_io - $cpu_io_old);
    $diff_total = ($cpu_user - $cpu_user_old) + ($cpu_nice - $cpu_nice_old) + ($cpu_sys - $cpu_sys_old) + ($cpu_io - $cpu_io_old) + ($cpu_idle - $cpu_idle_old);
    $cpu = $diff_used / $diff_total * 100;
    set_userdata(array("cpu_user" => $cpu_user, "cpu_nice" => $cpu_nice, "cpu_sys" => $cpu_sys, "cpu_idle" => $cpu_idle, "cpu_io" => $cpu_io));
    return $cpu;
}

$f_data[] = array('cpu_use', round(get_cpu(), 2) . " %");

function get_mem() {
    $file = file("/proc/meminfo");

    $data = humanSize(substr($file[0], strpos($file[0], " "), strrpos($file[0], " ") - strpos($file[0], " ")) * 1024);
    $str = $data[0] . " " . $data[2];
    $data = humanSize(substr($file[1], strpos($file[1], " "), strrpos($file[1], " ") - strpos($file[1], " ")) * 1024, $data[1]);
    $str = $data[0] . "/" . $str;
    return $str;
    /*
      $memory["total"]=round(substr($file[0],strpos($file[0]," "),strrpos($file[0]," ")-strpos($file[0]," "))/(1024*1024),2);
      $memory["free"]=round(substr($file[1],strpos($file[1]," "),strrpos($file[1]," ")-strpos($file[1]," "))/(1024*1024),2);
      return $memory["free"].'/'.$memory["total"];
     */
}

$f_data[] = array('mem_use', get_mem());

function get_swap() {
    $file = file("/proc/swaps");
    $tmp = explode("\t", substr($file[1], strpos($file[1], "partition")));

    $data = humanSize($tmp[1] * 1024);
    $str = $data[0] . " " . $data[2];
    $data = humanSize(($tmp[1] - $tmp[2]) * 1024, $data[1]);
    $str = $data[0] . "/" . $str;
    return $str;
    /*
      $swap["total"]=round($tmp[1]/(1024*1024),2);
      $swap["free"]=round(($tmp[1]-$tmp[2])/(1024*1024),2);
      return $swap["free"].'/'.$swap["total"];
     */
}

$f_data[] = array('swap_use', get_swap());

function humanSize($bytes, $limit_index = null) {
//  $type=array("", "kilo", "mega", "giga", "tera", "peta", "exa", "zetta", "yotta");
    $type = array("", "K", "M", "G", "T", "P");
    $index = 0;
    while (($bytes >= 1024) || ($limit_index && $limit_index > $index)) {
        $bytes/=1024;
        $index++;
    }
    $bytes = round($bytes, 2);
    return array($bytes, $index, $type[$index] . "b");
}

function get_space() {
//    ini_set("display_errors", 1);
    $data = humanSize(disk_total_space("/"));
    $str = $data[0] . " " . $data[2];
    $data = humanSize(disk_free_space("/"), $data[1]);
    $str = $data[0] . "/" . $str;

    return $str;
}

$f_data[] = array('space_use', get_space());

// получение времени работы системы (сервера) - функция не используется
function get_uptime() {
    $uptime = exec("uptime");
    $uptime = explode(",", substr($uptime, strpos($uptime, "up") + 3));
    $time = explode(":", $uptime[1]);
    if (!isset($time[1])) {
        $time[1] = str_replace(" min", "", $time[0]);
        $time[0] = 0;
    }
    $uptime = $uptime[0] . " " . trim($time[0]) . "h " . trim($time[1]) . "m";  // добавил функцию trim к двум переменным
    return $uptime;
}

// $f_data[] = array('uptime', get_uptime());

function get_active_user($name = 'user') {
    $path = realpath(session_save_path());
    $files = array_diff(scandir($path), array('.', '..'));
    $i = 0;
    foreach ($files as $file) {
        preg_match('/' . $name . '\|s\:\d+\:\"([^\"]+)\"/', @file_get_contents($path . '/' . $file), $p);
        $s = '';
        if ($p[1]) {
            $i++;
            /*
              $s .= $i++."\t".$p[1];$p=null;
              if (preg_match('/ip\|s\:\d+\:\"([^\"]+)\"/',@file_get_contents($path . '/' . $file),$p));
              $s .= "\t".$p[1];$p=null;
              if (preg_match('/lasttime\|s\:\d+\:\"([^\"]+)\"/',@file_get_contents($path . '/' . $file),$p));
              $s .= "\t".$p[1];$p=null;
              echo ($s."\n");
             */
        }
    }
    return $i;
}

echo $i;

function get_yate_version() {
    exec('yate --version', $ver);
    return $ver;
}

// - получение времени работы yate - - - - - - - - - - - - - - - - - - - - - - -

function get_yate_last_restart() {
    $time_system = time();

    $file_name = "/var/run/yate/yate.pid";
    $time_file = fileatime($file_name);


    $time_difference = $time_system - $time_file;

    $minutes = floor($time_difference / 60) % 60;
    $watch = floor($time_difference / (60 * 60)) % 24;
    $days = floor($time_difference / (60 * 60 * 24));


    $assembly = "";

    $assembly = $assembly . $days . " дн., ";

    if ($watch < 10) {
        $assembly = $assembly . "0" . $watch . " ч. ";
    } else {
        $assembly = $assembly . $watch . " ч. ";
    };

    if ($minutes < 10) {
        $assembly = $assembly . "0" . $minutes . " мин.";
    } else {
        $assembly = $assembly . $minutes . " мин.";
    };


    return $assembly;
}

$f_data[] = array('uptime', get_yate_last_restart());

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - состояние webrtc2sip (ВКЛ. или выкл.) - - - - - - - - - - - - - - - - - - -

function get_state_webrtc2sip() {

    $sda_command_ps = 'ps axu | grep webrtc2sip';
    $sda_output_ps = array();

    exec($sda_command_ps, $sda_output_ps);


    $sda_tick = 'выкл.';

    foreach ($sda_output_ps as $value) {

        $sda_row = strtolower($value);

        $sda_result = strpos($sda_row, 'screen -dms webrtc2sip webrtc2sip');


        if ($sda_result) {

            $sda_tick = 'ВКЛ.';

            break;
        }
    }


    return $sda_tick;
}

$f_data[] = array('Модуль: Звонок<br>с сайта', get_state_webrtc2sip());

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - состояние send_message (ВКЛ. или выкл.) - - - - - - - - - - - - - - - - - -

function get_state_send_message() {

    $sda_command_ps = 'ps axu | grep -i send_message | grep -v grep';
    $sda_output_ps = array();

    exec($sda_command_ps, $sda_output_ps);


    $sda_tick = 'выкл.';

    foreach ($sda_output_ps as $value) {

        $sda_row = strtolower($value);

        $sda_result = strpos($sda_row, '/usr/share/yate/scripts/send_message.php');


        if ($sda_result) {

            $sda_tick = 'ВКЛ.';

            break;
        }
    }


    return $sda_tick;
}

$f_data[] = array('Модуль: Почтовые<br>уведомления', get_state_send_message());

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// - состояние записи звонка(ВКЛ. или выкл.) - - - - - - - - - - - - - - - - - - -

function get_state_callrecord() {

    $sda_command_ps = 'ps axu | grep record.js';
    $sda_output_ps = array();

    exec($sda_command_ps, $sda_output_ps);


    $sda_tick = 'выкл.';

    foreach ($sda_output_ps as $value) {

        $sda_row = strtolower($value);

        $sda_result = strpos($sda_row, '/usr/share/yate/scripts/record.js');


        if ($sda_result) {

            $sda_tick = 'ВКЛ.';

            break;
        }
    }


    return $sda_tick;
}

$f_data[] = array('Модуль: Запись<br>Звонков', get_state_callrecord());

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


$f_data[] = array('version', $cur_ver);
//$cur_ver - переменная из config.php

/* функция заменена (см. выше)
  function get_yate_last_restart(){
  $time = time()-fileatime('/var/run/yate/yate.pid');
  //return date("d H:i",$s);
  $seconds = $time%60;
  $seconds=($seconds>9)?$seconds:'0'.$seconds;
  $mins = floor($time/60)%60;
  $mins=($mins>9)?$mins:'0'.$mins;
  $hours = floor($time/60/60)%24;
  $hours=($seconds>9)?$hours:'0'.$hours;
  $days = floor($time/60/60/24);

  return $days.' day '.$hours.':'.$mins;//.':'.$seconds;
  }
  $f_data[] = array('uptime', get_yate_last_restart());
 */

//$f_data[] = array('active_user',get_active_user());
//$f_data[] = array('active_extension',get_active_user('extension'));
//$f_data[] = array('yate_version',get_yate_version());
//exec('ps -ef | grep -c defunct',$def);
//$f_data[] = array('defunct processes',$def[0]-2);


$obj = array("success" => true);

$obj["total"] = count($f_data);
$obj["data"] = $f_data;

echo out($obj);
?>
