<?
$out = array("success"=>true);
if (isset($_SESSION['user'])) $out['user'] = $_SESSION['user'];
if (isset($_SESSION['extension'])) $out['extension'] = $_SESSION['extension'];
echo (json_encode($out)); 
?>