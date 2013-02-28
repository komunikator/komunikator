<?
$table_name = get_sql_field(next(explode('_',$action,2)));

foreach ($rows as $row) {
    $sql=sprintf('INSERT INTO %s (%s) VALUES (%s)', $table_name, implode(', ', array_map($conn->escapeSimple, array_map('get_sql_field',array_keys($row)))), implode(', ',array_map($conn->escapeSimple, $row))); 
    query ($sql);
    $sql="insert into actionlogs (date,performer,query,ip) values (".time().",\"{$_SESSION['user']}\",\"$sql\", \"{$_SERVER['REMOTE_ADDR']}\")";
    query ($sql);
};
if (!isset($need_out)) {
$out = array("success"=>true/*,"message"=>'saved'*/);
echo (out($out)); 
} else
unset($need_out);
?>