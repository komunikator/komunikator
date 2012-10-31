<?
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 

$total =  compact_array(query_to_array("SELECT count(*) FROM gateways"));
if(!is_array($total["data"]))  echo out(array("success"=>false,"message"=>$total));
    
$data =  compact_array(query_to_array("SELECT gateway_id as id, enabled, gateway, protocol, server, username, password, description, authname, domain FROM gateways ".get_sql_order_limit()));
if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));
    
$obj=array("success"=>true);
$obj["total"] = $total['data'][0][0]; 
$obj["data"] = $data['data']; 
echo out($obj);
?>