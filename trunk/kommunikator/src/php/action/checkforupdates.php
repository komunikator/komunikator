<?
need_user();
//set_time_limit(0);
//$out = shell_exec('ps -ef > /tmp/pbx_out; cat /tmp/pbx_out');
sleep (6);
$obj=array("success"=>true);
//$obj["message"] = 'update_not_found'; 
//or
$obj["update_exists"] = 'TEST version 2';
//$obj["data"] = $out; 
echo out($obj);

?>