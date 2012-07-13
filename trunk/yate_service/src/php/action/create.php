<?
foreach ($rows as $row) {
    $sql=sprintf('INSERT INTO %s (%s) VALUES ("%s")', $table_name, implode(', ', array_map($conn->escapeSimple, array_keys($row))), implode('", "',array_map($conn->escapeSimple, $row))); 
    query ($sql);
};
$out = array("success"=>true,"message"=>'saved');
echo (out($out)); 
?>