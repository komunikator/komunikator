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
if (!$time_offset) $time_offset=-240;

if(!$extension) {
    echo (out(array("success"=>false,"message"=>"Extension is undefined"))); exit;} 

$status =  compact_array(query_to_array("SELECT extension,CASE WHEN expires is not NULL THEN 'online' ELSE 'offline' END as status FROM extensions where extension in ($extension) ORDER BY 2 LIMIT 1000 OFFSET 0"));
$obj=array("success"=>true);
if (isset($_SESSION['extension']))
    $obj["status"] = $status['data'][0][1]; 
else
    $obj["status"] = $status['data'];
$data  =  compact_array(query_to_array ("SELECT time-($time_offset)*60,caller FROM call_logs where ".time()."-time < $period and (/*caller in ($extension) or*/ called in ($extension)) and direction='outgoing' LIMIT $limit OFFSET 0"));

$f_data = array();
foreach ($data["data"] as $row) {
    $row[0] = date($date_format,$row[0]); 
    $f_data[] = $row; 
    //$f_data[] = array('time'=>$row[0],'number'=>$row[1]); 
}

$obj["calls"] = $f_data; 
echo out($obj);

?>