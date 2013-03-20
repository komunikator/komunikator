<?php
/**
 * lib_queries.php
 * This file is part of the FreeSentral Project http://freesentral.com
 *
 * FreeSentral - is a Web Graphical User Interface for easy configuration of the Yate PBX software
 * Copyright (C) 2008-2009 Null Team
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301, USA.
 */
?>
<?php
require_once("config.php");

function start_debug_req_time()
{
    return microtime(true);
}

function stop_debug_req_time($time,$query)
{
    $debug_requests = false;
    $debug_requests_time = 0.5; // минимальное время выполнения запроса, после которого происходит журналирование
    $debug_requests_file = 'requests_debug.log';
    $time_d = microtime(true)-$time;
    if ($debug_requests)
        if ($time_d >= $debug_requests_time) file_put_contents($debug_requests_file,"$time_d\t$query\n",FILE_APPEND);
}

function query_to_array($query) {
    /*
    $res = query($query);
    if(!$res) {
        return array();
    }
    $array = array();
    $i=0;
    while ($row=$res->fetchRow()) {  
        foreach ($row as $key=>$value){
            $array[$i][$key]= $value;
     };
     $i++;
    }
    */
    global $conn;
    $time = start_debug_req_time();
    $array  = $conn->getAll($query);
    stop_debug_req_time($time, $query);
    Yate::Output("Executed: $query");
    Yate::Output("Result:".json_encode($array));
    //$res->free();
    return $array;
}

function query($query) {
    global $conn, $query_on, $max_resets_conn;
    global $dsn;
    $resets = 0;
    while(true) {
	if ($conn)
        {
            $time = start_debug_req_time();            
            $res = $conn->query($query);
            stop_debug_req_time($time, $query);
        }
        /*
        if(!$res) {
            while(true) {
                if($resets >= ($max_resets_conn-1)) {
                    Yate::Output("Could not execute: $query\n");
                    return null;
                }
                $resets++;
                if (!$conn)
                {
                        $conn = DB::connect($dsn, true);
                        break;
                    sleep(1);
                }else
                    $resets = $max_resets_conn;
            }
        }else    */
            break; 
    } 
    if($query_on)
        Yate::Output("Executed: $query");
    return $res;
}

function query_nores($query) {
    $res = query($query);
}

function getCustomVoicemailDir($called) {
    global $vm_base;

    $last = $called[strlen($called)-1];
    $alast = $called[strlen($called)-2];

    $dir = "$vm_base/$last";
    if (!is_dir($dir)) {
        mkdir($dir,0750);
        chown($dir,"apache");
    }
    $dir = "$vm_base/$last/$alast/";
    if (!is_dir($dir)) {
        mkdir($dir,0750);
        chown($dir,"apache");
    }
    $dir = "$vm_base/$last/$alast/$called";
    if (!is_dir($dir)) {
        mkdir($dir,0750);
        chown($dir,"apache");
    }
    return $dir;
}


?>