<?
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 
    
$sql=
<<<EOD
select * from (
	SELECT 
	ex.extension_id as id,  
	CASE 
		WHEN (SELECT count(*) FROM call_logs where caller =ex.extension and status!='unknown' and ended = false) > 1 THEN 'busy' 
		WHEN expires is not NULL THEN 'online' 
		ELSE 'offline' END as status, 
	ex.extension,
	ex.password,
	firstname, 
	lastname,
	ex.address,
	m.group as group_name,
	fwd.value as forward,
	fwd_busy.value as forward_busy,
	fwd_no_answ.value as forward_noanswer,
	no_answ_to.value as noanswer_timeout
 FROM extensions ex  
		left join  group_members gm 
			on ex.extension_id = gm.extension_id
			left join groups m 
				on gm.group_id = m.group_id 
				left join pbx_settings fwd 
					on fwd.extension_id = ex.extension_id and fwd.param = "forward"
					left join pbx_settings fwd_busy 
						on fwd_busy.extension_id = ex.extension_id and fwd_busy.param = "forward_busy"
							left join pbx_settings fwd_no_answ 
								on fwd_no_answ.extension_id = ex.extension_id and fwd_no_answ.param = "forward_noanswer"
								left join pbx_settings no_answ_to 
									on no_answ_to.extension_id = ex.extension_id and no_answ_to.param = "noanswer_timeout"

	) a 
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