<?
need_user();

$data = json_decode($HTTP_RAW_POST_DATA);
// file_put_contents('a',print_r($data,true));
$rows = array();
$extensions = array();

if ($data && !is_array($data)) $data = array($data);

foreach ($data as $row)
{
    $values = array();
    
    foreach ($row as $key => $value)
        if ($key == 'id') $id = $key;
	else
            if (in_array($key ,array('status','priority','forward','forward_busy','forward_noanswer','noanswer_timeout')));
            // else
            // if ($key == "extension" && !$value) $values[$key]=" (SELECT MAX(extension)+1 FROM (SELECT * FROM extensions)as x) ";
            else
                if ($key == 'group_name') { if ($value && $row->extension); $extensions[$row->extension] = $value; }
                else $values[$key] = "'$value'";

    $rows[] = $values;
}

$need_out = false;
include("create.php");
 
$rows = array();
$groups = array();

$result =  compact_array(query_to_array('SELECT extension,extension_id FROM extensions'));
if(!is_array($result["data"]))  echo out(array("success"=>false,"message"=>$result));
if ($result['data'] &&  $extensions)
foreach ($result['data'] as $row)
 foreach ($extensions as $key=>$value)
   if ($row[0]==$key) $groups[$row[1]]=$value;

$result = compact_array(query_to_array('SELECT groups.group,group_id FROM groups'));
if(!is_array($result['data']))  echo out(array('success'=>false,'message'=>$result));
if ($result['data'] && $groups)
  foreach ($result['data'] as $row)
    foreach ($groups as $key=>$value)
      if ($value==$row[0]) $rows[] = array('extension_id'=>$key,'group_id'=>$row[1]);


// file_put_contents('b',print_r($rows,true));
$action = 'create_group_members';
include("create.php");
?>