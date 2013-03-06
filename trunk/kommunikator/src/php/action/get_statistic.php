<?
//    ini_set("display_errors", 1);
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 

function lastDayToTimestamp() {
//returns an array containing day start and day end timestamps
    $format = '%d/%m/%Y%H';
    $date = strftime($format);
    $date=strptime($date,$format);
    //print_r($date);	
    $day_start=mktime(/*$date['tm_hour']-$_SESSION['time_offset']/60*/0,0,0,$date['tm_mon']+1,$date['tm_mday'],($date['tm_year']+1900));
    $day_end=$day_start+(60*60*24);
    //print_r(array('start'=>date($format,$day_start), 'end'=>date($format,$day_end)));	
    return array('start'=>$day_start, 'end'=>$day_end);
} 
$cur_date=lastDayToTimestamp(); {
    $status = 'offline';
    $query =  "select prompt_id, day, start_hour, end_hour, numeric_day FROM time_frames"; 
    $res = query_to_array($query);
    if(count($res)) {
        $day_week = date('w');
        $hour 	  = date('H')*1;
        //echo("Current week index '$day_week' : hour '$hour'");
        //$day_week = 2;
        //$hour 	  = 19;
        $status = 'offline';
        foreach ($res as $row)
            if ($row["numeric_day"]==$day_week && $row["start_hour"]<=$hour && $hour<$row["end_hour"])
                $status = 'online';
    };
}

$f_data[] = array('status',$status);

$sql=
<<<EOD
SELECT count(*) from (
    SELECT * from (
    SELECT a.time, a.caller, b.called, a.duration, a.status  FROM 
	(SELECT * FROM call_logs where direction = 'incoming' and status!='unknown' and ended = 1
	) a
	join 
	(SELECT * FROM call_logs where direction = 'outgoing' and status!='unknown' and ended = 1) b 
 	on a.billid = b.billid) c
    /* union 
    SELECT a.time, a.caller, a.called, a.duration, a.status  FROM 
	(SELECT * FROM call_logs where direction = 'incoming' and status!='unknown' and ended = 1
	and exists (SELECT * FROM groups WHERE extension = called)  
	) a */
) a
		where a.time between  {$cur_date['start']}  and {$cur_date['end']} ;
EOD;
$data =  compact_array(query_to_array($sql));
$f_data[] = array('day_total_calls',$data["data"][0][0]);

$sql=
<<<EOD
SELECT count(*) from (
    SELECT * from (
    SELECT a.time, a.caller, b.called, a.duration, a.status  FROM 
	(SELECT * FROM call_logs where direction = 'incoming' and status!='unknown' and ended = 0
	) a
	join 
	(SELECT * FROM call_logs where direction = 'outgoing' and status!='unknown' and ended = 0) b 
 	on a.billid = b.billid) c
    /*union 
    SELECT a.time, a.caller, a.called, a.duration, a.status  FROM 
	(SELECT * FROM call_logs where direction = 'incoming' and status!='unknown' and ended = 0
	and exists (SELECT * FROM groups WHERE extension = called)  
	) a  */
) a
		where a.time between  {$cur_date['start']}  and {$cur_date['end']} ;
EOD;
$data =  compact_array(query_to_array($sql));
$f_data[] = array('active_calls',$data["data"][0][0]);

$data =  compact_array(query_to_array("SELECT count(*) FROM gateways  where status = 'online'"));
$f_data[] = array('active_gateways',$data["data"][0][0]);

function get_userdata($name) {
    return $_SESSION['userdata'][$name];
}

function set_userdata($ar) {
    session_start();
    if (!$_SESSION['userdata']) $_SESSION['userdata']=array();
    foreach ($ar as $key=>$value)
        $_SESSION['userdata'][$key]=$value;
}

function get_cpu() {
    $file=file("/proc/stat");
    $tmp=explode(" ",$file[0]);
    $cpu_user_old=get_userdata("cpu_user");
    $cpu_nice_old=get_userdata("cpu_nice");
    $cpu_sys_old=get_userdata("cpu_sys");
    $cpu_idle_old=get_userdata("cpu_idle");
    $cpu_io_old=get_userdata("cpu_io");
    $cpu_user=$tmp[2];
    $cpu_nice=$tmp[3];
    $cpu_sys=$tmp[4];
    $cpu_idle=$tmp[5];
    $cpu_io=$tmp[6];
    $diff_used=($cpu_user-$cpu_user_old)+($cpu_nice-$cpu_nice_old)+($cpu_sys-$cpu_sys_old)+($cpu_io-$cpu_io_old);
    $diff_total=($cpu_user-$cpu_user_old)+($cpu_nice-$cpu_nice_old)+($cpu_sys-$cpu_sys_old)+($cpu_io-$cpu_io_old)+($cpu_idle-$cpu_idle_old);
    $cpu=$diff_used/$diff_total*100;
    set_userdata(array("cpu_user"=>$cpu_user,"cpu_nice"=>$cpu_nice,"cpu_sys"=>$cpu_sys,"cpu_idle"=>$cpu_idle,"cpu_io"=>$cpu_io));
    return $cpu; 
}

