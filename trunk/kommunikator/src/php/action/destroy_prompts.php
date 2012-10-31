<?
need_user();
$status = getparam("status");
$action = 'destroy_prompts';

$rows = array();
$rows[] = array('id'=>"(SELECT prompt_id FROM (SELECT * from prompts) a where a.status = '$status')");
$id_name = 'prompt_id';
require_once("destroy.php");
?>