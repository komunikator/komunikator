<?
need_user();
$pbx_out = "/tmp/update_out";
$obj=array("success"=>true);
$obj["data"] = str_replace("\n","<br>",file_get_contents($pbx_out)); 
echo out($obj);

?>