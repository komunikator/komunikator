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
            if ($key == 'gateway')
	     $values['gateway_id']= " (select gateway_id from gateways where gateway = '$value') "; 
		else 
            $values[$key]="'$value'"; 
$rows[] = $values;
}
$id_name = 'dial_plan_id';
require_once("update.php");
?>