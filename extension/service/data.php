<?php 
require_once("config.php");
require_once("framework.php");
require_once("menu.php");
require_once("lib/lib.php");
require_once("lib/lib_freesentral.php");
//$db_host = "localhost";

ini_set("session.use_only_cookies", "0");

include_classes();
require_once("set_debug.php");

$action = getparam("action"); 
$username = getparam("extension");
$password = getparam("password");

if ($action=='auth' && $username && $password) {
    $login = "";
    $_SESSION = array();
    $user = new User;
    $user->username = $username;
    $user->password = $password;
    if($user->login()) {
        $level = 'admin';
    }else {
        $extension = new Extension;
        $extension->extension = $username;
        $extension->password = $password;
        if ($extension->login())
            $level = 'extension';
        else
            $level = NULL;
    }
    if ($level) {
        $_SESSION['user'] = $username;
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = ($level == "admin") ? $user->user_id : $extension->extension_id;
        $_SESSION['level'] = $level;
        echo (json_encode(array("success"=>true,"session_name"=>session_name(),"session_id"=>session_id(),"message"=>"Auth successful")));
        exit;
    }
}

if (!$_SESSION['user'] or !$_SESSION['level']) {
    echo (json_encode(array("success"=>false,"message"=>"Auth failed")));
    exit;
}

$extension = "'".$_SESSION['user']."'";
//$extension = getparam("extension");;
$period = getparam("period");
$limit = getparam("limit");
$time_offset = getparam("time_offset");

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
    $caller = $_SESSION["user"];

    $command = "click_to_call $caller $called";

    $socket = new SocketConn;
    $msg = '';

    if($socket->error == "") {
        $obj=array("success"=>true);
        $socket->command($command);
    }
    else {
        $obj=array("success"=>false);
        $obj['message'] = "Can't make call. Please contact your system administrator.";
    }
    echo json_encode($obj);
}

if ($extension && $action == 'get_state') {
    $status =  compact_array(query_to_array (Database::query("SELECT extension,CASE WHEN expires is not NULL THEN 'online' ELSE 'offline' END as status FROM \"public\".\"extensions\" where extension in ($extension) ORDER BY 2 LIMIT 1000 OFFSET 0")));
    $obj=array("success"=>true,data=>array());
    $obj["data"]["status"] = $status['data'];
    $calls =  compact_array(query_to_array (Database::query("SELECT time-($time_offset)*'1 minutes'::interval,caller FROM \"public\".\"call_logs\" where now()+$time_offset*'1 minutes'::interval-time < $period*'1 second'::interval and (/*caller in ($extension) or*/ called in ($extension)) and direction='outgoing' LIMIT $limit OFFSET 0")));
    $obj["data"]["calls"] = $calls['data'];
    echo json_encode($obj);
}
else 
    if ($extension && $action == 'make_call')
        make_call();
    else
        echo json_encode(array("success"=>false,"message"=>"Unknown action '".$action."'"));
?>