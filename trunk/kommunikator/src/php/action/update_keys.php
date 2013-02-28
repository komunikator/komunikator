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
            if ($key == 'status')
	     $values['prompt_id']= " (select prompt_id from prompts where prompt = '$value') "; 
		else
            if ($key == 'destination')
	     $values['destination']= "(select extension from groups  where groups.group = '$value' union select '$value' limit 1) "; 
		else 
            $values[$key]="'$value'"; 
$rows[] = $values;
}
$id_name = 'key_id';
require_once("update.php");
?>