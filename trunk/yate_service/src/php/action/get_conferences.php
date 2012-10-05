<?
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 

$total =  compact_array(query_to_array("SELECT count(*) FROM dids"));
if(!is_array($total["data"]))  echo out(array("success"=>false,"message"=>$total));
    
$sql=
<<<EOD
	SELECT 
	did_id as id,
	substring(did,length("conference ")+1 ,length (did)) as conference,
	number,
	(select count(*) from call_logs where called = d.number and ended = '0') as participants
	FROM dids d where did like "conference %" order by 
EOD;

$data =  compact_array(query_to_array($sql.get_sql_order_limit()));

if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));
    
$obj=array("success"=>true);
$obj["total"] = $total['data'][0][0]; 
$obj["data"] = $data['data']; 
echo out($obj);
?>