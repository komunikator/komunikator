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

$sql =
        <<<EOD
 select * from (
	SELECT 
	CASE 
		WHEN (SELECT count(*) FROM call_logs where caller =ex.extension and status!='unknown' and ended = false) > 1 THEN 'busy' 
		WHEN expires is not NULL THEN 'online' 
		ELSE 'offline' END as status, 
	ex.extension,
	firstname, 
	lastname	
 FROM extensions ex  
		) a	
EOD;

$data = compact_array(query_to_array($sql));
if ($data["data"][0]) 
    $obj["extensions"] = $data["data"]; 
echo out($obj);
?>