<?
need_user();

$data = json_decode($HTTP_RAW_POST_DATA);

if ($data && !is_array($data)) $data = array($data);


foreach ($data as $row) {
    $values_1 = array();
    $values_2 = array();  // для таблицы - incoming_gateways
    
    foreach ($row as $key => $value) {
        $values_1[$key] = "'$value'";
        
        if ($key == 'id') $values_2['id'] = "'$value'";
        if ($key == 'gateway') $values_2['incoming_gateway'] = "'$value'";
        if ($key == 'server') $values_2['ip'] = "'$value'";
    }
    
    $values_1['modified'] = 1;
}


if  ( ( isset( $values_2['incoming_gateway'] ) )  or  ( isset( $values_2['ip'] ) ) )
{
    $need_out = false;


    $rows = array();
    $rows[] = $values_1;

    $id_name = 'gateway_id';
    $action = 'update_gateways';
    include("update.php");


    $rows = array();
    $rows[] = $values_2;

    $id_name = 'gateway_id';
    $action = 'update_incoming_gateways';
    include("update.php");
}
else
{
    $rows = array();
    $rows[] = $values_1;

    $id_name = 'gateway_id';
    $action = 'update_gateways';
    require_once("update.php");    
};
?>