<?
need_user();

$data = json_decode($HTTP_RAW_POST_DATA);
$rows = array();

if ($data && !is_array($data)) $data = array($data);


foreach ($data as $row)
{
    $values = array();

    foreach ($row as $key => $value) $values[$key] = $value;

    $rows[] = $values;
}


$need_out = false;


$id_name = 'gateway_id';
$action = 'destroy_gateways';
include("destroy.php");


$id_name = 'gateway_id';
$action = 'destroy_incoming_gateways';
include("destroy.php");
?>