<?
need_user();

$values = array();
$data = json_decode($HTTP_RAW_POST_DATA);
$rows = array();
$values = array();

if ($data && !is_array($data)) $data = array($data);
foreach ($data as $row)
{
$values = array();
    foreach ($row as $key=>$value)
            $values[$key]=$value; 
$rows[] = $values;
}
$id_name = 'did_id';
require_once("destroy.php");
?>