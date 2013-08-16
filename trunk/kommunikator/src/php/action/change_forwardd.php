<?

$extension =   $_SESSION['extension'];  

 if ($extension) {
        $sql = "SELECT extension_id = $exten_id from extensions where extension = '$extension' ";
       if (query_to_array($sql)) {
            $sql=sprintf("UPDATE  pbx_settings SET  param=[forward]= $extension WHERE extension = '$extension' " );
        query($sql);
           echo (out(array("success" => false, "message" => 'pwd_incorrect')));
           
          /* $sql = "update pbx_settings set value = '2' where extension_id = $exten_id and param = "forward"";
        query($sql);
        $sql = "insert into pbx_settings (extension_id,param,value) select $exten_id, 'forward', '2' from dual where not exists (select 1 from pbx_settings where extension_id = $exten_id and param = 'forward' and value = '2')";
        query($sql);*/
        
        //  $sql="update pbx_settings set value = $pbx_value where extension_id = $extension_id and param = '$pbx_key'";
   echo (out(array("success" => true, "message" => $id)));
   
        } else {
        echo (out(array("success" => false, "message" => 'pwd_incorrect'))); }
     
 }
?>