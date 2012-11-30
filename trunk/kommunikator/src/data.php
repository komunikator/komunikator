<?php
//ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] .'/tmp/');
//ini_set('session.gc_maxlifetime', 2592000); //30 day
//ini_set('session.cookie_lifetime', 2592000); //30 day

ini_set('session.name', 'session');
//ini_set('session.use_cookies', 0);
ini_set('session.use_only_cookies', 0);
session_start();
session_write_close();
//ini_set("error_reporting",'');
//ini_set("display_errors",true);

$log_file = 'log.log';
ini_set("log_errors",true);
ini_set("error_log",$log_file); 

require_once("libyate.php");
require_once("lib_queries.php");

$date_format = "d.m.y H:i:s"; 

if (!$conn) die('Database Connection Failed');

function handle_pear_error2($e) {
    die($e->getMessage().' '.print_r($e->getUserInfo(),true));
//echo json_encode(array("success"=>false,"message"=>"Error:'".$e->getMessage()/*.' '.print_r($e->getUserInfo(),true)*/));
};

require_once 'PEAR.php';
PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'handle_pear_error2');

//ini_set("session.use_only_cookies", "0");

require_once "php/util.php";

$action = getparam("action"); 

if ($action!='auth' && !(isset($_SESSION['user']) || isset($_SESSION['extension']))) {
    echo out(array("success"=>false,"message"=>"Auth failed"));
    exit;
}

$action_path = "php/action"; 

if (file_exists("$action_path/$action".".php")) include "$action_path/$action".".php"; 
else 
   echo out(array("success"=>false,"message"=>"Unknown action '".$action."'"));

?>