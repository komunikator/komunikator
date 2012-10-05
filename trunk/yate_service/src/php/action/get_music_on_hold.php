<?
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 

$total =  compact_array(query_to_array("SELECT count(*) FROM music_on_hold"));
if(!is_array($total["data"]))  echo out(array("success"=>false,"message"=>$total));

$data =  compact_array(query_to_array("SELECT music_on_hold_id as id, music_on_hold, description, file FROM music_on_hold ORDER BY ".get_sql_order_limit()));
//file_put_contents("test.txt","SELECT group_id as id, \"group\", description, extension FROM groups ORDER BY ".get_sql_order_limit());

if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));

$obj=array("success"=>true);
$obj["total"] = $total['data'][0][0]; 
$obj["data"] = $data['data']; 
echo out($obj);
?>