$f_data[] = array('cpu_use',round(get_cpu(),2)." %");

function get_mem() {
    $file=file("/proc/meminfo");
    
    $data =  humanSize(substr($file[0],strpos($file[0]," "),strrpos($file[0]," ")-strpos($file[0]," "))*1024);
    $str  = $data[0]." ".$data[2];
    $data =  humanSize(substr($file[1],strpos($file[1]," "),strrpos($file[1]," ")-strpos($file[1]," "))*1024,$data[1]);
    $str  = $data[0]. "/".$str;
    return $str; 
/*
    $memory["total"]=round(substr($file[0],strpos($file[0]," "),strrpos($file[0]," ")-strpos($file[0]," "))/(1024*1024),2);
    $memory["free"]=round(substr($file[1],strpos($file[1]," "),strrpos($file[1]," ")-strpos($file[1]," "))/(1024*1024),2);
    return $memory["free"].'/'.$memory["total"];
*/
}
$f_data[] = array('mem_use',get_mem());

function get_swap() {
    $file=file("/proc/swaps");
    $tmp=explode("\t",substr($file[1],strpos($file[1],"partition")));
    
    $data =  humanSize($tmp[1]*1024);
    $str  = $data[0]." ".$data[2];
    $data =  humanSize(($tmp[1]-$tmp[2])*1024,$data[1]);
    $str  = $data[0]. "/".$str;
    return $str; 
/*
    $swap["total"]=round($tmp[1]/(1024*1024),2);
    $swap["free"]=round(($tmp[1]-$tmp[2])/(1024*1024),2);
    return $swap["free"].'/'.$swap["total"];
*/
}
$f_data[] = array('swap_use',get_swap());

function humanSize($bytes,$limit_index = null) {
//  $type=array("", "kilo", "mega", "giga", "tera", "peta", "exa", "zetta", "yotta");
    $type=array("", "K", "M", "G", "T", "P");
    $index=0;
    while(($bytes>=1024) || ($limit_index && $limit_index>$index)) {
        $bytes/=1024;
        $index++;
    }
    $bytes = round($bytes,2);
    return array($bytes,$index,$type[$index]."b");
}

function get_space() {
//    ini_set("display_errors", 1);
    $data =  humanSize(disk_total_space("/"));
    $str  = $data[0]." ".$data[2];
    $data =  humanSize(disk_free_space("/"),$data[1]);
    $str  = $data[0]. "/".$str;
    
    return $str; 
}
$f_data[] = array('space_use',get_space());

function get_uptime() {
    $uptime=exec("uptime");
    $uptime=explode(",",substr($uptime,strpos($uptime,"up")+3));
    $time=explode(":",$uptime[1]);
    if(!isset($time[1])) {
        $time[1]=str_replace(" min","",$time[0]);
        $time[0]=0;
    }
    $uptime=$uptime[0]." ".$time[0]."h ".$time[1]."m";
    return $uptime;
}
//$f_data[] = array('uptime',get_uptime());

function get_active_user($name='user') {
    $path = realpath(session_save_path());
    $files = array_diff(scandir($path), array('.', '..'));
    $i=0;
    foreach ($files as $file) {
        preg_match('/'.$name.'\|s\:\d+\:\"([^\"]+)\"/',@file_get_contents($path . '/' . $file),$p);
        $s='';
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

function get_yate_version(){
    exec('yate --version',$ver);
  return $ver;
}

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

$f_data[] = array('uptime',get_yate_last_restart());

$f_data[] = array('active_user',get_active_user());
$f_data[] = array('active_extension',get_active_user('extension'));
$f_data[] = array('yate_version',get_yate_version());

exec('ps -ef | grep -c defunct',$def);
 
$f_data[] = array('defunct processes',$def[0]-2);


$obj=array("success"=>true);
$obj["total"] = count($f_data);
$obj["data"] = $f_data; 
echo out($obj);
?>