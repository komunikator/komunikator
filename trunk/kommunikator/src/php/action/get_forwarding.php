<?
if(!$_SESSION['extension']) {
    echo (out(array("success"=>false,"message"=>"Extension is undefined"))); exit;} 
 
$number = $_SESSION['extension'] ;

     
$sql=
<<<EOD
select * from (
	SELECT 
            ex.extension_id as id, 
            fwd.value as forward,
            fwd_busy.value as forward_busy,
	    fwd_no_answ.value as forward_noanswer,
	    no_answ_to.value as noanswer_timeout  
FROM 
   extensions ex 
    left join pbx_settings fwd 
    on fwd.extension_id = ex.extension_id and fwd.param = "forward"
        left join pbx_settings fwd_busy 					
            on fwd_busy.extension_id = ex.extension_id and fwd_busy.param = "forward_busy"
                left join pbx_settings fwd_no_answ 
                    on fwd_no_answ.extension_id = ex.extension_id and fwd_no_answ.param = "forward_noanswer"
                        left join pbx_settings no_answ_to 
                            on no_answ_to.extension_id = ex.extension_id and no_answ_to.param = "noanswer_timeout"

WHERE extension = $number
	) a  
EOD;

$data =  compact_array(query_to_array($sql.get_filter()));

if(!is_array($data["data"]))  echo out(array("success"=>false,"message"=>$data));
  $ras = $data['data'][0];
  $rak = $data['header'];
  $new_array = array();
  foreach ($rak as $key=>$value)
   $new_array[$value] =  $ras[$key];
  
  echo (out(array("success"=>true, "data" =>$new_array ))); exit; 
 
?>