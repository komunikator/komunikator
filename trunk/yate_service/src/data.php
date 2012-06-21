<?php
ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] .'/service/tmp/');
ini_set('session.gc_maxlifetime', 2592000); //30 day
ini_set('session.cookie_lifetime', 2592000); //30 day

ini_set('session.name', 'session');
//ini_set('session.use_cookies', 0);
ini_set('session.use_only_cookies', 0);
session_start();
//ini_set("error_reporting",'');
//ini_set("display_errors",true);

$log_file = 'log.log';
ini_set("log_errors",true);
ini_set("error_log",$log_file);

require_once("libyate.php");
require_once("lib_queries.php");

$default_ip = "ssl://127.0.0.1";	//	ip address where yate runs
$default_port = "5039";	// port used to connect to

require_once("socketconn.php");

if (!$conn) die('Database Connection Failed');

function handle_pear_error2($e) {
    die($e->getMessage().' '.print_r($e->getUserInfo(),true));
//echo json_encode(array("success"=>false,"message"=>"Error:'".$e->getMessage()/*.' '.print_r($e->getUserInfo(),true)*/));
};

require_once 'PEAR.php';
PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'handle_pear_error2');

//ini_set("session.use_only_cookies", "0");

function getparam($param) {
    $ret = NULL;
    if (isset($_POST[$param]))
        $ret = $_POST[$param];
    else if (isset($_GET[$param]))
            $ret = $_GET[$param];
        else
            return NULL;
    return $ret;
}

$action = getparam("action"); 
$username = getparam("user");
$extension = getparam("extension");
$password = getparam("password");

if ($username)  $username = $conn->escapeSimple($username);
if ($extension) $extension = $conn->escapeSimple($extension);
if (password)   $password = $conn->escapeSimple($password);
if ($action=='logout') {
    echo (json_encode(array("success"=>session_destroy())));
    exit;
}
if ($action=='auth' && $password) { 
    $_SESSION = array();
    if ($username) {
        $sql = "SELECT * from users where username = '$username' and password = '$password'";
        if (query_to_array($sql)) {
            $_SESSION['user'] = $username;
        }
    } else 
        if($extension) {
            $sql = "SELECT * from extensions where extension = '$extension' and password = '$password'";
            if (query_to_array($sql)) {
                $_SESSION['extension'] = $extension;
            }
        }
        
    if (isset($_SESSION['user']) || isset($_SESSION['extension'])) {
        $out = array("success"=>true,"session_name"=>session_name(),"session_id"=>session_id(),"message"=>"Auth successful");
        if (isset($_SESSION['user'])) $out['user'] = $_SESSION['user'];
        if (isset($_SESSION['extension'])) $out['extension'] = $_SESSION['extension'];
        echo (json_encode($out)); exit; 
        exit;
    }
}

if (!(isset($_SESSION['user']) || isset($_SESSION['extension']))) {
    echo (json_encode(array("success"=>false,"message"=>"Auth failed")));
    exit;
}

if ($_SESSION["extension"]) $extension = "'".$_SESSION["extension"]."'";

$period = getparam("period");
$limit = getparam("limit");
$time_offset = getparam("time_offset");

if ($period)      $period = $conn->escapeSimple($period);
if ($limit)       $limit = $conn->escapeSimple($limit);
if ($time_offset) $time_offset = $conn->escapeSimple($time_offset);

if (!$period) $period=10;
if (!$limit) $limit=1000;
if (!$time_offset) $time_offset=-240;

function compact_array($array) {
    $header = array();
    $data = array();
    if ($array)
        foreach ($array as $array_row) {
            $data_row = array();
            if ($array_row)
                foreach ($array_row as $key=>$value) {
                    if (!count ($data)) $header[] = $key;
                    $data_row[] = $value;
                }
            $data[] = $data_row;
        }
    return array('header'=>$header,'data'=>$data);
}
function make_call() {
    $called = getparam("number");
    //$caller = $_SESSION["user"];
    $caller = $_SESSION["extension"];
    
    $command = "click_to_call $caller $called";
    
    $socket = new SocketConn;
    $msg = '';
    
    if($socket->error == "") {
        $obj=array("success"=>true);
        $socket->command($command);
    }
    else {
        $obj=array("success"=>false);
        $obj['message'] = /*$socket->error;*/"Can't make call. Please contact your system administrator.";
    }
    echo json_encode($obj);
}

