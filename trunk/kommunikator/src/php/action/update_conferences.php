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
            if ($key == 'conference')
            $values['did'] = "'conference $value'";
		else 
            $values[$key]="'$value'"; 
$rows[] = $values;
}
$action  = 'update_dids';
$id_name = 'did_id';
require_once("update.php");
?>