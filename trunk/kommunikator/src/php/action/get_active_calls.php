<?
//sleep(10);
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 
$sql=
<<<EOD
    SELECT * from (
    SELECT a.time, 
	{$macro_sql['caller_called']}	
	-- a.caller, b.called, 
	a.duration, a.status  FROM 
	(SELECT * FROM call_logs where direction = 'incoming' and status!='unknown' and ended = 0
	) a
	join 
	(SELECT * FROM call_logs where direction = 'outgoing' and status!='unknown' and ended = 0) b 
 	on a.billid = b.billid) c
    /* union 
    SELECT a.time, a.caller, a.called, a.duration, a.status  FROM 
	(SELECT * FROM call_logs where direction = 'incoming' and status!='unknown' and ended = 0
	and exists (SELECT * FROM groups WHERE extension = called)  
	) a */
EOD;

$data =  compact_array(query_to_array($sql.get_filter()));
if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));
$total = count($data["data"]);

$data =  compact_array(query_to_array($sql.get_sql_order_limit()));
if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));

$obj=array("success"=>true);
$obj["total"] = $total;

$f_data = array();
foreach ($data["data"] as $row) {
    $row[0] -= $_SESSION['time_offset']*60;	
    $row[3] = round(time()-$_SESSION['time_offset']*60 - $row[0]); 
    $row[0] = date($date_format,$row[0]); 
    $f_data[] = $row; 
}

$obj["data"] = $f_data; 
echo out($obj);
?>