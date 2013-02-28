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
            case 'id': 
                $id = $key;
                break;
            case 'conference':
                $values['did'] = "'conference $value'";
                $values['destination'] = "'conf/1'";
                break;
            case 'participants':;;
                break;
            default:	
                $values[$key]="'$value'"; 
        }
$rows[] = $values;
}
$action  = 'create_dids';

require_once("create.php");
?>