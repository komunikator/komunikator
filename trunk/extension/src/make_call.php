<?

if(!$_SESSION["extension"]) {
    echo (json_encode(array("success"=>false,"message"=>"Extension is undefined"))); exit;} 

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
?>