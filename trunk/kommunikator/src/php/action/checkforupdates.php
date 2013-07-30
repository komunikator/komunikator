<?
need_user();
$msg = shell_exec("wget $updates_url -q -O -");
$data = json_decode($msg);
if (!$data)
    $data = array("success" => false, "message" => 'error');
echo json_encode ($data);
?>
