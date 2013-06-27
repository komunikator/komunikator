<?

$extension =   $_SESSION['extension'];  

 if ($extension) {
        $sql = "SELECT extension_id as id from extensions where extension = '$extension ' ";
       if (query_to_array($sql)) {
             $sql=sprintf("UPDATE  pbx_settings SET  param=[forward]= $extension WHERE extension = '$extension' " );
        query($sql); 
        
          
   echo (out(array("success" => true, "message" => $id)));
   
        } else {
        echo (out(array("success" => false, "message" => 'pwd_incorrect'))); }
     
 }
?>