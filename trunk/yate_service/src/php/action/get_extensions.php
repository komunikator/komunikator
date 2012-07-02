<?
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 

$total =  compact_array(query_to_array("SELECT count(*) FROM extensions"));
if(!is_array($total["data"]))  echo out(array("success"=>false,"message"=>$total));
    
$data =  compact_array(query_to_array("SELECT CASE WHEN expires is not NULL THEN 'online' ELSE 'offline' END as status, extension, firstname, lastname, null as groups FROM extensions ORDER BY ".get_sql_order_limit()));
if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));
    
$obj=array("success"=>true);
$obj["total"] = $total['data'][0][0]; 
$obj["data"] = $data['data']; 
echo out($obj);
?>