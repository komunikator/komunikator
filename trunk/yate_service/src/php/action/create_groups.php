<?
need_user();

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
	else
        //if ($key == "group" && !$value) $values[$key]="'".md5(uniqid(rand(),1))."'";
	//else
        //if ($key == "extension" && !$value) $values[$key]=" (SELECT MAX(extension)+1 FROM (SELECT * FROM groups)as x) ";
        //else
            $values[$key]="'$value'"; 
$rows[] = $values;
}

require_once("create.php");
?>