<?
need_user();
set_time_limit(0);
$out = shell_exec('sudo /tmp/test_update.sh > /tmp/update_out');
//shell_exec('sudo apt-get update > /tmp/update_out ; sudo apt-get install php5 >> /tmp/update_out');
//$out = shell_exec('cat /tmp/update_out');
//sleep (10);
$out = str_replace("\n","<br>",$out);
$obj=array("success"=>true);
//$obj["message"] = $out; 
$obj["message"] = 'update_success'; 
//or

echo out($obj);

?>