<?
// deprecated
/*
  $table_name = get_sql_field(next(explode('_',$action,2)));

  foreach ($rows as $row) {
  $sql=sprintf('SELECT %s FROM '.$table_name.' WHERE %s', );
  query ($sql);
  }

  foreach ($rows as $row) {
  $sql=sprintf('INSERT INTO %s (%s) VALUES ("%s")', $table_name, implode(', ', array_map($conn->escapeSimple, array_map('get_sql_fields',array_keys($row)))), implode('", "',array_map($conn->escapeSimple, $row)));
  query ($sql);
  };

  $out = array("success"=>true,"message"=>'saved');
  echo (out($out));
 */
?>