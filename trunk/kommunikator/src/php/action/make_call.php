<?
if(!$_SESSION["extension"]) {
    echo (out(array("success"=>false,"message"=>"Extension is undefined"))); exit;} 

require_once("php/socketconn.php");

$called = getparam("number");
if (!$called) {echo (out(array("success"=>false,"message"=>"Called number is undefined"))); exit;} 

//$caller = $_SESSION["user"];
$caller = $_SESSION["extension"];
$command = "click_to_call $caller $called";
$socket = new SocketConn;
$msg = '';

if($socket->error == "") {
    $obj=array("success"=>true);
    $socket->command($command);
    	    $sql="insert into actionlogs (date,performer,log,ip) values (".time().",\"{$_SESSION['extension']}\",\"$command\", \"{$_SERVER['REMOTE_ADDR']}\")";
	    query ($sql);
}
else {
    $obj=array("success"=>false);
    $obj['message'] = /*$socket->error;*/"Can't make call. Please contact your system administrator.";
}
echo out($obj);
?>