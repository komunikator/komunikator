<?
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 

$total =  compact_array(query_to_array("SELECT count(*) FROM time_frames"));
if(!is_array($total["data"]))  echo out(array("success"=>false,"message"=>$total));
$data =  compact_array(query_to_array("select time_frame_id as id, day, start_hour, end_hour from (SELECT time_frame_id, day, start_hour-{$_SESSION['time_offset']}/60 as start_hour, end_hour-{$_SESSION['time_offset']}/60 as end_hour, case when numeric_day = 0 then 7 else numeric_day end as numeric_day FROM time_frames) a order by numeric_day "));

if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));
    
$obj=array("success"=>true);
$obj["total"] = $total['data'][0][0]; 
$obj["data"] = $data['data']; 
echo out($obj);
?>