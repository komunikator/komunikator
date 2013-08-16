<?
$table_name = get_sql_field( next( explode('_', $action, 2) ) );

foreach ($rows as $row) {
    $sql = sprintf( 'INSERT INTO %s (%s) VALUES (%s)', $table_name, implode(', ', array_map( $conn->escapeSimple, array_map( 'get_sql_field', array_keys($row) ) ) ), implode(', ', array_map( $conn->escapeSimple, $row) ) );
    query($sql);
    
    $sql = "INSERT INTO actionlogs (date, performer, query, ip) VALUES (".time().", \"{$_SESSION['user']}\", \"$sql\", \"{$_SERVER['REMOTE_ADDR']}\")";
    query($sql);
};

if (!isset($need_out)) {
    // $out = array("success"=>true/*,"message"=>'saved'*/);
    $out = array("success" => true);
    echo(out($out)); 
}
else unset($need_out);
?>