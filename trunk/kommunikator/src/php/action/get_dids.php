<?
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 

$sql=
<<<EOD
	SELECT 
	d.did_id as id,
	d.number, 
	CASE 
	  WHEN d.destination = '{$source["attendant"]}' THEN 'attendant'
	  WHEN d.destination = '{$source["voicemail"]}'  THEN 'voicemail'
  	  ELSE d.destination
	END as destination,  	
	CASE
		 WHEN e.extension is not null THEN e.extension 
		 WHEN g.group is not null THEN g.group 
	 END as default_dest,
	d.description 
	FROM dids d  
		left join  extensions e 
			on e.extension_id = d.extension_id
			left join groups g 
				on g.group_id = d.group_id 
			where did_id not in (select did_id from dids where did like "conference %") 
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