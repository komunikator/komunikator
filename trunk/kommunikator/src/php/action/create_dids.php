<?
need_user();

$values = array();
$data = json_decode($HTTP_RAW_POST_DATA);
//file_put_contents('a',print_r($data,true));
$rows = array();
$values = array();
$extensions = array();

if ($data && !is_array($data)) $data = array($data);
foreach ($data as $row)
{
$values = array();
	$id = null;
    foreach ($row as $key=>$value)
	switch ($key) {
            case 'id': 
                $id = $key;
                break;
            case 'group':;;
            case 'extension':;;
                break;
            default:	
                $values[$key]="'$value'"; 
        }
$rows[] = $values;
}
include("create.php");
?>