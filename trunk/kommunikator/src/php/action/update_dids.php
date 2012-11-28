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
	switch ($key) {
            case 'extension':
	     	$values['extension_id']= " (select extension_id from extensions where extension = '$value') "; 
                break;
            case 'group':
	     	$values['group_id']= " (select group_id from groups where groups.group = '$value') "; 
                break;
            default:	
                $values[$key]="'$value'"; 
        }
$rows[] = $values;
}
$id_name = 'did_id';
require_once("update.php");
?>