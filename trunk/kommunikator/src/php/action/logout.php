<?
session_start();
    	    $sql="insert into actionlogs (date,performer,log,ip) values (".time().",\"{$_SESSION['user']}\",\"{$_SESSION['user']} logout\", \"{$_SERVER['REMOTE_ADDR']}\")";
	    query ($sql);
          
echo (out(array("success"=>session_destroy())));
?>