if ($action == 'get_status') {
    $out = array("success"=>true);
    if (isset($_SESSION['user'])) $out['user'] = $_SESSION['user'];
    if (isset($_SESSION['extension'])) $out['extension'] = $_SESSION['extension'];
    echo (json_encode($out)); exit; 
}

if ($action == 'get_state') {
    if(!$extension) {
        echo (json_encode(array("success"=>false,"message"=>"Extension is undefined"))); exit;} 
    else {
        $status =  compact_array(query_to_array("SELECT extension,CASE WHEN expires is not NULL THEN 'online' ELSE 'offline' END as status FROM extensions where extension in ($extension) ORDER BY 2 LIMIT 1000 OFFSET 0"));
        $obj=array("success"=>true);
        if (isset($_SESSION['extension']))
        $obj["status"] = $status['data'][0][1]; 
	else
        $obj["status"] = $status['data'];
        $calls  =  compact_array(query_to_array ("SELECT time-($time_offset)*60,caller FROM call_logs where ".time()."-time < $period and (/*caller in ($extension) or*/ called in ($extension)) and direction='outgoing' LIMIT $limit OFFSET 0"));
        $obj["calls"] = $calls['data'];
        echo json_encode($obj);
    }
}
else 
    if ($extension && $action == 'make_call')
        make_call();
    else
        //echo json_encode(array("success"=>false,"message"=>"User:'".$_SESSION['user']."'. Unknown action '".$action."'"));
if ($action == 'get_call_logs') {
   if(!$_SESSION['user']){
        echo (json_encode(array("success"=>false,"message"=>"User is undefined"))); exit;} 

        $total =  compact_array(query_to_array("SELECT count(*) FROM call_logs"));
        if(!is_array($total["data"]))  echo json_encode(array("success"=>false,"message"=>$total));

        $call_logs =  compact_array(query_to_array("SELECT time, caller, called, duration, status FROM call_logs ORDER BY ".getparam("sort")." ".getparam("dir").get_sql_limit(getparam("start"),getparam("size"))));
        if(!is_array($call_logs["data"]))  echo json_encode(array("success"=>false,"message"=>$call_logs));

        $obj=array("success"=>true);
        $obj["total"] = $total['data'][0][0]; 
        $obj["data"] = $call_logs['data']; 
        echo json_encode($obj);

}
else

if ($action == 'get_active_calls') {
   if(!$_SESSION['user']){
        echo (json_encode(array("success"=>false,"message"=>"User is undefined"))); exit;} 

        $total =  compact_array(query_to_array("SELECT count(*) FROM call_logs where status!='unknown' and /*ended = 0 */ended = false"));
        if(!is_array($total["data"]))  echo json_encode(array("success"=>false,"message"=>$total));

        $call_logs =  compact_array(query_to_array("SELECT time, caller, called, duration, status FROM call_logs where status!='unknown' and /*ended = 0 or*/ ended = false ORDER BY ".getparam("sort")." ".getparam("dir").get_sql_limit(getparam("start"),getparam("size"))));
        if(!is_array($call_logs["data"]))  echo json_encode(array("success"=>false,"message"=>$call_logs));

        $obj=array("success"=>true);
        $obj["total"] = $total['data'][0][0]; 
        $obj["data"] = $call_logs['data']; 
        echo json_encode($obj);

}
else
        echo json_encode(array("success"=>false,"message"=>"Unknown action '".$action."'"));

function get_sql_limit($start,$size,$page){
 if ($start==null || $size==null) return '';
 global $db_type_sql;
 if ($db_type_sql == 'mysql')
 return " LIMIT $start,$size";	
 return " LIMIT $size OFFSET $start";
}

?>