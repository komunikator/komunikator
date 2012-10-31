<?
$table_name = next(explode('_',$action,2));

if (!$id_name) $id_name='id';

foreach ($rows as $row) {
    $updates = array();
    $id = null;
    foreach ($row as $key => $value) {
        //$value = $conn->escapeSimple($value);
        if ($key == 'id') $id = $value; else {
            //$value = "$value";
            $updates[] = get_sql_field($key)." = $value";
        }
    }
    if ($id && $updates) {
        $sql=sprintf("UPDATE %s SET %s WHERE %s=%s", $table_name, implode(', ', $updates), $id_name,$id);
	file_put_contents('a',print_r($sql,true)."\n",FILE_APPEND);
        query($sql);
    }
};

if (!isset($need_out)) {
$out = array("success"=>true/*,"message"=>'saved'*/);
echo (out($out)); 
} else
unset($need_out);
 
?>