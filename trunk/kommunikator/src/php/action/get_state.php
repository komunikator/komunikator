<?
if ($_SESSION["extension"]) $extension = "'".$_SESSION["extension"]."'";
$period = getparam("period");
$limit = getparam("limit");
$time_offset = getparam("time_offset");

if ($period)      $period = $conn->escapeSimple($period);
if ($limit)       $limit = $conn->escapeSimple($limit);
if ($time_offset) $time_offset = $conn->escapeSimple($time_offset);

if (!$period) $period=10;
if (!$limit) $limit=1000;
if (!$time_offset) $time_offset=$_SESSION['time_offset'];
if (!$time_offset) $time_offset=-240;

//$time_offset = 0;

if(!$extension) {
    echo (out(array("success"=>false,"message"=>"Extension is undefined"))); exit;} 

$sql = "update ntn_settings set value = $extension, description = '".time()."' where param = 'exclude_called' and value = $extension";
query($sql);
$sql = "insert into ntn_settings (param,value,description) select 'exclude_called',$extension, '".time()."' from dual where not exists (select 1 from ntn_settings where param = 'exclude_called' and value = $extension)";
query($sql);

$status =  compact_array(query_to_array("SELECT extension,CASE WHEN expires is not NULL THEN 'online' ELSE 'offline' END as status FROM extensions where extension in ($extension) ORDER BY 2 LIMIT 1000 OFFSET 0"));
$obj=array("success"=>true);
if (isset($_SESSION['extension']))
    $obj["status"] = $status['data'][0][1]; 
else
    $obj["status"] = $status['data'];
$data  =  compact_array(query_to_array ("SELECT time-($time_offset)*60,caller,time FROM call_logs where ".time()."-time < $period and (/*caller in ($extension) or*/ called in ($extension)) and direction='outgoing' LIMIT $limit OFFSET 0"));
/*
$f_data = array();
foreach ($data["data"] as $row) {
    $row[0] = date($date_format,$row[0]); 
    $f_data[] = $row; 
    //$f_data[] = array('time'=>$row[0],'number'=>$row[1]); 
}

//$obj["calls"] = $f_data; 
*/
if ($data["data"][0] && ($_SESSION['last_call']!=$data["data"][0][2]))
 {
  $obj["incoming_call"] = array('time'=>date($date_format,$data["data"][0][0]),'number'=>$data["data"][0][1]); 
    session_start();
    $_SESSION['last_call']=$data["data"][0][2];
  session_write_close();
 }
echo out($obj);
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