<?
//sleep(10);
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 
$sql=
<<<EOD
SELECT "" id, a.* from (
	SELECT time, caller, called, duration, 
		CASE 
		WHEN reason !="" THEN REPLACE(LOWER(reason),' ','_') 
		ELSE status END as status
	from (
    SELECT a.time, 
	{$macro_sql['caller_called']}	
	-- a.caller, b.called, 
	b.duration, a.status,a.reason  FROM 
	(SELECT * FROM call_logs where direction = 'incoming' and status!='unknown' and ended = 1
	) a
	join 
	(SELECT * FROM call_logs where direction = 'outgoing' and status!='unknown' and ended = 1) b 
 	on a.billid = b.billid) c
    /* union 
    SELECT a.time, a.caller, a.called, a.duration, a.status  FROM 
	(SELECT * FROM call_logs where direction = 'incoming' and status!='unknown' and ended = 1
	and exists (SELECT * FROM groups WHERE extension = called)  
	) a  */
) a
EOD;

$data =  compact_array(query_to_array($sql.get_filter()));
if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));
$total = count($data["data"]);

$data =  compact_array(query_to_array($sql.get_sql_order_limit()));
if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));

//$total = count($data["data"]);

$obj=array("success"=>true);
$obj["total"] = $total;

/*
$total =  compact_array(query_to_array("SELECT count(*) FROM call_logs ".get_filter()));
if(!is_array($total["data"]))  echo out(array("success"=>false,"message"=>$total));
$data =  compact_array(query_to_array("SELECT \"\" id, time, caller, called, duration,  status FROM call_logs  ".get_sql_order_limit()));
if(!is_array($data["data"])) echo out(array("success"=>false,"message"=>$data));
$obj=array("success"=>true);
$obj["total"] = $total['data'][0][0]; 
//$obj["sql"] = get_sql_order_limit();
//$obj["sql"] = strtotime('2010/08/11 06:33:00'); //get_sql_order_limit();
*/

$f_data = array();
foreach ($data["data"] as $row) {
    $row[1] -= $_SESSION['time_offset']*60;	
    $row[1] = date($date_format,$row[1]); 
    $f_data[] = $row; 
}

$obj["data"] = $f_data; 
echo out($obj);
?>