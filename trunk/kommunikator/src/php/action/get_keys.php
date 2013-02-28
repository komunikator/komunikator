<?
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 

$total =  compact_array(query_to_array("SELECT count(*) FROM `keys`"));
if(!is_array($total["data"]))  echo out(array("success"=>false,"message"=>$total));

$sql=
<<<EOD
	SELECT 
	key_id as id, 
	p.status, 
	`key`,
	(select groups.group from groups  where groups.extension = destination union select destination limit 1) destination,
	k.description 
	FROM `keys` k 
		left join  prompts p 
			on k.prompt_id = p.prompt_id
EOD;
    
$data =  compact_array(query_to_array($sql.get_sql_order_limit()));
//file_put_contents("test.txt","SELECT group_id as id, \"group\", description, extension FROM groups ORDER BY ".get_sql_order_limit());

if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));
    
$obj=array("success"=>true);
$obj["total"] = $total['data'][0][0]; 
$obj["data"] = $data['data']; 
echo out($obj);
?>