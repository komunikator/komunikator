<?
need_user();
set_time_limit(0);
$out = shell_exec("sudo ./install_update.sh $updates_data_url > /tmp/update_out");
$out = str_replace("\n","<br>",$out);
$obj=array("success"=>true);
//$obj["message"] = $out; 
$obj["message"] = 'update_success'; 
//or

echo out($obj);

?>