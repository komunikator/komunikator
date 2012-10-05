<?
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 

$total =  compact_array(query_to_array("SELECT count(*) FROM dial_plans"));
if(!is_array($total["data"]))  echo out(array("success"=>false,"message"=>$total));
    
$sql=
<<<EOD
	SELECT 
	d.dial_plan_id as id,
        d.dial_plan,
	d.priority, 
	d.prefix, 
	d.gateway_id, 
	d.nr_of_digits_to_cut,
	d.position_to_start_cutting,
	d.nr_of_digits_to_replace,
	d.digits_to_replace_with,
	d.position_to_start_replacing,
	d.position_to_start_adding,
	d.digits_to_add
	FROM dial_plans d order by 
EOD;

$data =  compact_array(query_to_array($sql.get_sql_order_limit()));

if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));
    
$obj=array("success"=>true);
$obj["total"] = $total['data'][0][0]; 
$obj["data"] = $data['data']; 
echo out($obj);
?>