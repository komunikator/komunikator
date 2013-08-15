<?
need_user();

$data = json_decode($HTTP_RAW_POST_DATA);

if ($data && !is_array($data)) $data = array($data);


foreach ($data as $row)
{
    $values_1 = array();
    $values_2 = array();  // для таблицы - incoming_gateways
    
    foreach ($row as $key => $value)
    {
      if ($key == 'id') $id = $key;
      else
      {
          $values_1[$key] = "'$value'";
          
          if ($key == 'gateway') $values_2['incoming_gateway'] = "'$value'";
          if ($key == 'server') $values_2['ip'] = "'$value'";
      }
    }
        
    $values_1['modified'] = 1;
    
    $values_2['gateway_id'] = "(SELECT gateway_id FROM gateways WHERE gateway=".$values_2['incoming_gateway']." AND server=".$values_2['ip'].")";
}


$need_out = false;


$rows = array();
$rows[] = $values_1;

$action = 'create_gateways';
include("create.php");


$rows = array();
$rows[] = $values_2;

$action = 'create_incoming_gateways';
include("create.php");
?>