<?
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 

$sql=
<<<EOD
	SELECT 
	did_id as id,
	substring(did,length("conference ")+1 ,length (did)) as conference,
	number,
	(select count(*) from call_logs where called = d.number and ended = '0') as participants
	FROM dids d where did like "conference %"  
EOD;

$data =  compact_array(query_to_array($sql.get_filter()));
if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));
$total = count($data["data"]);

$data =  compact_array(query_to_array($sql.get_sql_order_limit()));
if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));
    
$obj=array("success"=>true);
$obj["total"] = $total; 
$obj["data"] = $data['data']; 
echo out($obj);
?>