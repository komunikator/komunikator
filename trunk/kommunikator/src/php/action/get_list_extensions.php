<?

if ($_SESSION["extension"])
    $extension = "'" . $_SESSION["extension"] . "'";
if (!$extension) {
    echo (out(array("success" => false, "message" => "Extension is undefined")));
    exit;
}
$sql =
        <<<EOD
 select * from (
	SELECT 
	CASE 
		-- WHEN (SELECT count(*) FROM call_logs where caller =ex.extension and status!='unknown' and ended = false) > 1 THEN 'busy' 
		WHEN coalesce(inuse_count,0)!=0 THEN 'busy'
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