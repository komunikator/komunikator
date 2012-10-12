<?
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 

$total =  compact_array(query_to_array("SELECT count(*) FROM prompts"));
if(!is_array($total["data"]))  echo out(array("success"=>false,"message"=>$total));
$prompt_path = "auto_attendant/";    
$data =  compact_array(query_to_array("SELECT prompt_id as id, status, prompt, description, ".get_SQL_concat(array("'$prompt_path'",'file'))." FROM prompts ".get_sql_order_limit()));

if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));
    
$obj=array("success"=>true);
$obj["total"] = $total['data'][0][0]; 
$obj["data"] = $data['data']; 
echo out($obj);
?>