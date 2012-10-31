<?
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 

$total =  compact_array(query_to_array("SELECT count(*) FROM dids"));
if(!is_array($total["data"]))  echo out(array("success"=>false,"message"=>$total));
    
$sql=
<<<EOD
	SELECT 
	d.did_id as id,
        d.did,
	d.number, 
	d.destination, 
	d.description, 
	e.extension,
	g.group 
	FROM dids d  
		left join  extensions e 
			on e.extension_id = d.extension_id
			left join groups g 
				on g.group_id = d.group_id 
			where did_id not in (select did_id from dids where did like "conference %") 
EOD;

$data =  compact_array(query_to_array($sql.get_sql_order_limit()));

if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));
    
$obj=array("success"=>true);
$obj["total"] = $total['data'][0][0]; 
$obj["data"] = $data['data']; 
echo out($obj);
?>