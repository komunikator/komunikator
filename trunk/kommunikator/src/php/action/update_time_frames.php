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
        if ($key == 'start_hour' || $key == 'end_hour') 
            $values[$key]=1*$value+($_SESSION['time_offset']/60); 
	else
            $values[$key]="'$value'"; 
$rows[] = $values;
}
$id_name = 'time_frame_id';
require_once("update.php");
?>