<?

if ($_SESSION["extension"])
    $extension = "'" . $_SESSION["extension"] . "'";
$period = getparam("period");
$limit = getparam("limit");
$time_offset = getparam("time_offset");

if ($period)
    $period = $conn->escapeSimple($period);
if ($limit)
    $limit = $conn->escapeSimple($limit);
if ($time_offset)
    $time_offset = $conn->escapeSimple($time_offset);

if (!$period)
    $period = 10;
if (!$limit)
    $limit = 1000;
if (!$time_offset)
    $time_offset = $_SESSION['time_offset'];
if (!$time_offset)
    $time_offset = -240;

//$time_offset = 0;

if (!$extension) {
    echo (out(array("success" => false, "message" => "Extension is undefined")));
    exit;
}

$sql = "update ntn_settings set value = $extension, description = '" . time() . "' where param = 'exclude_called' and value = $extension";
query($sql);
$sql = "insert into ntn_settings (param,value,description) select 'exclude_called',$extension, '" . time() . "' from dual where not exists (select 1 from ntn_settings where param = 'exclude_called' and value = $extension)";
query($sql);

$status = compact_array(query_to_array("SELECT extension,CASE WHEN expires is not NULL THEN 'online' ELSE 'offline' END as status FROM extensions where extension in ($extension) ORDER BY 2 LIMIT 1000 OFFSET 0"));
$obj = array("success" => true);
if (isset($_SESSION['extension']))
    $obj["status"] = $status['data'][0][1];
else
    $obj["status"] = $status['data'];

$inuse_count = compact_array(query_to_array("select inuse_count from extensions where extension = $extension"));
$obj["inuse_count"] = $inuse_count['data'];
//$data  =  compact_array(query_to_array ("SELECT time-($time_offset)*60,caller,time FROM call_logs where ".time()."-time < $period and (/*caller in ($extension) or*/ called in ($extension)) and direction='outgoing' LIMIT $limit OFFSET 0"));
$data = compact_array(query_to_array("SELECT a.time-($time_offset)*60,a.caller,a.time,case when b.called = a.called then null  when c.description !='' then c.description else b.called end FROM call_logs a left join call_logs b on b.billid=a.billid and b.direction='incoming' and b.ended=0 left join gateways c on c.authname=b.called where " . time() . "-a.time < $period and (/*caller in ($extension) or*/ a.called in ($extension)) and a.direction='outgoing' and a.ended=0 LIMIT $limit OFFSET 0"));
/*
  $f_data = array();
  foreach ($data["data"] as $row) {
  $row[0] = date($date_format,$row[0]);
  $f_data[] = $row;
  //$f_data[] = array('time'=>$row[0],'number'=>$row[1]);
  }

  //$obj["calls"] = $f_data;
 */
if ($data["data"][0] && ($_SESSION['last_call'] != $data["data"][0][2])) {
    //$obj["incoming_call"] = array('time'=>date($date_format,$data["data"][0][0]),'number'=>$data["data"][0][1]); 
    $obj["incoming_call"] = array('time' => date($date_format, $data["data"][0][0]), 'number' => $data["data"][0][1], 'incoming_trunk' => $data["data"][0][3]);
    session_start();
    $_SESSION['last_call'] = $data["data"][0][2];
    session_write_close();
}
echo out($obj);
?>