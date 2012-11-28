<?
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 

$total =  compact_array(query_to_array("select ((SELECT count(*) FROM groups) + (SELECT count(*) FROM extensions) + 2) count"));
if(!is_array($total["data"]))  echo out(array("success"=>false,"message"=>$total));

$const = array('attendant','voicemail');
    
$data =  compact_array(query_to_array("select 0 as id, '$const[0]' as name union select 1 as id, '$const[1]' as name union SELECT group_id as id, groups.group as name FROM groups union SELECT extension_id as id, extension as name from extensions ".get_sql_order_limit()));
//file_put_contents("test.txt","SELECT group_id as id, \"group\", description, extension FROM groups ORDER BY ".get_sql_order_limit());

if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));
    
$obj=array("success"=>true);
$obj["total"] = $total['data'][0][0]; 
$obj["data"] = $data['data']; 
echo out($obj);
?>