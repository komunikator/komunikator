<?
need_user();

$values = array();
$data = json_decode($HTTP_RAW_POST_DATA);
$rows = array();
$values = array();

if ($data && !is_array($data)) $data = array($data);
foreach ($data as $row) {
    $values = array();
    foreach ($row as $key=>$value)
        switch ($key) {
            case 'did':
		break;	
            case 'default_dest':
                if (preg_match('/\d{3}/',$value)) {
                    $values['extension_id']= " (select extension_id from extensions where extension = '$value') "; 
                    $values['group_id']= 'null'; 
                }
                else {
                    $values['group_id']= " (select group_id from groups where groups.group = '$value') "; 
                    $values['extension_id']= 'null'; 
                }	
                break;
            case 'destination':
                $values[$key]="'$source[$value]'"; 
                break;
            default:	
                $values[$key]="'$value'"; 
        }
    $rows[] = $values;
}
$id_name = 'did_id';
require_once("update.php");
?>