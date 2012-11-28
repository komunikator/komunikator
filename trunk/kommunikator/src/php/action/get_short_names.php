<?
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 

$total =  compact_array(query_to_array("SELECT count(*) FROM short_names"));
if(!is_array($total["data"]))  echo out(array("success"=>false,"message"=>$total));
    
$sql=
<<<EOD
	SELECT 
	short_name_id as id,
        short_name,
	name,
	number
	FROM short_names
	WHERE extension_id is null
EOD;

$data =  compact_array(query_to_array($sql.get_sql_order_limit()));

if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));
    
$obj=array("success"=>true);
$obj["total"] = $total['data'][0][0]; 
$obj["data"] = $data['data']; 
echo out($obj);
?>