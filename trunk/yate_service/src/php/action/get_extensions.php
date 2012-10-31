<?
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 

$total =  compact_array(query_to_array("SELECT count(*) FROM extensions"));
if(!is_array($total["data"]))  echo out(array("success"=>false,"message"=>$total));
    
$sql=
<<<EOD
	SELECT 
	ex.extension_id as id,  
	CASE WHEN expires is not NULL THEN 'online' ELSE 'offline' END as status, 
	ex.extension, 
	firstname, 
	lastname,
	m.group 
	FROM extensions ex  
		left join  group_members gm 
			on ex.extension_id = gm.extension_id
			left join groups m 
				on gm.group_id = m.group_id 
EOD;

$data =  compact_array(query_to_array($sql.get_sql_order_limit()));

if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));
    
$obj=array("success"=>true);
$obj["total"] = $total['data'][0][0]; 
$obj["data"] = $data['data']; 
echo out($obj);
?>