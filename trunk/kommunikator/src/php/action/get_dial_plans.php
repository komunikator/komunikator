<?
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 

$sql=
<<<EOD
	SELECT 
	d.dial_plan_id as id,
        d.dial_plan,
	d.priority, 
	d.prefix, 
	g.gateway, 
	d.nr_of_digits_to_cut,
	d.position_to_start_cutting,
	d.nr_of_digits_to_replace,
	d.digits_to_replace_with,
	d.position_to_start_replacing,
	d.position_to_start_adding,
	d.digits_to_add
	FROM dial_plans d 
		left join gateways g 
			on d.gateway_id = g.gateway_id

EOD;

$data =  compact_array(query_to_array($sql.get_filter()));
if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));
$total = count($data["data"]);

$data =  compact_array(query_to_array($sql.get_sql_order_limit()));

if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));
    
$obj=array("success"=>true);
$obj["total"] = $total; 
$obj["data"] = $data['data']; 
echo out($obj);
?>