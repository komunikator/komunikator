<?
need_user();

$table_name = 'extensions';
$values = array();
$data = json_decode($HTTP_RAW_POST_DATA);
$rows = array();
$values = array();

if ($data)
foreach ($data as $row)
{
$values = array();
    foreach ($row as $key=>$value)
        if ($key == 'id') $id = $key;
	//else
        if ($key == 'status');
        else
            $values[$key]=$value; 
$rows[] = $values;
}

require_once("create.php");
?>