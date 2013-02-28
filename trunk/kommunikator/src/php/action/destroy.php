<?
$table_name = get_sql_field(next(explode('_',$action,2)));

if (!$id_name) $id_name='id';
foreach ($rows as $row) {
    $updates = array();
    $id = null;
    foreach ($row as $key => $value) {
        //$value = $conn->escapeSimple($value);
        if ($key == 'id') $id = $value; 
    }
    if ($id) {
	$sql=sprintf("DELETE FROM %s WHERE %s=%s", $table_name,$id_name,$id);
        query($sql);
    $sql="insert into actionlogs (date,performer,query,ip) values (".time().",\"{$_SESSION['user']}\",\"$sql\", \"{$_SERVER['REMOTE_ADDR']}\")";
    query ($sql);
    }
};

$out = array("success"=>true/*,"message"=>'saved'*/);
echo (out($out)); 
?>