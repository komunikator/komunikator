<?
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 

$total =  compact_array(query_to_array("SELECT count(*) FROM gateways"));
if(!is_array($total["data"]))  echo out(array("success"=>false,"message"=>$total));
    
$data =  compact_array(query_to_array("SELECT gateway_id as id, status,/* case when enabled = 1 then true when enabled = 0 then false end as */ enabled, gateway, server, username, password, description, protocol, ip_transport,authname, domain, callerid FROM gateways ".get_sql_order_limit()));
if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));
    
$obj=array("success"=>true);
$obj["total"] = $total['data'][0][0]; 
$obj["data"] = $data['data']; 
/*
$f_data = array();
foreach ($data["data"] as $row) {
    $row[2] = $row[2] && true; 
    $f_data[] = $row; 
}
$obj["data"] = $f_data; 
*/
echo out($obj);
?>