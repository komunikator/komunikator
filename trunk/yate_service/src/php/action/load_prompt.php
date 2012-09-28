<?
if(isset($_FILES)) {
    $file_tmp  = $_FILES['prompt_file']['tmp_name'];
    $file_name = $_FILES['prompt_file']['name'];
    $file_size = $_FILES['prompt_file']['size'];
    $status = getparam("status");
    $file_name = 'prompt_'.$status;
    //echo ($file_tmp.", ".$file_name.", ".$file_size);
    
    $time = date("Y-m-d_H:i:s");
    //global $vm_base;
    
    $file_name    = "$vm_base/auto_attendant/$status"."_".$time.".tmp";
    $cn_file_name = "$vm_base/auto_attendant/$status.wav";;
    if(is_uploaded_file($file_tmp)) {
        if(move_uploaded_file($file_tmp, $file_name)) {         
            passthru("madplay -q --no-tty-control -m -R 8000 -o wave:\"$cn_file_name\" \"$file_name\"");
            if(!is_file($cn_file_name)) {
                echo (out(array('success'=>false,'message'=>"Could not convert files in .au format.")));
                return;
            }
            $total =  compact_array(query_to_array("SELECT prompt_id FROM prompts where status = '$status'"));
            $rows = array();
            $rows[] = array('prompt'=>"'$status'",'status'=>"'$status'",'file'=>"'$status".".wav'");
	    //print_r($total["data"]);
            if(!$total["data"][0][0]) {
	        $action = 'create_prompts';
                require_once("create.php");
            }
            else {
                $rows[0]['id']=$total["data"][0][0];
                $id_name = 'prompt_id';	
	        $action = 'update_prompts';
                require_once("update.php");
            }
        //echo (out(array('success'=>true)));
        } 
        else {
            echo (out(array('success'=>false,'message'=>"Could not upload file")));
        }    
    }  else {
        echo (out(array('success'=>false,'message'=>"Error")));
    }
} else
        echo (out(array('success'=>false,'message'=>"Error")));

?>