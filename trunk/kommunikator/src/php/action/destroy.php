<?
$table_name = get_sql_field( next( explode('_', $action, 2) ) );

if (!$id_name) $id_name='id';


foreach ($rows as $row) {
    $id = null;

    foreach ($row as $key => $value) {
        // $value = $conn->escapeSimple($value);
        if ($key == 'id') $id = $value; 
    }

    if ($id) {
        $sql = sprintf("DELETE FROM %s WHERE %s=%s", $table_name, $id_name, $id);
        query($sql);

        $sql = "INSERT INTO actionlogs (date, performer, query, ip) VALUES (".time().",\"{$_SESSION['user']}\",\"$sql\", \"{$_SERVER['REMOTE_ADDR']}\")";
        query($sql);
    }

};


if (!isset($need_out)) {
    $out = array("success" => true);
    echo(out($out)); 
}
else unset($need_out);
?>