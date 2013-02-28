<?
need_user();

$out = shell_exec('sudo service yate restart');
//exec('sudo /etc/init.d/yate restart',$out);
//sleep (3);
$obj=array("success"=>true);
$obj["message"] = $out; 
//$obj["data"] = $out; 
echo out($obj);

?>