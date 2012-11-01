<?
//sleep(10);
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 

$total =  compact_array(query_to_array("SELECT count(*) FROM call_logs ".get_filter()));
if(!is_array($total["data"]))  echo out(array("success"=>false,"message"=>$total));
$data =  compact_array(query_to_array("SELECT time, caller, called, duration,  status FROM call_logs /*where time !='99999999999999.999'*/ ".get_sql_order_limit()));
if(!is_array($data["data"])) echo out(array("success"=>false,"message"=>$data));
$obj=array("success"=>true);
$obj["total"] = $total['data'][0][0]; 
//$obj["sql"] = get_sql_order_limit();
//$obj["sql"] = strtotime('2010/08/11 06:33:00'); //get_sql_order_limit();

$f_data = array();
foreach ($data["data"] as $row) {
    $row[0] = date($date_format,$row[0]); 
    $f_data[] = $row; 
}

$obj["data"] = $f_data; 
echo out($obj);
?>