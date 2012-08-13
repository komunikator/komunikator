<?
need_user();

$table_name = 'extensions';
$values = array();
$data = json_decode($HTTP_RAW_POST_DATA);
$rows = array();
$values = array();

if ($data)
foreach ($data as $row)
{
$values = array();
    foreach ($row as $key=>$value)
        if ($key == 'group') $group = $value;
	else 
            {
	        if ($key == 'id') $extension_id = $value;
		$values[$key]="'$value'"; 
            }
$rows[] = $values;
}
$id_name = 'extension_id';
if ($group) $need_out = false;
include ("update.php");
if (!$group) return;

$sql=
<<<EOD
	SELECT group_member_id,g.group_id FROM group_members gm 
	left join groups g on g.group = '$group'  
	where gm.extension_id = '$extension_id'			
EOD;
$rows = array();

$result = compact_array(query_to_array($sql));
if(!is_array($result['data']))  echo out(array('success'=>false,'message'=>$result));
$row = $result['data'][0]; 

if ($row) 
 {
  $rows[] = array('id'=>$row[0],'group_id'=>$row[1]);
  $action  = 'update_group_members';
  $id_name = 'group_member_id';
  include ("update.php");
 }
 else {
  $rows[] = array('extension_id'=>"'$extension_id'",'group_id'=>" (SELECT group_id FROM groups WHERE groups.group = '$group') ");
  $action  = 'create_group_members';
  include ("create.php");
}
?>