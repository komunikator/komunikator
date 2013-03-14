<?php

// ------------ FUNC
function putMailSettings($data)
{
    if ($data) 
	foreach ($data as $data_key=>$data_value) {
    		$sql="update ntn_settings set value = '$data_value' where param = '$data_key'";             
                file_put_contents('sql',$sql."\n",FILE_APPEND);
    		query ($sql);
    		$sql="insert into ntn_settings (param,value) select '$data_key', '$data_value' from dual where not exists (select 1 from ntn_settings where param = '$data_key' and value = '$data_value')";
                file_put_contents('sql',$sql."\n",FILE_APPEND);
    		query ($sql);
        }    
    return true;
}
function getMailSettings() 
{
    $sql =
            <<<EOD
	SELECT 
        s.param,
	s.value
	FROM ntn_settings s 
EOD;

    $data = compact_array(query_to_array($sql));
    $ret = array();
    foreach ($data['data'] as $value)
        $ret[$value[0]] = $value[1];
    file_put_contents('$test$.txt',print_r($ret,true));
    return $ret;
}

// ------------ BEGIN
    if (!$_SESSION['user']) {
        echo (out(array("success" => false, "message" => "User is undefined")));
        exit;
    }

/*
  if (isset($_REQUEST['type']))
  {
  $ret = array(success => true, message => 'updated');
  if (!writeNetworkConfig($_REQUEST))
  $ret = array(success => false, message => 'error_updated');
  }
  else */


    if (isset($_POST['incoming_trunk']))
    {
        //file_put_contents('$test2$.txt',print_r($_POST,true));    
        $data = $_POST;
        if (putMailSettings($data))
        {
            echo (out(array("success" => true)));
        } else {
            echo (out(array("success" => false, "message" => "mail_settings saving error")));
        }
    } else {
        //file_put_contents('$test$.txt',print_r($_REQUEST,true));    
        $data = getMailSettings();
        $object = array("success" => true, "data" => $data);
        echo json_encode($object);
    }
?>