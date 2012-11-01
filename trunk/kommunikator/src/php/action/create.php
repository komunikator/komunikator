<?
$table_name = get_sql_field(next(explode('_',$action,2)));

foreach ($rows as $row) {
    $sql=sprintf('INSERT INTO %s (%s) VALUES (%s)', $table_name, implode(', ', array_map($conn->escapeSimple, array_map('get_sql_field',array_keys($row)))), implode(', ',array_map($conn->escapeSimple, $row))); 
    file_put_contents('a',print_r($sql,true)."\n",FILE_APPEND);
    query ($sql);
};
if (!isset($need_out)) {
$out = array("success"=>true/*,"message"=>'saved'*/);
echo (out($out)); 
} else
unset($need_out);
?>