<?
if (!$id_name) $id_name='id';
foreach ($rows as $row) {
    $updates = array();
    $id = null;
    foreach ($row as $key => $value) {
        $value = $conn->escapeSimple($value);
        if ($key == 'id') $id = $value; 
    }
    if ($id) {
	$sql=sprintf("DELETE FROM %s WHERE %s='%s'", $table_name,$id_name,$id);
        query($sql);
    }
};

$out = array("success"=>true,"message"=>'saved');
echo (out($out)); 
?>