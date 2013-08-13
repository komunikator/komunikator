<?
$user_cur_ver = $_REQUEST["cur_ver"]?$_REQUEST["cur_ver"]:'0.0';
$app_cur_ver = @file_get_contents('cur.ver');
if (!$app_cur_ver) $app_cur_ver = '0.0';
$obj=array("success"=>true);
  if ($app_cur_ver>$user_cur_ver)
   $obj["update_exists"] = $app_cur_ver;
  else 
   $obj["message"] = 'update_not_found';
echo json_encode ($obj);

?>